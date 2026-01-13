# Studio integration

## Studio API Endpoints

The Data Hub Bundle provides a RESTful API for the Pimcore Studio interface, enabling management of Data Hub configurations, GraphQL explorer access, and user/thumbnail management. All endpoints are prefixed with `/pimcore-studio/api/data-hub` and require appropriate permissions.

### Configuration Management

#### List Configurations
**Endpoint:** `GET /config`
**Operation ID:** `bundle_data_hub_config_collection`
**Permission:** `PLUGIN_DATA_HUB_CONFIG`

Lists all available Data Hub configurations. This endpoint returns a collection of configurations that can be managed and used for data integration.

**Response:** Collection of `Configuration` objects

---

#### Get Configuration
**Endpoint:** `GET /config/{name}`
**Operation ID:** `bundle_data_hub_config_get`
**Permission:** `PLUGIN_DATA_HUB_CONFIG`

Retrieves detailed information about a specific Data Hub configuration by name.

**Path Parameter:**
- `name` (string): The name of the configuration to retrieve

**Response:** `ConfigurationDetail` object

**Example:**
```
GET /config/assets
```

---

#### Add Configuration
**Endpoint:** `POST /config/add`
**Operation ID:** `bundle_data_hub_config_add`
**Permission:** `PLUGIN_DATA_HUB_CONFIG`

Creates a new Data Hub configuration with the specified name, type, and optional path.

**Query Parameters:**
- `name` (string, required): The name of the configuration (e.g., "assets")
- `type` (string, required): Type of the adapter (e.g., "graphql")
- `path` (string, optional): Configuration path (default: "")

**Response:** HTTP 200 OK (empty response)

**Example:**
```
POST /config/add?name=assets&type=graphql&path=
```

---

#### Clone Configuration
**Endpoint:** `POST /config/clone`
**Operation ID:** `bundle_data_hub_config_clone`
**Permission:** `PLUGIN_DATA_HUB_CONFIG`

Creates a copy of an existing Data Hub configuration with a new name.

**Query Parameters:**
- `name` (string, required): The name of the new configuration (e.g., "assets_copy")
- `originalName` (string, required): The name of the configuration to clone (e.g., "assets")

**Response:** JSON object with `clonedConfigurationName` (HTTP 201 Created)

**Example:**
```
POST /config/clone?name=assets_copy&originalName=assets
```

---

#### Delete Configuration
**Endpoint:** `DELETE /config/delete/{name}`
**Operation ID:** `bundle_data_hub_config_delete`
**Permission:** `PLUGIN_DATA_HUB_CONFIG`

Deletes a Data Hub configuration by name.

**Path Parameter:**
- `name` (string): The name of the configuration to delete

**Response:** HTTP 200 OK (empty response)

**Example:**
```
DELETE /config/delete/assets
```

---

#### Export Configuration
**Endpoint:** `GET /config/{name}/export`
**Operation ID:** `bundle_data_hub_config_export`
**Permission:** `PLUGIN_DATA_HUB_CONFIG`

Exports a Data Hub configuration as a JSON file for backup or transfer purposes.

**Path Parameter:**
- `name` (string): The name of the configuration to export

**Response:** JSON file download with Content-Disposition header

**Example:**
```
GET /config/assets/export
```

---

#### Import Configuration
**Endpoint:** `POST /config/import`
**Operation ID:** `bundle_data_hub_config_import`
**Permission:** `PLUGIN_DATA_HUB_CONFIG`

Imports a Data Hub configuration from a JSON file.

**Request Body:** Multipart form data with `file` field containing the JSON configuration file

**Response:** JSON object with import result (HTTP 201 Created)

**Possible Errors:**
- 400 Bad Request: No file uploaded or failed to read file contents

**Example:**
```
POST /config/import
Content-Type: multipart/form-data
```

---

### GraphQL Explorer

#### Get GraphQL Explorer
**Endpoint:** `GET /graphql/explorer/{clientname}`
**Operation ID:** `bundle_data_hub_graphql_explorer`
**Permission:** None specified

Returns the GraphQL Explorer HTML interface for a specific client configuration, allowing interactive query building and testing.

**Path Parameter:**
- `clientname` (string): The name of the GraphQL client configuration

**Query Parameters:** All query and request parameters are passed to the explorer

**Response:** HTML content (GraphQL Explorer interface)

**Example:**
```
GET /graphql/explorer/assets
```

---

#### Get GraphQL Explorer URL
**Endpoint:** `GET /graphql/explorer-url/{name}`
**Operation ID:** `bundle_data_hub_graphql_explorer_url`
**Permission:** `PLUGIN_DATA_HUB_CONFIG`

Retrieves the URL to access the GraphQL Explorer for a specific configuration.

**Path Parameter:**
- `name` (string): The name of the configuration

**Response:** JSON object with `explorerUrl` field

**Example:**
```
GET /graphql/explorer-url/assets
```

---

### Users

#### List Users
**Endpoint:** `GET /users`
**Operation ID:** `bundle_data_hub_users_collection`
**Permission:** `PLUGIN_DATA_HUB_CONFIG`

Retrieves a list of users or roles that can be assigned permissions in Data Hub configurations.

**Query Parameters:**
- `type` (string, optional): Filter by user type - "user" or "role" (default: "user")

**Response:** Collection of `PermissionUser` objects

**Example:**
```
GET /users?type=user
GET /users?type=role
```

---

### Thumbnails

#### List Thumbnails
**Endpoint:** `GET /thumbnails`
**Operation ID:** `bundle_data_hub_thumbnails_collection`
**Permission:** `thumbnails`

Retrieves a list of available image thumbnail configurations that can be used in Data Hub queries.

**Response:** Collection of `Thumbnail` objects

**Example:**
```
GET /thumbnails
```

---

## Permissions

The Studio API uses the following permission constants:

- **`PLUGIN_DATA_HUB_CONFIG`**: Required for managing Data Hub configurations, viewing users, and accessing GraphQL explorer URLs
- **`thumbnails`**: Required for listing thumbnail configurations

## Common Response Codes

- **200 OK**: Successful request
- **201 Created**: Resource created successfully (configuration add, clone, import)
- **400 Bad Request**: Invalid request data (import failures)
- **401 Unauthorized**: Missing or invalid authentication
- **403 Forbidden**: User lacks required permissions
- **404 Not Found**: Resource not found

## API Tags

All endpoints are tagged with `DataHub` for OpenAPI documentation grouping.

## Configuration Types

The Data Hub Bundle supports the following adapter types:
- **graphql**: GraphQL API adapter for querying and mutating Pimcore data

## Usage Examples

### Creating and Managing Configurations

1. **Create a new GraphQL configuration:**
   ```
   POST /config/add?name=products&type=graphql
   ```

2. **Get configuration details:**
   ```
   GET /config/products
   ```

3. **Clone an existing configuration:**
   ```
   POST /config/clone?name=products_dev&originalName=products
   ```

4. **Export for backup:**
   ```
   GET /config/products/export
   ```

5. **Delete configuration:**
   ```
   DELETE /config/delete/products_dev
   ```

### Working with GraphQL

1. **Get explorer URL:**
   ```
   GET /graphql/explorer-url/products
   ```

2. **Access GraphQL Explorer:**
   ```
   GET /graphql/explorer/products
   ```

### Managing Permissions

1. **List available users:**
   ```
   GET /users?type=user
   ```

2. **List available roles:**
   ```
   GET /users?type=role
   ```
