import template from './role-assignment-action-config.html.twig';

export default {
    template,

    inject: [
        'repositoryFactory',
    ],

    props: {
        client: {
            required: true,
            type: Object,
        },
        rule: {
            required: true,
            type: Object,
        },
    },

    computed: {
        aclRoleRepository () {
            return this.repositoryFactory.create('acl_role');
        },
    },
}
