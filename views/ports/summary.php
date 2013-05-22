<?php

/**
 * Multi-WAN destination port rules summary view.
 *
 * @category   apps
 * @package    multiwan
 * @subpackage views
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2006-2012 ClearFoundation
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
$this->lang->load('firewall');
$this->lang->load('multiwan');

///////////////////////////////////////////////////////////////////////////////
// Headers
///////////////////////////////////////////////////////////////////////////////

$headers = array(
    lang('firewall_nickname'),
    lang('network_protocol'),
    lang('network_port'),
    lang('network_interface'),
);

///////////////////////////////////////////////////////////////////////////////
// Anchors
///////////////////////////////////////////////////////////////////////////////

$anchors = array(anchor_add('/app/multiwan/ports/add'));

///////////////////////////////////////////////////////////////////////////////
// Items
///////////////////////////////////////////////////////////////////////////////

// Array ( [0] => Array ( [name] => bob [port] => 3333 [protocol] => 6 [protocol_name] => TCP [interface] => eth1 [enabled] => 268435456 ) )
foreach ($ports as $rule) {
    $status = ($rule['enabled']) ? 'disable' : 'enable';
    $anchor = ($rule['enabled']) ? 'anchor_disable' : 'anchor_enable';
    $key = $rule['protocol_name'] . '/' . $rule['port'] . '/' . $rule['interface'];

    $item['title'] = $rule['name'];
    $item['action'] = '/app/multiwan/ports/edit/' . $key;
    $item['anchors'] = button_set(
        array(
            $anchor('/app/multiwan/ports/set_state/' . $status . '/' . $key, 'high'),
            anchor_delete('/app/multiwan/ports/delete/' . $key, 'low'),
        )
    );
    $item['details'] = array(
        $rule['name'],
        $rule['protocol_name'],
        $rule['port'],
        $rule['interface'],
    );

    $items[] = $item;
}

///////////////////////////////////////////////////////////////////////////////
// Summary table
///////////////////////////////////////////////////////////////////////////////

echo summary_table(
    lang('multiwan_destination_port_rules'),
    $anchors,
    $headers,
    $items
);
