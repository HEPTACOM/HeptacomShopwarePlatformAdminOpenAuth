const { Component } = Shopware;

Component.extend('heptacom-admin-open-auth-url-field', 'sw-url-field', () => import('./components/heptacom-admin-open-auth-url-field'));
Component.register('heptacom-admin-open-auth-user-confirm-login', () => import('./components/heptacom-admin-open-auth-user-confirm-login'));
