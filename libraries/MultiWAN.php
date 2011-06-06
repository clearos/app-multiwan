<?php

/**
 * Multi-WAN class.
 *
 * @category   Apps
 * @package    MultiWAN
 * @subpackage Libraries
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2005-2011 ClearFoundation
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
use \clearos\apps\network\Iface as Iface;
use \clearos\apps\network\Iface_Manager as Iface_Manager;

clearos_load_library('base/File');
clearos_load_library('firewall/Firewall');
clearos_load_library('firewall/Rule');
clearos_load_library('network/Iface');
clearos_load_library('network/Iface_Manager');

// Exceptions
//-----------

use \clearos\apps\base\Engine_Exception as Engine_Exception;
use \clearos\apps\base\File_No_Match_Exception as File_No_Match_Exception;
use \clearos\apps\base\Validation_Exception as Validation_Exception;

clearos_load_library('base/Engine_Exception');
clearos_load_library('base/File_No_Match_Exception');
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
 * @copyright  2005-2011 ClearFoundation
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/multiwan/
 */

class MultiWAN extends Firewall
{
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
     * @param string  $protocol  protocol
     * @param integer $from      from port number
     * @param integer $to        to port number
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
     * Enables/disables a destination port rule.
     *
     * @param boolean $enabled   state falg
     * @param string  $protocol  protocol
     * @param integer $port      port number
     * @param string  $interface network interface
     *
     * @return void
     * @throws Engine_Exception
     */

    public function toggle_enable_port_rule($enabled, $protocol, $port, $interface)
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

        if ($enabled)
            $rule->enable();
        else
            $rule->disable();

        $this->add_rule($rule);
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

    public function get_port_rules()
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
     * Adds a source-based route rule to the firewall.
     *
     * @param string  $name      rule name
     * @param string  $address   address
     * @param string  $interface network interface
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
     * @param boolean $enable enable or diable multi-WAN
     *
     * @return void
     * @throws Engine_Exception
     */

    public function enable_multi_wan($enable)
    {
        clearos_profile(__METHOD__, __LINE__);

        $file = new File(Firewall::FILE_CONFIG);

        $matches = $file->replace_lines(sprintf("/^%s=/i", Firewall::CONSTANT_MULTIPATH),
            sprintf("%s=\"%s\"\n", Firewall::CONSTANT_MULTIPATH,
            ($enable) ? Firewall::CONSTANT_ON : Firewall::CONSTANT_OFF));
        if ($matches < 1) {
                $file->add_lines(sprintf("%s=\"%s\"\n", Firewall::CONSTANT_MULTIPATH,
                    ($enable) ? Firewall::CONSTANT_ON : Firewall::CONSTANT_OFF));
        }
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
        if (!$ph) return FALSE;

        $enabled = FALSE;
        if (chop(fgets($ph)) == Firewall::CONSTANT_ON) $enabled = TRUE;
        if (pclose($ph) != 0) return FALSE;

        return $enabled;
    }

    /** 
     * Checks firewall mode
     * 
     * If set to DMZ with MultiWAN and no source-based
     * routes for DMZ networks found, display warning...
     */

    public function sanity_check_dmz($link = TRUE)
    {
        clearos_profile(__METHOD__, __LINE__);

        // TODO: review an re-enable if necessary

        $firewall = new Firewall();

        try {
            switch ($firewall->GetMode()) {
            case Firewall::CONSTANT_AUTO:
            case Firewall::CONSTANT_GATEWAY:
                break;
            default:
                return;
            }

            $dmzif = $firewall->GetInterfaceDefinition(Firewall::CONSTANT_DMZ);
            if (empty($dmzif))
                return;

            $networks = array();
            foreach ($dmzif as $iface) {
                $ifn = new Iface($iface);
                $info = $ifn->GetInterfaceInfo();
                $networks[$iface]['network'] =
                    long2ip(ip2long($info['address']) & ip2long($info['netmask']));
                $networks[$iface]['netmask'] =
                    substr_count(decbin(ip2long($info['netmask'])), '1');
            }

            $rules = $firewall->get_rules();
            foreach ($rules as $rule) {
                if (! ($rule->get_flags() & Rule::ENABLED)) continue;
                if (! ($rule->get_flags() & Rule::SBR_HOST)) continue;
                if (($slash = strpos($rule->get_address(), '/')) === FALSE) continue;

                $network = substr($rule->get_address(), 0, $slash);
                $netmask = substr($rule->get_address(), $slash + 1);

                foreach ($networks as $iface => $dmznet) {
                    if ($dmznet['network'] != $network) continue;
                    unset($networks[$iface]);
                }
            }

            if (count($networks)) {
                $warning = FIREWALLMULTIWAN_LANG_DMZ_WARNING . '<br><ul>';
                foreach($networks as $iface => $network) {
                    $warning .= "<li>$iface: " . $network['network'];
                    $warning .= '/' . $network['netmask'] . '</li>';
                }
                $warning .= '</ul>';
                if ($link) {
                    $warning . FIREWALLMULTIWAN_LANG_DMZ_SBR . ' &#160; ';
                    $warning .= WebButtonContinue("AddSourceBasedRoute");

                    WebFormOpen();
                }

                echo WebDialogWarning($warning);

                if ($link) WebFormClose();
            }
        } catch (Exception $e) {
            echo WebDialogWarning(clearos_exception_message($e));
        }
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
