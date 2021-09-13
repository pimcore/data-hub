/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @category   Pimcore
 * @package    Object
 *
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

pimcore.registerNS("pimcore.plugin.datahub.mutationoperator.ifempty");

pimcore.plugin.datahub.mutationoperator.ifempty = Class.create(pimcore.plugin.datahub.mutationoperator.mutationoperator, {
    class: "IfEmpty",
    iconCls: "plugin_pimcore_datahub_icon_ifempty",
    defaultText: "IfEmpty",
    group: "other",
    hasTooltip: true
});
