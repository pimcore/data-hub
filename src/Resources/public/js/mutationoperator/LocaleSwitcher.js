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

pimcore.registerNS("pimcore.plugin.datahub.mutationoperator.localeswitcher");

pimcore.plugin.datahub.mutationoperator.localeswitcher = Class.create(pimcore.plugin.datahub.mutationoperator.mutationoperator, {
    class: "LocaleSwitcher",
    iconCls: "pimcore_icon_operator_localeswitcher",
    defaultText: "Locale Switcher",
    group: "other",
    hasTooltip: true,

    getConfigDialog: function (node, params) {
        this.node = node;

        this.textField = new Ext.form.TextField({
            fieldLabel: t('label'),
            length: 255,
            width: 200,
            value: this.node.data.configAttributes.label
        });

        var data = [];
        for (var i = 0; i < pimcore.settings.websiteLanguages.length; i++) {
            var language = pimcore.settings.websiteLanguages[i];
            data.push([language, t(pimcore.available_languages[language])]);
        }

        var store = new Ext.data.ArrayStore({
                fields: ["key", "value"],
                data: data
            }
        );

        var options = {
            fieldLabel: t('locale'),
            triggerAction: "all",
            editable: true,
            selectOnFocus: true,
            queryMode: 'local',
            typeAhead: true,
            forceSelection: true,
            store: store,
            componentCls: "object_field",
            mode: "local",
            width: 200,
            padding: 10,
            displayField: "value",
            valueField: "key",
            value: this.node.data.configAttributes.locale
        };

        this.localeField = new Ext.form.ComboBox(options);

        this.configPanel = new Ext.Panel({
            layout: "form",
            bodyStyle: "padding: 10px;",
            items: [this.textField, this.localeField],
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
            height: 200,
            modal: true,
            title: this.getDefaultText(),
            layout: "fit",
            items: [this.configPanel]
        });

        this.window.show();

        return this.window;
    },

    commitData: function ($super, params) {
        this.node.data.configAttributes.locale = this.localeField.getValue();

        $super(params);
    },

    getNodeLabel: function(configAttributes) {
        var nodeLabel = configAttributes.label;

        if (configAttributes.locale) {
            nodeLabel += '<span class="pimcore_gridnode_hint"> (' + configAttributes.locale + ')</span>';
        }

        return nodeLabel;
    }
});
