<?php

/**
 * Project:     SmartyValidate: Form Validator for the Smarty Template Engine
 * File:        SmartyValidate.class.php
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @link http://www.phpinsider.com/php/code/SmartyValidate/
 * @copyright 2001-2004 ispi of Lincoln, Inc.
 * @author Monte Ohrt <monte@ispi.net>
 * @package SmartyValidate
 * @version 1.6
 */


class SmartyValidate {

    /**
     * Class Constructor
     */
    function SmartyValidate() { }
    
    /**
     * initialize the session data
     *
     * @param string $form the name of the form being validated
     */
    function init($form = 'default') {    
        $_SESSION['SmartyValidate'][$form] = array();
        $_SESSION['SmartyValidate'][$form]['registered_criteria'] = array();
        $_SESSION['SmartyValidate'][$form]['registered_transform'] = array('trim');
        $_SESSION['SmartyValidate'][$form]['validators'] = array();
    }

    /**
     * clear (uninitialize) the session data
     *
     * @param string $form the name of the form being validated
     */
    function clear($form = 'default') {
        $_SESSION['SmartyValidate'][$form] = null;
    }    

    /**
     * test if the session data is initialized
     *
     * @param string $form the name of the form being validated
     */
    function is_init($form = 'default') {    
        return isset($_SESSION['SmartyValidate'][$form]);
    }    
            
    /**
     * validate the form
     *
     * @param string $formvars the array of submitted for variables
     * @param string $form the name of the form being validated
     */
    function is_valid(&$formvars, $form = 'default') {
        
        if(!SmartyValidate::is_init($form)) {
            trigger_error("SmartyValidate: [is_valid] form '$form' is not initialized.");
            return false;
        }
        
        // keep track of failed fields for current pass
        static $_failed_fields = array();
        
        $_ret = true;
        $_sess =& $_SESSION['SmartyValidate'][$form]['validators'];
        foreach($_sess as $_key => $_val) {
            $_field = $_sess[$_key]['field'];
            $_empty = $_sess[$_key]['empty'];
            
            if(in_array($_field, $_failed_fields)) {
                // already failed, skip this test
                continue;   
            }
            
            if($_sess[$_key]['trim']) {
                $formvars[$_field] = trim($formvars[$_field]);
            }
            if(isset($_sess[$_key]['transform'])) {
                $_trans_funcs = preg_split('![\s,]+!', $_sess[$_key]['transform'], -1, PREG_SPLIT_NO_EMPTY);
                foreach($_trans_funcs as $_trans_func) {
                    if(SmartyValidate::is_registered_transform($_trans_func, $form)) {
                        $formvars[$_field] = $_trans_func($formvars[$_field]);
                    } else {
                        trigger_error("SmartyValidate: [transform] function '$_trans_func' is not registered.");                        
                    }
                }
            }
            if(!isset($formvars[$_field])) {
                // field must exist, or else fails automatically
                $_sess[$_key]['valid'] = false;
                $_ret = false;
            } else {
                switch($_val['criteria']) {
                    case 'notEmpty':
                        $_sess[$_key]['valid'] =
                            SmartyValidate::_not_empty($formvars[$_field]);
                        break;
                    case 'isInt':
                        $_sess[$_key]['valid'] =
                            SmartyValidate::_is_int($formvars[$_field], $_empty);
                        break;
                    case 'isFloat':
                        $_sess[$_key]['valid'] =
                            SmartyValidate::_is_float($formvars[$_field], $_empty);
                        break;
                    case 'isNumber':
                        $_sess[$_key]['valid'] =
                            SmartyValidate::_is_number($formvars[$_field], $_empty);
                        break;
                    case 'isPrice':
                        $_sess[$_key]['valid'] =
                            SmartyValidate::_is_price($formvars[$_field], $_empty);
                        break;
                    case 'isEmail':
                        $_sess[$_key]['valid'] =
                            SmartyValidate::_is_email($formvars[$_field], $_empty);
                        break;
                    case 'isCCNum':
                        $_sess[$_key]['valid'] =
                            SmartyValidate::_is_cc_num($formvars[$_field], $_empty);
                        break;
                    case 'isCCExpDate':
                        $_sess[$_key]['valid'] =
                            SmartyValidate::_is_cc_exp_date($formvars[$_field], $_empty);
                        break;
                    case 'isDate':
                        $_sess[$_key]['valid'] =
                            SmartyValidate::_is_date($formvars[$_field], $_empty);
                        break;
                    case 'isEqual':
                        $_sess[$_key]['valid'] =
                            SmartyValidate::_is_equal(
                                $formvars[$_field],
                                $formvars[$_sess[$_key]['field2']],
                                $_empty);
                        break;
                    case 'isRange':
                        $_sess[$_key]['valid'] =
                            SmartyValidate::_is_range(
                                $formvars[$_field],
                                $_sess[$_key]['low'],
                                $_sess[$_key]['high'],
                                $_empty);
                        break;
                    case 'isLength':
                        $_sess[$_key]['valid'] =
                            SmartyValidate::_is_length(
                                $formvars[$_field],
                                $_sess[$_key]['min'],
                                $_sess[$_key]['max'],
                                $_empty);
                        break;
                    case 'isRegExp':
                        $_sess[$_key]['valid'] =
                            SmartyValidate::_is_regexp(
                                $formvars[$_field],
                                $_sess[$_key]['expression'],
                                $_empty);
                        break;
                    case 'isCustom':
                        $_sess[$_key]['valid'] =
                            SmartyValidate::_is_custom(
                                $form,
                                $formvars[$_field],
                                $_sess[$_key]['function'],
                                $_empty,
                                $_sess[$_key],
                                $formvars);
                        break;
                }
            }
            if(!$_sess[$_key]['valid']) {
                $_failed_fields[] = $_field;
                $_ret = false;
            }
        }
        return $_ret;
    }
    
    /**
     * wrapper to register_criteria, this function is deprecated
     *
     * @param string $func_name the function being registered
     */
    function register_function($func_name, $form = 'default') {
        return SmartyValidate::register_criteria($func_name, $form);
    }

    /**
     * register a callable function for form verification
     *
     * @param string $func_name the function being registered
     */
    function register_criteria($func_name, $form = 'default') {
        if(!SmartyValidate::is_init($form)) {
            trigger_error("SmartyValidate: [register_criteria] form '$form' is not initialized.");
            return false;
        }
        if(!function_exists($func_name)) {
            trigger_error("SmartyValidate: [register_criteria] function '$function' does not exist.");
            return false;
        }
        if(!in_array($func_name, $_SESSION['SmartyValidate'][$form]['registered_criteria']))
            $_SESSION['SmartyValidate'][$form]['registered_criteria'][] = $func_name;
        return true;
    }

    /**
     * register a callable function for form verification
     *
     * @param string $func_name the function being registered
     */
    function register_transform($func_name, $form = 'default') {
        if(!SmartyValidate::is_init($form)) {
            trigger_error("SmartyValidate: [register_transform] form '$form' is not initialized.");
            return false;
        }
        if(!function_exists($func_name)) {
            trigger_error("SmartyValidate: [register_transform] function '$func_name' does not exist.");
            return false;
        }
        if(!in_array($func_name, $_SESSION['SmartyValidate'][$form]['registered_transform']))
            $_SESSION['SmartyValidate'][$form]['registered_transform'][] = $func_name;
        return true;
    }    
        
    /**
     * test if a criteria function is registered
     *
     * @param string $var the value being booleanized
     */
    function is_registered_criteria($func_name, $form = 'default') {  
        if(!SmartyValidate::is_init($form)) {
            trigger_error("SmartyValidate: [is_registered_criteria] form '$form' is not initialized.");
            return false;
        }
        return in_array($func_name, $_SESSION['SmartyValidate'][$form]['registered_criteria']);
    }

    /**
     * test if a tranform function is registered
     *
     * @param string $var the value being booleanized
     */
    function is_registered_transform($func_name, $form = 'default') {
        if(!SmartyValidate::is_init($form)) {
            trigger_error("SmartyValidate: [is_registered_transform] form '$form' is not initialized.");
            return false;
        }
        return in_array($func_name, $_SESSION['SmartyValidate'][$form]['registered_transform']);
    }    
    
    /**
     * booleanize a value
     *
     * @param string $var the value being booleanized
     */
    function booleanize($var) {
        if(in_array(strtolower($var), array(true, 1, 'true','on','yes','y'))) {
            return true;
        }
        return false;
    }
    
    /**
     * test if a value is not empty
     *
     * @param string $value the value being tested
     */
    function _not_empty($value) {
        return (strlen($value) > 0);
    }
    
    /**
     * test if a value is an integer
     *
     * @param string $value the value being tested
     * @param boolean $empty if field can be empty
     */
    function _is_int($value, $empty = false) {
        
        if(strlen($value) == 0)
            return $empty;        
        
        return preg_match('!^\d+$!', $value);
    }
    
    /**
     * test if a value is a float
     *
     * @param string $value the value being tested
     * @param boolean $empty if field can be empty
     */
    function _is_float($value, $empty = false) {
        
        if(strlen($value) == 0)
            return $empty;
        
        return preg_match('!^\d+(\.\d+)?$!', $value);
    }

    /**
     * test if a value is a valid number (int of float)
     *
     * @param string $value the value being tested
     * @param boolean $empty if field can be empty
     */
    function _is_number($value, $empty = false) {
        
        if(strlen($value) == 0)
            return $empty;        
        
        return (SmartyValidate::_is_int($value) || SmartyValidate::_is_float($value));
    }
    
    /**
     * test if a value is a price
     *
     * @param string $value the value being tested
     * @param boolean $empty if field can be empty
     */
    function _is_price($value, $empty = false) {
        
        if(strlen($value) == 0)
            return $empty;        
        
        return (preg_match('/^\d+(\.\d{1,2})?$/', $value));
    }

     /**
     * test if a value is a valid e-mail address
     *
     * @param string $value the value being tested
     * @param boolean $empty if field can be empty
     */
    function _is_email($value, $empty = false) {

        if(strlen($value) == 0)
            return $empty;
        
		return !(preg_match('!@.*@|\.\.|\,!', $value) ||
            !preg_match('!^.+\@(\[?)[a-zA-Z0-9\.\-]+\.([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$!', $value));
    }

     /**
     * test if a value is a valid credit card checksum
     *
     * @param string $value the value being tested
     * @param boolean $empty if field can be empty
     */
    function _is_cc_num($value, $empty = false) {

        if(strlen($value) == 0)
            return $empty;

		// strip everything but digits
		$value = preg_replace('!\D+!', '', $value);

		if (empty($value))
			return false;

		$_c_digits = preg_split('//', $value, -1, PREG_SPLIT_NO_EMPTY);

		$_max_digit   = count($_c_digits)-1;
		$_even_odd    = $_max_digit % 2;

		$_sum = 0;
		for ($_count=0; $_count <= $_max_digit; $_count++) {
			$_digit = $_c_digits[$_count];
			if ($_even_odd) {
				$_digit = $_digit * 2;
				if ($_digit > 9) {
					$_digit = substr($_digit, 1, 1) + 1;
				}
			}
			$_even_odd = 1 - $_even_odd;
			$_sum += $_digit;
		}
		$_sum = $_sum % 10;
		if($_sum)
			return false;
		return true;
    }

     /**
     * test if a value is a valid credit card expiration date
     *
     * @param string $value the value being tested
     * @param boolean $empty if field can be empty
     */
    function _is_cc_exp_date($value, $empty = false) {
        
        if(strlen($value) == 0)
            return $empty;
                        
        if(!preg_match('!^(\d+)\D+(\d+)$!', $value, $_match))
            return false;
        
        $_month = $_match[1];
        $_year = $_match[2];
        
        if(strlen($_year) == 2)
            $_year = substr(date('Y', time()),0,2) . $_year;
        
		if(!SmartyValidate::_is_int($_month))
			return false;
		if($_month < 1 || $_month > 12)
			return false;
		if(!SmartyValidate::_is_int($_year))
			return false;
		if(date('Y',time()) > $_year)
			return false;
		if(date('Y',time()) == $_year && date('m', time()) > $_month)
			return false;

		return true;
    }

    /**
     * test if a value is a valid date (parsable by strtotime)
     *
     * @param string $value the value being tested
     * @param boolean $empty if field can be empty
     */
    function _is_date($value, $empty = false) {
        
        if(strlen($value) == 0)
            return $empty;
        
        return (strlen($value) == 0 || (strtotime($value) != -1 ));
        
    }

    /**
     * test if a value is a valid range
     *
     * @param string $value the value being tested
     * @param string $field2 the 2nd field to match against
     * @param boolean $empty if field can be empty
     */
    function _is_equal($value, $field2, $empty = false) {
        
//        if(strlen($value) == 0)
//            return $empty;
        
        return ($value == $field2);
        
    }

    /**
     * test if a value is a valid range
     *
     * @param string $value the value being tested
     * @param string $low the low value
     * @param string $high the high value
     * @param boolean $empty if field can be empty
     */
    function _is_range($value, $low, $high, $empty = false) {
        
        if(strlen($value) == 0)
            return $empty;
        
        return ($value >= $low && $value <= $high);
        
    }

    /**
     * test if a value is a valid range
     *
     * @param string $value the value being tested
     * @param string $low the low value
     * @param string $high the high value
     * @param boolean $empty if field can be empty
     */
    function _is_length($value, $min, $max, $empty = false) {
        
        $_length = strlen($value);
                
        if($_length >= $min && $_length <= $max)
            return true;
        elseif($_length == 0)
            return $empty;
        else
            return false;
        
    }

    /**
     * test if a value is a valid range
     *
     * @param string $value the value being tested
     * @param string $expresion the regular expression to match against
     * @param boolean $empty if field can be empty
     */
    function _is_regexp($value, $expression, $empty = false) {
        
        if(strlen($value) == 0)
            return $empty;
        
        return (preg_match($expression, $value));
        
    }

    /**
     * test if a value is a valid range
     *
     * @param string $value the value being tested
     * @param string $fuction the function to test against
     * @param boolean $empty if field can be empty
     */
    function _is_custom($form, $value, $function, $empty = false, &$params, &$formvars) {
        
        if(SmartyValidate::is_registered_criteria($function, $form)) {
            if(!function_exists($function)) {
                trigger_error("SmartyValidate: function '$function' does not exist.");
                return false;
            }
            return $function($value, $empty, $params, $formvars);
        } else {
            trigger_error("SmartyValidate: criteria function '$function' is not registered.");            
            return false;   
        }
    }                    
        
}

?>
