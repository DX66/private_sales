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
 * Configuration types management
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: pconf_types.php,v 1.32.2.2 2011/01/10 13:12:00 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

x_load('backoffice');

x_session_register('ptypes_per_page');

if ($REQUEST_METHOD == 'POST') {

    if ($action == 'update_page') {
        $ptypes_per_page = intval($_POST['ptypes_per_page']);
        func_header_location("pconf.php?mode=types");
    }

    if (is_array($posted_types)) {

        // Delete selected specifications

        if ($action == 'delete' && $flag_delete && is_array($posted_types[$flag_delete])) {
            foreach($posted_types[$flag_delete]['specifications'] as $k => $v) {
                if (!empty($v['delete']))
                    db_query("DELETE FROM $sql_tbl[pconf_specifications] WHERE specid='$k'");
            }
            $top_message['content'] = func_get_langvar_by_name('msg_pconf_data_del');
            func_header_location("pconf.php?mode=types");
        }

        // Update existing product types

        foreach ($posted_types as $k=>$v) {
            if (!empty($v['delete']) && $action == 'delete') {

                // Delete product type and all related information

                db_query("DELETE FROM $sql_tbl[pconf_product_types] WHERE ptypeid='$k' $provider_condition");
                db_query("DELETE FROM $sql_tbl[pconf_specifications] WHERE ptypeid='$k'");
                db_query("DELETE FROM $sql_tbl[pconf_slot_rules] WHERE ptypeid='$k'");
                $classids = func_query("SELECT classid FROM $sql_tbl[pconf_products_classes] WHERE ptypeid='$k'");
                if (is_array($classids)) {
                    foreach ($classids as $k2=>$v2) {
                        db_query("DELETE FROM $sql_tbl[pconf_class_specifications] WHERE classid='$v2[classid]'");
                        db_query("DELETE FROM $sql_tbl[pconf_class_requirements] WHERE classid='$v2[classid]'");
                    }
                }

                db_query("DELETE FROM $sql_tbl[pconf_products_classes] WHERE ptypeid='$k'");
                continue;
            }
            elseif ($action != 'delete') {
                $is_exists = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[pconf_product_types] WHERE ptype_name = '$v[ptype_name]' ".$provider_condition) > 0;
                $query_data = array(
                    'orderby' => $v['orderby']
                );
                if ($shop_language == $config['default_admin_language'] && !$is_exists)
                    $query_data['ptype_name'] = $v['ptype_name'];

                func_array2update('pconf_product_types', $query_data, "ptypeid='$k' ".$provider_condition);
                func_languages_alt_insert('ptype_name_'.$k, $v['ptype_name'], $shop_language);
            }

            if (is_array($v['specifications']) && $action != 'delete') {

                // Update specifications for this product type

                foreach ($v['specifications'] as $k1=>$v1) {
                    db_query("UPDATE $sql_tbl[pconf_specifications] SET spec_name='$v1[spec_name]', orderby='$v1[orderby]' WHERE specid='$k1'");
                }
            }

            // Add new specification(s)

            if ($new_list && is_array($new_list[$k])) {
                $data = $new_list[$k];
                foreach($data['spec_name'] as $nlk => $spec_name) {
                    $spec_name = trim($spec_name);

                    if (strlen($spec_name) == 0)
                        continue;

                    if (empty($data['orderby'][$nlk]))
                        $data['orderby'][$nlk] = func_query_first_cell("SELECT MAX(orderby) FROM $sql_tbl[pconf_specifications] WHERE ptypeid='$k'")+5;

                    $nlist = array (
                        'ptypeid' => $k,
                        'spec_name' => $spec_name,
                        'orderby' => $data['orderby'][$nlk]
                    );
                    func_array2insert('pconf_specifications', $nlist);
                }
            }
        }
    }

    if ($action != 'delete' && !empty($new_types)) {

        // New product type(s)

        if ($new_types && is_array($new_types))    {
            foreach($new_types['ptype_name'] as $ntk => $ptype_name) {
                if (strlen($ptype_name) == 0)
                    continue;
                if (empty($new_types['orderby'][$ntk]))
                    $new_types['orderby'][$ntk] = func_query_first_cell("SELECT MAX(orderby) FROM $sql_tbl[pconf_product_types] WHERE 1 $provider_condition") + 5;
                    $new_type = array (
                        'provider'   => $logged_userid,
                        'ptype_name' => $ptype_name,
                        'orderby'    => $new_types['orderby'][$ntk]
                    );

                    func_array2insert('pconf_product_types', $new_type);
            }
        }
    }
    $top_message['content'] = func_get_langvar_by_name('msg_pconf_data_'.(($action == 'delete') ? 'del' : 'upd'));

    func_header_location("pconf.php?mode=types".($page?"&page=$page":''));
} #/ if ($REQUEST_METHOD == 'POST')

/**
 * Get the prodict types information
 */
$total_items = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[pconf_product_types] WHERE 1 $provider_condition");

$objects_per_page = $ptypes_per_page;

require $xcart_dir.'/include/navigation.php';

$smarty->assign('navigation_script', "pconf.php?mode=types");
$smarty->assign('ptypes_per_page', $ptypes_per_page);

$product_types = func_query("SELECT * FROM $sql_tbl[pconf_product_types] WHERE 1 $provider_condition ORDER BY orderby, ptype_name LIMIT $first_page, $objects_per_page");

if (is_array($product_types)) {
    foreach ($product_types as $k=>$v) {
        $product_types[$k]['specifications'] = func_query("SELECT * FROM $sql_tbl[pconf_specifications] WHERE ptypeid='$v[ptypeid]' ORDER BY orderby, spec_name");
        $tmp = func_get_languages_alt('ptype_name_'.$v['ptypeid'], $shop_language);
        if (!empty($tmp))
            $product_types[$k]['ptype_name'] = $tmp;
    }
}

$smarty->assign('product_types', $product_types);

$smarty->assign('mode', 'types');
?>
