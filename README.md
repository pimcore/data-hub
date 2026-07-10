---
title: Datahub
---
# Pimcore Datahub

[<img src="https://sonarcloud.io/images/project_badges/sonarcloud-light.svg" alt="SonarQube Cloud" height="30" />](https://sonarcloud.io/summary/new_code?id=pimcore_data-hub)


Pimcore Datahub (data delivery and consumption platform) integrates different input & output channel
technologies into a simple & easy-to-configure system on top of Pimcore.

The basic configuration of Datahub comes with a GraphQL API, which is described in the next sections of this documentation. To use another configuration, Pimcore Datahub can be extended with different adapters (see [Further Information](#further-information)).

![Overview](./doc/img/overview.jpg)
*Sample presentation of Datahub config when choosing the GraphQL endpoint*

A short introduction video of an output channel based on the GraphQL query language can be found [here](./doc/img/graphql/intro.mp4).

## Features in a Nutshell
- Easy-to-configure interface layer for data delivery and consumption
- Tool of choice to connect Pimcore to any other systems and applications besides internal PHP API - whether they are backend applications like ERP systems or frontend applications like your storefront
- Multiple endpoints definition for different use cases and target/source systems
- Central and easy-to-use GUI to transform and prepare data for defined endpoints
- To-be-exposed data restriction to endpoints by defining workspaces and schemas.

## Documentation Overview
- [Installation](./doc/01_Installation_and_Upgrade/README.md)
- [Basic principle](./doc/02_Basic_Principle.md) for configuring an endpoint
- [Studio Integration](./doc/04_Studio_Integration.md) for the configuration panel and its permissions
- [GraphQL](./doc/10_GraphQL/README.md) [*default and recommended endpoint*]
- [Configuration & Deployment](./doc/20_Deployment.md)
- [Testing](./doc/30_Testing.md)

## Further Information
On Pimcore Datahub adapters:
- [Data Importer](https://github.com/pimcore/data-importer/blob/2026.x/doc/01_Installation.md)
- [Datahub Simple REST API](https://github.com/pimcore/data-hub-simple-rest/blob/2026.x/doc/01_Installation/README.md)
- [Datahub File Export](https://github.com/pimcore/data-hub-file-export/blob/2026.x/doc/01_Installation/README.md)
- [Datahub Productsup](https://github.com/pimcore/data-hub-productsup/blob/2026.x/doc/01_Installation/README.md)
- [Datahub Webhooks](https://github.com/pimcore/data-hub-webhooks/blob/2026.x/doc/01_Installation/README.md)
  
## Contributions
As Pimcore Datahub is a community project, any contributions highly appreciated.
For details see our [Contributing guide](https://github.com/pimcore/data-hub/blob/master/CONTRIBUTING.md).
