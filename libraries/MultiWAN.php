<?php

/**
 * Multi-WAN class.
 *
 * @category   Apps
 * @package    MultiWAN
 * @subpackage Libraries
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2006-2011 ClearFoundation
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/multiwan/
 */

///////////////////////////////////////////////////////////////////////////////
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Lesser General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Lesser General Public License for more details.
//
// You should have received a copy of the GNU Lesser General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
// N A M E S P A C E
///////////////////////////////////////////////////////////////////////////////

namespace clearos\apps\multiwan;

///////////////////////////////////////////////////////////////////////////////
// B O O T S T R A P
///////////////////////////////////////////////////////////////////////////////

$bootstrap = getenv('CLEAROS_BOOTSTRAP') ? getenv('CLEAROS_BOOTSTRAP') : '/usr/clearos/framework/shared';
require_once $bootstrap . '/bootstrap.php';

///////////////////////////////////////////////////////////////////////////////
// T R A N S L A T I O N S
///////////////////////////////////////////////////////////////////////////////

clearos_load_language('multiwan');

///////////////////////////////////////////////////////////////////////////////
// D E P E N D E N C I E S
///////////////////////////////////////////////////////////////////////////////

// Classes
//--------

use \clearos\apps\base\File as File;
use \clearos\apps\firewall\Firewall as Firewall;
use \clearos\apps\firewall\Rule as Rule;
use \clearos\apps\network\Iface_Manager as Iface_Manager;

clearos_load_library('base/File');
clearos_load_library('firewall/Firewall');
clearos_load_library('firewall/Rule');
clearos_load_library('network/Iface_Manager');

// Exceptions
//-----------

use \clearos\apps\base\Validation_Exception as Validation_Exception;
use \clearos\apps\multiwan\MultiWAN_Unknown_Status_Exception as MultiWAN_Unknown_Status_Exception;

clearos_load_library('base/Validation_Exception');
clearos_load_library('multiwan/MultiWAN_Unknown_Status_Exception');

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * Multi-WAN class.
 *
 * @category   Apps
 * @package    MultiWAN
 * @subpackage Libraries
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2006-2011 ClearFoundation
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/multiwan/
 */

class MultiWAN extends Firewall
{
    ///////////////////////////////////////////////////////////////////////////
    // C O N S T A N T S
    ///////////////////////////////////////////////////////////////////////////

    // TODO: move this to /var/clearos/multiwan with new sync tool
    const FILE_STATE = '/var/lib/syswatch/state';

    ///////////////////////////////////////////////////////////////////////////////
    // V A R I A B L E S
    ///////////////////////////////////////////////////////////////////////////////

    protected $ifs_in_use = array();
    protected $ifs_working = array();
    protected $is_state_loaded = FALSE;

    ///////////////////////////////////////////////////////////////////////////
    // M E T H O D S
    ///////////////////////////////////////////////////////////////////////////

    /**
     * MultiWAN constructor.
     */

    public function __construct() 
    {
        clearos_profile(__METHOD__, __LINE__);
    }

    /**
     * Adds a destination port rule to the firewall.
     *
     * @param string  $name      name
     * @param string  $protocol  protocol
     * @param integer $port      port number
     * @param string  $interface network interface
     *
     * @return void
     * @throws Engine_Exception
     */

    public function add_destination_port_rule($name, $protocol, $port, $interface)
    {
        clearos_profile(__METHOD__, __LINE__);

        Validation_Exception::is_valid($this->validate_name($name));
        Validation_Exception::is_valid($this->validate_protocol($protocol));
        Validation_Exception::is_valid($this->validate_port($port));
        Validation_Exception::is_valid($this->validate_interface($interface));

        $rule = new Rule();

        $rule->set_name($name);
        $rule->set_flags(Rule::SBR_PORT | Rule::ENABLED);
        $rule->set_protocol($rule->convert_protocol_name($protocol));
        $rule->set_port($port);
        $rule->set_parameter($interface);

        $this->add_rule($rule);
    }

    /**
     * Adds a source-based route rule to the firewall.
     *
     * @param string $name      rule name
     * @param string $address   address
     * @param string $interface network interface
     *
     * @return void
     * @throws Engine_Exception
     */

    public function add_source_based_route($name, $address, $interface)
    {
        clearos_profile(__METHOD__, __LINE__);

        Validation_Exception::is_valid($this->validate_name($name));
        Validation_Exception::is_valid($this->validate_address($address));
        Validation_Exception::is_valid($this->validate_interface($interface));

        $rule = new Rule();

        $rule->set_name($name);
        $rule->set_flags(Rule::SBR_HOST | Rule::ENABLED);
        $rule->set_address($address);
        $rule->set_parameter($interface);

        $this->add_rule($rule);
    }

    /**
     * Deletes a destination port rule from the firewall.
     *
     * @param string  $protocol  protocol
     * @param integer $port      port number
     * @param string  $interface network interface
     *
     * @return void
     * @throws Engine_Exception
     */

    public function delete_destination_port_rule($protocol, $port, $interface)
    {
        clearos_profile(__METHOD__, __LINE__);

        Validation_Exception::is_valid($this->validate_protocol($protocol));
        Validation_Exception::is_valid($this->validate_port($port));
        Validation_Exception::is_valid($this->validate_interface($interface));

        $rule = new Rule();

        $rule->set_flags(Rule::SBR_PORT);
        $rule->set_protocol($protocol);
        $rule->set_port($port);
        $rule->set_parameter($interface);

        $this->delete_rule($rule);
    }

    /**
     * Remove a source-based route rule from the firewall.
     *
     * @param string $address   address
     * @param string $interface interface
     *
     * @return void
     * @throws Engine_Exception
     */

    public function delete_source_based_route($address, $interface)
    {
        clearos_profile(__METHOD__, __LINE__);

        Validation_Exception::is_valid($this->validate_name($name));
        Validation_Exception::is_valid($this->validate_address($address));
        Validation_Exception::is_valid($this->validate_interface($interface));

        $rule = new Rule();

        $rule->set_flags(Rule::SBR_HOST);
        $rule->set_address($address);
        $rule->set_parameter($interface);

        $this->delete_rule($rule);
    }

    /**
     * Returns an array of destination port rules.
     *
     * Info array contains:
     *  - name
     *  - protocol
     *  - port
     *  - interface
     *  - enabled
     *
     * @return array array list containing destination port rules
     * @throws Engine_Exception
     */

    public function get_destination_port_rules()
    {
        clearos_profile(__METHOD__, __LINE__);

        $list = array();

        $rules = $this->get_rules();

        foreach ($rules as $rule) {
            if (!($rule->get_flags() & (Rule::SBR_PORT)))
                continue;

            $info = array();

            $info['name'] = $rule->get_name();
            $info['port'] = $rule->get_port();
            $info['protocol'] = $rule->get_protocol();
            $info['interface'] = $rule->get_parameter();
            $info['enabled'] = $rule->is_enabled();

            $list[] = $info;
        }

        return $list;
    }

    /**
     * Returns an array of external interfaces.
     *
     * @return array array list containing external interfaces
     */

    public function get_external_interfaces()
    {
        clearos_profile(__METHOD__, __LINE__);

        $iface_manager = new Iface_Manager();

        return $iface_manager->get_external_interfaces();
    }

    /**
     * Returns the interface weight.
     *
     * @param string $interface interface
     *
     * @return int integer weight value
     */

    public function get_interface_weight($interface)
    {
        clearos_profile(__METHOD__, __LINE__);

        // TODO: migrate to /etc/multiwan

        $list = array();

        $ph = @popen("source " . Firewall::FILE_CONFIG . " && echo \$MULTIPATH_WEIGHTS", "r");
        if(!$ph) return $list;

        $list = explode(" ", chop(fgets($ph)));
        pclose($ph);

        foreach ($list as $item) {
            if (preg_match("/\|/", $item)) {
                list($ifn_weight, $weight) = explode("|", $item, 2);
                if($ifn_weight == $interface) return $weight;
            }
        }

        return 1;
    }

    /**
     * Returns an array of source-based route rules.
     *
     * Info array contains:
     *  - name
     *  - interface
     *  - address
     *  - enabled
     *
     * @return array array list containing source-based route rules
     */

    public function get_source_based_routes()
    {
        clearos_profile(__METHOD__, __LINE__);

        $list = array();

        $rules = $this->get_rules();

        foreach ($rules as $rule) {
            if (!($rule->get_flags() & (Rule::SBR_HOST)))
                continue;

            $info = array();

            $info['name'] = $rule->get_name();
            $info['interface'] = $rule->get_parameter();
            $info['address'] = $rule->get_address();
            $info['enabled'] = $rule->is_enabled();

            $list[] = $info;
        }

        return $list;
    }

    /**
     * Returns list of working external (WAN) interfaces.
     *
     * Syswatch monitors the connections to the Internet.  A connection
     * is considered online when it can ping the Internet.
     *
     * @return array list of working WAN interfaces
     * @throws Engine_Exception, MultiWAN_Unknown_Status_Exception
     */

    public function get_working_external_interfaces()
    {
        if (!$this->is_state_loaded)
            $this->_load_status();

        return $this->ifs_working;
    }

    /**
     * Returns list of in use external (WAN) interfaces.
     *
     * Syswatch monitors the connections to the Internet.  A connection
     * is considered in use when it can ping the Internet and is actively
     * used to connect to the Internet.  A WAN interface used for only backup
     * purposes is only included in this list when non-backup WANs are all down.
     *
     * @return array list of in use WAN interfaces
     * @throws Engine_Exception, MultiWAN_Unknown_Status_Exception
     */

    public function get_in_use_external_interfaces()
    {
        if (!$this->is_state_loaded)
            $this->_load_status();

        return $this->ifs_in_use;
    }

    /**
     * Returns the state of multi-WAN.
     *
     * @return boolean TRUE if multi-WAN is enabled
     */

    public function is_enabled()
    {
        clearos_profile(__METHOD__, __LINE__);

        $ph = @popen("source " . Firewall::FILE_CONFIG . " && echo \$MULTIPATH", "r");

        if (!$ph)
            return FALSE;

        $enabled = FALSE;

        if (chop(fgets($ph)) == Firewall::CONSTANT_ON)
            $enabled = TRUE;

        if (pclose($ph) != 0)
            return FALSE;

        return $enabled;
    }

    /**
     * Enables/disables a destination port rule.
     *
     * @param boolean $state     state falg
     * @param string  $protocol  protocol
     * @param integer $port      port number
     * @param string  $interface network interface
     *
     * @return void
     * @throws Engine_Exception
     */

    public function set_destination_port_rule_state($state, $protocol, $port, $interface)
    {
        clearos_profile(__METHOD__, __LINE__);

        Validation_Exception::is_valid($this->validate_protocol($protocol));
        Validation_Exception::is_valid($this->validate_port($port));
        Validation_Exception::is_valid($this->validate_interface($interface));

        $rule = new Rule();

        $rule->set_flags(Rule::SBR_PORT);
        $rule->set_protocol($protocol);
        $rule->set_port($port);
        $rule->set_parameter($interface);

        if (!($rule = $this->find_rule($rule)))
            return;

        $this->delete_rule($rule);

        if ($state)
            $rule->enable();
        else
            $rule->disable();

        $this->add_rule($rule);
    }

    /**
     * Enable/disable a source-based route rule.
     *
     * @param boolean $state     state
     * @param string  $address   address
     * @param string  $interface network interface
     *
     * @return void
     * @throws Engine_Exception
     */

    public function set_source_based_route_state($state, $address, $interface)
    {
        clearos_profile(__METHOD__, __LINE__);

        Validation_Exception::is_valid($this->validate_name($name));
        Validation_Exception::is_valid($this->validate_address($address));
        Validation_Exception::is_valid($this->validate_interface($interface));

        $rule = new Rule();

        $rule->set_flags(Rule::SBR_HOST);
        $rule->set_address($address);
        $rule->set_parameter($interface);

        if (!($rule = $this->find_rule($rule)))
            return;

        $this->delete_rule($rule);

        if ($state)
            $rule->enable();
        else
            $rule->disable();

        $this->add_rule($rule);
    }

    /**
     * Sets the interface weight.
     *
     * @param string  $interface interface
     * @param integer $weight    weight
     *
     * @return void
     * @throws Engine_Exception
     */

    public function set_interface_weight($interface, $weight)
    {
        clearos_profile(__METHOD__, __LINE__);

        // Validation
        //-----------

        Validation_Exception::is_valid($this->validate_interface($interface));
        Validation_Exception::is_valid($this->validate_weight($weight));

        // Extra validation
        //-----------------

        $wanif_list = $this->get_external_interfaces();

        if (! in_array($interface, $wanif_list))
            throw new Validation_Exception(lang('multiwan_weight_must_be_set_on_external_network_interface'));

        // Set configuration
        //------------------

        $weights = "$interface|$weight";

        foreach ($wanif_list as $wanif) {
            if ($interface == $wanif)
                continue;

            $weights = "$weights $wanif|" . $this->get_interface_weight($wanif);
        }

        $config = new File(Firewall::FILE_CONFIG);

        $matches = $config->replace_lines("/^MULTIPATH_WEIGHTS=.*/", "MULTIPATH_WEIGHTS=\"$weights\"\n");

        if ($matches < 1)
            $config->add_lines("MULTIPATH_WEIGHTS=\"$weights\"\n");
    }

    /**
     * Sets the state of multi-WAN mode.
     *
     * @param boolean $state state of multi-WAN
     *
     * @return void
     * @throws Engine_Exception
     */

    public function set_multiwan_state($state)
    {
        clearos_profile(__METHOD__, __LINE__);

        $file = new File(Firewall::FILE_CONFIG);

        $enable_value = ($state) ? Firewall::CONSTANT_ON : Firewall::CONSTANT_OFF;

        $matches = $file->replace_lines('/^MULTIPATH=.*/i', "MULTIPATH=\"$enable_value\"\n");

        if ($matches < 1)
            $file->add_lines("MULTIPATH=\"$enable_value\"\n");
    }

    ///////////////////////////////////////////////////////////////////////////
    // V A L I D A T I O N   R O U T I N E S
    ///////////////////////////////////////////////////////////////////////////

    /**
     * Validates interface weight.
     *
     * @param integer $weight weight
     *
     * @return error message if weight is invalid
     */

    public function validate_weight($weight)
    {
        clearos_profile(__METHOD__, __LINE__);

        if (! preg_match('/^\d+$/', $weight))
            return lang('multiwan_weight_is_invalid');

        if (($weight < 1) || ($weight > 200))
            return lang('multiwan_weight_is_out_of_range');
    }

    ///////////////////////////////////////////////////////////////////////////
    // P R I V A T E  M E T H O D S
    ///////////////////////////////////////////////////////////////////////////

    /**
     * Loads state file.
     *
     * @access private
     *
     * @return void
     * @throws Engine_Exception, MultiWAN_Unknown_Status_Exception
     */

    protected function _load_status()
    {
        $file = new File(self::FILE_STATE);

        if (! $file->exists())
            throw new MultiWAN_Unknown_Status_Exception();

        $lines = $file->get_contents_as_array();

        foreach ($lines as $line) {
            $match = array();
            if (preg_match('/^SYSWATCH_WANIF=(.*)/', $line, $match)) {
                $ethraw = $match[1];
                $ethraw = preg_replace('/"/', '', $ethraw);
                $ethlist = explode(' ', $ethraw);
                $this->ifs_in_use = explode(' ', $ethraw);
                $this->is_state_loaded = TRUE;
            }

            if (preg_match('/^SYSWATCH_WANOK=(.*)/', $line, $match)) {
                $ethraw = $match[1];
                $ethraw = preg_replace('/"/', '', $ethraw);
                $ethlist = explode(' ', $ethraw);
                $this->ifs_working = explode(' ', $ethraw);
                $this->is_state_loaded = TRUE;
            }
        }
    }
}
