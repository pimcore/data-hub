## Document Mutations

> Experimental + not feature complete! Subject to change without notice.

> Important Note: To be able to fully exploit this feature you have to understand Pimcore's [editable naming strategy](https://pimcore.com/docs/6.x/Development_Documentation/Documents/Editable_Naming_Strategies.html) 

## Supported Document types

* Email
* Link
* Page
* ...

## Supported Page Element types

* Areablock
* Block
* Image (for now only target asset + alt text, no hotspots or markers)
* Input
* Multiselect
* Select
* Wysiwyg
* ...

## Basic 

## Sample 1 (Update existing document)


```graphql
mutation {
  updateDocumentPage(id: 99, input: {
    
    elements: {input: [
      {_tagName: "content:2.headline"
      text:"HEYYOU 3"},
      {_tagName: "headline"
      text:"NEW 2"}
    ],
      wysiwyg: [
        {_tagName:"content:1.content",
        text:"my new <b>wysiwyg</b>"}
        
      ]
    
    }
    module: "mymodule"}) {
    success
    document {
      controller
    }
  }
}
```

## Sample 2 (Create document with areablocks and nested block with images)

See demo document 99 for reference.

```graphql
mutation {
	createDocumentPage(
		key: "documentkey47"	
		parentId: 1
		input: {
			elements: {
				areablock: [
					{
						_tagName: "content"
						indices: [
							{ key: "1", type: "headlines", hidden: false }
							{
								key: "2"
								type: "wysiwyg-with-images"
								hidden: false
							}
							{ key: "3", type: "image", hidden: false }
						]
					}
				]
				block: { _tagName: "content:2.images", indices: [1, 2] }
				image: [
					{ _tagName: "content:3.image", alt: "alt text", id: 67 }
					{
						_tagName: "content:2.images:1.image"
						alt: "alt text"
						id: 18
					}
					{
						_tagName: "content:2.images:2.image"
						alt: "alt text"
						id: 22
					}
				]
				input: [
					{
						_tagName: "content:1.headline"
						text: "HEY, I AM A HEADLINE SUBHEADLINE"
					}
					{ _tagName: "headline", text: "THIS IST HEHEADLINE" }
				]
				wysiwyg: [
					{ _tagName: "content:1.lead", text: "The lead text" }
					{
						_tagName: "content:2.content"
						text: "<b>Lorem Ipsum</b> is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book..."
					}
				]
			}
			controller: "@AppBundle\\Controller\\ContentController"
			action: "default"
		}
	) {
		success
		document {
			controller
			elements {
				__typename
			}
		}
	}
}
```

![Grid](../img/graphql/document_create_mutation.png)

## Sample 3 (Update email document)

See demo document 144 for reference.

```graphql
mutation {
	updateDocumentEmail(
		id: 144
		input: {
			elements: {
				wysiwyg: [
					{
						_tagName: "greeting_text"
						text: "Hello ... I am a <b>Greeting Text</b>"
					}
				]
			}
			subject: "Email subject"
		}
	) {
		success
		document {
			controller
		}
	}
}
```

![Grid](../img/graphql/document_updateemail_mutation.png)

## Sample 4 (Create a new link document)

```graphql
mutation {
  createDocumentLink(key: "newlinkdocument", parentId:1, input: {
    internal:308
    internalType:"asset"         
    }    
  
  ) {
    success
  }
}
```
![Grid](../img/graphql/document_create_link.png)
