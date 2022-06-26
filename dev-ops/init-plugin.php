<?php

declare(strict_types=1);

function replaceInFiles(array $files, array $replacements) {
    foreach ($files as $file) {
        $contents = \file_get_contents($file);
        $replaced = \str_replace(\array_keys($replacements), \array_values($replacements), $contents);
        \file_put_contents($file, $replaced);
    }
}

function removeSection(array $files) {
    foreach ($files as $file) {
        $contents = \file_get_contents($file);
        $replaced = (string) \preg_replace('/# BEGIN-INIT-PLUGIN.*# END-INIT-PLUGIN/s', '', $contents);
        \file_put_contents($file, $replaced);
    }
}

$args = $argv;

$description = \array_pop($args);
$label = \array_pop($args);
$repositoryName = \array_pop($args);

if (empty($description)) {
    echo "You provided an empty description";
    exit(1);
}

if (empty($label)) {
    echo "You provided an empty label";
    exit(1);
}

$name = \implode(' ', \array_map('ucfirst', \array_map('strtolower', \explode('-', $repositoryName))));
$topicName = \trim(\str_replace(['Heptacom', 'Shopware', 'Platform'], '', $name));
$technicalName = \str_replace(' ', '', $name);
$topic = \str_replace(['Heptacom', 'Shopware', 'Platform'], '', $technicalName);
$packagistName = 'heptacom/shopware-platform' . \strtolower(\preg_replace('/([A-Z])/', '-$1', $topic));
$shopwareBundleName = 'heptacom-shopware-platform' . \strtolower(\preg_replace('/([A-Z])/', '-$1', $topic));

replaceInFiles(
    [
        __DIR__ . '/bin/phpstan/src/Rule/ContractsHaveDocumentationRule.php',
        __DIR__ . '/bin/phpstan/src/Rule/ImplementationsMustBeFinalRule.php',
        __DIR__ . '/bin/phpstan/src/Rule/InterfacesHaveDocumentationRule.php',
        __DIR__ . '/bin/phpstan/composer.json',
        __DIR__ . '/phpstan.neon',
    ],
    [
        'Heptacom\\ShopwarePlatform' => 'Heptacom\\ShopwarePlatform\\' . $topic,
        'Heptacom\\\\ShopwarePlatform' => 'Heptacom\\\\ShopwarePlatform\\\\' . $topic,
    ]
);
replaceInFiles(
    [
        __DIR__ . '/../src/HeptacomShopwarePlatformPluginName.php',
        __DIR__ . '/../composer.json',
    ],
    [
        'Heptacom\\ShopwarePlatform\\PluginName' => 'Heptacom\\ShopwarePlatform\\' . $topic,
        'Heptacom\\\\ShopwarePlatform\\\\PluginName' => 'Heptacom\\\\ShopwarePlatform\\\\' . $topic,
    ]
);
replaceInFiles(
    [
        __DIR__ . '/psalm.xml',
        __DIR__ . '/../src/HeptacomShopwarePlatformPluginName.php',
        __DIR__ . '/../composer.json',
        __DIR__ . '/bin/shopware/var/plugins.json',
    ],
    [
        'HeptacomShopwarePlatformPluginName' => 'HeptacomShopwarePlatform' . $topic,
    ]
);
replaceInFiles(
    [__DIR__ . '/bin/shopware/var/plugins.json'],
    ['heptacom-shopware-platform-plugin-name' => $shopwareBundleName]
);
replaceInFiles(
    [__DIR__ . '/../composer.json'],
    ['heptacom/shopware-platform-plugin-project' => $packagistName]
);
replaceInFiles(
    [__DIR__ . '/../composer.json'],
    [
        'HEPTACOM Shopware 6 Plugin' => \trim(\json_encode($description), '"'),
        'Plugin name' => \trim(\json_encode($label), '"'),
    ]
);
replaceInFiles(
    [__DIR__ . '/../README.md'],
    ['HEPTACOM Shopware 6 Plugin' => $description]
);
replaceInFiles(
    [__DIR__ . '/../README.md'],
    ['# Shopware Platform Plugin Project' => '# ' . $topicName]
);
removeSection([__DIR__ . '/../bitbucket-pipelines.yml']);

\rename(__DIR__ . '/../src/HeptacomShopwarePlatformPluginName.php', __DIR__ . '/../src/HeptacomShopwarePlatform' . $topic . '.php');
