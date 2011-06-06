<?php

///////////////////////////////////////////////////////////////////////////////
//
// Copyright 2010 ClearFoundation
//
///////////////////////////////////////////////////////////////////////////////
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
//
///////////////////////////////////////////////////////////////////////////////

// FIXME: what to do with read-only form values?
// FIXME: what to do with validating IP ranges and its ilk

///////////////////////////////////////////////////////////////////////////////
// Load dependencies
///////////////////////////////////////////////////////////////////////////////

$this->lang->load('dhcp');

///////////////////////////////////////////////////////////////////////////////
// Form modes
///////////////////////////////////////////////////////////////////////////////

if ($form_type === 'edit') {
	$form_path = '/dhcp/subnets/edit';
	$buttons = array(
		form_submit_update('submit'),
		anchor_cancel('/app/dhcp/subnets/'),
		anchor_delete('/app/dhcp/subnets/delete/' . $interface)
	);
} else {
	$form_path = '/dhcp/subnets/add';
	$buttons = array(
		form_submit_add('submit'),
		anchor_cancel('/app/dhcp/subnets/')
	);
}

///////////////////////////////////////////////////////////////////////////////
// Form open
///////////////////////////////////////////////////////////////////////////////

echo form_open($form_path . '/' . $interface);
echo form_fieldset(lang('dhcp_subnet'));

///////////////////////////////////////////////////////////////////////////////
// Form fields
///////////////////////////////////////////////////////////////////////////////

echo field_input('interface', $interface, lang('dhcp_network_interface'), TRUE);
echo field_input('network', $network, lang('dhcp_network'), TRUE);
echo field_dropdown('lease_time', $lease_times, $lease_time, lang('dhcp_lease_time'));
echo field_input('gateway', $gateway, lang('dhcp_gateway'));
echo field_input('start', $start, lang('dhcp_ip_range_start'));
echo field_input('end', $end, lang('dhcp_ip_range_end'));
echo field_input('dns1', $dns[0], lang('dhcp_dns') . " #1");
echo field_input('dns2', $dns[1], lang('dhcp_dns') . " #2");
echo field_input('dns3', $dns[2], lang('dhcp_dns') . " #3");
echo field_input('wins', $wins, lang('dhcp_wins'));
echo field_input('tftp', $tftp, lang('dhcp_tftp'));
echo field_input('ntp', $ntp, lang('dhcp_ntp'));

///////////////////////////////////////////////////////////////////////////////
// Buttons
///////////////////////////////////////////////////////////////////////////////

echo button_set($buttons);

///////////////////////////////////////////////////////////////////////////////
// Form close
///////////////////////////////////////////////////////////////////////////////

echo form_fieldset_close();
echo form_close();

// vim: ts=4 syntax=php
