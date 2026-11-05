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
  readStoredFieldtype,
  resolveColumnFieldtype
} from '../js/src/modules/config/components/column-config-modal/column-fieldtype.ts'
import {
  ADVANCED_COLUMN_KEY,
  ADVANCED_COLUMN_TYPE,
  advancedFromSchemaColumn,
  advancedToSchemaColumn
} from '../js/src/modules/config/components/column-config-modal/types.ts'

const FIELD_NAME = 'title'
const INPUT_FIELDTYPE = 'input'
const INPUT_COLUMN_TYPE = 'dataobject.input'
const SELECT_COLUMN_TYPE = 'dataobject.select'
const RELATION_FIELDTYPE = 'manyToManyObjectRelation'
const CLASSIFICATION_STORE_FIELDTYPE = 'classificationstore'
const CLASSIFICATION_STORE_TYPE = 'dataobject.classificationstore'
const LEGACY_CLASSIFICATION_STORE_KEY = '3-17'
const ADVANCED_COLUMN_TITLE = 'Full name'
const ADVANCED_FIELDTYPE = 'advanced'
const NO_TYPE = ''

const knownTypes = new Set([INPUT_FIELDTYPE, RELATION_FIELDTYPE, CLASSIFICATION_STORE_FIELDTYPE, 'integer'])
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

// The available-columns payload as the editor reads it: the input field, a classification store field
// carrying its legacy integer pair as the key, and the advanced column.
const availableFields = [
  { key: FIELD_NAME, type: INPUT_COLUMN_TYPE, config: { fieldDefinition: { fieldtype: INPUT_FIELDTYPE } } },
  { key: LEGACY_CLASSIFICATION_STORE_KEY, type: CLASSIFICATION_STORE_TYPE },
  { key: ADVANCED_COLUMN_KEY, type: ADVANCED_COLUMN_TYPE }
]

// Mirrors how the hydration effect composes the two helpers in use-column-editor-state.ts.
const hydrate = (storedColumns) => storedColumns.map((col) => ({
  ...col,
  fieldtype: resolveColumnFieldtype(
    col,
    availableFields.find((field) => matchesAvailableColumn(field, col, ADVANCED_COLUMN_TYPE)),
    isKnownType
  )
}))

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

    assert.equal(derived, ADVANCED_FIELDTYPE)
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

  it('keeps matching an available field by key after its attribute type changed in the class', () => {
    assert.equal(
      matchesAvailableColumn({ key: FIELD_NAME, type: SELECT_COLUMN_TYPE }, inputColumn, ADVANCED_COLUMN_TYPE),
      true
    )
  })

  it('matches an advanced column by type, because its key holds the user title', () => {
    assert.equal(
      matchesAvailableColumn(availableFields[2], advancedColumn, ADVANCED_COLUMN_TYPE),
      true
    )
  })

  it('matches a stored integer-pair classification store key against the key the payload carries', () => {
    const availableWithPairOnly = {
      key: LEGACY_CLASSIFICATION_STORE_KEY,
      type: CLASSIFICATION_STORE_TYPE
    }
    // A payload which additionally offers an identifier form alongside the key it carries today.
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

  it('takes the token of the available column a stored column was configured from', () => {
    assert.equal(
      resolveColumnFieldtype(
        { key: FIELD_NAME, fieldtype: FIELD_NAME, type: INPUT_COLUMN_TYPE },
        availableFields[0],
        isKnownType
      ),
      INPUT_FIELDTYPE
    )
  })

  it('keeps the stored token of an unmatched column when it names a known type', () => {
    assert.equal(
      resolveColumnFieldtype(
        { key: 'gone', fieldtype: RELATION_FIELDTYPE, type: 'dataobject.manyToManyObjectRelation' },
        undefined,
        isKnownType
      ),
      RELATION_FIELDTYPE
    )
  })

  it('reads no type for an unmatched column, rather than carrying a field name on', () => {
    assert.equal(
      resolveColumnFieldtype(
        { key: 'gone', fieldtype: 'gone', type: INPUT_COLUMN_TYPE },
        undefined,
        isKnownType
      ),
      NO_TYPE
    )
    assert.equal(
      resolveColumnFieldtype({ key: 'gone', type: INPUT_COLUMN_TYPE }, undefined, isKnownType),
      NO_TYPE
    )
  })

  it('takes the token of an available field whose attribute type changed since the save', () => {
    const drifted = { key: FIELD_NAME, type: SELECT_COLUMN_TYPE, config: { fieldDefinition: { fieldtype: 'select' } } }
    const stored = { key: FIELD_NAME, fieldtype: FIELD_NAME, type: INPUT_COLUMN_TYPE }

    assert.equal(matchesAvailableColumn(drifted, stored, ADVANCED_COLUMN_TYPE), true)
    assert.equal(resolveColumnFieldtype(stored, drifted, isKnownType), 'select')
  })

  it('resolves every stored column the way the hydration path composes the two helpers', () => {
    const hydrated = hydrate([
      { key: FIELD_NAME, fieldtype: FIELD_NAME, type: INPUT_COLUMN_TYPE },
      legacyClassificationStoreColumn,
      advancedColumn,
      { key: 'removed', fieldtype: 'removed', type: INPUT_COLUMN_TYPE }
    ])

    assert.deepEqual(hydrated.map((col) => col.fieldtype), [
      INPUT_FIELDTYPE,
      CLASSIFICATION_STORE_FIELDTYPE,
      ADVANCED_FIELDTYPE,
      NO_TYPE
    ])
  })
})
