import './module/heptacom-admin-open-auth-client';
import globalSnippets from './snippets';

const { Locale } = Shopware;

Object.entries(globalSnippets).forEach(([lang, snippets]) => {
    Locale.extend(lang, snippets);
});
