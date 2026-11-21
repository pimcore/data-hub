/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import tseslint from 'typescript-eslint'
import js from '@eslint/js'
import globals from 'globals'
import stylistic from '@stylistic/eslint-plugin'
import reactPlugin from 'eslint-plugin-react'
import jsxA11y from 'eslint-plugin-jsx-a11y'
import importPlugin from 'eslint-plugin-import'
import promisePlugin from 'eslint-plugin-promise'
import nPlugin from 'eslint-plugin-n'
import headerPluginRaw from 'eslint-plugin-header'
import { fileURLToPath } from 'url'
import path from 'path'

const __filename = fileURLToPath(import.meta.url)
const __dirname = path.dirname(__filename)

// eslint-plugin-header v3.x has no schema defined; ESLint v9 defaults to []
// which blocks all options. Patch in a permissive schema as a workaround.
const headerPlugin = {
  ...headerPluginRaw,
  rules: {
    ...headerPluginRaw.rules,
    header: {
      ...headerPluginRaw.rules.header,
      meta: {
        ...headerPluginRaw.rules.header.meta,
        schema: { type: 'array' }
      }
    }
  }
}

export default tseslint.config(
  // 1. Ignore patterns
  {
    ignores: ['**/*.gen.ts']
  },

  // 2. Main config for all TS/TSX/JS/JSX source files
  {
    files: ['**/*.{js,jsx,ts,tsx}'],
    extends: [
      js.configs.recommended,
      ...tseslint.configs.recommended
    ],
    plugins: {
      '@stylistic': stylistic,
      react: reactPlugin,
      'jsx-a11y': jsxA11y,
      import: importPlugin,
      promise: promisePlugin,
      n: nPlugin,
      header: headerPlugin
    },
    languageOptions: {
      globals: {
        ...globals.browser,
        ...globals.es2021
      },
      parserOptions: {
        project: true,
        tsconfigRootDir: __dirname,
        ecmaFeatures: { jsx: true }
      }
    },
    settings: {
      react: { version: 'detect' }
    },
    rules: {
      // -----------------------------------------------------------------------
      // React rules (from plugin:react/recommended)
      // -----------------------------------------------------------------------
      ...reactPlugin.configs.flat.recommended.rules,

      // -----------------------------------------------------------------------
      // jsx-a11y rules (from plugin:jsx-a11y/recommended)
      // -----------------------------------------------------------------------
      ...jsxA11y.flatConfigs.recommended.rules,

      // -----------------------------------------------------------------------
      // import rules (subset from eslint-config-standard)
      // -----------------------------------------------------------------------
      'import/export': 'error',
      'import/first': 'error',
      'import/no-absolute-path': ['error', { esmodule: true, commonjs: true, amd: false }],
      'import/no-duplicates': 'error',
      'import/no-named-default': 'error',
      'import/no-webpack-loader-syntax': 'error',

      // -----------------------------------------------------------------------
      // promise rules (subset from eslint-config-standard)
      // -----------------------------------------------------------------------
      'promise/param-names': 'error',

      // -----------------------------------------------------------------------
      // n rules (subset from eslint-config-standard)
      // -----------------------------------------------------------------------
      'n/handle-callback-err': ['error', '^(err|error)$'],
      'n/no-callback-literal': 'error',
      'n/no-deprecated-api': 'error',
      'n/no-exports-assign': 'error',
      'n/no-new-require': 'error',
      'n/no-path-concat': 'error',
      'n/process-exit-as-throw': 'error',

      // -----------------------------------------------------------------------
      // Standard JS style rules (from eslint-config-standard)
      // Base ESLint rules that are not superseded by @typescript-eslint/*
      // -----------------------------------------------------------------------
      'no-var': 'warn',
      'object-shorthand': ['warn', 'properties'],
      'accessor-pairs': ['error', { setWithoutGet: true, enforceForClassMembers: true }],
      'array-bracket-spacing': ['error', 'never'],
      'array-callback-return': ['error', { allowImplicit: false, checkForEach: false }],
      'arrow-spacing': ['error', { before: true, after: true }],
      'comma-style': ['error', 'last'],
      'computed-property-spacing': ['error', 'never', { enforceForClassMembers: true }],
      'constructor-super': 'error',
      'curly': ['error', 'multi-line'],
      'default-case-last': 'error',
      'dot-location': ['error', 'property'],
      'eol-last': 'error',
      'eqeqeq': ['error', 'always', { null: 'ignore' }],
      'generator-star-spacing': ['error', { before: true, after: true }],
      'new-cap': ['error', { newIsCap: true, capIsNew: false, properties: true }],
      'new-parens': 'error',
      'no-array-constructor': 'off', // superseded by @typescript-eslint/no-array-constructor
      'no-async-promise-executor': 'error',
      'no-caller': 'error',
      'no-case-declarations': 'error',
      'no-class-assign': 'error',
      'no-compare-neg-zero': 'error',
      'no-cond-assign': 'error',
      'no-const-assign': 'error',
      'no-constant-condition': ['error', { checkLoops: false }],
      'no-control-regex': 'error',
      'no-debugger': 'error',
      'no-delete-var': 'error',
      'no-dupe-args': 'error',
      'no-dupe-keys': 'error',
      'no-duplicate-case': 'error',
      'no-empty': ['error', { allowEmptyCatch: true }],
      'no-empty-character-class': 'error',
      'no-empty-pattern': 'error',
      'no-eval': 'error',
      'no-ex-assign': 'error',
      'no-extend-native': 'error',
      'no-extra-bind': 'error',
      'no-extra-boolean-cast': 'error',
      'no-fallthrough': 'error',
      'no-floating-decimal': 'error',
      'no-func-assign': 'error',
      'no-global-assign': 'error',
      'no-import-assign': 'error',
      'no-invalid-regexp': 'error',
      'no-irregular-whitespace': 'error',
      'no-iterator': 'error',
      'no-labels': ['error', { allowLoop: true }],
      'no-lone-blocks': 'error',
      'no-misleading-character-class': 'error',
      'no-multi-str': 'error',
      'no-new': 'error',
      'no-new-func': 'error',
      'no-new-object': 'error',
      'no-new-wrappers': 'error',
      'no-obj-calls': 'error',
      'no-octal': 'error',
      'no-octal-escape': 'error',
      'no-proto': 'error',
      'no-prototype-builtins': 'error',
      'no-regex-spaces': 'error',
      'no-return-assign': ['error', 'except-parens'],
      'no-self-assign': ['error', { props: true }],
      'no-self-compare': 'error',
      'no-sequences': 'error',
      'no-setter-return': 'error',
      'no-shadow-restricted-names': 'error',
      'no-sparse-arrays': 'error',
      'no-template-curly-in-string': 'error',
      'no-this-before-super': 'error',
      'no-undef-init': 'error',
      'no-unexpected-multiline': 'error',
      'no-unmodified-loop-condition': 'error',
      'no-unneeded-ternary': ['error', { defaultAssignment: false }],
      'no-unreachable': 'error',
      'no-unreachable-loop': 'error',
      'no-unsafe-finally': 'error',
      'no-unsafe-negation': 'error',
      'no-unused-private-class-members': 'error',
      'no-useless-backreference': 'error',
      'no-useless-call': 'error',
      'no-useless-catch': 'error',
      'no-useless-computed-key': 'error',
      'no-useless-escape': 'error',
      'no-useless-rename': 'error',
      'no-useless-return': 'error',
      'no-whitespace-before-property': 'error',
      'no-with': 'error',
      'object-curly-newline': ['error', { multiline: true, consistent: true }],
      'object-property-newline': ['error', { allowMultiplePropertiesPerLine: true }],
      'one-var': ['error', { initialized: 'never' }],
      'operator-linebreak': ['error', 'after', { overrides: { '?': 'before', ':': 'before', '|>': 'before' } }],
      'padded-blocks': ['error', { blocks: 'never', switches: 'never', classes: 'never' }],
      'prefer-const': ['error', { destructuring: 'all' }],
      'prefer-promise-reject-errors': 'error',
      'prefer-regex-literals': ['error', { disallowRedundantWrapping: true }],
      'quote-props': ['error', 'as-needed'],
      'rest-spread-spacing': ['error', 'never'],
      'semi-spacing': ['error', { before: false, after: true }],
      'space-in-parens': ['error', 'never'],
      'space-unary-ops': ['error', { words: true, nonwords: false }],
      'spaced-comment': ['error', 'always', {
        line: { markers: ['*package', '!', '/', ',', '='] },
        block: { balanced: true, markers: ['*package', '!', ',', ':', '::', 'flow-include'], exceptions: ['*'] }
      }],
      'symbol-description': 'error',
      'template-curly-spacing': ['error', 'never'],
      'template-tag-spacing': ['error', 'never'],
      'unicode-bom': ['error', 'never'],
      'use-isnan': ['error', { enforceForSwitchCase: true, enforceForIndexOf: true }],
      'valid-typeof': ['error', { requireStringLiterals: true }],
      'wrap-iife': ['error', 'any', { functionPrototypeMethods: true }],
      'yield-star-spacing': ['error', 'both'],
      'yoda': ['error', 'never'],
      // Turn off base rules superseded by @typescript-eslint/* equivalents
      'camelcase': 'off',
      'dot-notation': 'off',
      'no-implied-eval': 'off',
      'no-loss-of-precision': 'off',
      'no-redeclare': 'off',
      'no-throw-literal': 'off',
      'no-unused-vars': 'off',
      'no-unused-expressions': 'off',
      '@typescript-eslint/no-unused-expressions': 'off',
      'no-useless-constructor': 'off',
      'no-void': ['error', { allowAsStatement: true }],

      // -----------------------------------------------------------------------
      // @stylistic rules (replaces the 17 formatting rules removed from
      // @typescript-eslint v8, previously wired through standard-with-typescript)
      // -----------------------------------------------------------------------
      '@stylistic/block-spacing': ['error', 'always'],
      '@stylistic/brace-style': ['error', '1tbs', { allowSingleLine: true }],
      '@stylistic/comma-dangle': ['error', {
        arrays: 'never',
        objects: 'never',
        imports: 'never',
        exports: 'never',
        functions: 'never',
        enums: 'ignore',
        generics: 'ignore',
        tuples: 'ignore'
      }],
      '@stylistic/comma-spacing': ['error', { before: false, after: true }],
      '@stylistic/function-call-spacing': ['error', 'never'],
      '@stylistic/indent': ['error', 2, {
        SwitchCase: 1,
        VariableDeclarator: 1,
        outerIIFEBody: 1,
        MemberExpression: 1,
        FunctionDeclaration: { parameters: 1, body: 1 },
        FunctionExpression: { parameters: 1, body: 1 },
        CallExpression: { arguments: 1 },
        ArrayExpression: 1,
        ObjectExpression: 1,
        ImportDeclaration: 1,
        flatTernaryExpressions: false,
        ignoreComments: false,
        ignoredNodes: [
          'TemplateLiteral *',
          'JSXElement',
          'JSXElement > *',
          'JSXAttribute',
          'JSXIdentifier',
          'JSXNamespacedName',
          'JSXMemberExpression',
          'JSXSpreadAttribute',
          'JSXExpressionContainer',
          'JSXOpeningElement',
          'JSXClosingElement',
          'JSXFragment',
          'JSXOpeningFragment',
          'JSXClosingFragment',
          'JSXText',
          'JSXEmptyExpression',
          'JSXSpreadChild'
        ],
        offsetTernaryExpressions: true
      }],
      '@stylistic/key-spacing': ['error', { beforeColon: false, afterColon: true }],
      '@stylistic/keyword-spacing': ['error', { before: true, after: true }],
      '@stylistic/lines-between-class-members': ['error', 'always', { exceptAfterSingleLine: true }],
      '@stylistic/member-delimiter-style': ['error', {
        multiline: { delimiter: 'none' },
        singleline: { delimiter: 'comma', requireLast: false }
      }],
      '@stylistic/object-curly-spacing': ['error', 'always'],
      '@stylistic/quotes': ['error', 'single', { avoidEscape: true, allowTemplateLiterals: 'never' }],
      '@stylistic/semi': ['error', 'never'],
      '@stylistic/space-before-blocks': ['error', 'always'],
      '@stylistic/space-before-function-paren': ['error', 'always'],
      '@stylistic/space-infix-ops': 'error',
      '@stylistic/type-annotation-spacing': 'error',

      // -----------------------------------------------------------------------
      // @typescript-eslint type-aware rules (from standard-with-typescript)
      // -----------------------------------------------------------------------
      // no-explicit-any / no-unused-vars: turned off — pre-existing widespread usage;
      // address in a dedicated cleanup PR
      '@typescript-eslint/no-explicit-any': 'off',
      '@typescript-eslint/no-unused-vars': 'off',
      'no-unused-vars': 'off',
      // -----------------------------------------------------------------------
      '@typescript-eslint/consistent-type-assertions': ['error', {
        assertionStyle: 'as',
        objectLiteralTypeAssertions: 'never'
      }],
      '@typescript-eslint/consistent-type-definitions': ['error', 'interface'],
      '@typescript-eslint/consistent-type-exports': 'off',
      '@typescript-eslint/consistent-type-imports': ['error', {
        prefer: 'type-imports',
        disallowTypeAnnotations: true,
        fixStyle: 'inline-type-imports'
      }],
      '@typescript-eslint/explicit-function-return-type': ['error', {
        allowExpressions: true,
        allowHigherOrderFunctions: true,
        allowTypedFunctionExpressions: true,
        allowDirectConstAssertionInArrowFunctions: true
      }],
      '@typescript-eslint/method-signature-style': 'error',
      '@typescript-eslint/naming-convention': ['error', {
        selector: 'variableLike',
        leadingUnderscore: 'allow',
        trailingUnderscore: 'allow',
        format: ['camelCase', 'PascalCase', 'UPPER_CASE']
      }],
      '@typescript-eslint/no-array-constructor': 'error',
      '@typescript-eslint/no-base-to-string': 'off',
      '@typescript-eslint/no-confusing-void-expression': ['error', {
        ignoreArrowShorthand: false,
        ignoreVoidOperator: false
      }],
      '@typescript-eslint/no-dynamic-delete': 'error',
      '@typescript-eslint/no-extraneous-class': ['error', { allowWithDecorator: true }],
      '@typescript-eslint/no-floating-promises': 'error',
      '@typescript-eslint/no-for-in-array': 'error',
      '@typescript-eslint/no-invalid-void-type': 'error',
      '@typescript-eslint/no-misused-new': 'error',
      '@typescript-eslint/no-misused-promises': 'off',
      '@typescript-eslint/no-namespace': 'error',
      '@typescript-eslint/no-non-null-asserted-optional-chain': 'error',
      '@typescript-eslint/no-non-null-assertion': 'off',
      '@typescript-eslint/no-this-alias': ['error', { allowDestructuring: true }],
      '@typescript-eslint/no-unnecessary-boolean-literal-compare': 'error',
      '@typescript-eslint/no-unnecessary-type-assertion': 'error',
      '@typescript-eslint/no-unnecessary-type-constraint': 'error',
      '@typescript-eslint/no-unsafe-argument': 'error',
      '@typescript-eslint/non-nullable-type-assertion-style': 'error',
      '@typescript-eslint/prefer-function-type': 'error',
      '@typescript-eslint/prefer-includes': 'error',
      '@typescript-eslint/prefer-nullish-coalescing': ['error', {
        ignoreConditionalTests: false,
        ignoreMixedLogicalExpressions: false
      }],
      '@typescript-eslint/prefer-optional-chain': 'error',
      '@typescript-eslint/prefer-readonly': 'error',
      '@typescript-eslint/prefer-reduce-type-parameter': 'error',
      '@typescript-eslint/prefer-return-this-type': 'error',
      '@typescript-eslint/promise-function-async': 'error',
      '@typescript-eslint/require-array-sort-compare': ['error', { ignoreStringArrays: true }],
      '@typescript-eslint/restrict-plus-operands': ['error', { skipCompoundAssignments: false }],
      '@typescript-eslint/restrict-template-expressions': ['error', { allowNumber: true }],
      '@typescript-eslint/return-await': ['error', 'always'],
      '@typescript-eslint/strict-boolean-expressions': 'off',
      '@typescript-eslint/triple-slash-reference': ['error', {
        lib: 'never',
        path: 'never',
        types: 'never'
      }],
      '@typescript-eslint/unbound-method': 'off',
      // v8: renamed no-var-requires → no-require-imports
      '@typescript-eslint/no-require-imports': 'error',
      // v8: ban-types split into targeted rules
      '@typescript-eslint/no-unsafe-function-type': 'error',
      '@typescript-eslint/no-wrapper-object-types': 'error',
      '@typescript-eslint/no-empty-object-type': ['error', { allowInterfaces: 'always' }],
      // v8: no-throw-literal renamed to only-throw-error
      '@typescript-eslint/only-throw-error': 'error',
      // v8: prefer-ts-expect-error deprecated in favour of ban-ts-comment option
      '@typescript-eslint/ban-ts-comment': ['error', { 'ts-expect-error': 'allow-with-description' }],

      // -----------------------------------------------------------------------
      // header (license block check)
      // -----------------------------------------------------------------------
      'header/header': [2, 'block', [
        '*',
        ' * This source file is available under the terms of the',
        ' * Pimcore Open Core License (POCL)',
        ' * Full copyright and license information is available in',
        ' * LICENSE.md which is distributed with this source code.',
        ' *',
        ' *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)',
        ' *  @license    Pimcore Open Core License (POCL)',
        ' '
      ], 2],

      // -----------------------------------------------------------------------
      // React JSX rules (from original .eslintrc.js)
      // -----------------------------------------------------------------------
      'react/jsx-boolean-value': 'error',
      'react/jsx-closing-bracket-location': 'error',
      'react/jsx-curly-spacing': ['error', 'always'],
      'react/jsx-equals-spacing': 'error',
      'react/jsx-first-prop-new-line': 'error',
      'react/jsx-indent-props': ['error', 2],
      'react/jsx-indent': ['error', 2],
      'react/jsx-key': 'error',
      'react/jsx-max-props-per-line': ['error', { maximum: 1 }],
      'react/jsx-no-literals': 'off',
      'react/jsx-no-target-blank': 'error',
      'react/jsx-pascal-case': 'error',
      'react/jsx-sort-props': 'error',
      'react/jsx-tag-spacing': 'error',
      'react/jsx-no-constructed-context-values': 'error',
      'react/jsx-wrap-multilines': ['error', {
        declaration: 'parens-new-line',
        assignment: 'parens-new-line',
        return: 'parens-new-line',
        arrow: 'parens-new-line',
        condition: 'parens-new-line',
        logical: 'parens-new-line',
        prop: 'ignore'
      }],

      // -----------------------------------------------------------------------
      // Misc
      // -----------------------------------------------------------------------
      'max-lines': ['error', { max: 300 }]
    }
  },

  // 3. Node environment for config files themselves
  {
    files: ['eslint.config.{js,mjs,cjs}', '.eslintrc.{js,cjs}'],
    languageOptions: {
      globals: { ...globals.node }
    }
  }
)
