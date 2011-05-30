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
 * Edit wizard
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: pconf_wizard_modify.php,v 1.23.2.1 2011/01/10 13:12:00 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

if (empty($active_modules['Product_Configurator']))
    return;

x_load('backoffice');

/**
 * Add/modify Configurable product
 */
if ($REQUEST_METHOD == 'POST') {

    $top_message['anchor'] = 'list';

    if (is_array($posted_data)) {
        if ($action == 'delete_slots') {
            foreach ($posted_data as $k=>$v) {
                if ($v['delete']) {
                    db_query("DELETE FROM $sql_tbl[pconf_slots] WHERE slotid='$k'");
                    db_query("DELETE FROM $sql_tbl[pconf_slot_markups] WHERE slotid='$k'");
                    db_query("DELETE FROM $sql_tbl[pconf_slot_rules] WHERE slotid='$k'");
                }
            }
            $top_message['content'] = func_get_langvar_by_name('msg_pconf_slot_del');
            $top_message['anchor'] = 'step';
        }
        elseif ($action == 'update_slots') {
            foreach ($posted_data as $k=>$v) {
                db_query("UPDATE $sql_tbl[pconf_slots] SET orderby='$v[orderby]', status='$v[status]' WHERE slotid='$k'");
            }
            $top_message['content'] = func_get_langvar_by_name('msg_pconf_slot_upd');
            $top_message['anchor'] = 'step';

        } elseif ($action == 'update') {

            // Update steps
            foreach ($posted_data as $k => $v) {
                db_query("UPDATE $sql_tbl[pconf_wizards] SET orderby = '$v[orderby]' WHERE stepid = '$k'");
                $top_message['content'] = func_get_langvar_by_name('msg_pconf_step_upd');
            }

        } elseif ($action == 'delete') {

            // Delete steps
            foreach ($posted_data as $k=>$v) {
                if (!$v['delete'])
                    continue;

                db_query("DELETE FROM $sql_tbl[pconf_wizards] WHERE stepid = '$k' AND productid = '$productid'");
                $slotids = func_query_column("SELECT slotid FROM $sql_tbl[pconf_slots] WHERE stepid = '$k'");
                if (!empty($slotids)) {
                    db_query("DELETE FROM $sql_tbl[pconf_slots] WHERE stepid = '$k'");
                    db_query("DELETE FROM $sql_tbl[pconf_slot_markups] WHERE slotid IN ('".implode("','", $slotids)."')");
                    db_query("DELETE FROM $sql_tbl[pconf_slot_rules] WHERE slotid IN ('".implode("','", $slotids)."')");
                }
            }

            $top_message['content'] = func_get_langvar_by_name('msg_pconf_step_del');

        }
        elseif ($action == 'update_step') {

        // Update step details

            db_query("UPDATE $sql_tbl[pconf_wizards] SET orderby='$posted_data[orderby]' WHERE productid='$productid' AND stepid='$step'");
            func_languages_alt_insert($language_var_names['step_name'].$step, $posted_data['step_name'], $current_language);
            func_languages_alt_insert($language_var_names['step_descr'].$step, $posted_data['step_descr'], $current_language);

            $top_message['content'] = func_get_langvar_by_name('msg_pconf_step_upd');
            $top_message['anchor'] = 'step';
        }
    }

    if ($action == 'add_slot' && !empty($new_slot)) {

    // Add a new slot

        db_query("INSERT INTO $sql_tbl[pconf_slots] (stepid) VALUES ('$step')");
        $slotid = db_insert_id();
        db_query("UPDATE $sql_tbl[pconf_slots] SET slot_name='".$language_var_names["slot_name"].$slotid."', slot_descr='".$language_var_names["slot_descr"].$slotid."' WHERE slotid='$slotid'");
        func_languages_alt_insert($language_var_names['slot_name'].$slotid, $new_slot);
        func_languages_alt_insert($language_var_names['slot_descr'].$slotid, '');

        $top_message['content'] = func_get_langvar_by_name('msg_pconf_slot_add');
        $top_message['anchor'] = 'step';
    }

    if (!empty($new_step)) {

    // Add a new step

        db_query("INSERT INTO $sql_tbl[pconf_wizards] (productid) VALUES ('$productid')");
        $step = db_insert_id();
        db_query("UPDATE $sql_tbl[pconf_wizards] SET step_name='".$language_var_names["step_name"].$step."', step_descr='".$language_var_names["step_descr"].$step."' WHERE stepid='$step'");
        func_languages_alt_insert($language_var_names['step_name'].$step, $new_step);
        func_languages_alt_insert($language_var_names['step_descr'].$step, '');

        $top_message['content'] = func_get_langvar_by_name('msg_pconf_step_add');
        $top_message['anchor'] = 'step';
    }

    func_header_location("product_modify.php?productid=$productid&mode=pconf&edit=wizard&step=$step");
}

$wizards = func_query("SELECT * FROM $sql_tbl[pconf_wizards] WHERE productid='$productid' ORDER BY orderby, stepid");

if (is_array($wizards)) {
    $counter = 1;
    foreach ($wizards as $k=>$v) {
        $wizards[$k]['step_name'] = func_get_languages_alt($language_var_names['step_name'].$v['stepid'], $current_language);
        $wizards[$k]['step_counter'] = $counter++;
        if ($step == $v['stepid'])
            $current_step = $wizards[$k];
    }
    if (empty($current_step)) {
        $current_step = $wizards[0];
        $step = $current_step['stepid'];
    }
    $current_step['step_descr'] = func_get_languages_alt($language_var_names['step_descr'].$step, $current_language);
    $current_step['slots'] = func_query("SELECT * FROM $sql_tbl[pconf_slots] WHERE stepid='$current_step[stepid]' ORDER BY orderby, slotid");
    if (is_array($current_step['slots'])) {
        foreach ($current_step['slots'] as $k=>$v) {
            $current_step['slots'][$k]['slot_name'] = func_get_languages_alt($language_var_names['slot_name'].$v['slotid'], $current_language);
        }
    }
}

$smarty->assign('wizards', $wizards);
$smarty->assign('step', $step);
$smarty->assign('wizard_data', $current_step);

?>
