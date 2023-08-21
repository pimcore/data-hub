# Upgrade Notes

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
