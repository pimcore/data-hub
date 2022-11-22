<?php
declare(strict_types=1);

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\UnionType;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassTypeDefinitions;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Cache\RuntimeCache;
use Pimcore\Model\DataObject;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class ObjectTreeType extends UnionType implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    use ServiceTrait;

    /**
     * @param Service $graphQlService
     * @param array $config
     */
    public function __construct(Service $graphQlService, $config = ['name' => 'object_tree'])
    {
        $this->setGraphQLService($graphQlService);
        parent::__construct($config);
    }

    /**
     * @return array
     *
     * @throws \Exception
     */
    public function getTypes(): array
    {
        $context = RuntimeCache::get('datahub_context');
        /** @var Configuration $configuration */
        $configuration = $context['configuration'];

        $types = array_values(ClassTypeDefinitions::getAll(true));
        if ($configuration->getSpecialEntities()['object_folder']['read'] ?? false) {
            $types[] = $this->getGraphQlService()->getDataObjectTypeDefinition('_object_folder');
        }

        return $types;
    }

    /**
     * @inheritdoc
     */
    public function resolveType($element, $context, ResolveInfo $info)
    {
        if (!$element) {
            return null;
        }
        $object = DataObject\AbstractObject::getById($element['id']);

        if ($object instanceof DataObject\Folder) {
            return $this->getGraphQlService()->getDataObjectTypeDefinition('_object_folder');
        }

        return ClassTypeDefinitions::get($object->getClass());
    }
}
