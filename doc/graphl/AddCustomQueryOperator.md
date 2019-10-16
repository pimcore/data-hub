## Add a Custom Query Operator

There are two things that need to be one:
- add a type definition
- add the operator implementation

### Type definition

Add a section similar to this one to your `services.yml` file.

```
    pimcore.datahub.graphql.querytypegenerator_operator_mycustomoperator:
        class: Pimcore\Bundle\DataHubBundle\GraphQL\QueryOperatorConfigGenerator\MyCustomOperator
        tags:
            - { name: pimcore.datahub.graphql.querytypegenerator, id: typegenerator_queryoperator_mycustomoperator }                        
```

For reference, have a look at a look at the `Trimmer operator`:
https://github.com/pimcore/data-hub/blob/master/src/GraphQL/QueryOperatorConfigGenerator/Trimmer.php

### Operator Implementation

You have to provide both JavaScript code dealing with the UI configuration aspects specific to  your operator
and the server-side PHP implementation doing the actual calculations. 

A JS sample can be found here: https://github.com/pimcore/data-hub/blob/7c62b888014a3df37928867b89b0dcd4489c3df4/src/Resources/public/js/operator/Trimmer
Note that the namespace would be `pimcore.bundle.datahub.operator.mycustomoperator`

Make sure that your extension gets loaded. See [Pimcore Bundles](https://pimcore.com/docs/5.x/Development_Documentation/Extending_Pimcore/Bundle_Developers_Guide/Pimcore_Bundles/index.html)
docs page for further details.

Next thing is to provide the server-side implementation.
A sample can be found here: https://github.com/pimcore/data-hub/blob/9ed3056864a5e93455c3325a35af73fdf7e4b2dc/src/GraphQL/Query/Operator/Trimmer.php

Finally, we have to define how the operator instances get created.
In most cases we use the `DefaultOperatorFactory` for that:

```
    pimcore.datahub.graphql.dataobjectqueryoperator.factory.mycustomoperator:
        class: Pimcore\Bundle\DataHubBundle\GraphQL\Query\Operator\Factory\DefaultOperatorFactory
        arguments:
            $className: Pimcore\Bundle\DataHubBundle\GraphQL\Query\Operator\MyCustomOperator
        tags:
            - { name: pimcore.datahub.graphql.dataobjectqueryoperator_factory, id: mycustomoperator }
```



