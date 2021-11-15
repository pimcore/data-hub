/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

pimcore.registerNS("pimcore.plugin.datahub.adapter.graphql");
pimcore.plugin.datahub.adapter.graphql = Class.create({

    initialize: function (configPanel) {
        this.configPanel = configPanel;
    },

    addConfiguration: function (type) {
        Ext.MessageBox.prompt(t('plugin_pimcore_datahub_configpanel_enterkey_title'), t('plugin_pimcore_datahub_configpanel_enterkey_prompt'), this.addConfigurationComplete.bind(this, type), null, null, "");
    },

    addConfigurationComplete: function (type, button, value, object) {
        var regresult = value.match(/[a-zA-Z0-9_\-]+/);
        if (button == "ok" && value.length > 2 && value.length <= 80 && regresult == value) {
            Ext.Ajax.request({
                url: "/admin/pimcoredatahub/config/add",
                params: {
                    name: value,
                    type: type
                },
                success: function (response) {
                    var data = Ext.decode(response.responseText);
                    this.configPanel.refreshTree();

                    if (!data || !data.success) {
                        pimcore.helpers.showNotification(t("error"), t("plugin_pimcore_datahub_configpanel_error_adding_config") + ': <br/>' + data.message, "error");
                    } else {
                        this.openConfiguration(data.name);
                    }

                }.bind(this)
            });
        }
        else if (button == "cancel") {
            return;
        }
        else {
            Ext.Msg.alert(t("plugin_pimcore_datahub_configpanel"), value.length <= 80 ? t("plugin_pimcore_datahub_configpanel_invalid_name") : t("plugin_pimcore_datahub_configpanel_invalid_length"));
        }
    },

    openConfiguration: function (id) {
        var existingPanel = Ext.getCmp("plugin_pimcore_datahub_configpanel_panel_" + id);
        if(existingPanel) {
            this.configPanel.editPanel.setActiveTab(existingPanel);
            return;
        }

        Ext.Ajax.request({
            url: "/admin/pimcoredatahub/config/get",
            params: {
                name: id
            },
            success: function (response) {
                var data = Ext.decode(response.responseText);

                pimcore.plugin.datahub.graphql = pimcore.plugin.datahub.graphql || {};
                pimcore.plugin.datahub.graphql.supportedQueryDataTypes = data.supportedGraphQLQueryDataTypes;
                pimcore.plugin.datahub.graphql.supportedMutationDataTypes = data.supportedGraphQLMutationDataTypes;

                let fieldPanel = new pimcore.plugin.datahub.configuration.graphql.configItem(data, this);
                pimcore.layout.refresh();
            }.bind(this)
        });
    },

    cloneConfiguration: function (tree, record) {
        Ext.MessageBox.prompt(t('plugin_pimcore_datahub_configpanel_enterclonekey_title'), t('plugin_pimcore_datahub_configpanel_enterclonekey_enterclonekey_prompt'),
            this.cloneConfigurationComplete.bind(this, tree, record), null, null, "");
    },

    cloneConfigurationComplete: function (tree, record, button, value, object) {

        var regresult = value.match(/[a-zA-Z0-9_\-]+/);
        if (button == "ok" && value.length > 2 && value.length <= 80 && regresult == value) {
            Ext.Ajax.request({
                url: "/admin/pimcoredatahub/config/clone",
                params: {
                    name: value,
                    originalName: record.data.id
                },
                success: function (response) {
                    var data = Ext.decode(response.responseText);

                    this.configPanel.refreshTree();

                    if (!data || !data.success) {
                        pimcore.helpers.showNotification(t("error"), t("plugin_pimcore_datahub_configpanel_error_cloning_config") + ': <br/>' + data.message, "error");
                    } else {
                        this.openConfiguration(data.name, tree, record);
                    }

                }.bind(this)
            });
        }
        else if (button == "cancel") {
            return;
        }
        else {
            Ext.Msg.alert(t("plugin_pimcore_datahub_configpanel"), value.length <= 80 ? t("plugin_pimcore_datahub_configpanel_invalid_name") : t("plugin_pimcore_datahub_configpanel_invalid_length"));
        }
    },

    deleteConfiguration: function (tree, record) {
        Ext.Msg.confirm(t('delete'), t('delete_message'), function (btn) {
            if (btn == 'yes') {
                Ext.Ajax.request({
                    url: "/admin/pimcoredatahub/config/delete",
                    params: {
                        name: record.data.id
                    }
                });

                this.configPanel.getEditPanel().removeAll();
                record.remove();
            }
        }.bind(this));
    },

});
