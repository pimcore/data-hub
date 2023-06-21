# Add a Custom Query Operator

For adding a new query operator two steps are necessary: 
- add a type definition
- add the operator implementation

### Type Definition

Add a section similar to this one to your `services.yml` file.

```
    pimcore.datahub.graphql.querytypegenerator_operator_mycustomoperator:
        class: Pimcore\Bundle\DataHubBundle\GraphQL\QueryOperatorConfigGenerator\MyCustomOperator
        tags:
            - { name: pimcore.datahub.graphql.dataobjectquerytypegenerator, id: typegenerator_queryoperator_mycustomoperator }                        
```

For reference, have a look at a look at the 
[`Trimmer operator`](https://github.com/pimcore/data-hub/blob/master/src/GraphQL/Query/Operator/Trimmer.php).

### Operator Implementation

You have to provide both JavaScript code dealing with the UI configuration aspects specific to  your operator
and the server-side PHP implementation doing the actual calculations. 

A JS sample can be found [here](https://github.com/pimcore/data-hub/blob/master/src/Resources/public/js/queryoperator/Trimmer.js). 
Note that the namespace would be `pimcore.plugin.datahub.operator.mycustomoperator`. 

Make sure, that your extension gets loaded. See [Pimcore Bundles](https://pimcore.com/docs/6.x/Development_Documentation/Extending_Pimcore/Bundle_Developers_Guide/Pimcore_Bundles/index.html)
docs page for further details.

Next thing is to provide the server-side implementation.
A sample can be found [here](https://github.com/pimcore/data-hub/blob/master/src/GraphQL/Query/Operator/Trimmer.php). 

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
