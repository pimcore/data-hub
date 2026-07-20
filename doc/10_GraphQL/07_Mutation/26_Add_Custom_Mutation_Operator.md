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

For the frontend half, register a Studio dynamic type on the Datahub mutation operator registry. Extend
`DynamicTypeOperatorAbstract` and implement `getIcon()`, `getLabel()`, `getConfigModal()` and `getGroup()`. The mutation
registry only defines the `other` group. Register it from your bundle's Studio plugin against the
`DataHub/DynamicTypes/Operator/GraphQL/MutationRegistry` service. The shipped operators under
`assets/studio/js/src/modules/operators/operators/` are the reference implementations.

See the [Studio UI bundle documentation](https://github.com/pimcore/studio-ui-bundle/blob/1.x/doc/README.md) for how to
build and load a Studio plugin from your bundle.


 





