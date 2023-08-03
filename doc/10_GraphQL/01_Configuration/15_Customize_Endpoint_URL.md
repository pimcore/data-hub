# Customizing the Endpoint

The standard endpoint is
```
/pimcore-graphql-webservices/{clientname}?apikey={yourApiKey}
```

So if your configuration name is _blogdemo_ and your apikey _123456_
then your endpoint would be

```
/pimcore-graphql-webservices/blogdemo?apikey=12345
```

Here is a configuration example showing how to override the standard endpoint:

```yml
# app/config/routing.yml

# Changing URL to the explorer environement
admin_pimcoredatahub_config:
  path: /pimcore-datahub-webservices-my-endpoint/explorer/{clientname}
  defaults: { _controller: Pimcore\Bundle\DataHubBundle\Controller\GraphQLExplorerController::explorerAction }

# Changing endoint URL
admin_pimcoredatahub_webservice:
  path: /pimcore-graphql-webservices-my-endpoint/{clientname}
  defaults: { _controller: Pimcore\Bundle\DataHubBundle\Controller\WebserviceController::webonyxAction }
```
