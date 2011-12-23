<?php

/////////////////////////////////////////////////////////////////////////////
// General information
/////////////////////////////////////////////////////////////////////////////

$app['basename'] = 'multiwan';
$app['version'] = '6.2.0.beta3';
$app['release'] = '1';
$app['vendor'] = 'ClearFoundation';
$app['packager'] = 'ClearFoundation';
$app['license'] = 'GPLv3';
$app['license_core'] = 'LGPLv3';
$app['description'] = lang('multiwan_app_description');

/////////////////////////////////////////////////////////////////////////////
// App name and categories
/////////////////////////////////////////////////////////////////////////////

$app['name'] = lang('multiwan_app_name');
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
    'multiwan.conf' => array(
        'target' => '/etc/clearos/multiwan.conf',
        'mode' => '0644',
        'owner' => 'root',
        'group' => 'root',
        'config' => TRUE,
        'config_params' => 'noreplace',
    ),
);
