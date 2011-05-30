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
 * Extra fields management interface
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Provder interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: extra_fields.php,v 1.50.2.1 2011/01/10 13:12:08 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

define('IS_MULTILANGUAGE', true);
require './auth.php';
require $xcart_dir.'/include/security.php';

$location[] = array(func_get_langvar_by_name('lbl_extra_fields'), '');

$service_name_prefix = 'SERVICE_NAME';

/**
 * Use this condition when single mode is disabled
 */
$provider_condition = ($single_mode ? '' : "AND provider='$logged_userid'");

/**
 * Handle POST request
 */
if ($REQUEST_METHOD=="POST") {

    if ($mode == 'delete' && is_array($posted_data) && !empty($posted_data)) {

        // Delete field & associated info

        $deleted = false;
        $ids = array();
        foreach ($posted_data as $k=>$v) {
            if (empty($v['to_delete']))
                continue;
            $ids[] = $k;
        }

        $ids = func_query_column("SELECT fieldid FROM $sql_tbl[extra_fields] WHERE fieldid IN ('".implode("','", $ids)."') ".$provider_condition);
        if (!empty($ids)) {
            db_query("DELETE FROM $sql_tbl[extra_fields] WHERE fieldid IN ('".implode("','", $ids)."')");
            db_query("DELETE FROM $sql_tbl[extra_fields_lng] WHERE fieldid IN ('".implode("','", $ids)."')");
            db_query("DELETE FROM $sql_tbl[extra_field_values] WHERE fieldid IN ('".implode("','", $ids)."')");
            $deleted = true;
        }

        if ($deleted)
            $top_message['content'] = func_get_langvar_by_name('msg_extra_fields_del');

    } elseif ($mode == 'update' && !empty($posted_data) && is_array($posted_data)) {

        // Update the extra fields descriptions

        $active = false;
        foreach ($posted_data as $k => $v) {
            if (isset($v['to_delete']))
                unset($v['to_delete']);

            $v['service_name'] = trim($v['service_name']);
            if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[extra_fields] WHERE fieldid = '$k' $provider_condition") == 0) {
                continue;

            } elseif (($res = func_ef_check_service_name($v['service_name'], $logged_userid, $k)) !== true) {

                switch ($res) {
                    case 'empty':
                        $top_message['content'] = func_get_langvar_by_name('msg_err_extra_fields_upd_sname_empty');
                        break;
                    case 'name':
                        $top_message['content'] = func_get_langvar_by_name('msg_err_extra_fields_upd_sname_res');
                        break;
                    case 'format':
                        $top_message['content'] = func_get_langvar_by_name('msg_err_extra_fields_upd_sname_wrong');
                        break;
                    case 'duplicate':
                        $top_message['content'] = func_get_langvar_by_name('msg_err_extra_fields_upd_sname');
                        break;
                }

                if (!empty($top_message['content'])) {
                    $top_message['type'] = 'E';
                    func_header_location('extra_fields.php');
                }

            }

            $query_data = array(
                'fieldid' => $k,
                'code' => $shop_language,
                'field' => $v['field']
            );
            func_array2insert('extra_fields_lng', $query_data, true);
            if ($shop_language != $config['default_admin_language'])
                func_unset($v, 'field');

            $v['active'] = $v['active'];
            $v['provider'] = $logged_userid;
            func_array2update('extra_fields', $v, "fieldid = '$k'");
            $active = true;
        }
        if ($active)
            $top_message['content'] = func_get_langvar_by_name('msg_extra_fields_upd');

    } elseif ($mode == 'add' && !empty($new['field'])) {

        // Insert new extra field description

        if (($res = func_ef_check_service_name($new['service_name'], $logged_userid)) !== true) {

            switch ($res) {
                case 'empty':
                    $top_message['content'] = func_get_langvar_by_name('msg_err_extra_fields_upd_sname_empty');
                    break;
                case 'name':
                    $top_message['content'] = func_get_langvar_by_name('msg_err_extra_fields_upd_sname_res');
                    break;
                case 'format':
                    $top_message['content'] = func_get_langvar_by_name('msg_err_extra_fields_upd_sname_wrong');
                    break;
                case 'duplicate':
                    $top_message['content'] = func_get_langvar_by_name('msg_err_extra_fields_upd_sname');
                    break;
            }

            if (!empty($top_message['content'])) {
                $top_message['type'] = 'E';
                func_header_location('extra_fields.php');
            }

        }

        if (empty($new['orderby']))
            $new['orderby'] = func_query_first_cell("SELECT MAX(orderby) FROM $sql_tbl[extra_fields] WHERE 1 ".$provider_condition)+1;

        $new['active'] = $new['active'];
        $new['provider'] = $logged_userid;
        $fieldid = func_array2insert('extra_fields', $new);

        $query_data = array(
            'fieldid' => $fieldid,
            'code' => $shop_language,
            'field' => $new['field']
        );
        func_array2insert('extra_fields_lng', $query_data, true);

        $top_message['content'] = func_get_langvar_by_name('msg_extra_fields_add');

    }

    func_header_location('extra_fields.php');
}

$max_service_name = func_query_first_cell("SELECT MAX(SUBSTRING(service_name, ".(strlen($service_name_prefix)+1).")) FROM $sql_tbl[extra_fields] WHERE service_name LIKE '$service_name_prefix%'")+1;
if (strlen($max_service_name) < 2)
    $max_service_name = '0'.$max_service_name;

$smarty->assign('max_service_name', $service_name_prefix.$max_service_name);

/**
 * Get extra_fields
 */
$extra_fields = func_query_hash("SELECT $sql_tbl[extra_fields].*, IF($sql_tbl[extra_fields_lng].field != '', $sql_tbl[extra_fields_lng].field, $sql_tbl[extra_fields].field) as field FROM $sql_tbl[extra_fields] LEFT JOIN $sql_tbl[extra_fields_lng] ON $sql_tbl[extra_fields].fieldid = $sql_tbl[extra_fields_lng].fieldid AND $sql_tbl[extra_fields_lng].code = '$shop_language' WHERE 1 $provider_condition ORDER BY orderby", "fieldid", false);

$smarty->assign('extra_fields', $extra_fields);
$smarty->assign('count_extra_fields', count($extra_fields));
$smarty->assign('main','extra_fields');

// Assign the current location line
$smarty->assign('location', $location);

if (
    file_exists($xcart_dir.'/modules/gold_display.php')
    && is_readable($xcart_dir.'/modules/gold_display.php')
) {
    include $xcart_dir.'/modules/gold_display.php';
}
func_display('provider/home.tpl',$smarty);
?>
