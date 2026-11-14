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

namespace Pimcore\Bundle\DataHubBundle\Proposal\GraphQl\Tool;

use Mcp\Capability\Attribute\McpTool;
use Mcp\Capability\Attribute\Schema;
use Mcp\Schema\Result\CallToolResult;
use Mcp\Schema\ToolAnnotations;
use Pimcore\Bundle\DataHubBundle\Proposal\GraphQl\GraphQlConfigPolicy;
use Pimcore\Bundle\DataHubBundle\Proposal\Mcp\ConfigProposalTools;

/**
 * Proposes a GraphQL configuration — a change to one, or a new one — instead of writing it.
 * What is refused is {@see GraphQlConfigPolicy}'s to say.
 *
 * @internal
 */
final readonly class ProposeGraphQlConfigTool
{
    private const string TOOL_NAME = 'propose_graphql_config';

    public function __construct(
        private ConfigProposalTools $tools,
        private GraphQlConfigPolicy $policy,
    ) {
    }

    #[McpTool(
        name: self::TOOL_NAME,
        title: 'Propose GraphQL Configuration Changes',
        description: 'Propose a Data Hub GraphQL endpoint configuration for user approval — changes '
            . 'to an existing one, or a new one under a name that does not exist yet. Does NOT '
            . 'write — the configuration rides a change set the user reviews. For a change, read the '
            . 'current configuration first with get_graphql_config and send ONLY the sections and keys '
            . 'you change; anything you leave out keeps its current value. In schema.queryEntities and '
            . 'schema.mutationEntities send only the entities you change, each one COMPLETE (its id '
            . 'and its whole columnConfig, as it replaces the stored entity), and "<Entity>": null to '
            . 'remove one; entities you leave out stay as they are. Other keys and lists are replaced '
            . 'whole by what you send. Only the general, schema and workspaces sections may be '
            . 'sent: security and permissions '
            . 'are refused, and so is any create, update or delete access the endpoint does not '
            . 'already grant — propose read access only. A new configuration needs at least one '
            . 'query entity and starts inactive unless active is set. Returns '
            . '{proposalId, name}. The review widget is shown automatically once the proposal '
            . 'succeeds; end your turn after a successful proposal and wait for the decision.',
        annotations: new ToolAnnotations(
            readOnlyHint: false,
            destructiveHint: false,
            idempotentHint: false,
            openWorldHint: false,
        )
    )]
    public function execute(
        #[Schema(
            type: 'string',
            description: 'Name of the configuration: an existing one to change, or a new one to create.',
        )]
        string $name,
        #[Schema(
            type: 'string',
            description: 'The configuration as JSON or YAML — only the sections, keys and entities '
                . 'you change, from general, schema and workspaces; a changed entity complete, a removed '
                . 'one as null.'
        )]
        array|string $configuration,
        #[Schema(type: 'string', description: 'One sentence saying what the change does, shown on the review card.')]
        ?string $summary = null,
    ): CallToolResult {
        return $this->tools->propose($this->policy, self::TOOL_NAME, $name, $configuration, $summary);
    }
}
