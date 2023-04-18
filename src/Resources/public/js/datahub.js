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

pimcore.registerNS("pimcore.plugin.datahub");


pimcore.plugin.datahub = Class.create({
    getClassName: function () {
        return "pimcore.plugin.datahub";
    },

    initialize: function () {
        // if the new event exists, we use this
        if (pimcore.events.preMenuBuild) {
            document.addEventListener(pimcore.events.preMenuBuild, this.preMenuBuild.bind(this));
        } else {
            document.addEventListener(pimcore.events.pimcoreReady, this.pimcoreReady.bind(this));
        }


        document.addEventListener("pimcore.perspectiveEditor.permissions.structure.load", (e) => {
            if (e.detail.context === 'toolbar') {
                e.detail.structure['datahub'] = {};
            }
        });

        document.addEventListener("pimcore.perspectiveEditor.permissions.load", (e) => {
            const context = e.detail.context;
            const menu = e.detail.menu;
            const permissions = e.detail.permissions;

            if (context === 'toolbar' && menu === 'datahub') {
                if (permissions[context][menu] === undefined) {
                    permissions[context][menu] = [];
                }
                if (permissions[context][menu].indexOf('hidden') === -1) {
                    permissions[context][menu].push('hidden');
                }
            }
        });
    },

    preMenuBuild: function (e) {
        const perspectiveCfg = pimcore.globalmanager.get("perspective");

        if (perspectiveCfg.inToolbar("datahub") === false) {
            return
        }

        const user = pimcore.globalmanager.get("user");
        if (user.admin || user.isAllowed("plugin_datahub_config")) {
            let menu = e.detail.menu;

            menu.datahub = {
                label: t('plugin_pimcore_datahub_toolbar'),
                iconCls: 'pimcore_main_nav_icon_mind_map',
                priority: 55,
                shadow: false,
                handler: this.openDataHub,
                cls: "pimcore_navigation_flyout",
                noSubmenus: true
            };
        }
    },

    openDataHub: function(e) {
        try {
            pimcore.globalmanager.get("plugin_pimcore_datahub_config").activate();
        } catch (e) {
            pimcore.globalmanager.add("plugin_pimcore_datahub_config", new pimcore.plugin.datahub.config());
        }
    },

    pimcoreReady: function(e) {
        const perspectiveCfg = pimcore.globalmanager.get("perspective");

        if (perspectiveCfg.inToolbar("datahub") === false) {
            return
        }

        const user = pimcore.globalmanager.get("user");
        if (user.admin || user.isAllowed("plugin_datahub_config")) {

            let navEl = Ext.get('pimcore_menu_search').insertSibling('<li id="pimcore_menu_datahub" data-menu-tooltip="'
                + t('plugin_pimcore_datahub_toolbar') +
                '" class="pimcore_menu_item pimcore_menu_needs_children"><img alt="datahub" src="/bundles/pimcoreadmin/img/flat-white-icons/mind_map.svg"></li>', 'before');

            navEl.on('mousedown', function () {
                try {
                    pimcore.globalmanager.get("plugin_pimcore_datahub_config").activate();
                } catch (e) {
                    pimcore.globalmanager.add("plugin_pimcore_datahub_config", new pimcore.plugin.datahub.config());
                }
            });

            pimcore.helpers.initMenuTooltips();
        }
    }
});

var datahubPlugin = new pimcore.plugin.datahub();