<?php

/**
 * Multi-WAN source-based routes add/edit view.
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

$this->load->language('multiwan');
$this->load->language('network');

///////////////////////////////////////////////////////////////////////////////
// Form open
///////////////////////////////////////////////////////////////////////////////

echo form_open('/multiwan/routes/add');
echo form_header(lang('multiwan_source_based_route'));

///////////////////////////////////////////////////////////////////////////////
// Form fields
///////////////////////////////////////////////////////////////////////////////
// Even though hostnames are permitted, we only allow IP addresses right now.

echo field_input('name', $name, lang('multiwan_nickname'));
echo field_input('address', $address, lang('network_ip'));
echo field_simple_dropdown('interface', $interfaces, $interface, lang('network_interface'));

echo button_set(
    array(
        form_submit_add('submit'),
        anchor_cancel('/app/multiwan/routes/')
    )
);

///////////////////////////////////////////////////////////////////////////////
// Form close
///////////////////////////////////////////////////////////////////////////////

echo form_footer();
echo form_close();
