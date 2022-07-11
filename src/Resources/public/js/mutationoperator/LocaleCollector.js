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

pimcore.registerNS("pimcore.plugin.datahub.mutationoperator.localecollector");

pimcore.plugin.datahub.mutationoperator.localecollector = Class.create(pimcore.plugin.datahub.mutationoperator.mutationoperator, {
    class: "LocaleCollector",
    iconCls: "plugin_pimcore_datahub_icon_localecollector",
    defaultText: "Locale Collector",
    group: "other",
    hasTooltip: true,

    allowChild: function (targetNode, dropNode) {
        return (
            !targetNode.childNodes.length > 0
            && in_array(dropNode.data.dataType, [
                "booleanSelect",
                "checkbox",
                "country",
                "countrymultiselect",
                "date",
                "datetime",
                "email",
                "externalImage",
                "geopoint",
                "firstname",
                "gender",
                "input",
                "image",
                "language",
                "lastname",
                "newsletterActive",
                "manyToOneRelation",
                "multiselect",
                "newsletterConfirmed",
                "numeric",
                "select",
                "slider",
                "textarea",
                "time",
                "wysiwyg"
            ])
        );
    }
});
