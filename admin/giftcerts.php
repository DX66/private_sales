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
 * Gift certificates interface
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Admin interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: giftcerts.php,v 1.75.2.3 2011/01/25 09:43:11 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

require './auth.php';
require $xcart_dir.'/include/security.php';

x_load('order');

if (empty($mode)) $mode = '';

$location[] = array(func_get_langvar_by_name('lbl_gift_certificates'), 'giftcerts.php');

if ($REQUEST_METHOD == 'POST') {

    if (
        $mode == 'add_gc'
        || $mode == 'modify_gc'
        || $mode == 'preview'
    ) {

        $fill_error = (empty($purchaser) || empty($recipient));

        if (func_gc_wrong_template($gc_template)) {
            $gc_template = $config['Gift_Certificates']['default_giftcert_template'];
        }

        $giftcert = array(
            'purchaser' => stripslashes($purchaser),
            'recipient' => stripslashes($recipient),
            'message'   => stripslashes($message),
            'amount'    => $amount,
            'debit'     => $amount,
            'send_via'  => $send_via,
            'tpl_file'  => stripslashes($gc_template),
        );

        if ($send_via == 'E') {

            // Send via Email

            $fill_error = ($fill_error || empty($recipient_email));

            $giftcert['recipient_email'] = $recipient_email;

        } else {

            // Send via Postal Mail

            $has_states = func_is_display_states($recipient_country);

            $fill_error = (
                $fill_error
                || empty($recipient_firstname)
                || empty($recipient_lastname)
                || empty($recipient_address)
                || empty($recipient_city)
                || empty($recipient_zipcode)
                || (
                    empty($recipient_state)
                    && $has_states
                ) || empty($recipient_country)
                || (
                    empty($recipient_county)
                    && $has_states
                    && $config['General']['use_counties'] == 'Y'
                )
            );

            if (
                $config['General']['zip4_support'] == 'Y'
                && $recipient_country == 'US'
                && isset($recipient_zip4)
                && !empty($recipient_zip4)
            ) {
                $recipient_zip4 = substr(trim($recipient_zip4), 0, 4);
            }
            else {
                $recipient_zip4 = '';
            }

            $giftcert['recipient_firstname'] = stripslashes($recipient_firstname);
            $giftcert['recipient_lastname']  = stripslashes($recipient_lastname);
            $giftcert['recipient_address']   = stripslashes($recipient_address);
            $giftcert['recipient_city']      = stripslashes($recipient_city);
            $giftcert['recipient_zipcode']   = $recipient_zipcode;
            $giftcert['recipient_zip4']      = $recipient_zip4;
            $giftcert['recipient_county']    = $recipient_county;
            $giftcert['recipient_state']     = $recipient_state;
            $giftcert['recipient_country']   = $recipient_country;
            $giftcert['recipient_phone']     = $recipient_phone;

        }

        // If gcindex is empty - add
        // overwise - update

        if (!$fill_error) {

            if ($mode != 'preview') {

                $db_gc = $giftcert;

                foreach ($db_gc as $k=>$v) {

                    $db_gc[$k] = addslashes($v);

                }

            }

            if ($mode == 'add_gc') {

                $db_gc['gcid']     = $gcid = strtoupper(md5(uniqid(rand())));
                $db_gc['status']   = 'P';
                $db_gc['add_date'] = XC_TIME;

                func_array2insert('giftcerts', $db_gc);

                $top_message['content'] = func_get_langvar_by_name('msg_adm_gc_add');

            } elseif ($mode == 'preview') {

                if ($config['General']['use_counties'] == 'Y')
                    $giftcert['recipient_countyname'] = func_get_county($recipient_county);

                $giftcert['recipient_statename'] = func_get_state($recipient_state, $recipient_country);
                $giftcert['recipient_countryname'] = func_get_country($recipient_country);
                $giftcert['gcid'] = $gcid;

                $smarty->assign('giftcerts', array($giftcert));

                $charset = $smarty->get_template_vars('default_charset');
                $charset_text = ($charset)?"; charset=$charset":'';

                header("Content-Type: text/html$charset_text");
                header("Content-Disposition: inline; filename=giftcertificates.html");

                $_tmp_smarty_debug = $smarty->debugging;

                $smarty->debugging = false;

                func_display('modules/Gift_Certificates/gc_admin_print.tpl',$smarty);

                $smarty->debugging = $_tmp_smarty_debug;

                exit;

            } elseif ($gcid) {

                func_array2update(
                    'giftcerts',
                    $db_gc,
                    'gcid=\'' . $gcid . '\''
                );

                $top_message['content'] = func_get_langvar_by_name('msg_adm_gc_upd');

            }

            func_header_location('giftcerts.php');

        } else {

            $top_message['content'] = func_get_langvar_by_name('err_filling_form');
            $top_message['type'] = 'E';

        }

    } elseif ($mode == 'delete') {

        // Delete gift certificate

        if (!empty($gcids)) {
            $gcids = array_keys($gcids);

            if (func_query_first_cell("SELECT gcid FROM $sql_tbl[giftcerts] WHERE orderid='' AND gcid IN ('" .implode("' ,'", $gcids) . "')")) {

                $top_message['content'] = func_get_langvar_by_name('msg_adm_gcs_del');

                db_query("DELETE FROM $sql_tbl[giftcerts] WHERE orderid='' AND gcid IN ('".implode("' ,'", $gcids)."')");

            }

        }

        func_header_location('giftcerts.php');

    } elseif ($mode != 'print') {

        global $to_customer;

        $to_customer = $config['default_admin_language'];

        while (list($key,$val)=each($_POST)) {

            if (strstr($key, '-')) {

                list(
                    $field,
                    $gcid
                ) = explode('-', $key);

                if ($field == 'status') {

                    $res = func_query_first("SELECT * FROM $sql_tbl[giftcerts] WHERE gcid='$gcid'");

                    if (
                        $val=="A"
                        && $val!=$res['status']
                        && $res['send_via']=="E"
                    ) {
                        func_send_gc($config['Company']['orders_department'], $res);
                    }
                }

                db_query("UPDATE $sql_tbl[giftcerts] SET $field='$val' WHERE gcid='$gcid'");
            }
        }

        $top_message['content'] = func_get_langvar_by_name('msg_adm_gcs_upd');

        func_header_location('giftcerts.php' . (isset($navigation_page) ? '?page=' . $navigation_page : ''));

    }

}

if (
    $mode == 'add_gc'
    || $mode == 'modify_gc'
) {

    include $xcart_dir . '/include/countries.php';
    include $xcart_dir . '/include/states.php';

    if ($config['General']['use_counties'] == 'Y')
        include $xcart_dir . '/include/counties.php';

    $giftcert = func_query_first("SELECT * FROM $sql_tbl[giftcerts] where gcid='".@$gcid."'");

    if ($giftcert['send_via'] != 'E') {

        if ($config['General']['use_counties'] == 'Y')
            $giftcert['recipient_countyname'] = func_get_county($giftcert['recipient_county']);

        $giftcert['recipient_statename']   = func_get_state($giftcert['recipient_state'], $giftcert['recipient_country']);
        $giftcert['recipient_countryname'] = func_get_country($giftcert['recipient_country']);
    }

    $smarty->assign('giftcert', $giftcert);

    $gc_readonly = ('modify_gc' === $mode && 'P' !== $giftcert['status']) ? 'Y' : '';

    if (!$gc_readonly) {

        $smarty->assign('gc_templates', func_gc_get_templates($xcart_dir . $smarty_skin_dir));

    }

} elseif ($mode == 'print') {

    $giftcerts = false;

    if (!empty($gcids) && is_array($gcids)) {

        $tpl_cond = (!empty($tpl_file) ? " AND tpl_file='$tpl_file'" : '');

        $giftcerts = func_query("SELECT *, add_date+'".$config["Appearance"]["timezone_offset"]."' as add_date FROM $sql_tbl[giftcerts] WHERE send_via<>'E' AND gcid IN ('".implode("','", array_keys($gcids))."') ".$tpl_cond);

    }

    if (empty($giftcerts) || !is_array($giftcerts)) {

        $top_message['type'] = 'W';
        $top_message['content'] = func_get_langvar_by_name("msg_adm_warn_gc_sel");

        func_header_location('giftcerts.php');
    }

    foreach ($giftcerts as $k=>$v) {

        if ($config['General']['use_counties'] == 'Y')
            $giftcerts[$k]['recipient_countyname'] = func_get_county($v['recipient_county']);

        $giftcerts[$k]['recipient_statename']   = func_get_state($v['recipient_state'], $v['recipient_country']);
        $giftcerts[$k]['recipient_countryname'] = func_get_country($v['recipient_country']);

    }

    $smarty->assign('giftcerts',$giftcerts);

    $charset = $smarty->get_template_vars('default_charset');
    $charset_text = ($charset)?"; charset=$charset":'';

    header("Content-Type: text/html$charset_text");
    header("Content-Disposition: inline; filename=giftcertificates.html");

    $_tmp_smarty_debug = $smarty->debugging;
    $smarty->debugging = false;

    if (!empty($tpl_file)) {

        $css_file = preg_replace('!\.tpl$!', '.css', $tpl_file);

        if ($css_file != $tpl_file) {

            $smarty->assign('css_file', $css_file);;

        }

    }

    func_display('modules/Gift_Certificates/gc_admin_print.tpl',$smarty);

    $smarty->debugging = $_tmp_smarty_debug;

    exit;

} else {

    $expired_condition = $config['Gift_Certificates']['gc_show_expired'] == 'Y' ? '' : " AND status <>'E'";

    $objects_per_page = $config['Gift_Certificates']['gc_per_page_admin'];

    $query = "SELECT *, add_date+'" . $config["Appearance"]["timezone_offset"] . "' as add_date FROM $sql_tbl[giftcerts] WHERE 1 $expired_condition";

    $result = db_query($query);

    $total_items = db_num_rows($result);

    if ($total_items > 0) {

        include $xcart_dir . '/include/navigation.php';

    }

    $first_page = isset($first_page) ? $first_page : 0;

    $limit = ' LIMIT ' . $first_page . ', ' . $objects_per_page;

    $giftcerts = func_query($query . $limit);

    $show_print_button = false;

    if (is_array($giftcerts)) {

        foreach ($giftcerts as $k => $v) {

            if ('P' === $v['send_via']) {

                $show_print_button = true;

            }

            if (
                !empty($active_modules['RMA'])
                && empty($v['orderid'])
            ) {
                $return = func_query_first("SELECT * FROM $sql_tbl[returns] WHERE credit = '$v[gcid]'");

                if (!empty($return)) {

                    $giftcerts[$k]['return'] = $return;

                }
            }

            if ($v['orderid'] == 0) continue;

            $giftcerts[$k] = func_array_merge(
                $v,
                func_query_first("SELECT $sql_tbl[customers].id AS userid, $sql_tbl[customers].login AS existing_login, $sql_tbl[customers].usertype FROM $sql_tbl[customers], $sql_tbl[orders] WHERE $sql_tbl[customers].id=$sql_tbl[orders].userid AND $sql_tbl[orders].orderid='$v[orderid]'")
            );

        }

        $smarty->assign('giftcerts', $giftcerts);
        $smarty->assign('show_print_button', $show_print_button);
        $smarty->assign('navigation_script', 'giftcerts.php?mode=search');

    }

}

$smarty->assign('main',        'giftcerts');
$smarty->assign('allow_tpl',   ($current_area == 'A' || ($current_area == 'P' && !empty($active_modules['Simple_Mode']))));
$smarty->assign('gc_readonly', @$gc_readonly);

// Assign the current location line
$smarty->assign('location', $location);

if (
    file_exists($xcart_dir . '/modules/gold_display.php')
    && is_readable($xcart_dir . '/modules/gold_display.php')
) {
    include $xcart_dir . '/modules/gold_display.php';
}
func_display('admin/home.tpl',$smarty);
?>
