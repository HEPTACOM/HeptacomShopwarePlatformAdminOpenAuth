import template from './provider-settings.html.twig';

export default {
    template,

    props: {
        item: {
            required: true,
        },
    },

    data() {
        return {
            availableProperties: [
                'firstName',
                'lastName',
                'email',
                'timezone',
                'locale'
            ],
        };
    },

    watch: {
        item(newValue) {
            if (!newValue.config.attributeMapping) {
                newValue.config.attributeMapping = {};
            }
        }
    },
};
