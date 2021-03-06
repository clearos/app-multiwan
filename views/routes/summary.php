<?php

/**
 * Multi-WAN source-based routes summary view.
 *
 * @category   apps
 * @package    multiwan
 * @subpackage views
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2006-2015 ClearFoundation
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/multiwan/
 */

///////////////////////////////////////////////////////////////////////////////
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.  
//
///////////////////////////////////////////////////////////////////////////////


///////////////////////////////////////////////////////////////////////////////
// Load dependencies
///////////////////////////////////////////////////////////////////////////////

$this->lang->load('network');
$this->lang->load('multiwan');
$this->lang->load('firewall');

///////////////////////////////////////////////////////////////////////////////
// Headers
///////////////////////////////////////////////////////////////////////////////

$headers = array(
    lang('firewall_nickname'),
    lang('network_ip'),
    lang('network_interface'),
);

///////////////////////////////////////////////////////////////////////////////
// Anchors
///////////////////////////////////////////////////////////////////////////////

$anchors = array(anchor_add('/app/multiwan/routes/add'));

///////////////////////////////////////////////////////////////////////////////
// Items
///////////////////////////////////////////////////////////////////////////////

foreach ($routes as $rule) {

    // Deal with embedded / in network notation
    $address = preg_replace('/\//', '-', $rule['address']);

    // Order IPs in human-readable way
    $order_ip = "<span style='display: none'>" . sprintf("%032b", ip2long($rule['address'])) . "</span>" . $rule['address'];
    $status = ($rule['enabled']) ? 'disable' : 'enable';
    $anchor = ($rule['enabled']) ? 'anchor_disable' : 'anchor_enable';

    $item['current_state'] = (bool)$rule['enabled'];
    $item['title'] = $rule['name'];
    $item['action'] = '/app/multiwan/routes/edit/' . $$address;
    $item['anchors'] = button_set(
        array(
            $anchor('/app/multiwan/routes/set_state/' . $status . '/' . $address . '/' . $rule['interface'], 'high'),
            anchor_delete('/app/multiwan/routes/delete/' . $address . '/' . $rule['interface'], 'low'),
        )
    );
    $item['details'] = array(
        $rule['name'],
        $order_ip,
        $rule['interface'],
    );

    $items[] = $item;
}

///////////////////////////////////////////////////////////////////////////////
// Summary table
///////////////////////////////////////////////////////////////////////////////

$options = array(
    'id' => 'source-based-summary',
    'row-enable-disable' => TRUE
);
echo summary_table(
    lang('multiwan_source_based_routes'),
    $anchors,
    $headers,
    $items,
    $options
);
