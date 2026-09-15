/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React from 'react'
import { useStyles } from './proposal-card.styles'

export interface CardNode {
  readonly key: string
  /** a section the editor is not currently showing */
  readonly muted?: boolean
  readonly body: React.ReactNode
}

interface ProposalCardProps {
  readonly name: string
  readonly description?: string
  /** what the proposal does to the configuration, as tags beside its name */
  readonly tags?: React.ReactNode
  readonly nodes: CardNode[]
  readonly footer?: React.ReactNode
}

/**
 * The rail of a proposed configuration change, for any Data Hub adapter: the configuration it
 * is about, then a dotted spine of everything worth saying about it.
 *
 * Presentation only — it knows nothing about how a proposal is stored or reviewed, which is
 * what lets every adapter draw the same rail without agreeing on anything else. Nothing here
 * decides anything either: a proposal is approved or rejected whole, so the only control is a
 * section label, which opens it.
 */
export const ProposalCard: React.FC<ProposalCardProps> = ({ name, description, tags, nodes, footer }) => {
  const { styles, cx } = useStyles()

  return (
    <div className={ styles.card }>
      <div className={ styles.head }>
        <div className={ styles.identity }>
          <span className={ styles.name }>{ name }</span>
          { tags }
        </div>
        { description !== undefined && <div className={ styles.description }>{ description }</div> }
      </div>

      <div className={ styles.spine }>
        { nodes.map((node, index) => (
          <React.Fragment key={ node.key }>
            <div className={ styles.mark }>
              <span className={ cx(styles.node, node.muted === true && styles.nodeMuted) } />
              { index < nodes.length - 1 && <span className={ styles.line } /> }
            </div>
            <div className={ index === nodes.length - 1 ? styles.entryLast : styles.entry }>{ node.body }</div>
          </React.Fragment>
        )) }
      </div>

      { footer }
    </div>
  )
}

/** Whether the configuration is switched on, which no change set says on its own. */
export const StatePill: React.FC<{ readonly active: boolean, readonly label: string }> = ({ active, label }) => {
  const { styles, cx } = useStyles()

  return (
    <span className={ styles.pill }>
      <span className={ cx(styles.dot, active && styles.dotOn) } />
      { label }
    </span>
  )
}
