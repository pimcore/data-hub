# Add a Custom Mutation Operator

For adding a new mutation operator two steps are necessary: 
- add a type definition
- add the operator implementation

### Type Definition
Add a section similar to this one to your `services.yml` file.

```yaml
  pimcore.datahub.graphql.dataobjectmutationtypegenerator_operator_mycustommutationoperator:
    class: Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectMutationOperatorConfigGenerator\MyCustomMutationOperator
    tags:
      - { name: pimcore.datahub.graphql.dataobjectmutationtypegenerator, id: typegenerator_mutationoperator_mycustommutationoperator }                        
```

For reference have a look at:
[`IfEmpty Operator`](https://github.com/pimcore/data-hub/blob/2.x/src/GraphQL/DataObjectMutationOperatorConfigGenerator/IfEmpty.php).

This will again define a processor (see the next subsection) and try to automatically determine the input type
depending on its child element.


### Operator Implementation

An operator has two halves: the server-side input processor, and a Pimcore Studio frontend type providing its
configuration dialog.

Provide the input processor first. A sample can be found
[here](https://github.com/pimcore/data-hub/blob/2.x/src/GraphQL/DataObjectInputProcessor/IfEmptyOperator.php).
It will get the child value and only overwrite the current value if it is empty.

:::warning

The frontend half is not a public extension point yet. Operators are Pimcore Studio dynamic types extending
`DynamicTypeOperatorAbstract` (`readonly id`, `getIcon()`, `getLabel()`, `getConfigModal()`, `getGroup()`), registered
on the `DataHub/DynamicTypes/Operator/GraphQL/MutationRegistry` service, which defines only the `other` group. That base
class is **not exported** from the Datahub Studio SDK (`assets/studio/js/src/sdk/index.ts`), so another bundle's Studio
plugin cannot currently import it. The shipped operators under `assets/studio/js/src/modules/operators/operators/` show
the pattern, but until the operator API is exported, a custom operator has no configuration dialog in Pimcore Studio.

:::

For general plugin setup, see the Studio UI bundle's
[Getting Started with Your First Plugin](https://github.com/pimcore/studio-ui-bundle/blob/2026.x/doc/04_Extending/01_Getting_Started_with_Your_First_Plugin.md).


 





