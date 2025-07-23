import template from './sw-profile-index-general.html.twig';

const { Component } = Shopware;

Component.override('sw-profile-index-general', {
    template,

    inject: [
        'systemConfigApiService',
        'loginService',
    ],

    data() {
        return {
            denyPasswordLogin: false,
            userId: null,
        }
    },

    created() {
        this.systemConfigApiService.getValues('KskHeptacomAdminOpenAuth.config').then((response) => {
            this.denyPasswordLogin = response['KskHeptacomAdminOpenAuth.config.denyPasswordLogin'];
        });

        this.userId = Shopware.State.get('session').currentUser.id;
    },
});
