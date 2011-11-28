<?php

/**
 * Multi-WAN interfaces controller.
 *
 * @category   Apps
 * @package    MultiWAN
 * @subpackage Controllers
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
// D E P E N D E N C I E S
///////////////////////////////////////////////////////////////////////////////

use \Exception as Exception;

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * Multi-WAN interfaces controller.
 *
 * @category   Apps
 * @package    MultiWAN
 * @subpackage Controllers
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2006-2011 ClearFoundation
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/multiwan/
 */

class Interfaces extends ClearOS_Controller
{
    /**
     * Interfaces summary view.
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
            $data['interfaces'] = $this->multiwan->get_external_interfaces();
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }
 
        // Load views
        //-----------

        $this->page->view_form('multiwan/interfaces/summary', $data, lang('multiwan_interfaces'));
    }

    /**
     * Edit entry view.
     *
     * @param string $iface interface
     *
     * @return view
     */

    function edit($iface)
    {
        // Load libraries
        //---------------

        $this->lang->load('multiwan');
        $this->lang->load('network');
        $this->load->library('multiwan/MultiWAN');

        // Set validation rules
        //---------------------

        $this->form_validation->set_policy('weight', 'multiwan/MultiWAN', 'validate_weight', TRUE);

        $form_ok = $this->form_validation->run();

        // Handle form submit
        //-------------------

        if ($this->input->post('submit') && ($form_ok === TRUE)) {

            try {
                $this->multiwan->set_interface_weight($iface, $this->input->post('weight'));

                $this->page->set_status_updated();
                redirect('/multiwan/interfaces');
            } catch (Exception $e) {
                $this->page->view_exception($e);
                return;
            }
        }

        // Load the view data 
        //------------------- 

        try {
            $data['details'] = $this->multiwan->get_external_interface($iface);
            $data['iface'] = $iface;
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }

        // Load the views
        //---------------

        $this->page->view_form('multiwan/interfaces/edit', $data, lang('network_interface'));
    }
}
