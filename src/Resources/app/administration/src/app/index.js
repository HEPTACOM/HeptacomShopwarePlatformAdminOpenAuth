const { Component } = Shopware;

Component.register('heptacom-admin-open-auth-scope-field', () => import('./components/heptacom-admin-open-auth-scope-field'));
Component.extend('heptacom-admin-open-auth-url-field', 'sw-url-field', () => import('./components/heptacom-admin-open-auth-url-field'));
Component.register('heptacom-admin-open-auth-user-confirm-login', () => import('./components/heptacom-admin-open-auth-user-confirm-login'));
