# ![](img/graphql/logo_small.png) GraphQL 

![Explorer](img/graphql/iexplorer.png)

## Short Introduction Video

[![Preview](img/graphql/intro_preview.png)](./img/graphql/intro.mp4)

## Query Samples

If not otherwise noted then all samples can be tested using this [Endpoint](https://demo.pimcore.fun/pimcore-datahub-webservices/explorer/products?apikey=6332aa5e6d3d6c0be31da2a8b3442113).

* [Get Car + Manufacturer + Manufacturer Logo](graphl/querysamples/Sample_Dependencies.md)
* [Get Manufacturer Listing](graphl/querysamples/Sample_ManufacturerListing.md)
* [Filtered Manufacturer Listing](graphl/Filtering.md )
* [Many-to-Many Object Relation](graphl/querysamples/Sample_ManyToManyObject.md)
* [Advanced Many-to-Many Relation Metadata](graphl/querysamples/Sample4.md)
* [Get Asset Directly](graphl/querysamples/Sample_GetAsset.md)
* [Get Asset Listing](graphl/querysamples/Sample_GetAssetListing.md)
* [Get Asset Metadata](graphl/querysamples/Sample_AssetMetadata.md)
* [Advanced Many-to-Many Object Relation + Metadata](graphl/querysamples/Sample7.md)
* [Website Translator](graphl/querysamples/Sample_WebsiteTranslation.md)
* [Field-Collections](graphl/querysamples/Sample_Fieldcollections.md)
* [Get Element Properties](graphl/querysamples/Sample_ElementProperties.md)
* [Get object parent/children/siblings](graphl/querysamples/Sample_ParentChildrenSiblings.md)

## Mutation Samples
* see [Mutation main page](graphl/Mutations.md)
* [Adding custom mutations](graphl/AddCustomMutations.md)

## Configuration

* [General Settings](./graphl/General.md)
* [Schema Definition](./graphl/Schema.md)
* [Security Settings](./graphl/Security.md)
* [Custom Permissions](./graphl/Permissions.md)


## Customizing the endpoint

The standard endpoint is
```
/pimcore-graphql-webservices/{configurationname}?apikey={yourApiKey}
```

So if your configuration name is _blogdemo_ and your apikey _123456_
then your endpoint would be

```
/pimcore-graphql-webservices/blogdemo?apikey=12345
```

Here is a configuration example showing how to override the standard endpoint:

```yml
# app/config/routing.yml

admin_pimcoredatahub_config:
    path: /pimcore-datahub-webservices-my-endpoint/explorer/{clientname}
    defaults: { _controller: PimcoreDataHubBundle:GraphQLExplorer:explorer }

admin_pimcoredatahub_webservice:
  path: /pimcore-graphql-webservices-my-endpoint/{clientname}
  defaults: { _controller: PimcoreDataHubBundle:Webservice:webonyx }
```
## Queries

* Documents: see [Document Query Documentation](./graphl/DocumentQueries.md)
* DataObjects: see [DataObject Query Documentation](./graphl/DataObjectQueries.md)
* [Adding custom queries](graphl/AddCustomQuery.md)

## Mutations

See [Mutation Documentation](./graphl/Mutations.md)

## Endpoint + Hands-on-testing using iExplorer

![Open iExplorer](img/graphql/open_explorer.png)

## Configuration & Deployment

The configuration file can be found `var/config/datahub-configurations.php`.

As a workspace permission index is kept in the database as well you have to run the `datahub:graphql:rebuild-definitions` 
to rebuild the index.

Either call 

```bash
datahub:graphql:rebuild-definitions
``` 

to do that for all definitions, or

```bash
datahub:graphql:rebuild-definitions --definitions=newsapp,otherendpoint
```

for specific definitions.

## Events

See [Events Documentation](./graphl/Events.md)
