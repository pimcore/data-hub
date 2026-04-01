"use strict";
(self["webpackChunkpimcore_datahub_bundle"] = self["webpackChunkpimcore_datahub_bundle"] || []).push([["__federation_expose_default_export"], {
"./js/src/modules/config/components/column-config-modal/column-config-modal.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  ColumnConfigModal: () => (ColumnConfigModal)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_3);
/* import */ var _pimcore_studio_ui_bundle_api_data_object__rspack_import_4 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/api/data-object");
/* import */ var _pimcore_studio_ui_bundle_api_data_object__rspack_import_4_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_api_data_object__rspack_import_4);
/* import */ var _pimcore_studio_ui_bundle_modules_data_object__rspack_import_5 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/modules/data-object");
/* import */ var _pimcore_studio_ui_bundle_modules_data_object__rspack_import_5_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_modules_data_object__rspack_import_5);
/* import */ var _migration_modal__rspack_import_6 = __webpack_require__("./js/src/modules/config/components/migration-modal/index.ts");
/* import */ var _use_add_column_dropdown__rspack_import_7 = __webpack_require__("./js/src/modules/config/components/column-config-modal/use-add-column-dropdown.ts");
/* provided dependency */ var $ReactRefreshRuntime$ = __webpack_require__("./node_modules/@rspack/plugin-react-refresh/client/reactRefresh.js");

var _s = $RefreshSig$();
/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */ 






/**
 * Generic ColumnConfigModal for Data Hub adapter bundles.
 *
 * Handles both the new-format path (plain Modal + editor) and the legacy
 * migration path (MigrationModal with split view). The adapter-specific
 * editor is injected via the `renderEditor` render prop.
 */ const ColumnConfigModal = (param)=>{
    let { entity, classDefinitionId, columns, columnConfig, open, title, onApply, onCancel, renderEditor } = param;
    _s();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation)();
    const { getByName } = (0,_pimcore_studio_ui_bundle_modules_data_object__rspack_import_5.useClassDefinitions)();
    const resolvedClassId = react__rspack_import_1_default().useMemo(()=>{
        var _getByName;
        if (classDefinitionId !== undefined) return classDefinitionId;
        return ((_getByName = getByName(entity)) === null || _getByName === void 0 ? void 0 : _getByName.id) ?? entity;
    }, [
        classDefinitionId,
        entity,
        getByName
    ]);
    const isLegacy = columnConfig !== undefined;
    const [migratedColumns, setMigratedColumns] = (0,react__rspack_import_1.useState)([]);
    const columnEditorRef = (0,react__rspack_import_1.useRef)(null);
    const { data: availableFieldsData } = _pimcore_studio_ui_bundle_api_data_object__rspack_import_4.api.endpoints.dataObjectGetAvailableGridColumns.useQuery({
        classId: resolvedClassId,
        folderId: 1
    }, {
        skip: !isLegacy
    });
    const availableFields = (availableFieldsData === null || availableFieldsData === void 0 ? void 0 : availableFieldsData.columns) ?? [];
    const handleAddColumn = (0,react__rspack_import_1.useCallback)((column)=>{
        var _columnEditorRef_current;
        (_columnEditorRef_current = columnEditorRef.current) === null || _columnEditorRef_current === void 0 ? void 0 : _columnEditorRef_current.addColumn(column);
    }, []);
    const addColumnMenu = (0,_use_add_column_dropdown__rspack_import_7.useAddColumnDropdown)(availableFields, handleAddColumn);
    const handleConfirmMigration = ()=>{
        var _columnEditorRef_current;
        const cols = ((_columnEditorRef_current = columnEditorRef.current) === null || _columnEditorRef_current === void 0 ? void 0 : _columnEditorRef_current.getColumns()) ?? migratedColumns;
        onApply(cols);
    };
    const modalTitle = /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.ModalTitle, {
        iconName: "settings",
        children: title
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-config-modal.tsx",
        lineNumber: 113,
        columnNumber: 5
    }, undefined);
    if (!isLegacy) {
        return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Modal, {
            footer: null,
            onCancel: onCancel,
            open: open,
            size: "XL",
            title: modalTitle,
            children: renderEditor({
                ref: columnEditorRef,
                classDefinitionId: resolvedClassId,
                columns,
                entity,
                hideToolbar: false,
                onApply: (updatedColumns)=>{
                    onApply(updatedColumns);
                    onCancel();
                },
                onCancel
            })
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-config-modal.tsx",
            lineNumber: 120,
            columnNumber: 7
        }, undefined);
    }
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_migration_modal__rspack_import_6.MigrationModal, {
        legacyConfig: columnConfig,
        onClose: onCancel,
        onConfirm: handleConfirmMigration,
        open: open,
        renderToolbarLeft: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Dropdown, {
            menu: addColumnMenu,
            children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.IconTextButton, {
                icon: {
                    value: 'new'
                },
                children: t('data-hub.column-config-modal.add-column')
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-config-modal.tsx",
                lineNumber: 151,
                columnNumber: 11
            }, undefined)
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-config-modal.tsx",
            lineNumber: 150,
            columnNumber: 9
        }, undefined),
        title: title,
        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Flex, {
            style: {
                flex: 1,
                minWidth: 0,
                overflow: 'hidden'
            },
            vertical: true,
            children: renderEditor({
                ref: columnEditorRef,
                classDefinitionId: resolvedClassId,
                columns: migratedColumns,
                entity,
                hideToolbar: true,
                onApply: (cols)=>{
                    setMigratedColumns(cols);
                },
                onCancel: ()=>{}
            })
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-config-modal.tsx",
            lineNumber: 158,
            columnNumber: 7
        }, undefined)
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-config-modal.tsx",
        lineNumber: 144,
        columnNumber: 5
    }, undefined);
};
_s(ColumnConfigModal, "2yB+Cuqs5Lks3aR78axJ6jL2azw=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation,
        _pimcore_studio_ui_bundle_modules_data_object__rspack_import_5.useClassDefinitions,
        _use_add_column_dropdown__rspack_import_7.useAddColumnDropdown
    ];
});
_c = ColumnConfigModal;
var _c;
$RefreshReg$(_c, "ColumnConfigModal");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/config/components/column-config-modal/column-editor-item.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  ColumnEditorItemBody: () => (ColumnEditorItemBody)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _column_pipeline_form__rspack_import_2 = __webpack_require__("./js/src/modules/config/components/column-config-modal/column-pipeline-form.tsx");
/* provided dependency */ var $ReactRefreshRuntime$ = __webpack_require__("./node_modules/@rspack/plugin-react-refresh/client/reactRefresh.js");

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */ 

const ColumnEditorItemBody = (param)=>{
    let { column, entity, objectId, onPipelineChange, sourceFieldsRegistryId, transformersRegistryId } = param;
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_column_pipeline_form__rspack_import_2.ColumnPipelineForm, {
        column: column,
        config: column.pipelineConfig,
        entity: entity,
        objectId: objectId,
        onChange: (pipeline)=>{
            onPipelineChange(column._id, pipeline);
        },
        sourceFieldsRegistryId: sourceFieldsRegistryId,
        transformersRegistryId: transformersRegistryId,
        value: column.pipeline
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-editor-item.tsx",
        lineNumber: 35,
        columnNumber: 5
    }, undefined);
};
_c = ColumnEditorItemBody;
var _c;
$RefreshReg$(_c, "ColumnEditorItemBody");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/config/components/column-config-modal/column-pipeline-form.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  ColumnPipelineForm: () => (ColumnPipelineForm)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_3);
/* import */ var lodash__rspack_import_4 = __webpack_require__("./node_modules/lodash/lodash.js");
/* import */ var lodash__rspack_import_4_default = /*#__PURE__*/__webpack_require__.n(lodash__rspack_import_4);
/* import */ var _column_preview__rspack_import_5 = __webpack_require__("./js/src/modules/config/components/column-config-modal/column-preview.tsx");
/* provided dependency */ var $ReactRefreshRuntime$ = __webpack_require__("./node_modules/@rspack/plugin-react-refresh/client/reactRefresh.js");

var _s = $RefreshSig$();
/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */ 




const ColumnPipelineForm = (param)=>{
    let { column, entity, config, objectId, value, onChange, sourceFieldsRegistryId, transformersRegistryId } = param;
    _s();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_2.useTranslation)();
    const [form] = _pimcore_studio_ui_bundle_components__rspack_import_3.Form.useForm();
    const [liveValue, setLiveValue] = (0,react__rspack_import_1.useState)(value ?? {});
    (0,react__rspack_import_1.useEffect)(()=>{
        form.setFieldValue('value', value ?? {});
    }, [
        value
    ]);
    const onValuesChange = (changedValues)=>{
        const newPipelineValue = form.getFieldValue('value');
        if (newPipelineValue !== undefined && !(0,lodash__rspack_import_4.isEqual)(liveValue, newPipelineValue)) {
            setLiveValue(newPipelineValue);
            onChange === null || onChange === void 0 ? void 0 : onChange(newPipelineValue);
        }
    };
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Form, {
        form: form,
        initialValues: {
            value: value ?? {}
        },
        layout: "vertical",
        onValuesChange: onValuesChange,
        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.PipelineConfigProvider, {
            initialConfig: config ?? {},
            children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Form.Item, {
                name: "value",
                children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Pipeline, {
                    items: [
                        {
                            id: 'fields',
                            component: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Pipeline.CustomItem, {
                                children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Tabs, {
                                    items: [
                                        {
                                            key: 'title',
                                            label: t('data-hub.column-config-modal.pipeline.title'),
                                            forceRender: true,
                                            children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Form.Item, {
                                                label: t('data-hub.column-config-modal.pipeline.title'),
                                                name: "title",
                                                children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Input, {}, void 0, false, {
                                                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-pipeline-form.tsx",
                                                    lineNumber: 83,
                                                    columnNumber: 31
                                                }, undefined)
                                            }, void 0, false, {
                                                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-pipeline-form.tsx",
                                                lineNumber: 79,
                                                columnNumber: 29
                                            }, undefined)
                                        },
                                        {
                                            key: 'sourceFields',
                                            label: t('grid.advanced-column.advancedColumns'),
                                            forceRender: true,
                                            children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Pipeline.DynamicGroupItem, {
                                                dynamicTypeRegistryId: sourceFieldsRegistryId,
                                                id: "sourceFields",
                                                translationKeyPrefix: "data-hub.column-config-modal.pipeline"
                                            }, void 0, false, {
                                                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-pipeline-form.tsx",
                                                lineNumber: 92,
                                                columnNumber: 29
                                            }, undefined)
                                        },
                                        {
                                            key: 'transformers',
                                            label: t('grid.advanced-column.transformers'),
                                            forceRender: true,
                                            children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Pipeline.DynamicGroupItem, {
                                                dynamicTypeRegistryId: transformersRegistryId,
                                                id: "transformers",
                                                translationKeyPrefix: "data-hub.column-config-modal.pipeline"
                                            }, void 0, false, {
                                                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-pipeline-form.tsx",
                                                lineNumber: 104,
                                                columnNumber: 29
                                            }, undefined)
                                        }
                                    ]
                                }, void 0, false, {
                                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-pipeline-form.tsx",
                                    lineNumber: 72,
                                    columnNumber: 21
                                }, undefined)
                            }, void 0, false, {
                                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-pipeline-form.tsx",
                                lineNumber: 71,
                                columnNumber: 19
                            }, undefined)
                        },
                        {
                            id: 'preview',
                            component: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Pipeline.CustomItem, {
                                children: column !== undefined && /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_column_preview__rspack_import_5.ColumnPreview, {
                                    column: column,
                                    objectId: objectId ?? null,
                                    pipelineValue: liveValue
                                }, void 0, false, {
                                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-pipeline-form.tsx",
                                    lineNumber: 121,
                                    columnNumber: 23
                                }, undefined)
                            }, void 0, false, {
                                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-pipeline-form.tsx",
                                lineNumber: 119,
                                columnNumber: 19
                            }, undefined)
                        }
                    ],
                    value: value ?? {}
                }, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-pipeline-form.tsx",
                    lineNumber: 66,
                    columnNumber: 11
                }, undefined)
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-pipeline-form.tsx",
                lineNumber: 65,
                columnNumber: 9
            }, undefined)
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-pipeline-form.tsx",
            lineNumber: 64,
            columnNumber: 7
        }, undefined)
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-pipeline-form.tsx",
        lineNumber: 58,
        columnNumber: 5
    }, undefined);
};
_s(ColumnPipelineForm, "5ZxYW8yjx8VRbJTnFAhCtazzoGY=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_2.useTranslation,
        _pimcore_studio_ui_bundle_components__rspack_import_3.Form.useForm
    ];
});
_c = ColumnPipelineForm;
var _c;
$RefreshReg$(_c, "ColumnPipelineForm");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/config/components/column-config-modal/column-preview.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  ColumnPreview: () => (ColumnPreview)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_3);
/* import */ var _pimcore_studio_ui_bundle_modules_data_object__rspack_import_4 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/modules/data-object");
/* import */ var _pimcore_studio_ui_bundle_modules_data_object__rspack_import_4_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_modules_data_object__rspack_import_4);
/* import */ var _pimcore_studio_ui_bundle_api_data_object__rspack_import_5 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/api/data-object");
/* import */ var _pimcore_studio_ui_bundle_api_data_object__rspack_import_5_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_api_data_object__rspack_import_5);
/* import */ var _tanstack_react_table__rspack_import_6 = __webpack_require__("./node_modules/@tanstack/table-core/build/lib/index.mjs");
/* provided dependency */ var $ReactRefreshRuntime$ = __webpack_require__("./node_modules/@rspack/plugin-react-refresh/client/reactRefresh.js");

var _s = $RefreshSig$(), _s1 = $RefreshSig$(), _s2 = $RefreshSig$();
/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */ 






const columnHelper = (0,_tanstack_react_table__rspack_import_6.createColumnHelper)();
const PreviewGrid = (param)=>{
    let { value } = param;
    _s();
    const advancedGridCellRegistry = (0,_pimcore_studio_ui_bundle_app__rspack_import_2.useInjection)(_pimcore_studio_ui_bundle_app__rspack_import_2.serviceIds["DynamicTypes/AdvancedGridCellRegistry"]);
    const columns = value.map((item, index)=>{
        const isAdvancedCellType = advancedGridCellRegistry.hasDynamicType(item.type);
        return columnHelper.accessor(`${item.type}-${index}`, {
            header: item.type,
            meta: {
                editable: false,
                type: isAdvancedCellType ? item.type : 'dataobject.adapter',
                config: {
                    ...isAdvancedCellType ? {} : {
                        dataObjectType: item.type,
                        dataObjectConfig: {}
                    }
                }
            }
        });
    });
    const row = {};
    value.forEach((item, index)=>{
        row[`${item.type}-${index}`] = item.value;
    });
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.GridContentRenderer, {
        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Grid, {
            autoWidth: true,
            columns: columns,
            data: [
                row
            ]
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-preview.tsx",
            lineNumber: 61,
            columnNumber: 7
        }, undefined)
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-preview.tsx",
        lineNumber: 60,
        columnNumber: 5
    }, undefined);
};
_s(PreviewGrid, "9KQ9xuYilkCGI28BxlvVo49MSBc=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_2.useInjection
    ];
});
_c = PreviewGrid;
const PreviewResult = (param)=>{
    let { column, objectId, pipelineValue } = param;
    _s1();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_2.useTranslation)();
    const { currentLanguage } = (0,_pimcore_studio_ui_bundle_modules_data_object__rspack_import_4.useLanguageSelection)();
    const pipeline = pipelineValue !== undefined && Object.keys(pipelineValue).length > 0 ? pipelineValue : column.pipeline;
    // Resolve locale: explicit per-column override > global language (only for localizable columns)
    const resolvedLocale = column.localizable === true ? column.locale ?? currentLanguage : undefined;
    const { data, error, isLoading } = _pimcore_studio_ui_bundle_api_data_object__rspack_import_5.api.endpoints.dataObjectGetGridPreview.useQuery({
        body: {
            objectId,
            column: {
                type: column.type,
                key: column.key,
                locale: resolvedLocale,
                config: pipeline !== undefined ? {
                    advancedColumns: pipeline.sourceFields ?? [],
                    transformers: pipeline.transformers
                } : undefined
            }
        }
    });
    if (isLoading) {
        return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Text, {
            type: "secondary",
            children: t('data-hub.column-config-modal.preview.loading')
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-preview.tsx",
            lineNumber: 99,
            columnNumber: 12
        }, undefined);
    }
    if (error !== undefined) {
        const message = 'error' in error ? error.error : t('data-hub.column-config-modal.preview.error');
        return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Text, {
            type: "danger",
            children: message
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-preview.tsx",
            lineNumber: 104,
            columnNumber: 12
        }, undefined);
    }
    const value = data === null || data === void 0 ? void 0 : data.value;
    if (value === undefined || value === null || !Array.isArray(value) || value.length === 0) {
        return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Text, {
            type: "secondary",
            children: t('data-hub.column-config-modal.preview.no-data')
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-preview.tsx",
            lineNumber: 110,
            columnNumber: 12
        }, undefined);
    }
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(PreviewGrid, {
        value: value
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-preview.tsx",
        lineNumber: 113,
        columnNumber: 10
    }, undefined);
};
_s1(PreviewResult, "zuwTyOUPBWcYOoSk2XACM5ZfeSg=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_2.useTranslation,
        _pimcore_studio_ui_bundle_modules_data_object__rspack_import_4.useLanguageSelection
    ];
});
_c1 = PreviewResult;
const ColumnPreview = (param)=>{
    let { column, objectId, pipelineValue } = param;
    _s2();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_2.useTranslation)();
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)("div", {
        style: {
            minHeight: 60,
            display: 'flex',
            alignItems: 'center'
        },
        children: objectId === null ? /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Text, {
            type: "secondary",
            children: t('data-hub.column-config-modal.preview.placeholder')
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-preview.tsx",
            lineNumber: 123,
            columnNumber: 11
        }, undefined) : /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(PreviewResult, {
            column: column,
            objectId: objectId,
            pipelineValue: pipelineValue
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-preview.tsx",
            lineNumber: 128,
            columnNumber: 11
        }, undefined)
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-preview.tsx",
        lineNumber: 120,
        columnNumber: 5
    }, undefined);
};
_s2(ColumnPreview, "zlIdU9EjM2llFt74AbE2KsUJXyM=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_2.useTranslation
    ];
});
_c2 = ColumnPreview;
var _c, _c1, _c2;
$RefreshReg$(_c, "PreviewGrid");
$RefreshReg$(_c1, "PreviewResult");
$RefreshReg$(_c2, "ColumnPreview");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/config/components/column-config-modal/index.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  ADVANCED_COLUMN_KEY: () => (/* reexport safe */ _types__rspack_import_2.ADVANCED_COLUMN_KEY),
  ColumnConfigModal: () => (/* reexport safe */ _column_config_modal__rspack_import_0.ColumnConfigModal),
  ColumnEditorItemBody: () => (/* reexport safe */ _column_editor_item__rspack_import_5.ColumnEditorItemBody),
  ColumnPipelineForm: () => (/* reexport safe */ _column_pipeline_form__rspack_import_4.ColumnPipelineForm),
  ColumnPreview: () => (/* reexport safe */ _column_preview__rspack_import_3.ColumnPreview),
  advancedFromSchemaColumn: () => (/* reexport safe */ _types__rspack_import_2.advancedFromSchemaColumn),
  advancedToSchemaColumn: () => (/* reexport safe */ _types__rspack_import_2.advancedToSchemaColumn),
  useAddColumnDropdown: () => (/* reexport safe */ _use_add_column_dropdown__rspack_import_1.useAddColumnDropdown)
});
/* import */ var _column_config_modal__rspack_import_0 = __webpack_require__("./js/src/modules/config/components/column-config-modal/column-config-modal.tsx");
/* import */ var _use_add_column_dropdown__rspack_import_1 = __webpack_require__("./js/src/modules/config/components/column-config-modal/use-add-column-dropdown.ts");
/* import */ var _types__rspack_import_2 = __webpack_require__("./js/src/modules/config/components/column-config-modal/types.ts");
/* import */ var _column_preview__rspack_import_3 = __webpack_require__("./js/src/modules/config/components/column-config-modal/column-preview.tsx");
/* import */ var _column_pipeline_form__rspack_import_4 = __webpack_require__("./js/src/modules/config/components/column-config-modal/column-pipeline-form.tsx");
/* import */ var _column_editor_item__rspack_import_5 = __webpack_require__("./js/src/modules/config/components/column-config-modal/column-editor-item.tsx");
/* provided dependency */ var $ReactRefreshRuntime$ = __webpack_require__("./node_modules/@rspack/plugin-react-refresh/client/reactRefresh.js");
/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */ 






function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/config/components/column-config-modal/types.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  ADVANCED_COLUMN_KEY: () => (ADVANCED_COLUMN_KEY),
  advancedFromSchemaColumn: () => (advancedFromSchemaColumn),
  advancedToSchemaColumn: () => (advancedToSchemaColumn)
});
/* provided dependency */ var $ReactRefreshRuntime$ = __webpack_require__("./node_modules/@rspack/plugin-react-refresh/client/reactRefresh.js");
/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */ /**
 * The special column key that identifies an advanced/pipeline column.
 * Advanced columns have a pipeline (sourceFields + transformers) instead of a direct field mapping.
 */ const ADVANCED_COLUMN_KEY = 'advanced';
/**
 * Converts a persisted SchemaColumn into an AdvancedEditorColumn draft.
 * For advanced columns (key === ADVANCED_COLUMN_KEY) the persisted config is
 * unpacked into the frontend `pipeline` working state.
 */ const advancedFromSchemaColumn = (col)=>{
    var _col_config, _col_config1;
    return {
        _id: crypto.randomUUID(),
        key: col.key,
        fieldtype: col.fieldtype,
        type: col.type,
        config: col.config,
        pipeline: col.key === ADVANCED_COLUMN_KEY ? {
            title: col.title,
            sourceFields: (((_col_config = col.config) === null || _col_config === void 0 ? void 0 : _col_config.advancedColumns) ?? []).map((sf)=>({
                    ...sf,
                    config: Array.isArray(sf.config) ? sf.config[0] ?? {} : sf.config ?? {}
                })),
            transformers: (_col_config1 = col.config) === null || _col_config1 === void 0 ? void 0 : _col_config1.transformers
        } : undefined,
        locale: col.locale
    };
};
/**
 * Converts an AdvancedEditorColumn draft back to a persisted SchemaColumn.
 * For advanced columns the frontend `pipeline` is packed into config.advancedColumns + config.transformers.
 */ const advancedToSchemaColumn = (col)=>{
    var _col_pipeline;
    return {
        key: col.key,
        fieldtype: col.fieldtype,
        type: col.type,
        config: col.pipeline !== undefined ? {
            advancedColumns: (col.pipeline.sourceFields ?? []).map((sf)=>({
                    ...sf,
                    config: sf.config !== undefined && !Array.isArray(sf.config) ? [
                        sf.config
                    ] : sf.config ?? []
                })),
            transformers: col.pipeline.transformers
        } : col.config,
        title: (_col_pipeline = col.pipeline) === null || _col_pipeline === void 0 ? void 0 : _col_pipeline.title,
        locale: col.locale
    };
};

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/config/components/column-config-modal/use-add-column-dropdown.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  useAddColumnDropdown: () => (useAddColumnDropdown)
});
/* import */ var react__rspack_import_0 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_0_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_0);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_1 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_1);
/* import */ var lodash__rspack_import_2 = __webpack_require__("./node_modules/lodash/lodash.js");
/* import */ var lodash__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(lodash__rspack_import_2);
/* provided dependency */ var $ReactRefreshRuntime$ = __webpack_require__("./node_modules/@rspack/plugin-react-refresh/client/reactRefresh.js");
/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */ 


/**
 * Builds a nested Ant Design dropdown menu from a flat list of GridColumnConfiguration entries.
 */ const useAddColumnDropdown = (availableColumns, onMenuItemClick)=>{
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_1.useTranslation)();
    return (0,react__rspack_import_0.useMemo)(()=>{
        const groupTree = {};
        let menuIndex = 0;
        availableColumns.forEach((column)=>{
            let normalizedGroups = [];
            if (Array.isArray(column.group)) {
                const hasNestedArrays = column.group.some((item)=>Array.isArray(item));
                if (hasNestedArrays) {
                    normalizedGroups = column.group;
                } else {
                    normalizedGroups = [
                        column.group
                    ];
                }
            } else if (typeof column.group === 'string') {
                normalizedGroups = [
                    column.group
                ];
            } else {
                normalizedGroups = [
                    String(column.group)
                ];
            }
            normalizedGroups.forEach((groupPath)=>{
                let groupParts;
                if (typeof groupPath === 'string') {
                    groupParts = groupPath.split('.');
                } else if (Array.isArray(groupPath)) {
                    groupParts = groupPath.map((part)=>String(part));
                } else {
                    groupParts = [
                        String(groupPath)
                    ];
                }
                let currentLevel = groupTree;
                groupParts.forEach((part, index)=>{
                    if ((0,lodash__rspack_import_2.isNil)(currentLevel[part])) {
                        currentLevel[part] = {
                            items: [],
                            subGroups: {}
                        };
                    }
                    if (index === groupParts.length - 1) {
                        currentLevel[part].items.push(column);
                    } else {
                        currentLevel = currentLevel[part].subGroups;
                    }
                });
            });
        });
        const convertTreeToMenuItems = (tree)=>{
            return Object.entries(tree).map((param)=>{
                let [groupName, groupData] = param;
                const menuItem = {
                    key: `group-${menuIndex++}`,
                    label: t(groupName)
                };
                const subGroupItems = !(0,lodash__rspack_import_2.isEmpty)(Object.keys(groupData.subGroups)) ? convertTreeToMenuItems(groupData.subGroups) : [];
                const columnItems = groupData.items.map((column)=>{
                    let translationKey = column.key;
                    if (!(0,lodash__rspack_import_2.isNil)(column.config) && 'fieldDefinition' in column.config) {
                        const fieldDefinition = column.config.fieldDefinition;
                        translationKey = (fieldDefinition === null || fieldDefinition === void 0 ? void 0 : fieldDefinition.title) ?? column.key;
                    }
                    return {
                        key: column.key,
                        label: t(translationKey),
                        onClick: ()=>{
                            onMenuItemClick(column);
                        }
                    };
                });
                const allChildren = [
                    ...subGroupItems,
                    ...columnItems
                ];
                if (allChildren.length > 0) menuItem.children = allChildren;
                return menuItem;
            });
        };
        return {
            items: convertTreeToMenuItems(groupTree)
        };
    }, [
        availableColumns,
        t,
        onMenuItemClick
    ]);
};

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/config/components/migration-modal/index.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  MigrationModal: () => (/* reexport safe */ _migration_modal__rspack_import_0.MigrationModal)
});
/* import */ var _migration_modal__rspack_import_0 = __webpack_require__("./js/src/modules/config/components/migration-modal/migration-modal.tsx");
/* provided dependency */ var $ReactRefreshRuntime$ = __webpack_require__("./node_modules/@rspack/plugin-react-refresh/client/reactRefresh.js");
/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */ 

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/config/components/migration-modal/migration-modal.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  MigrationModal: () => (MigrationModal)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_3);
/* import */ var _sdk_utils_yaml__rspack_import_4 = __webpack_require__("./js/src/sdk/utils/yaml.ts");
/* provided dependency */ var $ReactRefreshRuntime$ = __webpack_require__("./node_modules/@rspack/plugin-react-refresh/client/reactRefresh.js");

var _s = $RefreshSig$();
/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */ 



/**
 * MigrationModal — generic split-view migration shell for Data Hub adapters.
 *
 * Manages two visual states internally:
 *  1. **Initial legacy view** (`isMigrating = false`): shows the legacy config as
 *     read-only YAML with a warning alert and a "Start Migration" button.
 *  2. **Split migration view** (`isMigrating = true`): left pane renders `children`
 *     (the consumer's new editor), right pane shows the legacy YAML as reference.
 *     Toolbar provides Cancel (returns to initial view) and Confirm Migration.
 */ const MigrationModal = (param)=>{
    let { open, title, iconName = 'settings', legacyConfig, renderToolbarLeft, onConfirm, onClose, children } = param;
    _s();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation)();
    const [isMigrating, setIsMigrating] = (0,react__rspack_import_1.useState)(false);
    const yamlValue = (0,_sdk_utils_yaml__rspack_import_4.stringifyYaml)(legacyConfig);
    const modalTitle = /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.ModalTitle, {
        iconName: iconName,
        children: title
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/migration-modal/migration-modal.tsx",
        lineNumber: 85,
        columnNumber: 5
    }, undefined);
    // ── Initial legacy view ──────────────────────────────────────────────────
    if (!isMigrating) {
        return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Modal, {
            footer: null,
            onCancel: onClose,
            open: open,
            size: "XL",
            title: modalTitle,
            children: [
                /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Flex, {
                    gap: "small",
                    vertical: true,
                    children: [
                        /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Alert, {
                            description: t('data-hub.migration-modal.legacy-notice'),
                            showIcon: true,
                            type: "warning"
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/migration-modal/migration-modal.tsx",
                            lineNumber: 104,
                            columnNumber: 11
                        }, undefined),
                        /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.CodeEditor, {
                            height: "400px",
                            preset: "yaml",
                            readOnly: true,
                            value: yamlValue
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/migration-modal/migration-modal.tsx",
                            lineNumber: 110,
                            columnNumber: 11
                        }, undefined)
                    ]
                }, void 0, true, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/migration-modal/migration-modal.tsx",
                    lineNumber: 100,
                    columnNumber: 9
                }, undefined),
                /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Toolbar, {
                    padding: {
                        x: 'none',
                        y: 'small'
                    },
                    theme: "secondary",
                    children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Space, {
                        size: "extra-small",
                        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Button, {
                            onClick: ()=>{
                                setIsMigrating(true);
                            },
                            type: "primary",
                            children: t('data-hub.migration-modal.start-migration')
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/migration-modal/migration-modal.tsx",
                            lineNumber: 123,
                            columnNumber: 13
                        }, undefined)
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/migration-modal/migration-modal.tsx",
                        lineNumber: 122,
                        columnNumber: 11
                    }, undefined)
                }, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/migration-modal/migration-modal.tsx",
                    lineNumber: 118,
                    columnNumber: 9
                }, undefined)
            ]
        }, void 0, true, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/migration-modal/migration-modal.tsx",
            lineNumber: 93,
            columnNumber: 7
        }, undefined);
    }
    // ── Split migration view ─────────────────────────────────────────────────
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Modal, {
        footer: null,
        onCancel: ()=>{
            setIsMigrating(false);
        },
        open: open,
        size: "XL",
        title: modalTitle,
        children: [
            /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Flex, {
                gap: "small",
                style: {
                    height: 'calc(80vh - 120px)',
                    overflow: 'hidden'
                },
                children: [
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Flex, {
                        style: {
                            flex: 1,
                            minWidth: 0,
                            overflow: 'hidden'
                        },
                        vertical: true,
                        children: children
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/migration-modal/migration-modal.tsx",
                        lineNumber: 149,
                        columnNumber: 9
                    }, undefined),
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)("div", {
                        style: {
                            width: 1,
                            background: 'var(--ant-color-split, rgba(0,0,0,.06))',
                            flexShrink: 0
                        }
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/migration-modal/migration-modal.tsx",
                        lineNumber: 156,
                        columnNumber: 9
                    }, undefined),
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Flex, {
                        gap: "small",
                        style: {
                            flex: 1,
                            minWidth: 0,
                            overflow: 'auto'
                        },
                        vertical: true,
                        children: [
                            /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Alert, {
                                description: t('data-hub.migration-modal.legacy-notice'),
                                showIcon: true,
                                type: "warning"
                            }, void 0, false, {
                                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/migration-modal/migration-modal.tsx",
                                lineNumber: 164,
                                columnNumber: 11
                            }, undefined),
                            /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.CodeEditor, {
                                height: "100%",
                                preset: "yaml",
                                readOnly: true,
                                value: yamlValue
                            }, void 0, false, {
                                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/migration-modal/migration-modal.tsx",
                                lineNumber: 169,
                                columnNumber: 11
                            }, undefined)
                        ]
                    }, void 0, true, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/migration-modal/migration-modal.tsx",
                        lineNumber: 159,
                        columnNumber: 9
                    }, undefined)
                ]
            }, void 0, true, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/migration-modal/migration-modal.tsx",
                lineNumber: 144,
                columnNumber: 7
            }, undefined),
            /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Toolbar, {
                padding: {
                    x: 'none',
                    y: 'small'
                },
                theme: "secondary",
                children: [
                    renderToolbarLeft,
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Space, {
                        size: "extra-small",
                        children: [
                            /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Button, {
                                onClick: ()=>{
                                    setIsMigrating(false);
                                },
                                type: "default",
                                children: t('data-hub.migration-modal.cancel')
                            }, void 0, false, {
                                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/migration-modal/migration-modal.tsx",
                                lineNumber: 185,
                                columnNumber: 11
                            }, undefined),
                            /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Button, {
                                onClick: onConfirm,
                                type: "primary",
                                children: t('data-hub.migration-modal.confirm-migration')
                            }, void 0, false, {
                                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/migration-modal/migration-modal.tsx",
                                lineNumber: 192,
                                columnNumber: 11
                            }, undefined)
                        ]
                    }, void 0, true, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/migration-modal/migration-modal.tsx",
                        lineNumber: 184,
                        columnNumber: 9
                    }, undefined)
                ]
            }, void 0, true, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/migration-modal/migration-modal.tsx",
                lineNumber: 178,
                columnNumber: 7
            }, undefined)
        ]
    }, void 0, true, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/migration-modal/migration-modal.tsx",
        lineNumber: 137,
        columnNumber: 5
    }, undefined);
};
_s(MigrationModal, "tDYIbaeXFk16l201s9nlIu+uQO0=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation
    ];
});
_c = MigrationModal;
var _c;
$RefreshReg$(_c, "MigrationModal");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/log-tab/filter-sidebar/filter-sidebar.styles.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  useStyles: () => (useStyles)
});
/* import */ var antd_style__rspack_import_0 = __webpack_require__("./node_modules/antd-style/es/functions/index.js");
/* provided dependency */ var $ReactRefreshRuntime$ = __webpack_require__("./node_modules/@rspack/plugin-react-refresh/client/reactRefresh.js");
/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */ 
const useStyles = (0,antd_style__rspack_import_0.createStyles)((param)=>{
    let { css } = param;
    return {
        fullWidth: css`
      width: 100%;
    `
    };
});

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/log-tab/filter-sidebar/filter-sidebar.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  DATE_FORMAT: () => (DATE_FORMAT),
  FilterSidebar: () => (FilterSidebar)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_1 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_modules_application_logger__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/modules/application-logger");
/* import */ var _pimcore_studio_ui_bundle_modules_application_logger__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_modules_application_logger__rspack_import_3);
/* import */ var react__rspack_import_4 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_4_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_4);
/* import */ var _filter_sidebar_styles__rspack_import_5 = __webpack_require__("./js/src/modules/log-tab/filter-sidebar/filter-sidebar.styles.tsx");
/* provided dependency */ var $ReactRefreshRuntime$ = __webpack_require__("./node_modules/@rspack/plugin-react-refresh/client/reactRefresh.js");

var _s = $RefreshSig$();
/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */ 




const DATE_FORMAT = 'YYYY-MM-DD HH:mm';
const FilterSidebar = ()=>{
    _s();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_1.useTranslation)();
    const { styles } = (0,_filter_sidebar_styles__rspack_import_5.useStyles)();
    const [form] = _pimcore_studio_ui_bundle_components__rspack_import_2.Form.useForm();
    const { dateFrom, setDateFrom, dateTo, setDateTo, relatedObjectId, setRelatedObjectId, message, setMessage, pid, setPid, resetFilters, updateFilters, isLoading } = (0,_pimcore_studio_ui_bundle_modules_application_logger__rspack_import_3.useFilter)();
    const handleResetFilters = ()=>{
        resetFilters();
        form.resetFields();
    };
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.ContentLayout, {
        renderToolbar: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Toolbar, {
            theme: "secondary",
            children: [
                /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.IconTextButton, {
                    disabled: isLoading,
                    icon: {
                        value: 'close'
                    },
                    onClick: handleResetFilters,
                    type: "link",
                    children: t('sidebar.clear-all-filters')
                }, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/log-tab/filter-sidebar/filter-sidebar.tsx",
                    lineNumber: 63,
                    columnNumber: 11
                }, undefined),
                /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Button, {
                    disabled: isLoading,
                    loading: isLoading,
                    onClick: updateFilters,
                    type: "primary",
                    children: t('button.apply')
                }, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/log-tab/filter-sidebar/filter-sidebar.tsx",
                    lineNumber: 72,
                    columnNumber: 11
                }, undefined)
            ]
        }, void 0, true, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/log-tab/filter-sidebar/filter-sidebar.tsx",
            lineNumber: 62,
            columnNumber: 9
        }, undefined),
        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Content, {
            padded: true,
            children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form, {
                form: form,
                layout: "vertical",
                children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Space, {
                    className: styles.fullWidth,
                    direction: "vertical",
                    size: "none",
                    children: [
                        /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Title, {
                            children: t('application-logger.sidebar.search-parameter')
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/log-tab/filter-sidebar/filter-sidebar.tsx",
                            lineNumber: 93,
                            columnNumber: 13
                        }, undefined),
                        /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                            label: t('application-logger.filter.date-from'),
                            name: "dateFrom",
                            children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.DatePicker, {
                                className: "w-full",
                                format: DATE_FORMAT,
                                onChange: (value)=>{
                                    setDateFrom(value);
                                },
                                outputType: "dateString",
                                showTime: {
                                    format: 'HH:mm'
                                },
                                value: dateFrom
                            }, void 0, false, {
                                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/log-tab/filter-sidebar/filter-sidebar.tsx",
                                lineNumber: 99,
                                columnNumber: 15
                            }, undefined)
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/log-tab/filter-sidebar/filter-sidebar.tsx",
                            lineNumber: 95,
                            columnNumber: 13
                        }, undefined),
                        /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                            label: t('application-logger.filter.date-to'),
                            name: "dateTo",
                            children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.DatePicker, {
                                className: "w-full",
                                format: DATE_FORMAT,
                                onChange: (value)=>{
                                    setDateTo(value);
                                },
                                outputType: "dateString",
                                showTime: {
                                    format: 'HH:mm'
                                },
                                value: dateTo
                            }, void 0, false, {
                                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/log-tab/filter-sidebar/filter-sidebar.tsx",
                                lineNumber: 115,
                                columnNumber: 15
                            }, undefined)
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/log-tab/filter-sidebar/filter-sidebar.tsx",
                            lineNumber: 111,
                            columnNumber: 13
                        }, undefined),
                        /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                            label: t('application-logger.filter.priority'),
                            name: "priority",
                            children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_modules_application_logger__rspack_import_3.PrioritySelect, {}, void 0, false, {
                                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/log-tab/filter-sidebar/filter-sidebar.tsx",
                                lineNumber: 131,
                                columnNumber: 15
                            }, undefined)
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/log-tab/filter-sidebar/filter-sidebar.tsx",
                            lineNumber: 127,
                            columnNumber: 13
                        }, undefined),
                        /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                            label: t('application-logger.filter.message'),
                            name: "message",
                            children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Input, {
                                onChange: (e)=>{
                                    const value = e.target.value;
                                    setMessage(value === '' ? null : value);
                                },
                                value: message ?? undefined
                            }, void 0, false, {
                                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/log-tab/filter-sidebar/filter-sidebar.tsx",
                                lineNumber: 138,
                                columnNumber: 15
                            }, undefined)
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/log-tab/filter-sidebar/filter-sidebar.tsx",
                            lineNumber: 134,
                            columnNumber: 13
                        }, undefined),
                        /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                            label: t('application-logger.filter.related-object-id'),
                            name: "relatedObjectId",
                            children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Input, {
                                min: "0",
                                onChange: (e)=>{
                                    const value = e.target.value;
                                    setRelatedObjectId(value === '' ? null : Number.parseInt(value));
                                },
                                step: "1",
                                type: "number",
                                value: relatedObjectId ?? undefined
                            }, void 0, false, {
                                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/log-tab/filter-sidebar/filter-sidebar.tsx",
                                lineNumber: 151,
                                columnNumber: 15
                            }, undefined)
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/log-tab/filter-sidebar/filter-sidebar.tsx",
                            lineNumber: 147,
                            columnNumber: 13
                        }, undefined),
                        /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                            label: t('application-logger.filter.pid'),
                            name: "pid",
                            children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Input, {
                                min: "0",
                                onChange: (e)=>{
                                    const value = e.target.value;
                                    setPid(value === '' ? null : Number.parseInt(value));
                                },
                                step: "1",
                                type: "number",
                                value: pid ?? undefined
                            }, void 0, false, {
                                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/log-tab/filter-sidebar/filter-sidebar.tsx",
                                lineNumber: 167,
                                columnNumber: 15
                            }, undefined)
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/log-tab/filter-sidebar/filter-sidebar.tsx",
                            lineNumber: 163,
                            columnNumber: 13
                        }, undefined)
                    ]
                }, void 0, true, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/log-tab/filter-sidebar/filter-sidebar.tsx",
                    lineNumber: 88,
                    columnNumber: 11
                }, undefined)
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/log-tab/filter-sidebar/filter-sidebar.tsx",
                lineNumber: 84,
                columnNumber: 9
            }, undefined)
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/log-tab/filter-sidebar/filter-sidebar.tsx",
            lineNumber: 83,
            columnNumber: 7
        }, undefined)
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/log-tab/filter-sidebar/filter-sidebar.tsx",
        lineNumber: 60,
        columnNumber: 5
    }, undefined);
};
_s(FilterSidebar, "XfBc8MsdY7mlTjjo3x9RQsYydFE=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_1.useTranslation,
        _filter_sidebar_styles__rspack_import_5.useStyles,
        _pimcore_studio_ui_bundle_components__rspack_import_2.Form.useForm,
        _pimcore_studio_ui_bundle_modules_application_logger__rspack_import_3.useFilter
    ];
});
_c = FilterSidebar;
var _c;
$RefreshReg$(_c, "FilterSidebar");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/log-tab/hooks/use-refresh-interval/use-refresh-interval.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  useRefreshInterval: () => (useRefreshInterval)
});
/* import */ var lodash__rspack_import_0 = __webpack_require__("./node_modules/lodash/lodash.js");
/* import */ var lodash__rspack_import_0_default = /*#__PURE__*/__webpack_require__.n(lodash__rspack_import_0);
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* provided dependency */ var $ReactRefreshRuntime$ = __webpack_require__("./node_modules/@rspack/plugin-react-refresh/client/reactRefresh.js");
/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */ 

const useRefreshInterval = (onRefresh)=>{
    const [refreshInterval, setRefreshInterval] = (0,react__rspack_import_1.useState)(undefined);
    const stableOnRefresh = (0,react__rspack_import_1.useCallback)(onRefresh, [
        onRefresh
    ]);
    (0,react__rspack_import_1.useEffect)(()=>{
        if ((0,lodash__rspack_import_0.isNil)(refreshInterval)) {
            return;
        }
        const intervalMs = Number.parseInt(refreshInterval) * 1000;
        const intervalId = setInterval(()=>{
            stableOnRefresh();
        }, intervalMs);
        return ()=>{
            clearInterval(intervalId);
        };
    }, [
        refreshInterval,
        stableOnRefresh
    ]);
    return {
        refreshInterval,
        setRefreshInterval
    };
};

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/log-tab/index.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  DataHubLogTab: () => (/* reexport safe */ _log_tab__rspack_import_0.DataHubLogTab)
});
/* import */ var _log_tab__rspack_import_0 = __webpack_require__("./js/src/modules/log-tab/log-tab.tsx");
/* provided dependency */ var $ReactRefreshRuntime$ = __webpack_require__("./node_modules/@rspack/plugin-react-refresh/client/reactRefresh.js");
/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */ 

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/log-tab/log-tab.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  DataHubLogTab: () => (DataHubLogTab)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_1 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_api__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/api");
/* import */ var _pimcore_studio_ui_bundle_api__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_api__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_3);
/* import */ var _pimcore_studio_ui_bundle_modules_application_logger__rspack_import_4 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/modules/application-logger");
/* import */ var _pimcore_studio_ui_bundle_modules_application_logger__rspack_import_4_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_modules_application_logger__rspack_import_4);
/* import */ var lodash__rspack_import_5 = __webpack_require__("./node_modules/lodash/lodash.js");
/* import */ var lodash__rspack_import_5_default = /*#__PURE__*/__webpack_require__.n(lodash__rspack_import_5);
/* import */ var react__rspack_import_6 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_6_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_6);
/* import */ var _filter_sidebar_filter_sidebar__rspack_import_7 = __webpack_require__("./js/src/modules/log-tab/filter-sidebar/filter-sidebar.tsx");
/* import */ var _hooks_use_refresh_interval_use_refresh_interval__rspack_import_8 = __webpack_require__("./js/src/modules/log-tab/hooks/use-refresh-interval/use-refresh-interval.ts");
/* import */ var _sidebar_provider_sidebar_provider__rspack_import_9 = __webpack_require__("./js/src/modules/log-tab/sidebar-provider/sidebar-provider.tsx");
/* provided dependency */ var $ReactRefreshRuntime$ = __webpack_require__("./node_modules/@rspack/plugin-react-refresh/client/reactRefresh.js");

var _s = $RefreshSig$();
/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */ 








const DataHubLogTab = (props)=>{
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_modules_application_logger__rspack_import_4.FilterProvider, {
        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(DataHubLogTabInner, {
            ...props
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/log-tab/log-tab.tsx",
            lineNumber: 48,
            columnNumber: 7
        }, undefined)
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/log-tab/log-tab.tsx",
        lineNumber: 47,
        columnNumber: 5
    }, undefined);
};
_c = DataHubLogTab;
const DataHubLogTabInner = (props)=>{
    _s();
    const { componentPrefix, configName } = props;
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_1.useTranslation)();
    const sidebarEntries = [
        {
            key: 'filter',
            icon: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Icon, {
                options: {
                    width: '16px',
                    height: '16px'
                },
                value: "filter"
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/log-tab/log-tab.tsx",
                lineNumber: 61,
                columnNumber: 9
            }, undefined),
            component: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_filter_sidebar_filter_sidebar__rspack_import_7.FilterSidebar, {}, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/log-tab/log-tab.tsx",
                lineNumber: 66,
                columnNumber: 18
            }, undefined)
        }
    ];
    const dispatch = (0,_pimcore_studio_ui_bundle_app__rspack_import_1.useAppDispatch)();
    const [currentPage, setCurrentPage] = (0,react__rspack_import_6.useState)(1);
    const [pageSize, setPageSize] = (0,react__rspack_import_6.useState)(20);
    const { columnFilters, setIsLoading: setFilterLoading } = (0,_pimcore_studio_ui_bundle_modules_application_logger__rspack_import_4.useFilter)();
    // Always append the fixed component filter silently
    const mergedFilters = [
        ...columnFilters,
        {
            key: 'component',
            type: 'equals',
            filterValue: componentPrefix + configName
        }
    ];
    const { data, isFetching } = (0,_pimcore_studio_ui_bundle_modules_application_logger__rspack_import_4.useBundleApplicationLoggerGetCollectionQuery)({
        body: {
            filters: {
                page: currentPage,
                pageSize,
                columnFilters: mergedFilters
            }
        }
    });
    const total = (data === null || data === void 0 ? void 0 : data.totalItems) ?? 0;
    const onPagerChange = (page, newPageSize)=>{
        setCurrentPage(page);
        setPageSize(newPageSize);
    };
    const refreshData = (0,react__rspack_import_6.useCallback)(()=>{
        dispatch(_pimcore_studio_ui_bundle_modules_application_logger__rspack_import_4.api.util.invalidateTags(_pimcore_studio_ui_bundle_api__rspack_import_2.invalidatingTags.APPLICATION_LOGGER()));
    }, [
        dispatch
    ]);
    const { refreshInterval, setRefreshInterval } = (0,_hooks_use_refresh_interval_use_refresh_interval__rspack_import_8.useRefreshInterval)(refreshData);
    (0,react__rspack_import_6.useEffect)(()=>{
        setFilterLoading(isFetching);
    }, [
        isFetching
    ]);
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_sidebar_provider_sidebar_provider__rspack_import_9.SidebarProvider, {
        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.ContentLayout, {
            className: "h-full",
            renderSidebar: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Sidebar, {
                entries: sidebarEntries
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/log-tab/log-tab.tsx",
                lineNumber: 120,
                columnNumber: 25
            }, undefined),
            renderToolbar: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Toolbar, {
                justify: "space-between",
                theme: "secondary",
                children: [
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Flex, {
                        align: "center",
                        gap: 8,
                        children: [
                            !(0,lodash__rspack_import_5.isNil)(refreshInterval) && /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)("span", {
                                children: t('application-logger.refresh-interval')
                            }, void 0, false, {
                                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/log-tab/log-tab.tsx",
                                lineNumber: 131,
                                columnNumber: 17
                            }, undefined),
                            /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.CreatableSelect, {
                                allowClear: true,
                                inputType: "number",
                                minWidth: 200,
                                numberInputProps: {
                                    min: 1
                                },
                                onChange: setRefreshInterval,
                                onCreateOption: (value)=>{
                                    return {
                                        value,
                                        label: t('application-logger.refresh-interval.seconds', {
                                            seconds: value
                                        })
                                    };
                                },
                                options: [
                                    {
                                        value: '3',
                                        label: t('application-logger.refresh-interval.seconds', {
                                            seconds: 3
                                        })
                                    },
                                    {
                                        value: '5',
                                        label: t('application-logger.refresh-interval.seconds', {
                                            seconds: 5
                                        })
                                    },
                                    {
                                        value: '10',
                                        label: t('application-logger.refresh-interval.seconds', {
                                            seconds: 10
                                        })
                                    },
                                    {
                                        value: '30',
                                        label: t('application-logger.refresh-interval.seconds', {
                                            seconds: 30
                                        })
                                    },
                                    {
                                        value: '60',
                                        label: t('application-logger.refresh-interval.seconds', {
                                            seconds: 60
                                        })
                                    }
                                ],
                                placeholder: t('application-logger.refresh-interval.select'),
                                validate: (value)=>!Number.isNaN(Number.parseInt(value)) && Number.parseInt(value) > 0,
                                value: refreshInterval
                            }, void 0, false, {
                                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/log-tab/log-tab.tsx",
                                lineNumber: 133,
                                columnNumber: 15
                            }, undefined)
                        ]
                    }, void 0, true, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/log-tab/log-tab.tsx",
                        lineNumber: 126,
                        columnNumber: 13
                    }, undefined),
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Flex, {
                        children: [
                            /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.IconButton, {
                                disabled: isFetching,
                                icon: {
                                    value: 'refresh'
                                },
                                onClick: refreshData
                            }, void 0, false, {
                                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/log-tab/log-tab.tsx",
                                lineNumber: 161,
                                columnNumber: 15
                            }, undefined),
                            total > 0 && /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(react_jsx_dev_runtime__rspack_import_0.Fragment, {
                                children: [
                                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Divider, {
                                        size: "small",
                                        type: "vertical"
                                    }, void 0, false, {
                                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/log-tab/log-tab.tsx",
                                        lineNumber: 168,
                                        columnNumber: 19
                                    }, undefined),
                                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Pagination, {
                                        current: currentPage,
                                        defaultPageSize: pageSize,
                                        onChange: onPagerChange,
                                        showSizeChanger: true,
                                        showTotal: (total)=>t('pagination.show-total', {
                                                total
                                            }),
                                        total: total
                                    }, void 0, false, {
                                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/log-tab/log-tab.tsx",
                                        lineNumber: 172,
                                        columnNumber: 19
                                    }, undefined)
                                ]
                            }, void 0, true)
                        ]
                    }, void 0, true, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/log-tab/log-tab.tsx",
                        lineNumber: 160,
                        columnNumber: 13
                    }, undefined)
                ]
            }, void 0, true, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/log-tab/log-tab.tsx",
                lineNumber: 122,
                columnNumber: 11
            }, undefined),
            children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Content, {
                loading: isFetching,
                padded: true,
                children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_modules_application_logger__rspack_import_4.ApplicationLoggerTable, {
                    items: (data === null || data === void 0 ? void 0 : data.items) ?? []
                }, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/log-tab/log-tab.tsx",
                    lineNumber: 190,
                    columnNumber: 11
                }, undefined)
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/log-tab/log-tab.tsx",
                lineNumber: 186,
                columnNumber: 9
            }, undefined)
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/log-tab/log-tab.tsx",
            lineNumber: 118,
            columnNumber: 7
        }, undefined)
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/log-tab/log-tab.tsx",
        lineNumber: 117,
        columnNumber: 5
    }, undefined);
};
_s(DataHubLogTabInner, "w32vuKC8wykMA0Q8Mqy0kYNGMg0=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_1.useTranslation,
        _pimcore_studio_ui_bundle_app__rspack_import_1.useAppDispatch,
        _pimcore_studio_ui_bundle_modules_application_logger__rspack_import_4.useFilter,
        _pimcore_studio_ui_bundle_modules_application_logger__rspack_import_4.useBundleApplicationLoggerGetCollectionQuery,
        _hooks_use_refresh_interval_use_refresh_interval__rspack_import_8.useRefreshInterval
    ];
});
_c1 = DataHubLogTabInner;
var _c, _c1;
$RefreshReg$(_c, "DataHubLogTab");
$RefreshReg$(_c1, "DataHubLogTabInner");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/log-tab/sidebar-provider/sidebar-provider.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  SidebarProvider: () => (SidebarProvider)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_1 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_1);
/* import */ var react__rspack_import_2 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_2);
/* provided dependency */ var $ReactRefreshRuntime$ = __webpack_require__("./node_modules/@rspack/plugin-react-refresh/client/reactRefresh.js");

var _s = $RefreshSig$();
/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */ 

/**
 * Minimal SidebarContext provider that opens the 'filter' tab by default.
 * SidebarProvider from the SDK is not exported, so this is an inline workaround.
 */ const SidebarProvider = (param)=>{
    let { children } = param;
    _s();
    const [activeTab, setActiveTab] = (0,react__rspack_import_2.useState)('filter');
    const contextValue = (0,react__rspack_import_2.useMemo)(()=>({
            entries: [],
            buttons: [],
            sizing: 'default',
            highlights: [],
            activeTab,
            setEntries: ()=>{},
            setButtons: ()=>{},
            setSizing: ()=>{},
            setHighlights: ()=>{},
            setActiveTab,
            addEntry: ()=>{},
            removeEntry: ()=>{},
            addButton: ()=>{},
            removeButton: ()=>{},
            toggleHighlight: ()=>{},
            openTab: (key)=>{
                setActiveTab(key);
            },
            closeTab: ()=>{
                setActiveTab('');
            },
            toggleTab: (key)=>{
                setActiveTab((prev)=>prev === key ? '' : key);
            }
        }), [
        activeTab
    ]);
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_1.SidebarContext.Provider, {
        value: contextValue,
        children: children
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/log-tab/sidebar-provider/sidebar-provider.tsx",
        lineNumber: 52,
        columnNumber: 5
    }, undefined);
};
_s(SidebarProvider, "3Cbfl7HSxU8F+6bdyzta4Yh8odw=");
_c = SidebarProvider;
var _c;
$RefreshReg$(_c, "SidebarProvider");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/sdk/index.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  ADVANCED_COLUMN_KEY: () => (/* reexport safe */ _modules_config_components_column_config_modal__rspack_import_9.ADVANCED_COLUMN_KEY),
  BaseDetailView: () => (/* reexport safe */ _components_base_detail_view__rspack_import_6.BaseDetailView),
  ColumnConfigModal: () => (/* reexport safe */ _modules_config_components_column_config_modal__rspack_import_9.ColumnConfigModal),
  ColumnEditorItemBody: () => (/* reexport safe */ _modules_config_components_column_config_modal__rspack_import_9.ColumnEditorItemBody),
  ColumnPipelineForm: () => (/* reexport safe */ _modules_config_components_column_config_modal__rspack_import_9.ColumnPipelineForm),
  ColumnPreview: () => (/* reexport safe */ _modules_config_components_column_config_modal__rspack_import_9.ColumnPreview),
  ConfigContainer: () => (/* reexport safe */ _modules_config_config_container__rspack_import_0.ConfigContainer),
  ConfigToolbar: () => (/* reexport safe */ _components_base_detail_view__rspack_import_6.ConfigToolbar),
  DataHubLogTab: () => (/* reexport safe */ _modules_log_tab__rspack_import_10.DataHubLogTab),
  DynamicTypeDataHubAdapterAbstract: () => (/* reexport safe */ _modules_config_dynamic_types_dynamic_type_data_hub_adapter_abstract__rspack_import_4.DynamicTypeDataHubAdapterAbstract),
  DynamicTypeDataHubAdapterRegistry: () => (/* reexport safe */ _modules_config_dynamic_types_dynamic_type_data_hub_adapter_registry__rspack_import_5.DynamicTypeDataHubAdapterRegistry),
  GeneralTab: () => (/* reexport safe */ _modules_graphql_components_tabs_general_tab__rspack_import_1.GeneralTab),
  MigrationModal: () => (/* reexport safe */ _modules_config_components_migration_modal__rspack_import_8.MigrationModal),
  PermissionsTab: () => (/* reexport safe */ _modules_graphql_components_tabs_permissions_tab__rspack_import_2.PermissionsTab),
  advancedFromSchemaColumn: () => (/* reexport safe */ _modules_config_components_column_config_modal__rspack_import_9.advancedFromSchemaColumn),
  advancedToSchemaColumn: () => (/* reexport safe */ _modules_config_components_column_config_modal__rspack_import_9.advancedToSchemaColumn),
  bundleServiceIds: () => (/* reexport safe */ _config_service_ids__rspack_import_3.bundleServiceIds),
  isValidYaml: () => (/* reexport safe */ _utils_yaml__rspack_import_7.isValidYaml),
  parseYaml: () => (/* reexport safe */ _utils_yaml__rspack_import_7.parseYaml),
  stringifyYaml: () => (/* reexport safe */ _utils_yaml__rspack_import_7.stringifyYaml),
  useAddColumnDropdown: () => (/* reexport safe */ _modules_config_components_column_config_modal__rspack_import_9.useAddColumnDropdown),
  useDetailView: () => (/* reexport safe */ _components_base_detail_view__rspack_import_6.useDetailView)
});
/* import */ var _modules_config_config_container__rspack_import_0 = __webpack_require__("./js/src/modules/config/config-container.tsx");
/* import */ var _modules_graphql_components_tabs_general_tab__rspack_import_1 = __webpack_require__("./js/src/modules/graphql/components/tabs/general-tab.tsx");
/* import */ var _modules_graphql_components_tabs_permissions_tab__rspack_import_2 = __webpack_require__("./js/src/modules/graphql/components/tabs/permissions-tab.tsx");
/* import */ var _config_service_ids__rspack_import_3 = __webpack_require__("./js/src/config/service-ids.ts");
/* import */ var _modules_config_dynamic_types_dynamic_type_data_hub_adapter_abstract__rspack_import_4 = __webpack_require__("./js/src/modules/config/dynamic-types/dynamic-type-data-hub-adapter-abstract.tsx");
/* import */ var _modules_config_dynamic_types_dynamic_type_data_hub_adapter_registry__rspack_import_5 = __webpack_require__("./js/src/modules/config/dynamic-types/dynamic-type-data-hub-adapter-registry.ts");
/* import */ var _components_base_detail_view__rspack_import_6 = __webpack_require__("./js/src/components/base-detail-view/index.ts");
/* import */ var _utils_yaml__rspack_import_7 = __webpack_require__("./js/src/sdk/utils/yaml.ts");
/* import */ var _modules_config_components_migration_modal__rspack_import_8 = __webpack_require__("./js/src/modules/config/components/migration-modal/index.ts");
/* import */ var _modules_config_components_column_config_modal__rspack_import_9 = __webpack_require__("./js/src/modules/config/components/column-config-modal/index.ts");
/* import */ var _modules_log_tab__rspack_import_10 = __webpack_require__("./js/src/modules/log-tab/index.ts");
/* provided dependency */ var $ReactRefreshRuntime$ = __webpack_require__("./node_modules/@rspack/plugin-react-refresh/client/reactRefresh.js");
/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */ 











function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/sdk/utils/yaml.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  isValidYaml: () => (isValidYaml),
  parseYaml: () => (parseYaml),
  stringifyYaml: () => (stringifyYaml)
});
/* import */ var yaml__rspack_import_0 = __webpack_require__("webpack/sharing/consume/default/yaml/yaml");
/* import */ var yaml__rspack_import_0_default = /*#__PURE__*/__webpack_require__.n(yaml__rspack_import_0);
/* provided dependency */ var $ReactRefreshRuntime$ = __webpack_require__("./node_modules/@rspack/plugin-react-refresh/client/reactRefresh.js");
/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */ 
/**
 * Parse a YAML string into a JavaScript value.
 *
 * @throws {YAMLParseError} if the input is not valid YAML
 */ const parseYaml = (str)=>{
    return (0,yaml__rspack_import_0.parse)(str);
};
/**
 * Stringify a JavaScript value as a YAML document.
 *
 * Defaults to an indent of 2 spaces.
 */ const stringifyYaml = (value, options)=>{
    return (0,yaml__rspack_import_0.stringify)(value, {
        indent: 2,
        ...options
    });
};
/**
 * Returns true if the given string is valid YAML, false otherwise.
 *
 * Never throws.
 */ const isValidYaml = (str)=>{
    try {
        (0,yaml__rspack_import_0.parse)(str);
        return true;
    } catch  {
        return false;
    }
};

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},

}]);
//# sourceMappingURL=__federation_expose_default_export.js.map