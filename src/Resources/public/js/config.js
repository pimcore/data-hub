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

pimcore.registerNS("pimcore.plugin.datahub.config");
pimcore.plugin.datahub.config = Class.create({

    importRoute: "/admin/pimcoredatahub/config/import",
    exportRoute: "/admin/pimcoredatahub/config/export",

    initialize: function () {
        this.getTabPanel();
    },

    activate: function () {
        let tabPanel = Ext.getCmp("pimcore_panel_tabs");
        tabPanel.setActiveItem(this.getTabPanel());
    },

    getTabPanel: function () {
        if (!this.panel) {
            this.panel = new Ext.Panel({
                id: "pimcore_plugin_datahub_config_tab",
                title: t("plugin_pimcore_datahub_toolbar"),
                iconCls: "plugin_pimcore_datahub_icon",
                border: false,
                layout: "border",
                closable: true,
                items: [this.getTree(), this.getEditPanel()]
            });

            var tabPanel = Ext.getCmp("pimcore_panel_tabs");
            tabPanel.add(this.panel);
            tabPanel.setActiveItem("pimcore_plugin_datahub_config_tab");

            this.panel.on("destroy", function () {
                pimcore.globalmanager.remove("plugin_pimcore_datahub_config");
            }.bind(this));

            pimcore.layout.refresh();
        }

        return this.panel;
    },

    userIsAllowedToCreate: function(adapter) {
        let user = pimcore.globalmanager.get("user");

        //everything is allowed for admins
        if (user.admin || user.isAllowed('plugin_datahub_admin')) {
            return true;
        }

        return user.isAllowed("plugin_datahub_adapter_" + adapter);
    },

    getTree: function () {
        if (!this.tree) {

            var store = Ext.create('Ext.data.TreeStore', {
                autoLoad: false,
                autoSync: true,
                proxy: {
                    type: 'ajax',
                    url: '/admin/pimcoredatahub/config/list',
                    reader: {
                        type: 'json'
                    }
                }
            });

            let menuItems = [];

            let firstHandler;

            for (let key in pimcore.plugin.datahub.adapter) {
                if( pimcore.plugin.datahub.adapter.hasOwnProperty( key ) && this.userIsAllowedToCreate(key)) {
                    let adapter = new pimcore.plugin.datahub.adapter[key](this);

                    if (!firstHandler) {
                        firstHandler = adapter.addConfiguration.bind(adapter, key);
                    }
                    menuItems.push(
                    {
                        text: t('plugin_pimcore_datahub_type_' + key),
                        iconCls: "plugin_pimcore_datahub_icon_" + key,
                        handler: adapter.addConfiguration.bind(adapter, key)
                    });
                }
            }

            var addConfigButton = new Ext.SplitButton({
                text: t("plugin_pimcore_datahub_configpanel_add"),
                iconCls: "pimcore_icon_add",
                handler: firstHandler,
                disabled:  !pimcore.settings['data-hub-writeable'] || !firstHandler,
                menu: menuItems,
            });

            const importButton = new Ext.Button({
                tooltip: t('plugin_pimcore_datahub_import'),
                iconCls: 'pimcore_icon_upload',
                handler: function () {
                    pimcore.helpers.uploadDialog(
                        this.importRoute,
                        "Filedata",
                        function (response) {
                            response = response.response;
                            const data = Ext.decode(response.responseText);

                            if(data){
                                const editPanel = new pimcore.plugin.datahub.adapter[data.type](this);
                                editPanel.openConfiguration(data.name)
                            }
                            this.refreshTree();

                        }.bind(this),
                        function () {
                            Ext.MessageBox.alert(t("error"), t("error"));
                        }
                    );
                }.bind(this)
            });

            this.tree = new Ext.tree.TreePanel({
                store: store,
                region: "west",
                autoScroll: true,
                animate: true,
                containerScroll: true,
                border: true,
                width: 230,
                split: true,
                root: {
                    id: '0',
                    expanded: true,
                    iconCls: "pimcore_icon_thumbnails"
                },
                rootVisible: false,
                tbar: {
                    items: [
                        addConfigButton,
                        importButton
                    ]
                },
                listeners: {
                    itemclick: this.onTreeNodeClick.bind(this),
                    itemcontextmenu: this.onTreeNodeContextmenu.bind(this),
                    render: function () {
                        this.getRootNode().expand()
                    }
                }
            });
        }

        return this.tree;
    },

    getEditPanel: function () {
        if (!this.editPanel) {
            this.editPanel = new Ext.TabPanel({
                region: "center"
            });
        }

        return this.editPanel;
    },


    onTreeNodeClick: function (tree, record, item, index, e, eOpts) {
        if (!record.isLeaf()) {
            return;
        }

        let adapterType = record.data.adapter;
        let adapterImpl = new pimcore.plugin.datahub.adapter[adapterType](this);
        adapterImpl.openConfiguration(record.id);
    },


    onTreeNodeContextmenu: function (tree, record, item, index, e, eOpts) {
        if (!record.isLeaf()) {
            return;
        }

        e.stopEvent();

        tree.select();

        var menu = new Ext.menu.Menu();
        menu.add(new Ext.menu.Item({
            text: t('delete'),
            iconCls: "pimcore_icon_delete",
            disabled: !record.data['writeable'] || (!record.data.permissions.delete),
            handler: this.deleteConfiguration.bind(this, tree, record)
        }));

        menu.add(new Ext.menu.Item({
            text: t('clone'),
            iconCls: "pimcore_icon_clone",
            disabled: !record.data['writeable'] || !this.userIsAllowedToCreate(record.data.adapter),
            handler: this.cloneConfiguration.bind(this, tree, record)
        }));

        menu.add(new Ext.menu.Item({
            text: t('plugin_pimcore_datahub_export'),
            iconCls: 'pimcore_icon_download',
            handler: function () {
                const recordName = record.data.id;
                pimcore.helpers.download(this.exportRoute + '?name=' + recordName);
            }.bind(this, tree, record)
        }));

        menu.showAt(e.pageX, e.pageY);
    },

    cloneConfiguration: function (tree, record) {
        let adapterType = record.data.adapter;
        let adapterImpl = new pimcore.plugin.datahub.adapter[adapterType](this);
        adapterImpl.cloneConfiguration(tree, record);
    },

    deleteConfiguration: function (tree, record) {
        let adapterType = record.data.adapter;
        let adapterImpl = new pimcore.plugin.datahub.adapter[adapterType](this);
        adapterImpl.deleteConfiguration(tree, record);
    },

    refreshTree: function() {
        this.tree.getStore().load({
            node: this.tree.getRootNode()
        });
    }

});