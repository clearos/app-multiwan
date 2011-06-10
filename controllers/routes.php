<?php

/**
 * Multi-WAN source-based routes controller.
 *
 * @category   Apps
 * @package    MultiWAN
 * @subpackage Controllers
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2011 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
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

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * Multi-WAN source-based routes controller.
 *
 * @category   Apps
 * @package    MultiWAN
 * @subpackage Controllers
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2011 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/multiwan/
 */

class Routes extends ClearOS_Controller
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
            $data['routes'] = $this->multiwan->get_source_based_routes();
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }
 
        // Load views
        //-----------

        $this->page->view_form('multiwan/routes/summary', $data, lang('multiwan_source_based_routes'));
    }

    /**
     * Add entry view.
     *
     * @param string $ip        IP address
     *
     * @return view
     */

    function add($ip = NULL)
    {
        $this->_add_edit($ip, 'add');
    }

    /**
     * Delete entry view.
     *
     * @param string $ip IP address
     *
     * @return view
     */

    function delete($ip = NULL, $interface = NULL)
    {
        $confirm_uri = '/app/multiwan/routes/destroy/' . $ip . '/' . $interface;
        $cancel_uri = '/app/multiwan/routes';
        $items = array($ip);

        $this->page->view_confirm_delete($confirm_uri, $cancel_uri, $items);
    }

    /**
     * Destroys entry.
     *
     * @param string $ip        IP address
     * @param string $interface network interface
     *
     * @return view
     */

    function destroy($ip = NULL, $interface = NULL)
    {
        // Load libraries
        //---------------

        $this->load->library('multiwan/MultiWAN');

        // Handle delete
        //--------------

        try {
            $this->multiwan->delete_source_based_route($ip, $interface);
            $this->multiwan->reset();

            $this->page->set_status_deleted();
            redirect('/multiwan');
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }
    }

    /**
     * Sets the state of the entry.
     *
     * @param string $type      enable or disable
     * @param string $ip        IP address
     * @param string $interface network interface
     *
     * @return view
     */

    function set_state($type, $ip = NULL, $interface = NULL)
    {
        // Load libraries
        //---------------

        $this->load->library('multiwan/MultiWAN');

        // Handle delete
        //--------------

        try {
            $state = ($type === 'enable') ? TRUE : FALSE;
            $this->multiwan->set_source_based_route_state($state, $ip, $interface);
            $this->multiwan->reset();

            $this->page->set_status_updated();
            redirect('/multiwan');
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
     * @param string $ip        IP address
     * @param string $form_type form type
     *
     * @return view
     */

    function _add_edit($ip, $form_type)
    {
        // Load libraries
        //---------------

        $this->load->library('multiwan/MultiWAN');
        $this->lang->load('multiwan');

        // Validate
        //---------

        $this->form_validation->set_policy('name', 'multiwan/MultiWAN', 'validate_name');
        $this->form_validation->set_policy('address', 'multiwan/MultiWAN', 'validate_ip', TRUE);
        $this->form_validation->set_policy('interface', 'multiwan/MultiWAN', 'validate_interface', TRUE);

        $form_ok = $this->form_validation->run();

        // Handle form submit
        //-------------------

        if ($this->input->post('submit') && ($form_ok === TRUE)) {

            $ip = $this->input->post('address');
            $name = $this->input->post('name');
            $interface = $this->input->post('interface');

            try {
                $this->multiwan->add_source_based_route($name, $ip, $interface);
                $this->multiwan->reset();

                // Return to summary page with status message
                $this->page->set_status_added();
                redirect('/multiwan/routes');
            } catch (Rule_Already_Exists_Exception $e) {
echo "dude";
            } catch (Exception $e) {
                $this->page->view_exception($e);
                return;
            }
        }

        // Load the view data 
        //------------------- 

        try {
            $data['interfaces'] = $this->multiwan->get_external_interfaces();
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }

        $data['form_type'] = $form_type;

        $data['address'] = $address;
        $data['name'] = isset($entry['name']) ? $entry['name'] : '';
        $data['interface'] = isset($entry['interface']) ? $entry['interface'] : '';

        // Load the views
        //---------------

        $this->page->view_form('multiwan/routes/add_edit', $data, lang('multiwan_source_based_routes'));
    }
}
