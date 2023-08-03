# Localization

### Default Language

You can change the default language for localized fields by passing the `defaultLanguage` argument
for single and listing queries.

#####  Sample Request
```
{
  getNewsListing(defaultLanguage: "de") {
  ...
}
```

### Define Language on Field Level

However, you can always provide an alternative language for a specific field.

#####  Sample Request
```
{
  getUser(id: 50, defaultLanguage: "en") {
    myAdvancedObjects {
      element {
        id
        classname
        title,
        deTitle: title(language: "de"),
        shortText(language: "de")
      }
      metadata {
        name
        value
      }
    }
  }
}
```

##### Response
```
{
  "data": {
    "getUser": {
      "myAdvancedObjects": [
        {
          "element": {
            "id": "8",
            "classname": "news",
            "title": "In enim justo",
            "deTitle": "Li Europan lingues es membres",
            "shortText": "Lor separat existentie es un myth. Por scientie, musica, sport etc, litot Europa usa li sam vocabular."
          },
          "metadata": [
            {
              "name": "altname",
              "value": "Ratman"
            },
            {
              "name": "name",
              "value": "Canine"
            }
          ]
        },
        {
          "element": {
            "id": "3",
            "classname": "news",
            "title": "Lorem ipsum dolor sit amet",
            "deTitle": "Er hörte leise Schritte hinter sich",
            "shortText": "Das bedeutete nichts Gutes. Wer würde ihm schon folgen, spät in der Nacht und dazu noch in dieser engen Gasse mitten im übel beleumundeten Hafenviertel?"
          },
          "metadata": [
            {
              "name": "altname",
              "value": "Spike"
            },
            {
              "name": "name",
              "value": "Doctor"
            }
          ]
        }
      ]
    }
  }
}
```
