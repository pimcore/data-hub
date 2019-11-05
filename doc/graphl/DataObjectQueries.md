## Dataobject Queries

## Supported query datatypes

Also check out the Pimcore's [data type documentation](https://pimcore.com/docs/5.x/Development_Documentation/Objects/Object_Classes/Data_Types/index.html). 

* Advanced Many-to-Many Relation
* Advanced Many-to-Many Object Relation
* Boolean Select
* [Calculated Value](https://pimcore.com/docs/5.x/Development_Documentation/Objects/Object_Classes/Data_Types/Calculated_Value_Type.html)
* Checkbox
* [Classification Store](ClassificationStore.md) page for more info.
* Country
* Countries (Multiselect)
* Date
* DateTime
* Email
* External Image
* Gender
* [Field-Collections](https://pimcore.com/docs/5.x/Development_Documentation/Objects/Object_Classes/Data_Types/Fieldcollections.html)
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
* RgbaColor
* Select
* Slider
* Textarea
* Time
* Video
* Wysiwyg

## Query Operators

Check out the [Operators](Operators.md) page for more info.

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

## Extending it
* [Add a custom query datatype](./AddCustomQueryDatatype.md)
* [Add a custom query operator](./AddCustomQueryOperator.md)

## Get Object

Also see the samples listed above.

```graphql
{
  getNews(id: 4) {
  ...
  }
} 
    
```

## Listings

* Restrict to object IDs

```graphql
{
  getNewsListing(ids: "4,5") {
    edges {
    ...
```

## Localization

See the [Localization Page](Localization.md) to learn more.
 
 
## Pagination

Example:
see [Get News Listing](querysamples/Sample2.md)

## Simple Sorting
```graphql
{
  getNewsListing(sortBy: ["title"], sortOrder: ["ASC"]) {
    edges {
      node {
      ...
```

## Filtering

You can use Pimcore's webservice filter logic
as described [here](https://pimcore.com/docs/5.x/Development_Documentation/Web_Services/Query_Filters.html).

See Example: [Filtered News Listing](Filtering.md)

