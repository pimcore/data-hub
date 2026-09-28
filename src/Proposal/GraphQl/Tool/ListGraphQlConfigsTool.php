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
use Mcp\Schema\Result\CallToolResult;
use Mcp\Schema\ToolAnnotations;
use Pimcore\Bundle\DataHubBundle\Proposal\GraphQl\GraphQlConfigPolicy;
use Pimcore\Bundle\DataHubBundle\Proposal\Mcp\ConfigProposalTools;

/**
 * The GraphQL endpoint configurations this installation has, so an agent can name one before reading it.
 *
 * @internal
 */
final readonly class ListGraphQlConfigsTool
{
    private const string TOOL_NAME = 'list_graphql_configs';

    public function __construct(
        private ConfigProposalTools $tools,
        private GraphQlConfigPolicy $policy,
    ) {
    }

    #[McpTool(
        name: self::TOOL_NAME,
        title: 'List GraphQL Configurations',
        description: 'List the Data Hub GraphQL endpoint configurations, with their group, whether '
            . 'they are active, the classes they expose for querying and how many mutation entities '
            . 'they have. Use this to find the exact name to pass to get_graphql_config.',
        annotations: new ToolAnnotations(
            readOnlyHint: true,
            destructiveHint: false,
            idempotentHint: true,
            openWorldHint: false,
        )
    )]
    public function execute(): CallToolResult
    {
        return $this->tools->list($this->policy, self::TOOL_NAME);
    }
}
