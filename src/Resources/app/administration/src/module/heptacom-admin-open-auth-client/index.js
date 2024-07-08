const { Component, Module } = Shopware;

import './acl';

Component.register('heptacom-admin-open-auth-role-assignment-action-config', () => import ('./component/role-assignment-action-config'));
Component.register('heptacom-admin-open-auth-client-rule-container', () => import ('./component/rule-container'));
Component.register('heptacom-admin-open-auth-client-rule-item', () => import ('./component/rule-item'));

Component.register('heptacom-admin-open-auth-client-create-page', () => import ('./page/create'));
Component.register('heptacom-admin-open-auth-client-edit-page', () => import ('./page/edit'));
Component.register('heptacom-admin-open-auth-client-listing-page', () => import ('./page/listing'));

Module.register('heptacom-admin-open-auth-client', {
    type: 'plugin',
    name: 'heptacom-admin-open-auth-client.module.name',
    title: 'heptacom-admin-open-auth-client.module.title',
    description: 'heptacom-admin-open-auth-client.module.description',
    color: '#FFC2A2',
    icon: 'regular-sign-in',

    routes: {
        create: {
            component: 'heptacom-admin-open-auth-client-create-page',
            path: 'create',
            meta: {
                parentPath: 'heptacom.admin.open.auth.client.settings',
                privilege: 'heptacom_admin_open_auth_client.creator'
            }
        },
        edit: {
            component: 'heptacom-admin-open-auth-client-edit-page',
            path: 'edit/:id',
            meta: {
                parentPath: 'heptacom.admin.open.auth.client.settings',
                privilege: 'heptacom_admin_open_auth_client.editor'
            },
            props: {
                default(route) {
                    return {
                        clientId: route.params.id
                    };
                }
            }
        },
        settings: {
            component: 'heptacom-admin-open-auth-client-listing-page',
            path: 'settings',
            meta: {
                parentPath: 'sw.settings.index',
                privilege: 'heptacom_admin_open_auth_client.viewer'
            }
        }
    },

    settingsItem: [{
        to: 'heptacom.admin.open.auth.client.settings',
        group: 'system',
        icon: 'regular-sign-in',
        privilege: 'heptacom_admin_open_auth_client.viewer'
    }]
});
