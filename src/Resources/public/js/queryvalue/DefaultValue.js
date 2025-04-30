/**
* This source file is available under the terms of the
* Pimcore Open Core License (POCL)
* Full copyright and license information is available in
* LICENSE.md which is distributed with this source code.
*
*  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.com)
*  @license    Pimcore Open Core License (POCL)
*/


pimcore.registerNS("pimcore.plugin.datahub.queryvalue.defaultvalue");

pimcore.plugin.datahub.queryvalue.defaultvalue = Class.create(pimcore.plugin.datahub.Abstract, {

    type: "value",
    class: "DefaultValue",

    getConfigTreeNode: function(configAttributes) {
        var node = {
            draggable: true,
            iconCls: "pimcore_icon_" + configAttributes.dataType,
            text: configAttributes.label,
            configAttributes: configAttributes,
            isTarget: true,
            leaf: true
        };

        return node;
    },

    getCopyNode: function(source) {
        var copy = source.createNode({
            iconCls: source.data.iconCls,
            text: source.data.text,
            isTarget: true,
            leaf: true,
            dataType: source.data.dataType,
            qtip: source.data.key,
            key: source.data.key,
            isOperator: source.data.isOperator ?? false,
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

    getConfigDialog: function(node, params) {
        return null;
    },

    commitData: function(params) {
        if(this.radiogroup.getValue().rb == "custom") {
            this.node.data.configAttributes.label = this.textfield.getValue();
            this.node.set('text', this.textfield.getValue());
        } else {
            this.node.data.configAttributes.label = this.node.get('text');
        }
        this.window.close();
        if (params && params.callback) {
            params.callback();
        }
    }
});