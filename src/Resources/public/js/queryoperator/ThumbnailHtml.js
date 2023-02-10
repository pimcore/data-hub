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

pimcore.registerNS("pimcore.plugin.datahub.queryoperator.thumbnailhtml");

pimcore.plugin.datahub.queryoperator.thumbnailhtml = Class.create(pimcore.plugin.datahub.Abstract, {
    operatorGroup: "transformer",
    type: "operator",
    class: "ThumbnailHtml",
    iconCls: "pimcore_icon_thumbnails",
    defaultText: "Thumbnail HTML",
    group: "other",

    getConfigTreeNode: function(configAttributes) {
        if (configAttributes) {
            var node = {
                draggable: true,
                iconCls: this.iconCls,
                text: configAttributes.label ? configAttributes.label : this.getDefaultText(),
                configAttributes: configAttributes,
                isTarget: true,
                expanded: true,
                leaf: false,
                expandable: false,
                allowChildren: true,
                isChildAllowed: this.allowChild
            };
        } else {
            //For building up operator list
            var configAttributes = { type: this.type, class: this.class, label: this.getDefaultText() };

            var node = {
                draggable: true,
                iconCls: this.iconCls,
                text: this.getDefaultText(),
                configAttributes: configAttributes,
                isTarget: true,
                leaf: true,
                isChildAllowed: this.allowChild
            };
        }
        node.isOperator = true;

        return node;
    },

    getCopyNode: function(source) {
        var copy = source.createNode({
            iconCls: this.iconCls,
            text: source.data.text,
            isTarget: true,
            leaf: false,
            expanded: true,
            isOperator: true,
            isChildAllowed: this.allowChild,
            configAttributes: {
                label: source.data.configAttributes.label,
                type: this.type,
                class: this.class
            }
        });

        return copy;
    },

    getConfigDialog: function(node, params) {
        this.node = node;

        this.textField = new Ext.form.TextField({
            fieldLabel: t('attribute'),
            length: 255,
            width: 200,
            value: this.node.data.configAttributes.label
        });

        this.thumbnailHtmlConfigField = new Ext.form.ComboBox({
            width: 500,
            autoSelect: true,
            valueField: "id",
            displayField: "id",
            value: this.node.data.configAttributes.thumbnailHtmlConfig,
            fieldLabel: t("thumbnail"),
            store: new Ext.data.Store({
                autoDestroy: true,
                autoLoad: true,
                proxy: {
                    type: 'ajax',
                    url: '/admin/pimcoredatahub/config/thumbnail-tree',
                    reader: {
                        type: 'json'
                    }
                },
                listeners: {
                    load: function() {
                        this.thumbnailHtmlConfigField.setValue(this.node.data.configAttributes.thumbnailHtmlConfig);
                    }.bind(this)
                },
                fields: ['id']
            }),
            triggerAction: "all"
        });

        this.configPanel = new Ext.Panel({
            layout: "form",
            bodyStyle: "padding: 10px;",
            items: [this.textField, this.thumbnailHtmlConfigField],
            buttons: [{
                text: t("apply"),
                iconCls: "pimcore_icon_apply",
                handler: function () {
                    this.commitData(params);
                }.bind(this)
            }]
        });

        this.window = new Ext.Window({
            width: 400,
            height: 350,
            modal: true,
            title: t('settings'),
            layout: "fit",
            items: [this.configPanel]
        });
        this.window.show();

        return this.window;
    },

    commitData: function(params) {
        this.node.set('isOperator', true);
        this.node.data.configAttributes.label = this.textField.getValue();
        this.node.data.configAttributes.thumbnailHtmlConfig = this.thumbnailHtmlConfigField.getValue();
        this.window.close();

        if (params && params.callback) {
            params.callback();
        }
    },

    allowChild: function (targetNode, dropNode) {
        if (targetNode.childNodes.length > 0) {
            return false;
        }

        return true;
    }
});
