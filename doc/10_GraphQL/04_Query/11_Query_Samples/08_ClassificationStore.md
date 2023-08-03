# Classification Store

## Supported Data Types

* BooleanSelect
* Calculated Value
* Checkbox
* Country
* Countries (Multiselect)
* Date
* DateTime
* Input
* InputQuantityValue
* Language
* Languages (Multiselect)
* Multiselect
* Numeric
* QuantityValue
* RgbaColor
* Select
* Slider
* Textarea
* Time
* Wysiwyg


## Request Example

`csfield` is the classification store field.

```graphql
{
{
  getCstest(id: 107) {
    id
    classname
    qvfield {
      unit {
        abbreviation
      }
      value
    }
    csfield(language: "default") {
      id
      name
      description
      features {
        __typename
        ... on csFeatureInput {
          type
          id
          name
          description
          text
        }
        ... on csFeatureQuantityValue {
          type
          id
          description
          quantityvalue {
            unit {
              abbreviation
              longname
            }
            value
          }
        }
        ... on csFeatureTextarea {
          text
          name
        }
      }
    }
  }
}
```

## Response Example

```json
{
  "data": {
    "getCstest": {
      "id": "107",
      "classname": "cstest",
      "qvfield": {
        "unit": {
          "abbreviation": "cm"
        },
        "value": 3
      },
      "csfield": [
        {
          "id": 2,
          "name": "Group1",
          "description": "Some group description",
          "features": [
            {
              "__typename": "csFeatureInput",
              "type": "input",
              "id": 2,
              "name": "The name of my key definition",
              "description": "The description ...",
              "text": "C"
            },
            {
              "__typename": "csFeatureQuantityValue",
              "type": "quantityValue",
              "id": 3,
              "description": "A quantityValue key definition",
              "quantityvalue": {
                "unit": {
                  "abbreviation": "mm",
                  "longname": "millimeter"
                },
                "value": 2
              }
            },
            {
              "__typename": "csFeatureTextarea",
              "text": "Hello, I am a textarea",
              "name": "key4"
            }
          ]
        }
      ]
    }
  }
}
```