import template from './provider-settings.html.twig';

const { Component } = Shopware;

Component.register('heptacom-admin-open-auth-provider-jira-settings', {
    template,

    props: {
        item: {
            required: true,
        },
    },
});
