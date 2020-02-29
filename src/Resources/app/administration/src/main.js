import './extension/sw-profile-index';
import './module/heptacom-admin-open-auth-client';
import globalSnippets from './snippets';
import extensionSnippets from './extension/snippets';

const { Locale } = Shopware;

[globalSnippets, extensionSnippets]
    .map(Object.entries)
    .flat()
    .forEach(([lang, snippets]) => Locale.extend(lang, snippets));
