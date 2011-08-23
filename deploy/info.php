<?php

/////////////////////////////////////////////////////////////////////////////
// General information
/////////////////////////////////////////////////////////////////////////////

$app['basename'] = 'multiwan';
$app['version'] = '5.9.9.4';
$app['release'] = '2';
$app['vendor'] = 'ClearFoundation';
$app['packager'] = 'ClearFoundation';
$app['license'] = 'GPLv3';
$app['license_core'] = 'LGPLv3';
$app['summary'] = lang('multiwan_app_summary');
$app['description'] = lang('multiwan_app_long_description');

/////////////////////////////////////////////////////////////////////////////
// App name and categories
/////////////////////////////////////////////////////////////////////////////

$app['name'] = lang('multiwan_multiwan');
$app['category'] = lang('base_category_network');
$app['subcategory'] = lang('base_subcategory_settings');

/////////////////////////////////////////////////////////////////////////////
// Controllers
/////////////////////////////////////////////////////////////////////////////

$app['controllers']['multiwan']['title'] = lang('multiwan_multiwan');
$app['controllers']['interfaces']['title'] = lang('multiwan_interfaces');
$app['controllers']['routes']['title'] = lang('multiwan_source_based_routes');
$app['controllers']['ports']['title'] = lang('multiwan_destination_port_rules');

/////////////////////////////////////////////////////////////////////////////
// Packaging
/////////////////////////////////////////////////////////////////////////////

$app['requires'] = array(
    'app-network',
);

$app['core_requires'] = array(
    'app-network-core',
    'app-firewall-core',
    'csplugin-routewatch',
    'iptables',
    'syswatch',
);

$app['core_file_manifest'] = array(
    'routewatch-multiwan.conf' => array(
        'target' => '/etc/clearsync.d/routewatch-multiwan.conf',
        'mode' => '0644',
        'owner' => 'root',
        'group' => 'root',
        'config' => TRUE,
        'config_params' => 'noreplace',
    ),
);
