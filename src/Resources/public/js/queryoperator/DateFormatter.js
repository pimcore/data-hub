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

pimcore.registerNS("pimcore.plugin.datahub.queryoperator.dateformatter");

pimcore.plugin.datahub.queryoperator.dateformatter = Class.create(pimcore.plugin.datahub.Abstract, {

    operatorGroup: "formatter",
    type: "operator",
    class: "DateFormatter",
    iconCls: "pimcore_icon_datetime",
    defaultText: "DateFormatter",
    group: "other",

    getConfigTreeNode: function (configAttributes) {
        if (configAttributes) {
            var nodeLabel = this.getNodeLabel(configAttributes);
            var node = {
                draggable: true,
                iconCls: this.iconCls,
                text: nodeLabel,
                configAttributes: configAttributes,
                isTarget: true,
                maxChildCount: 1,
                expanded: true,
                leaf: false,
                expandable: false,
                isChildAllowed: this.allowChild
            };
        } else {

            //For building up operator list
            var configAttributes = {type: this.type, class: this.class, label: this.getDefaultText()};

            var node = {
                draggable: true,
                iconCls: this.iconCls,
                text: this.getDefaultText(),
                configAttributes: configAttributes,
                isTarget: true,
                maxChildCount: 1,
                leaf: true,
                isChildAllowed: this.allowChild
            };
        }
        node.isOperator = true;
        return node;
    },

    getCopyNode: function (source) {

        var copy = source.createNode({
            iconCls: source.data.iconCls,
            text: source.data.text,
            isTarget: true,
            leaf: false,
            expandable: false,
            dataType: source.data.dataType,
            qtip: source.data.key,
            configAttributes: {
                label: source.data.text,
                type: this.type,
                class: this.class,
                attribute: source.data.key,
                dataType: source.data.dataType
            }
        });
        return copy;
    },

    getConfigDialog: function (node, params) {
        this.node = node;

        this.textField = new Ext.form.TextField({
            fieldLabel: t('label'),
            length: 255,
            width: 200,
            value: this.node.data.configAttributes.label
        });

        this.formatField = new Ext.form.TextField({
            label_width: 200,
            fieldLabel: t('date_format'),
            length: 255,
            width: 200,
            value: this.node.data.configAttributes.format
        });

        var helpButton = new Ext.Button({
            text: t("help"),
            handler: function () {
                window.open("http://php.net/manual/en/function.date.php");
            },
            iconCls: "pimcore_icon_help"
        });


        this.configPanel = new Ext.Panel({
            layout: "form",
            bodyStyle: "padding: 10px;",
            items: [this.textField, this.formatField, helpButton],
            buttons: [{
                text: t("apply"),
                iconCls: "pimcore_icon_apply",
                handler: function () {
                    this.commitData(params);
                }.bind(this)
            }]
        });

        this.window = new Ext.Window({
            width: 500,
            height: 250,
            modal: true,
            title: t('settings'),
            layout: "fit",
            items: [this.configPanel]
        });

        this.window.show();
        return this.window;
    },

    commitData: function (params) {
        this.node.data.configAttributes.label = this.textField.getValue();
        this.node.data.configAttributes.format = this.formatField.getValue();
        this.node.set('isOperator', true);
        this.window.close();

        if (params && params.callback) {
            params.callback();
        }
    },

    getNodeLabel: function (configAttributes) {
        var nodeLabel = configAttributes.label ? configAttributes.label : this.getDefaultText();
        if (configAttributes.format) {
            nodeLabel += '<span class="pimcore_gridnode_hint"> (' + configAttributes.format + ')</span>';
        }

        return nodeLabel;
    },

    allowChild: function (targetNode, dropNode) {
        if (targetNode.childNodes.length > 0) {
            return false;
        }
        return true;
    }
});