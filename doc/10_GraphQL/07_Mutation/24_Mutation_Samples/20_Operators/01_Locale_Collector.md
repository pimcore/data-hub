---
title: Locale Collector
---

# Sample for Mutation Operator "Locale Collector"

```graphql
mutation {
  updateTable(
    id: 7
    input: {
      name_translated: {
        en: "Dinner table"
        de: "Esstisch"
      }
    }
  ) {
    success
    message
    output {
      name_en: name(language: "en")
      name_de: name(language: "de")
    }
  }
}
```
