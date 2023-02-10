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

pimcore.registerNS("pimcore.plugin.datahub.fieldConfigDialog");
pimcore.plugin.datahub.fieldConfigDialog = Class.create({

    showFieldname: true,
    data: {},
    brickKeys: [],

    initialize: function (type, generalConfig, columnConfig, callback, settings) {

        this.type = type;
        this.generalConfig = generalConfig || {};
        this.columnConfig = columnConfig || {};
        this.columnConfig.columns = this.columnConfig.columns || [];
        this.callback = callback;

        this.settings = settings || {};

        if (!this.callback) {
            this.callback = function () {
            };
        }

        this.configPanel = new Ext.Panel({
            layout: "border",
            iconCls: "pimcore_icon_table",
            title: t("plugin_pimcore_datahub_configpanel_fields"),
            items: [
                this.getSelectionPanel(), this.getLeftPanel()]

        });


        var tabs = [this.configPanel];

        this.tabPanel = new Ext.TabPanel({
            activeTab: 0,
            forceLayout: true,
            items: tabs
        });

        buttons = [];

        buttons.push({
                text: t("apply"),
                iconCls: "pimcore_icon_apply",
                handler: function () {
                    this.commitData();
                }.bind(this)
            }
        );

        this.window = new Ext.Window({
            width: 950,
            height: '95%',
            modal: true,
            title: t("plugin_pimcore_datahub_" + this.type) + " " + t('plugin_pimcore_datahub_configpanel_schema_fields') + ' - ' + this.generalConfig.className,
            layout: "fit",
            items: [this.tabPanel],
            buttons: buttons
        });

        this.window.show();
    },

    doBuildChannelConfigTree: function (configuration) {

        var elements = [];
        if (configuration) {
            for (var i = 0; i < configuration.length; i++) {
                var configElement = this.getConfigElement(configuration[i]);
                if (configElement) {
                    var treenode = configElement.getConfigTreeNode(configuration[i].attributes);

                    if (configuration[i].attributes && configuration[i].attributes.children) {
                        var children = this.doBuildChannelConfigTree(configuration[i].attributes.children);
                        treenode.children = children;
                        if (children.length > 0) {
                            treenode.expandable = true;
                        }
                    }
                    elements.push(treenode);
                } else {
                    console.log("config element not found");
                }
            }
        }
        return elements;
    },

    getLeftPanel: function () {
        if (!this.leftPanel) {

            var items = this.getOperatorTrees();
            items.unshift(this.getClassDefinitionTreePanel());

            this.brickKeys = [];
            this.leftPanel = new Ext.Panel({
                cls: "pimcore_panel_tree pimcore_gridconfig_leftpanel",
                region: "center",
                split: true,
                width: 300,
                minSize: 175,
                collapsible: true,
                collapseMode: 'header',
                collapsed: false,
                animCollapse: false,
                layout: 'accordion',
                hideCollapseTool: true,
                header: false,
                layoutConfig: {
                    animate: false
                },
                hideMode: "offsets",
                items: items
            });
        }

        return this.leftPanel;
    },


    doGetRecursiveData: function (node) {
        var children = [];
        node.eachChild(function (child) {
            var attributes = child.data.configAttributes;
            attributes.children = this.doGetRecursiveData(child);
            var childConfig = {
                "isOperator": child.data.isOperator ? true : false,
                "attributes": attributes
            };

            children.push(childConfig);
        }.bind(this));

        return children;
    },


    commitData: function () {

        this.data = {};


        var operatorFound = false;

        if (this.selectionPanel) {
            this.data.columns = [];
            this.selectionPanel.getRootNode().eachChild(function (child) {
                var obj = {};

                if (child.data.isOperator) {
                    var attributes = child.data.configAttributes;
                    var operatorChildren = this.doGetRecursiveData(child);
                    attributes.children = operatorChildren;
                    operatorFound = true;

                    obj.isOperator = true;
                    obj.attributes = attributes;

                } else {
                    var attributes = {};
                    attributes.attribute = child.data.key;
                    attributes.label = child.data.layout ? child.data.layout.title : child.data.text;
                    attributes.dataType = child.data.dataType;
                    if (child.data.width) {
                        attributes.width = child.data.width;
                    }
                    obj.attributes = attributes;
                    obj.isOperator = false;
                }

                this.data.columns.push(obj);
            }.bind(this));
        }

        var user = pimcore.globalmanager.get("user");


        if (!operatorFound) {
            this.callback(this.data, this.settings);
            this.window.close();
        } else {
            var columnsPostData = Ext.encode(this.data.columns);
            Ext.Ajax.request({
                //TODO what to do with this stuff ?

                url: "/admin/object-helper/prepare-helper-column-configs",
                method: 'POST',
                params: {
                    columns: columnsPostData
                },
                success: function (response) {
                    var responseData = Ext.decode(response.responseText);
                    this.data.columns = responseData.columns;

                    this.callback(this.data, this.settings);
                    this.window.close();


                }.bind(this)
            });
        }
    },

    openConfigDialog: function (element, copy) {
        var window = element.getConfigDialog(copy, null);

        if (window) {
            //this is needed because of new focus management of extjs6
            setTimeout(function () {
                window.focus();
            }, 250);
        }
    },


    getSelectionPanel: function () {
        if (!this.selectionPanel) {

            var children = [];
            for (var i = 0; i < this.columnConfig.columns.length; i++) {
                var nodeConf = this.columnConfig.columns[i];

                if (nodeConf.isOperator) {
                    var child = this.doBuildChannelConfigTree([nodeConf]);
                    if (!child || !child[0]) {
                        continue;
                    }
                    child = child[0];
                } else {
                    var attributes = nodeConf.attributes;
                    let text = attributes.label ? t(attributes.label) : `(${attributes.attribute})`;

                    if (attributes.dataType !== "system" && this.showFieldname && attributes.key) {
                        text = text + " (" + attributes.key.replace("~", ".") + ")";
                    }

                    var child = {
                        text: text,
                        key: attributes.attribute,
                        type: "data",
                        dataType: attributes.dataType,
                        leaf: true,
                        layout: attributes.layout,
                        iconCls: "pimcore_icon_" + attributes.dataType
                    };
                    if (attributes.width) {
                        child.width = attributes.width;
                    }
                }
                children.push(child);
            }

            this.cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
                clicksToEdit: 1
            });

            var store = new Ext.data.TreeStore({
                fields: [{
                    name: "text"
                }
                ],
                root: {
                    id: "0",
                    root: true,
                    text: t("plugin_pimcore_datahub_configpanel_root"),
                    leaf: false,
                    isTarget: true,
                    expanded: true,
                    children: children
                }
            });

            this.selectionPanel = new Ext.tree.TreePanel({
                store: store,
                plugins: [this.cellEditing],
                rootVisible: true,
                viewConfig: {
                    plugins: {
                        ptype: 'treeviewdragdrop',
                        ddGroup: "columnconfigelement"
                    },
                    listeners: {
                        beforedrop: function (node, data, overModel, dropPosition, dropHandlers, eOpts) {
                            var target = overModel.getOwnerTree().getView();
                            var source = data.view;

                            if (target != source) {
                                var record = data.records[0];
                                var isOperator = record.data.isOperator;
                                var realOverModel = overModel;
                                if (dropPosition == "before" || dropPosition == "after") {
                                    realOverModel = overModel.parentNode;
                                }

                                if (isOperator || this.parentIsOperator(realOverModel)) {
                                    var attr = record.data;
                                    if (record.data.configAttributes) {
                                        attr = record.data.configAttributes;
                                    }
                                    var elementConfig = {
                                        "isOperator": true,
                                        "attributes": attr
                                    }

                                    var element = this.getConfigElement(elementConfig);
                                    var copy = element.getCopyNode(record);
                                    data.records = [copy]; // assign the copy as the new dropNode
                                    this.openConfigDialog(element, copy);
                                } else {

                                    if (!this.checkSupported(record)) {
                                        dropHandlers.cancelDrop();
                                        return false;
                                    }


                                    if (this.selectionPanel.getRootNode().findChild("key", record.data.key)) {
                                        dropHandlers.cancelDrop();
                                    } else {
                                        var copy = Ext.apply({}, record.data);
                                        delete copy.id;
                                        copy = record.createNode(copy);

                                        var ownerTree = this.selectionPanel;


                                        //TODO in case this ever get's reintegrated in to the core,
                                        // we don't support this on key level !
                                        // if (record.data.dataType == "classificationstore") {
                                        //     setTimeout(function () {
                                        //         var ccd = new pimcore.object.classificationstore.columnConfigDialog();
                                        //         ccd.getConfigDialog(ownerTree, copy, this.selectionPanel);
                                        //     }.bind(this), 100);
                                        // }
                                        data.records = [copy]; // assign the copy as the new dropNode
                                    }
                                }
                            } else {
                                // node has been moved inside right selection panel
                                var record = data.records[0];
                                var isOperator = record.data.isOperator;
                                var realOverModel = overModel;
                                if (dropPosition == "before" || dropPosition == "after") {
                                    realOverModel = overModel.parentNode;
                                }

                                if (isOperator || this.parentIsOperator(realOverModel)) {
                                    var attr = record.data;
                                    if (record.data.configAttributes) {
                                        // there is nothing to do, this guy has been configured already
                                        return;
                                        // attr = record.data.configAttributes;
                                    }
                                    var element = this.getConfigElement(attr);

                                    var copy = element.getCopyNode(record);
                                    data.records = [copy]; // assign the copy as the new dropNode

                                    this.openConfigDialog(element, copy);

                                    record.parentNode.removeChild(record);
                                }
                            }
                        }.bind(this),
                        drop: function (node, data, overModel) {
                            overModel.set('expandable', true);

                        }.bind(this),
                        nodedragover: function (targetNode, dropPosition, dragData, e, eOpts) {
                            var sourceNode = dragData.records[0];

                            if (sourceNode.data.isOperator) {
                                var realOverModel = targetNode;
                                if (dropPosition == "before" || dropPosition == "after") {
                                    realOverModel = realOverModel.parentNode;
                                }

                                var sourceType = this.getNodeTypeAndClass(sourceNode);
                                var targetType = this.getNodeTypeAndClass(realOverModel);
                                var allowed = true;


                                if (typeof realOverModel.data.isChildAllowed == "function") {
                                    allowed = allowed && realOverModel.data.isChildAllowed(realOverModel, sourceNode);
                                }

                                if (typeof sourceNode.data.isParentAllowed == "function") {
                                    allowed = allowed && sourceNode.data.isParentAllowed(realOverModel, sourceNode);
                                }


                                return allowed;
                            } else {
                                var targetNode = targetNode;

                                var allowed = true;
                                if (this.parentIsOperator(targetNode)) {
                                    if (dropPosition == "before" || dropPosition == "after") {
                                        targetNode = targetNode.parentNode;
                                    }

                                    if (typeof targetNode.data.isChildAllowed == "function") {
                                        allowed = allowed && targetNode.data.isChildAllowed(targetNode, sourceNode);
                                    }

                                    if (typeof sourceNode.data.isParentAllowed == "function") {
                                        allowed = allowed && sourceNode.data.isParentAllowed(targetNode, sourceNode);
                                    }

                                }

                                return allowed;
                            }
                        }.bind(this),
                        options: {
                            target: this.selectionPanel
                        }
                    }
                },
                id: 'tree',
                region: 'east',
                title: t('plugin_pimcore_datahub_configpanel_available_fields'),
                layout: 'fit',
                width: 640,
                split: true,
                autoScroll: true,
                rowLines: true,
                columnLines: true,
                listeners: {
                    itemcontextmenu: this.onTreeNodeContextmenu.bind(this)
                },
                columns: [
                    {
                        xtype: 'treecolumn',                    //this is so we know which column will show the tree
                        text: t('configuration'),
                        dataIndex: 'text',
                        flex: 90
                    }
                ]
            });

            var model = store.getModel();
            model.setProxy({
                type: 'memory'
            });
        }

        return this.selectionPanel;
    },

    parentIsOperator: function (record) {
        while (record) {
            if (record.data.isOperator) {
                return true;
            }
            record = record.parentNode;
        }
        return false;
    },

    getNodeTypeAndClass: function (node) {
        var type = "value";
        var className = "";
        if (node.data.configAttributes) {
            type = node.data.configAttributes.type;
            className = node.data.configAttributes['class'];
        } else if (node.data.dataType) {
            className = node.data.dataType.toLowerCase();
        }
        return {type: type, className: className};
    },

    onTreeNodeContextmenu: function (tree, record, item, index, e, eOpts) {
        e.stopEvent();

        tree.select();

        var menu = new Ext.menu.Menu();

        if (this.id != 0) {
            menu.add(new Ext.menu.Item({
                text: t('delete'),
                iconCls: "pimcore_icon_delete",
                handler: function (node) {
                    record.parentNode.removeChild(record, true);
                }.bind(this, record)
            }));

            if (record.data.children && record.data.children.length > 0) {
                menu.add(new Ext.menu.Item({
                    text: t('collapse_children'),
                    iconCls: "pimcore_icon_collapse_children",
                    handler: function (node) {
                        record.collapseChildren();
                    }.bind(this, record)
                }));

                menu.add(new Ext.menu.Item({
                    text: t('expand_children'),
                    iconCls: "pimcore_icon_expand_children",
                    handler: function (node) {
                        record.expandChildren();
                    }.bind(this, record)
                }));
            }

            if (record.data.isOperator) {
                menu.add(new Ext.menu.Item({
                    text: t('edit'),
                    iconCls: "pimcore_icon_edit",
                    handler: function (node) {
                        var nodeConfig = {
                            "isOperator": node.data.isOperator,
                            "attributes": node.data.configAttributes
                        }
                        this.getConfigElement(nodeConfig).getConfigDialog(node,
                            {
                                callback: function () {
                                    console.log("callback not needed for now");
                                }.bind(this)
                            });
                    }.bind(this, record)
                }));
            }
        }

        menu.showAt(e.pageX, e.pageY);
    },


    getClassDefinitionTreePanel: function () {
        if (!this.classDefinitionTreePanel) {

            var items = [];

            this.brickKeys = [];
            this.classDefinitionTreePanel = this.getClassTree("/admin/class/get-class-definition-for-column-config",
                this.generalConfig.classId, this.generalConfig.objectId);
        }

        return this.classDefinitionTreePanel;
    },

    getClassTree: function (url, classId, objectId) {

        var classTreeHelper = new pimcore.object.helpers.classTree(this.showFieldname, {
            showInvisible: true
        });
        var tree = classTreeHelper.getClassTree(url, classId, objectId);

        tree.addListener("itemdblclick", function (tree, record, item, index, e, eOpts) {
            if (!record.data.root && record.data.type != "layout" && record.data.dataType != 'localizedfields') {

                if (!this.checkSupported(record)) {
                    return;
                }

                var copy = Ext.apply({}, record.data);

                if (this.selectionPanel && !this.selectionPanel.getRootNode().findChild("key", record.data.key)) {
                    delete copy.id;
                    copy = this.selectionPanel.getRootNode().appendChild(copy);

                    var ownerTree = this.selectionPanel;

                    // TODO same as above regarding the core
                    // classificaton is stored on field level but on key level
                    // if (record.data.dataType == "classificationstore") {
                    //     var ccd = new pimcore.object.classificationstore.columnConfigDialog();
                    //     ccd.getConfigDialog(ownerTree, copy, this.selectionPanel);
                    // }
                }
            }
        }.bind(this));

        return tree;
    },

    getOperatorTrees: function () {
        var operators = pimcore.plugin.datahub[this.type + "operator"] ? Object.keys(pimcore.plugin.datahub[this.type + "operator"]) : [];
        var operatorGroups = [];

        for (var i = 0; i < operators.length; i++) {
            var operator = operators[i];

            if (operator == this.type + "operator") {
                continue;
            }

            if (!operator) {
                console.error("could not resolve operator");
                continue;
            }
            if (!this.availableOperators || this.availableOperators.indexOf(operator) >= 0) {
                var nodeConfig = pimcore.plugin.datahub[this.type + "operator"][operator].prototype;
                var configTreeNode = nodeConfig.getConfigTreeNode();

                var operatorGroup = nodeConfig.operatorGroup ? nodeConfig.operatorGroup : "other";

                if (!operatorGroups[operatorGroup]) {
                    operatorGroups[operatorGroup] = [];
                }

                var groupName = nodeConfig.group || "other";
                if (!operatorGroups[operatorGroup][groupName]) {
                    operatorGroups[operatorGroup][groupName] = [];
                }
                operatorGroups[operatorGroup][groupName].push(configTreeNode);
            }
        }

        var operatorGroupKeys = [];
        for (k in operatorGroups) {
            if (operatorGroups.hasOwnProperty(k)) {
                operatorGroupKeys.push(k);
            }
        }
        operatorGroupKeys.sort();
        var result = [];
        var len = operatorGroupKeys.length;
        for (i = 0; i < len; i++) {
            var operatorGroupName = operatorGroupKeys[i];
            var groupNodes = operatorGroups[operatorGroupName];
            result.push(this.getOperatorTree(operatorGroupName, groupNodes));

        }
        return result;
    },

    getOperatorTree: function (operatorGroupName, groups) {
        var groupKeys = [];
        for (k in groups) {
            if (groups.hasOwnProperty(k)) {
                groupKeys.push(k);
            }
        }

        groupKeys.sort();

        var len = groupKeys.length;

        var groupNodes = [];

        for (i = 0; i < len; i++) {
            var k = groupKeys[i];
            var children = groups[k];
            children.sort(
                function (x, y) {
                    return x.text < y.text ? -1 : 1;
                }
            );

            var groupNode = {
                iconCls: 'pimcore_icon_folder',
                text: t(k),
                allowDrag: false,
                allowDrop: false,
                leaf: false,
                expanded: true,
                children: children
            };

            groupNodes.push(groupNode);
        }

        var tree = new Ext.tree.TreePanel({
            title: t('operator_group_' + operatorGroupName),
            iconCls: 'pimcore_icon_gridconfig_operator_' + operatorGroupName,
            xtype: "treepanel",
            region: "south",
            autoScroll: true,
            layout: 'fit',
            rootVisible: false,
            resizeable: true,
            split: true,
            viewConfig: {
                plugins: {
                    ptype: 'treeviewdragdrop',
                    ddGroup: "columnconfigelement",
                    enableDrag: true,
                    enableDrop: false
                }
            },
            root: {
                id: "0",
                root: true,
                text: t("base"),
                draggable: false,
                leaf: false,
                isTarget: false,
                children: groupNodes
            }
        });

        tree.addListener("itemdblclick", function (tree, record, item, index, e, eOpts) {
            var attr = record.data;
            if (record.data.configAttributes) {
                attr = record.data.configAttributes;
            }
            var elementConfig = {
                "isOperator": true,
                "attributes": attr
            }

            var element = this.getConfigElement(elementConfig);
            var copy = element.getCopyNode(record);
            var addedNode = this.selectionPanel.getRootNode().appendChild(copy);
            this.openConfigDialog(element, addedNode);
        }.bind(this));

        return tree;
    },

    getConfigElement: function (configAttributes) {
        var element = null;
        var attributes = configAttributes.attributes;
        if (attributes && attributes.class && attributes.type) {
            var jsClass = attributes.class.toLowerCase();
            if (pimcore.plugin.datahub[this.type + attributes.type] && pimcore.plugin.datahub[this.type + attributes.type][jsClass]) {
                element = new pimcore.plugin.datahub[this.type + attributes.type][jsClass](this.generalConfig.classId);
            }
        } else {
            var dataType = configAttributes.dataType ? configAttributes.dataType.toLowerCase() : null;
            if (pimcore.plugin.datahub[this.type + "value"] && pimcore.plugin.datahub[this.type + "value"][dataType]) {
                element = new pimcore.plugin.datahub[this.type + "value"][dataType](this.generalConfig.classId);
            } else {
                element = new pimcore.plugin.datahub[this.type + "value"]["defaultvalue"](this.generalConfig.classId);
            }
        }
        return element;
    },

    checkSupported: function (record) {
        if (record.data.type == "data") {
            var dataType = record.data.dataType;
            if (dataType != "system" && !in_array(dataType, pimcore.plugin.datahub.graphql["supported" + ucfirst(this.type) + "DataTypes"])) {
                Ext.MessageBox.alert(t("error"), sprintf(t("plugin_pimcore_datahub_" + this.type) + " " + t('plugin_pimcore_datahub_datatype_not_supported_yet'), dataType));
                return false;
            }
        }
        return true;
    }
});
