import './page/listing';
import snippets from './snippets';

const { Module } = Shopware;

Module.register('heptacom-admin-open-auth-client', {
    type: 'plugin',
    name: 'heptacom-admin-open-auth-client.module.name',
    title: 'heptacom-admin-open-auth-client.module.title',
    description: 'heptacom-admin-open-auth-client.module.description',
    color: '#FFC2A2',
    icon: 'default-action-log-in',
    snippets,

    routes: {
        settings: {
            component: 'heptacom-admin-open-auth-client-listing-page',
            path: 'settings'
        },
    },

    settingsItem: [{
        to: 'heptacom.admin.open.auth.client.settings',
        group: 'system',
        icon: 'default-action-log-in',
    }]
});
