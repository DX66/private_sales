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
 * Order history functionality
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: history.php,v 1.16.2.1 2011/01/10 13:11:54 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../../"); die("Access denied"); }

if (is_array($order['history']) && !empty($order['history'])) {
    $fields = $_fields = array();

    foreach ($order['history'] as $hkey => $record) {
        $data = $record['details'];

        // Prepare the field names array
        if (is_array($data['diff']) && !empty($data['diff'])) {

            foreach($data['diff'] as $key => $val) {
                if (is_array($val) && !empty($val)) {
                    $_fields = array_merge($_fields, array_keys($val));
                } else {
                    $_fields[] = $val;
                }
                // Show order details for admin
                if ($key == 'X' && isset($val['details'])) {
                    if ($current_area == 'A' || ($current_area == 'P' && $single_mode)) {
                        $details_enc = text_decrypt($val['details']);
                    }
                    if ($details_enc) {
                        $data['diff'][$key]['details'] = $details_enc;
                    } else {
                        func_unset($data['diff'][$key], 'details');
                    }
                }
                // Do not show 'Order notes' for customer
                if ($key == 'X' && isset($val['notes']) && $current_area == 'C')
                    func_unset($data['diff'][$key], 'notes');

                $order['history'][$hkey]['details'] = $data;
            }

            if (!empty($_fields)) {
                array_unique($_fields);
                foreach ($_fields as $field) {
                    $fields[$field] = func_aom_get_field_name($field);
                }
                $smarty->assign('fields',$fields);
            }

        }

    }

    if (!empty($active_modules['RMA'])) {
        $smarty->assign('rma_reasons',func_get_rma_reasons());
        $smarty->assign('rma_actions',func_get_rma_actions());
    }
    $smarty->assign('memberships',func_get_memberships('C',true));

    // Prepare countries hash array
    require $xcart_dir.'/include/countries.php';
    if (!empty($countries)) {
        $_countries = array();
        foreach ($countries as $v) {
            $_countries[$v['country_code']] = $v['country'];
        }
        $smarty->assign('countries',$_countries);
    }

    // Prepare states hash array
    require $xcart_dir.'/include/states.php';
    if (!empty($states)) {
        $_states = array();
        foreach($states as $v) {
            $_states[$v['country_code']][$v['state_code']] = $v['state'];
        }
        $smarty->assign('states',$_states);

    }
}

?>
