"use strict";
(self["chunk_pimcore_datahub_bundle "] = self["chunk_pimcore_datahub_bundle "] || []).push([["__federation_expose_plugins"], {
"./js/src/modules/config/dynamic-types/adapters/dynamic-type-data-hub-adapter-graphql.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  DynamicTypeDataHubAdapterGraphQL: () => (DynamicTypeDataHubAdapterGraphQL)
});
/* import */ var _swc_helpers_ts_decorate__rspack_import_5 = __webpack_require__("./node_modules/tslib/tslib.es6.mjs");
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_2);
/* import */ var _dynamic_type_data_hub_adapter_abstract__rspack_import_3 = __webpack_require__("./js/src/modules/config/dynamic-types/dynamic-type-data-hub-adapter-abstract.tsx");
/* import */ var _graphql_components_graphql_detail_view__rspack_import_4 = __webpack_require__("./js/src/modules/graphql/components/graphql-detail-view.tsx");
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



class DynamicTypeDataHubAdapterGraphQL extends _dynamic_type_data_hub_adapter_abstract__rspack_import_3.DynamicTypeDataHubAdapterAbstract {
    getIcon() {
        return {
            type: 'name',
            value: 'graphql',
            colorToken: 'colorCodingViolet4'
        };
    }
    renderDetailView(props) {
        return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_graphql_components_graphql_detail_view__rspack_import_4.GraphQLDetailView, {
            ...props
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/config/dynamic-types/adapters/dynamic-type-data-hub-adapter-graphql.tsx",
            lineNumber: 26,
            columnNumber: 12
        }, this);
    }
    constructor(...args){
        super(...args), this.id = 'graphql';
    }
}
DynamicTypeDataHubAdapterGraphQL = (0,_swc_helpers_ts_decorate__rspack_import_5.__decorate)([
    (0,_pimcore_studio_ui_bundle_app__rspack_import_2.injectable)()
], DynamicTypeDataHubAdapterGraphQL);

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/config/index.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  DataHubModule: () => (DataHubModule)
});
/* import */ var _pimcore_studio_ui_bundle__rspack_import_0 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle");
/* import */ var _pimcore_studio_ui_bundle__rspack_import_0_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle__rspack_import_0);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_1 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_1);
/* import */ var _config_container__rspack_import_2 = __webpack_require__("./js/src/modules/config/config-container.tsx");
/* import */ var _config_service_ids__rspack_import_3 = __webpack_require__("./js/src/config/service-ids.ts");
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



const DataHubModule = {
    onInit: ()=>{
        const adapterRegistry = _pimcore_studio_ui_bundle__rspack_import_0.container.get(_config_service_ids__rspack_import_3.bundleServiceIds["DataHub/DynamicTypes/Adapter/Registry"]);
        adapterRegistry.registerDynamicType(_pimcore_studio_ui_bundle__rspack_import_0.container.get(_config_service_ids__rspack_import_3.bundleServiceIds["DataHub/DynamicTypes/Adapter/GraphQL"]));
        const widgetRegistryService = _pimcore_studio_ui_bundle__rspack_import_0.container.get(_pimcore_studio_ui_bundle_app__rspack_import_1.serviceIds.widgetManager);
        const mainNavRegistryService = _pimcore_studio_ui_bundle__rspack_import_0.container.get(_pimcore_studio_ui_bundle_app__rspack_import_1.serviceIds.mainNavRegistry);
        widgetRegistryService.registerWidget({
            name: 'data-hub-configuration',
            component: _config_container__rspack_import_2.ConfigContainer
        });
        mainNavRegistryService.registerMainNavItem({
            path: 'AutomationIntegration/DataHub',
            label: 'data-hub.configuration',
            order: 100,
            permission: 'plugin_datahub_config',
            perspectivePermission: 'automationIntegration.dataHubConfiguration',
            className: 'item-style-modifier',
            widgetConfig: {
                name: 'Data Hub Configuration',
                id: 'data-hub-configuration',
                component: 'data-hub-configuration',
                config: {
                    translationKey: 'data-hub.configuration',
                    icon: {
                        type: 'name',
                        value: 'datahub'
                    }
                }
            }
        });
    }
};

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/components/graphql-detail-view.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  GraphQLDetailView: () => (GraphQLDetailView)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_3);
/* import */ var _components_base_detail_view__rspack_import_4 = __webpack_require__("./js/src/components/base-detail-view/index.ts");
/* import */ var _tabs_general_tab__rspack_import_5 = __webpack_require__("./js/src/modules/graphql/components/tabs/general-tab.tsx");
/* import */ var _tabs_schema_definition_tab__rspack_import_6 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab.tsx");
/* import */ var _tabs_security_definition_tab__rspack_import_7 = __webpack_require__("./js/src/modules/graphql/components/tabs/security-definition-tab.tsx");
/* import */ var _tabs_permissions_tab__rspack_import_8 = __webpack_require__("./js/src/modules/graphql/components/tabs/permissions-tab.tsx");
/* import */ var _graphql_api_slice_enhanced__rspack_import_9 = __webpack_require__("./js/src/modules/graphql/graphql-api-slice-enhanced.ts");
/* import */ var _config_config_api_slice_enhanced__rspack_import_10 = __webpack_require__("./js/src/modules/config/config-api-slice-enhanced.ts");
/* import */ var lodash__rspack_import_11 = __webpack_require__("./node_modules/lodash/lodash.js");
/* import */ var lodash__rspack_import_11_default = /*#__PURE__*/__webpack_require__.n(lodash__rspack_import_11);
/* import */ var _utils_transformers__rspack_import_12 = __webpack_require__("./js/src/modules/graphql/utils/transformers.ts");
/* import */ var _pimcore_studio_ui_bundle_modules_app__rspack_import_13 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/modules/app");
/* import */ var _pimcore_studio_ui_bundle_modules_app__rspack_import_13_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_modules_app__rspack_import_13);
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












const GraphQLDetailView = (param)=>{
    let { configName, onChange, onDelete } = param;
    var _backendConfig_general;
    _s();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation)();
    // API hooks
    const { data: configData, error: fetchError, isLoading, isFetching, refetch, requestId } = (0,_config_config_api_slice_enhanced__rspack_import_10.useBundleDataHubConfigGetQuery)({
        name: configName
    }, {
        refetchOnMountOrArgChange: true
    });
    const { data: explorerUrlData } = (0,_graphql_api_slice_enhanced__rspack_import_9.useBundleDataHubGraphqlExplorerUrlQuery)({
        name: configName
    });
    const [updateConfig, { error: updateError, isLoading: isSaving }] = (0,_config_config_api_slice_enhanced__rspack_import_10.useBundleDataHubConfigUpdateMutation)();
    // Error tracking
    (0,react__rspack_import_1.useEffect)(()=>{
        if (!(0,lodash__rspack_import_11.isNil)(fetchError)) {
            (0,_pimcore_studio_ui_bundle_modules_app__rspack_import_13.trackError)(new _pimcore_studio_ui_bundle_modules_app__rspack_import_13.ApiError(fetchError));
        }
    }, [
        fetchError
    ]);
    (0,react__rspack_import_1.useEffect)(()=>{
        if (!(0,lodash__rspack_import_11.isNil)(updateError)) {
            (0,_pimcore_studio_ui_bundle_modules_app__rspack_import_13.trackError)(new _pimcore_studio_ui_bundle_modules_app__rspack_import_13.ApiError(updateError));
        }
    }, [
        updateError
    ]);
    const loading = isLoading || isFetching;
    const backendConfig = (configData === null || configData === void 0 ? void 0 : configData.configuration) ?? {};
    const isWriteable = (backendConfig === null || backendConfig === void 0 ? void 0 : (_backendConfig_general = backendConfig.general) === null || _backendConfig_general === void 0 ? void 0 : _backendConfig_general.writeable) ?? true;
    const handleSaveToApi = async (updatedConfig, modificationDate)=>{
        const response = await updateConfig({
            name: configName,
            bundleDataHubUpdateConfiguration: {
                data: JSON.stringify(updatedConfig),
                modificationDate
            }
        }).unwrap();
        return {
            modificationDate: response === null || response === void 0 ? void 0 : response.modificationDate
        };
    };
    // Shared form state management
    const { form, isDirty, initialValues, handleSave, handleValuesChange } = (0,_components_base_detail_view__rspack_import_4.useDetailView)({
        configName,
        configData: backendConfig,
        modificationDate: configData === null || configData === void 0 ? void 0 : configData.modificationDate,
        isLoading: loading,
        requestId,
        transformToForm: _utils_transformers__rspack_import_12.transformBackendToForm,
        transformToBackend: _utils_transformers__rspack_import_12.transformFormToBackend,
        onSave: handleSaveToApi,
        onChange
    });
    const handleOpenInTab = ()=>{
        if (explorerUrlData !== undefined && !(0,lodash__rspack_import_11.isEmpty)(explorerUrlData.explorerUrl)) {
            let explorerUrl = explorerUrlData.explorerUrl;
            const securityMethod = form.getFieldValue([
                'security',
                'method'
            ]);
            if (securityMethod === 'datahub_apikey') {
                const apikey = form.getFieldValue([
                    'security',
                    'apikey'
                ]);
                if (!(0,lodash__rspack_import_11.isNil)(apikey) && !(0,lodash__rspack_import_11.isEmpty)(apikey)) {
                    const firstKey = apikey.split('\n')[0];
                    if (!(0,lodash__rspack_import_11.isEmpty)(firstKey)) {
                        explorerUrl = `${explorerUrl}?apikey=${firstKey}`;
                    }
                }
            }
            window.open(explorerUrl, '_blank');
        }
    };
    const tabs = [
        {
            key: 'general',
            label: t('data-hub.tabs.general'),
            children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_tabs_general_tab__rspack_import_5.GeneralTab, {
                adapterTypeLabel: t('data-hub.adapter.graphql')
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/graphql-detail-view.tsx",
                lineNumber: 102,
                columnNumber: 17
            }, undefined)
        },
        {
            key: 'schema',
            label: t('data-hub.tabs.schema-definition'),
            children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_tabs_schema_definition_tab__rspack_import_6.SchemaDefinitionTab, {
                isWriteable: isWriteable
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/graphql-detail-view.tsx",
                lineNumber: 107,
                columnNumber: 17
            }, undefined)
        },
        {
            key: 'security',
            label: t('data-hub.tabs.security-definition'),
            children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_tabs_security_definition_tab__rspack_import_7.SecurityDefinitionTab, {
                isWriteable: isWriteable
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/graphql-detail-view.tsx",
                lineNumber: 112,
                columnNumber: 17
            }, undefined)
        },
        {
            key: 'permissions',
            label: t('data-hub.tabs.permissions'),
            children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_tabs_permissions_tab__rspack_import_8.PermissionsTab, {
                isWriteable: isWriteable
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/graphql-detail-view.tsx",
                lineNumber: 117,
                columnNumber: 17
            }, undefined)
        }
    ];
    const toolbar = /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_components_base_detail_view__rspack_import_4.ConfigToolbar, {
        additionalButtons: [
            /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.IconTextButton, {
                disabled: false,
                icon: {
                    value: 'graphql',
                    colorToken: 'colorCodingViolet4'
                },
                onClick: handleOpenInTab,
                children: t('data-hub.open-in-tab')
            }, "open-in-tab", false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/graphql-detail-view.tsx",
                lineNumber: 124,
                columnNumber: 9
            }, undefined)
        ],
        configName: configName,
        isDirty: isDirty,
        isLoading: loading,
        isSaving: isSaving,
        isWriteable: isWriteable,
        onDelete: onDelete,
        onRefresh: refetch,
        onSave: handleSave
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/graphql-detail-view.tsx",
        lineNumber: 122,
        columnNumber: 5
    }, undefined);
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_components_base_detail_view__rspack_import_4.BaseDetailView, {
        disabled: !isWriteable,
        form: form,
        initialValues: initialValues,
        isLoading: loading,
        onValuesChange: handleValuesChange,
        requestId: requestId ?? '',
        tabs: tabs,
        toolbar: toolbar
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/graphql-detail-view.tsx",
        lineNumber: 145,
        columnNumber: 5
    }, undefined);
};
_s(GraphQLDetailView, "2CZKP1yOm9tKNqSYJ54VvT5LGag=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation,
        _config_config_api_slice_enhanced__rspack_import_10.useBundleDataHubConfigGetQuery,
        _graphql_api_slice_enhanced__rspack_import_9.useBundleDataHubGraphqlExplorerUrlQuery,
        _config_config_api_slice_enhanced__rspack_import_10.useBundleDataHubConfigUpdateMutation,
        _components_base_detail_view__rspack_import_4.useDetailView
    ];
});
_c = GraphQLDetailView;
var _c;
$RefreshReg$(_c, "GraphQLDetailView");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/components/tabs/schema-definition-tab.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  SchemaDefinitionTab: () => (SchemaDefinitionTab)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _schema_definition_tab_query_grid__rspack_import_3 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/query-grid.tsx");
/* import */ var _schema_definition_tab_mutation_grid__rspack_import_4 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/mutation-grid.tsx");
/* import */ var _schema_definition_tab_generic_types_grid__rspack_import_5 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/generic-types-grid.tsx");
/* import */ var _config_components_field_width_container__rspack_import_6 = __webpack_require__("./js/src/modules/config/components/field-width-container.tsx");
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





const SchemaDefinitionTab = (param)=>{
    let { isWriteable = true } = param;
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.FormKit.Panel, {
        contentPadding: "extra-small",
        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_config_components_field_width_container__rspack_import_6.FieldWidthContainer, {
            children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Flex, {
                gap: "small",
                vertical: true,
                children: [
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                        name: [
                            'schema',
                            'query'
                        ],
                        noStyle: true,
                        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_schema_definition_tab_query_grid__rspack_import_3.QueryGrid, {
                            isWriteable: isWriteable
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab.tsx",
                            lineNumber: 34,
                            columnNumber: 13
                        }, undefined)
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab.tsx",
                        lineNumber: 30,
                        columnNumber: 11
                    }, undefined),
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                        name: [
                            'schema',
                            'mutation'
                        ],
                        noStyle: true,
                        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_schema_definition_tab_mutation_grid__rspack_import_4.MutationGrid, {
                            isWriteable: isWriteable
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab.tsx",
                            lineNumber: 41,
                            columnNumber: 13
                        }, undefined)
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab.tsx",
                        lineNumber: 37,
                        columnNumber: 11
                    }, undefined),
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                        name: [
                            'schema',
                            'genericTypes'
                        ],
                        noStyle: true,
                        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_schema_definition_tab_generic_types_grid__rspack_import_5.GenericTypesGrid, {
                            isWriteable: isWriteable
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab.tsx",
                            lineNumber: 48,
                            columnNumber: 13
                        }, undefined)
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab.tsx",
                        lineNumber: 44,
                        columnNumber: 11
                    }, undefined)
                ]
            }, void 0, true, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab.tsx",
                lineNumber: 26,
                columnNumber: 9
            }, undefined)
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab.tsx",
            lineNumber: 25,
            columnNumber: 7
        }, undefined)
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab.tsx",
        lineNumber: 24,
        columnNumber: 5
    }, undefined);
};
_c = SchemaDefinitionTab;
var _c;
$RefreshReg$(_c, "SchemaDefinitionTab");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/components/tabs/schema-definition-tab/generic-types-grid.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  GenericTypesGrid: () => (GenericTypesGrid)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_3);
/* import */ var _tanstack_react_table__rspack_import_4 = __webpack_require__("./node_modules/@tanstack/table-core/build/lib/index.mjs");
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



const GenericTypesGrid = (param)=>{
    let { value = [], onChange, isWriteable = true } = param;
    _s();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation)();
    const columns = (0,react__rspack_import_1.useMemo)(()=>{
        const columnHelper = (0,_tanstack_react_table__rspack_import_4.createColumnHelper)();
        return [
            columnHelper.accessor('name', {
                header: '',
                size: 200,
                meta: {
                    autoWidth: true
                },
                cell: (info)=>{
                    const entityName = info.getValue();
                    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Flex, {
                        align: "center",
                        style: {
                            paddingLeft: '12px'
                        },
                        children: t(`data-hub.schema.special.${entityName}`)
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/generic-types-grid.tsx",
                        lineNumber: 39,
                        columnNumber: 13
                    }, undefined);
                }
            }),
            columnHelper.accessor('create', {
                header: t('data-hub.workspaces.create'),
                size: 80,
                meta: {
                    type: 'checkbox',
                    editable: (row)=>isWriteable && row.createPossible,
                    tooltip: (row)=>!isWriteable ? null : row.createPossible ? null : t('data-hub.schema.operation-not-implemented'),
                    config: {
                        align: 'center',
                        disabled: (row)=>!isWriteable || !row.createPossible
                    }
                }
            }),
            columnHelper.accessor('read', {
                header: t('data-hub.workspaces.read'),
                size: 80,
                meta: {
                    type: 'checkbox',
                    editable: (row)=>isWriteable && row.readPossible,
                    tooltip: (row)=>!isWriteable ? null : row.readPossible ? null : t('data-hub.schema.operation-not-implemented'),
                    config: {
                        align: 'center',
                        disabled: (row)=>!isWriteable || !row.readPossible
                    }
                }
            }),
            columnHelper.accessor('update', {
                header: t('data-hub.workspaces.update'),
                size: 80,
                meta: {
                    type: 'checkbox',
                    editable: (row)=>isWriteable && row.updatePossible,
                    tooltip: (row)=>!isWriteable ? null : row.updatePossible ? null : t('data-hub.schema.operation-not-implemented'),
                    config: {
                        align: 'center',
                        disabled: (row)=>!isWriteable || !row.updatePossible
                    }
                }
            }),
            columnHelper.accessor('delete', {
                header: t('data-hub.workspaces.delete'),
                size: 80,
                meta: {
                    type: 'checkbox',
                    editable: (row)=>isWriteable && row.deletePossible,
                    tooltip: (row)=>!isWriteable ? null : row.deletePossible ? null : t('data-hub.schema.operation-not-implemented'),
                    config: {
                        align: 'center',
                        disabled: (row)=>!isWriteable || !row.deletePossible
                    }
                }
            })
        ];
    }, [
        t,
        isWriteable
    ]);
    const accordionItem = (0,react__rspack_import_1.useMemo)(()=>({
            key: 'genericTypes',
            id: 'genericTypes',
            title: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(react_jsx_dev_runtime__rspack_import_0.Fragment, {
                children: t('data-hub.schema.generic-types')
            }, void 0, false),
            children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.OperationalGrid.Grid, {}, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/generic-types-grid.tsx",
                lineNumber: 108,
                columnNumber: 7
            }, undefined)
        }), [
        t
    ]);
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.OperationalGrid, {
        autoWidth: true,
        columns: columns,
        onChange: onChange,
        value: value,
        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.OperationalGrid.Operations, {
            children: ()=>/*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Accordion, {
                    activeKey: "genericTypes",
                    bordered: true,
                    collapsible: "icon",
                    items: [
                        accordionItem
                    ],
                    size: "small",
                    table: true
                }, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/generic-types-grid.tsx",
                    lineNumber: 121,
                    columnNumber: 11
                }, undefined)
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/generic-types-grid.tsx",
            lineNumber: 119,
            columnNumber: 7
        }, undefined)
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/generic-types-grid.tsx",
        lineNumber: 113,
        columnNumber: 5
    }, undefined);
};
_s(GenericTypesGrid, "KEuX8ci3XDlv0DPhZU5ax93cqgg=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation
    ];
});
_c = GenericTypesGrid;
var _c;
$RefreshReg$(_c, "GenericTypesGrid");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/components/tabs/schema-definition-tab/mutation-grid.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  MutationGrid: () => (MutationGrid)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_3);
/* import */ var _tanstack_react_table__rspack_import_8 = __webpack_require__("./node_modules/@tanstack/table-core/build/lib/index.mjs");
/* import */ var _schema_accordion__rspack_import_4 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-accordion.tsx");
/* import */ var lodash__rspack_import_5 = __webpack_require__("./node_modules/lodash/lodash.js");
/* import */ var lodash__rspack_import_5_default = /*#__PURE__*/__webpack_require__.n(lodash__rspack_import_5);
/* import */ var _schema_fields_modal_schema_fields_modal__rspack_import_6 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/schema-fields-modal.tsx");
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







const MutationGrid = (param)=>{
    let { value = [], onChange, isWriteable = true } = param;
    _s();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation)();
    const [modalOpen, setModalOpen] = (0,react__rspack_import_1.useState)(false);
    const [selectedEntity, setSelectedEntity] = (0,react__rspack_import_1.useState)(null);
    const columns = (0,react__rspack_import_1.useMemo)(()=>{
        const columnHelper = (0,_tanstack_react_table__rspack_import_8.createColumnHelper)();
        return [
            columnHelper.accessor('entity', {
                header: t('data-hub.schema.entity'),
                size: 200,
                meta: {
                    type: 'input-text',
                    editable: isWriteable,
                    autoWidth: true
                }
            }),
            columnHelper.accessor('create', {
                header: t('data-hub.workspaces.create'),
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
                header: t('data-hub.workspaces.update'),
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
                header: t('data-hub.workspaces.delete'),
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
                id: 'settings',
                header: t('data-hub.schema.settings'),
                size: 100,
                cell: (info)=>{
                    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Flex, {
                        align: "center",
                        justify: "center",
                        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.IconButton, {
                            disabled: false,
                            icon: {
                                value: 'settings'
                            },
                            onClick: ()=>{
                                setSelectedEntity(value[info.row.index]);
                                setModalOpen(true);
                            },
                            type: "link"
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/mutation-grid.tsx",
                            lineNumber: 91,
                            columnNumber: 15
                        }, undefined)
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/mutation-grid.tsx",
                        lineNumber: 87,
                        columnNumber: 13
                    }, undefined);
                }
            },
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
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/mutation-grid.tsx",
                            lineNumber: 113,
                            columnNumber: 13
                        }, undefined)
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/mutation-grid.tsx",
                        lineNumber: 109,
                        columnNumber: 11
                    }, undefined)
            }
        ];
    }, [
        value,
        onChange,
        t,
        isWriteable
    ]);
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(react_jsx_dev_runtime__rspack_import_0.Fragment, {
        children: [
            /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.OperationalGrid, {
                autoWidth: true,
                columns: columns,
                onChange: onChange,
                value: value,
                children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.OperationalGrid.Operations, {
                    children: ()=>/*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_schema_accordion__rspack_import_4.SchemaAccordion, {
                            onChange: onChange,
                            type: "mutation",
                            value: value
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/mutation-grid.tsx",
                            lineNumber: 140,
                            columnNumber: 13
                        }, undefined)
                }, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/mutation-grid.tsx",
                    lineNumber: 138,
                    columnNumber: 9
                }, undefined)
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/mutation-grid.tsx",
                lineNumber: 132,
                columnNumber: 7
            }, undefined),
            modalOpen && !(0,lodash__rspack_import_5.isNil)(selectedEntity) && /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_schema_fields_modal_schema_fields_modal__rspack_import_6.SchemaFieldsModal, {
                className: selectedEntity.entity,
                disabled: !isWriteable,
                onApply: ()=>{
                    setModalOpen(false);
                },
                onCancel: ()=>{
                    setModalOpen(false);
                },
                open: modalOpen,
                operatorRegistryServiceId: _config_service_ids__rspack_import_7.bundleServiceIds["DataHub/DynamicTypes/Operator/GraphQL/MutationRegistry"],
                type: "mutation"
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/mutation-grid.tsx",
                lineNumber: 150,
                columnNumber: 9
            }, undefined)
        ]
    }, void 0, true);
};
_s(MutationGrid, "n6D/Yw9r1R/GF3AhU/Sat1DEDfU=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation
    ];
});
_c = MutationGrid;
var _c;
$RefreshReg$(_c, "MutationGrid");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/components/tabs/schema-definition-tab/query-grid.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  QueryGrid: () => (QueryGrid)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_3);
/* import */ var _tanstack_react_table__rspack_import_8 = __webpack_require__("./node_modules/@tanstack/table-core/build/lib/index.mjs");
/* import */ var _schema_accordion__rspack_import_4 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-accordion.tsx");
/* import */ var lodash__rspack_import_5 = __webpack_require__("./node_modules/lodash/lodash.js");
/* import */ var lodash__rspack_import_5_default = /*#__PURE__*/__webpack_require__.n(lodash__rspack_import_5);
/* import */ var _schema_fields_modal_schema_fields_modal__rspack_import_6 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/schema-fields-modal.tsx");
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







const QueryGrid = (param)=>{
    let { value = [], onChange, isWriteable = true } = param;
    _s();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation)();
    const [modalOpen, setModalOpen] = (0,react__rspack_import_1.useState)(false);
    const [selectedEntity, setSelectedEntity] = (0,react__rspack_import_1.useState)(null);
    const columns = (0,react__rspack_import_1.useMemo)(()=>{
        const columnHelper = (0,_tanstack_react_table__rspack_import_8.createColumnHelper)();
        return [
            columnHelper.accessor('entity', {
                header: t('data-hub.schema.entity'),
                size: 300,
                meta: {
                    type: 'input-text',
                    editable: isWriteable,
                    autoWidth: true
                }
            }),
            {
                id: 'settings',
                header: t('data-hub.schema.settings'),
                size: 100,
                cell: (info)=>{
                    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Flex, {
                        align: "center",
                        justify: "center",
                        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.IconButton, {
                            disabled: false,
                            icon: {
                                value: 'settings'
                            },
                            onClick: ()=>{
                                setSelectedEntity(value[info.row.index]);
                                setModalOpen(true);
                            },
                            type: "link"
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/query-grid.tsx",
                            lineNumber: 55,
                            columnNumber: 15
                        }, undefined)
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/query-grid.tsx",
                        lineNumber: 51,
                        columnNumber: 13
                    }, undefined);
                }
            },
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
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/query-grid.tsx",
                            lineNumber: 77,
                            columnNumber: 13
                        }, undefined)
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/query-grid.tsx",
                        lineNumber: 73,
                        columnNumber: 11
                    }, undefined)
            }
        ];
    }, [
        t,
        value,
        onChange,
        isWriteable
    ]);
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(react_jsx_dev_runtime__rspack_import_0.Fragment, {
        children: [
            /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.OperationalGrid, {
                autoWidth: true,
                columns: columns,
                onChange: onChange,
                value: value,
                children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.OperationalGrid.Operations, {
                    children: ()=>/*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_schema_accordion__rspack_import_4.SchemaAccordion, {
                            onChange: onChange,
                            type: "query",
                            value: value
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/query-grid.tsx",
                            lineNumber: 104,
                            columnNumber: 13
                        }, undefined)
                }, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/query-grid.tsx",
                    lineNumber: 102,
                    columnNumber: 9
                }, undefined)
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/query-grid.tsx",
                lineNumber: 96,
                columnNumber: 7
            }, undefined),
            modalOpen && /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_schema_fields_modal_schema_fields_modal__rspack_import_6.SchemaFieldsModal, {
                className: (selectedEntity === null || selectedEntity === void 0 ? void 0 : selectedEntity.entity) ?? '',
                disabled: !isWriteable,
                onApply: ()=>{
                    setModalOpen(false);
                },
                onCancel: ()=>{
                    setModalOpen(false);
                },
                open: modalOpen,
                operatorRegistryServiceId: _config_service_ids__rspack_import_7.bundleServiceIds["DataHub/DynamicTypes/Operator/GraphQL/QueryRegistry"]
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/query-grid.tsx",
                lineNumber: 114,
                columnNumber: 9
            }, undefined)
        ]
    }, void 0, true);
};
_s(QueryGrid, "n6D/Yw9r1R/GF3AhU/Sat1DEDfU=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation
    ];
});
_c = QueryGrid;
var _c;
$RefreshReg$(_c, "QueryGrid");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-accordion.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  SchemaAccordion: () => (SchemaAccordion)
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
/* import */ var _pimcore_studio_ui_bundle_modules_data_object__rspack_import_5 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/modules/data-object");
/* import */ var _pimcore_studio_ui_bundle_modules_data_object__rspack_import_5_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_modules_data_object__rspack_import_5);
/* import */ var _inline_dropdown_panel_inline_dropdown_panel__rspack_import_6 = __webpack_require__("./js/src/modules/graphql/components/inline-dropdown-panel/inline-dropdown-panel.tsx");
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





const SchemaAccordion = (param)=>{
    let { type, value = [], onChange } = param;
    _s();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation)();
    const [openDropdown, setOpenDropdown] = (0,react__rspack_import_1.useState)(false);
    const [selectedClasses, setSelectedClasses] = (0,react__rspack_import_1.useState)([]);
    const { getAllClassDefinitions } = (0,_pimcore_studio_ui_bundle_modules_data_object__rspack_import_5.useClassDefinitions)();
    const availableClasses = (0,react__rspack_import_1.useMemo)(()=>{
        return getAllClassDefinitions();
    }, [
        getAllClassDefinitions
    ]);
    const currentEntityNames = (0,react__rspack_import_1.useMemo)(()=>{
        return value.map((entity)=>entity.entity);
    }, [
        value
    ]);
    const options = (0,react__rspack_import_1.useMemo)(()=>{
        return availableClasses.filter((cls)=>!currentEntityNames.includes(cls.name)).map((cls)=>({
                value: cls.name,
                label: cls.name,
                searchValue: cls.name
            }));
    }, [
        availableClasses,
        currentEntityNames
    ]);
    const handleOpen = ()=>{
        setSelectedClasses([]);
        setOpenDropdown(true);
    };
    const handleCancel = ()=>{
        setSelectedClasses([]);
        setOpenDropdown(false);
    };
    const handleApply = ()=>{
        if (selectedClasses.length > 0 && !(0,lodash__rspack_import_4.isNil)(onChange)) {
            const newEntities = selectedClasses.map((className)=>{
                if (type === 'query') {
                    return {
                        id: className,
                        entity: className
                    };
                } else {
                    return {
                        id: className,
                        entity: className,
                        create: false,
                        update: true,
                        delete: false
                    };
                }
            });
            onChange([
                ...value,
                ...newEntities
            ]);
        }
        setSelectedClasses([]);
        setOpenDropdown(false);
    };
    const accordionItem = (0,react__rspack_import_1.useMemo)(()=>({
            key: type,
            id: type,
            title: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(react_jsx_dev_runtime__rspack_import_0.Fragment, {
                children: t(`data-hub.schema.${type}-schema`)
            }, void 0, false),
            info: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(react_jsx_dev_runtime__rspack_import_0.Fragment, {
                children: [
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.IconTextButton, {
                        icon: {
                            value: 'add-find'
                        },
                        onClick: (e)=>{
                            e.stopPropagation();
                            handleOpen();
                        },
                        children: t('add')
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-accordion.tsx",
                        lineNumber: 93,
                        columnNumber: 9
                    }, undefined),
                    openDropdown && /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_inline_dropdown_panel_inline_dropdown_panel__rspack_import_6.InlineDropdownPanel, {
                        children: [
                            /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Select, {
                                listHeight: 150,
                                mode: "multiple",
                                onChange: (values)=>{
                                    setSelectedClasses(values);
                                },
                                optionFilterProp: "searchValue",
                                options: options,
                                placeholder: t('data-hub.schema.select-class'),
                                placement: "topLeft",
                                showSearch: true,
                                style: {
                                    width: '400px'
                                },
                                value: selectedClasses
                            }, void 0, false, {
                                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-accordion.tsx",
                                lineNumber: 104,
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
                                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-accordion.tsx",
                                        lineNumber: 121,
                                        columnNumber: 15
                                    }, undefined),
                                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Button, {
                                        onClick: handleApply,
                                        type: "primary",
                                        children: t('button.apply')
                                    }, void 0, false, {
                                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-accordion.tsx",
                                        lineNumber: 127,
                                        columnNumber: 15
                                    }, undefined)
                                ]
                            }, void 0, true, {
                                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-accordion.tsx",
                                lineNumber: 116,
                                columnNumber: 13
                            }, undefined)
                        ]
                    }, void 0, true, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-accordion.tsx",
                        lineNumber: 103,
                        columnNumber: 11
                    }, undefined)
                ]
            }, void 0, true),
            children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.OperationalGrid.Grid, {}, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-accordion.tsx",
                lineNumber: 139,
                columnNumber: 7
            }, undefined)
        }), [
        type,
        openDropdown,
        options,
        selectedClasses
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
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-accordion.tsx",
        lineNumber: 144,
        columnNumber: 5
    }, undefined);
};
_s(SchemaAccordion, "EKJBSNl+Fu2X4od75JHVV/uFkCY=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation,
        _pimcore_studio_ui_bundle_modules_data_object__rspack_import_5.useClassDefinitions
    ];
});
_c = SchemaAccordion;
var _c;
$RefreshReg$(_c, "SchemaAccordion");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/available-fields-tree.styles.ts"(module, __webpack_exports__, __webpack_require__) {
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
        treeContainer: css`
      .ant-tree-title {
        white-space: nowrap;
      }
      
      .ant-tree-list-holder-inner .ant-tree-treenode {
        padding: 1px ${token.paddingXS}px;
      }
    `
    };
});

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/available-fields-tree.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  AvailableFieldsTree: () => (AvailableFieldsTree)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_utils__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/utils");
/* import */ var _pimcore_studio_ui_bundle_utils__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_utils__rspack_import_3);
/* import */ var lodash__rspack_import_4 = __webpack_require__("./node_modules/lodash/lodash.js");
/* import */ var lodash__rspack_import_4_default = /*#__PURE__*/__webpack_require__.n(lodash__rspack_import_4);
/* import */ var _hooks_use_tree_context__rspack_import_5 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/hooks/use-tree-context.tsx");
/* import */ var _utils_tree_operations__rspack_import_6 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/utils/tree-operations.ts");
/* import */ var _tree_node_renderer__rspack_import_7 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/tree-node-renderer.tsx");
/* import */ var _empty_tree_drop_zone__rspack_import_8 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/empty-tree-drop-zone.tsx");
/* import */ var _available_fields_tree_styles__rspack_import_9 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/available-fields-tree.styles.ts");
/* import */ var _hooks_use_tree_nodes__rspack_import_10 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/hooks/use-tree-nodes.tsx");
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









const AvailableFieldsTreeInner = (param)=>{
    let { operatorModalConfig, disabled, setOperatorModalConfig } = param;
    _s();
    const { items, operatorRegistry, fieldDefinitionRegistry, deleteByKey, findPath, getItem, updateItemAttributes } = (0,_hooks_use_tree_context__rspack_import_5.useTreeContext)();
    const { styles } = (0,_available_fields_tree_styles__rspack_import_9.useStyles)();
    const treeData = (0,_hooks_use_tree_nodes__rspack_import_10.useTreeNodes)({
        items,
        operatorRegistry,
        fieldDefinitionRegistry,
        disabled
    });
    const allKeys = (0,react__rspack_import_1.useMemo)(()=>(0,_utils_tree_operations__rspack_import_6.collectAllKeys)(items), [
        items
    ]);
    const [expandedKeys, setExpandedKeys] = (0,react__rspack_import_1.useState)([]);
    const prevAllKeysRef = (0,react__rspack_import_1.useRef)([]);
    (0,react__rspack_import_1.useEffect)(()=>{
        const prevKeys = prevAllKeysRef.current;
        const currentKeys = allKeys.map(String);
        if (prevKeys.length === 0 && currentKeys.length > 0) {
            prevAllKeysRef.current = currentKeys;
            setExpandedKeys(currentKeys);
            return;
        }
        if (currentKeys.length > prevKeys.length) {
            const newKeys = currentKeys.filter((k)=>!prevKeys.includes(k));
            const keysToExpand = new Set(expandedKeys.map(String));
            newKeys.forEach((newKey)=>{
                const path = findPath(newKey);
                if (path !== null && path.length > 1) {
                    const parentPath = path.slice(0, -1);
                    const parent = getItem(parentPath);
                    if ((parent === null || parent === void 0 ? void 0 : parent.isOperator) === true) {
                        keysToExpand.add(String(parent.key));
                    }
                }
            });
            prevAllKeysRef.current = currentKeys;
            setExpandedKeys(Array.from(keysToExpand));
            return;
        }
        if (currentKeys.length < prevKeys.length) {
            const validKeys = expandedKeys.filter((k)=>currentKeys.includes(String(k)));
            prevAllKeysRef.current = currentKeys;
            setExpandedKeys(validKeys);
            return;
        }
        prevAllKeysRef.current = currentKeys;
    }, [
        allKeys,
        expandedKeys,
        findPath,
        getItem
    ]);
    const handleExpand = (0,react__rspack_import_1.useCallback)((keys)=>{
        setExpandedKeys(keys);
    }, []);
    const handleModalApply = (0,react__rspack_import_1.useCallback)((updatedConfig)=>{
        if ((0,lodash__rspack_import_4.isNil)(operatorModalConfig)) return;
        updateItemAttributes(operatorModalConfig.itemData.key, updatedConfig.attributes);
        setOperatorModalConfig(null);
    }, [
        operatorModalConfig,
        updateItemAttributes,
        setOperatorModalConfig
    ]);
    const handleModalCancel = (0,react__rspack_import_1.useCallback)(()=>{
        setOperatorModalConfig(null);
    }, [
        setOperatorModalConfig
    ]);
    const handleActionsClick = (0,react__rspack_import_1.useCallback)((key, action)=>{
        if (action === 'delete') {
            if (disabled) return;
            deleteByKey(key);
            return;
        }
        if (action === 'edit' || action === 'view') {
            const path = findPath(key);
            if (path === null) return;
            const item = getItem(path);
            if (!(item === null || item === void 0 ? void 0 : item.isOperator)) return;
            if (!(0,_pimcore_studio_ui_bundle_utils__rspack_import_3.isNonEmptyString)(item.attributes.class)) return;
            setOperatorModalConfig({
                itemData: item,
                operatorId: item.attributes.class
            });
        }
    }, [
        disabled,
        deleteByKey,
        findPath,
        getItem,
        setOperatorModalConfig
    ]);
    const expandedKeysSet = (0,react__rspack_import_1.useMemo)(()=>new Set(expandedKeys), [
        expandedKeys
    ]);
    const titleRender = (0,react__rspack_import_1.useCallback)((node, initialComponent)=>{
        const isExpanded = expandedKeysSet.has(node.key);
        const hasChildren = node.children !== undefined && node.children.length > 0;
        const hasExpandedChildren = isExpanded && hasChildren;
        return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_tree_node_renderer__rspack_import_7.TreeNodeRenderer, {
            disabled: disabled,
            hasExpandedChildren: hasExpandedChildren,
            initialComponent: initialComponent,
            itemData: node.itemData
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/available-fields-tree.tsx",
            lineNumber: 159,
            columnNumber: 7
        }, undefined);
    }, [
        expandedKeysSet,
        disabled
    ]);
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(react_jsx_dev_runtime__rspack_import_0.Fragment, {
        children: [
            items.length === 0 ? disabled ? /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)("div", {
                style: {
                    padding: '16px',
                    textAlign: 'center',
                    color: '#999'
                },
                children: "No fields configured"
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/available-fields-tree.tsx",
                lineNumber: 173,
                columnNumber: 17
            }, undefined) : /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_empty_tree_drop_zone__rspack_import_8.EmptyTreeDropZone, {}, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/available-fields-tree.tsx",
                lineNumber: 174,
                columnNumber: 17
            }, undefined) : /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.TreeElement, {
                blockNode: true,
                className: styles.treeContainer,
                defaultExpandedKeys: expandedKeys,
                onActionsClick: handleActionsClick,
                onExpand: handleExpand,
                selectable: false,
                showIcon: true,
                titleRender: titleRender,
                treeData: treeData
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/available-fields-tree.tsx",
                lineNumber: 177,
                columnNumber: 11
            }, undefined),
            !(0,lodash__rspack_import_4.isNil)(operatorModalConfig) && (()=>{
                const operatorType = operatorRegistry.getDynamicType(operatorModalConfig.operatorId, false);
                if ((0,lodash__rspack_import_4.isNil)(operatorType)) return null;
                return operatorType.getConfigModal({
                    config: {
                        key: operatorModalConfig.itemData.key,
                        isOperator: true,
                        attributes: operatorModalConfig.itemData.attributes
                    },
                    operator: operatorType,
                    disabled,
                    onApply: handleModalApply,
                    onCancel: handleModalCancel
                });
            })()
        ]
    }, void 0, true);
};
_s(AvailableFieldsTreeInner, "kfFM/x+bOLLhZwJ0pwhguG2ukfI=", false, function() {
    return [
        _hooks_use_tree_context__rspack_import_5.useTreeContext,
        _available_fields_tree_styles__rspack_import_9.useStyles,
        _hooks_use_tree_nodes__rspack_import_10.useTreeNodes
    ];
});
_c = AvailableFieldsTreeInner;
const AvailableFieldsTree = (param)=>{
    let { entityConfig, operatorRegistryServiceId, disabled = false, onEntityConfigChange } = param;
    _s1();
    const [operatorModalConfig, setOperatorModalConfig] = (0,react__rspack_import_1.useState)(null);
    const handleOperatorAdded = (0,react__rspack_import_1.useCallback)((item)=>{
        if ((0,_pimcore_studio_ui_bundle_utils__rspack_import_3.isNonEmptyString)(item.attributes.class)) {
            setOperatorModalConfig({
                itemData: item,
                operatorId: item.attributes.class
            });
        }
    }, []);
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_hooks_use_tree_context__rspack_import_5.TreeProvider, {
        disabled: disabled,
        entityConfig: entityConfig,
        onEntityConfigChange: onEntityConfigChange,
        onOperatorAdded: handleOperatorAdded,
        operatorRegistryServiceId: operatorRegistryServiceId,
        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(AvailableFieldsTreeInner, {
            disabled: disabled,
            operatorModalConfig: operatorModalConfig,
            setOperatorModalConfig: setOperatorModalConfig
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/available-fields-tree.tsx",
            lineNumber: 235,
            columnNumber: 7
        }, undefined)
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/available-fields-tree.tsx",
        lineNumber: 228,
        columnNumber: 5
    }, undefined);
};
_s1(AvailableFieldsTree, "FlqgDGNlPW5cWypxM1Fs/cWepI8=");
_c1 = AvailableFieldsTree;
var _c, _c1;
$RefreshReg$(_c, "AvailableFieldsTreeInner");
$RefreshReg$(_c1, "AvailableFieldsTree");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/empty-tree-drop-zone.styles.ts"(module, __webpack_exports__, __webpack_require__) {
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
        dropZone: css`
      padding: ${token.paddingSM}px ${token.paddingXS}px;
      text-align: center;
      color: ${token.colorTextTertiary};
      border: 1px dashed ${token.colorBorder};
      border-radius: ${token.borderRadius}px;
      min-height: 100px;
    `
    };
});

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/empty-tree-drop-zone.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  EmptyTreeDropZone: () => (EmptyTreeDropZone)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_3);
/* import */ var _hooks_use_tree_context__rspack_import_4 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/hooks/use-tree-context.tsx");
/* import */ var _empty_tree_drop_zone_styles__rspack_import_5 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/empty-tree-drop-zone.styles.ts");
/* import */ var classnames__rspack_import_6 = __webpack_require__("./node_modules/classnames/index.js");
/* import */ var classnames__rspack_import_6_default = /*#__PURE__*/__webpack_require__.n(classnames__rspack_import_6);
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





const EmptyTreeDropContent = /*#__PURE__*/ (0,react__rspack_import_1.forwardRef)(_s(function EmptyTreeDropContent(props, ref) {
    _s();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation)();
    const { getStateClasses } = (0,_pimcore_studio_ui_bundle_components__rspack_import_2.useDroppable)();
    const { styles } = (0,_empty_tree_drop_zone_styles__rspack_import_5.useStyles)();
    const stateClasses = getStateClasses();
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Flex, {
        align: "center",
        className: classnames__rspack_import_6_default()(styles.dropZone, stateClasses.join(' ')),
        justify: "center",
        ref: ref,
        children: t('data-hub.schema.drag-class-attributes-or-operators')
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/empty-tree-drop-zone.tsx",
        lineNumber: 25,
        columnNumber: 5
    }, this);
}, "QUlEXGsoxilfjX8qWQAppX8+k2E=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation,
        _pimcore_studio_ui_bundle_components__rspack_import_2.useDroppable,
        _empty_tree_drop_zone_styles__rspack_import_5.useStyles
    ];
}));
_c = EmptyTreeDropContent;
const EmptyTreeDropZone = ()=>{
    _s1();
    const { isValidDragType, canDropToRoot, handleDropToRoot } = (0,_hooks_use_tree_context__rspack_import_4.useTreeContext)();
    const checkForValidContext = (0,react__rspack_import_1.useCallback)((info)=>{
        return isValidDragType(info);
    }, [
        isValidDragType
    ]);
    const checkForValidData = (0,react__rspack_import_1.useCallback)((info)=>{
        return canDropToRoot(info);
    }, [
        canDropToRoot
    ]);
    const onDrop = (0,react__rspack_import_1.useCallback)((info)=>{
        handleDropToRoot(info);
    }, [
        handleDropToRoot
    ]);
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Droppable, {
        isValidContext: checkForValidContext,
        isValidData: checkForValidData,
        onDrop: onDrop,
        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(EmptyTreeDropContent, {}, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/empty-tree-drop-zone.tsx",
            lineNumber: 61,
            columnNumber: 7
        }, undefined)
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/empty-tree-drop-zone.tsx",
        lineNumber: 56,
        columnNumber: 5
    }, undefined);
};
_s1(EmptyTreeDropZone, "gXhHYNrQoGktyS8wVrARhtRA5E8=", false, function() {
    return [
        _hooks_use_tree_context__rspack_import_4.useTreeContext
    ];
});
_c1 = EmptyTreeDropZone;
var _c, _c1;
$RefreshReg$(_c, "EmptyTreeDropContent");
$RefreshReg$(_c1, "EmptyTreeDropZone");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/hooks/use-tree-context.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  TreeProvider: () => (TreeProvider),
  useTreeContext: () => (useTreeContext)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var lodash__rspack_import_2 = __webpack_require__("./node_modules/lodash/lodash.js");
/* import */ var lodash__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(lodash__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_3);
/* import */ var _use_tree_state__rspack_import_4 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/hooks/use-tree-state.ts");
/* import */ var _drag_types__rspack_import_5 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/drag-types.ts");
/* import */ var _utils_tree_conversion_utils__rspack_import_6 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/utils/tree-conversion-utils.ts");
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





const TreeContext = /*#__PURE__*/ (0,react__rspack_import_1.createContext)(null);
const useTreeContext = ()=>{
    _s();
    const context = (0,react__rspack_import_1.useContext)(TreeContext);
    if (context === null) {
        throw new Error('useTreeContext must be used within a TreeProvider');
    }
    return context;
};
_s(useTreeContext, "b9L3QQ+jgeyIrH0NfHrJ8nn7VMU=");
const TreeProvider = (param)=>{
    let { children, entityConfig, operatorRegistryServiceId, disabled = false, onEntityConfigChange, onOperatorAdded } = param;
    _s1();
    const operatorRegistry = (0,_pimcore_studio_ui_bundle_app__rspack_import_3.useInjection)(operatorRegistryServiceId);
    const fieldDefinitionRegistry = (0,_pimcore_studio_ui_bundle_app__rspack_import_3.useInjection)(_pimcore_studio_ui_bundle_app__rspack_import_3.serviceIds["DynamicTypes/FieldDefinitionRegistry"]);
    const { items, findPath, getItem, deleteByKey, insert, appendToRoot, updateItemAttributes, move, canDrop, canDropToRoot } = (0,_use_tree_state__rspack_import_4.useTreeState)({
        entityConfig,
        onEntityConfigChange,
        operatorRegistry
    });
    const isValidDragType = (0,react__rspack_import_1.useCallback)((dragInfo)=>{
        return dragInfo.type === _drag_types__rspack_import_5.DragType.CLASS_ATTRIBUTE || dragInfo.type === _drag_types__rspack_import_5.DragType.OPERATOR || dragInfo.type === _drag_types__rspack_import_5.DragType.TREE_ITEM;
    }, []);
    const handleDrop = (0,react__rspack_import_1.useCallback)((dragInfo, targetKey, position)=>{
        const targetPath = findPath(targetKey);
        if ((0,lodash__rspack_import_2.isNil)(targetPath)) {
            console.warn('handleDrop: Target not found', targetKey);
            return;
        }
        if (dragInfo.type === _drag_types__rspack_import_5.DragType.TREE_ITEM && !(0,lodash__rspack_import_2.isNil)(dragInfo.data.key)) {
            move(String(dragInfo.data.key), targetPath, position);
            return;
        }
        const newItem = (0,_utils_tree_conversion_utils__rspack_import_6.createItemFromDragInfo)(dragInfo);
        if (!(0,lodash__rspack_import_2.isNil)(newItem)) {
            insert(newItem, targetPath, position);
            if (newItem.isOperator) {
                onOperatorAdded === null || onOperatorAdded === void 0 ? void 0 : onOperatorAdded(newItem);
            }
        }
    }, [
        findPath,
        move,
        insert,
        onOperatorAdded
    ]);
    const handleDropToRoot = (0,react__rspack_import_1.useCallback)((dragInfo)=>{
        if (dragInfo.type === _drag_types__rspack_import_5.DragType.TREE_ITEM && !(0,lodash__rspack_import_2.isNil)(dragInfo.data.key)) {
            const sourcePath = findPath(String(dragInfo.data.key));
            if (!(0,lodash__rspack_import_2.isNil)(sourcePath) && sourcePath.length === 1) {
                return;
            }
            move(String(dragInfo.data.key), [
                items.length
            ], _drag_types__rspack_import_5.DropPosition.BEFORE);
            return;
        }
        const newItem = (0,_utils_tree_conversion_utils__rspack_import_6.createItemFromDragInfo)(dragInfo);
        if (!(0,lodash__rspack_import_2.isNil)(newItem)) {
            appendToRoot(newItem);
            if (newItem.isOperator) {
                onOperatorAdded === null || onOperatorAdded === void 0 ? void 0 : onOperatorAdded(newItem);
            }
        }
    }, [
        items,
        findPath,
        move,
        appendToRoot,
        onOperatorAdded
    ]);
    const value = (0,react__rspack_import_1.useMemo)(()=>({
            items,
            operatorRegistry,
            fieldDefinitionRegistry,
            findPath,
            getItem,
            deleteByKey,
            insert,
            move,
            updateItemAttributes,
            canDrop,
            canDropToRoot,
            isValidDragType,
            handleDrop,
            handleDropToRoot
        }), [
        items,
        operatorRegistry,
        fieldDefinitionRegistry,
        findPath,
        getItem,
        deleteByKey,
        insert,
        move,
        updateItemAttributes,
        canDrop,
        canDropToRoot,
        isValidDragType,
        handleDrop,
        handleDropToRoot
    ]);
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(TreeContext.Provider, {
        value: value,
        children: children
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/hooks/use-tree-context.tsx",
        lineNumber: 174,
        columnNumber: 5
    }, undefined);
};
_s1(TreeProvider, "XvW3qcar5zo2s6PaOnoYtw4i8JY=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_3.useInjection,
        _pimcore_studio_ui_bundle_app__rspack_import_3.useInjection,
        _use_tree_state__rspack_import_4.useTreeState
    ];
});
_c = TreeProvider;
var _c;
$RefreshReg$(_c, "TreeProvider");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/hooks/use-tree-nodes.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  useTreeNodes: () => (useTreeNodes)
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
/* import */ var _tree_item_tree_item__rspack_import_5 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/tree-item/tree-item.ts");
/* import */ var _definitions_system_column_definitions__rspack_import_6 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/definitions/system-column-definitions.ts");
/* import */ var _operators_hooks_use_operator__rspack_import_7 = __webpack_require__("./js/src/modules/operators/hooks/use-operator.tsx");
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






const useTreeNodes = (param)=>{
    let { items, operatorRegistry, fieldDefinitionRegistry, disabled = false } = param;
    _s();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation)();
    const { getLocalizedName, getIcon } = (0,_operators_hooks_use_operator__rspack_import_7.useOperator)();
    return (0,react__rspack_import_1.useMemo)(()=>{
        const buildOperatorDisplay = (item)=>{
            const operatorType = operatorRegistry.getDynamicType(String(item.attributes.class ?? ''), false);
            const config = {
                key: item.key,
                isOperator: true,
                attributes: item.attributes
            };
            if (operatorType === null) {
                return {
                    icon: undefined,
                    iconProps: undefined,
                    title: String(item.attributes.label ?? '')
                };
            }
            const iconProps = getIcon(operatorType, operatorRegistry);
            return {
                icon: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Icon, {
                    ...iconProps,
                    iconColorGroup: "operator"
                }, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/hooks/use-tree-nodes.tsx",
                    lineNumber: 70,
                    columnNumber: 15
                }, undefined),
                iconProps,
                title: operatorType.getLabel(config, getLocalizedName(operatorType)) ?? String(item.attributes.label ?? '')
            };
        };
        const buildSystemColumnDisplay = (item)=>{
            const attributeValue = item.attributes.attribute ?? '';
            const systemColumn = _definitions_system_column_definitions__rspack_import_6.systemColumnLookup.get(attributeValue);
            if (systemColumn === undefined) {
                return {
                    icon: undefined,
                    iconProps: undefined,
                    title: undefined
                };
            }
            return {
                icon: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Icon, {
                    ...systemColumn.iconProps
                }, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/hooks/use-tree-nodes.tsx",
                    lineNumber: 88,
                    columnNumber: 15
                }, undefined),
                iconProps: systemColumn.iconProps,
                title: t(systemColumn.translationKey)
            };
        };
        const buildFieldDefinitionDisplay = (item)=>{
            const fieldDef = fieldDefinitionRegistry.getDynamicType(item.attributes.dataType ?? '', false);
            const iconProps = fieldDef === null || fieldDef === void 0 ? void 0 : fieldDef.getIcon();
            return {
                icon: (0,lodash__rspack_import_4.isNil)(iconProps) ? undefined : /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Icon, {
                    ...iconProps,
                    iconColorGroup: "fieldDefinition"
                }, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/hooks/use-tree-nodes.tsx",
                    lineNumber: 102,
                    columnNumber: 13
                }, undefined),
                iconProps,
                title: item.attributes.label ?? item.attributes.attribute ?? ''
            };
        };
        const getNodeDisplay = (item)=>{
            if (item.isOperator) return buildOperatorDisplay(item);
            if (item.attributes.dataType === 'system') return buildSystemColumnDisplay(item);
            return buildFieldDefinitionDisplay(item);
        };
        const buildNode = (item)=>{
            var _item_attributes_children;
            const treeItem = (0,_tree_item_tree_item__rspack_import_5.createTreeItem)(item, operatorRegistry);
            const display = getNodeDisplay(item);
            // When disabled, show only 'view' action for operators, no actions for fields
            let actions = [];
            if (disabled) {
                if (item.isOperator) {
                    actions = [
                        {
                            key: 'view',
                            icon: 'view'
                        }
                    ];
                }
            } else {
                actions = treeItem.getActions();
            }
            return {
                key: item.key,
                title: display.title,
                icon: display.icon,
                iconProps: display.iconProps,
                className: 'ant-tree-node--has-drag-and-drop',
                actions,
                itemData: item,
                children: (_item_attributes_children = item.attributes.children) === null || _item_attributes_children === void 0 ? void 0 : _item_attributes_children.map(buildNode)
            };
        };
        return items.map(buildNode);
    }, [
        items,
        operatorRegistry,
        fieldDefinitionRegistry,
        disabled
    ]);
};
_s(useTreeNodes, "V2s0DJ78ap3A58SDfu4QrJbGgcU=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation,
        _operators_hooks_use_operator__rspack_import_7.useOperator
    ];
});

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/hooks/use-tree-state.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  useTreeState: () => (useTreeState)
});
/* import */ var react__rspack_import_0 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_0_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_0);
/* import */ var lodash__rspack_import_1 = __webpack_require__("./node_modules/lodash/lodash.js");
/* import */ var lodash__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(lodash__rspack_import_1);
/* import */ var _utils_tree_operations__rspack_import_2 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/utils/tree-operations.ts");
/* import */ var _drag_types__rspack_import_3 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/drag-types.ts");
/* import */ var _utils_tree_conversion_utils__rspack_import_4 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/utils/tree-conversion-utils.ts");
/* import */ var _utils_tree_validation_utils__rspack_import_5 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/utils/tree-validation-utils.ts");
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





const useTreeState = (param)=>{
    let { entityConfig, onEntityConfigChange, operatorRegistry } = param;
    const isInternalUpdate = (0,react__rspack_import_0.useRef)(false);
    const [items, setItems] = (0,react__rspack_import_0.useState)(()=>{
        var _entityConfig_columnConfig;
        const externalColumns = (entityConfig === null || entityConfig === void 0 ? void 0 : (_entityConfig_columnConfig = entityConfig.columnConfig) === null || _entityConfig_columnConfig === void 0 ? void 0 : _entityConfig_columnConfig.columns) ?? [];
        return (0,_utils_tree_operations__rspack_import_2.ensureKeys)((0,_utils_tree_conversion_utils__rspack_import_4.persistedColumnsToInternalNodes)(externalColumns));
    });
    const itemsRef = (0,react__rspack_import_0.useRef)(items);
    itemsRef.current = items;
    // Sync from entityConfig when it changes externally
    (0,react__rspack_import_0.useEffect)(()=>{
        var _entityConfig_columnConfig;
        if (isInternalUpdate.current) {
            isInternalUpdate.current = false;
            return;
        }
        const externalColumns = (entityConfig === null || entityConfig === void 0 ? void 0 : (_entityConfig_columnConfig = entityConfig.columnConfig) === null || _entityConfig_columnConfig === void 0 ? void 0 : _entityConfig_columnConfig.columns) ?? [];
        setItems((0,_utils_tree_operations__rspack_import_2.ensureKeys)((0,_utils_tree_conversion_utils__rspack_import_4.persistedColumnsToInternalNodes)(externalColumns)));
    }, [
        entityConfig
    ]);
    const updateItems = (0,react__rspack_import_0.useCallback)((newItems)=>{
        isInternalUpdate.current = true;
        itemsRef.current = newItems;
        setItems(newItems);
        const columns = (0,_utils_tree_conversion_utils__rspack_import_4.internalNodesToPersistedColumns)(newItems);
        const updatedConfig = {
            ...entityConfig,
            columnConfig: {
                ...entityConfig === null || entityConfig === void 0 ? void 0 : entityConfig.columnConfig,
                columns
            }
        };
        onEntityConfigChange(updatedConfig);
    }, [
        entityConfig,
        onEntityConfigChange
    ]);
    const findPath = (0,react__rspack_import_0.useCallback)((key)=>{
        return (0,_utils_tree_operations__rspack_import_2.findItemPath)(items, key);
    }, [
        items
    ]);
    const getItem = (0,react__rspack_import_0.useCallback)((path)=>{
        return (0,_utils_tree_operations__rspack_import_2.getItemAtPath)(items, path);
    }, [
        items
    ]);
    const deleteByKey = (0,react__rspack_import_0.useCallback)((key)=>{
        const path = (0,_utils_tree_operations__rspack_import_2.findItemPath)(items, key);
        if ((0,lodash__rspack_import_1.isNil)(path)) return;
        const { items: newItems } = (0,_utils_tree_operations__rspack_import_2.removeAtPath)(items, path);
        updateItems(newItems);
    }, [
        items,
        updateItems
    ]);
    const insert = (0,react__rspack_import_0.useCallback)((item, targetPath, position)=>{
        const newItems = (0,_utils_tree_operations__rspack_import_2.insertAtPath)(items, item, targetPath, position);
        updateItems(newItems);
    }, [
        items,
        updateItems
    ]);
    const appendToRoot = (0,react__rspack_import_0.useCallback)((item)=>{
        const newItems = [
            ...items,
            item
        ];
        updateItems(newItems);
    }, [
        items,
        updateItems
    ]);
    const updateItemAttributes = (0,react__rspack_import_0.useCallback)((key, attributes)=>{
        const newItems = (0,_utils_tree_operations__rspack_import_2.mapTree)(items, (item)=>item.key === key ? {
                ...item,
                attributes: {
                    ...item.attributes,
                    ...attributes
                }
            } : item);
        updateItems(newItems);
    }, [
        items,
        updateItems
    ]);
    const move = (0,react__rspack_import_0.useCallback)((sourceKey, targetPath, position)=>{
        const sourcePath = (0,_utils_tree_operations__rspack_import_2.findItemPath)(items, sourceKey);
        if ((0,lodash__rspack_import_1.isNil)(sourcePath)) return;
        const { items: afterRemove, removed } = (0,_utils_tree_operations__rspack_import_2.removeAtPath)(items, sourcePath);
        if ((0,lodash__rspack_import_1.isNil)(removed)) return;
        const adjustedPath = (0,_utils_tree_operations__rspack_import_2.adjustPathAfterRemoval)(targetPath, sourcePath);
        const newItems = (0,_utils_tree_operations__rspack_import_2.insertAtPath)(afterRemove, removed, adjustedPath, position);
        updateItems(newItems);
    }, [
        items,
        updateItems
    ]);
    const canDrop = (0,react__rspack_import_0.useCallback)((dragInfo, targetKey, position)=>{
        const currentItems = itemsRef.current;
        if (dragInfo.type === _drag_types__rspack_import_3.DragType.TREE_ITEM && dragInfo.data.key === targetKey) {
            return false;
        }
        const targetPath = (0,_utils_tree_operations__rspack_import_2.findItemPath)(currentItems, targetKey);
        if ((0,lodash__rspack_import_1.isNil)(targetPath)) return false;
        const targetData = (0,_utils_tree_operations__rspack_import_2.getItemAtPath)(currentItems, targetPath);
        if ((0,lodash__rspack_import_1.isNil)(targetData)) return false;
        if (position === _drag_types__rspack_import_3.DropPosition.INTO) {
            return (0,_utils_tree_validation_utils__rspack_import_5.validateDropIntoTarget)(dragInfo, targetData, targetPath, currentItems, operatorRegistry);
        }
        if (targetPath.length <= 1) {
            return true;
        }
        const parentPath = targetPath.slice(0, -1);
        const parentData = (0,_utils_tree_operations__rspack_import_2.getItemAtPath)(currentItems, parentPath);
        return (0,lodash__rspack_import_1.isNil)(parentData) || (0,_utils_tree_validation_utils__rspack_import_5.validateDropToParent)(dragInfo, parentData, targetPath, currentItems, operatorRegistry);
    }, [
        operatorRegistry
    ]);
    const canDropToRoot = (0,react__rspack_import_0.useCallback)((_dragInfo)=>{
        return true;
    }, []);
    return {
        items,
        updateItems,
        updateItemAttributes,
        findPath,
        getItem,
        deleteByKey,
        insert,
        appendToRoot,
        move,
        canDrop,
        canDropToRoot
    };
};

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/index.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  AvailableFieldsTree: () => (/* reexport safe */ _available_fields_tree__rspack_import_0.AvailableFieldsTree)
});
/* import */ var _available_fields_tree__rspack_import_0 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/available-fields-tree.tsx");
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
"./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/source-config-utils.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  createSourceConfigFromAttributes: () => (createSourceConfigFromAttributes),
  createSourceConfigFromDragInfo: () => (createSourceConfigFromDragInfo)
});
/* import */ var lodash__rspack_import_0 = __webpack_require__("./node_modules/lodash/lodash.js");
/* import */ var lodash__rspack_import_0_default = /*#__PURE__*/__webpack_require__.n(lodash__rspack_import_0);
/* import */ var _drag_types__rspack_import_1 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/drag-types.ts");
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

function createSourceConfigFromDragInfo(dragInfo) {
    var _dragInfo_data, _dragInfo_data1;
    return {
        dataType: !(0,lodash__rspack_import_0.isNil)(dragInfo) && dragInfo.type === _drag_types__rspack_import_1.DragType.CLASS_ATTRIBUTE && !(0,lodash__rspack_import_0.isNil)((_dragInfo_data = dragInfo.data) === null || _dragInfo_data === void 0 ? void 0 : _dragInfo_data.dataType) ? String(dragInfo.data.dataType) : undefined,
        isOperator: !(0,lodash__rspack_import_0.isNil)(dragInfo) && dragInfo.type === _drag_types__rspack_import_1.DragType.OPERATOR,
        operatorClass: !(0,lodash__rspack_import_0.isNil)(dragInfo) && dragInfo.type === _drag_types__rspack_import_1.DragType.OPERATOR && !(0,lodash__rspack_import_0.isNil)((_dragInfo_data1 = dragInfo.data) === null || _dragInfo_data1 === void 0 ? void 0 : _dragInfo_data1.operatorId) ? String(dragInfo.data.operatorId) : undefined
    };
}
function createSourceConfigFromAttributes(attributes, isOperator) {
    return {
        dataType: attributes.dataType,
        isOperator,
        operatorClass: isOperator && !(0,lodash__rspack_import_0.isNil)(attributes.class) ? String(attributes.class) : undefined
    };
}

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/tree-item/tree-item.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  FieldDefinitionItem: () => (FieldDefinitionItem),
  OperatorItem: () => (OperatorItem),
  TreeItem: () => (TreeItem),
  createTreeItem: () => (createTreeItem)
});
/* import */ var _source_config_utils__rspack_import_0 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/source-config-utils.ts");
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
class TreeItem {
    toData() {
        return {
            key: this.key,
            isOperator: this.isOperator,
            attributes: {
                ...this.attributes
            }
        };
    }
    constructor(key, attributes){
        this.key = key;
        this.attributes = attributes;
    }
}
class FieldDefinitionItem extends TreeItem {
    canHaveChildren() {
        return false;
    }
    canAcceptChild(_child) {
        let _isMovingWithinSameParent = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : false;
        return false;
    }
    getActions() {
        return [
            {
                key: 'delete',
                icon: 'trash'
            }
        ];
    }
    getChildren(_operatorRegistry) {
        return [];
    }
    constructor(...args){
        super(...args), this.isOperator = false;
    }
}
class OperatorItem extends TreeItem {
    getOperatorType() {
        return this.operatorRegistry.getDynamicType(String(this.attributes.class ?? ''), false);
    }
    canHaveChildren() {
        var _operatorType_allowsChildren;
        const operatorType = this.getOperatorType();
        if (operatorType === undefined) return false;
        return ((_operatorType_allowsChildren = operatorType.allowsChildren) === null || _operatorType_allowsChildren === void 0 ? void 0 : _operatorType_allowsChildren.call(operatorType)) ?? false;
    }
    canAcceptChild(child) {
        let isMovingWithinSameParent = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : false;
        var _operatorType_allowChild;
        const operatorType = this.getOperatorType();
        if (operatorType === undefined) return false;
        if (!this.canHaveChildren()) return false;
        if (isMovingWithinSameParent) return true;
        const targetConfig = this.toData();
        const sourceConfig = (0,_source_config_utils__rspack_import_0.createSourceConfigFromAttributes)(child.attributes, child.isOperator);
        return ((_operatorType_allowChild = operatorType.allowChild) === null || _operatorType_allowChild === void 0 ? void 0 : _operatorType_allowChild.call(operatorType, targetConfig, sourceConfig)) ?? true;
    }
    getActions() {
        return [
            {
                key: 'edit',
                icon: 'edit'
            },
            {
                key: 'delete',
                icon: 'trash'
            }
        ];
    }
    getChildren(operatorRegistry) {
        const children = this.attributes.children ?? [];
        return children.map((child)=>createTreeItem(child, operatorRegistry));
    }
    constructor(key, attributes, operatorRegistry){
        super(key, attributes), this.operatorRegistry = operatorRegistry, this.isOperator = true;
    }
}
function createTreeItem(data, operatorRegistry) {
    if (data.isOperator) {
        return new OperatorItem(data.key, data.attributes, operatorRegistry);
    }
    return new FieldDefinitionItem(data.key, data.attributes);
}

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/tree-node-renderer.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  TreeNodeRenderer: () => (TreeNodeRenderer)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var lodash__rspack_import_2 = __webpack_require__("./node_modules/lodash/lodash.js");
/* import */ var lodash__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(lodash__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_3);
/* import */ var _hooks_use_tree_context__rspack_import_4 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/hooks/use-tree-context.tsx");
/* import */ var _tree_item_tree_item__rspack_import_5 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/tree-item/tree-item.ts");
/* import */ var _drag_types__rspack_import_6 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/drag-types.ts");
/* import */ var _operators_hooks_use_operator__rspack_import_7 = __webpack_require__("./js/src/modules/operators/hooks/use-operator.tsx");
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






const TreeNodeRenderer = (param)=>{
    let { itemData, initialComponent, hasExpandedChildren, disabled = false } = param;
    _s();
    const { operatorRegistry, fieldDefinitionRegistry, canDrop, isValidDragType, handleDrop } = (0,_hooks_use_tree_context__rspack_import_4.useTreeContext)();
    const { getIcon } = (0,_operators_hooks_use_operator__rspack_import_7.useOperator)();
    const nodeKey = itemData.key;
    const treeItem = (0,_tree_item_tree_item__rspack_import_5.createTreeItem)(itemData, operatorRegistry);
    const isSelfDrag = (info)=>{
        return info.type === _drag_types__rspack_import_6.DragType.TREE_ITEM && info.data.key === nodeKey;
    };
    const hotspots = (0,react__rspack_import_1.useMemo)(()=>{
        const baseHotspots = [
            {
                id: 'sorting-top',
                className: 'dnd__sorting dnd__sorting--top',
                isValidContext: (info)=>isValidDragType(info),
                isValidData: (info)=>!isSelfDrag(info) && canDrop(info, nodeKey, _drag_types__rspack_import_6.DropPosition.BEFORE),
                position: {
                    x: 0,
                    y: 0,
                    width: '100%',
                    height: '30%'
                },
                onDrop: (info)=>{
                    handleDrop(info, nodeKey, _drag_types__rspack_import_6.DropPosition.BEFORE);
                }
            },
            {
                id: 'drop-middle',
                isValidContext: (info)=>isValidDragType(info),
                isValidData: (info)=>!isSelfDrag(info) && treeItem.canHaveChildren() && canDrop(info, nodeKey, _drag_types__rspack_import_6.DropPosition.INTO),
                position: {
                    x: '0',
                    y: '30%',
                    width: '100%',
                    height: '40%'
                },
                onDrop: (info)=>{
                    handleDrop(info, nodeKey, _drag_types__rspack_import_6.DropPosition.INTO);
                }
            }
        ];
        if (!hasExpandedChildren) {
            baseHotspots.push({
                id: 'sorting-bottom',
                className: 'dnd__sorting dnd__sorting--bottom',
                isValidContext: (info)=>isValidDragType(info),
                isValidData: (info)=>!isSelfDrag(info) && canDrop(info, nodeKey, _drag_types__rspack_import_6.DropPosition.AFTER),
                position: {
                    x: 0,
                    y: '70%',
                    width: '100%',
                    height: '30%'
                },
                onDrop: (info)=>{
                    handleDrop(info, nodeKey, _drag_types__rspack_import_6.DropPosition.AFTER);
                }
            });
        }
        return baseHotspots;
    }, [
        nodeKey,
        treeItem,
        isValidDragType,
        canDrop,
        handleDrop,
        hasExpandedChildren
    ]);
    const iconProps = (0,react__rspack_import_1.useMemo)(()=>{
        if (itemData.isOperator && !(0,lodash__rspack_import_2.isNil)(itemData.attributes.class)) {
            const opType = operatorRegistry.getDynamicType(String(itemData.attributes.class), false);
            return (0,lodash__rspack_import_2.isNil)(opType) ? undefined : getIcon(opType, operatorRegistry);
        }
        if (!(0,lodash__rspack_import_2.isNil)(itemData.attributes.dataType)) {
            const fieldDef = fieldDefinitionRegistry.getDynamicType(String(itemData.attributes.dataType), false);
            return (fieldDef === null || fieldDef === void 0 ? void 0 : fieldDef.getIcon()) ?? undefined;
        }
        return undefined;
    }, [
        itemData,
        operatorRegistry,
        fieldDefinitionRegistry,
        getIcon
    ]);
    const dragInfo = (0,react__rspack_import_1.useMemo)(()=>({
            type: _drag_types__rspack_import_6.DragType.TREE_ITEM,
            data: {
                key: nodeKey,
                isOperator: itemData.isOperator,
                title: String(itemData.attributes.label ?? itemData.attributes.attribute ?? ''),
                dataType: itemData.attributes.dataType,
                operatorClass: itemData.attributes.class
            },
            icon: iconProps ?? {
                value: 'info'
            },
            title: String(itemData.attributes.label ?? itemData.attributes.attribute ?? '')
        }), [
        nodeKey,
        itemData,
        iconProps
    ]);
    return disabled ? /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(react_jsx_dev_runtime__rspack_import_0.Fragment, {
        children: initialComponent
    }, void 0, false) : /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.Draggable, {
        info: dragInfo,
        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_3.HotspotDroppable, {
            disableDndActiveIndicator: true,
            hotspots: hotspots,
            children: initialComponent
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/tree-node-renderer.tsx",
            lineNumber: 111,
            columnNumber: 9
        }, undefined)
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/tree-node-renderer.tsx",
        lineNumber: 110,
        columnNumber: 7
    }, undefined);
};
_s(TreeNodeRenderer, "spI3UNCesOE+Kn9t9doBlfQoD+U=", false, function() {
    return [
        _hooks_use_tree_context__rspack_import_4.useTreeContext,
        _operators_hooks_use_operator__rspack_import_7.useOperator
    ];
});
_c = TreeNodeRenderer;
var _c;
$RefreshReg$(_c, "TreeNodeRenderer");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/utils/tree-conversion-utils.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  createItemFromDragInfo: () => (createItemFromDragInfo),
  internalNodesToPersistedColumns: () => (internalNodesToPersistedColumns),
  persistedColumnsToInternalNodes: () => (persistedColumnsToInternalNodes)
});
/* import */ var _pimcore_studio_ui_bundle_utils__rspack_import_0 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/utils");
/* import */ var _pimcore_studio_ui_bundle_utils__rspack_import_0_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_utils__rspack_import_0);
/* import */ var lodash__rspack_import_1 = __webpack_require__("./node_modules/lodash/lodash.js");
/* import */ var lodash__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(lodash__rspack_import_1);
/* import */ var _drag_types__rspack_import_2 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/drag-types.ts");
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


function persistedColumnsToInternalNodes(columns) {
    return columns.map((col)=>{
        const { children: sourceChildren, ...restAttributes } = col.attributes;
        const attributes = {
            ...restAttributes
        };
        if (!(0,lodash__rspack_import_1.isNil)(sourceChildren)) {
            attributes.children = persistedColumnsToInternalNodes(sourceChildren);
        }
        return {
            key: String(col.key ?? ''),
            isOperator: col.isOperator,
            attributes
        };
    });
}
function internalNodesToPersistedColumns(nodes) {
    return nodes.map((node)=>{
        const { children: sourceChildren, ...restAttributes } = node.attributes;
        const attributes = {
            label: restAttributes.label ?? '',
            dataType: restAttributes.dataType ?? '',
            ...restAttributes
        };
        if (!(0,lodash__rspack_import_1.isNil)(sourceChildren)) {
            Object.assign(attributes, {
                children: internalNodesToPersistedColumns(sourceChildren)
            });
        }
        return {
            key: node.key,
            isOperator: node.isOperator,
            attributes
        };
    });
}
function createItemFromDragInfo(dragInfo) {
    if (dragInfo.type === _drag_types__rspack_import_2.DragType.CLASS_ATTRIBUTE) {
        return {
            key: (0,_pimcore_studio_ui_bundle_utils__rspack_import_0.uuid)(),
            isOperator: false,
            attributes: {
                attribute: String(dragInfo.data.attribute ?? dragInfo.data.key ?? ''),
                label: String(dragInfo.data.title ?? ''),
                dataType: String(dragInfo.data.dataType ?? 'text')
            }
        };
    }
    if (dragInfo.type === _drag_types__rspack_import_2.DragType.OPERATOR) {
        return {
            key: (0,_pimcore_studio_ui_bundle_utils__rspack_import_0.uuid)(),
            isOperator: true,
            attributes: {
                label: String(dragInfo.data.title ?? ''),
                class: String(dragInfo.data.operatorId ?? ''),
                type: _drag_types__rspack_import_2.DragType.OPERATOR,
                children: []
            }
        };
    }
    return null;
}

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/utils/tree-operations.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  adjustPathAfterRemoval: () => (adjustPathAfterRemoval),
  cloneItems: () => (cloneItems),
  collectAllKeys: () => (collectAllKeys),
  ensureKeys: () => (ensureKeys),
  findItemPath: () => (findItemPath),
  getItemAtPath: () => (getItemAtPath),
  insertAtPath: () => (insertAtPath),
  mapTree: () => (mapTree),
  moveItem: () => (moveItem),
  removeAtPath: () => (removeAtPath)
});
/* import */ var _pimcore_studio_ui_bundle_utils__rspack_import_0 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/utils");
/* import */ var _pimcore_studio_ui_bundle_utils__rspack_import_0_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_utils__rspack_import_0);
/* import */ var lodash__rspack_import_1 = __webpack_require__("./node_modules/lodash/lodash.js");
/* import */ var lodash__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(lodash__rspack_import_1);
/* import */ var _drag_types__rspack_import_2 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/drag-types.ts");
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


function mapTree(items, transform) {
    return items.map((item)=>{
        const transformed = transform(item);
        return {
            ...transformed,
            attributes: {
                ...transformed.attributes,
                ...Array.isArray(item.attributes.children) ? {
                    children: mapTree(item.attributes.children, transform)
                } : {}
            }
        };
    });
}
function cloneItems(items) {
    return mapTree(items, (item)=>({
            ...item,
            attributes: {
                ...item.attributes
            }
        }));
}
function ensureKeys(items) {
    return mapTree(items, (item)=>({
            ...item,
            key: (0,_pimcore_studio_ui_bundle_utils__rspack_import_0.isNonEmptyString)(item.key) ? item.key : (0,_pimcore_studio_ui_bundle_utils__rspack_import_0.uuid)(),
            attributes: {
                ...item.attributes
            }
        }));
}
function findItemPath(items, key) {
    let currentPath = arguments.length > 2 && arguments[2] !== void 0 ? arguments[2] : [];
    for(let i = 0; i < items.length; i++){
        const item = items[i];
        const itemPath = [
            ...currentPath,
            i
        ];
        if (item.key === key) {
            return itemPath;
        }
        if (Array.isArray(item.attributes.children)) {
            const childPath = findItemPath(item.attributes.children, key, itemPath);
            if (!(0,lodash__rspack_import_1.isNil)(childPath)) {
                return childPath;
            }
        }
    }
    return null;
}
function getItemAtPath(items, path) {
    if (path.length === 0) return null;
    let current = items[path[0]] ?? null;
    for(let i = 1; i < path.length && !(0,lodash__rspack_import_1.isNil)(current); i++){
        const children = current.attributes.children;
        if (!Array.isArray(children)) return null;
        current = children[path[i]] ?? null;
    }
    return current;
}
function getParentContext(items, path) {
    if (path.length === 0) return {
        parent: null,
        index: -1
    };
    if (path.length === 1) return {
        parent: items,
        index: path[0]
    };
    const parent = getItemAtPath(items, path.slice(0, -1));
    if ((0,lodash__rspack_import_1.isNil)(parent) || !Array.isArray(parent.attributes.children)) {
        return {
            parent: null,
            index: -1
        };
    }
    return {
        parent: parent.attributes.children,
        index: path[path.length - 1]
    };
}
function insertAtPath(items, item, path) {
    let position = arguments.length > 3 && arguments[3] !== void 0 ? arguments[3] : _drag_types__rspack_import_2.DropPosition.AFTER;
    const newItems = cloneItems(items);
    const itemWithKey = {
        ...item,
        key: (0,_pimcore_studio_ui_bundle_utils__rspack_import_0.isNonEmptyString)(item.key) ? item.key : (0,_pimcore_studio_ui_bundle_utils__rspack_import_0.uuid)(),
        attributes: {
            ...item.attributes,
            ...Array.isArray(item.attributes.children) ? {
                children: cloneItems(item.attributes.children)
            } : {}
        }
    };
    if (path.length === 0) {
        newItems.push(itemWithKey);
        return newItems;
    }
    if (position === _drag_types__rspack_import_2.DropPosition.INTO) {
        const target = getItemAtPath(newItems, path);
        if ((0,lodash__rspack_import_1.isNil)(target)) return items;
        if (!Array.isArray(target.attributes.children)) {
            target.attributes.children = [];
        }
        target.attributes.children.push(itemWithKey);
        return newItems;
    }
    const { parent, index } = getParentContext(newItems, path);
    if ((0,lodash__rspack_import_1.isNil)(parent) || index === -1) return items;
    const insertIndex = position === _drag_types__rspack_import_2.DropPosition.BEFORE ? index : index + 1;
    parent.splice(insertIndex, 0, itemWithKey);
    return newItems;
}
function removeAtPath(items, path) {
    if (path.length === 0) {
        return {
            items,
            removed: null
        };
    }
    const newItems = cloneItems(items);
    const { parent, index } = getParentContext(newItems, path);
    if ((0,lodash__rspack_import_1.isNil)(parent) || index === -1 || index >= parent.length) {
        return {
            items,
            removed: null
        };
    }
    const [removed] = parent.splice(index, 1);
    return {
        items: newItems,
        removed
    };
}
function moveItem(items, fromPath, toPath) {
    let position = arguments.length > 3 && arguments[3] !== void 0 ? arguments[3] : _drag_types__rspack_import_2.DropPosition.AFTER;
    const { items: afterRemove, removed } = removeAtPath(items, fromPath);
    if ((0,lodash__rspack_import_1.isNil)(removed)) return items;
    // Adjust toPath if it was affected by the removal
    const adjustedToPath = adjustPathAfterRemoval(toPath, fromPath);
    return insertAtPath(afterRemove, removed, adjustedToPath, position);
}
function adjustPathAfterRemoval(path, removedPath) {
    if (path.length === 0 || removedPath.length === 0) return path;
    if (path.length >= removedPath.length) {
        const compareIndex = removedPath.length - 1;
        if (removedPath.slice(0, compareIndex).every((v, i)=>v === path[i]) && removedPath[compareIndex] < path[compareIndex]) {
            // Decrement the affected index since an earlier sibling was removed
            const adjusted = [
                ...path
            ];
            adjusted[compareIndex]--;
            return adjusted;
        }
    }
    return path;
}
function collectAllKeys(items) {
    return (0,lodash__rspack_import_1.flatMap)(items, (item)=>{
        const children = item.attributes.children;
        return [
            item.key,
            ...Array.isArray(children) ? collectAllKeys(children) : []
        ];
    });
}

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/utils/tree-validation-utils.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  validateDropIntoTarget: () => (validateDropIntoTarget),
  validateDropToParent: () => (validateDropToParent)
});
/* import */ var lodash__rspack_import_0 = __webpack_require__("./node_modules/lodash/lodash.js");
/* import */ var lodash__rspack_import_0_default = /*#__PURE__*/__webpack_require__.n(lodash__rspack_import_0);
/* import */ var _tree_item_tree_item__rspack_import_1 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/tree-item/tree-item.ts");
/* import */ var _tree_operations__rspack_import_2 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/utils/tree-operations.ts");
/* import */ var _drag_types__rspack_import_3 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/drag-types.ts");
/* import */ var _tree_conversion_utils__rspack_import_4 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/utils/tree-conversion-utils.ts");
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




function getSourceTreeItem(dragInfo, currentItems, operatorRegistry) {
    if (dragInfo.type !== _drag_types__rspack_import_3.DragType.TREE_ITEM || (0,lodash__rspack_import_0.isNil)(dragInfo.data.key)) {
        return null;
    }
    const sourcePath = (0,_tree_operations__rspack_import_2.findItemPath)(currentItems, String(dragInfo.data.key));
    if ((0,lodash__rspack_import_0.isNil)(sourcePath)) return null;
    const sourceData = (0,_tree_operations__rspack_import_2.getItemAtPath)(currentItems, sourcePath);
    if ((0,lodash__rspack_import_0.isNil)(sourceData)) return null;
    return {
        item: (0,_tree_item_tree_item__rspack_import_1.createTreeItem)(sourceData, operatorRegistry),
        path: sourcePath
    };
}
function getDraggedTreeItem(dragInfo, operatorRegistry) {
    const newItemData = (0,_tree_conversion_utils__rspack_import_4.createItemFromDragInfo)(dragInfo);
    if ((0,lodash__rspack_import_0.isNil)(newItemData)) return null;
    return (0,_tree_item_tree_item__rspack_import_1.createTreeItem)(newItemData, operatorRegistry);
}
function isChildPath(childPath, parentPath) {
    return childPath.length === parentPath.length + 1 && childPath.slice(0, -1).every((v, i)=>v === parentPath[i]);
}
function hasSameParent(path1, path2) {
    if (path1.length !== path2.length) return false;
    return path1.slice(0, -1).every((v, i)=>v === path2.slice(0, -1)[i]);
}
function validateDropIntoTarget(dragInfo, targetData, targetPath, currentItems, operatorRegistry) {
    const targetItem = (0,_tree_item_tree_item__rspack_import_1.createTreeItem)(targetData, operatorRegistry);
    if (!targetItem.canHaveChildren()) return false;
    const source = getSourceTreeItem(dragInfo, currentItems, operatorRegistry);
    if (!(0,lodash__rspack_import_0.isNil)(source)) {
        const isAlreadyChild = isChildPath(source.path, targetPath);
        return targetItem.canAcceptChild(source.item, isAlreadyChild);
    }
    const draggedItem = getDraggedTreeItem(dragInfo, operatorRegistry);
    return !(0,lodash__rspack_import_0.isNil)(draggedItem) && targetItem.canAcceptChild(draggedItem, false);
}
function validateDropToParent(dragInfo, parentData, targetPath, currentItems, operatorRegistry) {
    const parentItem = (0,_tree_item_tree_item__rspack_import_1.createTreeItem)(parentData, operatorRegistry);
    const source = getSourceTreeItem(dragInfo, currentItems, operatorRegistry);
    if (!(0,lodash__rspack_import_0.isNil)(source)) {
        if (hasSameParent(source.path, targetPath)) return true;
        return parentItem.canAcceptChild(source.item, false);
    }
    const draggedItem = getDraggedTreeItem(dragInfo, operatorRegistry);
    return !(0,lodash__rspack_import_0.isNil)(draggedItem) && parentItem.canAcceptChild(draggedItem, false);
}

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/components/add-all-definitions-button.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  AddAllDefinitionsButton: () => (AddAllDefinitionsButton)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_3);
/* import */ var _pimcore_studio_ui_bundle_utils__rspack_import_4 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/utils");
/* import */ var _pimcore_studio_ui_bundle_utils__rspack_import_4_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_utils__rspack_import_4);
/* import */ var lodash__rspack_import_5 = __webpack_require__("./node_modules/lodash/lodash.js");
/* import */ var lodash__rspack_import_5_default = /*#__PURE__*/__webpack_require__.n(lodash__rspack_import_5);
/* import */ var _hooks_use_class_attributes_tree__rspack_import_6 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/hooks/use-class-attributes-tree.tsx");
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





const AddAllDefinitionsButton = (param)=>{
    let { classId, enabled, entityConfig, onEntityConfigChange } = param;
    _s();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation)();
    const { getFieldDefinitions } = (0,_hooks_use_class_attributes_tree__rspack_import_6.useClassAttributesTree)({
        classId,
        enabled
    });
    const handleAddAllDefinitions = (0,react__rspack_import_1.useCallback)(()=>{
        var _entityConfig_columnConfig;
        if ((0,lodash__rspack_import_5.isNil)(entityConfig)) return;
        const leafAttributes = getFieldDefinitions();
        const currentColumns = ((_entityConfig_columnConfig = entityConfig.columnConfig) === null || _entityConfig_columnConfig === void 0 ? void 0 : _entityConfig_columnConfig.columns) ?? [];
        const existingAttributes = new Set(currentColumns.map((col)=>{
            var _col_attributes;
            return (_col_attributes = col.attributes) === null || _col_attributes === void 0 ? void 0 : _col_attributes.attribute;
        }).filter((attr)=>!(0,lodash__rspack_import_5.isNil)(attr)));
        const newColumns = leafAttributes.filter((attr)=>!(0,lodash__rspack_import_5.isNil)(attr.attribute) && !existingAttributes.has(attr.attribute)).map((attr)=>({
                key: (0,_pimcore_studio_ui_bundle_utils__rspack_import_4.uuid)(),
                isOperator: false,
                attributes: {
                    attribute: attr.attribute,
                    label: String(attr.title ?? attr.attribute),
                    dataType: attr.dataType ?? 'text'
                }
            }));
        if (newColumns.length > 0) {
            onEntityConfigChange({
                ...entityConfig,
                columnConfig: {
                    ...entityConfig.columnConfig,
                    columns: [
                        ...currentColumns,
                        ...newColumns
                    ]
                }
            });
        }
    }, [
        entityConfig,
        getFieldDefinitions,
        onEntityConfigChange
    ]);
    const allDefinitionsAdded = (0,react__rspack_import_1.useCallback)(()=>{
        var _entityConfig_columnConfig;
        if ((0,lodash__rspack_import_5.isNil)(entityConfig)) return false;
        const leafAttributes = getFieldDefinitions();
        const currentColumns = ((_entityConfig_columnConfig = entityConfig.columnConfig) === null || _entityConfig_columnConfig === void 0 ? void 0 : _entityConfig_columnConfig.columns) ?? [];
        const existingAttributes = new Set(currentColumns.map((col)=>{
            var _col_attributes;
            return (_col_attributes = col.attributes) === null || _col_attributes === void 0 ? void 0 : _col_attributes.attribute;
        }).filter((attr)=>!(0,lodash__rspack_import_5.isNil)(attr)));
        const newAttributesCount = leafAttributes.filter((attr)=>!(0,lodash__rspack_import_5.isNil)(attr.attribute) && !existingAttributes.has(attr.attribute)).length;
        return newAttributesCount === 0;
    }, [
        entityConfig,
        getFieldDefinitions
    ]);
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Button, {
        disabled: allDefinitionsAdded(),
        onClick: handleAddAllDefinitions,
        children: t('data-hub.schema.insert-all-definitions')
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/components/add-all-definitions-button.tsx",
        lineNumber: 89,
        columnNumber: 5
    }, undefined);
};
_s(AddAllDefinitionsButton, "xD5Za95Gk/wLQawSFo1GTC57VcA=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation,
        _hooks_use_class_attributes_tree__rspack_import_6.useClassAttributesTree
    ];
});
_c = AddAllDefinitionsButton;
var _c;
$RefreshReg$(_c, "AddAllDefinitionsButton");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/components/class-attributes-sidebar.styles.tsx"(module, __webpack_exports__, __webpack_require__) {
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
      .ant-tree-title {
        white-space: nowrap;
      }
    `
    };
});

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/components/class-attributes-sidebar.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  ClassAttributesSidebar: () => (ClassAttributesSidebar)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_3);
/* import */ var _draggable_tree_title__rspack_import_4 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/components/draggable-tree-title.tsx");
/* import */ var _hooks_use_class_attributes_tree__rspack_import_5 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/hooks/use-class-attributes-tree.tsx");
/* import */ var _class_attributes_sidebar_styles__rspack_import_6 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/components/class-attributes-sidebar.styles.tsx");
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





const ClassAttributesSidebar = (param)=>{
    let { classId, enabled } = param;
    _s();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation)();
    const { styles } = (0,_class_attributes_sidebar_styles__rspack_import_6.useStyles)();
    const [searchValue, setSearchValue] = (0,react__rspack_import_1.useState)('');
    const { filteredTree, expandedKeys, isLoading } = (0,_hooks_use_class_attributes_tree__rspack_import_5.useClassAttributesTree)({
        classId,
        enabled,
        searchValue
    });
    const titleRender = (0,react__rspack_import_1.useCallback)((node, initialComponent)=>{
        return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_draggable_tree_title__rspack_import_4.DraggableTreeTitle, {
            initialComponent: initialComponent,
            node: node
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/components/class-attributes-sidebar.tsx",
            lineNumber: 39,
            columnNumber: 7
        }, undefined);
    }, []);
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.ContentLayout, {
        renderTopBar: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(react_jsx_dev_runtime__rspack_import_0.Fragment, {
            children: [
                /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.SidebarTitle, {
                    withBorder: true,
                    children: t('data-hub.schema.class-attributes')
                }, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/components/class-attributes-sidebar.tsx",
                    lineNumber: 50,
                    columnNumber: 11
                }, undefined),
                /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Box, {
                    padding: 'small',
                    children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.SearchInput, {
                        onChange: (e)=>{
                            setSearchValue(e.target.value);
                        },
                        placeholder: t('search'),
                        style: {
                            width: '100%',
                            maxWidth: '100%'
                        },
                        value: searchValue,
                        withoutAddon: true
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/components/class-attributes-sidebar.tsx",
                        lineNumber: 54,
                        columnNumber: 13
                    }, undefined)
                }, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/components/class-attributes-sidebar.tsx",
                    lineNumber: 53,
                    columnNumber: 11
                }, undefined)
            ]
        }, void 0, true),
        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Content, {
            loading: isLoading,
            padding: {
                x: 'small',
                top: 'extra-small',
                bottom: 'small'
            },
            children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.TreeElement, {
                className: styles.treeContainer,
                defaultExpandedKeys: expandedKeys,
                draggable: false,
                selectable: false,
                showIcon: true,
                titleRender: titleRender,
                treeData: filteredTree
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/components/class-attributes-sidebar.tsx",
                lineNumber: 70,
                columnNumber: 9
            }, undefined)
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/components/class-attributes-sidebar.tsx",
            lineNumber: 65,
            columnNumber: 7
        }, undefined)
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/components/class-attributes-sidebar.tsx",
        lineNumber: 47,
        columnNumber: 5
    }, undefined);
};
_s(ClassAttributesSidebar, "tiVAav0zG+VSl3qVLZD+/CquAIs=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation,
        _class_attributes_sidebar_styles__rspack_import_6.useStyles,
        _hooks_use_class_attributes_tree__rspack_import_5.useClassAttributesTree
    ];
});
_c = ClassAttributesSidebar;
var _c;
$RefreshReg$(_c, "ClassAttributesSidebar");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/components/draggable-tree-title.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  DraggableTreeTitle: () => (DraggableTreeTitle)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _drag_types__rspack_import_3 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/drag-types.ts");
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
 * Wraps tree node titles with drag capability for the class attributes sidebar.
 * Only nodes marked as field definitions are draggable.
 */ const DraggableTreeTitle = (param)=>{
    let { node, initialComponent } = param;
    if (node.isFieldDefinition !== true) {
        return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(react_jsx_dev_runtime__rspack_import_0.Fragment, {
            children: initialComponent
        }, void 0, false);
    }
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Draggable, {
        info: {
            type: _drag_types__rspack_import_3.DragType.CLASS_ATTRIBUTE,
            data: {
                key: String(node.key),
                attribute: node.attribute,
                title: String(node.title),
                dataType: String(node.dataType ?? 'text')
            },
            icon: node.iconProps,
            title: String(node.title)
        },
        children: initialComponent
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/components/draggable-tree-title.tsx",
        lineNumber: 34,
        columnNumber: 5
    }, undefined);
};
_c = DraggableTreeTitle;
var _c;
$RefreshReg$(_c, "DraggableTreeTitle");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/components/operator-grid-item.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  OperatorGridItem: () => (OperatorGridItem)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _drag_types__rspack_import_3 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/drag-types.ts");
/* import */ var lodash__rspack_import_4 = __webpack_require__("./node_modules/lodash/lodash.js");
/* import */ var lodash__rspack_import_4_default = /*#__PURE__*/__webpack_require__.n(lodash__rspack_import_4);
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



const OperatorGridItem = (param)=>{
    let { groupKey, subGroupKey, operator } = param;
    const dragKey = (0,lodash__rspack_import_4.isNil)(subGroupKey) ? `${groupKey}-${operator.id}` : `${groupKey}-${subGroupKey}-${operator.id}`;
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Draggable, {
        info: {
            type: _drag_types__rspack_import_3.DragType.OPERATOR,
            data: {
                key: dragKey,
                title: operator.localizedName,
                operatorId: operator.id
            },
            icon: operator.icon,
            title: operator.localizedName
        },
        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.GridButton, {
            icon: operator.icon,
            label: operator.localizedName
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/components/operator-grid-item.tsx",
            lineNumber: 49,
            columnNumber: 7
        }, undefined)
    }, dragKey, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/components/operator-grid-item.tsx",
        lineNumber: 36,
        columnNumber: 5
    }, undefined);
};
_c = OperatorGridItem;
var _c;
$RefreshReg$(_c, "OperatorGridItem");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/components/operators-sidebar.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  OperatorsSidebar: () => (OperatorsSidebar)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_3);
/* import */ var _modules_operators_hooks_use_operator__rspack_import_4 = __webpack_require__("./js/src/modules/operators/hooks/use-operator.tsx");
/* import */ var _operator_grid_item__rspack_import_5 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/components/operator-grid-item.tsx");
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




const OperatorsSidebar = (param)=>{
    let { operatorRegistryServiceId, gridContainerClassName, groupName, translatedGroupName } = param;
    _s();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation)();
    const operatorRegistry = (0,_pimcore_studio_ui_bundle_app__rspack_import_3.useInjection)(operatorRegistryServiceId);
    const { getLocalizedName, getGroup, getIcon } = (0,_modules_operators_hooks_use_operator__rspack_import_4.useOperator)();
    const groupData = (0,react__rspack_import_1.useMemo)(()=>{
        const operators = operatorRegistry.getDynamicTypes();
        const subGroups = new Map();
        operators.forEach((operator)=>{
            var _subGroups_get;
            if (operator.getGroup() !== groupName) return;
            const translatedSubGroup = getGroup(operator).subGroup;
            if (!subGroups.has(translatedSubGroup)) {
                subGroups.set(translatedSubGroup, []);
            }
            (_subGroups_get = subGroups.get(translatedSubGroup)) === null || _subGroups_get === void 0 ? void 0 : _subGroups_get.push({
                id: operator.id,
                icon: getIcon(operator, operatorRegistry),
                localizedName: getLocalizedName(operator)
            });
        });
        return subGroups;
    }, [
        operatorRegistry,
        groupName,
        getLocalizedName,
        getGroup,
        getIcon
    ]);
    const hasSubGroups = (0,react__rspack_import_1.useMemo)(()=>{
        return Array.from(groupData.keys()).some((key)=>key !== undefined);
    }, [
        groupData
    ]);
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(react_jsx_dev_runtime__rspack_import_0.Fragment, {
        children: [
            /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.SidebarTitle, {
                withBorder: true,
                children: translatedGroupName
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/components/operators-sidebar.tsx",
                lineNumber: 68,
                columnNumber: 7
            }, undefined),
            /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Box, {
                padding: hasSubGroups ? {
                    x: 'extra-small'
                } : undefined,
                children: Array.from(groupData.entries()).map((param)=>{
                    let [subGroupKey, operators] = param;
                    const sortedOperators = operators.sort((a, b)=>a.localizedName.localeCompare(b.localizedName));
                    if (subGroupKey === undefined) {
                        return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Box, {
                            className: gridContainerClassName,
                            padding: {
                                x: 'extra-small',
                                bottom: 'small'
                            },
                            children: sortedOperators.map((operator)=>/*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_operator_grid_item__rspack_import_5.OperatorGridItem, {
                                    groupKey: translatedGroupName,
                                    operator: operator
                                }, `${translatedGroupName}-${operator.id}`, false, {
                                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/components/operators-sidebar.tsx",
                                    lineNumber: 84,
                                    columnNumber: 19
                                }, undefined))
                        }, `${translatedGroupName}-direct`, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/components/operators-sidebar.tsx",
                            lineNumber: 78,
                            columnNumber: 15
                        }, undefined);
                    } else {
                        return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Panel, {
                            border: false,
                            collapsed: false,
                            contentPadding: "extra-small",
                            theme: "card-with-highlight",
                            title: t(subGroupKey),
                            children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Box, {
                                className: gridContainerClassName,
                                children: sortedOperators.map((operator)=>/*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_operator_grid_item__rspack_import_5.OperatorGridItem, {
                                        groupKey: translatedGroupName,
                                        operator: operator,
                                        subGroupKey: subGroupKey
                                    }, `${translatedGroupName}-${subGroupKey}-${operator.id}`, false, {
                                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/components/operators-sidebar.tsx",
                                        lineNumber: 104,
                                        columnNumber: 21
                                    }, undefined))
                            }, void 0, false, {
                                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/components/operators-sidebar.tsx",
                                lineNumber: 102,
                                columnNumber: 17
                            }, undefined)
                        }, `${translatedGroupName}-${subGroupKey}`, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/components/operators-sidebar.tsx",
                            lineNumber: 94,
                            columnNumber: 15
                        }, undefined);
                    }
                })
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/components/operators-sidebar.tsx",
                lineNumber: 72,
                columnNumber: 7
            }, undefined)
        ]
    }, void 0, true);
};
_s(OperatorsSidebar, "SEZSWd5kggC9EeQge4+VaQhlzrc=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation,
        _pimcore_studio_ui_bundle_app__rspack_import_3.useInjection,
        _modules_operators_hooks_use_operator__rspack_import_4.useOperator
    ];
});
_c = OperatorsSidebar;
var _c;
$RefreshReg$(_c, "OperatorsSidebar");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/definitions/system-column-definitions.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  SYSTEM_COLUMN_ICON: () => (SYSTEM_COLUMN_ICON),
  systemColumnDefinitions: () => (systemColumnDefinitions),
  systemColumnLookup: () => (systemColumnLookup)
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
 */ const SYSTEM_COLUMN_ICON = {
    value: 'system-columns',
    colorToken: 'colorCodingGreen1'
};
const systemColumnDefinitions = [
    {
        key: 'system-id',
        attribute: 'id',
        translationKey: 'data-hub.schema.system.id',
        iconProps: SYSTEM_COLUMN_ICON
    },
    {
        key: 'system-fullpath',
        attribute: 'fullpath',
        translationKey: 'data-hub.schema.system.fullpath',
        iconProps: SYSTEM_COLUMN_ICON
    },
    {
        key: 'system-key',
        attribute: 'key',
        translationKey: 'data-hub.schema.system.key',
        iconProps: SYSTEM_COLUMN_ICON
    },
    {
        key: 'system-published',
        attribute: 'published',
        translationKey: 'data-hub.schema.system.published',
        iconProps: SYSTEM_COLUMN_ICON
    },
    {
        key: 'system-creationDate',
        attribute: 'creationDate',
        translationKey: 'data-hub.schema.system.creationDate',
        iconProps: SYSTEM_COLUMN_ICON
    },
    {
        key: 'system-modificationDate',
        attribute: 'modificationDate',
        translationKey: 'data-hub.schema.system.modificationDate',
        iconProps: SYSTEM_COLUMN_ICON
    },
    {
        key: 'system-filename',
        attribute: 'filename',
        translationKey: 'data-hub.schema.system.filename',
        iconProps: SYSTEM_COLUMN_ICON
    },
    {
        key: 'system-classname',
        attribute: 'classname',
        translationKey: 'data-hub.schema.system.classname',
        iconProps: SYSTEM_COLUMN_ICON
    },
    {
        key: 'system-index',
        attribute: 'index',
        translationKey: 'data-hub.schema.system.index',
        iconProps: SYSTEM_COLUMN_ICON
    }
];
// Create a lookup map for quick access by attribute name
const systemColumnLookup = new Map(systemColumnDefinitions.map((def)=>[
        def.attribute,
        def
    ]));

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/drag-types.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  DragType: () => (DragType),
  DropPosition: () => (DropPosition)
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
 */ var DragType = /*#__PURE__*/ function(DragType) {
    DragType["CLASS_ATTRIBUTE"] = "class-attribute";
    DragType["OPERATOR"] = "operator";
    DragType["TREE_ITEM"] = "tree-item";
    return DragType;
}({});
var DropPosition = /*#__PURE__*/ function(DropPosition) {
    DropPosition["BEFORE"] = "before";
    DropPosition["AFTER"] = "after";
    DropPosition["INTO"] = "into";
    return DropPosition;
}({});

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/hooks/class-attributes-tree-helpers.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  collectAllKeys: () => (collectAllKeys),
  filterTreeNodesRecursive: () => (filterTreeNodesRecursive),
  removeObjectBricksNodes: () => (removeObjectBricksNodes),
  scanForObjectBricksFields: () => (scanForObjectBricksFields)
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
const filterTreeNodesRecursive = (nodes, searchValue)=>{
    if (!Array.isArray(nodes)) {
        return [];
    }
    return nodes.reduce((acc, node)=>{
        var _node_title, _node_attribute;
        const isFieldDefinition = node.isFieldDefinition === true;
        const matchesSearch = isFieldDefinition && ((((_node_title = node.title) === null || _node_title === void 0 ? void 0 : _node_title.toString().toLowerCase().includes(searchValue.toLowerCase())) ?? false) || (((_node_attribute = node.attribute) === null || _node_attribute === void 0 ? void 0 : _node_attribute.toLowerCase().includes(searchValue.toLowerCase())) ?? false));
        const filteredChildren = !(0,lodash__rspack_import_0.isNil)(node.children) && Array.isArray(node.children) ? filterTreeNodesRecursive(node.children, searchValue) : [];
        if ((matchesSearch ?? false) || filteredChildren.length > 0) {
            acc.push({
                ...node,
                children: filteredChildren.length > 0 ? filteredChildren : node.children
            });
        }
        return acc;
    }, []);
};
const collectAllKeys = (nodes)=>{
    return (0,lodash__rspack_import_0.flatMapDeep)(nodes, (node)=>[
            ...(0,lodash__rspack_import_0.isNil)(node.key) ? [] : [
                String(node.key)
            ],
            ...!(0,lodash__rspack_import_0.isNil)(node.children) && Array.isArray(node.children) ? collectAllKeys(node.children) : []
        ]);
};
/**
 * Recursively walks a raw layout's children to collect all objectbricks fields
 * that have a non-empty allowedTypes list.
 */ const scanForObjectBricksFields = (layout)=>{
    const results = [];
    const walk = (children)=>{
        children.forEach((child)=>{
            const node = child;
            if (node.fieldtype === 'objectbricks') {
                const allowedTypes = node.allowedTypes ?? [];
                if (allowedTypes.length > 0) {
                    results.push({
                        name: node.name,
                        title: node.title ?? null,
                        allowedTypes
                    });
                }
            }
            if (Array.isArray(node.children)) {
                walk(node.children);
            }
        });
    };
    if (Array.isArray(layout.children)) {
        walk(layout.children);
    }
    return results;
};
/**
 * Recursively removes objectbricks field nodes from the tree.
 * They are replaced by brick group nodes appended at the root level.
 */ const removeObjectBricksNodes = (nodes)=>{
    return nodes.filter((node)=>!(node.dataType === 'objectbricks' && node.isFieldDefinition === true)).map((node)=>({
            ...node,
            children: !(0,lodash__rspack_import_0.isNil)(node.children) && Array.isArray(node.children) ? removeObjectBricksNodes(node.children) : node.children
        }));
};

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/hooks/use-class-attributes-tree.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  useClassAttributesTree: () => (useClassAttributesTree)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_api_class_definition__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/api/class-definition");
/* import */ var _pimcore_studio_ui_bundle_api_class_definition__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_api_class_definition__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_modules_field_definitions__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/modules/field-definitions");
/* import */ var _pimcore_studio_ui_bundle_modules_field_definitions__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_modules_field_definitions__rspack_import_3);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_4 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_4_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_4);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_5 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_5_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_5);
/* import */ var lodash__rspack_import_6 = __webpack_require__("./node_modules/lodash/lodash.js");
/* import */ var lodash__rspack_import_6_default = /*#__PURE__*/__webpack_require__.n(lodash__rspack_import_6);
/* import */ var _definitions_system_column_definitions__rspack_import_7 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/definitions/system-column-definitions.ts");
/* import */ var _use_objectbrick_layouts__rspack_import_8 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/hooks/use-objectbrick-layouts.tsx");
/* import */ var _class_attributes_tree_helpers__rspack_import_9 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/hooks/class-attributes-tree-helpers.ts");
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









const useClassAttributesTree = (param)=>{
    let { classId, enabled, searchValue = '' } = param;
    _s();
    const fieldDefinitionRegistry = (0,_pimcore_studio_ui_bundle_app__rspack_import_4.useInjection)(_pimcore_studio_ui_bundle_app__rspack_import_4.serviceIds["DynamicTypes/FieldDefinitionRegistry"]);
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_4.useTranslation)();
    const { data: classLayout, isLoading, isFetching } = (0,_pimcore_studio_ui_bundle_api_class_definition__rspack_import_2.useClassDefinitionGetLayoutByIdQuery)({
        id: classId
    }, {
        skip: !enabled,
        refetchOnMountOrArgChange: true
    });
    const objectBricksFields = (0,react__rspack_import_1.useMemo)(()=>{
        if ((0,lodash__rspack_import_6.isNil)(classLayout)) return [];
        return (0,_class_attributes_tree_helpers__rspack_import_9.scanForObjectBricksFields)(classLayout);
    }, [
        classLayout
    ]);
    const allBrickKeys = (0,react__rspack_import_1.useMemo)(()=>{
        return Array.from(new Set((0,lodash__rspack_import_6.flatMap)(objectBricksFields, (f)=>f.allowedTypes)));
    }, [
        objectBricksFields
    ]);
    const { layouts: brickLayouts, isLoading: brickLayoutsLoading } = (0,_use_objectbrick_layouts__rspack_import_8.useObjectBrickLayouts)(allBrickKeys);
    const classAttributesTree = (0,react__rspack_import_1.useMemo)(()=>{
        const buildItemCallback = (brickKey)=>(param)=>{
                let { fieldDefinition, initialTreeItem } = param;
                const dynType = fieldDefinitionRegistry.getDynamicType(fieldDefinition.fieldtype, false);
                const isFieldDefinition = dynType instanceof _pimcore_studio_ui_bundle_modules_field_definitions__rspack_import_3.DynamicTypeFieldDefinitionDataAbstract;
                const { icon: _icon, ...restTreeItem } = initialTreeItem;
                const label = fieldDefinition.title ?? fieldDefinition.name;
                if (!(0,lodash__rspack_import_6.isNil)(brickKey) && isFieldDefinition) {
                    const attributeKey = `${brickKey}~${fieldDefinition.name}`;
                    const title = `${label} (${brickKey}.${fieldDefinition.name})`;
                    return {
                        ...restTreeItem,
                        title,
                        className: 'ant-tree-node--has-drag-and-drop',
                        icon: initialTreeItem.icon,
                        dataType: fieldDefinition.fieldtype,
                        attribute: attributeKey,
                        iconProps: (dynType === null || dynType === void 0 ? void 0 : dynType.getIcon()) ?? {
                            value: 'info'
                        },
                        isFieldDefinition: true
                    };
                }
                const title = isFieldDefinition ? `${label} (${fieldDefinition.name})` : initialTreeItem.title;
                return {
                    ...restTreeItem,
                    title,
                    className: isFieldDefinition ? 'ant-tree-node--has-drag-and-drop' : undefined,
                    icon: initialTreeItem.icon,
                    dataType: fieldDefinition.fieldtype,
                    attribute: fieldDefinition.name,
                    iconProps: (dynType === null || dynType === void 0 ? void 0 : dynType.getIcon()) ?? {
                        value: 'info'
                    },
                    isFieldDefinition
                };
            };
        const buildDataObjectColumns = ()=>{
            if ((0,lodash__rspack_import_6.isNil)(classLayout)) return [];
            const reduced = (0,_pimcore_studio_ui_bundle_modules_field_definitions__rspack_import_3.reduce)({
                layout: classLayout
            });
            if ((reduced === null || reduced === void 0 ? void 0 : reduced.structure) === undefined) return [];
            const { structure, fieldDefinitions } = reduced;
            const tree = (0,_pimcore_studio_ui_bundle_modules_field_definitions__rspack_import_3.buildTree)({
                structure,
                fieldDefinitions,
                itemCallback: buildItemCallback()
            });
            const children = (tree === null || tree === void 0 ? void 0 : tree.children) ?? [];
            return (0,_class_attributes_tree_helpers__rspack_import_9.removeObjectBricksNodes)(children);
        };
        const buildBrickGroupNodes = ()=>{
            if ((0,lodash__rspack_import_6.isNil)(classLayout) || brickLayouts.size === 0) return [];
            return (0,lodash__rspack_import_6.flatMap)(allBrickKeys, (brickKey)=>{
                const brickLayout = brickLayouts.get(brickKey);
                if ((0,lodash__rspack_import_6.isNil)(brickLayout)) return [];
                // Cast: ConfigLayoutDefinition is structurally compatible with Layout for reduce/buildTree
                const reduced = (0,_pimcore_studio_ui_bundle_modules_field_definitions__rspack_import_3.reduce)({
                    layout: brickLayout
                });
                if ((0,lodash__rspack_import_6.isNil)(reduced === null || reduced === void 0 ? void 0 : reduced.structure)) return [];
                const { structure, fieldDefinitions } = reduced;
                const brickTree = (0,_pimcore_studio_ui_bundle_modules_field_definitions__rspack_import_3.buildTree)({
                    structure,
                    fieldDefinitions,
                    itemCallback: buildItemCallback(brickKey)
                });
                const brickGroupNode = {
                    key: `brick-group-${brickKey}`,
                    title: `${brickKey} Columns`,
                    isLeaf: false,
                    icon: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_5.Icon, {
                        value: "object-bricks"
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/hooks/use-class-attributes-tree.tsx",
                        lineNumber: 149,
                        columnNumber: 17
                    }, undefined),
                    iconProps: {
                        value: 'object-bricks'
                    },
                    // Use brickTree.children directly to skip the redundant root Panel node
                    children: (brickTree === null || brickTree === void 0 ? void 0 : brickTree.children) ?? []
                };
                return [
                    brickGroupNode
                ];
            });
        };
        const buildSystemColumns = ()=>({
                key: 'system-columns',
                title: t('data-hub.schema.system-columns'),
                isLeaf: false,
                icon: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_5.Icon, {
                    ..._definitions_system_column_definitions__rspack_import_7.SYSTEM_COLUMN_ICON
                }, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/hooks/use-class-attributes-tree.tsx",
                    lineNumber: 163,
                    columnNumber: 13
                }, undefined),
                iconProps: _definitions_system_column_definitions__rspack_import_7.SYSTEM_COLUMN_ICON,
                children: _definitions_system_column_definitions__rspack_import_7.systemColumnDefinitions.map((param)=>{
                    let { key, attribute, translationKey, iconProps } = param;
                    return {
                        key,
                        title: t(translationKey),
                        isLeaf: true,
                        dataType: 'system',
                        icon: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_5.Icon, {
                            ...iconProps
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/hooks/use-class-attributes-tree.tsx",
                            lineNumber: 170,
                            columnNumber: 15
                        }, undefined),
                        iconProps,
                        className: 'ant-tree-node--has-drag-and-drop',
                        attribute,
                        isFieldDefinition: true
                    };
                })
            });
        try {
            const dataObjectColumnsData = buildDataObjectColumns();
            const dataObjectColumns = {
                key: 'object-columns',
                title: t('data-hub.schema.data-object-columns'),
                isLeaf: false,
                icon: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_5.Icon, {
                    value: "data-object"
                }, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/hooks/use-class-attributes-tree.tsx",
                    lineNumber: 184,
                    columnNumber: 15
                }, undefined),
                iconProps: {
                    value: 'data-object'
                },
                children: dataObjectColumnsData
            };
            return [
                dataObjectColumns,
                buildSystemColumns(),
                ...buildBrickGroupNodes()
            ];
        } catch (error) {
            console.error('Error building class attributes tree:', error);
            return [];
        }
    }, [
        classLayout,
        brickLayouts,
        allBrickKeys,
        fieldDefinitionRegistry,
        t
    ]);
    const filteredTree = (0,react__rspack_import_1.useMemo)(()=>{
        if (searchValue === '') {
            return classAttributesTree;
        }
        return (0,_class_attributes_tree_helpers__rspack_import_9.filterTreeNodesRecursive)(classAttributesTree, searchValue);
    }, [
        searchValue,
        classAttributesTree
    ]);
    const expandedKeys = (0,react__rspack_import_1.useMemo)(()=>{
        if (searchValue === '') {
            return (0,_class_attributes_tree_helpers__rspack_import_9.collectAllKeys)(classAttributesTree);
        }
        return (0,_class_attributes_tree_helpers__rspack_import_9.collectAllKeys)(filteredTree);
    }, [
        searchValue,
        classAttributesTree,
        filteredTree
    ]);
    const getFieldDefinitions = (0,react__rspack_import_1.useCallback)(()=>{
        const collectFieldDefinitions = (nodes)=>(0,lodash__rspack_import_6.flatMap)(nodes, (node)=>{
                if (node.isFieldDefinition === true) return [
                    node
                ];
                if (!(0,lodash__rspack_import_6.isNil)(node.children)) return collectFieldDefinitions(node.children);
                return [];
            });
        const objectColumnsNode = classAttributesTree.find((node)=>node.key === 'object-columns');
        const objectColumnDefs = !(0,lodash__rspack_import_6.isNil)(objectColumnsNode) && !(0,lodash__rspack_import_6.isNil)(objectColumnsNode.children) ? collectFieldDefinitions(objectColumnsNode.children) : [];
        const brickGroupNodes = classAttributesTree.filter((node)=>typeof node.key === 'string' && node.key.startsWith('brick-group-'));
        const brickColumnDefs = collectFieldDefinitions(brickGroupNodes);
        return [
            ...objectColumnDefs,
            ...brickColumnDefs
        ];
    }, [
        classAttributesTree
    ]);
    // Consider still loading if the class layout resolved but we know there are brick keys
    // to fetch and the brick layouts haven't all been resolved yet. This prevents the
    // intermediate flash between "class layout done" and "brick fetch effect fires".
    const pendingBrickFetch = !isLoading && !isFetching && allBrickKeys.length > 0 && brickLayouts.size === 0;
    return {
        classAttributesTree,
        filteredTree,
        expandedKeys,
        getFieldDefinitions,
        isLoading: isLoading || isFetching || brickLayoutsLoading || pendingBrickFetch
    };
};
_s(useClassAttributesTree, "PMEFpF2Lhae+S91aZZj2Neyq8mY=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_4.useInjection,
        _pimcore_studio_ui_bundle_app__rspack_import_4.useTranslation,
        _pimcore_studio_ui_bundle_api_class_definition__rspack_import_2.useClassDefinitionGetLayoutByIdQuery,
        _use_objectbrick_layouts__rspack_import_8.useObjectBrickLayouts
    ];
});

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/hooks/use-objectbrick-layouts.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  useObjectBrickLayouts: () => (useObjectBrickLayouts)
});
/* import */ var react__rspack_import_0 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_0_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_0);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_1 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_api_class_definition__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/api/class-definition");
/* import */ var _pimcore_studio_ui_bundle_api_class_definition__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_api_class_definition__rspack_import_2);
/* import */ var lodash__rspack_import_3 = __webpack_require__("./node_modules/lodash/lodash.js");
/* import */ var lodash__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(lodash__rspack_import_3);
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



const useObjectBrickLayouts = (brickKeys)=>{
    _s();
    const dispatch = (0,_pimcore_studio_ui_bundle_app__rspack_import_1.useAppDispatch)();
    const [layouts, setLayouts] = (0,react__rspack_import_0.useState)(new Map());
    // Start as true when keys are already known on first render — prevents a flash of the
    // incomplete tree between "class layout resolved" and "brick fetch effect fires".
    const [isLoading, setIsLoading] = (0,react__rspack_import_0.useState)(brickKeys.length > 0);
    // Stable serialization used as effect dependency — avoids re-firing on same keys with new array reference
    const sortedKeysString = [
        ...brickKeys
    ].sort((a, b)=>a.localeCompare(b)).join(',');
    (0,react__rspack_import_0.useEffect)(()=>{
        if (brickKeys.length === 0) {
            setLayouts(new Map());
            setIsLoading(false);
            return;
        }
        setIsLoading(true);
        let cancelled = false;
        const promises = brickKeys.map((key)=>dispatch(_pimcore_studio_ui_bundle_api_class_definition__rspack_import_2.api.endpoints.classObjectBrickGetLayoutByKey.initiate({
                key
            })));
        void Promise.all(promises).then((responses)=>{
            if (cancelled) return;
            const newLayouts = new Map();
            brickKeys.forEach((key, index)=>{
                var _responses_index;
                const data = (_responses_index = responses[index]) === null || _responses_index === void 0 ? void 0 : _responses_index.data;
                if (!(0,lodash__rspack_import_3.isNil)(data)) {
                    newLayouts.set(key, data);
                }
            });
            setLayouts(newLayouts);
            setIsLoading(false);
        });
        return ()=>{
            cancelled = true;
            promises.forEach((p)=>{
                p.unsubscribe();
            });
        };
    }, [
        dispatch,
        sortedKeysString
    ]);
    return {
        layouts,
        isLoading
    };
};
_s(useObjectBrickLayouts, "a+hCAvZUMwhVfk5LqMBvl2GBAFk=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_1.useAppDispatch
    ];
});

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/hooks/use-sidebar-entries.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  useSidebarEntries: () => (useSidebarEntries)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_3);
/* import */ var _modules_operators_hooks_use_operator__rspack_import_4 = __webpack_require__("./js/src/modules/operators/hooks/use-operator.tsx");
/* import */ var _modules_operators_hooks_use_operator_groups__rspack_import_5 = __webpack_require__("./js/src/modules/operators/hooks/use-operator-groups.tsx");
/* import */ var _components_class_attributes_sidebar__rspack_import_6 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/components/class-attributes-sidebar.tsx");
/* import */ var _components_operators_sidebar__rspack_import_7 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/components/operators-sidebar.tsx");
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






const useSidebarEntries = (param)=>{
    let { classId, enabled, operatorRegistryServiceId, gridContainerClassName } = param;
    _s();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation)();
    const operatorRegistry = (0,_pimcore_studio_ui_bundle_app__rspack_import_3.useInjection)(operatorRegistryServiceId);
    const { getGroup } = (0,_modules_operators_hooks_use_operator__rspack_import_4.useOperator)();
    const { getGroupIcon } = (0,_modules_operators_hooks_use_operator_groups__rspack_import_5.useOperatorGroups)(operatorRegistry);
    return (0,react__rspack_import_1.useMemo)(()=>{
        const entries = [];
        // Add class attributes entry
        entries.push({
            key: 'class-attributes',
            icon: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Icon, {
                value: "data-object"
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/hooks/use-sidebar-entries.tsx",
                lineNumber: 51,
                columnNumber: 13
            }, undefined),
            tooltip: t('data-hub.schema.class-attributes'),
            component: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_components_class_attributes_sidebar__rspack_import_6.ClassAttributesSidebar, {
                classId: classId,
                enabled: enabled
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/hooks/use-sidebar-entries.tsx",
                lineNumber: 54,
                columnNumber: 9
            }, undefined)
        });
        // Organize operators by group
        const operators = operatorRegistry.getDynamicTypes();
        const groups = new Map();
        operators.forEach((operator)=>{
            const groupName = operator.getGroup();
            const { group: translatedGroup } = getGroup(operator);
            const groupConfig = operatorRegistry.getGroupConfig(groupName);
            const priority = (groupConfig === null || groupConfig === void 0 ? void 0 : groupConfig.priority) ?? 999;
            if (!groups.has(translatedGroup)) {
                groups.set(translatedGroup, {
                    groupName,
                    priority
                });
            }
        });
        // Add operator group entries
        Array.from(groups.entries()).sort((param, param1)=>{
            let [, a] = param, [, b] = param1;
            return a.priority - b.priority;
        }).forEach((param)=>{
            let [groupKey, groupData] = param;
            const groupIcon = getGroupIcon(groupData.groupName);
            entries.push({
                key: groupKey,
                icon: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Icon, {
                    ...groupIcon
                }, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/hooks/use-sidebar-entries.tsx",
                    lineNumber: 87,
                    columnNumber: 17
                }, undefined),
                tooltip: t(groupKey),
                component: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Content, {
                    children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_components_operators_sidebar__rspack_import_7.OperatorsSidebar, {
                        gridContainerClassName: gridContainerClassName,
                        groupName: groupData.groupName,
                        operatorRegistryServiceId: operatorRegistryServiceId,
                        translatedGroupName: groupKey
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/hooks/use-sidebar-entries.tsx",
                        lineNumber: 91,
                        columnNumber: 15
                    }, undefined)
                }, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/hooks/use-sidebar-entries.tsx",
                    lineNumber: 90,
                    columnNumber: 13
                }, undefined)
            });
        });
        return entries;
    }, [
        classId,
        enabled,
        operatorRegistryServiceId,
        gridContainerClassName
    ]);
};
_s(useSidebarEntries, "W30Zu/OaPFLR+m6I+nEoPmS1SXM=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation,
        _pimcore_studio_ui_bundle_app__rspack_import_3.useInjection,
        _modules_operators_hooks_use_operator__rspack_import_4.useOperator,
        _modules_operators_hooks_use_operator_groups__rspack_import_5.useOperatorGroups
    ];
});

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/schema-fields-modal.styles.ts"(module, __webpack_exports__, __webpack_require__) {
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
        contentLayout: css`
      height: 60vh;
      border-bottom: 1px solid ${token.colorBorderSecondary};
      
      .pimcore-content-layout__content {
        padding-right: ${token.padding}px;
      }
    `,
        gridContainer: css`
      display: grid;
      grid-template-columns: 1fr 1fr 1fr;
      gap: ${token.marginXS}px;
      width: 100%;
      align-items: stretch;
    `
    };
});

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/schema-fields-modal.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  SchemaFieldsModal: () => (SchemaFieldsModal)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_3);
/* import */ var _pimcore_studio_ui_bundle_modules_data_object__rspack_import_4 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/modules/data-object");
/* import */ var _pimcore_studio_ui_bundle_modules_data_object__rspack_import_4_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_modules_data_object__rspack_import_4);
/* import */ var _available_fields_tree__rspack_import_5 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/available-fields-tree/index.ts");
/* import */ var lodash__rspack_import_6 = __webpack_require__("./node_modules/lodash/lodash.js");
/* import */ var lodash__rspack_import_6_default = /*#__PURE__*/__webpack_require__.n(lodash__rspack_import_6);
/* import */ var _schema_fields_modal_styles__rspack_import_7 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/schema-fields-modal.styles.ts");
/* import */ var _hooks_use_sidebar_entries__rspack_import_8 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/hooks/use-sidebar-entries.tsx");
/* import */ var _components_add_all_definitions_button__rspack_import_9 = __webpack_require__("./js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/components/add-all-definitions-button.tsx");
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








const SchemaFieldsModal = (param)=>{
    let { open, className, operatorRegistryServiceId, type = 'query', disabled = false, onCancel, onApply } = param;
    var _sidebarEntries_;
    _s();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation)();
    const { styles } = (0,_schema_fields_modal_styles__rspack_import_7.useStyles)();
    const form = _pimcore_studio_ui_bundle_components__rspack_import_2.Form.useFormInstance();
    const { getByName } = (0,_pimcore_studio_ui_bundle_modules_data_object__rspack_import_4.useClassDefinitions)();
    const classDefinition = getByName(className);
    const classId = (classDefinition === null || classDefinition === void 0 ? void 0 : classDefinition.id) ?? '';
    const enabled = open && !(0,lodash__rspack_import_6.isNil)(classDefinition);
    const sidebarEntries = (0,_hooks_use_sidebar_entries__rspack_import_8.useSidebarEntries)({
        classId,
        enabled,
        operatorRegistryServiceId,
        gridContainerClassName: styles.gridContainer
    });
    const [localEntityConfig, setLocalEntityConfig] = (0,react__rspack_import_1.useState)(undefined);
    (0,react__rspack_import_1.useEffect)(()=>{
        if (open) {
            const entities = form.getFieldValue([
                'schema',
                type
            ]) ?? [];
            const entity = entities.find((e)=>e.entity === className);
            setLocalEntityConfig((0,lodash__rspack_import_6.isNil)(entity) ? undefined : (0,lodash__rspack_import_6.cloneDeep)(entity));
        }
    }, [
        open,
        form,
        className,
        type
    ]);
    const handleApply = ()=>{
        const entities = form.getFieldValue([
            'schema',
            type
        ]) ?? [];
        const entityIndex = entities.findIndex((e)=>e.entity === className);
        if (entityIndex !== -1 && !(0,lodash__rspack_import_6.isNil)(localEntityConfig)) {
            const updatedEntities = [
                ...entities
            ];
            updatedEntities[entityIndex] = localEntityConfig;
            form.setFieldValue([
                'schema',
                type
            ], updatedEntities, {
                triggerChange: true
            });
        }
        onApply();
    };
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Modal, {
        footer: disabled ? null : /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Flex, {
            gap: "small",
            justify: "space-between",
            children: [
                /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_components_add_all_definitions_button__rspack_import_9.AddAllDefinitionsButton, {
                    classId: classId,
                    enabled: enabled,
                    entityConfig: localEntityConfig,
                    onEntityConfigChange: setLocalEntityConfig
                }, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/schema-fields-modal.tsx",
                    lineNumber: 90,
                    columnNumber: 13
                }, undefined),
                /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Button, {
                    onClick: handleApply,
                    type: "primary",
                    children: t('button.apply')
                }, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/schema-fields-modal.tsx",
                    lineNumber: 96,
                    columnNumber: 13
                }, undefined)
            ]
        }, void 0, true, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/schema-fields-modal.tsx",
            lineNumber: 86,
            columnNumber: 11
        }, undefined),
        onCancel: onCancel,
        open: open,
        size: "XL",
        title: t(`data-hub.schema.${type}-modal-title`, {
            entity: className
        }),
        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.ContentLayout, {
            className: styles.contentLayout,
            renderSidebar: disabled ? undefined : /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.SidebarProvider, {
                initialActiveTab: (_sidebarEntries_ = sidebarEntries[0]) === null || _sidebarEntries_ === void 0 ? void 0 : _sidebarEntries_.key,
                children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Sidebar, {
                    entries: sidebarEntries,
                    sizing: "large"
                }, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/schema-fields-modal.tsx",
                    lineNumber: 116,
                    columnNumber: 15
                }, undefined)
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/schema-fields-modal.tsx",
                lineNumber: 115,
                columnNumber: 13
            }, undefined),
            children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Content, {
                padded: true,
                padding: {
                    right: 'medium'
                },
                children: [
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Title, {
                        level: 3,
                        children: t('data-hub.schema.available-fields')
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/schema-fields-modal.tsx",
                        lineNumber: 127,
                        columnNumber: 11
                    }, undefined),
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_available_fields_tree__rspack_import_5.AvailableFieldsTree, {
                        disabled: disabled,
                        entityConfig: localEntityConfig,
                        onEntityConfigChange: setLocalEntityConfig,
                        operatorRegistryServiceId: operatorRegistryServiceId
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/schema-fields-modal.tsx",
                        lineNumber: 128,
                        columnNumber: 11
                    }, undefined)
                ]
            }, void 0, true, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/schema-fields-modal.tsx",
                lineNumber: 123,
                columnNumber: 9
            }, undefined)
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/schema-fields-modal.tsx",
            lineNumber: 110,
            columnNumber: 7
        }, undefined)
    }, `${className}-${type}`, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/schema-fields-modal.tsx",
        lineNumber: 82,
        columnNumber: 5
    }, undefined);
};
_s(SchemaFieldsModal, "aU+5cEBnqGiTLCTo6Xa6ZZl60Zs=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation,
        _schema_fields_modal_styles__rspack_import_7.useStyles,
        _pimcore_studio_ui_bundle_components__rspack_import_2.Form.useFormInstance,
        _pimcore_studio_ui_bundle_modules_data_object__rspack_import_4.useClassDefinitions,
        _hooks_use_sidebar_entries__rspack_import_8.useSidebarEntries
    ];
});
_c = SchemaFieldsModal;
var _c;
$RefreshReg$(_c, "SchemaFieldsModal");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/components/tabs/security-definition-tab.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  SecurityDefinitionTab: () => (SecurityDefinitionTab)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_3);
/* import */ var _security_definition_tab_workspace_grid__rspack_import_4 = __webpack_require__("./js/src/modules/graphql/components/tabs/security-definition-tab/workspace-grid.tsx");
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



const SecurityDefinitionTab = (param)=>{
    let { isWriteable = true } = param;
    _s();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation)();
    const form = _pimcore_studio_ui_bundle_components__rspack_import_2.Form.useFormInstance();
    const generateApiKey = ()=>{
        const currentValue = form.getFieldValue([
            'security',
            'apikey'
        ]);
        const currentValueStr = String(currentValue ?? '');
        const newKey = generateRandomKey();
        const newValue = currentValueStr.length > 0 ? `${currentValueStr}\n${newKey}` : newKey;
        form.setFieldsValue({
            security: {
                apikey: newValue
            }
        }, {
            triggerChange: true
        });
    };
    const generateRandomKey = ()=>{
        return Array.from(crypto.getRandomValues(new Uint8Array(16))).map((b)=>b.toString(16).padStart(2, '0')).join('');
    };
    const methodOptions = [
        {
            value: 'datahub_apikey',
            label: t('data-hub.security.method.apikey')
        }
    ];
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.FormKit.Panel, {
        contentPadding: "extra-small",
        children: [
            /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.FormKit.Panel, {
                contentPadding: "extra-small",
                title: t('data-hub.security.authentication'),
                children: [
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                        initialValue: "datahub_apikey",
                        label: t('data-hub.security.method'),
                        name: [
                            'security',
                            'method'
                        ],
                        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Select, {
                            options: methodOptions
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/security-definition-tab.tsx",
                            lineNumber: 61,
                            columnNumber: 11
                        }, undefined)
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/security-definition-tab.tsx",
                        lineNumber: 56,
                        columnNumber: 9
                    }, undefined),
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                        label: t('data-hub.security.apikey'),
                        name: [
                            'security',
                            'apikey'
                        ],
                        tooltip: t('data-hub.security.apikey-description'),
                        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.TextArea, {
                            autoSize: {
                                minRows: 4,
                                maxRows: 10
                            }
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/security-definition-tab.tsx",
                            lineNumber: 71,
                            columnNumber: 11
                        }, undefined)
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/security-definition-tab.tsx",
                        lineNumber: 66,
                        columnNumber: 9
                    }, undefined),
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Box, {
                        margin: {
                            bottom: 'small'
                        },
                        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Button, {
                            onClick: generateApiKey,
                            type: "default",
                            children: t('data-hub.security.generate-apikey')
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/security-definition-tab.tsx",
                            lineNumber: 77,
                            columnNumber: 11
                        }, undefined)
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/security-definition-tab.tsx",
                        lineNumber: 76,
                        columnNumber: 9
                    }, undefined),
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                        name: [
                            'security',
                            'skipPermissionCheck'
                        ],
                        valuePropName: "checked",
                        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Switch, {
                            labelRight: t('data-hub.security.skip-permission-check')
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/security-definition-tab.tsx",
                            lineNumber: 89,
                            columnNumber: 11
                        }, undefined)
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/security-definition-tab.tsx",
                        lineNumber: 85,
                        columnNumber: 9
                    }, undefined),
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                        name: [
                            'security',
                            'disableIntrospection'
                        ],
                        valuePropName: "checked",
                        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Switch, {
                            labelRight: t('data-hub.security.disable-introspection'),
                            tooltip: t('data-hub.security.introspection-description')
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/security-definition-tab.tsx",
                            lineNumber: 96,
                            columnNumber: 11
                        }, undefined)
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/security-definition-tab.tsx",
                        lineNumber: 92,
                        columnNumber: 9
                    }, undefined)
                ]
            }, void 0, true, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/security-definition-tab.tsx",
                lineNumber: 52,
                columnNumber: 7
            }, undefined),
            /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.FormKit.Panel, {
                contentPadding: "extra-small",
                title: t('data-hub.workspaces.title'),
                children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Flex, {
                    gap: "small",
                    vertical: true,
                    children: [
                        /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                            name: [
                                'workspaces',
                                'documents'
                            ],
                            noStyle: true,
                            children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_security_definition_tab_workspace_grid__rspack_import_4.WorkspaceGrid, {
                                isWriteable: isWriteable,
                                type: "documents"
                            }, void 0, false, {
                                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/security-definition-tab.tsx",
                                lineNumber: 115,
                                columnNumber: 13
                            }, undefined)
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/security-definition-tab.tsx",
                            lineNumber: 111,
                            columnNumber: 11
                        }, undefined),
                        /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                            name: [
                                'workspaces',
                                'assets'
                            ],
                            noStyle: true,
                            children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_security_definition_tab_workspace_grid__rspack_import_4.WorkspaceGrid, {
                                isWriteable: isWriteable,
                                type: "assets"
                            }, void 0, false, {
                                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/security-definition-tab.tsx",
                                lineNumber: 125,
                                columnNumber: 13
                            }, undefined)
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/security-definition-tab.tsx",
                            lineNumber: 121,
                            columnNumber: 11
                        }, undefined),
                        /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                            name: [
                                'workspaces',
                                'objects'
                            ],
                            noStyle: true,
                            children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_security_definition_tab_workspace_grid__rspack_import_4.WorkspaceGrid, {
                                isWriteable: isWriteable,
                                type: "objects"
                            }, void 0, false, {
                                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/security-definition-tab.tsx",
                                lineNumber: 135,
                                columnNumber: 13
                            }, undefined)
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/security-definition-tab.tsx",
                            lineNumber: 131,
                            columnNumber: 11
                        }, undefined)
                    ]
                }, void 0, true, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/security-definition-tab.tsx",
                    lineNumber: 107,
                    columnNumber: 9
                }, undefined)
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/security-definition-tab.tsx",
                lineNumber: 103,
                columnNumber: 7
            }, undefined)
        ]
    }, void 0, true, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/security-definition-tab.tsx",
        lineNumber: 51,
        columnNumber: 5
    }, undefined);
};
_s(SecurityDefinitionTab, "wfxjnKr7LGpJ5V4dKGCG0URqBpg=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation,
        _pimcore_studio_ui_bundle_components__rspack_import_2.Form.useFormInstance
    ];
});
_c = SecurityDefinitionTab;
var _c;
$RefreshReg$(_c, "SecurityDefinitionTab");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/components/tabs/security-definition-tab/workspace-accordion.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  WorkspaceAccordion: () => (WorkspaceAccordion)
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



const WorkspaceAccordion = (param)=>{
    let { type, value = [], onChange } = param;
    _s();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation)();
    const handleAdd = ()=>{
        if (!(0,lodash__rspack_import_4.isNil)(onChange)) {
            onChange([
                ...value,
                {
                    path: '',
                    create: false,
                    read: true,
                    update: false,
                    delete: false
                }
            ]);
        }
    };
    const accordionItem = (0,react__rspack_import_1.useMemo)(()=>({
            key: type,
            id: type,
            title: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(react_jsx_dev_runtime__rspack_import_0.Fragment, {
                children: t(`data-hub.workspaces.${type}`)
            }, void 0, false),
            info: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.IconTextButton, {
                icon: {
                    value: 'add-find'
                },
                onClick: (e)=>{
                    e.stopPropagation();
                    handleAdd();
                },
                children: t('add')
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/security-definition-tab/workspace-accordion.tsx",
                lineNumber: 47,
                columnNumber: 7
            }, undefined),
            children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.OperationalGrid.Grid, {}, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/security-definition-tab/workspace-accordion.tsx",
                lineNumber: 58,
                columnNumber: 7
            }, undefined)
        }), [
        type,
        value,
        handleAdd,
        t
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
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/security-definition-tab/workspace-accordion.tsx",
        lineNumber: 63,
        columnNumber: 5
    }, undefined);
};
_s(WorkspaceAccordion, "CccNeKN0du6YulWrVwXID5UDC2U=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation
    ];
});
_c = WorkspaceAccordion;
var _c;
$RefreshReg$(_c, "WorkspaceAccordion");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/components/tabs/security-definition-tab/workspace-grid.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  WorkspaceGrid: () => (WorkspaceGrid)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_3);
/* import */ var _tanstack_react_table__rspack_import_6 = __webpack_require__("./node_modules/@tanstack/table-core/build/lib/index.mjs");
/* import */ var _workspace_accordion__rspack_import_4 = __webpack_require__("./js/src/modules/graphql/components/tabs/security-definition-tab/workspace-accordion.tsx");
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





const WorkspaceGrid = (param)=>{
    let { type, value = [], onChange, isWriteable = true } = param;
    _s();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation)();
    // Map workspace type to link type for the column
    const linkType = type === 'documents' ? 'document-link' : type === 'assets' ? 'asset-link' : 'object-link';
    const columns = (0,react__rspack_import_1.useMemo)(()=>{
        const columnHelper = (0,_tanstack_react_table__rspack_import_6.createColumnHelper)();
        return [
            columnHelper.accessor('path', {
                header: t('data-hub.workspaces.path'),
                size: 300,
                meta: {
                    editable: isWriteable,
                    type: linkType,
                    autoWidth: true
                }
            }),
            columnHelper.accessor('create', {
                header: t('data-hub.workspaces.create'),
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
            columnHelper.accessor('read', {
                header: t('data-hub.workspaces.read'),
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
                header: t('data-hub.workspaces.update'),
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
                header: t('data-hub.workspaces.delete'),
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
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/security-definition-tab/workspace-grid.tsx",
                            lineNumber: 102,
                            columnNumber: 13
                        }, undefined)
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/security-definition-tab/workspace-grid.tsx",
                        lineNumber: 98,
                        columnNumber: 11
                    }, undefined),
                enableResizing: false,
                enableSorting: false
            }
        ];
    }, [
        linkType,
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
            children: ()=>/*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_workspace_accordion__rspack_import_4.WorkspaceAccordion, {
                    onChange: onChange,
                    type: type,
                    value: value
                }, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/security-definition-tab/workspace-grid.tsx",
                    lineNumber: 130,
                    columnNumber: 11
                }, undefined)
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/security-definition-tab/workspace-grid.tsx",
            lineNumber: 128,
            columnNumber: 7
        }, undefined)
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/graphql/components/tabs/security-definition-tab/workspace-grid.tsx",
        lineNumber: 122,
        columnNumber: 5
    }, undefined);
};
_s(WorkspaceGrid, "qdzg63pBJl6l39Idk27W0YRinRM=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation
    ];
});
_c = WorkspaceGrid;
var _c;
$RefreshReg$(_c, "WorkspaceGrid");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/graphql-api-slice-enhanced.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  api: () => (enhancedGraphqlApiSlice),
  useBundleDataHubGraphqlExplorerQuery: () => (useBundleDataHubGraphqlExplorerQuery),
  useBundleDataHubGraphqlExplorerUrlQuery: () => (useBundleDataHubGraphqlExplorerUrlQuery),
  useLazyBundleDataHubGraphqlExplorerQuery: () => (useLazyBundleDataHubGraphqlExplorerQuery),
  useLazyBundleDataHubGraphqlExplorerUrlQuery: () => (useLazyBundleDataHubGraphqlExplorerUrlQuery)
});
/* import */ var _graphql_api_slice_gen__rspack_import_0 = __webpack_require__("./js/src/modules/graphql/graphql-api-slice.gen.ts");
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
const enhancedGraphqlApiSlice = _graphql_api_slice_gen__rspack_import_0.api.enhanceEndpoints({
    addTagTypes: [
        'DataHubGraphql'
    ],
    endpoints: {
        bundleDataHubGraphqlExplorer: {
            providesTags: (result, error, arg)=>[
                    {
                        type: 'DataHubGraphql',
                        id: arg.clientname
                    }
                ]
        }
    }
});

const { useBundleDataHubGraphqlExplorerQuery, useLazyBundleDataHubGraphqlExplorerQuery, useBundleDataHubGraphqlExplorerUrlQuery, useLazyBundleDataHubGraphqlExplorerUrlQuery } = enhancedGraphqlApiSlice;

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/graphql-api-slice.gen.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  addTagTypes: () => (addTagTypes),
  api: () => (injectedRtkApi),
  useBundleDataHubGraphqlExplorerQuery: () => (useBundleDataHubGraphqlExplorerQuery),
  useBundleDataHubGraphqlExplorerUrlQuery: () => (useBundleDataHubGraphqlExplorerUrlQuery)
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
            bundleDataHubGraphqlExplorer: build.query({
                query: (queryArg)=>({
                        url: `/pimcore-studio/api/bundle/data-hub/graphql/explorer/${queryArg.clientname}`
                    }),
                providesTags: [
                    "Bundle Data Hub"
                ]
            }),
            bundleDataHubGraphqlExplorerUrl: build.query({
                query: (queryArg)=>({
                        url: `/pimcore-studio/api/bundle/data-hub/graphql/explorer-url/${queryArg.name}`
                    }),
                providesTags: [
                    "Bundle Data Hub"
                ]
            })
        }),
    overrideExisting: false
});

const { useBundleDataHubGraphqlExplorerQuery, useBundleDataHubGraphqlExplorerUrlQuery } = injectedRtkApi;

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/index.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  GraphQLModule: () => (GraphQLModule)
});
/* import */ var _pimcore_studio_ui_bundle__rspack_import_0 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle");
/* import */ var _pimcore_studio_ui_bundle__rspack_import_0_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle__rspack_import_0);
/* import */ var _config_service_ids__rspack_import_1 = __webpack_require__("./js/src/config/service-ids.ts");
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

const GraphQLModule = {
    onInit: ()=>{
        const operatorQueryRegistry = _pimcore_studio_ui_bundle__rspack_import_0.container.get(_config_service_ids__rspack_import_1.bundleServiceIds["DataHub/DynamicTypes/Operator/GraphQL/QueryRegistry"]);
        operatorQueryRegistry.registerDynamicType(_pimcore_studio_ui_bundle__rspack_import_0.container.get(_config_service_ids__rspack_import_1.bundleServiceIds["DataHub/DynamicTypes/Operator/Alias"]));
        operatorQueryRegistry.registerDynamicType(_pimcore_studio_ui_bundle__rspack_import_0.container.get(_config_service_ids__rspack_import_1.bundleServiceIds["DataHub/DynamicTypes/Operator/Concatenator"]));
        operatorQueryRegistry.registerDynamicType(_pimcore_studio_ui_bundle__rspack_import_0.container.get(_config_service_ids__rspack_import_1.bundleServiceIds["DataHub/DynamicTypes/Operator/DateFormatter"]));
        operatorQueryRegistry.registerDynamicType(_pimcore_studio_ui_bundle__rspack_import_0.container.get(_config_service_ids__rspack_import_1.bundleServiceIds["DataHub/DynamicTypes/Operator/ElementCounter"]));
        operatorQueryRegistry.registerDynamicType(_pimcore_studio_ui_bundle__rspack_import_0.container.get(_config_service_ids__rspack_import_1.bundleServiceIds["DataHub/DynamicTypes/Operator/Substring"]));
        operatorQueryRegistry.registerDynamicType(_pimcore_studio_ui_bundle__rspack_import_0.container.get(_config_service_ids__rspack_import_1.bundleServiceIds["DataHub/DynamicTypes/Operator/Text"]));
        operatorQueryRegistry.registerDynamicType(_pimcore_studio_ui_bundle__rspack_import_0.container.get(_config_service_ids__rspack_import_1.bundleServiceIds["DataHub/DynamicTypes/Operator/Thumbnail"]));
        operatorQueryRegistry.registerDynamicType(_pimcore_studio_ui_bundle__rspack_import_0.container.get(_config_service_ids__rspack_import_1.bundleServiceIds["DataHub/DynamicTypes/Operator/ThumbnailHtml"]));
        operatorQueryRegistry.registerDynamicType(_pimcore_studio_ui_bundle__rspack_import_0.container.get(_config_service_ids__rspack_import_1.bundleServiceIds["DataHub/DynamicTypes/Operator/TranslateValue"]));
        operatorQueryRegistry.registerDynamicType(_pimcore_studio_ui_bundle__rspack_import_0.container.get(_config_service_ids__rspack_import_1.bundleServiceIds["DataHub/DynamicTypes/Operator/Trimmer"]));
        const operatorMutationRegistry = _pimcore_studio_ui_bundle__rspack_import_0.container.get(_config_service_ids__rspack_import_1.bundleServiceIds["DataHub/DynamicTypes/Operator/GraphQL/MutationRegistry"]);
        operatorMutationRegistry.registerDynamicType(_pimcore_studio_ui_bundle__rspack_import_0.container.get(_config_service_ids__rspack_import_1.bundleServiceIds["DataHub/DynamicTypes/Operator/IfEmpty"]));
        operatorMutationRegistry.registerDynamicType(_pimcore_studio_ui_bundle__rspack_import_0.container.get(_config_service_ids__rspack_import_1.bundleServiceIds["DataHub/DynamicTypes/Operator/LocaleCollector"]));
        operatorMutationRegistry.registerDynamicType(_pimcore_studio_ui_bundle__rspack_import_0.container.get(_config_service_ids__rspack_import_1.bundleServiceIds["DataHub/DynamicTypes/Operator/LocaleSwitcher"]));
        operatorQueryRegistry.registerGroupConfig('formatter', {
            icon: {
                type: 'name',
                value: 'formatters',
                colorToken: 'colorCodingRed2'
            },
            priority: 10
        });
        operatorQueryRegistry.registerGroupConfig('transformer', {
            icon: {
                type: 'name',
                value: 'transformers',
                colorToken: 'colorCodingViolet2'
            },
            priority: 20
        });
        operatorQueryRegistry.registerGroupConfig('other', {
            icon: {
                type: 'name',
                value: 'other-operators',
                colorToken: 'colorCodingBeige2'
            },
            priority: 30
        });
        operatorMutationRegistry.registerGroupConfig('other', {
            icon: {
                type: 'name',
                value: 'other-operators',
                colorToken: 'colorCodingBeige2'
            },
            priority: 30
        });
    }
};

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/graphql/utils/transformers.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  extractElementPath: () => (extractElementPath),
  transformApiKeyFromBackend: () => (transformApiKeyFromBackend),
  transformApiKeyToBackend: () => (transformApiKeyToBackend),
  transformBackendToForm: () => (transformBackendToForm),
  transformFormToBackend: () => (transformFormToBackend),
  transformGenericTypeFromBackend: () => (transformGenericTypeFromBackend),
  transformGenericTypeToBackend: () => (transformGenericTypeToBackend),
  transformPermissionFromBackend: () => (transformPermissionFromBackend),
  transformPermissionToBackend: () => (transformPermissionToBackend),
  transformPermissionsFromBackend: () => (transformPermissionsFromBackend),
  transformPermissionsToBackend: () => (transformPermissionsToBackend),
  transformSchemaEntitiesFromBackend: () => (transformSchemaEntitiesFromBackend),
  transformSchemaEntitiesToBackend: () => (transformSchemaEntitiesToBackend),
  transformWorkspaceFromBackend: () => (transformWorkspaceFromBackend),
  transformWorkspaceToBackend: () => (transformWorkspaceToBackend),
  transformWorkspacesFromBackend: () => (transformWorkspacesFromBackend),
  transformWorkspacesToBackend: () => (transformWorkspacesToBackend)
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
function extractElementPath(pathValue) {
    if ((0,lodash__rspack_import_0.isString)(pathValue)) {
        return pathValue;
    }
    return (pathValue === null || pathValue === void 0 ? void 0 : pathValue.fullPath) ?? '';
}
function transformWorkspaceToBackend(workspace) {
    return {
        cpath: extractElementPath(workspace.path),
        create: workspace.create ?? false,
        read: workspace.read ?? false,
        update: workspace.update ?? false,
        delete: workspace.delete ?? false
    };
}
function transformWorkspaceFromBackend(backendWorkspace) {
    return {
        path: backendWorkspace.cpath ?? '',
        create: backendWorkspace.create ?? false,
        read: backendWorkspace.read ?? false,
        update: backendWorkspace.update ?? false,
        delete: backendWorkspace.delete ?? false
    };
}
function transformWorkspacesToBackend(workspaces) {
    return {
        document: ((workspaces === null || workspaces === void 0 ? void 0 : workspaces.documents) ?? []).map(transformWorkspaceToBackend),
        asset: ((workspaces === null || workspaces === void 0 ? void 0 : workspaces.assets) ?? []).map(transformWorkspaceToBackend),
        object: ((workspaces === null || workspaces === void 0 ? void 0 : workspaces.objects) ?? []).map(transformWorkspaceToBackend)
    };
}
function transformWorkspacesFromBackend(backendWorkspaces) {
    return {
        documents: ((backendWorkspaces === null || backendWorkspaces === void 0 ? void 0 : backendWorkspaces.document) ?? []).map(transformWorkspaceFromBackend),
        assets: ((backendWorkspaces === null || backendWorkspaces === void 0 ? void 0 : backendWorkspaces.asset) ?? []).map(transformWorkspaceFromBackend),
        objects: ((backendWorkspaces === null || backendWorkspaces === void 0 ? void 0 : backendWorkspaces.object) ?? []).map(transformWorkspaceFromBackend)
    };
}
function transformPermissionToBackend(permission, type) {
    return {
        id: permission.id,
        name: permission.name,
        read: permission.read ?? false,
        update: permission.update ?? false,
        delete: permission.delete ?? false
    };
}
function transformPermissionFromBackend(backendPermission, type) {
    return {
        id: backendPermission.id,
        name: backendPermission.name ?? (type === 'role' ? backendPermission.role ?? '' : backendPermission.user ?? ''),
        read: backendPermission.read ?? false,
        update: backendPermission.update ?? false,
        delete: backendPermission.delete ?? false
    };
}
function transformPermissionsToBackend(permissions) {
    return {
        role: ((permissions === null || permissions === void 0 ? void 0 : permissions.roles) ?? []).map((perm)=>transformPermissionToBackend(perm, 'role')),
        user: ((permissions === null || permissions === void 0 ? void 0 : permissions.users) ?? []).map((perm)=>transformPermissionToBackend(perm, 'user'))
    };
}
function transformPermissionsFromBackend(backendPermissions) {
    return {
        roles: ((backendPermissions === null || backendPermissions === void 0 ? void 0 : backendPermissions.role) ?? []).map((perm)=>transformPermissionFromBackend(perm, 'role')),
        users: ((backendPermissions === null || backendPermissions === void 0 ? void 0 : backendPermissions.user) ?? []).map((perm)=>transformPermissionFromBackend(perm, 'user'))
    };
}
function transformGenericTypeToBackend(genericType) {
    return {
        name: genericType.name,
        readAllowed: genericType.read ?? false,
        createAllowed: genericType.create ?? false,
        updateAllowed: genericType.update ?? false,
        deleteAllowed: genericType.delete ?? false
    };
}
function transformGenericTypeFromBackend(backendEntity) {
    return {
        name: backendEntity.name,
        readPossible: backendEntity.readPossible ?? false,
        createPossible: backendEntity.createPossible ?? false,
        updatePossible: backendEntity.updatePossible ?? false,
        deletePossible: backendEntity.deletePossible ?? false,
        read: backendEntity.readAllowed ?? false,
        create: backendEntity.createAllowed ?? false,
        update: backendEntity.updateAllowed ?? false,
        delete: backendEntity.deleteAllowed ?? false
    };
}
// Maps UI 'entity' field to backend 'name' field
function transformSchemaEntitiesToBackend(entities) {
    if (Array.isArray(entities)) {
        return entities.map((entity)=>{
            const { entity: entityField, id, ...rest } = entity;
            return {
                id,
                name: entityField ?? rest.name ?? id,
                ...rest
            };
        });
    }
    return Object.entries(entities).map((param)=>{
        let [id, entity] = param;
        const { entity: entityField, ...rest } = entity;
        return {
            id,
            name: entityField ?? rest.name ?? id,
            ...rest
        };
    });
}
function transformSchemaEntitiesFromBackend(backendEntities) {
    let defaultValue = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : [];
    if ((0,lodash__rspack_import_0.isNil)(backendEntities)) {
        return defaultValue;
    }
    if (Array.isArray(backendEntities)) {
        return backendEntities.map((entity)=>({
                ...entity,
                entity: entity.name ?? entity.id
            }));
    }
    return Object.entries(backendEntities).map((param)=>{
        let [id, entity] = param;
        return {
            id,
            ...entity,
            entity: entity.name ?? id
        };
    });
}
function transformApiKeyToBackend(apikey) {
    if ((0,lodash__rspack_import_0.isNil)(apikey)) {
        return [];
    }
    if (typeof apikey === 'string') {
        return apikey.split('\n').filter((key)=>key.trim() !== '');
    }
    return [];
}
function transformApiKeyFromBackend(apikey) {
    if ((0,lodash__rspack_import_0.isNil)(apikey)) {
        return '';
    }
    if (Array.isArray(apikey)) {
        return apikey.join('\n');
    }
    return apikey;
}
function transformFormToBackend(formValues, existingConfig) {
    var _formValues_security, _formValues_security1, _formValues_security2, _formValues_security3, _formValues_schema, _formValues_schema1, _formValues_schema2;
    const { userPermissions, ...restConfig } = existingConfig;
    return {
        ...restConfig,
        general: {
            ...existingConfig.general,
            active: formValues.active,
            description: formValues.description,
            group: formValues.group
        },
        security: {
            ...existingConfig.security,
            method: (_formValues_security = formValues.security) === null || _formValues_security === void 0 ? void 0 : _formValues_security.method,
            apikey: transformApiKeyToBackend((_formValues_security1 = formValues.security) === null || _formValues_security1 === void 0 ? void 0 : _formValues_security1.apikey),
            skipPermissionCheck: ((_formValues_security2 = formValues.security) === null || _formValues_security2 === void 0 ? void 0 : _formValues_security2.skipPermissionCheck) ?? false,
            disableIntrospection: ((_formValues_security3 = formValues.security) === null || _formValues_security3 === void 0 ? void 0 : _formValues_security3.disableIntrospection) ?? false
        },
        workspaces: transformWorkspacesToBackend(formValues.workspaces),
        permissions: transformPermissionsToBackend(formValues.permissions),
        schema: {
            ...existingConfig.schema,
            queryEntities: transformSchemaEntitiesToBackend(((_formValues_schema = formValues.schema) === null || _formValues_schema === void 0 ? void 0 : _formValues_schema.query) ?? []),
            mutationEntities: transformSchemaEntitiesToBackend(((_formValues_schema1 = formValues.schema) === null || _formValues_schema1 === void 0 ? void 0 : _formValues_schema1.mutation) ?? []),
            specialEntities: (((_formValues_schema2 = formValues.schema) === null || _formValues_schema2 === void 0 ? void 0 : _formValues_schema2.genericTypes) ?? []).map(transformGenericTypeToBackend)
        }
    };
}
function transformBackendToForm(backendConfig, configName) {
    var _backendConfig_general, _backendConfig_general1, _backendConfig_general2, _backendConfig_security, _backendConfig_security1, _backendConfig_security2, _backendConfig_security3, _backendConfig_schema, _backendConfig_schema1, _backendConfig_schema2;
    return {
        active: ((_backendConfig_general = backendConfig.general) === null || _backendConfig_general === void 0 ? void 0 : _backendConfig_general.active) ?? true,
        name: configName,
        description: ((_backendConfig_general1 = backendConfig.general) === null || _backendConfig_general1 === void 0 ? void 0 : _backendConfig_general1.description) ?? '',
        group: ((_backendConfig_general2 = backendConfig.general) === null || _backendConfig_general2 === void 0 ? void 0 : _backendConfig_general2.group) ?? '',
        security: {
            method: ((_backendConfig_security = backendConfig.security) === null || _backendConfig_security === void 0 ? void 0 : _backendConfig_security.method) ?? 'datahub_apikey',
            apikey: transformApiKeyFromBackend((_backendConfig_security1 = backendConfig.security) === null || _backendConfig_security1 === void 0 ? void 0 : _backendConfig_security1.apikey),
            skipPermissionCheck: ((_backendConfig_security2 = backendConfig.security) === null || _backendConfig_security2 === void 0 ? void 0 : _backendConfig_security2.skipPermissionCheck) ?? false,
            disableIntrospection: ((_backendConfig_security3 = backendConfig.security) === null || _backendConfig_security3 === void 0 ? void 0 : _backendConfig_security3.disableIntrospection) ?? false
        },
        workspaces: transformWorkspacesFromBackend(backendConfig.workspaces),
        permissions: transformPermissionsFromBackend(backendConfig.permissions),
        schema: {
            query: transformSchemaEntitiesFromBackend((_backendConfig_schema = backendConfig.schema) === null || _backendConfig_schema === void 0 ? void 0 : _backendConfig_schema.queryEntities, []),
            mutation: transformSchemaEntitiesFromBackend((_backendConfig_schema1 = backendConfig.schema) === null || _backendConfig_schema1 === void 0 ? void 0 : _backendConfig_schema1.mutationEntities, []),
            genericTypes: (((_backendConfig_schema2 = backendConfig.schema) === null || _backendConfig_schema2 === void 0 ? void 0 : _backendConfig_schema2.specialEntities) ?? []).map(transformGenericTypeFromBackend)
        }
    };
}

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/operators/components/operator-modal.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  OperatorModal: () => (OperatorModal)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_3);
/* import */ var _hooks_use_operator_modal__rspack_import_4 = __webpack_require__("./js/src/modules/operators/hooks/use-operator-modal.tsx");
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



const OperatorModal = (param)=>{
    let { children, initialValues, size, footer, disabled = false, ...props } = param;
    _s();
    const { onCancel } = props;
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation)();
    const { localizedName, getInitialValues, updateAttributes } = (0,_hooks_use_operator_modal__rspack_import_4.useOperatorModal)(props);
    const [form] = _pimcore_studio_ui_bundle_components__rspack_import_2.Form.useForm();
    (0,react__rspack_import_1.useEffect)(()=>{
        const initialData = getInitialValues(initialValues);
        form.setFieldsValue(initialData);
    }, []);
    const handleApply = (0,react__rspack_import_1.useCallback)(async ()=>{
        if (disabled) return;
        const values = await form.validateFields();
        updateAttributes(values);
    }, [
        disabled,
        form,
        updateAttributes
    ]);
    (0,react__rspack_import_1.useEffect)(()=>{
        if (disabled) return;
        const handleKeyDown = (e)=>{
            if (e.key === 'Enter') {
                void handleApply();
            }
        };
        document.addEventListener('keydown', handleKeyDown);
        return ()=>{
            document.removeEventListener('keydown', handleKeyDown);
        };
    }, [
        disabled,
        handleApply
    ]);
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Modal, {
        footer: (footer === null || footer === void 0 ? void 0 : footer({
            handleApply,
            onCancel,
            disabled
        })) ?? (disabled ? null : undefined),
        okText: t('apply'),
        onCancel: onCancel,
        onOk: footer === undefined && !disabled ? ()=>{
            void handleApply();
        } : undefined,
        open: true,
        size: size,
        title: localizedName,
        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.FormKit, {
            formProps: {
                form,
                disabled
            },
            children: children({
                form
            })
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/components/operator-modal.tsx",
            lineNumber: 72,
            columnNumber: 7
        }, undefined)
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/components/operator-modal.tsx",
        lineNumber: 63,
        columnNumber: 5
    }, undefined);
};
_s(OperatorModal, "ehWOO+lUepke2/ovC/BZAE3tQds=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation,
        _hooks_use_operator_modal__rspack_import_4.useOperatorModal,
        _pimcore_studio_ui_bundle_components__rspack_import_2.Form.useForm
    ];
});
_c = OperatorModal;
var _c;
$RefreshReg$(_c, "OperatorModal");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/operators/components/thumbnail-select.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  ThumbnailSelect: () => (ThumbnailSelect)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _thumbnails_api_slice_gen__rspack_import_3 = __webpack_require__("./js/src/modules/operators/thumbnails-api-slice.gen.ts");
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


const ThumbnailSelect = (param)=>{
    let { ...props } = param;
    _s();
    const { data: thumbnailsData } = (0,_thumbnails_api_slice_gen__rspack_import_3.useBundleDataHubThumbnailsCollectionQuery)();
    const thumbnailOptions = (thumbnailsData === null || thumbnailsData === void 0 ? void 0 : thumbnailsData.items.map((thumbnail)=>({
            label: thumbnail.text,
            value: thumbnail.id
        }))) ?? [];
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Select, {
        options: thumbnailOptions,
        showSearch: true,
        style: {
            width: '100%'
        },
        ...props
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/components/thumbnail-select.tsx",
        lineNumber: 26,
        columnNumber: 5
    }, undefined);
};
_s(ThumbnailSelect, "XIzzAKI7zLVnCM89Fl5KssSOLhA=", false, function() {
    return [
        _thumbnails_api_slice_gen__rspack_import_3.useBundleDataHubThumbnailsCollectionQuery
    ];
});
_c = ThumbnailSelect;
var _c;
$RefreshReg$(_c, "ThumbnailSelect");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/operators/dynamic-type-operator-abstract.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  DynamicTypeOperatorAbstract: () => (DynamicTypeOperatorAbstract)
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

class DynamicTypeOperatorAbstract {
    getGroup() {
        return 'other';
    }
    getSubGroup() {
        return undefined;
    }
    // Return undefined for unlimited children or a number to enforce a limit
    getMaxChildCount() {
        return undefined;
    }
    allowChild(targetConfig, sourceConfig) {
        const maxChildCount = this.getMaxChildCount();
        if (maxChildCount !== undefined) {
            const attributes = targetConfig.attributes;
            const currentChildCount = Array.isArray(attributes === null || attributes === void 0 ? void 0 : attributes.children) ? attributes.children.length : 0;
            if (currentChildCount >= maxChildCount) {
                return false;
            }
        }
        const allowedTypes = this.allowedChildTypes();
        if (allowedTypes.length > 0 && (sourceConfig === null || sourceConfig === void 0 ? void 0 : sourceConfig.dataType) !== undefined) {
            return allowedTypes.includes(sourceConfig.dataType);
        }
        return true;
    }
    allowsChildren() {
        return true;
    }
    // Return empty array for no restrictions on child data types
    allowedChildTypes() {
        return [];
    }
}
DynamicTypeOperatorAbstract = (0,_swc_helpers_ts_decorate__rspack_import_1.__decorate)([
    (0,_pimcore_studio_ui_bundle_app__rspack_import_0.injectable)()
], DynamicTypeOperatorAbstract);

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/operators/dynamic-type-operator-registry.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  DynamicTypeOperatorRegistry: () => (DynamicTypeOperatorRegistry)
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

class DynamicTypeOperatorRegistry extends _pimcore_studio_ui_bundle_modules_element__rspack_import_1.DynamicTypeRegistryAbstract {
    registerGroupConfig(group, config) {
        this.groupConfigs.set(group, config);
    }
    getGroupConfig(group) {
        return this.groupConfigs.get(group);
    }
    constructor(...args){
        super(...args), this.groupConfigs = new Map();
    }
}
DynamicTypeOperatorRegistry = (0,_swc_helpers_ts_decorate__rspack_import_2.__decorate)([
    (0,_pimcore_studio_ui_bundle_app__rspack_import_0.injectable)()
], DynamicTypeOperatorRegistry);

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/operators/hooks/use-operator-groups.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  useOperatorGroups: () => (useOperatorGroups)
});
/* import */ var react__rspack_import_0 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_0_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_0);
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
function useOperatorGroups(registry) {
    _s();
    const getGroupIcon = react__rspack_import_0_default().useCallback((groupName)=>{
        const groupConfig = registry.getGroupConfig(groupName);
        return (groupConfig === null || groupConfig === void 0 ? void 0 : groupConfig.icon) ?? {
            type: 'name',
            value: 'data-object'
        };
    }, [
        registry
    ]);
    return {
        getGroupIcon
    };
}
_s(useOperatorGroups, "ccbrT6CTqspv6DhIrN28xU6uUN0=");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/operators/hooks/use-operator-modal.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  useOperatorModal: () => (useOperatorModal)
});
/* import */ var react__rspack_import_0 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_0_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_0);
/* import */ var _use_operator__rspack_import_1 = __webpack_require__("./js/src/modules/operators/hooks/use-operator.tsx");
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

function useOperatorModal(props) {
    _s();
    const { operator, config, onApply } = props;
    const { getLocalizedName } = (0,_use_operator__rspack_import_1.useOperator)();
    const localizedName = (0,react__rspack_import_0.useMemo)(()=>getLocalizedName(operator), [
        getLocalizedName,
        operator
    ]);
    const updateAttributes = (0,react__rspack_import_0.useCallback)((values)=>{
        const updatedConfig = {
            ...config,
            attributes: {
                ...config.attributes,
                ...values
            }
        };
        onApply(updatedConfig);
    }, [
        config,
        onApply
    ]);
    const getInitialValues = (0,react__rspack_import_0.useCallback)((additionalDefaults)=>{
        const result = {
            label: localizedName,
            ...additionalDefaults,
            ...config.attributes
        };
        return result;
    }, [
        config.attributes,
        localizedName
    ]);
    return {
        localizedName,
        updateAttributes,
        getInitialValues
    };
}
_s(useOperatorModal, "0zWtX7GwVD+i+zyKmHFXZk5EKhk=", false, function() {
    return [
        _use_operator__rspack_import_1.useOperator
    ];
});

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/operators/hooks/use-operator.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  useOperator: () => (useOperator)
});
/* import */ var react__rspack_import_0 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_0_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_0);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_1 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_1);
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


function useOperator() {
    _s();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_1.useTranslation)();
    const getLocalizedName = (0,react__rspack_import_0.useCallback)((operator)=>t(`data-hub.operator.${(0,lodash__rspack_import_2.kebabCase)(operator.id)}`), []);
    const getGroup = (0,react__rspack_import_0.useCallback)((operator)=>{
        const group = operator.getGroup();
        const subGroup = operator.getSubGroup();
        return {
            group: t(`data-hub.operator.group.${(0,lodash__rspack_import_2.kebabCase)(group)}`),
            subGroup: (0,lodash__rspack_import_2.isNil)(subGroup) ? undefined : t(`data-hub.operator.subgroup.${(0,lodash__rspack_import_2.kebabCase)(subGroup)}`)
        };
    }, []);
    const getIcon = (0,react__rspack_import_0.useCallback)((operator, registry)=>{
        const operatorIcon = operator.getIcon();
        if (!(0,lodash__rspack_import_2.isNil)(operatorIcon.colorToken)) {
            return operatorIcon;
        }
        const groupName = operator.getGroup();
        const groupConfig = registry.getGroupConfig(groupName);
        if ((0,lodash__rspack_import_2.isNil)(groupConfig === null || groupConfig === void 0 ? void 0 : groupConfig.icon.colorToken)) {
            return operatorIcon;
        }
        return {
            ...operatorIcon,
            colorToken: groupConfig.icon.colorToken
        };
    }, []);
    return {
        getLocalizedName,
        getGroup,
        getIcon
    };
}
_s(useOperator, "PR6d5SX23/+JlnzumfGbyJfVZIA=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_1.useTranslation
    ];
});

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/operators/operators/alias/alias-config-modal.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  AliasConfigModal: () => (AliasConfigModal)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_3);
/* import */ var _components_operator_modal__rspack_import_4 = __webpack_require__("./js/src/modules/operators/components/operator-modal.tsx");
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



const AliasConfigModal = (props)=>{
    _s();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation)();
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_components_operator_modal__rspack_import_4.OperatorModal, {
        ...props,
        children: ()=>/*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                label: t('data-hub.label'),
                name: "label",
                children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Input, {}, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/alias/alias-config-modal.tsx",
                    lineNumber: 28,
                    columnNumber: 11
                }, undefined)
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/alias/alias-config-modal.tsx",
                lineNumber: 24,
                columnNumber: 9
            }, undefined)
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/alias/alias-config-modal.tsx",
        lineNumber: 22,
        columnNumber: 5
    }, undefined);
};
_s(AliasConfigModal, "zlIdU9EjM2llFt74AbE2KsUJXyM=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation
    ];
});
_c = AliasConfigModal;
var _c;
$RefreshReg$(_c, "AliasConfigModal");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/operators/operators/alias/dynamic-type-operator-alias.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  DynamicTypeOperatorAlias: () => (DynamicTypeOperatorAlias)
});
/* import */ var _swc_helpers_ts_decorate__rspack_import_6 = __webpack_require__("./node_modules/tslib/tslib.es6.mjs");
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_utils__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/utils");
/* import */ var _pimcore_studio_ui_bundle_utils__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_utils__rspack_import_3);
/* import */ var _dynamic_type_operator_abstract__rspack_import_4 = __webpack_require__("./js/src/modules/operators/dynamic-type-operator-abstract.ts");
/* import */ var _alias_config_modal__rspack_import_5 = __webpack_require__("./js/src/modules/operators/operators/alias/alias-config-modal.tsx");
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




class DynamicTypeOperatorAlias extends _dynamic_type_operator_abstract__rspack_import_4.DynamicTypeOperatorAbstract {
    getIcon() {
        return {
            type: 'name',
            value: 'text-input'
        };
    }
    getLabel(config, localizedName) {
        const label = config.attributes.label;
        return (0,_pimcore_studio_ui_bundle_utils__rspack_import_3.isNonEmptyString)(label) ? label : localizedName;
    }
    getConfigModal(props) {
        return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_alias_config_modal__rspack_import_5.AliasConfigModal, {
            ...props
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/alias/dynamic-type-operator-alias.tsx",
            lineNumber: 36,
            columnNumber: 12
        }, this);
    }
    getGroup() {
        return 'other';
    }
    getMaxChildCount() {
        return 1;
    }
    constructor(...args){
        super(...args), this.id = 'Alias';
    }
}
DynamicTypeOperatorAlias = (0,_swc_helpers_ts_decorate__rspack_import_6.__decorate)([
    (0,_pimcore_studio_ui_bundle_app__rspack_import_2.injectable)()
], DynamicTypeOperatorAlias);

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/operators/operators/concatenator/concatenator-config-modal.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  ConcatenatorConfigModal: () => (ConcatenatorConfigModal)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_3);
/* import */ var _components_operator_modal__rspack_import_4 = __webpack_require__("./js/src/modules/operators/components/operator-modal.tsx");
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



const ConcatenatorConfigModal = (props)=>{
    _s();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation)();
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_components_operator_modal__rspack_import_4.OperatorModal, {
        ...props,
        initialValues: {
            glue: ''
        },
        children: ()=>/*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(react_jsx_dev_runtime__rspack_import_0.Fragment, {
                children: [
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                        label: t('data-hub.label'),
                        name: "label",
                        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Input, {}, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/concatenator/concatenator-config-modal.tsx",
                            lineNumber: 32,
                            columnNumber: 13
                        }, undefined)
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/concatenator/concatenator-config-modal.tsx",
                        lineNumber: 28,
                        columnNumber: 11
                    }, undefined),
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                        label: t('data-hub.operator.glue'),
                        name: "glue",
                        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Input, {}, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/concatenator/concatenator-config-modal.tsx",
                            lineNumber: 38,
                            columnNumber: 13
                        }, undefined)
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/concatenator/concatenator-config-modal.tsx",
                        lineNumber: 34,
                        columnNumber: 11
                    }, undefined)
                ]
            }, void 0, true)
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/concatenator/concatenator-config-modal.tsx",
        lineNumber: 22,
        columnNumber: 5
    }, undefined);
};
_s(ConcatenatorConfigModal, "zlIdU9EjM2llFt74AbE2KsUJXyM=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation
    ];
});
_c = ConcatenatorConfigModal;
var _c;
$RefreshReg$(_c, "ConcatenatorConfigModal");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/operators/operators/concatenator/dynamic-type-operator-concatenator.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  DynamicTypeOperatorConcatenator: () => (DynamicTypeOperatorConcatenator)
});
/* import */ var _swc_helpers_ts_decorate__rspack_import_8 = __webpack_require__("./node_modules/tslib/tslib.es6.mjs");
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_2);
/* import */ var lodash__rspack_import_3 = __webpack_require__("./node_modules/lodash/lodash.js");
/* import */ var lodash__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(lodash__rspack_import_3);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_4 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_4_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_4);
/* import */ var _pimcore_studio_ui_bundle_utils__rspack_import_5 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/utils");
/* import */ var _pimcore_studio_ui_bundle_utils__rspack_import_5_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_utils__rspack_import_5);
/* import */ var _dynamic_type_operator_abstract__rspack_import_6 = __webpack_require__("./js/src/modules/operators/dynamic-type-operator-abstract.ts");
/* import */ var _concatenator_config_modal__rspack_import_7 = __webpack_require__("./js/src/modules/operators/operators/concatenator/concatenator-config-modal.tsx");
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






class DynamicTypeOperatorConcatenator extends _dynamic_type_operator_abstract__rspack_import_6.DynamicTypeOperatorAbstract {
    getIcon() {
        return {
            type: 'name',
            value: 'operator-concatenator'
        };
    }
    getLabel(config, localizedName) {
        const label = config.attributes.label;
        const glue = config.attributes.glue;
        const displayLabel = (0,_pimcore_studio_ui_bundle_utils__rspack_import_5.isNonEmptyString)(label) ? label : localizedName;
        if (!(0,lodash__rspack_import_3.isNil)(glue) && glue !== '') {
            return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(react_jsx_dev_runtime__rspack_import_0.Fragment, {
                children: [
                    displayLabel,
                    " ",
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_4.Text, {
                        type: "secondary",
                        children: [
                            "(",
                            glue,
                            ")"
                        ]
                    }, void 0, true, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/concatenator/dynamic-type-operator-concatenator.tsx",
                        lineNumber: 41,
                        columnNumber: 26
                    }, this)
                ]
            }, void 0, true);
        }
        return displayLabel;
    }
    getConfigModal(props) {
        return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_concatenator_config_modal__rspack_import_7.ConcatenatorConfigModal, {
            ...props
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/concatenator/dynamic-type-operator-concatenator.tsx",
            lineNumber: 50,
            columnNumber: 12
        }, this);
    }
    getGroup() {
        return 'transformer';
    }
    getSubGroup() {
        return 'other';
    }
    allowsChildren() {
        return true;
    }
    constructor(...args){
        super(...args), this.id = 'Concatenator';
    }
}
DynamicTypeOperatorConcatenator = (0,_swc_helpers_ts_decorate__rspack_import_8.__decorate)([
    (0,_pimcore_studio_ui_bundle_app__rspack_import_2.injectable)()
], DynamicTypeOperatorConcatenator);

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/operators/operators/date-formatter/date-formatter-config-modal.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  DateFormatterConfigModal: () => (DateFormatterConfigModal)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_3);
/* import */ var _components_operator_modal__rspack_import_4 = __webpack_require__("./js/src/modules/operators/components/operator-modal.tsx");
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



const DateFormatterConfigModal = (props)=>{
    _s();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation)();
    const openHelp = ()=>{
        window.open('https://www.php.net/manual/en/function.date.php', '_blank', 'noopener,noreferrer');
    };
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_components_operator_modal__rspack_import_4.OperatorModal, {
        ...props,
        footer: (param)=>{
            let { handleApply, onCancel, disabled } = param;
            return disabled ? null : /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Flex, {
                justify: "space-between",
                children: [
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.IconTextButton, {
                        icon: {
                            value: 'help-circle'
                        },
                        onClick: openHelp,
                        type: "default",
                        children: t('data-hub.help')
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/date-formatter/date-formatter-config-modal.tsx",
                        lineNumber: 33,
                        columnNumber: 15
                    }, undefined),
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Flex, {
                        gap: "small",
                        children: [
                            /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Button, {
                                onClick: onCancel,
                                children: t('cancel')
                            }, void 0, false, {
                                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/date-formatter/date-formatter-config-modal.tsx",
                                lineNumber: 42,
                                columnNumber: 17
                            }, undefined),
                            /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Button, {
                                onClick: ()=>{
                                    void handleApply();
                                },
                                type: "primary",
                                children: t('apply')
                            }, void 0, false, {
                                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/date-formatter/date-formatter-config-modal.tsx",
                                lineNumber: 45,
                                columnNumber: 17
                            }, undefined)
                        ]
                    }, void 0, true, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/date-formatter/date-formatter-config-modal.tsx",
                        lineNumber: 41,
                        columnNumber: 15
                    }, undefined)
                ]
            }, void 0, true, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/date-formatter/date-formatter-config-modal.tsx",
                lineNumber: 32,
                columnNumber: 13
            }, undefined);
        },
        initialValues: {
            format: 'Y-m-d H:i:s'
        },
        size: "M",
        children: ()=>/*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(react_jsx_dev_runtime__rspack_import_0.Fragment, {
                children: [
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                        label: t('data-hub.label'),
                        name: "label",
                        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Input, {
                            maxLength: 255
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/date-formatter/date-formatter-config-modal.tsx",
                            lineNumber: 64,
                            columnNumber: 13
                        }, undefined)
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/date-formatter/date-formatter-config-modal.tsx",
                        lineNumber: 60,
                        columnNumber: 11
                    }, undefined),
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                        label: t('data-hub.operator.dateformatter.format'),
                        name: "format",
                        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Input, {
                            maxLength: 255
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/date-formatter/date-formatter-config-modal.tsx",
                            lineNumber: 71,
                            columnNumber: 13
                        }, undefined)
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/date-formatter/date-formatter-config-modal.tsx",
                        lineNumber: 67,
                        columnNumber: 11
                    }, undefined)
                ]
            }, void 0, true)
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/date-formatter/date-formatter-config-modal.tsx",
        lineNumber: 26,
        columnNumber: 5
    }, undefined);
};
_s(DateFormatterConfigModal, "zlIdU9EjM2llFt74AbE2KsUJXyM=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation
    ];
});
_c = DateFormatterConfigModal;
var _c;
$RefreshReg$(_c, "DateFormatterConfigModal");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/operators/operators/date-formatter/dynamic-type-operator-date-formatter.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  DynamicTypeOperatorDateFormatter: () => (DynamicTypeOperatorDateFormatter)
});
/* import */ var _swc_helpers_ts_decorate__rspack_import_8 = __webpack_require__("./node_modules/tslib/tslib.es6.mjs");
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_2);
/* import */ var lodash__rspack_import_3 = __webpack_require__("./node_modules/lodash/lodash.js");
/* import */ var lodash__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(lodash__rspack_import_3);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_4 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_4_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_4);
/* import */ var _pimcore_studio_ui_bundle_utils__rspack_import_5 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/utils");
/* import */ var _pimcore_studio_ui_bundle_utils__rspack_import_5_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_utils__rspack_import_5);
/* import */ var _dynamic_type_operator_abstract__rspack_import_6 = __webpack_require__("./js/src/modules/operators/dynamic-type-operator-abstract.ts");
/* import */ var _date_formatter_config_modal__rspack_import_7 = __webpack_require__("./js/src/modules/operators/operators/date-formatter/date-formatter-config-modal.tsx");
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






class DynamicTypeOperatorDateFormatter extends _dynamic_type_operator_abstract__rspack_import_6.DynamicTypeOperatorAbstract {
    getIcon() {
        return {
            type: 'name',
            value: 'date-formatter'
        };
    }
    getLabel(config, localizedName) {
        const label = config.attributes.label;
        const format = config.attributes.format;
        const displayLabel = (0,_pimcore_studio_ui_bundle_utils__rspack_import_5.isNonEmptyString)(label) ? label : localizedName;
        if (!(0,lodash__rspack_import_3.isNil)(format)) {
            return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(react_jsx_dev_runtime__rspack_import_0.Fragment, {
                children: [
                    displayLabel,
                    " ",
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_4.Text, {
                        type: "secondary",
                        children: [
                            "(",
                            format,
                            ")"
                        ]
                    }, void 0, true, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/date-formatter/dynamic-type-operator-date-formatter.tsx",
                        lineNumber: 41,
                        columnNumber: 26
                    }, this)
                ]
            }, void 0, true);
        }
        return displayLabel;
    }
    getConfigModal(props) {
        return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_date_formatter_config_modal__rspack_import_7.DateFormatterConfigModal, {
            ...props
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/date-formatter/dynamic-type-operator-date-formatter.tsx",
            lineNumber: 50,
            columnNumber: 12
        }, this);
    }
    getGroup() {
        return 'formatter';
    }
    getSubGroup() {
        return 'other';
    }
    getMaxChildCount() {
        return 1;
    }
    constructor(...args){
        super(...args), this.id = 'DateFormatter';
    }
}
DynamicTypeOperatorDateFormatter = (0,_swc_helpers_ts_decorate__rspack_import_8.__decorate)([
    (0,_pimcore_studio_ui_bundle_app__rspack_import_2.injectable)()
], DynamicTypeOperatorDateFormatter);

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/operators/operators/element-counter/dynamic-type-operator-element-counter.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  DynamicTypeOperatorElementCounter: () => (DynamicTypeOperatorElementCounter)
});
/* import */ var _swc_helpers_ts_decorate__rspack_import_6 = __webpack_require__("./node_modules/tslib/tslib.es6.mjs");
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_utils__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/utils");
/* import */ var _pimcore_studio_ui_bundle_utils__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_utils__rspack_import_3);
/* import */ var _dynamic_type_operator_abstract__rspack_import_4 = __webpack_require__("./js/src/modules/operators/dynamic-type-operator-abstract.ts");
/* import */ var _element_counter_config_modal__rspack_import_5 = __webpack_require__("./js/src/modules/operators/operators/element-counter/element-counter-config-modal.tsx");
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




class DynamicTypeOperatorElementCounter extends _dynamic_type_operator_abstract__rspack_import_4.DynamicTypeOperatorAbstract {
    getIcon() {
        return {
            type: 'name',
            value: 'counter'
        };
    }
    getLabel(config, localizedName) {
        const label = config.attributes.label;
        return (0,_pimcore_studio_ui_bundle_utils__rspack_import_3.isNonEmptyString)(label) ? label : localizedName;
    }
    getConfigModal(props) {
        return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_element_counter_config_modal__rspack_import_5.ElementCounterConfigModal, {
            ...props
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/element-counter/dynamic-type-operator-element-counter.tsx",
            lineNumber: 37,
            columnNumber: 12
        }, this);
    }
    getGroup() {
        return 'transformer';
    }
    getSubGroup() {
        return 'other';
    }
    allowsChildren() {
        return true;
    }
    constructor(...args){
        super(...args), this.id = 'ElementCounter';
    }
}
DynamicTypeOperatorElementCounter = (0,_swc_helpers_ts_decorate__rspack_import_6.__decorate)([
    (0,_pimcore_studio_ui_bundle_app__rspack_import_2.injectable)()
], DynamicTypeOperatorElementCounter);

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/operators/operators/element-counter/element-counter-config-modal.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  ElementCounterConfigModal: () => (ElementCounterConfigModal)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_3);
/* import */ var _components_operator_modal__rspack_import_4 = __webpack_require__("./js/src/modules/operators/components/operator-modal.tsx");
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



const ElementCounterConfigModal = (props)=>{
    _s();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation)();
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_components_operator_modal__rspack_import_4.OperatorModal, {
        ...props,
        initialValues: {
            countEmpty: false
        },
        children: ()=>/*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(react_jsx_dev_runtime__rspack_import_0.Fragment, {
                children: [
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                        label: t('data-hub.label'),
                        name: "label",
                        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Input, {}, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/element-counter/element-counter-config-modal.tsx",
                            lineNumber: 32,
                            columnNumber: 13
                        }, undefined)
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/element-counter/element-counter-config-modal.tsx",
                        lineNumber: 28,
                        columnNumber: 11
                    }, undefined),
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                        name: "countEmpty",
                        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Switch, {
                            labelRight: t('data-hub.operator.element-counter.count-empty')
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/element-counter/element-counter-config-modal.tsx",
                            lineNumber: 37,
                            columnNumber: 13
                        }, undefined)
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/element-counter/element-counter-config-modal.tsx",
                        lineNumber: 34,
                        columnNumber: 11
                    }, undefined)
                ]
            }, void 0, true)
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/element-counter/element-counter-config-modal.tsx",
        lineNumber: 22,
        columnNumber: 5
    }, undefined);
};
_s(ElementCounterConfigModal, "zlIdU9EjM2llFt74AbE2KsUJXyM=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation
    ];
});
_c = ElementCounterConfigModal;
var _c;
$RefreshReg$(_c, "ElementCounterConfigModal");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/operators/operators/if-empty/dynamic-type-operator-if-empty.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  DynamicTypeOperatorIfEmpty: () => (DynamicTypeOperatorIfEmpty)
});
/* import */ var _swc_helpers_ts_decorate__rspack_import_6 = __webpack_require__("./node_modules/tslib/tslib.es6.mjs");
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_1 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_1);
/* import */ var react__rspack_import_2 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_utils__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/utils");
/* import */ var _pimcore_studio_ui_bundle_utils__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_utils__rspack_import_3);
/* import */ var _dynamic_type_operator_abstract__rspack_import_4 = __webpack_require__("./js/src/modules/operators/dynamic-type-operator-abstract.ts");
/* import */ var _if_empty_config_modal__rspack_import_5 = __webpack_require__("./js/src/modules/operators/operators/if-empty/if-empty-config-modal.tsx");
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




class DynamicTypeOperatorIfEmpty extends _dynamic_type_operator_abstract__rspack_import_4.DynamicTypeOperatorAbstract {
    getIcon() {
        return {
            type: 'name',
            value: 'settings-brightness'
        };
    }
    getLabel(config, localizedName) {
        const label = config.attributes.label;
        return (0,_pimcore_studio_ui_bundle_utils__rspack_import_3.isNonEmptyString)(label) ? label : localizedName;
    }
    getGroup() {
        return 'other';
    }
    getConfigModal(props) {
        return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_if_empty_config_modal__rspack_import_5.IfEmptyConfigModal, {
            ...props
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/if-empty/dynamic-type-operator-if-empty.tsx",
            lineNumber: 40,
            columnNumber: 12
        }, this);
    }
    getMaxChildCount() {
        return 1;
    }
    constructor(...args){
        super(...args), this.id = 'IfEmpty';
    }
}
DynamicTypeOperatorIfEmpty = (0,_swc_helpers_ts_decorate__rspack_import_6.__decorate)([
    (0,_pimcore_studio_ui_bundle_app__rspack_import_1.injectable)()
], DynamicTypeOperatorIfEmpty);

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/operators/operators/if-empty/if-empty-config-modal.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  IfEmptyConfigModal: () => (IfEmptyConfigModal)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_3);
/* import */ var _components_operator_modal__rspack_import_4 = __webpack_require__("./js/src/modules/operators/components/operator-modal.tsx");
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



const IfEmptyConfigModal = (props)=>{
    _s();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation)();
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_components_operator_modal__rspack_import_4.OperatorModal, {
        ...props,
        children: ()=>/*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                label: t('data-hub.label'),
                name: "label",
                children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Input, {}, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/if-empty/if-empty-config-modal.tsx",
                    lineNumber: 28,
                    columnNumber: 11
                }, undefined)
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/if-empty/if-empty-config-modal.tsx",
                lineNumber: 24,
                columnNumber: 9
            }, undefined)
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/if-empty/if-empty-config-modal.tsx",
        lineNumber: 22,
        columnNumber: 5
    }, undefined);
};
_s(IfEmptyConfigModal, "zlIdU9EjM2llFt74AbE2KsUJXyM=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation
    ];
});
_c = IfEmptyConfigModal;
var _c;
$RefreshReg$(_c, "IfEmptyConfigModal");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/operators/operators/index.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  DynamicTypeOperatorAlias: () => (/* reexport safe */ _alias_dynamic_type_operator_alias__rspack_import_0.DynamicTypeOperatorAlias),
  DynamicTypeOperatorConcatenator: () => (/* reexport safe */ _concatenator_dynamic_type_operator_concatenator__rspack_import_1.DynamicTypeOperatorConcatenator),
  DynamicTypeOperatorDateFormatter: () => (/* reexport safe */ _date_formatter_dynamic_type_operator_date_formatter__rspack_import_2.DynamicTypeOperatorDateFormatter),
  DynamicTypeOperatorElementCounter: () => (/* reexport safe */ _element_counter_dynamic_type_operator_element_counter__rspack_import_3.DynamicTypeOperatorElementCounter),
  DynamicTypeOperatorIfEmpty: () => (/* reexport safe */ _if_empty_dynamic_type_operator_if_empty__rspack_import_10.DynamicTypeOperatorIfEmpty),
  DynamicTypeOperatorLocaleCollector: () => (/* reexport safe */ _locale_collector_dynamic_type_operator_locale_collector__rspack_import_11.DynamicTypeOperatorLocaleCollector),
  DynamicTypeOperatorLocaleSwitcher: () => (/* reexport safe */ _locale_switcher_dynamic_type_operator_locale_switcher__rspack_import_12.DynamicTypeOperatorLocaleSwitcher),
  DynamicTypeOperatorSubstring: () => (/* reexport safe */ _substring_dynamic_type_operator_substring__rspack_import_4.DynamicTypeOperatorSubstring),
  DynamicTypeOperatorText: () => (/* reexport safe */ _text_dynamic_type_operator_text__rspack_import_5.DynamicTypeOperatorText),
  DynamicTypeOperatorThumbnail: () => (/* reexport safe */ _thumbnail_dynamic_type_operator_thumbnail__rspack_import_6.DynamicTypeOperatorThumbnail),
  DynamicTypeOperatorThumbnailHtml: () => (/* reexport safe */ _thumbnail_html_dynamic_type_operator_thumbnail_html__rspack_import_7.DynamicTypeOperatorThumbnailHtml),
  DynamicTypeOperatorTranslateValue: () => (/* reexport safe */ _translate_value_dynamic_type_operator_translate_value__rspack_import_8.DynamicTypeOperatorTranslateValue),
  DynamicTypeOperatorTrimmer: () => (/* reexport safe */ _trimmer_dynamic_type_operator_trimmer__rspack_import_9.DynamicTypeOperatorTrimmer)
});
/* import */ var _alias_dynamic_type_operator_alias__rspack_import_0 = __webpack_require__("./js/src/modules/operators/operators/alias/dynamic-type-operator-alias.tsx");
/* import */ var _concatenator_dynamic_type_operator_concatenator__rspack_import_1 = __webpack_require__("./js/src/modules/operators/operators/concatenator/dynamic-type-operator-concatenator.tsx");
/* import */ var _date_formatter_dynamic_type_operator_date_formatter__rspack_import_2 = __webpack_require__("./js/src/modules/operators/operators/date-formatter/dynamic-type-operator-date-formatter.tsx");
/* import */ var _element_counter_dynamic_type_operator_element_counter__rspack_import_3 = __webpack_require__("./js/src/modules/operators/operators/element-counter/dynamic-type-operator-element-counter.tsx");
/* import */ var _substring_dynamic_type_operator_substring__rspack_import_4 = __webpack_require__("./js/src/modules/operators/operators/substring/dynamic-type-operator-substring.tsx");
/* import */ var _text_dynamic_type_operator_text__rspack_import_5 = __webpack_require__("./js/src/modules/operators/operators/text/dynamic-type-operator-text.tsx");
/* import */ var _thumbnail_dynamic_type_operator_thumbnail__rspack_import_6 = __webpack_require__("./js/src/modules/operators/operators/thumbnail/dynamic-type-operator-thumbnail.tsx");
/* import */ var _thumbnail_html_dynamic_type_operator_thumbnail_html__rspack_import_7 = __webpack_require__("./js/src/modules/operators/operators/thumbnail-html/dynamic-type-operator-thumbnail-html.tsx");
/* import */ var _translate_value_dynamic_type_operator_translate_value__rspack_import_8 = __webpack_require__("./js/src/modules/operators/operators/translate-value/dynamic-type-operator-translate-value.tsx");
/* import */ var _trimmer_dynamic_type_operator_trimmer__rspack_import_9 = __webpack_require__("./js/src/modules/operators/operators/trimmer/dynamic-type-operator-trimmer.tsx");
/* import */ var _if_empty_dynamic_type_operator_if_empty__rspack_import_10 = __webpack_require__("./js/src/modules/operators/operators/if-empty/dynamic-type-operator-if-empty.tsx");
/* import */ var _locale_collector_dynamic_type_operator_locale_collector__rspack_import_11 = __webpack_require__("./js/src/modules/operators/operators/locale-collector/dynamic-type-operator-locale-collector.tsx");
/* import */ var _locale_switcher_dynamic_type_operator_locale_switcher__rspack_import_12 = __webpack_require__("./js/src/modules/operators/operators/locale-switcher/dynamic-type-operator-locale-switcher.tsx");
/* provided dependency */ var $ReactRefreshRuntime$ = __webpack_require__("./node_modules/@rspack/plugin-react-refresh/client/reactRefresh.js");
/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */ // Query Operators










// Mutation Operators




function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/operators/operators/locale-collector/dynamic-type-operator-locale-collector.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  DynamicTypeOperatorLocaleCollector: () => (DynamicTypeOperatorLocaleCollector)
});
/* import */ var _swc_helpers_ts_decorate__rspack_import_6 = __webpack_require__("./node_modules/tslib/tslib.es6.mjs");
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_1 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_1);
/* import */ var react__rspack_import_2 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_utils__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/utils");
/* import */ var _pimcore_studio_ui_bundle_utils__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_utils__rspack_import_3);
/* import */ var _dynamic_type_operator_abstract__rspack_import_4 = __webpack_require__("./js/src/modules/operators/dynamic-type-operator-abstract.ts");
/* import */ var _locale_collector_config_modal__rspack_import_5 = __webpack_require__("./js/src/modules/operators/operators/locale-collector/locale-collector-config-modal.tsx");
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




class DynamicTypeOperatorLocaleCollector extends _dynamic_type_operator_abstract__rspack_import_4.DynamicTypeOperatorAbstract {
    getIcon() {
        return {
            type: 'name',
            value: 'locale-collector'
        };
    }
    getLabel(config, localizedName) {
        const label = config.attributes.label;
        return (0,_pimcore_studio_ui_bundle_utils__rspack_import_3.isNonEmptyString)(label) ? label : localizedName;
    }
    getGroup() {
        return 'other';
    }
    getConfigModal(props) {
        return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_locale_collector_config_modal__rspack_import_5.LocaleCollectorConfigModal, {
            ...props
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/locale-collector/dynamic-type-operator-locale-collector.tsx",
            lineNumber: 40,
            columnNumber: 12
        }, this);
    }
    getMaxChildCount() {
        return 1;
    }
    allowedChildTypes() {
        return [
            'booleanSelect',
            'checkbox',
            'country',
            'countrymultiselect',
            'date',
            'datetime',
            'email',
            'externalImage',
            'geopoint',
            'firstname',
            'gender',
            'input',
            'image',
            'language',
            'lastname',
            'newsletterActive',
            'manyToOneRelation',
            'multiselect',
            'newsletterConfirmed',
            'numeric',
            'select',
            'slider',
            'textarea',
            'time',
            'wysiwyg'
        ];
    }
    constructor(...args){
        super(...args), this.id = 'LocaleCollector';
    }
}
DynamicTypeOperatorLocaleCollector = (0,_swc_helpers_ts_decorate__rspack_import_6.__decorate)([
    (0,_pimcore_studio_ui_bundle_app__rspack_import_1.injectable)()
], DynamicTypeOperatorLocaleCollector);

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/operators/operators/locale-collector/locale-collector-config-modal.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  LocaleCollectorConfigModal: () => (LocaleCollectorConfigModal)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_3);
/* import */ var _components_operator_modal__rspack_import_4 = __webpack_require__("./js/src/modules/operators/components/operator-modal.tsx");
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



const LocaleCollectorConfigModal = (props)=>{
    _s();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation)();
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_components_operator_modal__rspack_import_4.OperatorModal, {
        ...props,
        children: ()=>/*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                label: t('data-hub.label'),
                name: "label",
                children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Input, {}, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/locale-collector/locale-collector-config-modal.tsx",
                    lineNumber: 28,
                    columnNumber: 11
                }, undefined)
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/locale-collector/locale-collector-config-modal.tsx",
                lineNumber: 24,
                columnNumber: 9
            }, undefined)
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/locale-collector/locale-collector-config-modal.tsx",
        lineNumber: 22,
        columnNumber: 5
    }, undefined);
};
_s(LocaleCollectorConfigModal, "zlIdU9EjM2llFt74AbE2KsUJXyM=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation
    ];
});
_c = LocaleCollectorConfigModal;
var _c;
$RefreshReg$(_c, "LocaleCollectorConfigModal");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/operators/operators/locale-switcher/dynamic-type-operator-locale-switcher.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  DynamicTypeOperatorLocaleSwitcher: () => (DynamicTypeOperatorLocaleSwitcher)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_utils__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/utils");
/* import */ var _pimcore_studio_ui_bundle_utils__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_utils__rspack_import_3);
/* import */ var _dynamic_type_operator_abstract__rspack_import_4 = __webpack_require__("./js/src/modules/operators/dynamic-type-operator-abstract.ts");
/* import */ var _locale_switcher_config_modal__rspack_import_5 = __webpack_require__("./js/src/modules/operators/operators/locale-switcher/locale-switcher-config-modal.tsx");
/* import */ var lodash__rspack_import_6 = __webpack_require__("./node_modules/lodash/lodash.js");
/* import */ var lodash__rspack_import_6_default = /*#__PURE__*/__webpack_require__.n(lodash__rspack_import_6);
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





class DynamicTypeOperatorLocaleSwitcher extends _dynamic_type_operator_abstract__rspack_import_4.DynamicTypeOperatorAbstract {
    getIcon() {
        return {
            type: 'name',
            value: 'locale-switcher'
        };
    }
    getLabel(config, localizedName) {
        const label = config.attributes.label;
        const locale = config.attributes.locale;
        const displayLabel = (0,_pimcore_studio_ui_bundle_utils__rspack_import_3.isNonEmptyString)(label) ? label : localizedName;
        if (!(0,lodash__rspack_import_6.isNil)(locale) && locale !== '') {
            return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(react_jsx_dev_runtime__rspack_import_0.Fragment, {
                children: [
                    displayLabel,
                    " ",
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Text, {
                        type: "secondary",
                        children: [
                            "(",
                            locale,
                            ")"
                        ]
                    }, void 0, true, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/locale-switcher/dynamic-type-operator-locale-switcher.tsx",
                        lineNumber: 42,
                        columnNumber: 26
                    }, this)
                ]
            }, void 0, true);
        }
        return displayLabel;
    }
    getConfigModal(props) {
        return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_locale_switcher_config_modal__rspack_import_5.LocaleSwitcherConfigModal, {
            ...props
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/locale-switcher/dynamic-type-operator-locale-switcher.tsx",
            lineNumber: 51,
            columnNumber: 12
        }, this);
    }
    getMaxChildCount() {
        return 1;
    }
    constructor(...args){
        super(...args), this.id = 'LocaleSwitcher';
    }
}

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/operators/operators/locale-switcher/locale-switcher-config-modal.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  LocaleSwitcherConfigModal: () => (LocaleSwitcherConfigModal)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_3);
/* import */ var _pimcore_studio_ui_bundle_modules_app__rspack_import_4 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/modules/app");
/* import */ var _pimcore_studio_ui_bundle_modules_app__rspack_import_4_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_modules_app__rspack_import_4);
/* import */ var _pimcore_studio_ui_bundle_modules_translations__rspack_import_5 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/modules/translations");
/* import */ var _pimcore_studio_ui_bundle_modules_translations__rspack_import_5_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_modules_translations__rspack_import_5);
/* import */ var _components_operator_modal__rspack_import_6 = __webpack_require__("./js/src/modules/operators/components/operator-modal.tsx");
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





const LocaleSwitcherConfigModal = (props)=>{
    _s();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation)();
    const settings = (0,_pimcore_studio_ui_bundle_modules_app__rspack_import_4.useSettings)();
    const { getDisplayName } = (0,_pimcore_studio_ui_bundle_modules_translations__rspack_import_5.useLanguageLookup)();
    const availableLanguages = (settings.validLanguages ?? []).map((locale)=>({
            value: locale,
            label: `${getDisplayName(locale)} [${locale}]`
        }));
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_components_operator_modal__rspack_import_6.OperatorModal, {
        ...props,
        initialValues: {
            locale: ''
        },
        size: "M",
        children: ()=>/*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(react_jsx_dev_runtime__rspack_import_0.Fragment, {
                children: [
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                        label: t('data-hub.label'),
                        name: "label",
                        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Input, {
                            maxLength: 255
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/locale-switcher/locale-switcher-config-modal.tsx",
                            lineNumber: 42,
                            columnNumber: 13
                        }, undefined)
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/locale-switcher/locale-switcher-config-modal.tsx",
                        lineNumber: 38,
                        columnNumber: 11
                    }, undefined),
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                        label: t('data-hub.locale'),
                        name: "locale",
                        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Select, {
                            options: availableLanguages,
                            placeholder: t('data-hub.locale.select')
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/locale-switcher/locale-switcher-config-modal.tsx",
                            lineNumber: 49,
                            columnNumber: 13
                        }, undefined)
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/locale-switcher/locale-switcher-config-modal.tsx",
                        lineNumber: 45,
                        columnNumber: 11
                    }, undefined)
                ]
            }, void 0, true)
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/locale-switcher/locale-switcher-config-modal.tsx",
        lineNumber: 31,
        columnNumber: 5
    }, undefined);
};
_s(LocaleSwitcherConfigModal, "MK/t/oRx5yGS8BsoKau8yfjCpIM=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation,
        _pimcore_studio_ui_bundle_modules_app__rspack_import_4.useSettings,
        _pimcore_studio_ui_bundle_modules_translations__rspack_import_5.useLanguageLookup
    ];
});
_c = LocaleSwitcherConfigModal;
var _c;
$RefreshReg$(_c, "LocaleSwitcherConfigModal");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/operators/operators/substring/dynamic-type-operator-substring.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  DynamicTypeOperatorSubstring: () => (DynamicTypeOperatorSubstring)
});
/* import */ var _swc_helpers_ts_decorate__rspack_import_8 = __webpack_require__("./node_modules/tslib/tslib.es6.mjs");
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_2);
/* import */ var lodash__rspack_import_3 = __webpack_require__("./node_modules/lodash/lodash.js");
/* import */ var lodash__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(lodash__rspack_import_3);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_4 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_4_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_4);
/* import */ var _pimcore_studio_ui_bundle_utils__rspack_import_5 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/utils");
/* import */ var _pimcore_studio_ui_bundle_utils__rspack_import_5_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_utils__rspack_import_5);
/* import */ var _dynamic_type_operator_abstract__rspack_import_6 = __webpack_require__("./js/src/modules/operators/dynamic-type-operator-abstract.ts");
/* import */ var _substring_config_modal__rspack_import_7 = __webpack_require__("./js/src/modules/operators/operators/substring/substring-config-modal.tsx");
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






class DynamicTypeOperatorSubstring extends _dynamic_type_operator_abstract__rspack_import_6.DynamicTypeOperatorAbstract {
    getIcon() {
        return {
            type: 'name',
            value: 'substring'
        };
    }
    getLabel(config, localizedName) {
        const label = config.attributes.label;
        const start = config.attributes.start;
        const length = config.attributes.length;
        const displayLabel = (0,_pimcore_studio_ui_bundle_utils__rspack_import_5.isNonEmptyString)(label) ? label : localizedName;
        if (!(0,lodash__rspack_import_3.isNil)(start) || !(0,lodash__rspack_import_3.isNil)(length)) {
            return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(react_jsx_dev_runtime__rspack_import_0.Fragment, {
                children: [
                    displayLabel,
                    " ",
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_4.Text, {
                        type: "secondary",
                        children: [
                            "(",
                            start,
                            ",",
                            length ?? '∞',
                            ")"
                        ]
                    }, void 0, true, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/substring/dynamic-type-operator-substring.tsx",
                        lineNumber: 44,
                        columnNumber: 26
                    }, this)
                ]
            }, void 0, true);
        }
        return displayLabel;
    }
    getConfigModal(props) {
        return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_substring_config_modal__rspack_import_7.SubstringConfigModal, {
            ...props
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/substring/dynamic-type-operator-substring.tsx",
            lineNumber: 53,
            columnNumber: 12
        }, this);
    }
    getGroup() {
        return 'transformer';
    }
    getSubGroup() {
        return 'string';
    }
    getMaxChildCount() {
        return 1;
    }
    constructor(...args){
        super(...args), this.id = 'Substring';
    }
}
DynamicTypeOperatorSubstring = (0,_swc_helpers_ts_decorate__rspack_import_8.__decorate)([
    (0,_pimcore_studio_ui_bundle_app__rspack_import_2.injectable)()
], DynamicTypeOperatorSubstring);

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/operators/operators/substring/substring-config-modal.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  SubstringConfigModal: () => (SubstringConfigModal)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_3);
/* import */ var _components_operator_modal__rspack_import_4 = __webpack_require__("./js/src/modules/operators/components/operator-modal.tsx");
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



const SubstringConfigModal = (props)=>{
    _s();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation)();
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_components_operator_modal__rspack_import_4.OperatorModal, {
        ...props,
        initialValues: {
            start: 0,
            ellipses: false
        },
        children: ()=>/*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(react_jsx_dev_runtime__rspack_import_0.Fragment, {
                children: [
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                        label: t('data-hub.label'),
                        name: "label",
                        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Input, {}, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/substring/substring-config-modal.tsx",
                            lineNumber: 32,
                            columnNumber: 13
                        }, undefined)
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/substring/substring-config-modal.tsx",
                        lineNumber: 28,
                        columnNumber: 11
                    }, undefined),
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                        label: t('data-hub.operator.start'),
                        name: "start",
                        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.InputNumber, {
                            min: 0,
                            style: {
                                width: '100%'
                            }
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/substring/substring-config-modal.tsx",
                            lineNumber: 38,
                            columnNumber: 13
                        }, undefined)
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/substring/substring-config-modal.tsx",
                        lineNumber: 34,
                        columnNumber: 11
                    }, undefined),
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                        label: t('data-hub.operator.length'),
                        name: "length",
                        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.InputNumber, {
                            min: 0,
                            style: {
                                width: '100%'
                            }
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/substring/substring-config-modal.tsx",
                            lineNumber: 47,
                            columnNumber: 13
                        }, undefined)
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/substring/substring-config-modal.tsx",
                        lineNumber: 43,
                        columnNumber: 11
                    }, undefined),
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                        name: "ellipses",
                        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Switch, {
                            labelRight: t('data-hub.operator.ellipses')
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/substring/substring-config-modal.tsx",
                            lineNumber: 55,
                            columnNumber: 13
                        }, undefined)
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/substring/substring-config-modal.tsx",
                        lineNumber: 52,
                        columnNumber: 11
                    }, undefined)
                ]
            }, void 0, true)
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/substring/substring-config-modal.tsx",
        lineNumber: 22,
        columnNumber: 5
    }, undefined);
};
_s(SubstringConfigModal, "zlIdU9EjM2llFt74AbE2KsUJXyM=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation
    ];
});
_c = SubstringConfigModal;
var _c;
$RefreshReg$(_c, "SubstringConfigModal");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/operators/operators/text/dynamic-type-operator-text.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  DynamicTypeOperatorText: () => (DynamicTypeOperatorText)
});
/* import */ var _swc_helpers_ts_decorate__rspack_import_6 = __webpack_require__("./node_modules/tslib/tslib.es6.mjs");
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_utils__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/utils");
/* import */ var _pimcore_studio_ui_bundle_utils__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_utils__rspack_import_3);
/* import */ var _dynamic_type_operator_abstract__rspack_import_4 = __webpack_require__("./js/src/modules/operators/dynamic-type-operator-abstract.ts");
/* import */ var _text_config_modal__rspack_import_5 = __webpack_require__("./js/src/modules/operators/operators/text/text-config-modal.tsx");
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




class DynamicTypeOperatorText extends _dynamic_type_operator_abstract__rspack_import_4.DynamicTypeOperatorAbstract {
    getIcon() {
        return {
            type: 'name',
            value: 'long-text'
        };
    }
    getLabel(config, localizedName) {
        const textValue = config.attributes.textValue;
        return (0,_pimcore_studio_ui_bundle_utils__rspack_import_3.isNonEmptyString)(textValue) ? textValue : localizedName;
    }
    getConfigModal(props) {
        return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_text_config_modal__rspack_import_5.TextConfigModal, {
            ...props
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/text/dynamic-type-operator-text.tsx",
            lineNumber: 37,
            columnNumber: 12
        }, this);
    }
    getGroup() {
        return 'formatter';
    }
    getSubGroup() {
        return 'string';
    }
    allowsChildren() {
        return false;
    }
    constructor(...args){
        super(...args), this.id = 'Text';
    }
}
DynamicTypeOperatorText = (0,_swc_helpers_ts_decorate__rspack_import_6.__decorate)([
    (0,_pimcore_studio_ui_bundle_app__rspack_import_2.injectable)()
], DynamicTypeOperatorText);

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/operators/operators/text/text-config-modal.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  TextConfigModal: () => (TextConfigModal)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_3);
/* import */ var _components_operator_modal__rspack_import_4 = __webpack_require__("./js/src/modules/operators/components/operator-modal.tsx");
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



const TextConfigModal = (props)=>{
    _s();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation)();
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_components_operator_modal__rspack_import_4.OperatorModal, {
        ...props,
        initialValues: {
            textValue: ''
        },
        children: ()=>/*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                label: t('data-hub.operator.text'),
                name: "textValue",
                children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Input, {}, void 0, false, {
                    fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/text/text-config-modal.tsx",
                    lineNumber: 31,
                    columnNumber: 11
                }, undefined)
            }, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/text/text-config-modal.tsx",
                lineNumber: 27,
                columnNumber: 9
            }, undefined)
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/text/text-config-modal.tsx",
        lineNumber: 22,
        columnNumber: 5
    }, undefined);
};
_s(TextConfigModal, "zlIdU9EjM2llFt74AbE2KsUJXyM=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation
    ];
});
_c = TextConfigModal;
var _c;
$RefreshReg$(_c, "TextConfigModal");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/operators/operators/thumbnail-html/dynamic-type-operator-thumbnail-html.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  DynamicTypeOperatorThumbnailHtml: () => (DynamicTypeOperatorThumbnailHtml)
});
/* import */ var _swc_helpers_ts_decorate__rspack_import_8 = __webpack_require__("./node_modules/tslib/tslib.es6.mjs");
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_1 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_1);
/* import */ var react__rspack_import_2 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_2);
/* import */ var lodash__rspack_import_3 = __webpack_require__("./node_modules/lodash/lodash.js");
/* import */ var lodash__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(lodash__rspack_import_3);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_4 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_4_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_4);
/* import */ var _pimcore_studio_ui_bundle_utils__rspack_import_5 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/utils");
/* import */ var _pimcore_studio_ui_bundle_utils__rspack_import_5_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_utils__rspack_import_5);
/* import */ var _dynamic_type_operator_abstract__rspack_import_6 = __webpack_require__("./js/src/modules/operators/dynamic-type-operator-abstract.ts");
/* import */ var _thumbnail_html_config_modal__rspack_import_7 = __webpack_require__("./js/src/modules/operators/operators/thumbnail-html/thumbnail-html-config-modal.tsx");
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






class DynamicTypeOperatorThumbnailHtml extends _dynamic_type_operator_abstract__rspack_import_6.DynamicTypeOperatorAbstract {
    getIcon() {
        return {
            type: 'name',
            value: 'thumbnail-html'
        };
    }
    getLabel(config, localizedName) {
        const label = config.attributes.label;
        const thumbnailHtmlConfig = config.attributes.thumbnailHtmlConfig;
        const displayLabel = (0,_pimcore_studio_ui_bundle_utils__rspack_import_5.isNonEmptyString)(label) ? label : localizedName;
        if (!(0,lodash__rspack_import_3.isNil)(thumbnailHtmlConfig) && thumbnailHtmlConfig !== '') {
            return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(react_jsx_dev_runtime__rspack_import_0.Fragment, {
                children: [
                    displayLabel,
                    " ",
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_4.Text, {
                        type: "secondary",
                        children: [
                            "(",
                            thumbnailHtmlConfig,
                            ")"
                        ]
                    }, void 0, true, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/thumbnail-html/dynamic-type-operator-thumbnail-html.tsx",
                        lineNumber: 41,
                        columnNumber: 26
                    }, this)
                ]
            }, void 0, true);
        }
        return displayLabel;
    }
    getGroup() {
        return 'transformer';
    }
    getSubGroup() {
        return 'other';
    }
    getConfigModal(props) {
        return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_thumbnail_html_config_modal__rspack_import_7.ThumbnailHtmlConfigModal, {
            ...props
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/thumbnail-html/dynamic-type-operator-thumbnail-html.tsx",
            lineNumber: 58,
            columnNumber: 12
        }, this);
    }
    getMaxChildCount() {
        return 1;
    }
    constructor(...args){
        super(...args), this.id = 'ThumbnailHtml';
    }
}
DynamicTypeOperatorThumbnailHtml = (0,_swc_helpers_ts_decorate__rspack_import_8.__decorate)([
    (0,_pimcore_studio_ui_bundle_app__rspack_import_1.injectable)()
], DynamicTypeOperatorThumbnailHtml);

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/operators/operators/thumbnail-html/thumbnail-html-config-modal.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  ThumbnailHtmlConfigModal: () => (ThumbnailHtmlConfigModal)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_3);
/* import */ var _components_operator_modal__rspack_import_4 = __webpack_require__("./js/src/modules/operators/components/operator-modal.tsx");
/* import */ var _components_thumbnail_select__rspack_import_5 = __webpack_require__("./js/src/modules/operators/components/thumbnail-select.tsx");
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




const ThumbnailHtmlConfigModal = (props)=>{
    _s();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation)();
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_components_operator_modal__rspack_import_4.OperatorModal, {
        ...props,
        children: ()=>/*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(react_jsx_dev_runtime__rspack_import_0.Fragment, {
                children: [
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                        label: t('data-hub.label'),
                        name: "label",
                        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Input, {}, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/thumbnail-html/thumbnail-html-config-modal.tsx",
                            lineNumber: 30,
                            columnNumber: 13
                        }, undefined)
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/thumbnail-html/thumbnail-html-config-modal.tsx",
                        lineNumber: 26,
                        columnNumber: 11
                    }, undefined),
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                        label: t('data-hub.thumbnail'),
                        name: "thumbnailHtmlConfig",
                        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_components_thumbnail_select__rspack_import_5.ThumbnailSelect, {
                            placeholder: t('data-hub.operator.thumbnail-html.thumbnail-name')
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/thumbnail-html/thumbnail-html-config-modal.tsx",
                            lineNumber: 37,
                            columnNumber: 13
                        }, undefined)
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/thumbnail-html/thumbnail-html-config-modal.tsx",
                        lineNumber: 33,
                        columnNumber: 11
                    }, undefined)
                ]
            }, void 0, true)
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/thumbnail-html/thumbnail-html-config-modal.tsx",
        lineNumber: 23,
        columnNumber: 5
    }, undefined);
};
_s(ThumbnailHtmlConfigModal, "zlIdU9EjM2llFt74AbE2KsUJXyM=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation
    ];
});
_c = ThumbnailHtmlConfigModal;
var _c;
$RefreshReg$(_c, "ThumbnailHtmlConfigModal");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/operators/operators/thumbnail/dynamic-type-operator-thumbnail.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  DynamicTypeOperatorThumbnail: () => (DynamicTypeOperatorThumbnail)
});
/* import */ var _swc_helpers_ts_decorate__rspack_import_8 = __webpack_require__("./node_modules/tslib/tslib.es6.mjs");
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_1 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_1);
/* import */ var react__rspack_import_2 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_2);
/* import */ var lodash__rspack_import_3 = __webpack_require__("./node_modules/lodash/lodash.js");
/* import */ var lodash__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(lodash__rspack_import_3);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_4 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_4_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_4);
/* import */ var _pimcore_studio_ui_bundle_utils__rspack_import_5 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/utils");
/* import */ var _pimcore_studio_ui_bundle_utils__rspack_import_5_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_utils__rspack_import_5);
/* import */ var _dynamic_type_operator_abstract__rspack_import_6 = __webpack_require__("./js/src/modules/operators/dynamic-type-operator-abstract.ts");
/* import */ var _thumbnail_config_modal__rspack_import_7 = __webpack_require__("./js/src/modules/operators/operators/thumbnail/thumbnail-config-modal.tsx");
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






class DynamicTypeOperatorThumbnail extends _dynamic_type_operator_abstract__rspack_import_6.DynamicTypeOperatorAbstract {
    getIcon() {
        return {
            type: 'name',
            value: 'image-thumbnail'
        };
    }
    getLabel(config, localizedName) {
        const label = config.attributes.label;
        const thumbnailConfig = config.attributes.thumbnailConfig;
        const displayLabel = (0,_pimcore_studio_ui_bundle_utils__rspack_import_5.isNonEmptyString)(label) ? label : localizedName;
        if (!(0,lodash__rspack_import_3.isNil)(thumbnailConfig) && thumbnailConfig !== '') {
            return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(react_jsx_dev_runtime__rspack_import_0.Fragment, {
                children: [
                    displayLabel,
                    " ",
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_4.Text, {
                        type: "secondary",
                        children: [
                            "(",
                            thumbnailConfig,
                            ")"
                        ]
                    }, void 0, true, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/thumbnail/dynamic-type-operator-thumbnail.tsx",
                        lineNumber: 41,
                        columnNumber: 26
                    }, this)
                ]
            }, void 0, true);
        }
        return displayLabel;
    }
    getGroup() {
        return 'transformer';
    }
    getSubGroup() {
        return 'other';
    }
    getConfigModal(props) {
        return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_thumbnail_config_modal__rspack_import_7.ThumbnailConfigModal, {
            ...props
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/thumbnail/dynamic-type-operator-thumbnail.tsx",
            lineNumber: 58,
            columnNumber: 12
        }, this);
    }
    getMaxChildCount() {
        return 1;
    }
    constructor(...args){
        super(...args), this.id = 'Thumbnail';
    }
}
DynamicTypeOperatorThumbnail = (0,_swc_helpers_ts_decorate__rspack_import_8.__decorate)([
    (0,_pimcore_studio_ui_bundle_app__rspack_import_1.injectable)()
], DynamicTypeOperatorThumbnail);

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/operators/operators/thumbnail/thumbnail-config-modal.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  ThumbnailConfigModal: () => (ThumbnailConfigModal)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_3);
/* import */ var _components_operator_modal__rspack_import_4 = __webpack_require__("./js/src/modules/operators/components/operator-modal.tsx");
/* import */ var _components_thumbnail_select__rspack_import_5 = __webpack_require__("./js/src/modules/operators/components/thumbnail-select.tsx");
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




const ThumbnailConfigModal = (props)=>{
    _s();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation)();
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_components_operator_modal__rspack_import_4.OperatorModal, {
        ...props,
        children: ()=>/*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(react_jsx_dev_runtime__rspack_import_0.Fragment, {
                children: [
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                        label: t('data-hub.label'),
                        name: "label",
                        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Input, {}, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/thumbnail/thumbnail-config-modal.tsx",
                            lineNumber: 30,
                            columnNumber: 13
                        }, undefined)
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/thumbnail/thumbnail-config-modal.tsx",
                        lineNumber: 26,
                        columnNumber: 11
                    }, undefined),
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                        label: t('data-hub.thumbnail'),
                        name: "thumbnailConfig",
                        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_components_thumbnail_select__rspack_import_5.ThumbnailSelect, {
                            placeholder: t('data-hub.operator.thumbnail.thumbnail-name')
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/thumbnail/thumbnail-config-modal.tsx",
                            lineNumber: 37,
                            columnNumber: 13
                        }, undefined)
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/thumbnail/thumbnail-config-modal.tsx",
                        lineNumber: 33,
                        columnNumber: 11
                    }, undefined)
                ]
            }, void 0, true)
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/thumbnail/thumbnail-config-modal.tsx",
        lineNumber: 23,
        columnNumber: 5
    }, undefined);
};
_s(ThumbnailConfigModal, "zlIdU9EjM2llFt74AbE2KsUJXyM=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation
    ];
});
_c = ThumbnailConfigModal;
var _c;
$RefreshReg$(_c, "ThumbnailConfigModal");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/operators/operators/translate-value/dynamic-type-operator-translate-value.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  DynamicTypeOperatorTranslateValue: () => (DynamicTypeOperatorTranslateValue)
});
/* import */ var _swc_helpers_ts_decorate__rspack_import_8 = __webpack_require__("./node_modules/tslib/tslib.es6.mjs");
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_1 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_1);
/* import */ var react__rspack_import_2 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_2);
/* import */ var lodash__rspack_import_3 = __webpack_require__("./node_modules/lodash/lodash.js");
/* import */ var lodash__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(lodash__rspack_import_3);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_4 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_4_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_4);
/* import */ var _pimcore_studio_ui_bundle_utils__rspack_import_5 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/utils");
/* import */ var _pimcore_studio_ui_bundle_utils__rspack_import_5_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_utils__rspack_import_5);
/* import */ var _dynamic_type_operator_abstract__rspack_import_6 = __webpack_require__("./js/src/modules/operators/dynamic-type-operator-abstract.ts");
/* import */ var _translate_value_config_modal__rspack_import_7 = __webpack_require__("./js/src/modules/operators/operators/translate-value/translate-value-config-modal.tsx");
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






class DynamicTypeOperatorTranslateValue extends _dynamic_type_operator_abstract__rspack_import_6.DynamicTypeOperatorAbstract {
    getIcon() {
        return {
            type: 'name',
            value: 'translate'
        };
    }
    getLabel(config, localizedName) {
        const label = config.attributes.label;
        const prefix = config.attributes.prefix;
        const displayLabel = (0,_pimcore_studio_ui_bundle_utils__rspack_import_5.isNonEmptyString)(label) ? label : localizedName;
        if (!(0,lodash__rspack_import_3.isNil)(prefix) && prefix !== '') {
            return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(react_jsx_dev_runtime__rspack_import_0.Fragment, {
                children: [
                    displayLabel,
                    " ",
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_4.Text, {
                        type: "secondary",
                        children: [
                            "(",
                            prefix,
                            ")"
                        ]
                    }, void 0, true, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/translate-value/dynamic-type-operator-translate-value.tsx",
                        lineNumber: 41,
                        columnNumber: 26
                    }, this)
                ]
            }, void 0, true);
        }
        return displayLabel;
    }
    getGroup() {
        return 'transformer';
    }
    getSubGroup() {
        return 'string';
    }
    getConfigModal(props) {
        return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_translate_value_config_modal__rspack_import_7.TranslateValueConfigModal, {
            ...props
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/translate-value/dynamic-type-operator-translate-value.tsx",
            lineNumber: 58,
            columnNumber: 12
        }, this);
    }
    getMaxChildCount() {
        return 1;
    }
    constructor(...args){
        super(...args), this.id = 'TranslateValue';
    }
}
DynamicTypeOperatorTranslateValue = (0,_swc_helpers_ts_decorate__rspack_import_8.__decorate)([
    (0,_pimcore_studio_ui_bundle_app__rspack_import_1.injectable)()
], DynamicTypeOperatorTranslateValue);

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/operators/operators/translate-value/translate-value-config-modal.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  TranslateValueConfigModal: () => (TranslateValueConfigModal)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_3);
/* import */ var _components_operator_modal__rspack_import_4 = __webpack_require__("./js/src/modules/operators/components/operator-modal.tsx");
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



const TranslateValueConfigModal = (props)=>{
    _s();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation)();
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_components_operator_modal__rspack_import_4.OperatorModal, {
        ...props,
        children: ()=>/*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(react_jsx_dev_runtime__rspack_import_0.Fragment, {
                children: [
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                        label: t('data-hub.label'),
                        name: "label",
                        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Input, {}, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/translate-value/translate-value-config-modal.tsx",
                            lineNumber: 29,
                            columnNumber: 13
                        }, undefined)
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/translate-value/translate-value-config-modal.tsx",
                        lineNumber: 25,
                        columnNumber: 11
                    }, undefined),
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                        label: t('data-hub.prefix'),
                        name: "prefix",
                        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Input, {}, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/translate-value/translate-value-config-modal.tsx",
                            lineNumber: 36,
                            columnNumber: 13
                        }, undefined)
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/translate-value/translate-value-config-modal.tsx",
                        lineNumber: 32,
                        columnNumber: 11
                    }, undefined)
                ]
            }, void 0, true)
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/translate-value/translate-value-config-modal.tsx",
        lineNumber: 22,
        columnNumber: 5
    }, undefined);
};
_s(TranslateValueConfigModal, "zlIdU9EjM2llFt74AbE2KsUJXyM=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation
    ];
});
_c = TranslateValueConfigModal;
var _c;
$RefreshReg$(_c, "TranslateValueConfigModal");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/operators/operators/trimmer/dynamic-type-operator-trimmer.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  DynamicTypeOperatorTrimmer: () => (DynamicTypeOperatorTrimmer)
});
/* import */ var _swc_helpers_ts_decorate__rspack_import_8 = __webpack_require__("./node_modules/tslib/tslib.es6.mjs");
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_2);
/* import */ var lodash__rspack_import_3 = __webpack_require__("./node_modules/lodash/lodash.js");
/* import */ var lodash__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(lodash__rspack_import_3);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_4 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_4_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_4);
/* import */ var _pimcore_studio_ui_bundle_utils__rspack_import_5 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/utils");
/* import */ var _pimcore_studio_ui_bundle_utils__rspack_import_5_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_utils__rspack_import_5);
/* import */ var _dynamic_type_operator_abstract__rspack_import_6 = __webpack_require__("./js/src/modules/operators/dynamic-type-operator-abstract.ts");
/* import */ var _trimmer_config_modal__rspack_import_7 = __webpack_require__("./js/src/modules/operators/operators/trimmer/trimmer-config-modal.tsx");
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






class DynamicTypeOperatorTrimmer extends _dynamic_type_operator_abstract__rspack_import_6.DynamicTypeOperatorAbstract {
    getIcon() {
        return {
            type: 'name',
            value: 'trimmer'
        };
    }
    getLabel(config, localizedName) {
        const label = config.attributes.label;
        const trim = config.attributes.trim;
        const displayLabel = (0,_pimcore_studio_ui_bundle_utils__rspack_import_5.isNonEmptyString)(label) ? label : localizedName;
        if (!(0,lodash__rspack_import_3.isNil)(trim) && trim > 0) {
            var _s = $RefreshSig$();
            const TrimLabel = ()=>{
                _s();
                const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_2.useTranslation)();
                const modes = [
                    'disabled',
                    'left',
                    'right',
                    'both'
                ];
                const mode = modes[trim];
                if ((0,lodash__rspack_import_3.isNil)(mode)) {
                    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(react_jsx_dev_runtime__rspack_import_0.Fragment, {
                        children: displayLabel
                    }, void 0, false);
                }
                const translatedMode = t(`data-hub.operator.trim.${mode}`);
                return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(react_jsx_dev_runtime__rspack_import_0.Fragment, {
                    children: [
                        displayLabel,
                        " ",
                        /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_4.Text, {
                            type: "secondary",
                            children: [
                                "(",
                                translatedMode,
                                ")"
                            ]
                        }, void 0, true, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/trimmer/dynamic-type-operator-trimmer.tsx",
                            lineNumber: 49,
                            columnNumber: 33
                        }, this)
                    ]
                }, void 0, true);
            };
            _s(TrimLabel, "zlIdU9EjM2llFt74AbE2KsUJXyM=", false, function() {
                return [
                    _pimcore_studio_ui_bundle_app__rspack_import_2.useTranslation
                ];
            });
            return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(TrimLabel, {}, void 0, false, {
                fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/trimmer/dynamic-type-operator-trimmer.tsx",
                lineNumber: 51,
                columnNumber: 14
            }, this);
        }
        return displayLabel;
    }
    getConfigModal(props) {
        return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_trimmer_config_modal__rspack_import_7.TrimmerConfigModal, {
            ...props
        }, void 0, false, {
            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/trimmer/dynamic-type-operator-trimmer.tsx",
            lineNumber: 58,
            columnNumber: 12
        }, this);
    }
    getGroup() {
        return 'transformer';
    }
    getSubGroup() {
        return 'string';
    }
    getMaxChildCount() {
        return 1;
    }
    constructor(...args){
        super(...args), this.id = 'Trimmer';
    }
}
DynamicTypeOperatorTrimmer = (0,_swc_helpers_ts_decorate__rspack_import_8.__decorate)([
    (0,_pimcore_studio_ui_bundle_app__rspack_import_2.injectable)()
], DynamicTypeOperatorTrimmer);

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/operators/operators/trimmer/trimmer-config-modal.tsx"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  TrimmerConfigModal: () => (TrimmerConfigModal)
});
/* import */ var react_jsx_dev_runtime__rspack_import_0 = __webpack_require__("./node_modules/react/jsx-dev-runtime.js");
/* import */ var react__rspack_import_1 = __webpack_require__("webpack/sharing/consume/default/react/react");
/* import */ var react__rspack_import_1_default = /*#__PURE__*/__webpack_require__.n(react__rspack_import_1);
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/components");
/* import */ var _pimcore_studio_ui_bundle_components__rspack_import_2_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_components__rspack_import_2);
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3 = __webpack_require__("webpack/container/remote/@pimcore/studio-ui-bundle/app");
/* import */ var _pimcore_studio_ui_bundle_app__rspack_import_3_default = /*#__PURE__*/__webpack_require__.n(_pimcore_studio_ui_bundle_app__rspack_import_3);
/* import */ var _components_operator_modal__rspack_import_4 = __webpack_require__("./js/src/modules/operators/components/operator-modal.tsx");
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



const TrimmerConfigModal = (props)=>{
    _s();
    const { t } = (0,_pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation)();
    return /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_components_operator_modal__rspack_import_4.OperatorModal, {
        ...props,
        initialValues: {
            trim: 3
        },
        children: ()=>/*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(react_jsx_dev_runtime__rspack_import_0.Fragment, {
                children: [
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                        label: t('data-hub.label'),
                        name: "label",
                        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Input, {}, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/trimmer/trimmer-config-modal.tsx",
                            lineNumber: 32,
                            columnNumber: 13
                        }, undefined)
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/trimmer/trimmer-config-modal.tsx",
                        lineNumber: 28,
                        columnNumber: 11
                    }, undefined),
                    /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Form.Item, {
                        label: t('data-hub.operator.trim'),
                        name: "trim",
                        children: /*#__PURE__*/ (0,react_jsx_dev_runtime__rspack_import_0.jsxDEV)(_pimcore_studio_ui_bundle_components__rspack_import_2.Select, {
                            options: [
                                {
                                    label: t('data-hub.operator.trim.disabled'),
                                    value: 0
                                },
                                {
                                    label: t('data-hub.operator.trim.left'),
                                    value: 1
                                },
                                {
                                    label: t('data-hub.operator.trim.right'),
                                    value: 2
                                },
                                {
                                    label: t('data-hub.operator.trim.both'),
                                    value: 3
                                }
                            ]
                        }, void 0, false, {
                            fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/trimmer/trimmer-config-modal.tsx",
                            lineNumber: 38,
                            columnNumber: 13
                        }, undefined)
                    }, void 0, false, {
                        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/trimmer/trimmer-config-modal.tsx",
                        lineNumber: 34,
                        columnNumber: 11
                    }, undefined)
                ]
            }, void 0, true)
    }, void 0, false, {
        fileName: "/var/www/dev-bundles/pimcore/data-hub/assets/studio/js/src/modules/operators/operators/trimmer/trimmer-config-modal.tsx",
        lineNumber: 22,
        columnNumber: 5
    }, undefined);
};
_s(TrimmerConfigModal, "zlIdU9EjM2llFt74AbE2KsUJXyM=", false, function() {
    return [
        _pimcore_studio_ui_bundle_app__rspack_import_3.useTranslation
    ];
});
_c = TrimmerConfigModal;
var _c;
$RefreshReg$(_c, "TrimmerConfigModal");

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/modules/operators/thumbnails-api-slice.gen.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  addTagTypes: () => (addTagTypes),
  api: () => (injectedRtkApi),
  useBundleDataHubThumbnailsCollectionQuery: () => (useBundleDataHubThumbnailsCollectionQuery)
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
            bundleDataHubThumbnailsCollection: build.query({
                query: ()=>({
                        url: `/pimcore-studio/api/bundle/data-hub/thumbnails`
                    }),
                providesTags: [
                    "Bundle Data Hub"
                ]
            })
        }),
    overrideExisting: false
});

const { useBundleDataHubThumbnailsCollectionQuery } = injectedRtkApi;

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},
"./js/src/plugins.ts"(module, __webpack_exports__, __webpack_require__) {
__webpack_require__.r(__webpack_exports__);
__webpack_require__.d(__webpack_exports__, {
  DataHubPlugin: () => (DataHubPlugin)
});
/* import */ var _modules_config_index__rspack_import_0 = __webpack_require__("./js/src/modules/config/index.tsx");
/* import */ var _modules_graphql_index__rspack_import_1 = __webpack_require__("./js/src/modules/graphql/index.tsx");
/* import */ var _config_service_ids__rspack_import_2 = __webpack_require__("./js/src/config/service-ids.ts");
/* import */ var _modules_config_dynamic_types_dynamic_type_data_hub_adapter_registry__rspack_import_3 = __webpack_require__("./js/src/modules/config/dynamic-types/dynamic-type-data-hub-adapter-registry.ts");
/* import */ var _modules_config_dynamic_types_adapters_dynamic_type_data_hub_adapter_graphql__rspack_import_4 = __webpack_require__("./js/src/modules/config/dynamic-types/adapters/dynamic-type-data-hub-adapter-graphql.tsx");
/* import */ var _modules_operators_dynamic_type_operator_registry__rspack_import_5 = __webpack_require__("./js/src/modules/operators/dynamic-type-operator-registry.ts");
/* import */ var _modules_operators_operators__rspack_import_6 = __webpack_require__("./js/src/modules/operators/operators/index.ts");
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






if (true) {
    module.hot.accept();
}
const DataHubPlugin = {
    name: 'data-hub-plugin',
    // Register and overwrite services here
    onInit: (param)=>{
        let { container } = param;
        container.bind(String(_config_service_ids__rspack_import_2.bundleServiceIds["DataHub/DynamicTypes/Adapter/Registry"])).to(_modules_config_dynamic_types_dynamic_type_data_hub_adapter_registry__rspack_import_3.DynamicTypeDataHubAdapterRegistry).inSingletonScope();
        container.bind(String(_config_service_ids__rspack_import_2.bundleServiceIds["DataHub/DynamicTypes/Adapter/GraphQL"])).to(_modules_config_dynamic_types_adapters_dynamic_type_data_hub_adapter_graphql__rspack_import_4.DynamicTypeDataHubAdapterGraphQL).inSingletonScope();
        container.bind(String(_config_service_ids__rspack_import_2.bundleServiceIds["DataHub/DynamicTypes/Operator/GraphQL/QueryRegistry"])).to(_modules_operators_dynamic_type_operator_registry__rspack_import_5.DynamicTypeOperatorRegistry).inSingletonScope();
        container.bind(String(_config_service_ids__rspack_import_2.bundleServiceIds["DataHub/DynamicTypes/Operator/GraphQL/MutationRegistry"])).to(_modules_operators_dynamic_type_operator_registry__rspack_import_5.DynamicTypeOperatorRegistry).inSingletonScope();
        // Query Operators
        container.bind(String(_config_service_ids__rspack_import_2.bundleServiceIds["DataHub/DynamicTypes/Operator/Alias"])).to(_modules_operators_operators__rspack_import_6.DynamicTypeOperatorAlias).inSingletonScope();
        container.bind(String(_config_service_ids__rspack_import_2.bundleServiceIds["DataHub/DynamicTypes/Operator/Concatenator"])).to(_modules_operators_operators__rspack_import_6.DynamicTypeOperatorConcatenator).inSingletonScope();
        container.bind(String(_config_service_ids__rspack_import_2.bundleServiceIds["DataHub/DynamicTypes/Operator/DateFormatter"])).to(_modules_operators_operators__rspack_import_6.DynamicTypeOperatorDateFormatter).inSingletonScope();
        container.bind(String(_config_service_ids__rspack_import_2.bundleServiceIds["DataHub/DynamicTypes/Operator/ElementCounter"])).to(_modules_operators_operators__rspack_import_6.DynamicTypeOperatorElementCounter).inSingletonScope();
        container.bind(String(_config_service_ids__rspack_import_2.bundleServiceIds["DataHub/DynamicTypes/Operator/Substring"])).to(_modules_operators_operators__rspack_import_6.DynamicTypeOperatorSubstring).inSingletonScope();
        container.bind(String(_config_service_ids__rspack_import_2.bundleServiceIds["DataHub/DynamicTypes/Operator/Text"])).to(_modules_operators_operators__rspack_import_6.DynamicTypeOperatorText).inSingletonScope();
        container.bind(String(_config_service_ids__rspack_import_2.bundleServiceIds["DataHub/DynamicTypes/Operator/Thumbnail"])).to(_modules_operators_operators__rspack_import_6.DynamicTypeOperatorThumbnail).inSingletonScope();
        container.bind(String(_config_service_ids__rspack_import_2.bundleServiceIds["DataHub/DynamicTypes/Operator/ThumbnailHtml"])).to(_modules_operators_operators__rspack_import_6.DynamicTypeOperatorThumbnailHtml).inSingletonScope();
        container.bind(String(_config_service_ids__rspack_import_2.bundleServiceIds["DataHub/DynamicTypes/Operator/TranslateValue"])).to(_modules_operators_operators__rspack_import_6.DynamicTypeOperatorTranslateValue).inSingletonScope();
        container.bind(String(_config_service_ids__rspack_import_2.bundleServiceIds["DataHub/DynamicTypes/Operator/Trimmer"])).to(_modules_operators_operators__rspack_import_6.DynamicTypeOperatorTrimmer).inSingletonScope();
        // Mutation Operators
        container.bind(String(_config_service_ids__rspack_import_2.bundleServiceIds["DataHub/DynamicTypes/Operator/IfEmpty"])).to(_modules_operators_operators__rspack_import_6.DynamicTypeOperatorIfEmpty).inSingletonScope();
        container.bind(String(_config_service_ids__rspack_import_2.bundleServiceIds["DataHub/DynamicTypes/Operator/LocaleCollector"])).to(_modules_operators_operators__rspack_import_6.DynamicTypeOperatorLocaleCollector).inSingletonScope();
        container.bind(String(_config_service_ids__rspack_import_2.bundleServiceIds["DataHub/DynamicTypes/Operator/LocaleSwitcher"])).to(_modules_operators_operators__rspack_import_6.DynamicTypeOperatorLocaleSwitcher).inSingletonScope();
    },
    // register modules here
    onStartup: (param)=>{
        let { moduleSystem } = param;
        moduleSystem.registerModule(_modules_config_index__rspack_import_0.DataHubModule);
        moduleSystem.registerModule(_modules_graphql_index__rspack_import_1.GraphQLModule);
    }
};

function $RefreshSig$() { return $ReactRefreshRuntime$.createSignatureFunctionForTransform() }
function $RefreshReg$(type, id) { $ReactRefreshRuntime$.register(type, module.id + "_" + id) }
Promise.resolve().then(() => { $ReactRefreshRuntime$.refresh(module.id, module.hot) });


},

}]);
//# sourceMappingURL=__federation_expose_plugins.js.map