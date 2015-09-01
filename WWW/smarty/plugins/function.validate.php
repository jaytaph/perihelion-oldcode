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

function smarty_function_validate($params, &$smarty) {

    if (!class_exists('SmartyValidate')) {
        $smarty->trigger_error("validate: missing SmartyValidate class");
        return;
    }
    if (strlen($params['field']) == 0) {
        $smarty->trigger_error("validate: missing 'field' parameter");
        return;
    }
    if (strlen($params['criteria']) == 0) {
        $smarty->trigger_error("validate: missing 'criteria' parameter");
        return;
    }
    if(isset($params['trim'])) {
        $params['trim'] = SmartyValidate::booleanize($params['trim']);   
    } else {
        $params['trim'] = false;   
    }
    if(isset($params['empty'])) {
        $params['empty'] = SmartyValidate::booleanize($params['empty']);
    } else {
        $params['empty'] = false;   
    }
        
    switch($params['criteria']) {
        case 'notEmpty':
        case 'isInt':
        case 'isFloat':
        case 'isNumber':
        case 'isPrice':
        case 'isEmail':
        case 'isCCNum':
        case 'isCCExpDate':
        case 'isDate':
            break;
        case 'isEqual':
            if (strlen($params['field2']) == 0) {
                $smarty->trigger_error("validate: isEqual missing 'field2' parameter");
                return;
            }
            break;
        case 'isRange':
            if (strlen($params['low']) == 0) {
                $smarty->trigger_error("validate: missing 'low' parameter");
                return;
            }
            if (strlen($params['high']) == 0) {
                $smarty->trigger_error("validate: missing 'high' parameter");
                return;
            }            
            break;
        case 'isLength':
            if (strlen($params['min']) == 0) {
                $smarty->trigger_error("validate: missing 'min' parameter");
                return;
            }
            if (strlen($params['max']) == 0) {
                $smarty->trigger_error("validate: missing 'max' parameter");
                return;
            }            
            break;
        case 'isRegExp':
            if (strlen($params['expression']) == 0) {
                $smarty->trigger_error("validate: isRegExp missing 'expression' parameter");
                return;
            }            
            break;
        case 'isCustom':
            if (strlen($params['function']) == 0) {
                $smarty->trigger_error("validate: isCustom missing 'function' parameter");
                return;
            }
            if(!preg_match('!^\w+(::\w+)?$!', $params['function'])) {
                $smarty->trigger_error("validate: isCustom invalid 'function' parameter");
                return;                
            }
            break;
        default:
            $smarty->trigger_error("validate: unknown criteria '" . $params['criteria'] . "'");
            return;                
            break;   
    }
      
    $_form = isset($params['form']) ? $params['form'] : 'default';
    $_sess =& $_SESSION['SmartyValidate'][$_form]['validators'];
    
    $_found = false;
    if(isset($_sess) && is_array($_sess)) {
        foreach($_sess as $_key => $_field) {
            if($_field['field'] == $params['field']
                && $_field['criteria'] == $params['criteria']) { 
                // field exists
                $_found = true;
                if(isset($_sess[$_key]['valid'])
                        && !$_sess[$_key]['valid']) {
                    // not valid, show error and reset
                    $_echo = true;
                    if(isset($params['assign'])) {
                        $smarty->assign($params['assign'], $_sess[$_key]['message']);
                        $_echo = false;
                    }
                    if(isset($params['append'])) {
                        $smarty->append($params['append'], $_sess[$_key]['message']);
                        $_echo = false;
                    }
                    if($_echo) {
                        // no assign or append, so echo message
                        echo $_sess[$_key]['message'];
                    }
                    $_sess[$_key]['valid'] = null;
                    break;
                }
            }
        }
    }
    if(!$_found) {
        // create
        $_sess[] = $params;
    }   

}

?>
