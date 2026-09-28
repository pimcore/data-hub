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
 * One GraphQL configuration, as the document a proposal sends back — without the security
 * settings and the ACL, which no proposal may carry.
 *
 * @internal
 */
final readonly class GetGraphQlConfigTool
{
    private const string TOOL_NAME = 'get_graphql_config';

    public function __construct(
        private ConfigProposalTools $tools,
        private GraphQlConfigPolicy $policy,
    ) {
    }

    #[McpTool(
        name: self::TOOL_NAME,
        title: 'Get GraphQL Configuration',
        description: 'Read one Data Hub GraphQL endpoint configuration by name. Returns the general, '
            . 'schema (queryEntities and mutationEntities keyed by class name with their '
            . 'columnConfig, specialEntities keyed by generic type) and workspaces (asset, document '
            . 'and object trees with read/create/update/delete flags) sections — the security '
            . 'settings with the API keys and per-configuration permissions are withheld and cannot '
            . 'be proposed. Send this document back to propose_graphql_config with your changes '
            . 'applied.',
        annotations: new ToolAnnotations(
            readOnlyHint: true,
            destructiveHint: false,
            idempotentHint: true,
            openWorldHint: false,
        )
    )]
    public function execute(
        #[Schema(type: 'string', description: 'Name of the configuration, as list_graphql_configs reports it.')]
        string $name,
    ): CallToolResult {
        return $this->tools->get($this->policy, self::TOOL_NAME, $name);
    }
}
