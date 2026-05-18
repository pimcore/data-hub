/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { type ReactNode, useState } from 'react'
import {
  Alert,
  Button,
  CodeEditor,
  Flex,
  Modal,
  ModalTitle,
  Space,
  Toolbar
} from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { stringifyYaml } from '../../../../sdk/utils/yaml'

export interface MigrationModalProps {
  /** Controls modal visibility */
  open: boolean
  /** Modal title — already translated by the consumer */
  title: string
  /** Icon name rendered in the modal title bar (defaults to "settings") */
  iconName?: string
  /** The raw legacy config object — serialized to YAML internally for the read-only reference pane */
  legacyConfig: Record<string, unknown>
  /**
   * Optional toolbar slot rendered left-aligned in the split-view toolbar.
   * Use this to provide an "Add column" / "Add field" dropdown specific to your editor.
   */
  renderToolbarLeft?: ReactNode
  /**
   * Called when the user clicks "Confirm Migration".
   * The consumer is responsible for reading the current state from its editor
   * (e.g. via an imperative ref) and persisting it before or after this callback.
   */
  onConfirm: () => void
  /**
   * Called when the user closes the modal from either the initial legacy view
   * (X button or Cancel in the initial toolbar) or from the split migration view
   * (Cancel button returns to the initial view — the modal stays open).
   * To close the modal entirely from the initial view, this should set `open` to false.
   */
  onClose: () => void
  /**
   * The new-format editor component rendered in the left pane during migration.
   * The consumer is responsible for wiring this up with its own state / ref.
   */
  children: ReactNode
}

/**
 * MigrationModal — generic split-view migration shell for Data Hub adapters.
 *
 * Manages two visual states internally:
 *  1. **Initial legacy view** (`isMigrating = false`): shows the legacy config as
 *     read-only YAML with a warning alert and a "Start Migration" button.
 *  2. **Split migration view** (`isMigrating = true`): left pane renders `children`
 *     (the consumer's new editor), right pane shows the legacy YAML as reference.
 *     Toolbar provides Cancel (returns to initial view) and Confirm Migration.
 */
export const MigrationModal = ({
  open,
  title,
  iconName = 'settings',
  legacyConfig,
  renderToolbarLeft,
  onConfirm,
  onClose,
  children
}: MigrationModalProps): React.JSX.Element => {
  const { t } = useTranslation()
  const [isMigrating, setIsMigrating] = useState(false)

  const yamlValue = stringifyYaml(legacyConfig)

  const modalTitle = (
    <ModalTitle iconName={ iconName }>
      { title }
    </ModalTitle>
  )

  // ── Initial legacy view ──────────────────────────────────────────────────
  if (!isMigrating) {
    return (
      <Modal
        footer={ null }
        onCancel={ onClose }
        open={ open }
        size="XL"
        title={ modalTitle }
      >
        <Flex
          gap="small"
          vertical
        >
          <Alert
            description={ t('data-hub.migration-modal.legacy-notice') }
            showIcon
            type="warning"
          />

          <CodeEditor
            height="400px"
            preset="yaml"
            readOnly
            value={ yamlValue }
          />
        </Flex>

        <Toolbar
          padding={ { x: 'none', y: 'small' } }
          theme='secondary'
        >
          <Space size='extra-small'>
            <Button
              onClick={ () => { setIsMigrating(true) } }
              type='primary'
            >
              { t('data-hub.migration-modal.start-migration') }
            </Button>
          </Space>
        </Toolbar>
      </Modal>
    )
  }

  // ── Split migration view ─────────────────────────────────────────────────
  return (
    <Modal
      footer={ null }
      onCancel={ () => { setIsMigrating(false) } }
      open={ open }
      size="XL"
      title={ modalTitle }
    >
      <Flex
        gap="small"
        style={ { height: 'calc(80vh - 120px)', overflow: 'hidden' } }
      >
        { /* Left — new editor supplied by consumer */ }
        <Flex
          style={ { flex: 1, minWidth: 0, overflow: 'hidden' } }
          vertical
        >
          { children }
        </Flex>

        <div style={ { width: 1, background: 'var(--ant-color-split, rgba(0,0,0,.06))', flexShrink: 0 } } />

        { /* Right — legacy config (read-only YAML reference) */ }
        <Flex
          gap="small"
          style={ { flex: 1, minWidth: 0, overflow: 'auto' } }
          vertical
        >
          <Alert
            description={ t('data-hub.migration-modal.legacy-notice') }
            showIcon
            type="warning"
          />
          <CodeEditor
            height="100%"
            preset="yaml"
            readOnly
            value={ yamlValue }
          />
        </Flex>
      </Flex>

      <Toolbar
        padding={ { x: 'none', y: 'small' } }
        theme='secondary'
      >
        { renderToolbarLeft }

        <Space size='extra-small'>
          <Button
            onClick={ () => { setIsMigrating(false) } }
            type='default'
          >
            { t('data-hub.migration-modal.cancel') }
          </Button>

          <Button
            onClick={ onConfirm }
            type='primary'
          >
            { t('data-hub.migration-modal.confirm-migration') }
          </Button>
        </Space>
      </Toolbar>
    </Modal>
  )
}
