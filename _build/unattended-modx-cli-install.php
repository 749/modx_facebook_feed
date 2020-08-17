<?php
/*
 * This file is part of MODX Revolution.
 *
 * Copyright (c) MODX, LLC. All Rights Reserved.
 *
 * For complete copyright and license information, see the COPYRIGHT and LICENSE
 * files found in the top-level directory of this distribution.
 */

/**
 * CLI install script for MODX
 *
 * Run and answer the questions. Also you can pre-define values:
 * php www/setup/install.php --database=my_db --database_user=db_user_12 --language=ru
 */
$path = dirname(__FILE__) . '/deps/modx/setup/';
$config = __DIR__ . '/config.xml';
$mode = 'new';

$modxZipFile = dirname(__FILE__) . '/deps/modx.zip';
if(!file_exists($modxZipFile)) {
    echo "Downloading ModX 2.7.1...";
    file_put_contents($modxZipFile, fopen('https://modx.com/download/direct?id=modx-2.7.1-pl.zip&0=abs', 'r'));
}

echo "unzipping ModX";
chdir(dirname(__FILE__). '/deps/');
system('unzip modx.zip');
system('mv -f modx-2.7.1-pl modx');

$languages = array_slice(scandir($path . 'lang/'), 2);

$variables = [
    'database_type' => 'mysql',
    'database_server' => [
        'prompt' => 'connection_database_host',
        'default' => 'localhost',
    ],
    'database' => [
        'prompt' => 'connection_database_name',
        'default' => '',
    ],
    'database_user' => [
        'prompt' => 'connection_database_login',
        'default' => '',
    ],
    'database_password' => [
        'prompt' => 'connection_database_pass',
        'default' => '',
    ],
    'database_connection_charset' => 'utf8',
    'database_charset' => 'utf8',
    'database_collation' => 'utf8_general_ci',
    'table_prefix' => [
        'prompt' => 'connection_table_prefix',
        'default' => 'modx_',
    ],
    'https_port' => 443,
    'http_host' => 'localhost',
    'inplace' => 0,
    'unpacked' => 0,
    'language' => [
        'prompt' => 'choose_language',
        'default' => 'en',
    ],
    'cmsadmin' => [
        'prompt' => 'connection_default_admin_login',
        'default' => 'admin',
    ],
    'cmspassword' => [
        'prompt' => 'connection_default_admin_password',
        'default' => '',
    ],
    'cmsadminemail' => [
        'prompt' => 'connection_default_admin_email',
        'default' => '',
    ],
    'context_web_path' => dirname($path) . '/',
    'context_web_url' => '/',
    'core_path' => dirname($path) . '/core/',
    'context_mgr_path' => dirname($path) . '/manager/',
    'context_mgr_url' => '/manager/',
    'context_connectors_path' => dirname($path) . '/connectors/',
    'context_connectors_url' => '/connectors/',
    'remove_setup_directory' => 1,
];

// Parse CLI arguments
$args = array_slice($argv, 1);
$cli_variables = [];
foreach ($args as $arg) {
    $tmp = array_map('trim', explode('=', $arg));
    if (count($tmp) === 2) {
        $k = ltrim($tmp[0], '-');
        if (isset($variables[$k])) {
            $cli_variables[$k] = $tmp[1];
        } elseif ($k == 'mode' && in_array($tmp[1], ['new', 'upgrade', 'advanced'])) {
            $mode = $tmp[1];
        }
    }
}

$lang = !empty($cli_variables['language']) && in_array($cli_variables['language'], $languages)
    ? $cli_variables['language']
    : 'en';
/** @var array $_lang */
require($path . 'lang/' . $lang . '/default.inc.php');

// If it is upgrade - parse old variables
$old_variables = [];
if (file_exists($config)) {
        $tmp = simplexml_load_file($config);
        $tmp = @json_decode(json_encode($tmp), true);
        if (is_array($tmp)) {
            $old_variables = $tmp;
        }
}

// Merge everything
$variables = array_merge($variables, $old_variables, $cli_variables);

// Process config
$data = [];
foreach ($variables as $key => $params) {
    if (!is_array($params)) {
        $data[$key] = $params;
    } else {
        echo 'Configuration value not defined: ' . $key . PHP_EOL;
        echo 'Using empty string.'.PHP_EOL;
        $data[$key] = "";
    }
    if (strpos($key, '_path') !== false || strpos($key, '_url') !== false) {
        $data[$key] = preg_replace('#/+#', '/', ('/' . trim($data[$key], '/') . '/'));
    }
}

// Check directories
$rmdir = function ($dir) use (&$rmdir) {
    $files = array_diff(scandir($dir), ['.', '..']);
    foreach ($files as $file) {
        is_dir("$dir/$file") ? $rmdir("$dir/$file") : unlink("$dir/$file");
    }
    rmdir($dir);
};
$dirs = [
    'core_path' => '/core/',
    'context_mgr_path' => '/manager/',
    'context_connectors_path' => '/connectors/',
];
foreach ($dirs as $key => $value) {
    $target = dirname($path) . $value;
    if (file_exists($target) && $data[$key] != $target && strpos($data[$key], dirname($path)) === 0) {
        $rmdir(rtrim($data[$key], DIRECTORY_SEPARATOR));
        rename($target, $data[$key]);
    }
}
unset($rmdir);

// Generate config file
$xml = new SimpleXMLElement('<modx/>');
foreach ($data as $key => $value) {
    $xml->addChild($key, $value);
}
file_put_contents($path . 'config.xml', $xml->asXML());

// Run install!
echo "\n\033[32m{$_lang['thank_installing']}MODX!\n\n\033[0m";
$argv = [
    $path . 'index.php',
    '--installmode=' . $mode,
    '--core_path=' . $data['core_path'],
    '--config=' . $path . 'config.xml',
];
require($path . 'index.php');
