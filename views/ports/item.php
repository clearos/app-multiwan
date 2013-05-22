<?php

/**
 * Multi-WAN destination port rules view.
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

$this->load->language('multiwan');
$this->load->language('firewall');
$this->load->language('network');

///////////////////////////////////////////////////////////////////////////////
// Form
///////////////////////////////////////////////////////////////////////////////

echo form_open('/multiwan/ports/add');
echo form_header(lang('multiwan_destination_port_rule'));

echo field_input('name', $name, lang('firewall_nickname'));
echo field_simple_dropdown('protocol', $protocols, $protocol, lang('network_protocol'));
echo field_input('port', $port, lang('network_port'));
echo field_simple_dropdown('interface', $interfaces, $interface, lang('network_interface'));

echo field_button_set(
    array(
        form_submit_add('submit'),
        anchor_cancel('/app/multiwan/ports')
    )
);

echo form_footer();
echo form_close();
