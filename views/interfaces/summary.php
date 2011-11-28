<?php

/**
 * Multiwan interfaces view.
 *
 * @category   ClearOS
 * @package    MultiWAN
 * @subpackage Views
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2006-2011 ClearFoundation
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

$this->lang->load('base');
$this->lang->load('multiwan');

///////////////////////////////////////////////////////////////////////////////
// Headers
///////////////////////////////////////////////////////////////////////////////

$headers = array(
    '',
	lang('network_ip'),
	lang('multiwan_network_status'),
	lang('multiwan_multiwan_status'),
	lang('multiwan_weight')
);

///////////////////////////////////////////////////////////////////////////////
// Anchors 
///////////////////////////////////////////////////////////////////////////////

$anchors = array();

///////////////////////////////////////////////////////////////////////////////
// Items
///////////////////////////////////////////////////////////////////////////////

foreach ($interfaces as $iface => $details) {

    // FIXME: need a theme element here to highlight good/bad status
    $in_use = ($details['in_use']) ? lang('multiwan_in_use') : lang('multiwan_offline');
    $working = ($details['working']) ? lang('multiwan_online') : lang('multiwan_offline');

	$item['title'] = "$iface / " .  $details['address'];
	$item['action'] = "/app/multiwan/interfaces/edit/" . $iface;
	$item['anchors'] = button_set(
        array(
            anchor_edit('/app/multiwan/interfaces/edit/' . $iface),
        )
    );

	$item['details'] = array(
		$iface,
		$details['address'],
        $working,
        $in_use,
		$details['weight']
	);

	$items[] = $item;
}

sort($items);

///////////////////////////////////////////////////////////////////////////////
// Summary table
///////////////////////////////////////////////////////////////////////////////

echo summary_table(
	lang('multiwan_interfaces'),
	$anchors,
	$headers,
	$items
);
