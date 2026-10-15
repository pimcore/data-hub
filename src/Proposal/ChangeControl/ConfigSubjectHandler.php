<?php

declare(strict_types=1);

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Bundle\DataHubBundle\Proposal\ChangeControl;

use LogicException;
use Pimcore\Bundle\ChangeControlBundle\Merge\Exception\UnsupportedMergeSaveModeException;
use Pimcore\Bundle\ChangeControlBundle\Merge\MergeSaveMode;
use Pimcore\Bundle\ChangeControlBundle\Merge\StateShapeInterface;
use Pimcore\Bundle\ChangeControlBundle\Subject\Exception\SubjectAccessDeniedException;
use Pimcore\Bundle\ChangeControlBundle\Subject\SubjectHandlerInterface;
use Pimcore\Bundle\ChangeControlBundle\Subject\SubjectRef;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\Proposal\ConfigDocument;
use Pimcore\Bundle\DataHubBundle\Proposal\ConfigProposalPolicyInterface;
use Pimcore\Bundle\DataHubBundle\Proposal\StoredConfigurationsInterface;
use Pimcore\Bundle\DataHubBundle\Service\AdapterAvailabilityServiceInterface;
use Pimcore\Bundle\DataHubBundle\Service\Studio\ConfigurationServiceInterface;
use Pimcore\Bundle\DataHubBundle\Utils\Constants\PermissionConstants;
use Pimcore\Model\User;
use Pimcore\Model\UserInterface;
use function sprintf;
use Throwable;

/**
 * One adapter's configurations as a Change Control subject, keyed by configuration name.
 * Registered once per adapter, each with its own policy.
 */
final readonly class ConfigSubjectHandler implements SubjectHandlerInterface
{
    public function __construct(
        private ConfigProposalPolicyInterface $policy,
        private ConfigurationServiceInterface $configurations,
        private StoredConfigurationsInterface $stored,
        private AdapterAvailabilityServiceInterface $adapters,
    ) {
    }

    public function outOfDraftScope(): array
    {
        return ConfigDocument::withheldSections($this->policy);
    }

    public function shape(SubjectRef $subject): StateShapeInterface
    {
        return new ConfigStateShape();
    }

    public function readState(SubjectRef $subject): array
    {
        $configuration = $this->load($subject->ref);

        // no configuration yet: the create lane has no base and merges last-write-wins
        return $configuration === null
            ? []
            : ConfigDocument::readable($configuration->getConfiguration(), $this->policy);
    }

    /**
     * Reviewing shows the configuration's current values, so it is gated exactly like opening
     * it in the Data Hub.
     *
     * @throws SubjectAccessDeniedException
     */
    public function authorizeRead(SubjectRef $subject, UserInterface $user): void
    {
        $this->assertConfigPermission($subject, $user, PermissionConstants::PLUGIN_DATA_HUB_PERMISSION_READ);
    }

    public function authorize(SubjectRef $subject, UserInterface $user, MergeSaveMode $mode): void
    {
        // a configuration is live the moment it is written, so a mode this cannot land is
        // refused here rather than at apply — the review meta probes with each mode
        if ($mode !== MergeSaveMode::Publish) {
            throw SubjectAccessDeniedException::forRef(
                $this->policy->subjectType(),
                $subject->ref,
                MergeSaveMode::Publish->requiredPermission(),
            );
        }

        $this->assertConfigPermission($subject, $user, PermissionConstants::PLUGIN_DATA_HUB_PERMISSION_UPDATE);
    }

    public function applyMerged(
        SubjectRef $subject,
        array $finalData,
        UserInterface $mergedBy,
        MergeSaveMode $mode,
    ): array {
        if ($mode !== MergeSaveMode::Publish) {
            throw UnsupportedMergeSaveModeException::by(self::class, $mode);
        }

        $name = $subject->ref;

        if ($finalData === []) {
            // an empty merged state would be a removal; a configuration is never deleted through a merge
            throw new LogicException(
                sprintf('Refusing to apply an empty state to %s "%s".', $this->policy->noun(), $name)
            );
        }

        $stored = $this->stored->find($name);
        if ($stored !== null && $stored->getType() !== $this->policy->configType()) {
            // names are unique across adapters: saving here would overwrite another adapter's configuration
            throw new LogicException(sprintf(
                'Refusing to apply a %s to "%s": that name belongs to a %s configuration.',
                $this->policy->noun(),
                $name,
                $stored->getType(),
            ));
        }

        $isNew = $stored === null;
        if ($isNew) {
            $this->configurations->addConfiguration(
                $name,
                $this->policy->configType(),
                (string) ($finalData['general']['path'] ?? '')
            );
            $stored = $this->load($name);
        }

        $configuration = ConfigDocument::withStored($finalData, $stored?->getConfiguration() ?? [], $this->policy);

        // identity and adapter type are the subject's, not the draft's
        $configuration['general']['name'] = $name;
        $configuration['general']['type'] = $this->policy->configType();

        try {
            $this->policy->save($name, $configuration);
        } catch (Throwable $e) {
            if ($isNew) {
                $this->removeCreated($name);
            }

            throw $e;
        }

        return [];
    }

    /** the configuration stored under this name when it is one of this adapter's */
    private function load(string $name): ?Configuration
    {
        $configuration = $this->stored->find($name);

        return $configuration?->getType() === $this->policy->configType() ? $configuration : null;
    }

    /**
     * A create whose save failed leaves no empty configuration behind: the approval failed, so
     * the name must stay free and a retry must create again.
     */
    private function removeCreated(string $name): void
    {
        try {
            $this->configurations->deleteConfiguration($name);
        } catch (Throwable) {
            // the failed save is what the approver has to see
        }
    }

    /**
     * @throws SubjectAccessDeniedException
     */
    private function assertConfigPermission(SubjectRef $subject, UserInterface $user, string $permission): void
    {
        // isAllowed() wants the concrete user model; an actor this cannot verify is denied
        $configuration = $this->load($subject->ref);
        $allowed = $user instanceof User
            && $user->isAllowed(PermissionConstants::PLUGIN_DATA_HUB_CONFIG)
            && ($configuration === null
                ? $this->mayCreate($user, $permission)
                : $configuration->isAllowed($permission, $user));

        if (!$allowed) {
            throw SubjectAccessDeniedException::forRef(
                $this->policy->subjectType(),
                $subject->ref,
                PermissionConstants::PLUGIN_DATA_HUB_CONFIG
            );
        }
    }

    /**
     * Reading a configuration that does not exist yet needs the area permission alone; writing
     * one is a create, gated as the Data Hub's own create is, so a review never offers an
     * approval the save would refuse.
     */
    private function mayCreate(User $user, string $permission): bool
    {
        $type = $this->policy->configType();

        return $permission === PermissionConstants::PLUGIN_DATA_HUB_PERMISSION_READ
            || ($this->adapters->isEnabled($type)
                && ($user->isAllowed(PermissionConstants::PLUGIN_DATA_HUB_ADMIN)
                    || $user->isAllowed(PermissionConstants::PLUGIN_DATA_HUB_ADAPTER_PREFIX . $type)));
    }
}
