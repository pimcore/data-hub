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

:::warning

The frontend half is not a public extension point yet. Operators are Pimcore Studio dynamic types extending
`DynamicTypeOperatorAbstract` (`readonly id`, `getIcon()`, `getLabel()`, `getConfigModal()`, `getGroup()`), registered
on the `DataHub/DynamicTypes/Operator/GraphQL/QueryRegistry` service. That base class is **not exported** from the
Datahub Studio SDK (`assets/studio/js/src/sdk/index.ts`), so another bundle's Studio plugin cannot currently import it.
The shipped operators under `assets/studio/js/src/modules/operators/operators/` show the pattern, but until the
operator API is exported, a custom operator has no configuration dialog in Pimcore Studio.

:::

An operator whose server-side implementation needs no configuration options still works: it is selectable only if a
frontend type is registered for it, so plan for the export limitation above.

For general plugin setup, see the Studio UI bundle's
[Getting Started with Your First Plugin](https://github.com/pimcore/studio-ui-bundle/blob/2026.x/doc/04_Extending/01_Getting_Started_with_Your_First_Plugin.md).

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
