import template from './sw-verify-user-modal.html.twig';

const { Component } = Shopware;

Component.override('sw-verify-user-modal', {
    template,

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
