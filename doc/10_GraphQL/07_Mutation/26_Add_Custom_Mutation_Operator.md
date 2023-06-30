# Add a Custom Mutation Operator

For adding a new mutation operator two steps are necessary: 
- add a type definition
- add the operator implementation

### Type Definition
Add a section similar to this one to your `services.yml` file.

```
  pimcore.datahub.graphql.dataobjectmutationtypegenerator_operator_mycustommutationoperator:
    class: Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectMutationOperatorConfigGenerator\MyCustomMutationOperator
    tags:
      - { name: pimcore.datahub.graphql.dataobjectmutationtypegenerator, id: typegenerator_mutationoperator_mycustommutationoperator }                        
```

For reference have a look at:
[`IfEmpty Operator`](https://github.com/pimcore/data-hub/blob/master/src/GraphQL/DataObjectMutationOperatorConfigGenerator/IfEmpty.php).

This will again define a processor (see the next subsection) and try to automatically determine the input type
depending on its child element.


### Operator Implementation

You have to provide both JavaScript code dealing with the UI configuration aspects specific to your operator
and the server-side PHP implementation processing the input (the input processor according to your input schema).

A JS sample can be found 
[here](https://github.com/pimcore/data-hub/blob/master/src/Resources/public/js/mutationoperator/IfEmpty.js).

Note that the namespace in your case would be `pimcore.plugin.datahub.mutationoperator.mycustommutationoperator`.

Make sure that your extension gets loaded. See [Pimcore Bundles](https://pimcore.com/docs/6.x/Development_Documentation/Extending_Pimcore/Bundle_Developers_Guide/Pimcore_Bundles/index.html)
docs page for further details.

Next thing is to provide the input processor on the server side.
A sample can be found 
[here](https://github.com/pimcore/data-hub/blob/master/src/GraphQL/DataObjectInputProcessor/IfEmptyOperator.php).
It will get the child value and only overwrite the current value if it is empty.


 





