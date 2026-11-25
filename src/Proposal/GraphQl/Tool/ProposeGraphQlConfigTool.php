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
        description: 'Propose changes to a Data Hub GraphQL endpoint configuration, or a new one under an unused '
            . 'name. Load the data-hub-graphql-configuration skill before the first call. Read the document '
            . 'with get_graphql_config first and send only what you change. Allowed: general, schema, '
            . 'workspaces (read access only); refused: security, permissions. Returns {proposalId, name}.',
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
            description: 'An existing configuration to change, or a new name to create.',
        )]
        string $name,
        #[Schema(
            type: 'string',
            description: 'JSON or YAML object with only the sections and keys you change.'
        )]
        array|string $configuration,
        #[Schema(type: 'string', description: 'One sentence for the review card.')]
        ?string $summary = null,
    ): CallToolResult {
        return $this->tools->propose($this->policy, self::TOOL_NAME, $name, $configuration, $summary);
    }
}
