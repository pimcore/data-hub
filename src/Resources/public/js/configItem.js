/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

pimcore.registerNS("pimcore.plugin.datahub.configItem");
pimcore.plugin.datahub.configItem = Class.create(pimcore.element.abstract, {
    initialize: function (data, parent) {
        this.parent = parent;
        this.data = data.configuration;

        this.tab = new Ext.TabPanel({
            activeTab: 0,
            title: this.data.general.name,
            closable: true,
            deferredRender: false,
            forceLayout: true,
            iconCls: "plugin_pimcore_datahub_icon_" + this.data.general.type,
            id: "plugin_pimcore_datahub_configpanel_panel_" + data.name,
            buttons: {
                componentCls: 'plugin_pimcore_datahub_statusbar',
                itemId: 'footer'
            },
            items: [this.getGeneral(), this.getSchema(), this.getSecurity()]
        });

        this.tab.on("activate", this.tabactivated.bind(this));
        this.tab.on("destroy", this.tabdestroy.bind(this));

        this.parent.editPanel.add(this.tab);
        this.parent.editPanel.setActiveTab(this.tab);
        this.parent.editPanel.updateLayout();

        this.showInfo();
    },

    openExplorer: function (callbackFn) {
        Ext.Ajax.request({
            url: '/admin/pimcoredatahub/config/get-explorer-url?' + this.data.general.name,

            success: function (callbackFn, response, opts) {

                var data = Ext.decode(response.responseText);
                var securityValues = this.securityForm.getForm().getFieldValues();
                var explorerUrl = window.location.origin + data.explorerUrl + this.data.general.name;
                if (securityValues && securityValues["method"] == "datahub_apikey") {
                    explorerUrl = explorerUrl + "?apikey=" + securityValues["apikey"];
                }
                callbackFn(explorerUrl);
            }.bind(this, callbackFn)
        });

    },

    showInfo: function () {

        var footer = this.tab.getDockedComponent('footer');

        footer.removeAll();

        footer.add({
            xtype: 'button',
            text: t('plugin_pimcore_datahub_graphql_open_explorer_in_iframe'),
            iconCls: 'pimcore_icon_iframe',
            handler: function () {
                this.openExplorer(function (explorerUrl) {
                    pimcore.helpers.openGenericIframeWindow("plugin_datahub_iframe_" + this.data.general.name, explorerUrl, "plugin_pimcore_datahub_icon_graphql",
                        t("plugin_pimcore_datahub_graphql_iexplorer") + " - " + this.data.general.name
                    );
                }.bind(this));
            }.bind(this)
        });

        footer.add({
            xtype: 'button',
            text: t('plugin_pimcore_datahub_graphql_open_explorer_in_tab'),
            iconCls: 'pimcore_icon_open_window',
            handler: function () {
                this.openExplorer(function (explorerUrl) {
                    window.open(explorerUrl, '_blank');
                }.bind(this));
            }.bind(this)
        });

        footer.add({
            text: t("save"),
            iconCls: "pimcore_icon_apply",
            handler: this.save.bind(this)
        });


    },

    tabactivated: function () {
        this.setupChangeDetector();
        this.tabdestroyed = false;
    },

    tabdestroy: function () {
        this.tabdestroyed = true;
    },


    getGeneral: function () {


        this.generalForm = new Ext.form.FormPanel({
            bodyStyle: "padding:10px;",
            autoScroll: true,
            defaults: {
                labelWidth: 200,
                width: 600
            },
            border: false,
            title: t("plugin_pimcore_datahub_configpanel_item_general"),
            items: [
                {
                    xtype: "checkbox",
                    fieldLabel: t("active"),
                    name: "active",
                    value: this.data.general && this.data.general.hasOwnProperty("active") ? this.data.general.active : true
                },
                {
                    xtype: "textfield",
                    fieldLabel: t("type"),
                    name: "type",
                    value: t("plugin_pimcore_datahub_type_" + this.data.general.type),
                    readOnly: true
                },
                {
                    xtype: "textfield",
                    fieldLabel: t("name"),
                    name: "name",
                    value: this.data.general.name,
                    readOnly: true
                }, {
                    name: "description",
                    fieldLabel: t("description"),
                    xtype: "textarea",
                    height: 100,
                    value: this.data.general.description
                },
                {
                    xtype: "displayfield",
                    value: t("plugin_pimcore_datahub_configpanel_condition_hint"),
                    name: "sql_list_hint",
                    readOnly: true
                },
                {
                    name: "sqlObjectCondition",
                    fieldLabel: t("plugin_pimcore_datahub_configpanel_sqlObjectCondition"),
                    xtype: "textarea",
                    height: 100,
                    value: this.data.general.sqlObjectCondition
                }
            ]
        });

        return this.generalForm;
    },

    getSecurity: function () {

        var methodsStore = Ext.create('Ext.data.Store', {
            fields: ['method', 'translatedMethod'],
            data: [{
                'method': 'datahub_apikey',
                "translatedMethod": t("plugin_pimcore_datahub_configpanel_security_method_apikey"),
                'allowBlank': false
            }]
        });


        this.assetWorkspace = new pimcore.plugin.datahub.workspace.asset(this);
        this.objectWorkspace = new pimcore.plugin.datahub.workspace.object(this);

        var apikeyField = new Ext.form.field.Text({
            xtype: "textfield",
            labelWidth: 200,
            width: 600,
            fieldLabel: t("plugin_pimcore_datahub_security_datahub_apikey"),
            name: "apikey",
            value: this.data.security ? this.data.security.apikey : "",
            minLength: 16
        });

        this.securityForm = new Ext.form.FormPanel({
            bodyStyle: "padding:10px;",
            autoScroll: true,
            defaults: {
                labelWidth: 200
            },
            border: false,
            title: t("plugin_pimcore_datahub_configpanel_security"),
            items: [
                {
                    xtype: "combobox",
                    fieldLabel: t("plugin_pimcore_datahub_configpanel_security_method"),
                    name: "method",
                    store: methodsStore,
                    value: this.data.security && this.data.security.method ? this.data.security.method : "datahub_apikey",
                    valueField: 'method',
                    displayField: 'translatedMethod',
                    width: 600
                },
                {
                    xtype: "fieldcontainer",
                    layout: 'hbox',

                    items: [apikeyField,
                        {
                            xtype: "button",
                            width: 32,
                            style: "margin-left: 8px",
                            iconCls: "pimcore_icon_clear_cache",
                            handler: function () {
                                apikeyField.setValue(md5(uniqid()));
                            }.bind(this)
                        }
                    ]
                },
                {
                    xtype: 'displayfield',
                    hideLabel: true,
                    value: t("plugin_pimcore_datahub_security_apikey_description"),
                    cls: "pimcore_extra_label_bottom"
                },
                {
                    xtype: 'fieldset',
                    width: 800,
                    title: t("workspaces"),
                    items: [
                        this.assetWorkspace.getPanel(),
                        this.objectWorkspace.getPanel()
                    ]
                }
            ]
        });

        return this.securityForm;
    },

    getSchema: function () {

        this.createSchemaStoreAndGrid("query");
        this.createSchemaStoreAndGrid("mutation");
        this.createSpecialSettingsGrid();

        this.schemaForm = new Ext.form.FormPanel({
            bodyStyle: "padding:10px;",
            autoScroll: true,
            defaults: {
                labelWidth: 200,
                width: 800
            },
            border: false,
            title: t("plugin_pimcore_datahub_configpanel_schema"),
            items: [
                {
                    xtype: 'fieldset',
                    title: t('plugin_pimcore_datahub_graphql_query_schema'),
                    items: [
                        this.querySchemaGrid
                    ]
                }, {
                    xtype: 'fieldset',
                    title: t('plugin_pimcore_datahub_graphql_mutation_schema'),
                    items: [
                        this.mutationSchemaGrid
                    ]
                },
                {
                    xtype: 'fieldset',
                    title: t('plugin_pimcore_datahub_graphql_special_schema'),
                    items: [
                        this.specialSchemaGrid
                    ]
                }
            ]
        });

        return this.schemaForm;
    },

    onAdd: function (type) {
        this.showEntitySelectionDialog(type);
    },


    updateData: function (data, grid) {
    },

    createSchemaStoreAndGrid: function (type) {
        var schemaToolbar = Ext.create('Ext.Toolbar', {
            cls: 'main-toolbar',
            items: [
                {
                    text: t('add'),
                    handler: this.onAdd.bind(this, type),
                    iconCls: "pimcore_icon_add"
                }
            ]
        });

        var fields = ['id', 'columnConfig'];
        if (type == "mutation") {
            fields.push("create");
            fields.push("update");
            fields.push("delete");
        }
        this[type + "SchemaStore"] = Ext.create('Ext.data.Store', {
            reader: {
                type: 'memory'
            },
            fields: fields,
            data: this.data.schema[type + "Entities"]
        });

        var columns = [
            {
                text: t("plugin_pimcore_datahub_configpanel_entity"),
                sortable: true,
                dataIndex: 'id',
                editable: false,
                filter: 'string',
                flex: 1
            }
        ];

        var additionalColumns = ["create", "update", "delete"];
        if (type == "mutation") {
            for (var i = 0; i < additionalColumns.length; i++) {
                var checkColumn = Ext.create('Ext.grid.column.Check', {
                    text: t(additionalColumns[i]),
                    dataIndex: additionalColumns[i]
                });
                columns.push(checkColumn);
            }
        }

        columns.push({
            xtype: 'actioncolumn',
            text: t('settings'),
            menuText: t('settings'),
            width: 60,
            items: [
                {
                    tooltip: t('settings'),
                    icon: "/bundles/pimcoreadmin/img/flat-color-icons/settings.svg",
                    handler: function (grid, rowIndex) {
                        var record = grid.getStore().getAt(rowIndex);

                        var classStore = pimcore.globalmanager.get("object_types_store");
                        var classIdx = classStore.findExact("text", record.data.id);
                        if (classIdx >= 0) {
                            var classRecord = classStore.getAt(classIdx);
                            classId = classRecord.data.id;
                            var columnConfig = record.get("columnConfig");

                            var dialog = new pimcore.plugin.datahub.fieldConfigDialog(type, {
                                    className: classRecord.data.text,
                                    classId: classId
                                },
                                columnConfig,
                                function (data, settings) {
                                    record.set('columnConfig', data);
                                }, null);
                        }
                    }.bind(this)
                }]
        });

        columns.push({
            xtype: 'actioncolumn',
            text: t('delete'),
            menuText: t('delete'),
            width: 60,
            items: [{
                tooltip: t('delete'),
                icon: "/bundles/pimcoreadmin/img/flat-color-icons/delete.svg",
                handler: function (grid, rowIndex) {
                    grid.getStore().removeAt(rowIndex);
                }.bind(this)
            }
            ]
        });

        var prop = type + "SchemaGrid";
        this[prop] = Ext.create('Ext.grid.Panel', {
            frame: false,
            bodyCls: "pimcore_editable_grid",
            autoScroll: true,
            store: this[type + "SchemaStore"],
            columnLines: true,
            stripeRows: true,
            columns: {
                items: columns
            },
            trackMouseOver: true,
            selModel: Ext.create('Ext.selection.RowModel', {}),
            tbar: schemaToolbar,
            viewConfig: {
                forceFit: true,
                enableTextSelection: true
            }
        });

    },

    createSpecialSettingsGrid: function () {
        var schemaToolbar = Ext.create('Ext.Toolbar', {
            cls: 'main-toolbar'
        });

        var fields = ['id', 'create', 'read', 'update', 'delete'];

        this.specialSchemaStore = Ext.create('Ext.data.Store', {
            reader: {
                type: 'memory'
            },
            fields: fields,
            data: this.data.schema.specialEntities
        });

        var columns = [
            {
                sortable: true,
                dataIndex: 'id',
                editable: false,
                filter: 'string',
                renderer: function (v) {
                    return t("plugin_pimcore_datahub_graphql_special_" + v);
                },
                flex: 1
            }
        ];

        var additionalColumns = ["create", "read", "update", "delete"];

        for (var i = 0; i < additionalColumns.length; i++) {
            var checkColumn = Ext.create('Ext.grid.column.Check', {
                text: t(additionalColumns[i]),
                dataIndex: additionalColumns[i]
            });
            columns.push(checkColumn);
        }


        this.specialSchemaGrid = Ext.create('Ext.grid.Panel', {
            frame: false,
            bodyCls: "pimcore_editable_grid",
            autoScroll: true,
            store: this.specialSchemaStore,
            columnLines: true,
            stripeRows: true,
            columns: {
                items: columns
            },
            trackMouseOver: true,
            selModel: Ext.create('Ext.selection.RowModel', {}),
            tbar: schemaToolbar,
            viewConfig: {
                forceFit: true,
                enableTextSelection: true
            }
        });
    },


    getSaveData: function () {

        var saveData = {};
        saveData["general"] = this.generalForm.getForm().getFieldValues();
        saveData["schema"] = this.schemaForm.getForm().getFieldValues();
        saveData["security"] = this.securityForm.getForm().getFieldValues();
        saveData["schema"]["queryEntities"] = this.getSchemaData("query");
        saveData["schema"]["mutationEntities"] = this.getSchemaData("mutation");
        saveData["schema"]["specialEntities"] = this.getSchemaData("special");
        saveData["workspaces"] = {};
        saveData["workspaces"]["asset"] = this.assetWorkspace.getValues();
        saveData["workspaces"]["object"] = this.objectWorkspace.getValues();
        return Ext.encode(saveData);
    },


    getSchemaData: function (type) {
        var tmData = [];

        var store = this[type + "SchemaStore"];
        var data = store.queryBy(function (record, id) {
            return true;
        });

        for (var i = 0; i < data.items.length; i++) {
            tmData.push(data.items[i].data);
        }

        return tmData;
    },

    save: function () {
        var saveData = this.getSaveData();

        Ext.Ajax.request({
            url: "/admin/pimcoredatahub/config/save",
            params: {
                data: saveData
            },
            method: "post",
            success: function (response) {
                var rdata = Ext.decode(response.responseText);
                if (rdata && rdata.success) {
                    pimcore.helpers.showNotification(t("success"), t("plugin_pimcore_datahub_configpanel_item_save_success"), "success");
                    this.resetChanges();
                } else {
                    pimcore.helpers.showNotification(t("error"), t("plugin_pimcore_datahub_configpanel_item_saveerror"), "error", t(rdata.message));
                }
            }.bind(this)
        });
    },

    showEntitySelectionDialog: function (type) {

        var store = this[type + "SchemaStore"];
        this.entitySelectionDialog = new Ext.Window({
            autoHeight: true,
            title: t('plugin_pimcore_datahub_operator_select_entity'),
            closeAction: 'close',
            width: 500,
            modal: true
        });

        var entityStore = new Ext.data.JsonStore({
            proxy: {
                url: '/admin/class/get-tree',
                type: 'ajax',
                reader: {
                    type: 'json',
                    idProperty: 'text'
                }
            },
            fields: ['id'],
            autoDestroy: true,
            autoLoad: true,
            sortInfo: {field: 'id', direction: "ASC"}
        });

        var entityCombo = new Ext.form.ComboBox(
            {
                xtype: "combo",
                fieldLabel: t("plugin_pimcore_datahub_configpanel_entity"),
                store: entityStore,
                triggerAction: 'all',
                editable: false,
                width: 450
            }
        );

        var form = new Ext.form.FormPanel({
            bodyStyle: 'padding: 10px;',
            items: [entityCombo],
            bbar: [
                "->",
                {
                    xtype: "button",
                    text: t("OK"),
                    iconCls: "pimcore_icon_bool",
                    handler: function () {
                        var entity = entityCombo.getValue();
                        if (entity) {
                            var record = store.getById(entity);
                            if (!record) {
                                var newData = {
                                    id: entity,
                                    name: entity
                                };
                                if (type == "mutation") {
                                    newData["update"] = true;
                                }
                                var addedRecord = store.addSorted(newData);
                                addedRecord = addedRecord[0];
                                this[type + "SchemaGrid"].getSelectionModel().select([addedRecord]);
                            }
                        }

                        this.entitySelectionDialog.close();

                    }.bind(this)
                },
                {
                    xtype: "button",
                    text: t("cancel"),
                    iconCls: "pimcore_icon_cancel",
                    handler: function () {
                        this.entitySelectionDialog.close();
                    }.bind(this)
                }]
        });

        this.entitySelectionDialog.add(form);
        this.entitySelectionDialog.show();
    },


});
