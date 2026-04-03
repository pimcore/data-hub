# Upgrade Notes

## Upgrade to 2026.1.0

### PHP & Symfony Version Requirements
- Added support for `PHP` `8.5`.
- Removed support for `PHP` `8.3` and Symfony `v6`.
- Minimum required Symfony version is now `^7.2` (dev: `^7.4` for `symfony/runtime`).

### Removed ExtJS / Admin Classic UI
- Removed all ExtJS-based frontend JavaScript files (`src/Resources/public/js/`), including the full configuration UI, field dialog, adapters, workspace editors, query and mutation operators.
- Removed `ConfigController` (previously provided the classic admin config API at `/pimcore-datahub-webservices/explorer/{clientname}` and related actions).
- Removed `GraphQLExplorerController` (previously served the ExtJS-based GraphQL Explorer at `/pimcore-datahub-webservices/explorer/{clientname}`).
- Removed `AdminListener` (no longer needed without classic admin integration).
- Removed `StudioRouteLoader` — Studio routes are now registered directly via `routing.yml` using attribute-based routing.
- Removed `ExportService` and `ImportService` (classic admin import/export, not related to Studio).
- `PimcoreDataHubBundle` no longer implements `PimcoreBundleAdminClassicInterface` or `DependentBundleInterface` and no longer uses `BundleAdminClassicTrait`.
- Removed dependency on `pimcore/admin-ui-classic-bundle` and `pimcore/compatibility-bridge-v10` from `composer.json`.

### Routing Changes
- The Studio routing entry has been renamed from `pimcore_statistics_explorer_studio` to `pimcore_data_hub_studio`. Update any references in custom routing configurations accordingly.
- The separate `studio_routing.yaml` file has been removed; Studio routes are now defined directly in `routing.yml`.

### Studio Integration
- Added full Studio UI integration for GraphQL configurations (browse, create, edit, clone, delete, import, export).
- New Studio REST API endpoints added under `/studio/api/datahub/config/...` (collection, add, get, update, clone, delete, import, export).
- New Studio REST API endpoints added: GraphQL Explorer URL (`/studio/api/datahub/graphql/explorer`), thumbnails, and user permissions.
- New interfaces introduced for Studio services:
  - `ConfigurationServiceInterface` (`src/Service/Studio/ConfigurationServiceInterface.php`)
  - `ConfigurationHydratorInterface` (`src/Hydrator/ConfigurationHydratorInterface.php`)
  - `ConfigurationDehydratorInterface` (`src/Hydrator/ConfigurationDehydratorInterface.php`)
  - `ConfigurationDetailHydratorInterface` (`src/Hydrator/ConfigurationDetailHydratorInterface.php`)
  - `GraphQLExplorerServiceInterface` (`src/Service/Studio/GraphQLExplorerServiceInterface.php`)
  - `ThumbnailHydratorInterface` (`src/Hydrator/ThumbnailHydratorInterface.php`)
  - `ThumbnailServiceInterface` (`src/Service/Studio/ThumbnailServiceInterface.php`)
  - `PermissionUserHydratorInterface` (`src/Hydrator/PermissionUserHydratorInterface.php`)
  - `UserServiceInterface` (`src/Service/Studio/UserServiceInterface.php`)
- New pre-response events dispatched before Studio API responses:
  - `ConfigurationEvent`, `ConfigurationDetailEvent`, `PermissionUserEvent`, `ThumbnailEvent`

### New GraphQL Events
- Added `ListingEvents::PRE_BUILD` event dispatched before building GraphQL query listings. Implement a listener on this event to modify listing arguments before the query is executed.

### Return Type and PHPDoc Changes
- `PimcoreDataHubBundle::getInstaller()` return type changed from `?InstallerInterface` to `InstallerInterface` (non-nullable).
- `ElementPropertyType::resolveType()` return type changed from `ObjectType|string|callable|Deferred|null` to `ObjectType|callable|null` — `string` and `Deferred` removed from union.
- `HotspotMetadataType::resolveType()` return type changed from `ObjectType|string|callable|Deferred|null` to `ObjectType|callable|null` — `string` and `Deferred` removed from union.
- `DocumentType::resolveType()` return type changed from `callable|Deferred|ObjectType|null|string` to `callable|ObjectType|null`.
- `AbstractOperator::$children` property PHPDoc type changed from `ConfigElementInterface[]` to `array` — `ConfigElementInterface` from `Pimcore\DataObject\GridColumnConfig` is no longer referenced.
- `AbstractOperator::getChildren()` return PHPDoc changed from `ConfigElementInterface[]` to `array`.
- `Service::buildOperator()` parameter PHPDoc changed from `ConfigElementInterface` to `array`.
- `ReverseManyToManyObjects::resolve()` return PHPDoc changed from `array|null` to `array`.
- `ElementMetadataKeyValuePairInputType::getInstance()` return PHPDoc changed from `ElementMetadataKeyValuePairInputType|null` to `ElementMetadataKeyValuePairInputType`.
- `ClassificationstoreFeatureQueryTypeGenerator\Date::getFieldType()` return PHPDoc changed from `DateType|null` to `DateType`.
- `Thumbnail::getLabeledValue()` and `ThumbnailHtml::getLabeledValue()` return PHPDoc changed from `\stdClass|null` to `\stdClass`.
- `DateFormatter::format()` return PHPDoc changed from `Carbon|int|string` to `int|string`.
- `Link::resolveValue()`, `Link::resolveHref()`, `Link::resolveTarget()`, `Link::resolveLinkValue()` return PHPDoc changed from `string|null` / `null` to `mixed`.

### Dependency Updates
- Removed Doctrine `enum` type mapping — the custom `enum: string` mapping is no longer required in Doctrine configuration.

## 2.2.0
- [MutationType] Added system field `key` as an optional argument for data object mutation

## 2.0.0

- [General] Marked several classes as internal and/or final.
- [General] Added timezone indication for date and datetime fields in the GraphQL schema, including creation and modification date.
Therefore, the type of both date fields in the GraphQL schema have changed from `int` (timestamp) to `string`.
- [Operator] Removed merge operator
- [Command] Removed RebuildDefinitionsCommand (`datahub:graphql:rebuild-definitions`), use RebuildWorkspacesCommand (`datahub:graphql:rebuild-workspaces`) instead.
- [Config] Removed DatahubConfigLocator class
- [Config] Removed support for legacy config file (`datahub-configurations.php`)
- [Config] Removed `getConfigModificationDate` from config dao.
- [Config] Removed sql query config field
- [QueryType] `args['path']` will no longer be supported, use `args['fullpath']` instead.
- [DataType] Generated type names now include the type itself, see https://github.com/pimcore/data-hub/issues/879
- [QueryType] Change type name for `table` definition, now the name also includes the type, see https://github.com/pimcore/data-hub/pull/883
- [DataType] Added numeric-prefix `col` for `table` columns to allow numbers as column names

## 1.8.0
- [General] Dropped support of `pimcore/pimcore` v10. Bumped minimum requirement of `pimcore/pimcore` to `^11.2`
- [General] Replaced Request::get() with explicit input sources.
- [Schema Definition] Added a button to add all definitions except system columns, along with multi drag-and-drop functionality using CTRL+ click to select and drag multiple fields to the configuration.
- [Schema Definition] A category selector with CTRL + click functionality was added, and the issue with deleting the entire configuration has been resolved.

## 1.7.0
- [GraphQL] Deprecated SQL Condition.
- [GraphQL] Added the possibility to disable deprecated SQL Condition.

## 1.6.0
- [General] If you want to use Datahub 1.6 and Pimcore 11, please make sure to require the `pimcore/admin-ui-classic-bundle`.
- [Config Location] Change default directory for configurations to `var/config/data_hub`
- When using Pimcore 11, configurations from `datahub-configurations.php` are no longer used. To migrate those configurations
to the settings store, use the provided `datahub:configuration:migrate-legacy-config` command.
- Added the ability to import and export each type of Datahub configuration.
Be sure to include the `supported_types` configuration in any custom implementation to use the import functionality!
- Added possibility to disable the introspection for GraphQL via configuration tree.

## 1.5.0
- When "Skip Permission Check" is active in a GraphQL configuration, the "Workspaces" settings are also skipped 
- It is possible to add more than one API key per configuration now. Therefore, the API keys get stored as an array
in the configuration. Configurations from previous versions are still supported, but they get converted to the new format
as soon as they get saved.
- Added "Translation Listing" and "Translation" to the Generic Types
