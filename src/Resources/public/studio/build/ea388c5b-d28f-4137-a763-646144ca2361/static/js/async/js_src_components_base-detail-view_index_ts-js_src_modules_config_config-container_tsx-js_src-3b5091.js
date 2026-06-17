"use strict";
(self["chunk_pimcore_datahub_bundle "] = self["chunk_pimcore_datahub_bundle "] || []).push([["js_src_components_base-detail-view_index_ts-js_src_modules_config_config-container_tsx-js_src-3b5091"], {
"./js/src/components/base-detail-view/base-detail-view.styles.tsx"(module, __webpack_exports__, __webpack_require__) {
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
        formWrapper: css`
      display: contents;

      > form {
        display: flex;
        flex-direction: column;
        flex: 1;
        min-height: 0;
      }
    `
    };
});

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/components/base-detail-view/base-detail-view.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  BaseDetailView: () => (BaseDetailView)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _base_detail_view_styles__rspack_import_3 = __webpack_require__("./js/src/components/base-detail-view/base-detail-view.styles.tsx");
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


function BaseDetailView(param) {
    let { toolbar, tabs, isLoading, form, initialValues, onValuesChange, disabled = false, requestId } = param;
    _s();
    const { styles } = (0,_base_detail_view_styles__rspack_import_3.useStyles)();
    const enhancedTabs = tabs.map((tab)=>({
            ...tab,
            children: tab.fullHeight === true ? tab.children : /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Content, {
                padded: true,
                children: tab.children
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/components/base-detail-view/base-detail-view.tsx",
                lineNumber: 51,
                columnNumber: 9
            }, this)
        }));
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.ContentLayout, {
        renderToolbar: toolbar,
        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Content, {
            loading: isLoading,
            overflow: {
                x: 'auto',
                y: 'hidden'
            },
            children: !isLoading && /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)("div", {
                className: styles.formWrapper,
                children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.FormKit, {
                    formProps: {
                        form,
                        initialValues,
                        layout: 'vertical',
                        onValuesChange,
                        disabled
                    },
                    wrapInPanel: false,
                    children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Tabs, {
                        defaultActiveKey: "general",
                        fullHeight: true,
                        items: enhancedTabs,
                        noTabBarMargin: true,
                        type: "card"
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/components/base-detail-view/base-detail-view.tsx",
                        lineNumber: 76,
                        columnNumber: 15
                    }, this)
                }, requestId, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/components/base-detail-view/base-detail-view.tsx",
                    lineNumber: 65,
                    columnNumber: 13
                }, this)
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/components/base-detail-view/base-detail-view.tsx",
                lineNumber: 64,
                columnNumber: 11
            }, this)
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/components/base-detail-view/base-detail-view.tsx",
            lineNumber: 59,
            columnNumber: 7
        }, this)
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/components/base-detail-view/base-detail-view.tsx",
        lineNumber: 58,
        columnNumber: 5
    }, this);
}
_s(BaseDetailView, "1BGFRu6BGAbhzJ8kKgs1GUjvI6w=", false, function() {
    return [
        _base_detail_view_styles__rspack_import_3.useStyles
    ];
});
_c = BaseDetailView;
var _c;
$RefreshReg$(_c, "BaseDetailView");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/components/base-detail-view/components/config-toolbar/config-toolbar.styles.ts"(module, __webpack_exports__, __webpack_require__) {
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
    let { css, token } = param;
    return {
        divider: css`
      height: ${token.fontSizeLG}px;
      align-self: center;
      margin-top: 0 !important;
      margin-bottom: 0 !important;
      margin-inline: ${token.marginXXS}px;
    `
    };
});

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/components/base-detail-view/components/config-toolbar/config-toolbar.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  ConfigToolbar: () => (ConfigToolbar)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_3);
/* import */ var _modules_config_components_export_button__rspack_import_4 = __webpack_require__("./js/src/modules/config/components/export-button/index.ts");
/* import */ var _config_toolbar_styles__rspack_import_5 = __webpack_require__("./js/src/components/base-detail-view/components/config-toolbar/config-toolbar.styles.ts");
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




function ConfigToolbar(param) {
    let { configName, isWriteable, isLoading, isSaving, isDirty, onSave, onRefresh, onDelete, additionalButtons = [], leftAdditionalContent } = param;
    _s();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation)();
    const { styles } = (0,_config_toolbar_styles__rspack_import_5.useStyles)();
    const saveButton = /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Button, {
        disabled: !isDirty || !isWriteable,
        loading: isSaving,
        onClick: onSave,
        type: "primary",
        children: t('save')
    }, "save", false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/components/base-detail-view/components/config-toolbar/config-toolbar.tsx",
        lineNumber: 46,
        columnNumber: 5
    }, this);
    const rightButtons = [
        ...additionalButtons,
        !isWriteable ? /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Tooltip, {
            title: t('config_not_writeable'),
            children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)("span", {
                children: saveButton
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/components/base-detail-view/components/config-toolbar/config-toolbar.tsx",
                lineNumber: 65,
                columnNumber: 11
            }, this)
        }, "save-tooltip", false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/components/base-detail-view/components/config-toolbar/config-toolbar.tsx",
            lineNumber: 61,
            columnNumber: 9
        }, this) : saveButton
    ];
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Toolbar, {
        children: [
            /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Space, {
                size: "extra-small",
                children: [
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Tooltip, {
                        title: t('refresh'),
                        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.IconButton, {
                            disabled: isLoading,
                            icon: {
                                value: 'refresh'
                            },
                            onClick: onRefresh
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/components/base-detail-view/components/config-toolbar/config-toolbar.tsx",
                            lineNumber: 75,
                            columnNumber: 11
                        }, this)
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/components/base-detail-view/components/config-toolbar/config-toolbar.tsx",
                        lineNumber: 74,
                        columnNumber: 9
                    }, this),
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Tooltip, {
                        title: isWriteable ? t('delete') : t('config_not_writeable'),
                        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.IconButton, {
                            disabled: !isWriteable,
                            icon: {
                                value: 'trash'
                            },
                            onClick: onDelete
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/components/base-detail-view/components/config-toolbar/config-toolbar.tsx",
                            lineNumber: 82,
                            columnNumber: 11
                        }, this)
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/components/base-detail-view/components/config-toolbar/config-toolbar.tsx",
                        lineNumber: 81,
                        columnNumber: 9
                    }, this),
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_modules_config_components_export_button__rspack_import_4.ExportButton, {
                        configName: configName
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/components/base-detail-view/components/config-toolbar/config-toolbar.tsx",
                        lineNumber: 88,
                        columnNumber: 9
                    }, this),
                    leftAdditionalContent !== undefined && /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(react_jsx_dev_runtime__rspack_import_0.Fragment, {
                        children: [
                            /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Divider, {
                                className: styles.divider,
                                type: "vertical"
                            }, void 0, false, {
                                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/components/base-detail-view/components/config-toolbar/config-toolbar.tsx",
                                lineNumber: 91,
                                columnNumber: 13
                            }, this),
                            leftAdditionalContent
                        ]
                    }, void 0, true)
                ]
            }, void 0, true, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/components/base-detail-view/components/config-toolbar/config-toolbar.tsx",
                lineNumber: 73,
                columnNumber: 7
            }, this),
            /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.ButtonGroup, {
                items: rightButtons
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/components/base-detail-view/components/config-toolbar/config-toolbar.tsx",
                lineNumber: 99,
                columnNumber: 7
            }, this)
        ]
    }, void 0, true, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/components/base-detail-view/components/config-toolbar/config-toolbar.tsx",
        lineNumber: 72,
        columnNumber: 5
    }, this);
}
_s(ConfigToolbar, "RLrtxoYLhU6K3pUxNPOpN7Ah0UQ=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation,
        _config_toolbar_styles__rspack_import_5.useStyles
    ];
});
_c = ConfigToolbar;
var _c;
$RefreshReg$(_c, "ConfigToolbar");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/components/base-detail-view/components/config-toolbar/index.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  ConfigToolbar: () => (/* reexport safe */ _config_toolbar__rspack_import_0.ConfigToolbar)
});
/* import */ var _config_toolbar__rspack_import_0 = __webpack_require__("./js/src/components/base-detail-view/components/config-toolbar/config-toolbar.tsx");
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
"./js/src/components/base-detail-view/hooks/index.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  useDetailView: () => (/* reexport safe */ _use_detail_view__rspack_import_0.useDetailView)
});
/* import */ var _use_detail_view__rspack_import_0 = __webpack_require__("./js/src/components/base-detail-view/hooks/use-detail-view.ts");
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
"./js/src/components/base-detail-view/hooks/use-detail-view.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  useDetailView: () => (useDetailView)
});
/* import */ var react__rspack_import_0 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_0_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_0);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_1 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_2);
/* import */ var lodash__rspack_import_3 = __webpack_require__("./node_modules/lodash/lodash.js");
/* import */ var lodash__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(lodash__rspack_import_3);
/* import */ var _track_config_error__rspack_import_4 = __webpack_require__("./js/src/components/base-detail-view/track-config-error.ts");
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




function useDetailView(param) {
    let { configName, configData, modificationDate, isLoading, requestId, transformToForm, transformToBackend, onSave, onChange, successMessageKey = 'save-success' } = param;
    const [form] = _pimcore_studio_ui_bundle_components__rspack_import_1.Form.useForm();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_2.useTranslation)();
    const messageApi = (0,_pimcore_studio_ui_bundle_components__rspack_import_1.useMessage)();
    const [isDirty, setIsDirty] = (0,react__rspack_import_0.useState)(false);
    const modificationDateRef = (0,react__rspack_import_0.useRef)(0);
    const isSavingRef = (0,react__rspack_import_0.useRef)(false);
    const initialValues = (0,react__rspack_import_0.useMemo)(()=>{
        return transformToForm(configData ?? {}, configName);
    }, [
        configData,
        configName
    ]);
    (0,react__rspack_import_0.useEffect)(()=>{
        if (isLoading || (0,lodash__rspack_import_3.isNil)(configData)) return;
        form.setFieldsValue(initialValues);
        setIsDirty(false);
        onChange(false);
        if (!isSavingRef.current) {
            modificationDateRef.current = modificationDate ?? 0;
        }
    }, [
        requestId,
        initialValues,
        modificationDate,
        isLoading
    ]);
    const handleValuesChange = ()=>{
        setIsDirty(true);
        onChange(true);
    };
    const handleSave = ()=>{
        if (isSavingRef.current) {
            return;
        }
        form.validateFields().then(async (values)=>{
            isSavingRef.current = true;
            try {
                const mergedValues = {
                    ...initialValues,
                    ...values
                };
                const updatedConfig = transformToBackend(mergedValues, configData ?? {});
                const response = await onSave(updatedConfig, modificationDateRef.current);
                if (!(0,lodash__rspack_import_3.isNil)(response === null || response === void 0 ? void 0 : response.modificationDate)) {
                    modificationDateRef.current = response.modificationDate;
                }
                setIsDirty(false);
                onChange(false);
                void messageApi.success(t(successMessageKey));
            } catch (error) {
                (0,_track_config_error__rspack_import_4.trackConfigError)(error);
            } finally{
                isSavingRef.current = false;
            }
        }).catch((error)=>{
            console.error('Validation failed:', error);
            void messageApi.error(t('data-hub.save-validation-error'));
        });
    };
    return {
        form,
        isDirty,
        initialValues,
        handleSave,
        handleValuesChange
    };
}

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/components/base-detail-view/index.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  BaseDetailView: () => (/* reexport safe */ _base_detail_view__rspack_import_0.BaseDetailView),
  ConfigToolbar: () => (/* reexport safe */ _components_config_toolbar__rspack_import_1.ConfigToolbar),
  trackConfigError: () => (/* reexport safe */ _track_config_error__rspack_import_3.trackConfigError),
  useDetailView: () => (/* reexport safe */ _hooks__rspack_import_2.useDetailView)
});
/* import */ var _base_detail_view__rspack_import_0 = __webpack_require__("./js/src/components/base-detail-view/base-detail-view.tsx");
/* import */ var _components_config_toolbar__rspack_import_1 = __webpack_require__("./js/src/components/base-detail-view/components/config-toolbar/index.ts");
/* import */ var _hooks__rspack_import_2 = __webpack_require__("./js/src/components/base-detail-view/hooks/index.ts");
/* import */ var _track_config_error__rspack_import_3 = __webpack_require__("./js/src/components/base-detail-view/track-config-error.ts");
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
"./js/src/components/base-detail-view/track-config-error.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  trackConfigError: () => (trackConfigError)
});
/* import */ var _pimcore_studio_ui_bundle_modules_app__rspack_import_0 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/modules/app");
/* import */ var _pimcore_studio_ui_bundle_modules_app__rspack_import_0_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_modules_app__rspack_import_0);
/* import */ var lodash__rspack_import_1 = __webpack_require__("./node_modules/lodash/lodash.js");
/* import */ var lodash__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(lodash__rspack_import_1);
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

// Mirrors HttpResponseErrorKeys::VALIDATION_FAILED in the studio-backend-bundle.
const VALIDATION_FAILED_ERROR_KEY = 'error_validation_failed';
/**
 * Tracks an API error coming from a Data Hub config load/save.
 *
 * For a generic validation failure the backend returns the human-readable reason
 * in `message` (e.g. "Please define a schema for the export"), while the default
 * ApiError handling would only show the translated "Validation failed" error key
 * and discard that reason. In that case we surface the message instead so the user
 * sees what is actually wrong.
 */ const trackConfigError = (error)=>{
    if (!(0,_pimcore_studio_ui_bundle_modules_app__rspack_import_0.isApiErrorData)(error)) {
        return;
    }
    if ('data' in error) {
        const details = error.data;
        if ((details === null || details === void 0 ? void 0 : details.errorKey) === VALIDATION_FAILED_ERROR_KEY && !(0,lodash__rspack_import_1.isEmpty)(details.message)) {
            // Pass the reason as the top-level message so ApiError.getContent surfaces it verbatim.
            (0,_pimcore_studio_ui_bundle_modules_app__rspack_import_0.trackError)(new _pimcore_studio_ui_bundle_modules_app__rspack_import_0.ApiError({
                message: details.message
            }));
            return;
        }
    }
    (0,_pimcore_studio_ui_bundle_modules_app__rspack_import_0.trackError)(new _pimcore_studio_ui_bundle_modules_app__rspack_import_0.ApiError(error));
};

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/config/service-ids.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  bundleServiceIds: () => (bundleServiceIds)
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
 * Service IDs for the Data Hub Bundle
 * Centralized location for all dependency injection service identifiers
 */ const bundleServiceIds = {
    'DataHub/DynamicTypes/Adapter/Registry': 'DataHub/DynamicTypes/Adapter/Registry',
    'DataHub/DynamicTypes/Adapter/GraphQL': 'DataHub/DynamicTypes/Adapter/GraphQL',
    'DataHub/DynamicTypes/Operator/GraphQL/QueryRegistry': 'DataHub/DynamicTypes/Operator/GraphQL/QueryRegistry',
    'DataHub/DynamicTypes/Operator/GraphQL/MutationRegistry': 'DataHub/DynamicTypes/Operator/GraphQL/MutationRegistry',
    // Query Operators
    'DataHub/DynamicTypes/Operator/Alias': 'DataHub/DynamicTypes/Operator/Alias',
    'DataHub/DynamicTypes/Operator/Concatenator': 'DataHub/DynamicTypes/Operator/Concatenator',
    'DataHub/DynamicTypes/Operator/DateFormatter': 'DataHub/DynamicTypes/Operator/DateFormatter',
    'DataHub/DynamicTypes/Operator/ElementCounter': 'DataHub/DynamicTypes/Operator/ElementCounter',
    'DataHub/DynamicTypes/Operator/Substring': 'DataHub/DynamicTypes/Operator/Substring',
    'DataHub/DynamicTypes/Operator/Text': 'DataHub/DynamicTypes/Operator/Text',
    'DataHub/DynamicTypes/Operator/Thumbnail': 'DataHub/DynamicTypes/Operator/Thumbnail',
    'DataHub/DynamicTypes/Operator/ThumbnailHtml': 'DataHub/DynamicTypes/Operator/ThumbnailHtml',
    'DataHub/DynamicTypes/Operator/TranslateValue': 'DataHub/DynamicTypes/Operator/TranslateValue',
    'DataHub/DynamicTypes/Operator/Trimmer': 'DataHub/DynamicTypes/Operator/Trimmer',
    // Mutation Operators
    'DataHub/DynamicTypes/Operator/IfEmpty': 'DataHub/DynamicTypes/Operator/IfEmpty',
    'DataHub/DynamicTypes/Operator/LocaleCollector': 'DataHub/DynamicTypes/Operator/LocaleCollector',
    'DataHub/DynamicTypes/Operator/LocaleSwitcher': 'DataHub/DynamicTypes/Operator/LocaleSwitcher'
};

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/config/components/config-sidebar/components/config-sidebar-toolbar/toolbar.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  ConfigSidebarToolbar: () => (ConfigSidebarToolbar)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_3);
/* import */ var _config_service_ids__rspack_import_4 = __webpack_require__("./js/src/config/service-ids.ts");
/* import */ var _import_button_import_button__rspack_import_5 = __webpack_require__("./js/src/modules/config/components/import-button/import-button.tsx");
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




const ConfigSidebarToolbar = (param)=>{
    let { onAdd, onRefresh, handleOpenConfig, isFetching } = param;
    _s();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation)();
    const adapterRegistry = _pimcore_studio_ui_bundle_app__rspack_import_3.container.get(_config_service_ids__rspack_import_4.bundleServiceIds["DataHub/DynamicTypes/Adapter/Registry"]);
    const adapters = adapterRegistry.getDynamicTypes();
    const dropdownItems = adapters.map((adapter)=>({
            key: adapter.id,
            label: t(adapter.getNameTranslationKey()),
            icon: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Icon, {
                ...adapter.getIcon()
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/config-sidebar/components/config-sidebar-toolbar/toolbar.tsx",
                lineNumber: 35,
                columnNumber: 11
            }, undefined),
            onClick: ()=>{
                onAdd(adapter.id);
            }
        }));
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Toolbar, {
        children: [
            /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Flex, {
                gap: "extra-small",
                children: [
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Tooltip, {
                        title: t('refresh'),
                        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.IconButton, {
                            disabled: isFetching,
                            icon: {
                                value: 'refresh'
                            },
                            onClick: onRefresh,
                            type: "link"
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/config-sidebar/components/config-sidebar-toolbar/toolbar.tsx",
                            lineNumber: 43,
                            columnNumber: 11
                        }, undefined)
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/config-sidebar/components/config-sidebar-toolbar/toolbar.tsx",
                        lineNumber: 42,
                        columnNumber: 9
                    }, undefined),
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_import_button_import_button__rspack_import_5.ImportButton, {
                        disabled: isFetching,
                        handleOpenConfig: handleOpenConfig,
                        onRefresh: onRefresh
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/config-sidebar/components/config-sidebar-toolbar/toolbar.tsx",
                        lineNumber: 51,
                        columnNumber: 9
                    }, undefined)
                ]
            }, void 0, true, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/config-sidebar/components/config-sidebar-toolbar/toolbar.tsx",
                lineNumber: 41,
                columnNumber: 7
            }, undefined),
            /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Dropdown, {
                menu: {
                    items: dropdownItems
                },
                trigger: [
                    'click'
                ],
                children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.DropdownButton, {
                    children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Flex, {
                        align: "center",
                        gap: "extra-small",
                        children: [
                            /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Icon, {
                                value: "new"
                            }, void 0, false, {
                                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/config-sidebar/components/config-sidebar-toolbar/toolbar.tsx",
                                lineNumber: 67,
                                columnNumber: 13
                            }, undefined),
                            t('new')
                        ]
                    }, void 0, true, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/config-sidebar/components/config-sidebar-toolbar/toolbar.tsx",
                        lineNumber: 63,
                        columnNumber: 11
                    }, undefined)
                }, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/config-sidebar/components/config-sidebar-toolbar/toolbar.tsx",
                    lineNumber: 62,
                    columnNumber: 9
                }, undefined)
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/config-sidebar/components/config-sidebar-toolbar/toolbar.tsx",
                lineNumber: 58,
                columnNumber: 7
            }, undefined)
        ]
    }, void 0, true, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/config-sidebar/components/config-sidebar-toolbar/toolbar.tsx",
        lineNumber: 40,
        columnNumber: 5
    }, undefined);
};
_s(ConfigSidebarToolbar, "zlIdU9EjM2llFt74AbE2KsUJXyM=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation
    ];
});
_c = ConfigSidebarToolbar;
var _c;
$RefreshReg$(_c, "ConfigSidebarToolbar");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/config/components/config-sidebar/config-sidebar.styles.ts"(module, __webpack_exports__, __webpack_require__) {
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
        treeContainer: css`
      .ant-tree-list-holder-inner {
        align-items: start;
      }

      .ant-tree-node-content-wrapper {
        white-space: nowrap;
      }
    `
    };
});

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/config/components/config-sidebar/config-sidebar.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  ConfigSidebar: () => (ConfigSidebar)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var lodash__rspack_import_2 = __webpack_require__("./node_modules/lodash/lodash.js");
/* import */ var lodash__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(lodash__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_3);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_4 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_4_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_4);
/* import */ var _components_config_sidebar_toolbar_toolbar__rspack_import_5 = __webpack_require__("./js/src/modules/config/components/config-sidebar/components/config-sidebar-toolbar/toolbar.tsx");
/* import */ var _config_service_ids__rspack_import_6 = __webpack_require__("./js/src/config/service-ids.ts");
/* import */ var _providers_config_provider__rspack_import_7 = __webpack_require__("./js/src/modules/config/providers/config-provider.tsx");
/* import */ var _hooks_use_data_hub_config__rspack_import_8 = __webpack_require__("./js/src/modules/config/hooks/use-data-hub-config.ts");
/* import */ var _utils_tree_helpers__rspack_import_9 = __webpack_require__("./js/src/modules/config/utils/tree-helpers.ts");
/* import */ var _utils_adapter_helpers__rspack_import_10 = __webpack_require__("./js/src/modules/config/utils/adapter-helpers.ts");
/* import */ var _utils_get_export_url__rspack_import_11 = __webpack_require__("./js/src/modules/config/utils/get-export-url.ts");
/* import */ var _config_sidebar_styles__rspack_import_12 = __webpack_require__("./js/src/modules/config/components/config-sidebar/config-sidebar.styles.ts");
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











const ConfigSidebar = (param)=>{
    let { handleOpenConfig } = param;
    _s();
    const { configurationsData, isLoading, isFetching, refetch, expandedKeys, setExpandedKeys } = (0,_providers_config_provider__rspack_import_7.useConfigContext)();
    const [configListData, setConfigListData] = (0,react__rspack_import_1.useState)([]);
    const [filteredData, setFilteredData] = (0,react__rspack_import_1.useState)([]);
    const [searchValue, setSearchValue] = (0,react__rspack_import_1.useState)('');
    const [treeKey, setTreeKey] = (0,react__rspack_import_1.useState)(0);
    const { handleAdd, handleClone, handleDelete } = (0,_hooks_use_data_hub_config__rspack_import_8.useDataHubConfig)({
        refetch
    });
    (0,react__rspack_import_1.useEffect)(()=>{
        if (!(0,lodash__rspack_import_2.isNil)(configurationsData === null || configurationsData === void 0 ? void 0 : configurationsData.items)) {
            setConfigListData(configurationsData.items);
            setFilteredData(configurationsData.items);
            setTreeKey((prev)=>prev + 1);
        }
    }, [
        configurationsData
    ]);
    (0,react__rspack_import_1.useEffect)(()=>{
        if (searchValue === '') {
            setFilteredData(configListData);
        } else {
            setFilteredData((0,_utils_tree_helpers__rspack_import_9.filterConfigsRecursive)(configListData, searchValue));
        }
    }, [
        searchValue,
        configListData
    ]);
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_4.useTranslation)();
    const { styles } = (0,_config_sidebar_styles__rspack_import_12.useStyles)();
    const adapterRegistry = _pimcore_studio_ui_bundle_app__rspack_import_4.container.get(_config_service_ids__rspack_import_6.bundleServiceIds["DataHub/DynamicTypes/Adapter/Registry"]);
    const getTreeItemIcon = (item)=>{
        if (item.allowChildren === true) {
            return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Icon, {
                value: "folder"
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/config-sidebar/config-sidebar.tsx",
                lineNumber: 69,
                columnNumber: 14
            }, undefined);
        }
        const adapterType = item.adapter;
        if ((0,lodash__rspack_import_2.isUndefined)(adapterType)) {
            return undefined;
        }
        const adapter = adapterRegistry.getDynamicType(adapterType, false);
        return (0,lodash__rspack_import_2.isUndefined)(adapter) ? undefined : /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Icon, {
            ...adapter.getIcon()
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/config-sidebar/config-sidebar.tsx",
            lineNumber: 78,
            columnNumber: 47
        }, undefined);
    };
    const getTreeItemActions = (item)=>{
        if (item.allowChildren === true) {
            return [];
        }
        const actions = [
            {
                key: 'clone',
                icon: 'copy-03'
            },
            {
                key: 'export',
                icon: 'export'
            }
        ];
        // Only add delete action if writeable
        if (item.writable) {
            actions.push({
                key: 'delete',
                icon: 'trash'
            });
        }
        return actions;
    };
    const transformToTreeData = (items)=>{
        if ((0,lodash__rspack_import_2.isNil)(items)) {
            return [];
        }
        return items.filter((item)=>{
            if (item.allowChildren === true) return true;
            return (0,_utils_adapter_helpers__rspack_import_10.hasValidAdapter)(item.adapter, adapterRegistry);
        }).sort((a, b)=>{
            return a.text.localeCompare(b.text, undefined, {
                sensitivity: 'base'
            });
        }).map((item)=>{
            return {
                key: (0,lodash__rspack_import_2.isUndefined)(item.id) ? '' : String(item.id),
                title: item.text,
                icon: getTreeItemIcon(item),
                children: (0,lodash__rspack_import_2.isUndefined)(item.children) ? undefined : transformToTreeData(item.children),
                isLeaf: item.leaf,
                actions: getTreeItemActions(item),
                allowDrag: false,
                allowDrop: false
            };
        });
    };
    const treeData = (0,react__rspack_import_1.useMemo)(()=>transformToTreeData(filteredData), [
        filteredData
    ]);
    const handleAddWrapper = (adapterType)=>{
        handleAdd(adapterType, handleOpenConfig);
    };
    const handleCloneWrapper = (key)=>{
        const config = (0,_utils_tree_helpers__rspack_import_9.findConfigById)(key, configListData);
        if (!(0,lodash__rspack_import_2.isNil)(config)) {
            handleClone(config, handleOpenConfig);
        }
    };
    const handleDeleteWrapper = (key)=>{
        const config = (0,_utils_tree_helpers__rspack_import_9.findConfigById)(key, configListData);
        if (!(0,lodash__rspack_import_2.isNil)(config)) {
            handleDelete(config);
        }
    };
    const handleExportWrapper = (key)=>{
        const config = (0,_utils_tree_helpers__rspack_import_9.findConfigById)(key, configListData);
        if (!(0,lodash__rspack_import_2.isNil)(config)) {
            window.location.href = (0,_utils_get_export_url__rspack_import_11.getExportUrl)(config.text);
        }
    };
    const handleActionsClick = (key, action)=>{
        switch(action){
            case 'clone':
                handleCloneWrapper(key);
                break;
            case 'export':
                handleExportWrapper(key);
                break;
            case 'delete':
                handleDeleteWrapper(key);
                break;
        }
    };
    const handleTreeItemClick = (key)=>{
        const config = (0,_utils_tree_helpers__rspack_import_9.findConfigById)(key, configListData);
        if (!(0,lodash__rspack_import_2.isNil)(config)) {
            if (config.allowChildren === true) {
                const currentKeys = expandedKeys;
                if (!(0,lodash__rspack_import_2.isNil)(currentKeys) && currentKeys.includes(key)) {
                    setExpandedKeys(currentKeys.filter((k)=>k !== key));
                } else {
                    setExpandedKeys([
                        ...currentKeys,
                        key
                    ]);
                }
            } else {
                handleOpenConfig(config);
            }
        }
    };
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.ContentLayout, {
        renderToolbar: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_components_config_sidebar_toolbar_toolbar__rspack_import_5.ConfigSidebarToolbar, {
            handleOpenConfig: handleOpenConfig,
            isFetching: isFetching,
            onAdd: handleAddWrapper,
            onRefresh: refetch
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/config-sidebar/config-sidebar.tsx",
            lineNumber: 186,
            columnNumber: 9
        }, undefined),
        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Content, {
            loading: isLoading,
            padded: true,
            children: [
                /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.SearchInput, {
                    onChange: (e)=>{
                        setSearchValue(e.target.value);
                    },
                    placeholder: t('search'),
                    withoutAddon: true
                }, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/config-sidebar/config-sidebar.tsx",
                    lineNumber: 198,
                    columnNumber: 9
                }, undefined),
                /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Content, {
                    loading: isFetching,
                    none: filteredData.length === 0,
                    children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.TreeElement, {
                        className: styles.treeContainer,
                        defaultExpandedKeys: expandedKeys,
                        onActionsClick: handleActionsClick,
                        onExpand: (keys)=>{
                            setExpandedKeys(keys);
                        },
                        onSelected: (key)=>{
                            handleTreeItemClick(String(key));
                        },
                        treeData: treeData
                    }, `config-tree-${treeKey}`, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/config-sidebar/config-sidebar.tsx",
                        lineNumber: 209,
                        columnNumber: 11
                    }, undefined)
                }, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/config-sidebar/config-sidebar.tsx",
                    lineNumber: 204,
                    columnNumber: 9
                }, undefined)
            ]
        }, void 0, true, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/config-sidebar/config-sidebar.tsx",
            lineNumber: 194,
            columnNumber: 7
        }, undefined)
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/config-sidebar/config-sidebar.tsx",
        lineNumber: 184,
        columnNumber: 5
    }, undefined);
};
_s(ConfigSidebar, "RTShtdWUOvS0jh511TdLgI7D6ws=", false, function() {
    return [
        _providers_config_provider__rspack_import_7.useConfigContext,
        _hooks_use_data_hub_config__rspack_import_8.useDataHubConfig,
        _pimcore_studio_ui_bundle_app__rspack_import_4.useTranslation,
        _config_sidebar_styles__rspack_import_12.useStyles
    ];
});
_c = ConfigSidebar;
var _c;
$RefreshReg$(_c, "ConfigSidebar");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/config/components/export-button/export-button.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  ExportButton: () => (ExportButton)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_3);
/* import */ var _utils_get_export_url__rspack_import_4 = __webpack_require__("./js/src/modules/config/utils/get-export-url.ts");
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



const ExportButton = (param)=>{
    let { configName, disabled } = param;
    _s();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation)();
    const handleExport = ()=>{
        window.location.href = (0,_utils_get_export_url__rspack_import_4.getExportUrl)(configName);
    };
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Tooltip, {
        title: t('tree.actions.export'),
        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.IconButton, {
            disabled: disabled,
            icon: {
                value: 'export'
            },
            onClick: handleExport
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/export-button/export-button.tsx",
            lineNumber: 30,
            columnNumber: 7
        }, undefined)
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/export-button/export-button.tsx",
        lineNumber: 29,
        columnNumber: 5
    }, undefined);
};
_s(ExportButton, "zlIdU9EjM2llFt74AbE2KsUJXyM=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation
    ];
});
_c = ExportButton;
var _c;
$RefreshReg$(_c, "ExportButton");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/config/components/export-button/index.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  ExportButton: () => (/* reexport safe */ _export_button__rspack_import_0.ExportButton)
});
/* import */ var _export_button__rspack_import_0 = __webpack_require__("./js/src/modules/config/components/export-button/export-button.tsx");
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
"./js/src/modules/config/components/field-width-container.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  FieldWidthContainer: () => (FieldWidthContainer)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_modules_element__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/modules/element");
/* import */ var _pimcore_studio_ui_bundle_modules_element__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_modules_element__rspack_import_2);
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

const FieldWidthContainer = (param)=>{
    let { children } = param;
    _s();
    const fieldWidth = (0,_pimcore_studio_ui_bundle_modules_element__rspack_import_2.useFieldWidth)();
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)("div", {
        style: {
            maxWidth: `${fieldWidth.large}px`,
            width: '100%'
        },
        children: children
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/field-width-container.tsx",
        lineNumber: 22,
        columnNumber: 5
    }, undefined);
};
_s(FieldWidthContainer, "CTJxdXncK7t9bRshhZ3AiLL2izM=", false, function() {
    return [
        _pimcore_studio_ui_bundle_modules_element__rspack_import_2.useFieldWidth
    ];
});
_c = FieldWidthContainer;
var _c;
$RefreshReg$(_c, "FieldWidthContainer");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/config/components/import-button/import-button.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  ImportButton: () => (ImportButton)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_api__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/api");
/* import */ var _pimcore_studio_ui_bundle_api__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_api__rspack_import_3);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_4 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_4_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_4);
/* import */ var _utils_tree_helpers__rspack_import_5 = __webpack_require__("./js/src/modules/config/utils/tree-helpers.ts");
/* import */ var lodash__rspack_import_6 = __webpack_require__("./node_modules/lodash/lodash.js");
/* import */ var lodash__rspack_import_6_default = /*#__PURE__*/__webpack_require__.n(lodash__rspack_import_6);
/* import */ var _config_service_ids__rspack_import_7 = __webpack_require__("./js/src/config/service-ids.ts");
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






const ImportButton = (param)=>{
    let { onRefresh, handleOpenConfig, disabled = false } = param;
    _s();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_4.useTranslation)();
    const adapterRegistry = _pimcore_studio_ui_bundle_app__rspack_import_4.container.get(_config_service_ids__rspack_import_7.bundleServiceIds["DataHub/DynamicTypes/Adapter/Registry"]);
    const handleImportSuccess = async (data)=>{
        const { data: updatedData } = await onRefresh();
        if (!(0,lodash__rspack_import_6.isUndefined)(updatedData === null || updatedData === void 0 ? void 0 : updatedData.items)) {
            const importedConfig = (0,_utils_tree_helpers__rspack_import_5.findConfigInTree)(updatedData.items, (item)=>!(0,lodash__rspack_import_6.isUndefined)(item.id) && item.id === data.name);
            if (!(0,lodash__rspack_import_6.isUndefined)(importedConfig)) {
                handleOpenConfig(importedConfig);
            }
        }
        // Let the adapter run follow-up work (e.g. index generation) in a separate request,
        // now that the imported configuration is resolvable. No-op for adapters that don't override it.
        const adapter = adapterRegistry.getDynamicType(data.type, false);
        if (!(0,lodash__rspack_import_6.isNil)(adapter)) {
            await adapter.afterImport(data.name);
        }
    };
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.ImportModal, {
        accept: ".json,application/json",
        action: `${(0,_pimcore_studio_ui_bundle_api__rspack_import_3.getPrefix)()}/bundle/data-hub/config/import`,
        onUploadSuccess: handleImportSuccess,
        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Tooltip, {
            title: t('tree.actions.import'),
            children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.IconButton, {
                disabled: disabled,
                icon: {
                    value: 'import'
                },
                type: "link"
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/import-button/import-button.tsx",
                lineNumber: 70,
                columnNumber: 9
            }, undefined)
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/import-button/import-button.tsx",
            lineNumber: 69,
            columnNumber: 7
        }, undefined)
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/import-button/import-button.tsx",
        lineNumber: 64,
        columnNumber: 5
    }, undefined);
};
_s(ImportButton, "zlIdU9EjM2llFt74AbE2KsUJXyM=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_4.useTranslation
    ];
});
_c = ImportButton;
var _c;
$RefreshReg$(_c, "ImportButton");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/config/components/tabs/config-tab-content.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  ConfigTabContent: () => (ConfigTabContent)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var lodash__rspack_import_2 = __webpack_require__("./node_modules/lodash/lodash.js");
/* import */ var lodash__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(lodash__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_3);
/* import */ var _config_service_ids__rspack_import_4 = __webpack_require__("./js/src/config/service-ids.ts");
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



const ConfigTabContent = (param)=>{
    let { config, onDelete, modifiedConfigs, setModifiedConfigs, isActive } = param;
    _s();
    const adapterRegistry = _pimcore_studio_ui_bundle_app__rspack_import_3.container.get(_config_service_ids__rspack_import_4.bundleServiceIds["DataHub/DynamicTypes/Adapter/Registry"]);
    const handleChange = (0,react__rspack_import_1.useCallback)((isDirty)=>{
        setModifiedConfigs((prev)=>{
            const isCurrentlyModified = prev.includes(config.id);
            if (isDirty && !isCurrentlyModified) {
                return [
                    ...prev,
                    config.id
                ];
            } else if (!isDirty && isCurrentlyModified) {
                return prev.filter((id)=>id !== config.id);
            }
            return prev;
        });
    }, [
        config.id,
        setModifiedConfigs
    ]);
    const adapterType = config.adapter;
    if ((0,lodash__rspack_import_2.isUndefined)(adapterType)) {
        return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)("div", {
            children: "Unknown adapter type"
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/tabs/config-tab-content.tsx",
            lineNumber: 44,
            columnNumber: 12
        }, undefined);
    }
    try {
        const adapter = adapterRegistry.getDynamicType(adapterType, false);
        if ((0,lodash__rspack_import_2.isNil)(adapter)) {
            return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)("div", {
                children: [
                    "Adapter not found: ",
                    adapterType
                ]
            }, void 0, true, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/tabs/config-tab-content.tsx",
                lineNumber: 50,
                columnNumber: 14
            }, undefined);
        }
        return adapter.renderDetailView({
            configName: config.text,
            configId: config.id,
            isActive,
            hasStudioColumnConfig: config.studioColumnConfig,
            onChange: handleChange,
            onDelete
        });
    } catch (err) {
        console.error('Error rendering form:', err);
        return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)("div", {
            children: "Error rendering adapter form"
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/tabs/config-tab-content.tsx",
            lineNumber: 63,
            columnNumber: 12
        }, undefined);
    }
};
_s(ConfigTabContent, "y/H5GIiu8jog9Hni7mlqNguo+do=");
_c = ConfigTabContent;
var _c;
$RefreshReg$(_c, "ConfigTabContent");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/config/components/tabs/config-tabs.styles.tsx"(module, __webpack_exports__, __webpack_require__) {
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
    let { css, token } = param;
    return {
        tabsContainer: css`
      height: 100%;

      .ant-tabs-content-holder {
        flex: 1;
        min-height: 0;
        overflow: hidden;
      }

      .ant-tabs-content,
      .ant-tabs-tabpane {
        height: 100%;
      }
    `,
        tabs: css`
      .ant-tabs-tab {
        padding: ${token.paddingSM}px ${token.paddingXXS}px !important;
      }
    `
    };
});

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/config/components/tabs/config-tabs.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  ConfigTabs: () => (ConfigTabs)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var lodash__rspack_import_3 = __webpack_require__("./node_modules/lodash/lodash.js");
/* import */ var lodash__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(lodash__rspack_import_3);
/* import */ var _config_tab_content__rspack_import_4 = __webpack_require__("./js/src/modules/config/components/tabs/config-tab-content.tsx");
/* import */ var _config_tabs_styles__rspack_import_5 = __webpack_require__("./js/src/modules/config/components/tabs/config-tabs.styles.tsx");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_6 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_6_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_6);
/* import */ var _config_service_ids__rspack_import_7 = __webpack_require__("./js/src/config/service-ids.ts");
/* import */ var _hooks_use_data_hub_config__rspack_import_8 = __webpack_require__("./js/src/modules/config/hooks/use-data-hub-config.ts");
/* import */ var _providers_config_provider__rspack_import_9 = __webpack_require__("./js/src/modules/config/providers/config-provider.tsx");
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








const TabItem = (param)=>{
    let { config } = param;
    const adapterRegistry = _pimcore_studio_ui_bundle_app__rspack_import_6.container.get(_config_service_ids__rspack_import_7.bundleServiceIds["DataHub/DynamicTypes/Adapter/Registry"]);
    const adapterType = config.adapter;
    if ((0,lodash__rspack_import_3.isUndefined)(adapterType)) {
        return null;
    }
    const adapter = adapterRegistry.getDynamicType(adapterType, false);
    return (0,lodash__rspack_import_3.isUndefined)(adapter) ? null : /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Icon, {
        ...adapter.getIcon()
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/tabs/config-tabs.tsx",
        lineNumber: 42,
        columnNumber: 40
    }, undefined);
};
_c = TabItem;
const ConfigTabs = (param)=>{
    let { openedConfigs, activeTabKey, configurationsData, onChangeTab, onCloseTab, modifiedConfigs, setModifiedConfigs } = param;
    _s();
    const { styles } = (0,_config_tabs_styles__rspack_import_5.useStyles)();
    const { refetch } = (0,_providers_config_provider__rspack_import_9.useConfigContext)();
    const { handleDelete } = (0,_hooks_use_data_hub_config__rspack_import_8.useDataHubConfig)({
        refetch
    });
    const handleDeleteConfig = (0,react__rspack_import_1.useCallback)((configId)=>{
        const config = openedConfigs.find((c)=>c.id === configId);
        if (!(0,lodash__rspack_import_3.isNil)(config)) {
            handleDelete(config, ()=>{
                onCloseTab(configId);
            });
        }
    }, [
        openedConfigs,
        handleDelete,
        onCloseTab
    ]);
    const tabItems = (0,react__rspack_import_1.useMemo)(()=>{
        // Recursively collect all config IDs from the tree
        const collectConfigIds = (items)=>{
            const ids = new Set();
            items.forEach((item)=>{
                ids.add(item.id);
                if (!(0,lodash__rspack_import_3.isNil)(item.children)) {
                    collectConfigIds(item.children).forEach((id)=>ids.add(id));
                }
            });
            return ids;
        };
        const existingConfigIds = (0,lodash__rspack_import_3.isNil)(configurationsData === null || configurationsData === void 0 ? void 0 : configurationsData.items) ? new Set() : collectConfigIds(configurationsData.items);
        return openedConfigs.filter((config)=>!(0,lodash__rspack_import_3.isNil)(existingConfigIds) && existingConfigIds.has(config.id)).map((config)=>({
                key: config.id,
                label: `${config.text} ${modifiedConfigs.includes(config.id) ? '*' : ''}`,
                icon: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(TabItem, {
                    config: config
                }, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/tabs/config-tabs.tsx",
                    lineNumber: 89,
                    columnNumber: 15
                }, undefined),
                children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_config_tab_content__rspack_import_4.ConfigTabContent, {
                    config: config,
                    isActive: activeTabKey === config.id,
                    modifiedConfigs: modifiedConfigs,
                    onDelete: ()=>{
                        handleDeleteConfig(config.id);
                    },
                    setModifiedConfigs: setModifiedConfigs
                }, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/tabs/config-tabs.tsx",
                    lineNumber: 90,
                    columnNumber: 19
                }, undefined)
            }));
    }, [
        configurationsData,
        openedConfigs,
        modifiedConfigs,
        setModifiedConfigs,
        activeTabKey,
        handleDeleteConfig
    ]);
    if ((0,lodash__rspack_import_3.isUndefined)(activeTabKey)) {
        return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Content, {
            none: true
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/tabs/config-tabs.tsx",
            lineNumber: 101,
            columnNumber: 12
        }, undefined);
    }
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Tabs, {
        activeKey: activeTabKey,
        className: styles.tabs,
        hasStickyHeader: true,
        items: tabItems,
        onChange: onChangeTab,
        onClose: onCloseTab,
        rootClassName: styles.tabsContainer
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/components/tabs/config-tabs.tsx",
        lineNumber: 105,
        columnNumber: 5
    }, undefined);
};
_s(ConfigTabs, "2o/YLb5nRsDf1zi2BBOIDsu9MYA=", false, function() {
    return [
        _config_tabs_styles__rspack_import_5.useStyles,
        _providers_config_provider__rspack_import_9.useConfigContext,
        _hooks_use_data_hub_config__rspack_import_8.useDataHubConfig
    ];
});
_c1 = ConfigTabs;
var _c, _c1;
$RefreshReg$(_c, "TabItem");
$RefreshReg$(_c1, "ConfigTabs");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/config/config-api-slice-enhanced.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  api: () => (api),
  useBundleDataHubConfigAddMutation: () => (useBundleDataHubConfigAddMutation),
  useBundleDataHubConfigCloneMutation: () => (useBundleDataHubConfigCloneMutation),
  useBundleDataHubConfigCollectionQuery: () => (useBundleDataHubConfigCollectionQuery),
  useBundleDataHubConfigDeleteMutation: () => (useBundleDataHubConfigDeleteMutation),
  useBundleDataHubConfigExportQuery: () => (useBundleDataHubConfigExportQuery),
  useBundleDataHubConfigGetQuery: () => (useBundleDataHubConfigGetQuery),
  useBundleDataHubConfigImportMutation: () => (useBundleDataHubConfigImportMutation),
  useBundleDataHubConfigUpdateMutation: () => (useBundleDataHubConfigUpdateMutation)
});
/* import */ var _config_api_slice_gen__rspack_import_0 = __webpack_require__("./js/src/modules/config/config-api-slice.gen.ts");
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
const api = _config_api_slice_gen__rspack_import_0.api.enhanceEndpoints({
    addTagTypes: [
        'DataHubConfigs'
    ],
    endpoints: {
        bundleDataHubConfigCollection: {
            providesTags: [
                'DataHubConfigs'
            ]
        },
        bundleDataHubConfigAdd: {
            invalidatesTags: [
                'DataHubConfigs'
            ]
        },
        bundleDataHubConfigClone: {
            invalidatesTags: [
                'DataHubConfigs'
            ]
        },
        bundleDataHubConfigDelete: {
            invalidatesTags: [
                'DataHubConfigs'
            ]
        },
        bundleDataHubConfigGet: {
            providesTags: []
        },
        bundleDataHubConfigExport: {
            providesTags: (result, error, arg)=>[
                    {
                        type: 'DataHubConfigs',
                        id: arg.name
                    }
                ]
        },
        bundleDataHubConfigImport: {
            invalidatesTags: [
                'DataHubConfigs'
            ]
        },
        bundleDataHubConfigUpdate: {
            invalidatesTags: [
                'DataHubConfigs'
            ]
        }
    }
});
const { useBundleDataHubConfigCollectionQuery, useBundleDataHubConfigAddMutation, useBundleDataHubConfigCloneMutation, useBundleDataHubConfigDeleteMutation, useBundleDataHubConfigGetQuery, useBundleDataHubConfigExportQuery, useBundleDataHubConfigImportMutation, useBundleDataHubConfigUpdateMutation } = api;

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/config/config-api-slice.gen.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  addTagTypes: () => (addTagTypes),
  api: () => (injectedRtkApi),
  useBundleDataHubConfigAddMutation: () => (useBundleDataHubConfigAddMutation),
  useBundleDataHubConfigCloneMutation: () => (useBundleDataHubConfigCloneMutation),
  useBundleDataHubConfigCollectionQuery: () => (useBundleDataHubConfigCollectionQuery),
  useBundleDataHubConfigDeleteMutation: () => (useBundleDataHubConfigDeleteMutation),
  useBundleDataHubConfigExportQuery: () => (useBundleDataHubConfigExportQuery),
  useBundleDataHubConfigGetQuery: () => (useBundleDataHubConfigGetQuery),
  useBundleDataHubConfigImportMutation: () => (useBundleDataHubConfigImportMutation),
  useBundleDataHubConfigUpdateMutation: () => (useBundleDataHubConfigUpdateMutation)
});
/* import */ var _pimcore_studio_ui_bundle_api__rspack_import_0 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/api");
/* import */ var _pimcore_studio_ui_bundle_api__rspack_import_0_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_api__rspack_import_0);
/* provided dependency */ var $ReactRefreshRuntime$ = __webpack_require__("./node_modules/@rspack/plugin-react-refresh/client/reactRefresh.js");

const addTagTypes = [
    "Bundle Data Hub"
];
const injectedRtkApi = _pimcore_studio_ui_bundle_api__rspack_import_0.api.enhanceEndpoints({
    addTagTypes
}).injectEndpoints({
    endpoints: (build)=>({
            bundleDataHubConfigAdd: build.mutation({
                query: (queryArg)=>({
                        url: `/pimcore-studio/api/bundle/data-hub/config/add`,
                        method: "POST",
                        params: {
                            name: queryArg.name,
                            type: queryArg["type"],
                            path: queryArg.path
                        }
                    }),
                invalidatesTags: [
                    "Bundle Data Hub"
                ]
            }),
            bundleDataHubConfigClone: build.mutation({
                query: (queryArg)=>({
                        url: `/pimcore-studio/api/bundle/data-hub/config/clone`,
                        method: "POST",
                        params: {
                            name: queryArg.name,
                            originalName: queryArg.originalName
                        }
                    }),
                invalidatesTags: [
                    "Bundle Data Hub"
                ]
            }),
            bundleDataHubConfigCollection: build.query({
                query: ()=>({
                        url: `/pimcore-studio/api/bundle/data-hub/config`
                    }),
                providesTags: [
                    "Bundle Data Hub"
                ]
            }),
            bundleDataHubConfigDelete: build.mutation({
                query: (queryArg)=>({
                        url: `/pimcore-studio/api/bundle/data-hub/config/delete/${queryArg.name}`,
                        method: "DELETE"
                    }),
                invalidatesTags: [
                    "Bundle Data Hub"
                ]
            }),
            bundleDataHubConfigExport: build.query({
                query: (queryArg)=>({
                        url: `/pimcore-studio/api/bundle/data-hub/config/${queryArg.name}/export`
                    }),
                providesTags: [
                    "Bundle Data Hub"
                ]
            }),
            bundleDataHubConfigGet: build.query({
                query: (queryArg)=>({
                        url: `/pimcore-studio/api/bundle/data-hub/config/${queryArg.name}`
                    }),
                providesTags: [
                    "Bundle Data Hub"
                ]
            }),
            bundleDataHubConfigUpdate: build.mutation({
                query: (queryArg)=>({
                        url: `/pimcore-studio/api/bundle/data-hub/config/${queryArg.name}`,
                        method: "PUT",
                        body: queryArg.bundleDataHubUpdateConfiguration
                    }),
                invalidatesTags: [
                    "Bundle Data Hub"
                ]
            }),
            bundleDataHubConfigImport: build.mutation({
                query: (queryArg)=>({
                        url: `/pimcore-studio/api/bundle/data-hub/config/import`,
                        method: "POST",
                        body: queryArg.body
                    }),
                invalidatesTags: [
                    "Bundle Data Hub"
                ]
            })
        }),
    overrideExisting: false
});

const { useBundleDataHubConfigAddMutation, useBundleDataHubConfigCloneMutation, useBundleDataHubConfigCollectionQuery, useBundleDataHubConfigDeleteMutation, useBundleDataHubConfigExportQuery, useBundleDataHubConfigGetQuery, useBundleDataHubConfigUpdateMutation, useBundleDataHubConfigImportMutation } = injectedRtkApi;

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/config/config-container.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  ConfigContainer: () => (ConfigContainer)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _components_config_sidebar_config_sidebar__rspack_import_3 = __webpack_require__("./js/src/modules/config/components/config-sidebar/config-sidebar.tsx");
/* import */ var _components_tabs_config_tabs__rspack_import_4 = __webpack_require__("./js/src/modules/config/components/tabs/config-tabs.tsx");
/* import */ var _config_api_slice_enhanced__rspack_import_5 = __webpack_require__("./js/src/modules/config/config-api-slice-enhanced.ts");
/* import */ var _hooks_use_tab_manager__rspack_import_6 = __webpack_require__("./js/src/modules/config/hooks/use-tab-manager.ts");
/* import */ var _providers_config_provider__rspack_import_7 = __webpack_require__("./js/src/modules/config/providers/config-provider.tsx");
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






const ConfigContainer = ()=>{
    _s();
    const { data: configurationsData, isLoading, isFetching, refetch } = (0,_config_api_slice_enhanced__rspack_import_5.useBundleDataHubConfigCollectionQuery)();
    const { openedConfigs, activeTabKey, handleOpenConfig, handleCloseTab, handleChangeTab } = (0,_hooks_use_tab_manager__rspack_import_6.useTabManager)();
    const [expandedKeys, setExpandedKeys] = (0,react__rspack_import_1.useState)([]);
    const [modifiedConfigs, setModifiedConfigs] = (0,react__rspack_import_1.useState)([]);
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_providers_config_provider__rspack_import_7.ConfigProvider, {
        value: {
            configurationsData,
            isLoading,
            isFetching,
            refetch,
            expandedKeys,
            setExpandedKeys
        },
        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.ConfigLayout, {
            leftItem: {
                children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_components_config_sidebar_config_sidebar__rspack_import_3.ConfigSidebar, {
                    handleOpenConfig: handleOpenConfig
                }, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/config-container.tsx",
                    lineNumber: 39,
                    columnNumber: 13
                }, undefined)
            },
            rightItem: {
                children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_components_tabs_config_tabs__rspack_import_4.ConfigTabs, {
                    activeTabKey: activeTabKey,
                    configurationsData: configurationsData,
                    modifiedConfigs: modifiedConfigs,
                    onChangeTab: handleChangeTab,
                    onCloseTab: handleCloseTab,
                    openedConfigs: openedConfigs,
                    setModifiedConfigs: setModifiedConfigs
                }, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/config-container.tsx",
                    lineNumber: 44,
                    columnNumber: 13
                }, undefined)
            }
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/config-container.tsx",
            lineNumber: 36,
            columnNumber: 7
        }, undefined)
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/config-container.tsx",
        lineNumber: 26,
        columnNumber: 5
    }, undefined);
};
_s(ConfigContainer, "c4IWg49fO7Y6Jdnrs/0qMDb8D/A=", false, function() {
    return [
        _config_api_slice_enhanced__rspack_import_5.useBundleDataHubConfigCollectionQuery,
        _hooks_use_tab_manager__rspack_import_6.useTabManager
    ];
});
_c = ConfigContainer;
var _c;
$RefreshReg$(_c, "ConfigContainer");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/config/dynamic-types/dynamic-type-data-hub-adapter-abstract.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  DynamicTypeDataHubAdapterAbstract: () => (DynamicTypeDataHubAdapterAbstract)
});
/* import */ var _swc_helpers_ts_decorate__rspack_import_1 = __webpack_require__("./node_modules/tslib/tslib.es6.mjs");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_0 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_0_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_0);
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

class DynamicTypeDataHubAdapterAbstract {
    getNameTranslationKey() {
        return `data-hub.adapter.${this.id}`;
    }
    /**
   * Hook invoked after a configuration of this adapter type has been imported.
   * Adapters can override this to run follow-up work in a separate request once the
   * imported configuration is resolvable (e.g. building a search index). Defaults to a no-op.
   */ async afterImport(configName) {}
}
DynamicTypeDataHubAdapterAbstract = (0,_swc_helpers_ts_decorate__rspack_import_1.__decorate)([
    (0,_pimcore_studio_ui_bundle_app__rspack_import_0.injectable)()
], DynamicTypeDataHubAdapterAbstract);

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/config/dynamic-types/dynamic-type-data-hub-adapter-registry.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  DynamicTypeDataHubAdapterRegistry: () => (DynamicTypeDataHubAdapterRegistry)
});
/* import */ var _swc_helpers_ts_decorate__rspack_import_2 = __webpack_require__("./node_modules/tslib/tslib.es6.mjs");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_0 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_0_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_0);
/* import */ var _pimcore_studio_ui_bundle_modules_element__rspack_import_1 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/modules/element");
/* import */ var _pimcore_studio_ui_bundle_modules_element__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_modules_element__rspack_import_1);
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

class DynamicTypeDataHubAdapterRegistry extends _pimcore_studio_ui_bundle_modules_element__rspack_import_1.DynamicTypeRegistryAbstract {
}
DynamicTypeDataHubAdapterRegistry = (0,_swc_helpers_ts_decorate__rspack_import_2.__decorate)([
    (0,_pimcore_studio_ui_bundle_app__rspack_import_0.injectable)()
], DynamicTypeDataHubAdapterRegistry);

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/config/hooks/use-data-hub-config.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  useDataHubConfig: () => (useDataHubConfig)
});
/* import */ var react__rspack_import_0 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_0_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_0);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_1 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_2);
/* import */ var lodash__rspack_import_3 = __webpack_require__("./node_modules/lodash/lodash.js");
/* import */ var lodash__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(lodash__rspack_import_3);
/* import */ var _config_api_slice_enhanced__rspack_import_4 = __webpack_require__("./js/src/modules/config/config-api-slice-enhanced.ts");
/* import */ var _utils_tree_helpers__rspack_import_5 = __webpack_require__("./js/src/modules/config/utils/tree-helpers.ts");
/* import */ var _pimcore_studio_ui_bundle_modules_app__rspack_import_6 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/modules/app");
/* import */ var _pimcore_studio_ui_bundle_modules_app__rspack_import_6_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_modules_app__rspack_import_6);
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






const useDataHubConfig = (param)=>{
    let { refetch } = param;
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_2.useTranslation)();
    const modal = (0,_pimcore_studio_ui_bundle_components__rspack_import_1.useFormModal)();
    const [addConfig, { error: addError }] = (0,_config_api_slice_enhanced__rspack_import_4.useBundleDataHubConfigAddMutation)();
    const [cloneConfig, { error: cloneError }] = (0,_config_api_slice_enhanced__rspack_import_4.useBundleDataHubConfigCloneMutation)();
    const [deleteConfig, { error: deleteError }] = (0,_config_api_slice_enhanced__rspack_import_4.useBundleDataHubConfigDeleteMutation)();
    const validateConfigName = async (_rule, value)=>{
        if (!(0,lodash__rspack_import_3.isString)(value) || value.trim().length === 0) {
            throw new Error(t('data-hub.config.name-required'));
        }
        if (value.length < 3) {
            throw new Error(t('data-hub.config.name-min-length'));
        }
        if (value.length > 80) {
            throw new Error(t('data-hub.config.name-max-length'));
        }
        if (!/^[a-zA-Z0-9_-]+$/.test(value)) {
            throw new Error(t('data-hub.config.name-pattern'));
        }
        await Promise.resolve();
    };
    (0,react__rspack_import_0.useEffect)(()=>{
        if (!(0,lodash__rspack_import_3.isNil)(addError)) {
            (0,_pimcore_studio_ui_bundle_modules_app__rspack_import_6.trackError)(new _pimcore_studio_ui_bundle_modules_app__rspack_import_6.ApiError(addError));
        }
    }, [
        addError
    ]);
    (0,react__rspack_import_0.useEffect)(()=>{
        if (!(0,lodash__rspack_import_3.isNil)(cloneError)) {
            (0,_pimcore_studio_ui_bundle_modules_app__rspack_import_6.trackError)(new _pimcore_studio_ui_bundle_modules_app__rspack_import_6.ApiError(cloneError));
        }
    }, [
        cloneError
    ]);
    (0,react__rspack_import_0.useEffect)(()=>{
        if (!(0,lodash__rspack_import_3.isNil)(deleteError)) {
            const apiError = new _pimcore_studio_ui_bundle_modules_app__rspack_import_6.ApiError(deleteError);
            (0,_pimcore_studio_ui_bundle_modules_app__rspack_import_6.trackError)(apiError);
        }
    }, [
        deleteError
    ]);
    const handleAdd = (0,react__rspack_import_0.useCallback)((adapterType, onSuccess)=>{
        modal.input({
            label: t('data-hub.add.name'),
            rule: {
                required: true,
                validator: validateConfigName
            },
            onOk: async (value)=>{
                const result = await addConfig({
                    name: value,
                    type: adapterType
                });
                if ((0,lodash__rspack_import_3.has)(result, 'error')) {
                    return;
                }
                const { data: updatedData } = await refetch();
                if (!(0,lodash__rspack_import_3.isUndefined)(updatedData === null || updatedData === void 0 ? void 0 : updatedData.items)) {
                    const addedConfig = (0,_utils_tree_helpers__rspack_import_5.findConfigInTree)(updatedData.items, (item)=>!(0,lodash__rspack_import_3.isUndefined)(item.id) && item.id === value);
                    if (!(0,lodash__rspack_import_3.isUndefined)(addedConfig) && !(0,lodash__rspack_import_3.isNil)(onSuccess)) {
                        onSuccess(addedConfig);
                    }
                }
            }
        });
    }, [
        addConfig,
        refetch,
        modal
    ]);
    const handleClone = (0,react__rspack_import_0.useCallback)((config, onSuccess)=>{
        if ((0,lodash__rspack_import_3.isNil)(config)) return;
        modal.input({
            label: t('data-hub.clone.name'),
            rule: {
                required: true,
                validator: validateConfigName
            },
            onOk: async (value)=>{
                const configId = String(config.id ?? '');
                const result = await cloneConfig({
                    name: value,
                    originalName: configId
                });
                if ((0,lodash__rspack_import_3.has)(result, 'error')) {
                    return;
                }
                const { data: updatedData } = await refetch();
                if (!(0,lodash__rspack_import_3.isUndefined)(updatedData === null || updatedData === void 0 ? void 0 : updatedData.items)) {
                    const clonedConfig = (0,_utils_tree_helpers__rspack_import_5.findConfigInTree)(updatedData.items, (item)=>!(0,lodash__rspack_import_3.isUndefined)(item.id) && item.id === value);
                    if (!(0,lodash__rspack_import_3.isUndefined)(clonedConfig) && !(0,lodash__rspack_import_3.isNil)(onSuccess)) {
                        onSuccess(clonedConfig);
                    }
                }
            }
        });
    }, [
        cloneConfig,
        refetch,
        modal
    ]);
    const handleDelete = (0,react__rspack_import_0.useCallback)((config, onSuccess)=>{
        if ((0,lodash__rspack_import_3.isNil)(config)) return;
        modal.confirm({
            title: t('delete'),
            content: t('data-hub.delete.confirm', {
                name: config.text
            }),
            onOk: async ()=>{
                const configId = String(config.id ?? '');
                const result = await deleteConfig({
                    name: configId
                });
                if ((0,lodash__rspack_import_3.has)(result, 'error')) {
                    return;
                }
                await refetch();
                if (!(0,lodash__rspack_import_3.isNil)(onSuccess)) {
                    onSuccess();
                }
            }
        });
    }, [
        deleteConfig,
        refetch,
        modal
    ]);
    return {
        handleAdd,
        handleClone,
        handleDelete
    };
};

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/config/hooks/use-tab-manager.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  useTabManager: () => (useTabManager)
});
/* import */ var react__rspack_import_0 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_0_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_0);
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
const useTabManager = ()=>{
    const [openedConfigs, setOpenedConfigs] = (0,react__rspack_import_0.useState)([]);
    const [activeTabKey, setActiveTabKey] = (0,react__rspack_import_0.useState)(undefined);
    const handleOpenConfig = (0,react__rspack_import_0.useCallback)((config)=>{
        const isAlreadyOpened = openedConfigs.some((item)=>item.id === config.id);
        if (!isAlreadyOpened) {
            setOpenedConfigs((prev)=>[
                    ...prev,
                    config
                ]);
        }
        setActiveTabKey(config.id);
    }, [
        openedConfigs
    ]);
    const handleCloseTab = (0,react__rspack_import_0.useCallback)((key)=>{
        setOpenedConfigs((prev)=>{
            const targetIndex = prev.findIndex((tab)=>(tab === null || tab === void 0 ? void 0 : tab.id) === key);
            const updatedConfigs = prev.filter((config)=>config.id !== key);
            if (key === activeTabKey) {
                const prevTab = prev[targetIndex - 1];
                const nextTab = prev[targetIndex + 1];
                setActiveTabKey((prevTab === null || prevTab === void 0 ? void 0 : prevTab.id) ?? (nextTab === null || nextTab === void 0 ? void 0 : nextTab.id));
            }
            return updatedConfigs;
        });
    }, [
        activeTabKey
    ]);
    const handleChangeTab = (0,react__rspack_import_0.useCallback)((key)=>{
        setActiveTabKey(key);
    }, []);
    return {
        openedConfigs,
        activeTabKey,
        handleOpenConfig,
        handleCloseTab,
        handleChangeTab
    };
};

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/config/providers/config-provider.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  ConfigProvider: () => (ConfigProvider),
  useConfigContext: () => (useConfigContext)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var lodash__rspack_import_2 = __webpack_require__("./node_modules/lodash/lodash.js");
/* import */ var lodash__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(lodash__rspack_import_2);
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

const ConfigContext = /*#__PURE__*/ (0,react__rspack_import_1.createContext)(undefined);
const ConfigProvider = (param)=>{
    let { children, value } = param;
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(ConfigContext.Provider, {
        value: value,
        children: children
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/providers/config-provider.tsx",
        lineNumber: 33,
        columnNumber: 5
    }, undefined);
};
_c = ConfigProvider;
const useConfigContext = ()=>{
    _s();
    const context = (0,react__rspack_import_1.useContext)(ConfigContext);
    if ((0,lodash__rspack_import_2.isUndefined)(context)) {
        throw new Error('useConfigContext must be used within ConfigProvider');
    }
    return context;
};
_s(useConfigContext, "b9L3QQ+jgeyIrH0NfHrJ8nn7VMU=");
var _c;
$RefreshReg$(_c, "ConfigProvider");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/config/users-api-slice.gen.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  addTagTypes: () => (addTagTypes),
  api: () => (injectedRtkApi),
  useBundleDataHubUsersCollectionQuery: () => (useBundleDataHubUsersCollectionQuery)
});
/* import */ var _pimcore_studio_ui_bundle_api__rspack_import_0 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/api");
/* import */ var _pimcore_studio_ui_bundle_api__rspack_import_0_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_api__rspack_import_0);
/* provided dependency */ var $ReactRefreshRuntime$ = __webpack_require__("./node_modules/@rspack/plugin-react-refresh/client/reactRefresh.js");

const addTagTypes = [
    "Bundle Data Hub"
];
const injectedRtkApi = _pimcore_studio_ui_bundle_api__rspack_import_0.api.enhanceEndpoints({
    addTagTypes
}).injectEndpoints({
    endpoints: (build)=>({
            bundleDataHubUsersCollection: build.query({
                query: (queryArg)=>({
                        url: `/pimcore-studio/api/bundle/data-hub/users`,
                        params: {
                            type: queryArg["type"]
                        }
                    }),
                providesTags: [
                    "Bundle Data Hub"
                ]
            })
        }),
    overrideExisting: false
});

const { useBundleDataHubUsersCollectionQuery } = injectedRtkApi;

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/config/utils/adapter-helpers.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  hasValidAdapter: () => (hasValidAdapter)
});
/* import */ var lodash__rspack_import_0 = __webpack_require__("./node_modules/lodash/lodash.js");
/* import */ var lodash__rspack_import_0_default = /*#__PURE__*/__webpack_require__.n(lodash__rspack_import_0);
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
const hasValidAdapter = (adapterType, adapterRegistry)=>{
    if ((0,lodash__rspack_import_0.isUndefined)(adapterType)) return false;
    return adapterRegistry.hasDynamicType(adapterType);
};

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/config/utils/get-export-url.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  getExportUrl: () => (getExportUrl)
});
/* import */ var _pimcore_studio_ui_bundle_api__rspack_import_0 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/api");
/* import */ var _pimcore_studio_ui_bundle_api__rspack_import_0_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_api__rspack_import_0);
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
const getExportUrl = (configName)=>{
    return `${(0,_pimcore_studio_ui_bundle_api__rspack_import_0.getPrefix)()}/bundle/data-hub/config/${configName}/export`;
};

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/config/utils/tree-helpers.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  filterConfigsRecursive: () => (filterConfigsRecursive),
  findConfigById: () => (findConfigById),
  findConfigInTree: () => (findConfigInTree)
});
/* import */ var lodash__rspack_import_0 = __webpack_require__("./node_modules/lodash/lodash.js");
/* import */ var lodash__rspack_import_0_default = /*#__PURE__*/__webpack_require__.n(lodash__rspack_import_0);
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
const findConfigById = (id, items)=>{
    if ((0,lodash__rspack_import_0.isNil)(items) || !Array.isArray(items)) {
        return null;
    }
    for (const item of items){
        if (!(0,lodash__rspack_import_0.isNil)(item.id) && String(item.id) === String(id)) return item;
        if (!(0,lodash__rspack_import_0.isUndefined)(item.children) && !(0,lodash__rspack_import_0.isNil)(item.children)) {
            const found = findConfigById(id, item.children);
            if (!(0,lodash__rspack_import_0.isNil)(found)) return found;
        }
    }
    return null;
};
const findConfigInTree = (items, predicate)=>{
    if ((0,lodash__rspack_import_0.isNil)(items) || !Array.isArray(items)) {
        return undefined;
    }
    for (const item of items){
        if (predicate(item)) return item;
        if (!(0,lodash__rspack_import_0.isUndefined)(item.children)) {
            const found = findConfigInTree(item.children, predicate);
            if (!(0,lodash__rspack_import_0.isUndefined)(found)) return found;
        }
    }
    return undefined;
};
const filterConfigsRecursive = (items, searchValue)=>{
    if (!(0,lodash__rspack_import_0.isArray)(items)) {
        return [];
    }
    return items.reduce((acc, item)=>{
        const matchesSearch = item.text.toLowerCase().includes(searchValue.toLowerCase());
        const filteredChildren = filterConfigsRecursive(item.children, searchValue);
        if (matchesSearch || filteredChildren.length > 0) {
            acc.push({
                ...item,
                children: filteredChildren.length > 0 ? filteredChildren : item.children
            });
        }
        return acc;
    }, []);
};

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/components/inline-dropdown-panel/inline-dropdown-panel.styles.tsx"(module, __webpack_exports__, __webpack_require__) {
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
        panel: css`
      position: absolute;
      z-index: ${token.zIndexPopupBase};
      min-width: 400px;
      background-color: ${token.colorBgContainer};
      padding: ${token.padding}px;
      box-shadow: ${token.boxShadowSecondary};
      border-radius: ${token.borderRadius}px;
    `
    };
});

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/components/inline-dropdown-panel/inline-dropdown-panel.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  InlineDropdownPanel: () => (InlineDropdownPanel)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _inline_dropdown_panel_styles__rspack_import_2 = __webpack_require__("./js/src/modules/graphql/components/inline-dropdown-panel/inline-dropdown-panel.styles.tsx");
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

const InlineDropdownPanel = (param)=>{
    let { children } = param;
    _s();
    const { styles } = (0,_inline_dropdown_panel_styles__rspack_import_2.useStyles)();
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)("div", {
        className: styles.panel,
        children: children
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/inline-dropdown-panel/inline-dropdown-panel.tsx",
        lineNumber: 22,
        columnNumber: 5
    }, undefined);
};
_s(InlineDropdownPanel, "1BGFRu6BGAbhzJ8kKgs1GUjvI6w=", false, function() {
    return [
        _inline_dropdown_panel_styles__rspack_import_2.useStyles
    ];
});
_c = InlineDropdownPanel;
var _c;
$RefreshReg$(_c, "InlineDropdownPanel");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/components/tabs/general-tab.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  GeneralTab: () => (GeneralTab)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_3);
/* provided dependency */ var $ReactRefreshRuntime$ = __webpack_require__("./node_modules/@rspack/plugin-react-refresh/client/reactRefresh.js");
var _this = undefined;

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


const GeneralTab = function() {
    let { adapterTypeLabel } = arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : {};
    _s();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation)();
    const typeLabel = adapterTypeLabel ?? t('data-hub.adapter.graphql');
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.FormKit.Panel, {
        contentPadding: "extra-small",
        children: [
            /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                name: "active",
                valuePropName: "checked",
                children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Switch, {
                    labelRight: t('data-hub.config.active')
                }, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/general-tab.tsx",
                    lineNumber: 29,
                    columnNumber: 9
                }, _this)
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/general-tab.tsx",
                lineNumber: 25,
                columnNumber: 7
            }, _this),
            /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                label: t('data-hub.config.type'),
                children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Input, {
                    disabled: true,
                    value: typeLabel
                }, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/general-tab.tsx",
                    lineNumber: 33,
                    columnNumber: 9
                }, _this)
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/general-tab.tsx",
                lineNumber: 32,
                columnNumber: 7
            }, _this),
            /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                label: t('data-hub.config.name'),
                name: "name",
                children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Input, {
                    disabled: true
                }, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/general-tab.tsx",
                    lineNumber: 43,
                    columnNumber: 9
                }, _this)
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/general-tab.tsx",
                lineNumber: 39,
                columnNumber: 7
            }, _this),
            /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                label: t('data-hub.config.description'),
                name: "description",
                children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.TextArea, {
                    rows: 4
                }, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/general-tab.tsx",
                    lineNumber: 50,
                    columnNumber: 9
                }, _this)
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/general-tab.tsx",
                lineNumber: 46,
                columnNumber: 7
            }, _this),
            /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                label: t('data-hub.config.group'),
                name: "group",
                children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Input, {}, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/general-tab.tsx",
                    lineNumber: 57,
                    columnNumber: 9
                }, _this)
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/general-tab.tsx",
                lineNumber: 53,
                columnNumber: 7
            }, _this)
        ]
    }, void 0, true, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/general-tab.tsx",
        lineNumber: 24,
        columnNumber: 5
    }, _this);
};
_s(GeneralTab, "zlIdU9EjM2llFt74AbE2KsUJXyM=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation
    ];
});
_c = GeneralTab;
var _c;
$RefreshReg$(_c, "GeneralTab");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/components/tabs/permissions-tab.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  PermissionsTab: () => (PermissionsTab)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _permissions_tab_permission_grid__rspack_import_3 = __webpack_require__("./js/src/modules/graphql/components/tabs/permissions-tab/permission-grid.tsx");
/* import */ var _config_components_field_width_container__rspack_import_4 = __webpack_require__("./js/src/modules/config/components/field-width-container.tsx");
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



const PermissionsTab = (param)=>{
    let { isWriteable = true } = param;
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.FormKit.Panel, {
        contentPadding: "extra-small",
        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_config_components_field_width_container__rspack_import_4.FieldWidthContainer, {
            children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Flex, {
                gap: "small",
                vertical: true,
                children: [
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                        name: [
                            'permissions',
                            'roles'
                        ],
                        noStyle: true,
                        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_permissions_tab_permission_grid__rspack_import_3.PermissionGrid, {
                            isWriteable: isWriteable,
                            type: "roles"
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/permissions-tab.tsx",
                            lineNumber: 32,
                            columnNumber: 13
                        }, undefined)
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/permissions-tab.tsx",
                        lineNumber: 28,
                        columnNumber: 11
                    }, undefined),
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                        name: [
                            'permissions',
                            'users'
                        ],
                        noStyle: true,
                        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_permissions_tab_permission_grid__rspack_import_3.PermissionGrid, {
                            isWriteable: isWriteable,
                            type: "users"
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/permissions-tab.tsx",
                            lineNumber: 41,
                            columnNumber: 13
                        }, undefined)
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/permissions-tab.tsx",
                        lineNumber: 37,
                        columnNumber: 11
                    }, undefined)
                ]
            }, void 0, true, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/permissions-tab.tsx",
                lineNumber: 24,
                columnNumber: 9
            }, undefined)
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/permissions-tab.tsx",
            lineNumber: 23,
            columnNumber: 7
        }, undefined)
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/permissions-tab.tsx",
        lineNumber: 22,
        columnNumber: 5
    }, undefined);
};
_c = PermissionsTab;
var _c;
$RefreshReg$(_c, "PermissionsTab");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/components/tabs/permissions-tab/permission-accordion.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  PermissionAccordion: () => (PermissionAccordion)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_3);
/* import */ var lodash__rspack_import_4 = __webpack_require__("./node_modules/lodash/lodash.js");
/* import */ var lodash__rspack_import_4_default = /*#__PURE__*/__webpack_require__.n(lodash__rspack_import_4);
/* import */ var _inline_dropdown_panel_inline_dropdown_panel__rspack_import_5 = __webpack_require__("./js/src/modules/graphql/components/inline-dropdown-panel/inline-dropdown-panel.tsx");
/* import */ var _config_users_api_slice_gen__rspack_import_6 = __webpack_require__("./js/src/modules/config/users-api-slice.gen.ts");
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





const PermissionAccordion = (param)=>{
    let { type, value = [], onChange } = param;
    _s();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation)();
    const [openDropdown, setOpenDropdown] = (0,react__rspack_import_1.useState)(false);
    const [selectedItems, setSelectedItems] = (0,react__rspack_import_1.useState)([]);
    const isRoles = type === 'roles';
    const { data: roleList } = (0,_config_users_api_slice_gen__rspack_import_6.useBundleDataHubUsersCollectionQuery)({
        type: 'role'
    }, {
        skip: !isRoles,
        refetchOnMountOrArgChange: true
    });
    const { data: userList } = (0,_config_users_api_slice_gen__rspack_import_6.useBundleDataHubUsersCollectionQuery)({
        type: 'user'
    }, {
        skip: isRoles,
        refetchOnMountOrArgChange: true
    });
    const items = isRoles ? roleList === null || roleList === void 0 ? void 0 : roleList.items : userList === null || userList === void 0 ? void 0 : userList.items;
    const currentNames = (0,react__rspack_import_1.useMemo)(()=>value.map((p)=>p.name), [
        value
    ]);
    const options = (0,react__rspack_import_1.useMemo)(()=>{
        return (items === null || items === void 0 ? void 0 : items.filter((item)=>{
            const name = item.text;
            return !currentNames.includes(name);
        }).map((item)=>({
                value: item.id,
                label: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Flex, {
                    align: "center",
                    gap: "mini",
                    children: [
                        /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Icon, {
                            value: isRoles ? 'shield' : 'user'
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/permissions-tab/permission-accordion.tsx",
                            lineNumber: 56,
                            columnNumber: 13
                        }, undefined),
                        item.text
                    ]
                }, void 0, true, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/permissions-tab/permission-accordion.tsx",
                    lineNumber: 52,
                    columnNumber: 11
                }, undefined),
                searchValue: item.text
            }))) ?? [];
    }, [
        items,
        currentNames,
        isRoles
    ]);
    const createPermission = (id)=>{
        const item = items === null || items === void 0 ? void 0 : items.find((i)=>i.id === id);
        if ((0,lodash__rspack_import_4.isNil)(item)) return undefined;
        const existingPermission = value.find((p)=>p.name === item.text);
        if (!(0,lodash__rspack_import_4.isNil)(existingPermission)) return undefined;
        return {
            id: item.id,
            name: item.text,
            read: true,
            update: false,
            delete: false
        };
    };
    const handleOpen = ()=>{
        setSelectedItems([]);
        setOpenDropdown(true);
    };
    const handleCancel = ()=>{
        setSelectedItems([]);
        setOpenDropdown(false);
    };
    const handleApply = ()=>{
        if (selectedItems.length > 0) {
            const newPermissions = selectedItems.map((id)=>createPermission(id)).filter((p)=>!(0,lodash__rspack_import_4.isNil)(p));
            if (!(0,lodash__rspack_import_4.isNil)(onChange) && newPermissions.length > 0) {
                onChange([
                    ...value,
                    ...newPermissions
                ]);
            }
        }
        setSelectedItems([]);
        setOpenDropdown(false);
    };
    const accordionItem = (0,react__rspack_import_1.useMemo)(()=>({
            key: type,
            id: type,
            title: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(react_jsx_dev_runtime__rspack_import_0.Fragment, {
                children: t(isRoles ? 'data-hub.permissions.role-permissions' : 'data-hub.permissions.user-permissions')
            }, void 0, false),
            info: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(react_jsx_dev_runtime__rspack_import_0.Fragment, {
                children: [
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.IconTextButton, {
                        icon: {
                            value: 'plus-circle'
                        },
                        onClick: (e)=>{
                            e.stopPropagation();
                            handleOpen();
                        },
                        children: t('add')
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/permissions-tab/permission-accordion.tsx",
                        lineNumber: 110,
                        columnNumber: 9
                    }, undefined),
                    openDropdown && /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_inline_dropdown_panel_inline_dropdown_panel__rspack_import_5.InlineDropdownPanel, {
                        children: [
                            /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Select, {
                                listHeight: 150,
                                mode: "multiple",
                                onChange: (values)=>{
                                    setSelectedItems(values);
                                },
                                optionFilterProp: "searchValue",
                                options: options,
                                placeholder: t(isRoles ? 'data-hub.permissions.role' : 'data-hub.permissions.user'),
                                placement: "topLeft",
                                showSearch: true,
                                style: {
                                    width: '400px'
                                },
                                value: selectedItems
                            }, void 0, false, {
                                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/permissions-tab/permission-accordion.tsx",
                                lineNumber: 121,
                                columnNumber: 13
                            }, undefined),
                            /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Flex, {
                                gap: "small",
                                justify: "flex-end",
                                style: {
                                    marginTop: '12px'
                                },
                                children: [
                                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Button, {
                                        onClick: handleCancel,
                                        type: "default",
                                        children: t('button.cancel')
                                    }, void 0, false, {
                                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/permissions-tab/permission-accordion.tsx",
                                        lineNumber: 138,
                                        columnNumber: 15
                                    }, undefined),
                                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Button, {
                                        onClick: handleApply,
                                        type: "primary",
                                        children: t('button.apply')
                                    }, void 0, false, {
                                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/permissions-tab/permission-accordion.tsx",
                                        lineNumber: 144,
                                        columnNumber: 15
                                    }, undefined)
                                ]
                            }, void 0, true, {
                                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/permissions-tab/permission-accordion.tsx",
                                lineNumber: 133,
                                columnNumber: 13
                            }, undefined)
                        ]
                    }, void 0, true, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/permissions-tab/permission-accordion.tsx",
                        lineNumber: 120,
                        columnNumber: 11
                    }, undefined)
                ]
            }, void 0, true),
            children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.OperationalGrid.Grid, {}, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/permissions-tab/permission-accordion.tsx",
                lineNumber: 156,
                columnNumber: 7
            }, undefined)
        }), [
        type,
        openDropdown,
        options,
        selectedItems
    ]);
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Accordion, {
        activeKey: type,
        bordered: true,
        collapsible: "icon",
        items: [
            accordionItem
        ],
        size: "small",
        table: true
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/permissions-tab/permission-accordion.tsx",
        lineNumber: 161,
        columnNumber: 5
    }, undefined);
};
_s(PermissionAccordion, "v2P8cHnjTDkbi8ECqilEMh2nPp0=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation,
        _config_users_api_slice_gen__rspack_import_6.useBundleDataHubUsersCollectionQuery,
        _config_users_api_slice_gen__rspack_import_6.useBundleDataHubUsersCollectionQuery
    ];
});
_c = PermissionAccordion;
var _c;
$RefreshReg$(_c, "PermissionAccordion");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/components/tabs/permissions-tab/permission-grid.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  PermissionGrid: () => (PermissionGrid)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_3);
/* import */ var _tanstack_react_table__rspack_import_6 = __webpack_require__("./node_modules/@tanstack/table-core/build/lib/index.mjs");
/* import */ var _permission_accordion__rspack_import_4 = __webpack_require__("./js/src/modules/graphql/components/tabs/permissions-tab/permission-accordion.tsx");
/* import */ var lodash__rspack_import_5 = __webpack_require__("./node_modules/lodash/lodash.js");
/* import */ var lodash__rspack_import_5_default = /*#__PURE__*/__webpack_require__.n(lodash__rspack_import_5);
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





const PermissionGrid = (param)=>{
    let { type, value = [], onChange, isWriteable = true } = param;
    _s();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation)();
    const itemType = type === 'roles' ? 'role' : 'user';
    const columns = (0,react__rspack_import_1.useMemo)(()=>{
        const columnHelper = (0,_tanstack_react_table__rspack_import_6.createColumnHelper)();
        return [
            columnHelper.accessor('name', {
                header: itemType === 'role' ? t('data-hub.permissions.role') : t('data-hub.permissions.user'),
                size: 300,
                meta: {
                    editable: false,
                    autoWidth: true
                }
            }),
            columnHelper.accessor('read', {
                header: t('data-hub.permissions.read'),
                size: 80,
                meta: {
                    type: 'checkbox',
                    editable: isWriteable,
                    config: {
                        align: 'center',
                        disabled: !isWriteable
                    }
                }
            }),
            columnHelper.accessor('update', {
                header: t('data-hub.permissions.update'),
                size: 80,
                meta: {
                    type: 'checkbox',
                    editable: isWriteable,
                    config: {
                        align: 'center',
                        disabled: !isWriteable
                    }
                }
            }),
            columnHelper.accessor('delete', {
                header: t('data-hub.permissions.delete'),
                size: 80,
                meta: {
                    type: 'checkbox',
                    editable: isWriteable,
                    config: {
                        align: 'center',
                        disabled: !isWriteable
                    }
                }
            }),
            {
                id: 'actions',
                header: '',
                size: 60,
                cell: (info)=>/*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Flex, {
                        align: "center",
                        justify: "center",
                        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.IconButton, {
                            icon: {
                                value: 'trash'
                            },
                            onClick: ()=>{
                                const newData = [
                                    ...value
                                ];
                                newData.splice(info.row.index, 1);
                                if (!(0,lodash__rspack_import_5.isNil)(onChange)) {
                                    onChange(newData);
                                }
                            },
                            type: "link"
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/permissions-tab/permission-grid.tsx",
                            lineNumber: 88,
                            columnNumber: 13
                        }, undefined)
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/permissions-tab/permission-grid.tsx",
                        lineNumber: 84,
                        columnNumber: 11
                    }, undefined),
                enableResizing: false,
                enableSorting: false
            }
        ];
    }, [
        itemType,
        t,
        value,
        onChange,
        isWriteable
    ]);
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.OperationalGrid, {
        autoWidth: true,
        columns: columns,
        onChange: onChange,
        value: value,
        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.OperationalGrid.Operations, {
            children: ()=>/*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_permission_accordion__rspack_import_4.PermissionAccordion, {
                    onChange: onChange,
                    type: type,
                    value: value
                }, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/permissions-tab/permission-grid.tsx",
                    lineNumber: 116,
                    columnNumber: 11
                }, undefined)
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/permissions-tab/permission-grid.tsx",
            lineNumber: 114,
            columnNumber: 7
        }, undefined)
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/permissions-tab/permission-grid.tsx",
        lineNumber: 108,
        columnNumber: 5
    }, undefined);
};
_s(PermissionGrid, "qdzg63pBJl6l39Idk27W0YRinRM=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation
    ];
});
_c = PermissionGrid;
var _c;
$RefreshReg$(_c, "PermissionGrid");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},

}]);
//# sourceMappingURL=js_src_components_base-detail-view_index_ts-js_src_modules_config_config-container_tsx-js_src-3b5091.js.map