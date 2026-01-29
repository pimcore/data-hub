/**
* Pimcore
*
* This source file is available under two different licenses:
* - Pimcore Open Core License (POCL)
* - Pimcore Commercial License (PCL)
* Full copyright and license information is available in
* LICENSE.md which is distributed with this source code.
*
*  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
*  @license    https://github.com/pimcore/studio-ui-bundle/blob/1.x/LICENSE.md POCL and PCL
*/

import { type ConfigFile } from '@rtk-query/codegen-openapi'
import { EndpointMatcherFunction } from '@rtk-query/codegen-openapi/lib/types';

const pathMatcher = (pattern: RegExp): EndpointMatcherFunction => {
  return (name, definition) => {
    return pattern.test(definition.path);
  }
}

const config: ConfigFile = {
  schemaFile: './docs.jsonopenapi.json',
  apiFile: '@pimcore/studio-ui-bundle/api',
  apiImport: 'api',
  outputFiles: {
    '../../js/src/modules/config/config-api-slice.gen.ts': {
      filterEndpoints: pathMatcher(/bundle\/data-hub\/config/i)
    },
    '../../js/src/modules/graphql/graphql-api-slice.gen.ts': {
      filterEndpoints: pathMatcher(/bundle\/data-hub\/graphql/i)
    },
  },
  exportName: 'api',
  hooks: true,
  tag: true
}

export default config
