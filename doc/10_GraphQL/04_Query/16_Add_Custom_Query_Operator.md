# Add a Custom Query Operator

For adding a new query operator two steps are necessary: 
- add a type definition
- add the operator implementation

### Type Definition

Add a section similar to this one to your `services.yml` file.

```yaml
    pimcore.datahub.graphql.querytypegenerator_operator_mycustomoperator:
        class: Pimcore\Bundle\DataHubBundle\GraphQL\QueryOperatorConfigGenerator\MyCustomOperator
        tags:
            - { name: pimcore.datahub.graphql.dataobjectquerytypegenerator, id: typegenerator_queryoperator_mycustomoperator }                        
```

For reference, have a look at the
[`Trimmer operator`](https://github.com/pimcore/data-hub/blob/2.x/src/GraphQL/Query/Operator/Trimmer.php).

### Operator Implementation

An operator has two halves: the server-side PHP implementation doing the actual calculation, and a Pimcore Studio
frontend type providing its configuration dialog.

Provide the server-side implementation first. A sample can be found
[here](https://github.com/pimcore/data-hub/blob/2.x/src/GraphQL/Query/Operator/Trimmer.php).

For the frontend half, register a Studio dynamic type on the Datahub query operator registry. Extend
`DynamicTypeOperatorAbstract` and implement `getIcon()`, `getLabel()`, `getConfigModal()` and `getGroup()`, where the
group is one of `formatter`, `transformer` or `other`. Register it from your bundle's Studio plugin against the
`DataHub/DynamicTypes/Operator/GraphQL/QueryRegistry` service. The shipped operators under
`assets/studio/js/src/modules/operators/operators/` are the reference implementations.

See the [Studio UI bundle documentation](https://github.com/pimcore/studio-ui-bundle/blob/1.x/doc/README.md) for how to
build and load a Studio plugin from your bundle.

Finally, we have to define how the operator instances get created.
In most cases we use the `DefaultOperatorFactory` for that:

```yaml
    pimcore.datahub.graphql.dataobjectqueryoperator.factory.mycustomoperator:
        class: Pimcore\Bundle\DataHubBundle\GraphQL\Query\Operator\Factory\DefaultOperatorFactory
        arguments:
            $className: Pimcore\Bundle\DataHubBundle\GraphQL\Query\Operator\MyCustomOperator
        tags:
            - { name: pimcore.datahub.graphql.dataobjectqueryoperator_factory, id: mycustomoperator }
```
