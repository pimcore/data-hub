## Add a Custom Query Datatype

There are two things that need to be one:
- add a type definition
- provide a resolver implementation (optional)

Add a section similar to this one to your `services.yml` file.

```
    pimcore.datahub.graphql.dataobjectquerytypegenerator_datatype_mycustomdatatype:
        class: Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectQueryFieldConfigGenerator\MyCustomDatatype
        tags:
            - { name: pimcore.datahub.graphql.dataobjectquerytypegenerator, id: typegenerator_dataobjectquerydatatype_mycustomdatatype }                        
```

For reference, have a look at a look at the `Link datatype`:
https://github.com/pimcore/data-hub/blob/7c62b888014a3df37928867b89b0dcd4489c3df4/src/GraphQL/DataObjectQueryFieldConfigGenerator/Link.php

It also shows how specific attributes are resolved. If you don't provide a resolver function then the getter method is
called instead. 
