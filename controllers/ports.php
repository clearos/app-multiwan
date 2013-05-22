<?php

/**
 * Multi-WAN destination port rules controller.
 *
 * @category   apps
 * @package    multiwan
 * @subpackage controllers
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
// D E P E N D E N C I E S
///////////////////////////////////////////////////////////////////////////////

use \clearos\apps\firewall\Rule_Already_Exists_Exception as Rule_Already_Exists_Exception;

use \Exception as Exception;

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////


/**
 * Multi-WAN destination port rules controller.
 *
 * @category   apps
 * @package    multiwan
 * @subpackage controllers
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2006-2012 ClearFoundation
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/multiwan/
 */

class Ports extends ClearOS_Controller
{
    /**
     * Source-based routes summary view.
     *
     * @return view
     */

    function index()
    {
        // Load libraries
        //---------------

        $this->load->library('multiwan/MultiWAN');
        $this->lang->load('multiwan');

        // Load view data
        //---------------

        try {
            $data['ports'] = $this->multiwan->get_destination_port_rules();
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }
 
        // Load views
        //-----------

        $this->page->view_form('multiwan/ports/summary', $data, lang('multiwan_destination_port_rules'));
    }

    /**
     * Add entry view.
     *
     * @param string $port port
     *
     * @return view
     */

    function add()
    {
        $this->_item('add');
    }

    /**
     * Delete entry view.
     *
     * @param string $protocol  protocol
     * @param string $port      port
     * @param string $interface interface
     *
     * @return view
     */

    function delete($protocol, $port, $interface)
    {
        $confirm_uri = '/app/multiwan/ports/destroy/' . $protocol . '/' . $port . '/' . $interface;
        $cancel_uri = '/app/multiwan/ports';
        $items = array($protocol . ' ' . $port);

        $this->page->view_confirm_delete($confirm_uri, $cancel_uri, $items);
    }

    /**
     * Destroys entry.
     *
     * @param string $protocol  protocol
     * @param string $port      port
     * @param string $interface network interface
     *
     * @return view
     */

    function destroy($protocol, $port, $interface)
    {
        // Load libraries
        //---------------

        $this->load->library('multiwan/MultiWAN');

        // Handle delete
        //--------------

        try {
            $this->multiwan->delete_destination_port_rule($protocol, $port, $interface);
            $this->multiwan->reset();

            $this->page->set_status_deleted();
            redirect('/multiwan/ports');
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }
    }

    /**
     * Sets the state of the entry.
     *
     * @param string $state     enable or disable
     * @param string $protocol  protocol
     * @param string $port      port
     * @param string $interface network interface
     *
     * @return view
     */

    function set_state($state, $protocol, $port, $interface)
    {
        // Load libraries
        //---------------

        $this->load->library('multiwan/MultiWAN');

        // Handle delete
        //--------------

        try {
            $state = ($state === 'enable') ? TRUE : FALSE;
            $this->multiwan->set_destination_port_rule_state($state, $protocol, $port, $interface);
            $this->multiwan->reset();

            $this->page->set_status_updated();
            redirect('/multiwan/ports');
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }
    }

    ///////////////////////////////////////////////////////////////////////////////
    // P R I V A T E
    ///////////////////////////////////////////////////////////////////////////////

    /**
     * Common add/edit form handler.
     *
     * @param string $form_type form type
     * @param string $ip        IP address
     *
     * @return view
     */

    function _item($form_type)
    {
        // Load libraries
        //---------------

        $this->load->library('multiwan/MultiWAN');
        $this->lang->load('multiwan');

        // Validate
        //---------

        $this->form_validation->set_policy('name', 'multiwan/MultiWAN', 'validate_name', TRUE);
        $this->form_validation->set_policy('port', 'multiwan/MultiWAN', 'validate_port', TRUE);
        $this->form_validation->set_policy('protocol', 'multiwan/MultiWAN', 'validate_protocol', TRUE);
        $this->form_validation->set_policy('interface', 'multiwan/MultiWAN', 'validate_interface', TRUE);

        $form_ok = $this->form_validation->run();

        // Handle form submit
        //-------------------

        if ($this->input->post('submit') && ($form_ok === TRUE)) {

            $name = $this->input->post('name');
            $port = $this->input->post('port');
            $protocol = $this->input->post('protocol');
            $interface = $this->input->post('interface');

            try {
                $this->multiwan->add_destination_port_rule($name, $protocol, $port, $interface);
                $this->multiwan->reset();

                $this->page->set_status_added();
                redirect('/multiwan/ports');
            } catch (Rule_Already_Exists_Exception $e) {
            } catch (Exception $e) {
                $this->page->view_exception($e);
                return;
            }
        }

        // Load the view data 
        //------------------- 

        try {
            $data['ports'] = $this->multiwan->get_destination_port_rules();
            $data['protocols'] = $this->multiwan->get_basic_protocols();
            $data['interfaces'] = $this->multiwan->get_in_use_external_interfaces();
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }

        $data['form_type'] = $form_type;

        // Load the views
        //---------------

        $this->page->view_form('multiwan/ports/item', $data, lang('multiwan_destination_port_rule'));
    }
}
