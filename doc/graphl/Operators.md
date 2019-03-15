## Operators

![Overview](../img/graphql/operators_overview.png)

#### Date Formatter

Utilizes the PHP date formatter.

![Datahub Configs](../img/graphql/date_formatter.png)

####  Asset Thumbnail

Returns the thumbnail URL of the assigned image.


![Thumbnail Config](../img/graphql/operator_thumbnail.png)

#### Concatenator

Concatenates the child values.

![Concatenator Config](../img/graphql/operator_concatenator.png)

Request:
```
{
  getPerson(id: 28) {
    concatenatedname
  }
}
```

```
{
  "data": {
    "getPerson": {
      "concatenatedname": "John Doe"
    }
  }
}
```


####  Element Counter

Counts the child elements.

![Datahub Configs](../img/graphql/operator_elementcounter1.png)

Request:
```
{
  getUser(id: 50, defaultLanguage: "de") {
    count_myobjects
  }
}

```

Response:
```
{
  "data": {
    "getUser": {
      "count_myobjects": 2
    }
  }
}
```


#### Merge

...

#### Substring

As the name says.

#### Static Text

Adds some static text.

#### Translate Value

Similar to Pimcore's [Translate Value](https://pimcore.com/docs/5.x/User_Documentation/DataObjects/Grid_Configuration_Operators/Operators/TranslateValue.html)

Example: [Website Translations](Sample8.md)

#### Trimmer

As the name says.
