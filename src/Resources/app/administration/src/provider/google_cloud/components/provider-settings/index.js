import template from './provider-settings.html.twig';

export default {
    template,

    props: {
        item: {
            required: true,
        },
    },

    watch: {
        item(newValue) {
            if (!newValue.config.scopes) {
                newValue.config.scopes = [];
            }
        }
    }
};
