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


pimcore.registerNS("pimcore.plugin.datahub.helper");
pimcore.plugin.datahub.helper = {

    isAllowed: function (key, data) {
        let user = pimcore.globalmanager.get("user");
        let userIds = array_merge([user.id], user.roles);

        //everything is allowed for admins
        if (user.admin || user.isAllowed('plugin_datahub_admin')) {
            return true;
        }

        let adapter = data.adapter ?? data.general.type;
        let adapterAllowed = user.isAllowed("plugin_datahub_adapter_" + adapter);
        if (key === "create" || adapterAllowed) {
            return adapterAllowed;
        }

        if (typeof data.permissions === "undefined") {
            return false;
        }

        let types = ["user", "role"];
        for (let tkey in types) {
            let type = types[tkey];
            for (let pkey in data.permissions[type]) {
                if (data.permissions[type].hasOwnProperty(pkey)) {
                    let permission = data.permissions[type][pkey];
                    if (in_array(permission.id, userIds)) {
                        return permission[key];
                    }
                }
            }
        }

        return false;
    }
};
