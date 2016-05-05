<?php

/**
 * Multi-WAN controller.
 *
 * @category   apps
 * @package    multiwan
 * @subpackage controllers
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
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * Multi-WAN controller.
 *
 * @category   apps
 * @package    multiwan
 * @subpackage controllers
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2006-2011 ClearFoundation
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/multiwan/
 */

class MultiWAN extends ClearOS_Controller
{
    /**
     * Multi-WAN overview.
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
            $status = $this->multiwan->get_external_status();
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }

        // Load views
        //-----------

        if ($status) {
            $views = array('multiwan/interfaces', 'multiwan/routes', 'multiwan/ports');

            $this->page->view_forms($views, lang('multiwan_multiwan'));
        } else {
            $this->page->view_form('multiwan/waiting', NULL, lang('multiwan_multiwan'));
        }
    }

    /**
     * Multi-WAN status.
     *
     * @return status encoded in json
     */

    function status()
    {
        header('Cache-Control: no-cache, must-revalidate');
        header('Content-type: application/json');

        $this->load->library('multiwan/MultiWAN');

        $status['status'] = $this->multiwan->get_external_status();

        echo json_encode($status);
    }

}
