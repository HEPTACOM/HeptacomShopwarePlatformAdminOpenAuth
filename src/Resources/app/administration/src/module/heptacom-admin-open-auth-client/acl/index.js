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
                    'heptacom_admin_open_auth_client_rule:read',
                    'heptacom_admin_open_auth_client_rule_condition:read',
                ],
                dependencies: []
            },
            editor: {
                privileges: [
                    'heptacom_admin_open_auth_client:update',
                    'heptacom_admin_open_auth_client_rule:create',
                    'heptacom_admin_open_auth_client_rule:update',
                    'heptacom_admin_open_auth_client_rule:delete',
                    'heptacom_admin_open_auth_client_rule_condition:create',
                    'heptacom_admin_open_auth_client_rule_condition:update',
                    'heptacom_admin_open_auth_client_rule_condition:delete',
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
