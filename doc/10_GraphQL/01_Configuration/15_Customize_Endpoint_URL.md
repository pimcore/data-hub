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