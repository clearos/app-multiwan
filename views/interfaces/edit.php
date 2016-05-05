<?php

/**
 * MultiWAN item view.
 *
 * @category   apps
 * @package    multiwan
 * @subpackage views
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2006-2016 ClearFoundation
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

$this->lang->load('multiwan');
$this->lang->load('network');

///////////////////////////////////////////////////////////////////////////////
// Form handler
///////////////////////////////////////////////////////////////////////////////

$buttons = array(
    form_submit_update('submit'),
    anchor_cancel('/app/multiwan/'),
);

///////////////////////////////////////////////////////////////////////////////
// Form
///////////////////////////////////////////////////////////////////////////////

echo form_open('/multiwan/interfaces/edit/' . $iface);
echo form_header(lang('multiwan_weight'));

echo field_input('iface', $iface, lang('network_interface'), TRUE);
echo field_input('address', $details['address'], lang('network_ip'), TRUE);
echo field_simple_dropdown('weight', $weights, $details['weight'], lang('multiwan_weight'));
echo field_dropdown('mode', $modes, $details['mode'], lang('multiwan_mode'));

echo field_button_set($buttons);

echo form_footer();
echo form_close();
