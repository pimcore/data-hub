"use strict";
(self["chunk_pimcore_datahub_bundle "] = self["chunk_pimcore_datahub_bundle "] || []).push([["__federation_expose_default_export"], {
"./js/src/modules/config/components/column-config-modal/base-column-editor.styles.ts"(module, __webpack_exports__, __webpack_require__) {
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
    let { token, css } = param;
    return {
        body: css`
    display: flex;
    gap: ${token.marginSM}px;
    height: 100%;
  `,
        fieldsPanel: css`
    display: flex;
    flex-direction: column;
    gap: ${token.marginXS}px;
    width: 280px;
    height: 100%;
    padding-right: ${token.paddingSM}px;
    border-right: 1px solid ${token.colorBorderSecondary};
  `,
        list: css`
    flex: 1;
    min-width: 0;
    height: 100%;
    overflow-y: auto;
  `
    };
});

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/config/components/column-config-modal/base-column-editor.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  BaseColumnEditor: () => (BaseColumnEditor)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var antd__rspack_import_13 = __webpack_require__("./node_modules/antd/es/tag/index.js");
/* import */ var antd__rspack_import_14 = __webpack_require__("./node_modules/antd/es/empty/index.js");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_3);
/* import */ var _base_column_editor_styles__rspack_import_4 = __webpack_require__("./js/src/modules/config/components/column-config-modal/base-column-editor.styles.ts");
/* import */ var _fields_to_add_panel__rspack_import_5 = __webpack_require__("./js/src/modules/config/components/column-config-modal/fields-to-add-panel.tsx");
/* import */ var _pimcore_studio_ui_bundle_modules_data_object__rspack_import_6 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/modules/data-object");
/* import */ var _pimcore_studio_ui_bundle_modules_data_object__rspack_import_6_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_modules_data_object__rspack_import_6);
/* import */ var _pimcore_studio_ui_bundle_modules_auth__rspack_import_7 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/modules/auth");
/* import */ var _pimcore_studio_ui_bundle_modules_auth__rspack_import_7_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_modules_auth__rspack_import_7);
/* import */ var lodash__rspack_import_8 = __webpack_require__("./node_modules/lodash/lodash.js");
/* import */ var lodash__rspack_import_8_default = /*#__PURE__*/__webpack_require__.n(lodash__rspack_import_8);
/* import */ var _column_editor_item__rspack_import_9 = __webpack_require__("./js/src/modules/config/components/column-config-modal/column-editor-item.tsx");
/* import */ var _column_locale_control__rspack_import_10 = __webpack_require__("./js/src/modules/config/components/column-config-modal/column-locale-control.tsx");
/* import */ var _use_column_editor_state__rspack_import_11 = __webpack_require__("./js/src/modules/config/components/column-config-modal/use-column-editor-state.ts");
/* import */ var _types__rspack_import_12 = __webpack_require__("./js/src/modules/config/components/column-config-modal/types.ts");
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












const getColumnLabel = (col)=>{
    if (col.key === '') return '';
    return col.key;
};
const BaseColumnEditor = /*#__PURE__*/ _s((0,react__rspack_import_1.forwardRef)(_c = _s(function BaseColumnEditor(param, ref) {
    let { entity, classDefinitionId, columns, onApply, onCancel, hideToolbar = false, sourceFieldsRegistryId, transformersRegistryId, language, onLanguageChange, exportableOnly = false } = param;
    var _user_contentLanguages;
    _s();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_2.useTranslation)();
    const { styles } = (0,_base_column_editor_styles__rspack_import_4.useStyles)();
    const user = (0,_pimcore_studio_ui_bundle_modules_auth__rspack_import_7.useUser)();
    const [fieldsToAddOpen, setFieldsToAddOpen] = (0,react__rspack_import_1.useState)(true);
    // Own the language state here. Initialize once from the prop, falling back to
    // the user's first content language. This is the single source of truth —
    // no synchronization effects needed.
    const initialLanguage = language ?? ((_user_contentLanguages = user.contentLanguages) === null || _user_contentLanguages === void 0 ? void 0 : _user_contentLanguages[0]) ?? 'en';
    const [currentLanguage, setCurrentLanguage] = (0,react__rspack_import_1.useState)(initialLanguage);
    const [hasLocalizedFields, setHasLocalizedFields] = (0,react__rspack_import_1.useState)(false);
    // On mount, persist the resolved initial language to the parent so that
    // entities that have never had a language set get one saved immediately.
    (0,react__rspack_import_1.useEffect)(()=>{
        onLanguageChange === null || onLanguageChange === void 0 ? void 0 : onLanguageChange(currentLanguage);
    }, []);
    const handleLanguageChange = (0,react__rspack_import_1.useCallback)((lang)=>{
        setCurrentLanguage(lang);
        onLanguageChange === null || onLanguageChange === void 0 ? void 0 : onLanguageChange(lang);
    }, [
        onLanguageChange
    ]);
    // Build a stable context value to pass to the provider.
    const languageContextValue = (0,react__rspack_import_1.useMemo)(()=>({
            currentLanguage,
            setCurrentLanguage: handleLanguageChange,
            hasLocalizedFields,
            setHasLocalizedFields
        }), [
        currentLanguage,
        handleLanguageChange,
        hasLocalizedFields
    ]);
    const { draft, isLoading, objectId, columnGroups, onAddAdvancedColumn, openElementSelector, handleAddColumnOfType, handlePipelineChange, handleRemove, handleApply, handleDiscard, handleLocaleChange, handleReorder, getColumns } = (0,_use_column_editor_state__rspack_import_11.useColumnEditorState)({
        entity,
        classDefinitionId,
        columns,
        onApply,
        onCancel,
        exportableOnly
    });
    (0,react__rspack_import_1.useImperativeHandle)(ref, ()=>({
            getColumns,
            addColumn: handleAddColumnOfType
        }), [
        getColumns,
        handleAddColumnOfType
    ]);
    const stackItems = draft.map((col)=>{
        var _col_pipeline, _col_pipeline1;
        const isAdvanced = col.type === _types__rspack_import_12.ADVANCED_COLUMN_TYPE;
        const label = getColumnLabel(col);
        return {
            id: col._id,
            sortable: true,
            type: isAdvanced ? 'collapse' : 'default',
            defaultActive: isAdvanced && col.isNew === true,
            children: isAdvanced ? /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(antd__rspack_import_13["default"], {
                color: "purple",
                children: !(0,lodash__rspack_import_8.isNil)((_col_pipeline = col.pipeline) === null || _col_pipeline === void 0 ? void 0 : _col_pipeline.title) ? String((_col_pipeline1 = col.pipeline) === null || _col_pipeline1 === void 0 ? void 0 : _col_pipeline1.title) : label
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/base-column-editor.tsx",
                lineNumber: 147,
                columnNumber: 13
            }, this) : /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(antd__rspack_import_13["default"], {
                children: label
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/base-column-editor.tsx",
                lineNumber: 148,
                columnNumber: 13
            }, this),
            ...isAdvanced ? {
                body: col.pipelineConfig !== undefined ? /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_column_editor_item__rspack_import_9.ColumnEditorItemBody, {
                    column: col,
                    entity: entity,
                    objectId: objectId,
                    onPipelineChange: handlePipelineChange,
                    sourceFieldsRegistryId: sourceFieldsRegistryId,
                    transformersRegistryId: transformersRegistryId
                }, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/base-column-editor.tsx",
                    lineNumber: 153,
                    columnNumber: 19
                }, this) : /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Spin, {}, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/base-column-editor.tsx",
                    lineNumber: 162,
                    columnNumber: 19
                }, this)
            } : {},
            renderRightToolbar: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Space, {
                size: "mini",
                children: [
                    col.localizable === true && isAdvanced && /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_column_locale_control__rspack_import_10.ColumnLocaleControl, {
                        onChange: (locale)=>{
                            handleLocaleChange(col._id, locale);
                        },
                        value: col.locale
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/base-column-editor.tsx",
                        lineNumber: 168,
                        columnNumber: 15
                    }, this),
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.IconButton, {
                        icon: {
                            value: 'trash'
                        },
                        onClick: ()=>{
                            handleRemove(col._id);
                        },
                        theme: "secondary"
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/base-column-editor.tsx",
                        lineNumber: 173,
                        columnNumber: 13
                    }, this)
                ]
            }, void 0, true, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/base-column-editor.tsx",
                lineNumber: 166,
                columnNumber: 11
            }, this)
        };
    });
    if (isLoading) {
        return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Flex, {
            align: "center",
            justify: "center",
            style: {
                minHeight: 200
            },
            children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Spin, {}, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/base-column-editor.tsx",
                lineNumber: 190,
                columnNumber: 11
            }, this)
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/base-column-editor.tsx",
            lineNumber: 185,
            columnNumber: 9
        }, this);
    }
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_modules_data_object__rspack_import_6.LanguageSelectionContext.Provider, {
        value: languageContextValue,
        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.ContentLayout, {
            renderToolbar: hideToolbar ? undefined : /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Toolbar, {
                padding: {
                    x: 'none',
                    y: 'small'
                },
                theme: "secondary",
                children: [
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Flex, {
                        gap: "mini",
                        children: [
                            /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.IconTextButton, {
                                icon: {
                                    value: 'new'
                                },
                                onClick: ()=>{
                                    setFieldsToAddOpen((isOpen)=>!isOpen);
                                },
                                type: "default",
                                children: t('data-hub.column-config-modal.add-column')
                            }, void 0, false, {
                                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/base-column-editor.tsx",
                                lineNumber: 206,
                                columnNumber: 19
                            }, this),
                            onAddAdvancedColumn !== undefined && /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.IconTextButton, {
                                icon: {
                                    value: 'new'
                                },
                                onClick: onAddAdvancedColumn,
                                type: "default",
                                children: t('data-hub.column-config-modal.add-advanced-column')
                            }, void 0, false, {
                                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/base-column-editor.tsx",
                                lineNumber: 215,
                                columnNumber: 21
                            }, this)
                        ]
                    }, void 0, true, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/base-column-editor.tsx",
                        lineNumber: 205,
                        columnNumber: 17
                    }, this),
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Space, {
                        size: "extra-small",
                        children: [
                            /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Button, {
                                onClick: handleDiscard,
                                type: "default",
                                children: t('data-hub.column-config-modal.discard')
                            }, void 0, false, {
                                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/base-column-editor.tsx",
                                lineNumber: 226,
                                columnNumber: 19
                            }, this),
                            /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Button, {
                                onClick: handleApply,
                                type: "primary",
                                children: t('data-hub.column-config-modal.apply')
                            }, void 0, false, {
                                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/base-column-editor.tsx",
                                lineNumber: 233,
                                columnNumber: 19
                            }, this)
                        ]
                    }, void 0, true, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/base-column-editor.tsx",
                        lineNumber: 225,
                        columnNumber: 17
                    }, this)
                ]
            }, void 0, true, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/base-column-editor.tsx",
                lineNumber: 201,
                columnNumber: 15
            }, this),
            renderTopBar: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Toolbar, {
                align: "center",
                position: "content",
                theme: "secondary",
                children: [
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Button, {
                        onClick: openElementSelector,
                        children: t('data-hub.column-config-modal.preview.select-object')
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/base-column-editor.tsx",
                        lineNumber: 248,
                        columnNumber: 15
                    }, this),
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_modules_data_object__rspack_import_6.LanguageSelectionWithProvider, {}, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/base-column-editor.tsx",
                        lineNumber: 252,
                        columnNumber: 15
                    }, this)
                ]
            }, void 0, true, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/base-column-editor.tsx",
                lineNumber: 243,
                columnNumber: 13
            }, this),
            children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Content, {
                padded: true,
                padding: {
                    x: 'none',
                    y: 'small'
                },
                style: {
                    height: 'calc(80vh - 200px)'
                },
                children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)("div", {
                    className: styles.body,
                    children: [
                        !hideToolbar && fieldsToAddOpen && /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_fields_to_add_panel__rspack_import_5.FieldsToAddPanel, {
                            groups: columnGroups,
                            onClose: ()=>{
                                setFieldsToAddOpen(false);
                            },
                            onColumnSelect: handleAddColumnOfType
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/base-column-editor.tsx",
                            lineNumber: 263,
                            columnNumber: 17
                        }, this),
                        /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)("div", {
                            className: styles.list,
                            children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Space, {
                                direction: "vertical",
                                style: {
                                    width: '100%'
                                },
                                children: [
                                    draft.length === 0 && /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(antd__rspack_import_14["default"], {
                                        image: antd__rspack_import_14["default"].PRESENTED_IMAGE_SIMPLE
                                    }, void 0, false, {
                                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/base-column-editor.tsx",
                                        lineNumber: 276,
                                        columnNumber: 21
                                    }, this),
                                    draft.length > 0 && /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.StackList, {
                                        items: stackItems,
                                        onItemsChange: (items)=>{
                                            handleReorder(items.map((item)=>String(item.id)));
                                        },
                                        sortable: true
                                    }, void 0, false, {
                                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/base-column-editor.tsx",
                                        lineNumber: 280,
                                        columnNumber: 21
                                    }, this)
                                ]
                            }, void 0, true, {
                                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/base-column-editor.tsx",
                                lineNumber: 271,
                                columnNumber: 17
                            }, this)
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/base-column-editor.tsx",
                            lineNumber: 270,
                            columnNumber: 15
                        }, this)
                    ]
                }, void 0, true, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/base-column-editor.tsx",
                    lineNumber: 261,
                    columnNumber: 13
                }, this)
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/base-column-editor.tsx",
                lineNumber: 256,
                columnNumber: 11
            }, this)
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/base-column-editor.tsx",
            lineNumber: 197,
            columnNumber: 9
        }, this)
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/base-column-editor.tsx",
        lineNumber: 196,
        columnNumber: 7
    }, this);
}, "u4B4gEumk8L47M0AualEfe0mSIU=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_2.useTranslation,
        _base_column_editor_styles__rspack_import_4.useStyles,
        _pimcore_studio_ui_bundle_modules_auth__rspack_import_7.useUser,
        _use_column_editor_state__rspack_import_11.useColumnEditorState
    ];
})), "u4B4gEumk8L47M0AualEfe0mSIU=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_2.useTranslation,
        _base_column_editor_styles__rspack_import_4.useStyles,
        _pimcore_studio_ui_bundle_modules_auth__rspack_import_7.useUser,
        _use_column_editor_state__rspack_import_11.useColumnEditorState
    ];
});
_c1 = BaseColumnEditor;
var _c, _c1;
$RefreshReg$(_c, "BaseColumnEditor$forwardRef");
$RefreshReg$(_c1, "BaseColumnEditor");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
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
/* import */ var _use_add_column_groups__rspack_import_7 = __webpack_require__("./js/src/modules/config/components/column-config-modal/use-add-column-groups.ts");
/* import */ var _types__rspack_import_8 = __webpack_require__("./js/src/modules/config/components/column-config-modal/types.ts");
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
    let { entity, classDefinitionId, columns, columnConfig, open, title, onApply, onCancel, language, onLanguageChange, exportableOnly = false, renderEditor } = param;
    _s();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation)();
    const alertModal = (0,_pimcore_studio_ui_bundle_components__rspack_import_2.useAlertModal)();
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
    const [migratedColumns, setMigratedColumns] = (0,react__rspack_import_1.useState)([]);
    const [isMigrated, setIsMigrated] = (0,react__rspack_import_1.useState)(false);
    const columnEditorRef = (0,react__rspack_import_1.useRef)(null);
    const isLegacy = columnConfig !== undefined && !isMigrated;
    const { data: availableFieldsData } = _pimcore_studio_ui_bundle_api_data_object__rspack_import_4.api.endpoints.dataObjectGetAvailableGridColumns.useQuery({
        classId: resolvedClassId,
        folderId: 1
    }, {
        skip: !isLegacy
    });
    const allAvailableFields = (availableFieldsData === null || availableFieldsData === void 0 ? void 0 : availableFieldsData.columns) ?? [];
    const availableFields = exportableOnly ? allAvailableFields.filter((field)=>field.exportable === true) : allAvailableFields;
    const handleAddColumn = (0,react__rspack_import_1.useCallback)((column)=>{
        var _columnEditorRef_current;
        (_columnEditorRef_current = columnEditorRef.current) === null || _columnEditorRef_current === void 0 ? void 0 : _columnEditorRef_current.addColumn(column);
    }, []);
    const columnGroups = (0,_use_add_column_groups__rspack_import_7.useAddColumnGroups)(availableFields);
    const advancedColumn = availableFields.find((field)=>field.type === _types__rspack_import_8.ADVANCED_COLUMN_TYPE || field.key === _types__rspack_import_8.ADVANCED_COLUMN_KEY);
    const commitMigration = (cols)=>{
        setMigratedColumns(cols);
        setIsMigrated(true);
    };
    const handleConfirmMigration = ()=>{
        var _columnEditorRef_current;
        const cols = ((_columnEditorRef_current = columnEditorRef.current) === null || _columnEditorRef_current === void 0 ? void 0 : _columnEditorRef_current.getColumns()) ?? migratedColumns;
        if (cols.length === 0) {
            alertModal.warn({
                title: t('data-hub.migration-modal.confirm-empty-columns-title'),
                content: t('data-hub.migration-modal.confirm-empty-columns-content'),
                okText: t('data-hub.migration-modal.confirm-empty-columns-ok'),
                cancelText: t('data-hub.migration-modal.confirm-empty-columns-cancel'),
                okCancel: true,
                onOk: ()=>{
                    commitMigration(cols);
                }
            });
            return;
        }
        commitMigration(cols);
    };
    const handleCancel = ()=>{
        setIsMigrated(false);
        onCancel();
    };
    const modalTitle = /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.ModalTitle, {
        iconName: "settings",
        children: title
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-config-modal.tsx",
        lineNumber: 159,
        columnNumber: 5
    }, undefined);
    if (!isLegacy) {
        return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Modal, {
            footer: null,
            onCancel: handleCancel,
            open: open,
            size: "XL",
            title: modalTitle,
            children: renderEditor({
                ref: columnEditorRef,
                classDefinitionId: resolvedClassId,
                columns: isMigrated ? migratedColumns : columns,
                entity,
                hideToolbar: false,
                exportableOnly,
                language,
                onLanguageChange,
                onApply: (updatedColumns)=>{
                    onApply(updatedColumns);
                    handleCancel();
                },
                onCancel: handleCancel
            })
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-config-modal.tsx",
            lineNumber: 166,
            columnNumber: 7
        }, undefined);
    }
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_migration_modal__rspack_import_6.MigrationModal, {
        legacyConfig: columnConfig,
        onClose: handleCancel,
        onConfirm: handleConfirmMigration,
        open: open,
        renderToolbarLeft: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Flex, {
            gap: "mini",
            children: [
                /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.ColumnPickerPopover, {
                    groups: columnGroups,
                    onSelect: (item)=>{
                        if (item.meta !== undefined) {
                            handleAddColumn(item.meta);
                        }
                    },
                    placement: "leftBottom",
                    children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.IconTextButton, {
                        icon: {
                            value: 'new'
                        },
                        children: t('data-hub.column-config-modal.add-column')
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-config-modal.tsx",
                        lineNumber: 209,
                        columnNumber: 13
                    }, undefined)
                }, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-config-modal.tsx",
                    lineNumber: 200,
                    columnNumber: 11
                }, undefined),
                advancedColumn !== undefined && /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.IconTextButton, {
                    icon: {
                        value: 'new'
                    },
                    onClick: ()=>{
                        handleAddColumn(advancedColumn);
                    },
                    children: t('data-hub.column-config-modal.add-advanced-column')
                }, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-config-modal.tsx",
                    lineNumber: 215,
                    columnNumber: 13
                }, undefined)
            ]
        }, void 0, true, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-config-modal.tsx",
            lineNumber: 199,
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
                exportableOnly,
                language,
                onLanguageChange,
                onApply: (cols)=>{
                    setMigratedColumns(cols);
                },
                onCancel: ()=>{}
            })
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-config-modal.tsx",
            lineNumber: 226,
            columnNumber: 7
        }, undefined)
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-config-modal.tsx",
        lineNumber: 193,
        columnNumber: 5
    }, undefined);
};
_s(ColumnConfigModal, "s4y+nYUpHNR5JMoXEGZ+wQrBXF0=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation,
        _pimcore_studio_ui_bundle_components__rspack_import_2.useAlertModal,
        _pimcore_studio_ui_bundle_modules_data_object__rspack_import_5.useClassDefinitions,
        _use_add_column_groups__rspack_import_7.useAddColumnGroups
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
"./js/src/modules/config/components/column-config-modal/column-locale-control.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  ColumnLocaleControl: () => (ColumnLocaleControl)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_modules_auth__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/modules/auth");
/* import */ var _pimcore_studio_ui_bundle_modules_auth__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_modules_auth__rspack_import_3);
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


const ColumnLocaleControl = (param)=>{
    let { value, onChange } = param;
    _s();
    const user = (0,_pimcore_studio_ui_bundle_modules_auth__rspack_import_3.useUser)();
    const languages = [
        '-',
        ...Array.isArray(user.contentLanguages) ? user.contentLanguages : []
    ];
    const selected = value ?? '-';
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.LanguageSelection, {
        languages: languages,
        onSelectLanguage: (lang)=>{
            onChange(lang === '-' ? null : lang);
        },
        selectedLanguage: selected
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-locale-control.tsx",
        lineNumber: 29,
        columnNumber: 5
    }, undefined);
};
_s(ColumnLocaleControl, "BPnln+wUpxLjLAxQmw7xYz9C+QI=", false, function() {
    return [
        _pimcore_studio_ui_bundle_modules_auth__rspack_import_3.useUser
    ];
});
_c = ColumnLocaleControl;
var _c;
$RefreshReg$(_c, "ColumnLocaleControl");

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
/* import */ var _migration_modal__rspack_import_6 = __webpack_require__("./js/src/modules/config/components/migration-modal/index.ts");
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
    const { compact } = (0,_migration_modal__rspack_import_6.useCompactLayout)();
    const [liveValue, setLiveValue] = (0,react__rspack_import_1.useState)(value ?? {});
    const sourceFieldsGroup = /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Pipeline.DynamicGroupItem, {
        dynamicTypeRegistryId: sourceFieldsRegistryId,
        id: "sourceFields",
        showTitle: !compact,
        translationKeyPrefix: "data-hub.column-config-modal.pipeline"
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-pipeline-form.tsx",
        lineNumber: 48,
        columnNumber: 5
    }, undefined);
    const transformersGroup = /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Pipeline.DynamicGroupItem, {
        dynamicTypeRegistryId: transformersRegistryId,
        id: "transformers",
        showTitle: !compact,
        translationKeyPrefix: "data-hub.column-config-modal.pipeline"
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-pipeline-form.tsx",
        lineNumber: 57,
        columnNumber: 5
    }, undefined);
    const fieldsLayout = compact ? /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Tabs, {
        items: [
            {
                key: 'sourceFields',
                label: t('data-hub.column-config-modal.pipeline.sourceFields'),
                forceRender: true,
                children: sourceFieldsGroup
            },
            {
                key: 'transformers',
                label: t('data-hub.column-config-modal.pipeline.transformers'),
                forceRender: true,
                children: transformersGroup
            }
        ]
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-pipeline-form.tsx",
        lineNumber: 67,
        columnNumber: 7
    }, undefined) : /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.SplitLayout, {
        leftItem: {
            children: sourceFieldsGroup,
            size: 50
        },
        rightItem: {
            children: transformersGroup,
            size: 50
        },
        withDivider: true
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-pipeline-form.tsx",
        lineNumber: 85,
        columnNumber: 7
    }, undefined);
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
                            id: 'title',
                            component: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Pipeline.CustomItem, {
                                children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Box, {
                                    padding: {
                                        top: 'mini',
                                        bottom: 'mini',
                                        x: 'none'
                                    },
                                    children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Form.Item, {
                                        name: "title",
                                        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Input, {
                                            placeholder: t('data-hub.column-config-modal.pipeline.title'),
                                            style: {
                                                maxWidth: '100%'
                                            }
                                        }, void 0, false, {
                                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-pipeline-form.tsx",
                                            lineNumber: 127,
                                            columnNumber: 25
                                        }, undefined)
                                    }, void 0, false, {
                                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-pipeline-form.tsx",
                                        lineNumber: 126,
                                        columnNumber: 23
                                    }, undefined)
                                }, void 0, false, {
                                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-pipeline-form.tsx",
                                    lineNumber: 125,
                                    columnNumber: 21
                                }, undefined)
                            }, void 0, false, {
                                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-pipeline-form.tsx",
                                lineNumber: 124,
                                columnNumber: 19
                            }, undefined)
                        },
                        {
                            id: 'fields',
                            component: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Pipeline.CustomItem, {
                                children: fieldsLayout
                            }, void 0, false, {
                                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-pipeline-form.tsx",
                                lineNumber: 139,
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
                                    lineNumber: 149,
                                    columnNumber: 23
                                }, undefined)
                            }, void 0, false, {
                                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-pipeline-form.tsx",
                                lineNumber: 147,
                                columnNumber: 19
                            }, undefined)
                        }
                    ],
                    value: value ?? {}
                }, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-pipeline-form.tsx",
                    lineNumber: 119,
                    columnNumber: 11
                }, undefined)
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-pipeline-form.tsx",
                lineNumber: 118,
                columnNumber: 9
            }, undefined)
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-pipeline-form.tsx",
            lineNumber: 117,
            columnNumber: 7
        }, undefined)
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-pipeline-form.tsx",
        lineNumber: 111,
        columnNumber: 5
    }, undefined);
};
_s(ColumnPipelineForm, "65fCs5Lp2w21JJczfZp4WAbT0jI=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_2.useTranslation,
        _pimcore_studio_ui_bundle_components__rspack_import_3.Form.useForm,
        _migration_modal__rspack_import_6.useCompactLayout
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
        const safeKey = `${item.type.replace(/\./g, '_')}-${index}`;
        return columnHelper.accessor(safeKey, {
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
        const safeKey = `${item.type.replace(/\./g, '_')}-${index}`;
        row[safeKey] = item.value;
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
            lineNumber: 62,
            columnNumber: 7
        }, undefined)
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-preview.tsx",
        lineNumber: 61,
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
    var _lastData_current;
    _s1();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_2.useTranslation)();
    const { currentLanguage } = (0,_pimcore_studio_ui_bundle_modules_data_object__rspack_import_4.useLanguageSelection)();
    const pipeline = pipelineValue !== undefined && Object.keys(pipelineValue).length > 0 ? pipelineValue : column.pipeline;
    // Resolve locale: explicit per-column override > global language (only for localizable columns)
    const resolvedLocale = column.localizable === true ? column.locale ?? currentLanguage : undefined;
    const { data, error, isFetching } = _pimcore_studio_ui_bundle_api_data_object__rspack_import_5.api.endpoints.dataObjectGetGridPreview.useQuery({
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
    // Keep the last successful data so re-fetches don't flash "no data"
    const lastData = (0,react__rspack_import_1.useRef)(data);
    if (data !== undefined) lastData.current = data;
    if (error !== undefined) {
        const message = 'error' in error ? error.error : t('data-hub.column-config-modal.preview.error');
        return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Text, {
            type: "danger",
            children: message
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-preview.tsx",
            lineNumber: 105,
            columnNumber: 12
        }, undefined);
    }
    if (isFetching && lastData.current === undefined) {
        return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Text, {
            type: "secondary",
            children: t('data-hub.column-config-modal.preview.loading')
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-preview.tsx",
            lineNumber: 109,
            columnNumber: 12
        }, undefined);
    }
    const value = (_lastData_current = lastData.current) === null || _lastData_current === void 0 ? void 0 : _lastData_current.value;
    if (value === undefined || value === null || !Array.isArray(value) || value.length === 0) {
        return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Text, {
            type: "secondary",
            children: t('data-hub.column-config-modal.preview.no-data')
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-preview.tsx",
            lineNumber: 115,
            columnNumber: 12
        }, undefined);
    }
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(PreviewGrid, {
        value: value
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-preview.tsx",
        lineNumber: 118,
        columnNumber: 10
    }, undefined);
};
_s1(PreviewResult, "lnjvhcrKuNomQU4VYdkSXoylW3k=", false, function() {
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
    // Debounce pipeline changes so the previous result stays visible during edits
    const [debouncedPipelineValue, setDebouncedPipelineValue] = (0,react__rspack_import_1.useState)(pipelineValue);
    (0,react__rspack_import_1.useEffect)(()=>{
        const timer = setTimeout(()=>{
            setDebouncedPipelineValue(pipelineValue);
        }, 300);
        return ()=>{
            clearTimeout(timer);
        };
    }, [
        pipelineValue
    ]);
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Box, {
        padding: {
            top: 'small',
            bottom: 'none',
            x: 'small'
        },
        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Flex, {
            align: "center",
            gap: "small",
            children: [
                /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Text, {
                    style: {
                        wordBreak: 'keep-all'
                    },
                    children: [
                        t('grid.advanced-column.preview'),
                        ":"
                    ]
                }, void 0, true, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-preview.tsx",
                    lineNumber: 137,
                    columnNumber: 9
                }, undefined),
                objectId === null ? /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Text, {
                    type: "secondary",
                    children: t('data-hub.column-config-modal.preview.placeholder')
                }, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-preview.tsx",
                    lineNumber: 140,
                    columnNumber: 13
                }, undefined) : /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(PreviewResult, {
                    column: column,
                    objectId: objectId,
                    pipelineValue: debouncedPipelineValue
                }, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-preview.tsx",
                    lineNumber: 145,
                    columnNumber: 13
                }, undefined)
            ]
        }, void 0, true, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-preview.tsx",
            lineNumber: 133,
            columnNumber: 7
        }, undefined)
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/column-preview.tsx",
        lineNumber: 132,
        columnNumber: 5
    }, undefined);
};
_s2(ColumnPreview, "HuemuXamUgisuk7o691X0FCNzAs=", false, function() {
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
"./js/src/modules/config/components/column-config-modal/fields-to-add-panel.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  FieldsToAddPanel: () => (FieldsToAddPanel)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_3);
/* import */ var _base_column_editor_styles__rspack_import_4 = __webpack_require__("./js/src/modules/config/components/column-config-modal/base-column-editor.styles.ts");
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
 * Embedded "Fields to add" panel shown inside the migrated column editor: the
 * searchable ColumnPicker tree with a collapsible header.
 */ const FieldsToAddPanel = (param)=>{
    let { groups, onColumnSelect, onClose } = param;
    _s();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation)();
    const { styles } = (0,_base_column_editor_styles__rspack_import_4.useStyles)();
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)("div", {
        className: styles.fieldsPanel,
        children: [
            /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Header, {
                fullWidth: true,
                title: t('data-hub.column-config-modal.fields-to-add'),
                children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Flex, {
                    className: "w-full",
                    justify: "flex-end",
                    children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.IconButton, {
                        icon: {
                            value: 'collapse-sidebar',
                            colorToken: 'colorPrimary'
                        },
                        onClick: onClose
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/fields-to-add-panel.tsx",
                        lineNumber: 41,
                        columnNumber: 11
                    }, undefined)
                }, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/fields-to-add-panel.tsx",
                    lineNumber: 37,
                    columnNumber: 9
                }, undefined)
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/fields-to-add-panel.tsx",
                lineNumber: 33,
                columnNumber: 7
            }, undefined),
            /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.ColumnPicker, {
                fillHeight: true,
                groups: groups,
                onSelect: (item)=>{
                    if (item.meta !== undefined) {
                        onColumnSelect(item.meta);
                    }
                }
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/fields-to-add-panel.tsx",
                lineNumber: 48,
                columnNumber: 7
            }, undefined)
        ]
    }, void 0, true, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/column-config-modal/fields-to-add-panel.tsx",
        lineNumber: 32,
        columnNumber: 5
    }, undefined);
};
_s(FieldsToAddPanel, "RLrtxoYLhU6K3pUxNPOpN7Ah0UQ=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation,
        _base_column_editor_styles__rspack_import_4.useStyles
    ];
});
_c = FieldsToAddPanel;
var _c;
$RefreshReg$(_c, "FieldsToAddPanel");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/config/components/column-config-modal/index.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  ADVANCED_COLUMN_KEY: () => (/* reexport safe */ _types__rspack_import_2.ADVANCED_COLUMN_KEY),
  ADVANCED_COLUMN_TYPE: () => (/* reexport safe */ _types__rspack_import_2.ADVANCED_COLUMN_TYPE),
  BaseColumnEditor: () => (/* reexport safe */ _base_column_editor__rspack_import_7.BaseColumnEditor),
  ColumnConfigModal: () => (/* reexport safe */ _column_config_modal__rspack_import_0.ColumnConfigModal),
  ColumnEditorItemBody: () => (/* reexport safe */ _column_editor_item__rspack_import_5.ColumnEditorItemBody),
  ColumnLocaleControl: () => (/* reexport safe */ _column_locale_control__rspack_import_6.ColumnLocaleControl),
  ColumnPipelineForm: () => (/* reexport safe */ _column_pipeline_form__rspack_import_4.ColumnPipelineForm),
  ColumnPreview: () => (/* reexport safe */ _column_preview__rspack_import_3.ColumnPreview),
  advancedFromSchemaColumn: () => (/* reexport safe */ _types__rspack_import_2.advancedFromSchemaColumn),
  advancedToSchemaColumn: () => (/* reexport safe */ _types__rspack_import_2.advancedToSchemaColumn),
  useAddColumnGroups: () => (/* reexport safe */ _use_add_column_groups__rspack_import_1.useAddColumnGroups)
});
/* import */ var _column_config_modal__rspack_import_0 = __webpack_require__("./js/src/modules/config/components/column-config-modal/column-config-modal.tsx");
/* import */ var _use_add_column_groups__rspack_import_1 = __webpack_require__("./js/src/modules/config/components/column-config-modal/use-add-column-groups.ts");
/* import */ var _types__rspack_import_2 = __webpack_require__("./js/src/modules/config/components/column-config-modal/types.ts");
/* import */ var _column_preview__rspack_import_3 = __webpack_require__("./js/src/modules/config/components/column-config-modal/column-preview.tsx");
/* import */ var _column_pipeline_form__rspack_import_4 = __webpack_require__("./js/src/modules/config/components/column-config-modal/column-pipeline-form.tsx");
/* import */ var _column_editor_item__rspack_import_5 = __webpack_require__("./js/src/modules/config/components/column-config-modal/column-editor-item.tsx");
/* import */ var _column_locale_control__rspack_import_6 = __webpack_require__("./js/src/modules/config/components/column-config-modal/column-locale-control.tsx");
/* import */ var _base_column_editor__rspack_import_7 = __webpack_require__("./js/src/modules/config/components/column-config-modal/base-column-editor.tsx");
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
  ADVANCED_COLUMN_TYPE: () => (ADVANCED_COLUMN_TYPE),
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
 * The column type registered by the backend for advanced/pipeline columns.
 * Used as the reliable discriminator when loading persisted columns, because the key
 * is now set to the user-defined title rather than the hardcoded sentinel 'advanced'.
 */ const ADVANCED_COLUMN_TYPE = 'dataobject.advanced';
/**
 * Converts a persisted SchemaColumn into an AdvancedEditorColumn draft.
 * For advanced columns (type === ADVANCED_COLUMN_TYPE) the persisted config is
 * unpacked into the frontend `pipeline` working state, and the column key
 * (which holds the user title) is mapped to pipeline.title.
 */ const advancedFromSchemaColumn = (col)=>{
    var _col_config, _col_config1;
    return {
        _id: crypto.randomUUID(),
        key: col.key,
        fieldtype: col.fieldtype,
        type: col.type,
        config: col.config,
        pipeline: col.type === ADVANCED_COLUMN_TYPE ? {
            // New format: key holds the title. Legacy format: key === 'advanced', title was a separate field.
            title: col.key !== ADVANCED_COLUMN_KEY ? col.key : col.title,
            sourceFields: (((_col_config = col.config) === null || _col_config === void 0 ? void 0 : _col_config.advancedColumns) ?? []).map((sf)=>({
                    ...sf,
                    config: sf.config ?? {}
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
        key: col.type === ADVANCED_COLUMN_TYPE && ((_col_pipeline = col.pipeline) === null || _col_pipeline === void 0 ? void 0 : _col_pipeline.title) !== undefined && col.pipeline.title !== '' ? col.pipeline.title : col.key,
        fieldtype: col.fieldtype,
        type: col.type,
        config: col.pipeline !== undefined ? {
            advancedColumns: (col.pipeline.sourceFields ?? []).map((sf)=>({
                    ...sf,
                    config: sf.config ?? {}
                })),
            transformers: col.pipeline.transformers
        } : col.config,
        locale: col.locale
    };
};

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/config/components/column-config-modal/use-add-column-groups.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  useAddColumnGroups: () => (useAddColumnGroups)
});
/* import */ var react__rspack_import_0 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_0_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_0);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_1 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_1);
/* import */ var lodash__rspack_import_2 = __webpack_require__("./node_modules/lodash/lodash.js");
/* import */ var lodash__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(lodash__rspack_import_2);
/* import */ var _types__rspack_import_3 = __webpack_require__("./js/src/modules/config/components/column-config-modal/types.ts");
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
 * Builds grouped, selectable column groups for the studio ColumnPicker from a
 * flat list of GridColumnConfiguration entries. Each leaf carries its column in
 * `meta`, so the picker's `onSelect` can hand it back to the add-column handler.
 */ const useAddColumnGroups = (availableColumns)=>{
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_1.useTranslation)();
    return (0,react__rspack_import_0.useMemo)(()=>{
        const groupTree = {};
        // The advanced column is offered through its own dedicated button, not the tree.
        const treeColumns = availableColumns.filter((column)=>column.key !== _types__rspack_import_3.ADVANCED_COLUMN_KEY && column.type !== _types__rspack_import_3.ADVANCED_COLUMN_TYPE);
        treeColumns.forEach((column)=>{
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
        let groupIndex = 0;
        const convertTreeToGroups = (tree)=>{
            return Object.entries(tree).reduce((acc, param)=>{
                let [groupName, groupData] = param;
                const children = convertTreeToGroups(groupData.subGroups);
                const items = groupData.items.map((column)=>{
                    let translationKey = column.key;
                    if (!(0,lodash__rspack_import_2.isNil)(column.config) && 'fieldDefinition' in column.config) {
                        const fieldDefinition = column.config.fieldDefinition;
                        translationKey = (fieldDefinition === null || fieldDefinition === void 0 ? void 0 : fieldDefinition.title) ?? column.key;
                    }
                    return {
                        key: column.key,
                        label: t(translationKey),
                        meta: column
                    };
                });
                if (items.length > 0 || children.length > 0) {
                    acc.push({
                        key: `group-${groupIndex++}`,
                        label: t(groupName),
                        items,
                        children
                    });
                }
                return acc;
            }, []);
        };
        return convertTreeToGroups(groupTree);
    }, [
        availableColumns,
        t
    ]);
};

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/config/components/column-config-modal/use-column-editor-state.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  useColumnEditorState: () => (useColumnEditorState)
});
/* import */ var react__rspack_import_0 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_0_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_0);
/* import */ var _pimcore_studio_ui_bundle_modules_element__rspack_import_1 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/modules/element");
/* import */ var _pimcore_studio_ui_bundle_modules_element__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_modules_element__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_api_data_object__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/api/data-object");
/* import */ var _pimcore_studio_ui_bundle_api_data_object__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_api_data_object__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_modules_data_object__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/modules/data-object");
/* import */ var _pimcore_studio_ui_bundle_modules_data_object__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_modules_data_object__rspack_import_3);
/* import */ var lodash__rspack_import_4 = __webpack_require__("./node_modules/lodash/lodash.js");
/* import */ var lodash__rspack_import_4_default = /*#__PURE__*/__webpack_require__.n(lodash__rspack_import_4);
/* import */ var _types__rspack_import_5 = __webpack_require__("./js/src/modules/config/components/column-config-modal/types.ts");
/* import */ var _use_add_column_groups__rspack_import_6 = __webpack_require__("./js/src/modules/config/components/column-config-modal/use-add-column-groups.ts");
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






const SYSTEM_COLUMNS = [
    {
        key: 'id',
        type: 'system.id',
        group: [
            'system'
        ],
        config: []
    },
    {
        key: 'fullpath',
        type: 'system.string',
        group: [
            'system'
        ],
        config: []
    }
];
const useColumnEditorState = (param)=>{
    let { entity, classDefinitionId, columns, onApply, onCancel, exportableOnly = false } = param;
    const { getByName } = (0,_pimcore_studio_ui_bundle_modules_data_object__rspack_import_3.useClassDefinitions)();
    const resolvedClassId = (0,react__rspack_import_0.useMemo)(()=>{
        var _getByName;
        if (!(0,lodash__rspack_import_4.isNil)(classDefinitionId)) return classDefinitionId;
        return ((_getByName = getByName(entity)) === null || _getByName === void 0 ? void 0 : _getByName.id) ?? entity;
    }, [
        classDefinitionId,
        entity,
        getByName
    ]);
    const { data, isLoading } = _pimcore_studio_ui_bundle_api_data_object__rspack_import_2.api.endpoints.dataObjectGetAvailableGridColumns.useQuery({
        classId: resolvedClassId,
        folderId: 1
    });
    const [draft, setDraft] = (0,react__rspack_import_0.useState)(()=>columns.map(_types__rspack_import_5.advancedFromSchemaColumn));
    (0,react__rspack_import_0.useEffect)(()=>{
        setDraft(columns.map(_types__rspack_import_5.advancedFromSchemaColumn));
    }, [
        columns
    ]);
    const [objectId, setObjectId] = (0,react__rspack_import_0.useState)(null);
    const hasManualSelection = (0,react__rspack_import_0.useRef)(false);
    const { data: gridData } = _pimcore_studio_ui_bundle_api_data_object__rspack_import_2.api.endpoints.dataObjectGetGrid.useQuery({
        classId: resolvedClassId,
        body: {
            folderId: 1,
            columns: SYSTEM_COLUMNS,
            filters: {
                includeDescendants: true,
                page: 1,
                pageSize: 1
            }
        }
    }, {
        skip: resolvedClassId === undefined
    });
    (0,react__rspack_import_0.useEffect)(()=>{
        var _gridData_items;
        if (hasManualSelection.current) return;
        const firstItem = gridData === null || gridData === void 0 ? void 0 : (_gridData_items = gridData.items) === null || _gridData_items === void 0 ? void 0 : _gridData_items[0];
        if ((firstItem === null || firstItem === void 0 ? void 0 : firstItem.id) !== undefined) {
            setObjectId(firstItem.id);
        }
    }, [
        gridData === null || gridData === void 0 ? void 0 : gridData.items
    ]);
    const { open: openElementSelector } = (0,_pimcore_studio_ui_bundle_modules_element__rspack_import_1.useElementSelector)({
        selectionType: _pimcore_studio_ui_bundle_modules_element__rspack_import_1.SelectionType.Single,
        areas: {
            object: true,
            asset: false,
            document: false
        },
        config: {
            objects: {
                allowedTypes: [
                    'object'
                ],
                ...entity !== undefined ? {
                    allowedClasses: [
                        entity
                    ]
                } : {}
            }
        },
        onFinish: (event)=>{
            var _event_items;
            const item = event === null || event === void 0 ? void 0 : (_event_items = event.items) === null || _event_items === void 0 ? void 0 : _event_items[0];
            if (item !== undefined) {
                hasManualSelection.current = true;
                setObjectId(item.data.id);
            }
        }
    });
    const availableFields = (data === null || data === void 0 ? void 0 : data.columns) ?? [];
    (0,react__rspack_import_0.useEffect)(()=>{
        if (availableFields.length === 0) return;
        setDraft((prev)=>prev.map((col)=>{
                const available = availableFields.find((f)=>f.key === col.key || col.type === _types__rspack_import_5.ADVANCED_COLUMN_TYPE && f.type === col.type);
                if (available === undefined) return col;
                return {
                    ...col,
                    localizable: col.localizable ?? available.localizable,
                    pipelineConfig: col.pipelineConfig ?? available.config
                };
            }));
    }, [
        availableFields
    ]);
    const handleAddColumnOfType = (0,react__rspack_import_0.useCallback)((column)=>{
        setDraft((prev)=>[
                ...prev,
                {
                    _id: crypto.randomUUID(),
                    key: column.key,
                    fieldtype: column.key,
                    type: column.type,
                    pipelineConfig: column.config,
                    localizable: column.localizable,
                    isNew: true
                }
            ]);
    }, []);
    // Only the add-column dropdown is restricted to exportable columns. The full
    // availableFields list is still used above to hydrate already-configured columns,
    // so existing schemas containing non-exportable columns keep rendering unchanged.
    const addColumnFields = (0,react__rspack_import_0.useMemo)(()=>exportableOnly ? availableFields.filter((field)=>field.exportable === true) : availableFields, [
        availableFields,
        exportableOnly
    ]);
    const columnGroups = (0,_use_add_column_groups__rspack_import_6.useAddColumnGroups)(addColumnFields);
    const advancedColumn = (0,react__rspack_import_0.useMemo)(()=>addColumnFields.find((field)=>field.type === _types__rspack_import_5.ADVANCED_COLUMN_TYPE || field.key === _types__rspack_import_5.ADVANCED_COLUMN_KEY), [
        addColumnFields
    ]);
    const onAddAdvancedColumn = advancedColumn !== undefined ? ()=>{
        handleAddColumnOfType(advancedColumn);
    } : undefined;
    const handlePipelineChange = (id, pipeline)=>{
        setDraft((prev)=>prev.map((col)=>col._id === id ? {
                    ...col,
                    pipeline
                } : col));
    };
    const handleRemove = (id)=>{
        setDraft((prev)=>prev.filter((col)=>col._id !== id));
    };
    const handleApply = ()=>{
        onApply(draft.filter((col)=>col.key !== '').map(_types__rspack_import_5.advancedToSchemaColumn));
    };
    const handleDiscard = ()=>{
        setDraft(columns.map(_types__rspack_import_5.advancedFromSchemaColumn));
        onCancel();
    };
    const handleLocaleChange = (id, locale)=>{
        setDraft((prev)=>prev.map((c)=>c._id === id ? {
                    ...c,
                    locale
                } : c));
    };
    const handleReorder = (ids)=>{
        setDraft((prev)=>{
            return ids.map((id)=>prev.find((col)=>col._id === id)).filter((col)=>col !== undefined);
        });
    };
    return {
        draft,
        isLoading,
        objectId,
        availableFields,
        columnGroups,
        onAddAdvancedColumn,
        openElementSelector,
        handleAddColumnOfType,
        handlePipelineChange,
        handleRemove,
        handleApply,
        handleDiscard,
        handleLocaleChange,
        handleReorder,
        getColumns: ()=>draft.filter((col)=>col.key !== '').map(_types__rspack_import_5.advancedToSchemaColumn)
    };
};

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/config/components/migration-modal/compact-layout-context.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  CompactLayoutProvider: () => (CompactLayoutProvider),
  useCompactLayout: () => (useCompactLayout)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* provided dependency */ var $ReactRefreshRuntime$ = __webpack_require__("./node_modules/@rspack/plugin-react-refresh/client/reactRefresh.js");

var _s = $RefreshSig$(), _s1 = $RefreshSig$();
/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */ 
const CompactLayoutContext = /*#__PURE__*/ (0,react__rspack_import_1.createContext)({
    compact: false
});
const CompactLayoutProvider = (param)=>{
    let { children, compact = false } = param;
    _s();
    return (0,react__rspack_import_1.useMemo)(()=>/*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(CompactLayoutContext.Provider, {
            value: {
                compact
            },
            children: children
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/migration-modal/compact-layout-context.tsx",
            lineNumber: 32,
            columnNumber: 5
        }, undefined), [
        children,
        compact
    ]);
};
_s(CompactLayoutProvider, "nwk+m61qLgjDVUp4IGV/072DDN4=");
_c = CompactLayoutProvider;
const useCompactLayout = ()=>{
    _s1();
    return (0,react__rspack_import_1.useContext)(CompactLayoutContext);
};
_s1(useCompactLayout, "gDsCjeeItUuvgOWf1v4qoK9RF6k=");
var _c;
$RefreshReg$(_c, "CompactLayoutProvider");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/config/components/migration-modal/index.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  CompactLayoutProvider: () => (/* reexport safe */ _compact_layout_context__rspack_import_1.CompactLayoutProvider),
  MigrationModal: () => (/* reexport safe */ _migration_modal__rspack_import_0.MigrationModal),
  useCompactLayout: () => (/* reexport safe */ _compact_layout_context__rspack_import_1.useCompactLayout)
});
/* import */ var _migration_modal__rspack_import_0 = __webpack_require__("./js/src/modules/config/components/migration-modal/migration-modal.tsx");
/* import */ var _compact_layout_context__rspack_import_1 = __webpack_require__("./js/src/modules/config/components/migration-modal/compact-layout-context.tsx");
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
/* import */ var _compact_layout_context__rspack_import_5 = __webpack_require__("./js/src/modules/config/components/migration-modal/compact-layout-context.tsx");
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
        lineNumber: 86,
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
                            lineNumber: 105,
                            columnNumber: 11
                        }, undefined),
                        /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.CodeEditor, {
                            height: "400px",
                            preset: "yaml",
                            readOnly: true,
                            value: yamlValue
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/migration-modal/migration-modal.tsx",
                            lineNumber: 111,
                            columnNumber: 11
                        }, undefined)
                    ]
                }, void 0, true, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/migration-modal/migration-modal.tsx",
                    lineNumber: 101,
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
                            lineNumber: 124,
                            columnNumber: 13
                        }, undefined)
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/migration-modal/migration-modal.tsx",
                        lineNumber: 123,
                        columnNumber: 11
                    }, undefined)
                }, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/migration-modal/migration-modal.tsx",
                    lineNumber: 119,
                    columnNumber: 9
                }, undefined)
            ]
        }, void 0, true, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/migration-modal/migration-modal.tsx",
            lineNumber: 94,
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
                        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_compact_layout_context__rspack_import_5.CompactLayoutProvider, {
                            compact: true,
                            children: children
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/migration-modal/migration-modal.tsx",
                            lineNumber: 154,
                            columnNumber: 11
                        }, undefined)
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/migration-modal/migration-modal.tsx",
                        lineNumber: 150,
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
                        lineNumber: 159,
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
                                lineNumber: 167,
                                columnNumber: 11
                            }, undefined),
                            /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.CodeEditor, {
                                height: "100%",
                                preset: "yaml",
                                readOnly: true,
                                value: yamlValue
                            }, void 0, false, {
                                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/migration-modal/migration-modal.tsx",
                                lineNumber: 172,
                                columnNumber: 11
                            }, undefined)
                        ]
                    }, void 0, true, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/migration-modal/migration-modal.tsx",
                        lineNumber: 162,
                        columnNumber: 9
                    }, undefined)
                ]
            }, void 0, true, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/migration-modal/migration-modal.tsx",
                lineNumber: 145,
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
                                lineNumber: 188,
                                columnNumber: 11
                            }, undefined),
                            /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Button, {
                                onClick: onConfirm,
                                type: "primary",
                                children: t('data-hub.migration-modal.confirm-migration')
                            }, void 0, false, {
                                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/migration-modal/migration-modal.tsx",
                                lineNumber: 195,
                                columnNumber: 11
                            }, undefined)
                        ]
                    }, void 0, true, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/migration-modal/migration-modal.tsx",
                        lineNumber: 187,
                        columnNumber: 9
                    }, undefined)
                ]
            }, void 0, true, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/migration-modal/migration-modal.tsx",
                lineNumber: 181,
                columnNumber: 7
            }, undefined)
        ]
    }, void 0, true, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/migration-modal/migration-modal.tsx",
        lineNumber: 138,
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
  ADVANCED_COLUMN_TYPE: () => (/* reexport safe */ _modules_config_components_column_config_modal__rspack_import_9.ADVANCED_COLUMN_TYPE),
  BaseColumnEditor: () => (/* reexport safe */ _modules_config_components_column_config_modal__rspack_import_9.BaseColumnEditor),
  BaseDetailView: () => (/* reexport safe */ _components_base_detail_view__rspack_import_6.BaseDetailView),
  ColumnConfigModal: () => (/* reexport safe */ _modules_config_components_column_config_modal__rspack_import_9.ColumnConfigModal),
  ColumnEditorItemBody: () => (/* reexport safe */ _modules_config_components_column_config_modal__rspack_import_9.ColumnEditorItemBody),
  ColumnLocaleControl: () => (/* reexport safe */ _modules_config_components_column_config_modal__rspack_import_9.ColumnLocaleControl),
  ColumnPipelineForm: () => (/* reexport safe */ _modules_config_components_column_config_modal__rspack_import_9.ColumnPipelineForm),
  ColumnPreview: () => (/* reexport safe */ _modules_config_components_column_config_modal__rspack_import_9.ColumnPreview),
  CompactLayoutProvider: () => (/* reexport safe */ _modules_config_components_migration_modal__rspack_import_8.CompactLayoutProvider),
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
  trackConfigError: () => (/* reexport safe */ _components_base_detail_view__rspack_import_6.trackConfigError),
  useAddColumnGroups: () => (/* reexport safe */ _modules_config_components_column_config_modal__rspack_import_9.useAddColumnGroups),
  useCompactLayout: () => (/* reexport safe */ _modules_config_components_migration_modal__rspack_import_8.useCompactLayout),
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