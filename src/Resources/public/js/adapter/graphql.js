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

pimcore.registerNS("pimcore.plugin.datahub.adapter.graphql");
pimcore.plugin.datahub.adapter.graphql = Class.create(pimcore.plugin.datahub.adapter.abstract, {
    openConfiguration: function (id) {
        if(this.checkIfPanelExists(id)) {
            return;
        }

        Ext.Ajax.request({
            url: "/admin/pimcoredatahub/config/get",
            params: {
                name: id
            },
            success: function (response) {
                // check again here to prevent double click problem
                if(this.checkIfPanelExists(id)) {
                    return;
                }

                let data = Ext.decode(response.responseText);

                pimcore.plugin.datahub.graphql = pimcore.plugin.datahub.graphql || {};
                pimcore.plugin.datahub.graphql.supportedQueryDataTypes = data.supportedGraphQLQueryDataTypes;
                pimcore.plugin.datahub.graphql.supportedMutationDataTypes = data.supportedGraphQLMutationDataTypes;

                let fieldPanel = new pimcore.plugin.datahub.configuration.graphql.configItem(data, this);
                pimcore.layout.refresh();
            }.bind(this)
        });
    }
});
