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
 * Address book management
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: address_book.php,v 1.18.2.5 2011/01/10 13:11:48 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header('Location: ../'); die('Access denied'); }

x_load('user');

/**
 * Update address book during profile edit
 */
if ($REQUEST_METHOD == 'POST') {

    if (!isset($address_book) || empty($address_book)) {
        return;
    }

    // Prepare an array of addresses to delete
    $delete_address = (isset($delete_address) && !empty($delete_address))
        ? array_keys($delete_address)
        : array();

    $addr_errors = array();

    foreach ($address_book as $addrid => $data) {

        if (in_array($addrid, $delete_address)) {

            // Delete address
            func_delete_address($addrid);
        }
        else {

            // Add/update address

            $save_flag = $current_area != 'C' || !$is_anonymous;

            if (
                !empty($addrid)
                && in_array($addrid, array('B','S'))
            ) {

                // Store address record during registration at checkout
                if ($addrid == 'S' && $ship2diff != 'Y') {
                    continue;
                }

                if ($main == 'checkout') {

                    if (!empty($data['address_2'])) {
                        $data['address'] .= "\n" . $data['address_2'];
                    }
                    func_unset($data, 'address_2');

                    if ($is_anonymous) {
                        $anonymous_userinfo['address'][$addrid] = $data;
                    } else {
                        $cart['used_' . strtolower($addrid) . '_address'] = func_stripslashes($data);
                    }
                }

                $data['default_' . strtolower($addrid)] = 'Y';
                if ($addrid == 'B' && $ship2diff != 'Y') {
                    $data['default_s'] = 'Y';
                    func_unset($address_book, 'S');
                    func_unset($cart, 'used_s_address');
                }

                if (
                    $save_flag
                    && $current_area == 'C'
                    && $main == 'checkout'
                    && $logged_userid > 0
                ) {
                    if (
                        isset($existing_address[$addrid])
                        && $existing_address[$addrid] > 0
                    ) {
                        // Update address book from checkout
                        $_res2 = func_save_address($logged_userid, $existing_address[$addrid], $data);
                        $save_flag = false;
                    }

                    if (
                        $save_flag 
                        && (
                            func_is_address_book_empty($logged_userid)
                            || isset($new_address[$addrid])
                        )
                    ) {
                        // Add new address book row from checkout
                        $_res = func_save_address($logged_userid, 0, $data);
                        $cart['used_' . strtolower($addrid) . '_address']['id'] = $_res['addressid'];
                    } 

                    $save_flag = false;
                }
            }

            if ($save_flag) {
                $_res = func_save_address($logged_userid, $addrid, $data);

                if (empty($addrid) && $current_area != 'C') {
                    $new_addressid = $_res['addressid'];
                }
            }

        }
    }
    
    // Mark default address(es)
    if (!empty($logged_userid)) {
        foreach (array('B', 'S') as $suffix) {
            $fieldname = 'default_' . strtolower($suffix);
            if (isset($_POST[$fieldname])) {
                $addressid = ($_POST[$fieldname] == 0 && isset($new_addressid))
                    ? $new_addressid
                    : abs(intval($_POST[$fieldname]));
                func_mark_default_address($addressid, $logged_userid, $suffix);
            }
        }
    }
}

// Process deletion of the address in the storefront
if (
    $current_area == 'C'
    && $mode == 'delete'
    && !empty($id)
    && func_check_address_owner($logged_userid, $id)
) {
    $res = func_delete_address($id);

    if ($res) {
        $top_message = array(
            'type'    => 'I',
            'content' => func_get_langvar_by_name('txt_address_' . $mode . '_success')
        );
    }

    func_header_location('address_book.php');
}

?>
