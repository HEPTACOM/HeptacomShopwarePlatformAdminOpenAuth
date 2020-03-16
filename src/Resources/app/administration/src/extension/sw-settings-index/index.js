import template from './sw-settings-index.html.twig';

const { Component } = Shopware;

/**
 * @deprecated Remove on compatibility to shopware >= 6.2
 */
Component.override('sw-profile-index', {
    template,
});
