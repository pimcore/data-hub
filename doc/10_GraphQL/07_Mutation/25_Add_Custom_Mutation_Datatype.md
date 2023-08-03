# Add a Custom Mutation Datatype

For adding a new mutation data type two steps are necessary: 
- add an input type definition
- provide a processor implementation

Add a section similar to this one to your `services.yml` file.

```
    pimcore.datahub.graphql.dataobjectmutationtypegenerator_datatype_mycustomdatatype:
        class: Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectMutationFieldConfigGenerator\MyCustomDatatype
        tags:
            - { name: pimcore.datahub.graphql.dataobjectmutationtypegenerator, id: typegenerator_dataobjectmutationdatatype_mycustomdatatype }                                    
```

For reference, have a look at a look at the 
[`Geopoint datatype`](https://github.com/pimcore/data-hub/blob/master/src/GraphQL/DataObjectMutationFieldConfigGenerator/Geopoint.php).

You return a valid GraphQL input type (which is an `InputObjectType`) here and a `Processor` which processes the actual
input data.

For a rather simple example have a look at the 
[`Date`](https://github.com/pimcore/data-hub/blob/master/src/GraphQL/DataObjectMutationFieldConfigGenerator/Date.php) 
implementation (which both accepts integer and string input values).
 