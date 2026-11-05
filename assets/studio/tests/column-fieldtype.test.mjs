/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

// Run with: node --test "tests/*.test.mjs" (Node >= 22.18 or 24, which strips TypeScript types natively)
import assert from 'node:assert/strict'
import { describe, it } from 'node:test'
import {
  deriveColumnFieldtype,
  matchesAvailableColumn,
  readStoredFieldtype
} from '../js/src/modules/config/components/column-config-modal/column-fieldtype.ts'
import {
  ADVANCED_COLUMN_TYPE,
  advancedFromSchemaColumn,
  advancedToSchemaColumn
} from '../js/src/modules/config/components/column-config-modal/types.ts'

const FIELD_NAME = 'title'
const INPUT_FIELDTYPE = 'input'
const INPUT_COLUMN_TYPE = 'dataobject.input'
const RELATION_FIELDTYPE = 'manyToManyObjectRelation'
const CLASSIFICATION_STORE_TYPE = 'dataobject.classificationstore'
const LEGACY_CLASSIFICATION_STORE_KEY = '3-17'
const ADVANCED_COLUMN_TITLE = 'Full name'

const knownTypes = new Set([INPUT_FIELDTYPE, RELATION_FIELDTYPE, 'integer'])
const isKnownType = (name) => knownTypes.has(name)

const inputColumn = {
  key: FIELD_NAME,
  type: INPUT_COLUMN_TYPE,
  config: { fieldDefinition: { fieldtype: INPUT_FIELDTYPE } }
}

const advancedColumn = {
  key: ADVANCED_COLUMN_TITLE,
  fieldtype: ADVANCED_COLUMN_TITLE,
  type: ADVANCED_COLUMN_TYPE,
  config: {
    advancedColumns: [{ key: 'firstname', type: INPUT_COLUMN_TYPE, config: {} }],
    transformers: [{ type: 'concatenator' }]
  }
}

const legacyClassificationStoreColumn = {
  key: LEGACY_CLASSIFICATION_STORE_KEY,
  fieldtype: LEGACY_CLASSIFICATION_STORE_KEY,
  type: CLASSIFICATION_STORE_TYPE
}

describe('column fieldtype helpers', () => {
  it('derives the token from the field definition of the column', () => {
    assert.equal(deriveColumnFieldtype(inputColumn), INPUT_FIELDTYPE)
  })

  it('derives the token from the segment of the type after the last dot without a field definition', () => {
    assert.equal(
      deriveColumnFieldtype({ key: FIELD_NAME, type: 'dataobject.manyToManyObjectRelation' }),
      RELATION_FIELDTYPE
    )
  })

  it('derives the whole type when it carries no dot', () => {
    assert.equal(deriveColumnFieldtype({ key: FIELD_NAME, type: 'integer' }), 'integer')
  })

  it('falls through to the type segment for an empty or missing field definition fieldtype', () => {
    assert.equal(
      deriveColumnFieldtype({ key: FIELD_NAME, type: INPUT_COLUMN_TYPE, config: { fieldDefinition: { fieldtype: '' } } }),
      INPUT_FIELDTYPE
    )
    assert.equal(
      deriveColumnFieldtype({ key: FIELD_NAME, type: INPUT_COLUMN_TYPE, config: { fieldDefinition: {} } }),
      INPUT_FIELDTYPE
    )
    assert.equal(deriveColumnFieldtype({ key: FIELD_NAME, type: INPUT_COLUMN_TYPE }), INPUT_FIELDTYPE)
  })

  it('keeps a stored token which names a known field definition type', () => {
    assert.equal(readStoredFieldtype(INPUT_FIELDTYPE, isKnownType), INPUT_FIELDTYPE)
  })

  it('reads no type from a stored field name', () => {
    assert.equal(readStoredFieldtype(FIELD_NAME, isKnownType), undefined)
  })

  it('reads no type from an empty or missing stored token', () => {
    assert.equal(readStoredFieldtype('', isKnownType), undefined)
    assert.equal(readStoredFieldtype(undefined, isKnownType), undefined)
  })

  it('returns a known stored token unchanged and never guesses one from the column type', () => {
    assert.equal(readStoredFieldtype(RELATION_FIELDTYPE, isKnownType), RELATION_FIELDTYPE)
  })

  it('derives a token which is no known field type for an advanced column', () => {
    const derived = deriveColumnFieldtype(advancedColumn)

    assert.equal(derived, 'advanced')
    assert.equal(isKnownType(derived), false)
    assert.equal(readStoredFieldtype(derived, isKnownType), undefined)
  })

  it('round-trips an advanced column with its title in the key and its pipeline in the config', () => {
    const roundTripped = advancedToSchemaColumn(advancedFromSchemaColumn(advancedColumn))

    assert.equal(roundTripped.key, ADVANCED_COLUMN_TITLE)
    assert.deepEqual(roundTripped.config.advancedColumns, advancedColumn.config.advancedColumns)
    assert.deepEqual(roundTripped.config.transformers, advancedColumn.config.transformers)
  })

  it('matches an available field by key', () => {
    assert.equal(
      matchesAvailableColumn({ key: FIELD_NAME, type: INPUT_COLUMN_TYPE }, inputColumn, ADVANCED_COLUMN_TYPE),
      true
    )
    assert.equal(
      matchesAvailableColumn({ key: 'firstname', type: INPUT_COLUMN_TYPE }, inputColumn, ADVANCED_COLUMN_TYPE),
      false
    )
  })

  it('matches an advanced column by type, because its key holds the user title', () => {
    assert.equal(
      matchesAvailableColumn({ key: 'advanced', type: ADVANCED_COLUMN_TYPE }, advancedColumn, ADVANCED_COLUMN_TYPE),
      true
    )
  })

  it('matches a legacy integer-pair classification store key, also when the payload offers an identifier form', () => {
    const availableWithPairOnly = {
      key: LEGACY_CLASSIFICATION_STORE_KEY,
      type: CLASSIFICATION_STORE_TYPE
    }
    const availableWithIdentifier = {
      key: LEGACY_CLASSIFICATION_STORE_KEY,
      identifier: 'colours~size',
      type: CLASSIFICATION_STORE_TYPE
    }

    assert.equal(
      matchesAvailableColumn(availableWithPairOnly, legacyClassificationStoreColumn, ADVANCED_COLUMN_TYPE),
      true
    )
    assert.equal(
      matchesAvailableColumn(availableWithIdentifier, legacyClassificationStoreColumn, ADVANCED_COLUMN_TYPE),
      true
    )
  })
})
