# DataObject Queries

## Supported Data Types

Also check out the Pimcore's [data type documentation](https://pimcore.com/docs/6.x/Development_Documentation/Objects/Object_Classes/Data_Types/index.html). 

* Advanced Many-to-Many Relation
* Advanced Many-to-Many Object Relation
* Block
* Boolean Select
* [Calculated Value](https://pimcore.com/docs/6.x/Development_Documentation/Objects/Object_Classes/Data_Types/Calculated_Value_Type.html)
* Checkbox
* Classification Store
* Country
* Countries (Multiselect)
* Date
* DateTime
* Email
* External Image
* Gender
* [Field-Collections](https://pimcore.com/docs/6.x/Development_Documentation/Objects/Object_Classes/Data_Types/Fieldcollections.html)
* Firstname
* Geobounds
* Geopoint
* Geopolygon
* Image
* Image Advanced
* Input
* InputQuantityValue
* Language
* Languages (Multiselect)
* Lastname
* Link
* Many-to-One Relation
* Many-to-Many Relation
* Many-to-Many Object Relation
* Multiselect
* Newsletter Active
* Newsletter Confirmed
* Numeric
* Quantity Value
* Reverse Many-to-Many Object Relation
* RgbaColor
* Select
* Slider
* StructuredTable
* Table
* Textarea
* Time
* [URL Slug](https://pimcore.com/docs/6.x/Development_Documentation/Objects/Object_Classes/Data_Types/Others.html)
* Video
* Wysiwyg

## Available Query Operators

Check out the [Operators](./06_Operators.md) page for more information.

* Alias
* Asset Thumbnail
* Asset Thumbnail HTML
* Concatenator
* Date Formatter
* Element Counter
* Merge
* Substring
* Static Text
* Trimmer

## Get single Data Object

Base structure for getting single data object: 

```graphql
{
  getNews(id: 4) {
  ...
  }
} 
    
```

## Get List of Data Objects 

Base structure for getting a list of data objects, restricted by IDs: 

```graphql
{
  getNewsListing(ids: "4,5") {
     edges {
    ...
```

Base structure for getting a list of data objects, restricted by fullpath:

```graphql
{
  getNewsListing(fullpaths: "/NewsArticle,/NewsArticle2") {
    totalCount
    edges {
      node {
        id        
      }
    }
  }
}
```

Sometimes it can happen that the fullpath already contains a comma. To make sure the comma is not
interpreted as a list separator in this case, you can quote the path:

```graphql
{
  getNewsListing(fullpaths: "'/NewsArticle,Headline','/NewsArticle2'") {
    totalCount
    edges {
      node {
        id        
      }
    }
  }
}
```
 
 
#### Pagination
Pagination can be applied as query parameters.

```graphql
{
  # 'first' is the limit
  # 'after' the offset
  getManufacturerListing(first: 3, after: 1) {
    edges {
      node {
        id
        name
      }
    }
  }
}
```

#### Simple Sorting
Sorting can be applied as query parameters, for example sort by name, descending.

```graphql
{
  getManufacturerListing(sortBy: "name", sortOrder: "DESC") {
    edges {
      node {
        id
        name
      }
    }
  }
}
```

#### Filtering

You can use Pimcore's webservice filter logic as described 
[here](https://pimcore.com/docs/pimcore/6.9/Development_Documentation/Web_Services/Query_Filters.html) 
for filtering listing requests.

For details see [filtering documentation page](./10_Filtering.md)


## Localization of Queries
Queries can be localized For details see the [localization documentation page](./08_Localization.md).


## Extend Data Object Queries
It is possible to add custom query data types and query operators. For details see detail documentation
pages: 
* [Add a custom query datatype](./15_Add_Custom_Query_Datatype.md)
* [Add a custom query operator](./16_Add_Custom_Query_Operator.md)


## More Examples
See following list for more examples with data object queries: 

- [Manufacturer Listing with sorting and paging](./11_Query_Samples/20_Sample_Manufacturer_Listing.md)
- [Many-to-Many Object Relation](./11_Query_Samples/21_Sample_ManyToMany_Object_Relation.md)
- [Advanced Many-to-Many Object Relation](./11_Query_Samples/22_Sample_Advanced_ManyToMany_Object_Relation.md)
- [Advanced Many-to-Many Relation Metadata](./11_Query_Samples/23_Sample_Advanced_ManyToMany_Relation_Metadata.md)
- [Field-Collections on Data Objects](./11_Query_Samples/24_Sample_Fieldcollections.md)
- [Objects Parent/Children/Siblings](./11_Query_Samples/25_Sample_Parent_Children_Siblings.md)
- [Get linked data](./11_Query_Samples/26_Sample_Get_Linked_Data.md)
- [Translate Values](./11_Query_Samples/27_Sample_Translate_Values.md)

