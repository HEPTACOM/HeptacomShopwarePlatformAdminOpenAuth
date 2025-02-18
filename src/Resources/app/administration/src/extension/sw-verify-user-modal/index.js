import template from './sw-verify-user-modal.html.twig';

const { Component } = Shopware;

Component.override('sw-verify-user-modal', {
    template,

    inject: ['systemConfigApiService'],

    data() {
        return {
            denyPasswordLogin: false,
        };
    },

    created() {
        this.systemConfigApiService.getValues('KskHeptacomAdminOpenAuth.config').then((response) => {
            this.denyPasswordLogin = response['KskHeptacomAdminOpenAuth.config.denyPasswordLogin'];
        });
    },

    methods: {
        onCloseConfirmPasswordModal() {
            this.$refs.heptacomAdminOpenAuthUserConfirmLogin.abortAuthFlow();

            return this.$super('onCloseConfirmPasswordModal');
        },

        heptacomAdminOpenAuthUserConfirm(context) {
            this.$emit('verified', context);
        }
    }
});
