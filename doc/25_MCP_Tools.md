---
title: MCP Tools
description: The GraphQL configuration MCP tools of the bundle, and which of them a Studio-managed MCP server can offer.
---

# MCP Tools

The Data Hub contributes MCP tools to the Pimcore Agent that read GraphQL configurations and propose changes to them.

The tools are registered only when the Change Control bundle and the Pimcore Agent bundle are installed and enabled.
They reach a caller in one of two ways:

- **Agent chat:** every tool below, in the Pimcore Agent's tool groups `pimcore-data-hub-graphql-read` and
  `pimcore-data-hub-graphql-propose`.
- **Studio-managed MCP server:** only the tools tagged `pimcore.studio_backend.mcp_tool`. They are listed in the Studio
  tool catalogue (`GET /pimcore-studio/api/mcp/tools`), and an administrator can assign them to a configured MCP server
  (`/pimcore-mcp/studio/<slug>`) that external MCP clients connect to. Such a client carries no agent chat session, so a
  tool that needs one is not tagged and is not offered there.

| Tool | Purpose | Read / write | Propose / direct-write | Studio-managed server |
|---|---|---|---|---|
| `list_graphql_configs` | List the GraphQL configurations the user may read, with group, description and whether each is active. | read | – | yes |
| `get_graphql_config` | Read one GraphQL configuration in full, as the document a proposal sends back. | read | – | yes |
| `propose_graphql_config` | Propose changes to a GraphQL configuration, or a new one, for review in a change set. | write | propose | no, agent chat only |

- **Read / write:** a read tool is annotated `readOnlyHint: true`, so a Studio-managed server offers it under the OAuth
  scope `mcp:read`.
- **Propose / direct-write:** no tool of this bundle writes a configuration directly. `propose_graphql_config` records
  the configuration as a proposal in a change set; it is applied only once a user approves it. The proposal is stored in
  the agent chat session and reviewed on the chat's review card, so the tool needs the agent chat.
- **Permissions:** every tool runs as the calling user. It needs the `plugin_datahub_config` permission and, for a
  single configuration, read access to it (`list_graphql_configs` lists only those).
- **Withheld fields:** `get_graphql_config` leaves out `security`, `permissions` and names them under `withheld`;
  `propose_graphql_config` refuses a proposal that carries them. They hold the API keys and the authentication settings,
  and decide who may approve a proposal; a person changes them in the Data Hub editor.

The read and propose logic these tools share (`ConfigProposalTools`) is used by the Data Hub adapters as well; each
adapter bundle documents its own tools.
