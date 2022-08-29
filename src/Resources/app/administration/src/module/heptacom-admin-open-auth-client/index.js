import './acl';
import './page/create';
import './page/edit';
import './page/listing';

const { Module } = Shopware;

Module.register('heptacom-admin-open-auth-client', {
    type: 'plugin',
    name: 'heptacom-admin-open-auth-client.module.name',
    title: 'heptacom-admin-open-auth-client.module.title',
    description: 'heptacom-admin-open-auth-client.module.description',
    color: '#FFC2A2',
    icon: 'default-action-log-in',

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
        icon: 'default-action-log-in',
        privilege: 'heptacom_admin_open_auth_client.viewer'
    }]
});
