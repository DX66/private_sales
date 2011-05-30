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
 * Modify slot
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: pconf_slot_modify.php,v 1.37.2.1 2011/01/10 13:12:00 ferz Exp $
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

    $top_message['anchor'] = 'slot';

    if (is_array($posted_data)) {

        if (
            $action == 'update_markups'
            || $action == 'delete_markups'
        ) {

            foreach ($posted_data as $k => $v) {

                if (
                    $v['delete']
                    && $action == 'delete_markups'
                ) {

                    db_query("DELETE FROM $sql_tbl[pconf_slot_markups] WHERE markupid='$k'");

                    continue;

                } elseif ($action != 'delete_markups') {

                    func_array2update(
                        'pconf_slot_markups',
                        array(
                            'markup'         => func_convert_number($v['markup']),
                            'markup_type'     => $v['markup_type'],
                            'membershipid'     => $v['membershipid'],
                        ),
                        'markupid=\'' . $k . '\''
                    );

                }

            } // foreach ($posted_data as $k => $v)

            $top_message['content'] = func_get_langvar_by_name('msg_pconf_mod_' . ($action == 'update_markups') ? 'upd' : 'del');
            $top_message['anchor']     = 'price';

        }

        if (
            $action == 'delete_rules'
            && is_array($to_delete)
        ) {

            foreach ($to_delete as $k => $v) {

                db_query("DELETE FROM $sql_tbl[pconf_slot_rules] WHERE slotid='$slot' AND index_by_and='$k'");

            }

            $top_message['content'] = func_get_langvar_by_name('msg_pconf_rule_del');
            $top_message['anchor']     = 'rules';
        }

        if (
            $action == 'update_rules'
            && is_array($add_rules)
        ) {

            $index_by_and = func_query_first_cell("SELECT MAX(index_by_and) FROM $sql_tbl[pconf_slot_rules] WHERE slotid='$slot'") + 1;

            foreach ($add_rules as $k => $v) {

                $is_rule_exists = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[pconf_slot_rules] WHERE slotid='$slot' AND ptypeid = '$v'");

                if (!$is_rule_exists) {

                    $is_added = true;

                    func_array2insert(
                        'pconf_slot_rules',
                        array(
                            'slotid'         => $slot,
                            'ptypeid'         => $v,
                            'index_by_and'     => $index_by_and,
                        )
                    );

                } // if (!$is_rule_exists)

            } // foreach ($add_rules as $k => $v)

            if ($is_added) {

                $top_message['content'] = func_get_langvar_by_name('msg_pconf_rule_upd');

            }

            $top_message['anchor'] = 'rules';

        } elseif ($action == 'update_slot') {

            func_array2update(
                'pconf_slots',
                $posted_data,
                "slotid='$slot'"
            );

            func_languages_alt_insert($language_var_names['slot_name'] . $slot, $posted_data['slot_name'], $current_language);
            func_languages_alt_insert($language_var_names['slot_descr'] . $slot, $posted_data['slot_descr'], $current_language);

            $top_message['content'] = func_get_langvar_by_name('msg_pconf_slot_upd');

        } elseif ($action == 'update_slot_capacity') {

            $posted_data['multiple'] = $posted_data['multiple'];

            if ($posted_data['multiple'] == 'Y') {

                $posted_data['amount_min']             = max(1, intval($posted_data['amount_min']));
                $posted_data['amount_max']             = max($posted_data['amount_min'], intval($posted_data['amount_max']));

                $a = $posted_data['default_amount'];

                $posted_data['default_amount']         = $a > $amount_max
                    ? $amount_max
                    : max($a, $amount_min);

            }

            func_array2update(
                'pconf_slots',
                $posted_data,
                "slotid='$slot'"
            );

            $top_message['anchor']     = 'rules';
            $top_message['content'] = func_get_langvar_by_name('msg_pconf_slot_upd');

        }

    } // if (is_array($posted_data))

    if (
        $action == 'update_markups'
        && doubleval($new_markup) != 0
    ) {

        // Create new price modifier

        if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[pconf_slot_markups] WHERE slotid='$slot' AND membershipid = '$new_membershipid'") == 0) {

            func_array2insert(
                'pconf_slot_markups',
                array(
                    'slotid'         => $slot,
                    'markup'         => func_convert_number($new_markup),
                    'markup_type'     => $new_markup_type,
                    'membershipid'     => $new_membershipid,
                )
            );

            $is_added = true;

        }

        if ($is_added) {

            $top_message['content'] = func_get_langvar_by_name('msg_pconf_mod_add');

        }

        $top_message['anchor'] = 'price';

    }

    func_header_location("product_modify.php?productid=$productid&mode=pconf&edit=slot&slot=$slot");

} // if ($REQUEST_METHOD == 'POST')

if (!empty($slot)) {

    // Get the slot data

    $slot_data = func_pconf_get_slot_data($slot);

    $step = $slot_data['stepid'];

    // Get data for step related with this slot

    $wizards = func_pconf_get_wizards($productid);

    if (is_array($wizards)) {

        $counter = 1;

        foreach ($wizards as $k => $v) {

            $wizards[$k]['step_name']         = func_get_languages_alt($language_var_names['step_name'] . $v['stepid'], $current_language);
            $wizards[$k]['step_counter']     = $counter++;

            if ($v['stepid'] == $step) {

                $current_step = $wizards[$k];

            }

        } // foreach ($wizards as $k => $v)

    } // if (is_array($wizards))

} // if (!empty($slot))

/**
 * Get the prodict types information
 */
$provider_condition = $single_mode
    ? ''
    : " AND provider='$product_info[provider]'";

$ptype_ids     = array();
$ptype_ids     = func_query("SELECT ptypeid FROM $sql_tbl[pconf_slot_rules] WHERE slotid='$slot'");

/**
 * Get 'ptypeid' key value
 *
 * @param array $elem array with 'ptypeid' key
 *
 * @return mixed
 * @see    ____func_see____
 * @since  1.0.0
 */
function get_ptypeid($elem)
{
    return $elem['ptypeid'];
}

$ptype_condition = !empty($ptype_ids)
    ? 'AND ptypeid NOT IN (\'' . implode('\',\'', array_map('get_ptypeid', $ptype_ids)) . '\')'
    : '';

$product_types = func_query("SELECT * FROM $sql_tbl[pconf_product_types] WHERE 1 $provider_condition $ptype_condition ORDER BY orderby, ptype_name");

/**
 * Get the rule array for the slot
 */
$indexes_by_and = func_query("SELECT index_by_and FROM $sql_tbl[pconf_slot_rules] WHERE slotid='$slot' GROUP BY index_by_and ORDER BY index_by_and");

if (is_array($indexes_by_and)) {

    foreach ($indexes_by_and as $k => $v) {

        $rule_by_or['index_by_and'] = $v['index_by_and'];
        $rule_by_or['rules_by_and'] = func_query("SELECT $sql_tbl[pconf_product_types].* FROM $sql_tbl[pconf_product_types], $sql_tbl[pconf_slot_rules] WHERE $sql_tbl[pconf_product_types].ptypeid=$sql_tbl[pconf_slot_rules].ptypeid AND $sql_tbl[pconf_slot_rules].slotid='$slot' AND $sql_tbl[pconf_slot_rules].index_by_and='$v[index_by_and]'");

        $rules_by_or[] = $rule_by_or;
    }

    $smarty->assign('rules_by_or', $rules_by_or);
}

/**
 * Get the markups data for current slot
 */
$markups = func_pconf_get_markups($slot);

$smarty->assign('markups',             $markups);
$smarty->assign('wizards',             $wizards);
$smarty->assign('slot_data',         $slot_data);
$smarty->assign('slot',             $slot);
$smarty->assign('step',             $step);
$smarty->assign('wizard_data',         $current_step);
$smarty->assign('product_types',     $product_types);
$smarty->assign('memberships',        func_get_memberships());

?>
