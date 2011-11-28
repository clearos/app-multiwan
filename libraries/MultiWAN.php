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
use \clearos\apps\network\Network_Status as Network_Status;

clearos_load_library('base/File');
clearos_load_library('firewall/Firewall');
clearos_load_library('firewall/Rule');
clearos_load_library('network/Iface_Manager');
clearos_load_library('network/Network_Status');

// Exceptions
//-----------

use \clearos\apps\base\File_Not_Found_Exception as File_Not_Found_Exception;
use \clearos\apps\base\Validation_Exception as Validation_Exception;

clearos_load_library('base/File_Not_Found_Exception');
clearos_load_library('base/Validation_Exception');

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

    const FILE_CONFIG = '/etc/clearos/multiwan.conf';
    const COMMAND_FIREWALL_START = '/usr/sbin/firewall-start';
    const CONSTANT_ON = 'on';
    const CONSTANT_OFF = 'off';
    const DEFAULT_WEIGHT = 1;

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
            $info['protocol_name'] = $rule->get_protocol_name();
            $info['interface'] = $rule->get_parameter();
            $info['enabled'] = $rule->is_enabled();

            $list[] = $info;
        }

        return $list;
    }

    /**
     * Returns interface details.
     *
     * @param string $iface interface
     *
     * @return array list of external interfaces
     */

    public function get_external_interface($iface)
    {
        clearos_profile(__METHOD__, __LINE__);

        Validation_Exception::is_valid($this->validate_interface($iface));

        $all_ifs = $this->get_external_interfaces();

        return $all_ifs[$iface];
    }

    /**
     * Returns an array of external interfaces.
     *
     * @return array list of external interfaces
     */

    public function get_external_interfaces()
    {
        clearos_profile(__METHOD__, __LINE__);

        $iface_manager = new Iface_Manager();

        $iface_details = $iface_manager->get_interface_details();
        $ifaces = $iface_manager->get_external_interfaces();
        $working = $this->get_working_external_interfaces();
        $in_use = $this->get_in_use_external_interfaces();
        

        $external = array();

        foreach ($ifaces as $iface) {
            $external[$iface]['working'] = (in_array($iface, $working)) ? TRUE : FALSE;
            $external[$iface]['in_use'] = (in_array($iface, $in_use)) ? TRUE : FALSE;
            $external[$iface]['address'] = (isset($iface_details[$iface]['address'])) ? $iface_details[$iface]['address'] : '';
            $external[$iface]['weight'] = $this->get_interface_weight($iface);
        }

        return $external;
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

        // TODO: apply file class and coding standards

        $list = array();

        $ph = @popen("source " . self::FILE_CONFIG . " && echo \$MULTIPATH_WEIGHTS", "r");
        if(!$ph) return $list;

        $list = explode(" ", chop(fgets($ph)));
        pclose($ph);

        foreach ($list as $item) {
            if (preg_match("/\|/", $item)) {
                list($ifn_weight, $weight) = explode("|", $item, 2);
                if($ifn_weight == $interface) return $weight;
            }
        }

        return self::DEFAULT_WEIGHT;
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
     * @throws Engine_Exception, Network_Status_Unknown_Exception
     */

    public function get_working_external_interfaces()
    {
        clearos_profile(__METHOD__, __LINE__);

        $status = new Network_Status();

        return $status->get_working_external_interfaces();
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
     * @throws Engine_Exception, Network_Status_Unknown_Exception
     */

    public function get_in_use_external_interfaces()
    {
        $status = new Network_Status();

        return $status->get_in_use_external_interfaces();
    }

    /**
     * Returns the state of multi-WAN.
     *
     * @return boolean TRUE if multi-WAN is enabled
     */

    public function is_enabled()
    {
        clearos_profile(__METHOD__, __LINE__);

        $ph = @popen("source " . self::FILE_CONFIG . " && echo \$MULTIPATH", "r");

        if (!$ph)
            return FALSE;

        $enabled = FALSE;

        if (chop(fgets($ph)) == self::CONSTANT_ON)
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

        // Set configuration
        //------------------

        $weights = "$interface|$weight";

        $ifaces = $this->get_external_interfaces();
        
        foreach ($ifaces as $iface => $details) {
            if ($interface == $iface)
                continue;
            
            $weights = "$weights $iface|" . $details['weight'];
        }

        $config = new File(self::FILE_CONFIG);
        $matches = 0;

        try {
            $matches = $config->replace_lines("/^MULTIPATH_WEIGHTS=.*/", "MULTIPATH_WEIGHTS=\"$weights\"\n");
        } catch (File_Not_Found_Exception $e) {
            // Not fatal
            $config->create('root', 'root', '0644');
        }

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

        $file = new File(self::FILE_CONFIG);

        $enable_value = ($state) ? self::CONSTANT_ON : self::CONSTANT_OFF;

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
}
