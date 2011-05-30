<?php
/* vim: set ts=4 sw=4 sts=4 et: */
/*****************************************************************************\
+-----------------------------------------------------------------------------+
| X-Cart                                                                      |
| Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>                  |
| All rights reserved.                                                        |
+-----------------------------------------------------------------------------+
| PLEASE READ  THE FULL TEXT OF SOFTWARE LICENSE AGREEMENT IN THE "COPYRIGHT" |
| FILE PROVIDED WITH THIS DISTRIBUTION. THE AGREEMENT TEXT IS ALSO AVAILABLE  |
| AT THE FOLLOWING URL: http://www.x-cart.com/license.php                     |
|                                                                             |
| THIS  AGREEMENT  EXPRESSES  THE  TERMS  AND CONDITIONS ON WHICH YOU MAY USE |
| THIS SOFTWARE   PROGRAM   AND  ASSOCIATED  DOCUMENTATION   THAT  RUSLAN  R. |
| FAZLYEV (hereinafter  referred to as "THE AUTHOR") IS FURNISHING  OR MAKING |
| AVAILABLE TO YOU WITH  THIS  AGREEMENT  (COLLECTIVELY,  THE  "SOFTWARE").   |
| PLEASE   REVIEW   THE  TERMS  AND   CONDITIONS  OF  THIS  LICENSE AGREEMENT |
| CAREFULLY   BEFORE   INSTALLING   OR  USING  THE  SOFTWARE.  BY INSTALLING, |
| COPYING   OR   OTHERWISE   USING   THE   SOFTWARE,  YOU  AND  YOUR  COMPANY |
| (COLLECTIVELY,  "YOU")  ARE  ACCEPTING  AND AGREEING  TO  THE TERMS OF THIS |
| LICENSE   AGREEMENT.   IF  YOU    ARE  NOT  WILLING   TO  BE  BOUND BY THIS |
| AGREEMENT, DO  NOT INSTALL OR USE THE SOFTWARE.  VARIOUS   COPYRIGHTS   AND |
| OTHER   INTELLECTUAL   PROPERTY   RIGHTS    PROTECT   THE   SOFTWARE.  THIS |
| AGREEMENT IS A LICENSE AGREEMENT THAT GIVES  YOU  LIMITED  RIGHTS   TO  USE |
| THE  SOFTWARE   AND  NOT  AN  AGREEMENT  FOR SALE OR FOR  TRANSFER OF TITLE.|
| THE AUTHOR RETAINS ALL RIGHTS NOT EXPRESSLY GRANTED BY THIS AGREEMENT.      |
|                                                                             |
| The Initial Developer of the Original Code is Ruslan R. Fazlyev             |
| Portions created by Ruslan R. Fazlyev are Copyright (C) 2001-2011           |
| Ruslan R. Fazlyev. All Rights Reserved.                                     |
+-----------------------------------------------------------------------------+
\*****************************************************************************/

/**
 * Templater plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     html_select_date
 * Purpose:  Prints the dropdowns for date selection.
 * -------------------------------------------------------------
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: function.html_select_date.php,v 1.21.2.1 2011/01/10 13:11:53 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../../../"); die("Access denied"); }

function smarty_function_html_select_date($params, &$smarty)
{
    require_once $smarty->_get_plugin_filepath('shared','escape_special_chars');
    require_once $smarty->_get_plugin_filepath('shared','make_timestamp');
    require_once $smarty->_get_plugin_filepath('function','html_options');
    /* Default values. */
    $prefix          = 'Date_';
    $start_year      = strftime("%Y");
    $end_year        = $start_year;
    $display_days    = true;
    $display_months  = true;
    $display_years   = true;
    $month_format    = "%B";
    /* Write months as numbers by default  GL */
    $month_value_format = "%m";
    $day_format      = "%02d";
    /* Write day values using this format MB */
    $day_value_format = "%d";
    $year_as_text    = false;
    /* Display years in reverse order? Ie. 2000,1999,.... */
    $reverse_years   = false;
    /* Should the select boxes be part of an array when returned from PHP?
     e.g. setting it to 'birthday', would create "birthday[Day]",
     "birthday[Month]" & "birthday[Year]". Can be combined with prefix */
    $field_array     = null;
    /* <select size>'s of the different <select> tags.
     If not set, uses default dropdown. */
    $day_size        = null;
    $month_size      = null;
    $year_size       = null;
    /* Unparsed attributes common to *ALL* the <select>/<input> tags.
     An example might be in the template: all_extra ='class ="foo"'. */
    $all_extra       = null;
    /* Separate attributes for the tags. */
    $day_extra       = null;
    $month_extra     = null;
    $year_extra      = null;
    /* Order in which to display the fields.
     'D' -> day, 'M' -> month, 'Y' -> year. */
    $field_order     = 'MDY';
    /* String printed between the different fields. */
    $field_separator = "\n";
    $time = XC_TIME;
    $all_empty       = null;
    $day_empty       = null;
    $month_empty     = null;
    $year_empty      = null;
    $extra_attrs     = '';

    foreach ($params as $_key=>$_value) {
        switch ($_key) {
        case 'prefix':
        case 'time':
        case 'start_year':
        case 'end_year':
        case 'month_format':
        case 'day_format':
        case 'day_value_format':
        case 'field_array':
        case 'day_size':
        case 'month_size':
        case 'year_size':
        case 'all_extra':
        case 'day_extra':
        case 'month_extra':
        case 'year_extra':
        case 'field_order':
        case 'field_separator':
        case 'month_value_format':
        case 'month_empty':
        case 'day_empty':
        case 'year_empty':
            $$_key = (string)$_value;
            break;

        case 'all_empty':
            $$_key = (string)$_value;
            $day_empty = $month_empty = $year_empty = $all_empty;
            break;

        case 'display_days':
        case 'display_months':
        case 'display_years':
        case 'year_as_text':
        case 'reverse_years':
        case 'is_native':
        case 'use_unique_key':
            $$_key = (bool)$_value;
            break;

        default:
             if(!is_array($_value)) {
                $extra_attrs .= ' '.$_key.'="'.smarty_function_escape_special_chars($_value).'"';
            } else {
                $smarty->trigger_error("html_select_date: extra attribute '$_key' cannot be an array", E_USER_NOTICE);
            }
        }
    }

    if (preg_match('!^-\d+$!',$time)) {
        // negative timestamp, use date()
        $time = date('Y-m-d',$time);
    }

    if ($use_unique_key)
        $unique_key = '['.func_get_uid().']';
    else
        $unique_key = '';

    // If $time is not in format yyyy-mm-dd
    if (!preg_match('/^\d{0,4}-\d{0,2}-\d{0,2}$/S', $time)) {
        // then $time is empty or unix timestamp or mysql timestamp
        // using smarty_make_timestamp to get an unix timestamp and
        // strftime to make yyyy-mm-dd
        $time = strftime('%Y-%m-%d', smarty_make_timestamp($time));
    }
    // Now split this in pieces, which later can be used to set the select
    $time = explode("-", $time);

    // make syntax "+N" or "-N" work with start_year and end_year
    if (preg_match('!^(\+|\-)\s*(\d+)$!S', $end_year, $match)) {
        if ($match[1] == '+') {
            $end_year = strftime('%Y') + $match[2];
        } else {
            $end_year = strftime('%Y') - $match[2];
        }
    }
    if (preg_match('!^(\+|\-)\s*(\d+)$!S', $start_year, $match)) {
        if ($match[1] == '+') {
            $start_year = strftime('%Y') + $match[2];
        } else {
            $start_year = strftime('%Y') - $match[2];
        }
    }
    if (strlen($time[0]) > 0) {
        if ($start_year > $time[0] && !isset($params['start_year'])) {
            // force start year to include given date if not explicitly set
            $start_year = $time[0];
        }
        if ($end_year < $time[0] && !isset($params['end_year'])) {
            // force end year to include given date if not explicitly set
            $end_year = $time[0];
        }
    }

    $field_order = strtoupper($field_order);

    $html_result = $month_result = $day_result = $year_result = '';

    $is_abbr = $is_fullname = false;

    if ($display_months) {
        $month_names = array();
        $month_values = array();
        if(isset($month_empty)) {
            $month_names[''] = $month_empty;
            $month_values[''] = '';
        }

        if (!$is_native) {
            $is_fullname = (strpos($month_format, "%B") !== false);
            $is_abbr = (strpos($month_format, "%b") !== false);
            $month_format_orig = $month_format;
            $month_format = str_replace(array("%B","%b"), array('',''), $month_format);
        }

        for ($i = 1; $i <= 12; $i++) {
            if (!$is_native) {
                if ($is_fullname) {
                    $month_names[$i] = str_replace("%B", strftime(func_get_langvar_by_name('lbl_month_fullname_'.$i, array(), false, false, true), mktime(0, 0, 0, $i, 1, 2000)), $month_format_orig);
                }
                if ($is_abbr) {
                    $month_names[$i] = str_replace("%b", strftime(func_get_langvar_by_name('lbl_month_abbr_'.$i, array(), false, false, true), mktime(0, 0, 0, $i, 1, 2000)), $month_format_orig);;
                }
            }
            if (!empty($month_format)) {
                $month_names[$i] = strftime($month_format, mktime(0, 0, 0, $i, 1, 2000));
            }
            $month_values[$i] = strftime($month_value_format, mktime(0, 0, 0, $i, 1, 2000));
        }

        $month_result .= '<select name=';
        if (null !== $field_array) {
            $month_result .= '"' . $field_array . '[' . $prefix . 'Month'.$unique_key.']"';
        } else {
            $month_result .= '"' . $prefix . 'Month'.$unique_key.'"';
        }
        if (null !== $month_size) {
            $month_result .= ' size="' . $month_size . '"';
        }
        if (null !== $month_extra) {
            $month_result .= ' ' . $month_extra;
         }
        if (null !== $all_extra) {
            $month_result .= ' ' . $all_extra;
        }
        $month_result .= '>'."\n";

        $month_result .= smarty_function_html_options(
            array('output'     => $month_names,
                  'values'     => $month_values,
                  'selected'   => (int)$time[1] ? strftime($month_value_format, mktime(0, 0, 0, (int)$time[1], 1, 2000)) : '',
                  'print_result' => false),
            $smarty);
        $month_result .= '</select>';
    }

    if ($display_days) {
        $days = array();
        if (isset($day_empty)) {
            $days[''] = $day_empty;
            $day_values[''] = '';
        }
        for ($i = 1; $i <= 31; $i++) {
            $days[] = sprintf($day_format, $i);
            $day_values[] = sprintf($day_value_format, $i);
        }

        $day_result .= '<select name=';
        if (null !== $field_array){
            $day_result .= '"' . $field_array . '[' . $prefix . 'Day'.$unique_key.']"';
        } else {
            $day_result .= '"' . $prefix . 'Day'.$unique_key.'"';
        }
        if (null !== $day_size){
            $day_result .= ' size="' . $day_size . '"';
        }
        if (null !== $all_extra){
            $day_result .= ' ' . $all_extra;
        }
        if (null !== $day_extra){
            $day_result .= ' ' . $day_extra;
        }
        $day_result .= '>'."\n";
        $day_result .= smarty_function_html_options(
            array('output'     => $days,
                  'values'     => $day_values,
                  'selected'   => $time[2],
                  'print_result' => false),
            $smarty);
        $day_result .= '</select>';
    }

    if ($display_years) {
        if (null !== $field_array){
            $year_name = $field_array . '[' . $prefix . 'Year]';
        } else {
            $year_name = $prefix . 'Year';
        }
        if ($year_as_text) {
            $year_result .= '<input type="text" name="' . $year_name.$unique_key . '" value="' . $time[0] . '" size="4" maxlength="4"';
            if (null !== $all_extra){
                $year_result .= ' ' . $all_extra;
            }
            if (null !== $year_extra){
                $year_result .= ' ' . $year_extra;
            }
            $year_result .= '>';
        } else {
            $years = range((int)$start_year, (int)$end_year);
            if ($reverse_years) {
                rsort($years, SORT_NUMERIC);
            } else {
                sort($years, SORT_NUMERIC);
            }
            $yearvals = $years;
            if(isset($year_empty)) {
                array_unshift($years, $year_empty);
                array_unshift($yearvals, '');
            }
            $year_result .= '<select name="' . $year_name.$unique_key . '"';
            if (null !== $year_size){
                $year_result .= ' size="' . $year_size . '"';
            }
            if (null !== $all_extra){
                $year_result .= ' ' . $all_extra;
            }
            if (null !== $year_extra){
                $year_result .= ' ' . $year_extra;
            }
            $year_result .= '>'."\n";
            $year_result .= smarty_function_html_options(
                array('output' => $years,
                      'values' => $yearvals,
                      'selected'   => $time[0],
                      'print_result' => false),
                $smarty);
            $year_result .= '</select>';
        }
    }

    // Loop thru the field_order field
    for ($i = 0; $i <= 2; $i++){
        $c = substr($field_order, $i, 1);
        switch ($c){
        case 'D':
            $html_result .= $day_result;
            break;

        case 'M':
            $html_result .= $month_result;
            break;

        case 'Y':
            $html_result .= $year_result;
            break;
        }
        // Add the field seperator
        if($i != 2) {
            $html_result .= $field_separator;
        }
    }

    return $html_result;
}

?>
