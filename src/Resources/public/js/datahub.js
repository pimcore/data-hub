/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

pimcore.registerNS("pimcore.bundle.datahub");
pimcore.bundle.datahub = Class.create(pimcore.bundle.admin, {
    getClassName: function () {
        return "pimcore.bundle.datahub";
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },

    pimcoreReady: function (params, broker) {


        var user = pimcore.globalmanager.get("user");

        if (user.admin || user.isAllowed("plugin_datahub_config")) {
            var menu = pimcore.globalmanager.get("layout_toolbar").settingsMenu;
            menu.add({
                text: t("plugin_pimcore_datahub_toolbar"),
                iconCls: "plugin_pimcore_datahub_nav_icon",
                handler: function () {
                    try {
                        pimcore.globalmanager.get("plugin_pimcore_datahub_config").activate();
                    }
                    catch (e) {
                        pimcore.globalmanager.add("plugin_pimcore_datahub_config", new pimcore.bundle.datahub.config());
                    }
                }
            });
        }
    }
});

var dtaPlugin = new pimcore.bundle.datahub();

