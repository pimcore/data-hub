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

pimcore.plugin.datahub = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return "pimcore.plugin.datahub";
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },

    pimcoreReady: function (params, broker) {
        var user = pimcore.globalmanager.get("user");
        if (user.admin || user.isAllowed("plugin_datahub_config")) {

            let navEl = Ext.get('pimcore_menu_search').insertSibling('<li id="pimcore_menu_datahub" data-menu-tooltip="'
                + t('plugin_pimcore_datahub_toolbar') +
                '" class="pimcore_menu_item pimcore_menu_needs_children"><img src="/bundles/pimcoreadmin/img/flat-white-icons/mind_map.svg"></li>', 'before');

            navEl.on('mousedown', function () {
                try {
                    pimcore.globalmanager.get("plugin_pimcore_datahub_config").activate();
                }
                catch (e) {
                    pimcore.globalmanager.add("plugin_pimcore_datahub_config", new pimcore.plugin.datahub.config());
                }
            });

            pimcore.helpers.initMenuTooltips();
        }
    }
});

var dtaPlugin = new pimcore.plugin.datahub();

