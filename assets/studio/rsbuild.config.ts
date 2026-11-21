import { defineConfig } from '@rsbuild/core'
import { pluginReact } from '@rsbuild/plugin-react'
import { pluginModuleFederation } from '@module-federation/rsbuild-plugin';
import { pluginGenerateEntrypoints, pluginWriteBuildId } from '@pimcore/studio-ui-bundle/rsbuild/plugins';
import { createDynamicRemote } from '@pimcore/studio-ui-bundle/rsbuild/utils';
import path from 'node:path'
import fs from 'node:fs';
import { getBuildGroupId } from '@pimcore/studio-ui-bundle/bundler/build-id';
import packages from './package.json'

const buildId = getBuildGroupId(__dirname);
const buildRoot = path.resolve(__dirname, '..', '..', 'src', 'Resources', 'public', 'studio', 'build');
const buildPath = path.resolve(buildRoot, buildId);

// This bundle has a single build target (no SDK/app pair sharing a build-id group), so it's
// safe to remove any other build dirs here. It's also necessary: studio-package-build picks
// the lexicographically greatest .build-id it finds on disk, not the one just built, so a
// stale dir left over from an earlier source state could otherwise get archived instead of
// this build.
if (fs.existsSync(buildRoot)) {
  for (const file of fs.readdirSync(buildRoot)) {
    if (file !== buildId) {
      fs.rmSync(path.resolve(buildRoot, file), { recursive: true, force: true });
    }
  }
}

if (!fs.existsSync(buildPath)) {
  fs.mkdirSync(buildPath, { recursive: true });
}

let nodeEnv = process.env.NODE_ENV;
let env: 'development' | 'production' = 'production';

const isDevServer = nodeEnv === 'dev-server';
if (nodeEnv !== env) {
  env = 'development';
}

export default defineConfig({
  mode: env,
  server: {
    port: 3033,
  },
  dev: {
    ...(isDevServer ? {} : {assetPrefix: '/bundles/pimcoredatahub/studio/build/' + buildId}),
    client: {
      host: 'localhost',
      port: 3033,
      protocol: 'ws'
    }
  },
  source: {
    entry: {
      main: './js/src/main.ts'
    },
    decorators: {
      version: 'legacy'
    }
  },
  output: {
    manifest: true,
    assetPrefix: '/bundles/pimcoredatahub/studio/build/' + buildId,
    distPath: {
      root: buildPath
    },
  },
  tools: {
    bundlerChain: (chain, { env }) => {
      chain.output.uniqueName('pimcore_datahub_bundle');
    },
  },
  plugins: [
    pluginWriteBuildId({ buildId }),
    pluginGenerateEntrypoints({
      alternativePluginExportPath: '/plugins'
    }),
    pluginReact(),
    pluginModuleFederation({
      name: 'pimcore_datahub_bundle',
      filename: 'static/js/remoteEntry.js',
      exposes: {
        './plugins': './js/src/plugins.ts',
        '.': './js/src/sdk/index.ts',
      },
      dts: false,
      remotes: {
        '@pimcore/studio-ui-bundle': createDynamicRemote('pimcore_studio_ui_bundle'),
        // optional: with Change Control absent the remote resolves to an empty container, and
        // only the review surface - which nothing mounts without it - reads from this one
        '@pimcore/change-control-bundle': createDynamicRemote('pimcore_change_control_bundle', true),
      },
      shared: {
        ...packages.dependencies,
        react: {
          singleton: true,
          eager: true,
          requiredVersion: false,
        },
        'react-dom': {
          singleton: true,
          eager: true,
          requiredVersion: false,
        },
        'inversify': {
          // singleton: true,
          eager: true,
          version: '6.1.x',
          requiredVersion: '6.1.x',
        },
        'yaml': {
          singleton: true,
          eager: true,
          requiredVersion: packages.dependencies.yaml,
        },
      },
    })
  ]
})
