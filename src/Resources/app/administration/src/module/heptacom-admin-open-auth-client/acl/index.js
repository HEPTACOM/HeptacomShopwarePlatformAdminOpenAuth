Shopware.Service('privileges')
    .addPrivilegeMappingEntry({
        category: 'permissions',
        parent: 'settings',
        key: 'heptacom_admin_open_auth_client',
        roles: {
            viewer: {
                privileges: [
                    'heptacom_admin_open_auth_client:read',
                    'heptacom_admin_open_auth_user_key:read',
                ],
                dependencies: []
            },
            editor: {
                privileges: [
                    'heptacom_admin_open_auth_client:update',
                ],
                dependencies: [
                    'heptacom_admin_open_auth_client.viewer',
                ]
            },
            creator: {
                privileges: [
                    'heptacom_admin_open_auth_client:create',
                ],
                dependencies: [
                    'heptacom_admin_open_auth_client.editor',
                ]
            },
            deleter: {
                privileges: [
                    'heptacom_admin_open_auth_client:delete',
                ],
                dependencies: [
                    'heptacom_admin_open_auth_client.viewer',
                ]
            },
        }
    });
