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
 * Fields management to display on the contact us page
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Admin interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: contact_us_profiles.php,v 1.24.2.1 2011/01/10 13:11:45 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load('backoffice', 'user');

/**
 * Serialized arrays:
 * Standart fields descriptions and statuses:
 * $config['Contact_Us']['contact_us_fields']
 *   array:
 *       field = field_name
 *       avail = 'BCP'
 *       required = 'BCP'
 */

if($mode == 'update_status' && $REQUEST_METHOD == 'POST') {
    $tmp = array();
    if($default_data) {
        foreach($default_data as $k => $v) {
            $tmp[] = array('field' => $k, 'avail' => @implode('', @array_keys($v['avail'])), "required" => @implode("", @array_keys($v['required'])));
        }
    }
    $tmp_string = addslashes(serialize($tmp));
    x_log_flag('log_activity', 'ACTIVITY', "'$login' user has changed 'Contact us::contact_us_fields' option to '$tmp_string'");
    db_query("REPLACE INTO $sql_tbl[config] (name, value, category) VALUES ('contact_us_fields', '$tmp_string', 'Contact_Us')");
    db_query("UPDATE $sql_tbl[contact_fields] SET avail = '', required = ''");
    if($add_data) {
        foreach($add_data as $k => $v) {
            db_query("UPDATE $sql_tbl[contact_fields] SET avail = '".@implode("", @array_keys($v['avail']))."', required = '".@implode("", @array_keys($v['required']))."' WHERE fieldid = '$k'");
        }
    }
}
elseif ($mode == 'update_fields' && $REQUEST_METHOD == 'POST') {
    if ($update) {
        foreach ($update as $k => $v) {
            func_languages_alt_insert('lbl_contact_field_'.$k, $v['field'], $current_language);
            unset($v['field']);
            if ($v['type'] == 'S' && $v['variants']) {
                $variants = func_query_first_cell("SELECT variants FROM $sql_tbl[contact_fields] WHERE fieldid = '$k'");
                if (!empty($variants))
                    $variants = unserialize($variants);
                if (!is_array($variants))
                    $variants = array();

                $variants[$current_language] = implode(";", array_filter(explode(";", stripslashes($v['variants'])), "func_callback_empty"));
                $v['variants'] = addslashes(serialize($variants));

            } else
                $v['variants'] = '';

            func_array2update('contact_fields', $v, "fieldid = '$k'");
        }
    }

    if ($newfield && (($newfield_variants && $newfield_type == 'S') || $newfield_type != 'S')) {
        if (!$newfield_orderby)
            $newfield_orderby = func_query_first_cell("SELECT MAX(orderby) FROM $sql_tbl[contact_fields]")+1;
        if ($newfield_type == 'S')
            $newfield_variants = addslashes(serialize(array($current_language => implode(";", array_filter(explode(";", stripslashes($newfield_variants)), 'func_callback_empty')))));
        else
            $newfield_variants = '';

        db_query("INSERT INTO $sql_tbl[contact_fields] (field, type, orderby, variants) VALUES ('$newfield', '$newfield_type', '$newfield_orderby', '$newfield_variants')");
        $id = db_insert_id();
        func_languages_alt_insert('lbl_contact_field_'.$id, $newfield);
    }
}
elseif ($mode == 'delete' && $REQUEST_METHOD == 'POST' && $fields) {
    db_query("DELETE FROM $sql_tbl[contact_fields] WHERE fieldid IN ('".implode("', '", array_keys($fields))."')");
    db_query("DELETE FROM $sql_tbl[languages_alt] WHERE SUBSTRING(name, 20) IN ('".implode("', '", array_keys($fields))."') AND name LIKE 'lbl_contact_field_%'");
}

if ($mode) {
    func_header_location("configuration.php?option=Contact_Us");
}

foreach ($default_contact_us_fields as $k=>$v) {
    $default_contact_us_fields[$k]['title'] = func_get_default_field($k);
}

$usertypes_array = array('C' => '');
if (!empty($active_modules['XAffiliate']))
    $usertypes_array['B'] = "";

if (empty($active_modules['Simple_Mode']))
    $usertypes_array['P'] = "";

$default_fields = unserialize($config['Contact_Us']['contact_us_fields']);
if (!$default_fields) {
    $default_fields = array();
    $enabled_field = array('B' => 'Y', "C" => 'Y', "P" => 'Y');
    foreach ($default_contact_us_fields as $k => $v) {
        $default_fields[] = array(
            'title'     => $v['title'],
            'field'     => $k,
            'avail'     => ($v['avail'] == 'Y' ? $enabled_field : $v['avail']),
            'required'  => ($v['required'] == 'Y' ? $enabled_field : $v['required'])
        );
    }
}
else {
    foreach ($default_fields as $k => $v) {
        $v['title'] = func_get_default_field($v['field']);
        $v['avail'] = func_keys2hash($v['avail']);
        $v['required'] = func_keys2hash($v['required']);
        $default_fields[$k] = $v;
    }
}

$additional_fields = func_query("SELECT * FROM $sql_tbl[contact_fields] ORDER BY orderby");
if (!empty($additional_fields)) {
    foreach ($additional_fields as $k => $v) {
        // Compatibility with old XC 4.1.8 format
        if ($v['variants']) {
            $probe = @unserialize($v['variants']);
            if (!is_array($probe) || count($probe) <= 0) {
                $v['variants'] = @serialize(array($current_language => $v['variants']));
            }
        }

        $additional_fields[$k]['title'] = func_get_languages_alt("lbl_contact_field_".$v['fieldid']);
        $additional_fields[$k]['avail'] = func_keys2hash($v['avail']);
        $additional_fields[$k]['required'] = func_keys2hash($v['required']);

        if ($v['type'] == 'S' && !empty($v['variants'])) {
            $v['variants'] = unserialize($v['variants']);
            if (is_array($v['variants']) && !empty($v['variants'])) {
                if (!empty($v['variants'][$shop_language])) {
                    $additional_fields[$k]['variants'] = $v['variants'][$shop_language];

                } elseif (!empty($v['variants'][$config['default_admin_language']])) {
                    $additional_fields[$k]['variants'] = $v['variants'][$config['default_admin_language']];

                } else {
                    $key = key($v['variants']);
                    $additional_fields[$k]['variants'] = empty($key) ? '' : $v['variants'][$key];
                }

            } else {
                $additional_fields[$k] = '';
            }
        }
    }

    $smarty->assign('additional_fields', $additional_fields);
}

$smarty->assign('default_fields', $default_fields);

$smarty->assign('usertypes_array', $usertypes_array);

$smarty->assign('col_width', floor(80 / count($usertypes_array)));
$smarty->assign('colspan', count($usertypes_array) * 2 + 1);

// Field types
$types = array(
    'T' => 'Text',
    'C' => 'Checkbox',
    'S' => "Select box",
);
$smarty->assign('sections', $sections);
$smarty->assign('types', $types);

?>
