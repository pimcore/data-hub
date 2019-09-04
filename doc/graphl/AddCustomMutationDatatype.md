## Add a Custom Mutation Datatype

There are two things that need to be one:
- add a input type definition
- provide a processor implementation

Add a section similar to this one to your `services.yml` file.

```
    pimcore.datahub.graphql.mutationtypegenerator_datatype_mycustomdatatype:
        class: Pimcore\Bundle\DataHubBundle\GraphQL\MutationFieldConfigGenerator\MyCustomDatatype
        tags:
            - { name: pimcore.datahub.graphql.dataobjectmutationtypegenerator, id: typegenerator_mutationdatatype_mycustomdatatype }                                    
```

For reference, have a look at a look at the `Geopoint datatype`:
https://github.com/pimcore/data-hub/blob/master/src/GraphQL/MutationFieldConfigGenerator/Geopoint.php

You return a valid GraphQL input type (which is an `InputObjectType`) here and a `Processor` which processes the actual
input data.

For a rather simple example have a look at the `Date` implementation (which both accepts integer and string input values).
https://github.com/pimcore/data-hub/blob/master/src/GraphQL/MutationFieldConfigGenerator/Date.php   