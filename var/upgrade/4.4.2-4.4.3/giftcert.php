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
 * Gift certificate page interface
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Customer interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: giftcert.php,v 1.46.2.3 2011/01/10 13:11:42 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

define('NUMBER_VARS', 'amount');

require './auth.php';

x_load('user');

if (empty($active_modules['Gift_Certificates'])) {
    func_header_location('home.php');
}

x_session_register('cart');

if (empty($mode)) $mode = '';

if (!$config['Gift_Certificates']['min_gc_amount'])
    $config['Gift_Certificates']['min_gc_amount'] = 0;

if (!$config['Gift_Certificates']['max_gc_amount'])
    $config['Gift_Certificates']['max_gc_amount'] = 0;

/**
 * Gift certificates module
 */
if (
    isset($gcid)
    && !empty($login)
) {

    if (empty($gcid)) {

        $top_message = array(
            'content' => func_get_langvar_by_name('lbl_giftcertid_is_empty'),
            'type'    => 'E',
        );

        func_header_location('giftcert.php');
    }

    $gc_array = func_query_first("SELECT * FROM $sql_tbl[giftcerts] WHERE gcid='$gcid'");

    if (count($gc_array) == 0) {
        $top_message = array(
            'content' => func_get_langvar_by_name('lbl_giftcert_not_found'),
            'type'    => 'E',
        );

        func_header_location('giftcert.php');
    }

    $smarty->assign('gc_array', $gc_array);

} elseif (
    $mode == 'gc2cart'
    || $mode == 'addgc2wl'
    || $mode == 'preview'
) {

    $fill_error = (empty($purchaser) || empty($recipient));

    $amount_error = (
        ($amount < $config['Gift_Certificates']['min_gc_amount'])
        || (
            $config['Gift_Certificates']['max_gc_amount'] > 0
            && $amount > $config['Gift_Certificates']['max_gc_amount']
        )
    );

    // Add GC to cart

    if ($send_via == 'E') {

        // Send via Email

        $fill_error = ($fill_error || empty($recipient_email));

        $giftcert = array(
            'purchaser'       => stripslashes($purchaser),
            'recipient'       => stripslashes($recipient),
            'message'         => stripslashes($message),
            'amount'          => $amount,
            'send_via'        => $send_via,
            'recipient_email' => $recipient_email,
        );

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
            ) || (
                empty($recipient_country)
                && func_is_display_countries()
            ) || (
                empty($recipient_county)
                && $has_states
                && $config['General']['use_counties'] == 'Y'
            )
        );

        if (
            $config['Gift_Certificates']['allow_customer_select_tpl'] != 'Y'
            || func_gc_wrong_template($gc_template)
        ) {
            $gc_template = $config['Gift_Certificates']['default_giftcert_template'];
        }

        if (
            $config['General']['zip4_support'] == 'Y'
            && $recipient_country == 'US'
            && isset($recipient_zip4)
            && !empty($recipient_zip4)
        ) {

            $recipient_zip4 = substr(trim($recipient_zip4), 0, 4);

        } else {

            $recipient_zip4 = '';

        }

        $giftcert = array (
            'purchaser'             => stripslashes($purchaser),
            'recipient'             => stripslashes($recipient),
            'message'               => stripslashes($message),
            'amount'                => $amount,
            'send_via'              => $send_via,
            'recipient_firstname'   => stripslashes($recipient_firstname),
            'recipient_lastname'    => stripslashes($recipient_lastname),
            'recipient_address'     => stripslashes($recipient_address),
            'recipient_city'        => stripslashes($recipient_city),
            'recipient_zipcode'     => stripslashes($recipient_zipcode),
            'recipient_zip4'        => stripslashes($recipient_zip4),
            'recipient_county'      => stripslashes($recipient_county),
            'recipient_countyname'  => func_get_county($recipient_county),
            'recipient_state'       => stripslashes($recipient_state),
            'recipient_statename'   => func_get_state($recipient_state, $recipient_country),
            'recipient_country'     => $recipient_country,
            'recipient_countryname' => func_get_country($recipient_country),
            'recipient_phone'       => stripslashes($recipient_phone),
            'tpl_file'              => stripslashes($gc_template),
        );

    }

    // If gcindex is empty - add
    // overwise - update

    if (
        !$fill_error
        && !$amount_error
    ) {

        if (
            $mode == 'addgc2wl'
        ) {
            include $xcart_dir . '/modules/Wishlist/wishlist.php';
        }

        if ($mode == 'preview') {

            $smarty->assign('giftcerts', array($giftcert));

            $charset = $smarty->get_template_vars('default_charset');

            $charset_text = ($charset) ? "; charset=$charset" : '';

            header("Content-Type: text/html$charset_text");
            header("Content-Disposition: inline; filename=giftcertificates.html");

            $_tmp_smarty_debug = $smarty->debugging;

            $smarty->debugging = false;

            if (!empty($gc_template)) {

                $css_file = preg_replace('/\.tpl$/', '.css', $gc_template);
                $css_fullpath = $xcart_dir . $smarty_skin_dir . '/modules/Gift_Certificates/' . $css_file;

                if (
                    file_exists($css_fullpath)
                    && $css_file != $gc_template
                ) {
                    $smarty->assign('css_file', $css_file);
                }
            }

            func_display('modules/Gift_Certificates/gc_customer_print.tpl', $smarty);

            $smarty->debugging = $_tmp_smarty_debug;

            exit;
        }

        if (
            isset($gcindex)
            && isset($cart['giftcerts'][$gcindex])
        ) {

            $cart['giftcerts'][$gcindex] = $giftcert;

        } else {

            $gcindex = count($cart['giftcerts']);

            $cart['giftcerts'][$gcindex] = $giftcert;

        }

        x_load('cart');

        $products = func_products_in_cart($cart, $user_account['membershipid']);
        $cart = func_array_merge($cart, func_calculate($cart, $products, $logged_userid, $current_area, 0));

        if ($mode == 'gc2cart') {
            func_register_ajax_message(
                'cartChanged',
                array(
                    'changes' => array(
                        'gcindex'  => $gcindex,
                        'quantity' => 0,
                        'changed'  => 1,
                    ),
                    'isEmpty' => false,
                    'status'  => 1,
                )
            );
        }

        if ($config['General']['redirect_to_cart'] == 'Y') {

            func_header_location('cart.php');

        } else {

            $top_message['content'] = func_get_langvar_by_name('msg_adm_gc_add');

            func_header_location('giftcert.php');

        }

    }

} elseif ($mode == 'delgc') {

    // Remove GC from cart

    $success = false;
    if (isset($cart['giftcerts'][$gcindex])) {
        array_splice($cart['giftcerts'], $gcindex, 1);
        $success = true;
    }

    func_register_ajax_message(
        'cartChanged',
        array(
            'changes' => array(
                'gcindex'  => $gcindex,
                'quantity' => 1,
                'changed'  => -1,
            ),
            'isEmpty' => empty($cart['products']) && empty($cart['giftcerts']),
            'status'  => $success ? 1 : 2,
        )
    );

    func_header_location('cart.php');
}

include $xcart_dir . '/include/common.php';

require $xcart_dir . '/include/countries.php';
require $xcart_dir . '/include/states.php';

if ($config['General']['use_counties'] == 'Y')
    include $xcart_dir . '/include/counties.php';

if (
    empty($fill_error)
    && empty($amount_error)
) {

    if ($action == 'wl') {

        $smarty->assign('giftcert', unserialize(func_query_first_cell("SELECT object FROM $sql_tbl[wishlist] WHERE wishlistid='$gcindex'")));
        $smarty->assign('action', 'wl');
        $smarty->assign('wlitem', $gcindex);

    } elseif (
        isset($gcindex)
        && isset($cart['giftcerts'][$gcindex])
    ) {
        $smarty->assign('giftcert',@$cart['giftcerts'][$gcindex]);
    }

} else {

    $smarty->assign('giftcert',     $giftcert);
    $smarty->assign('fill_error',   $fill_error);
    $smarty->assign('amount_error', $amount_error);

}

if (!empty($logged_userid))
    $smarty->assign('userinfo', func_userinfo($logged_userid, 'C'));

$smarty->assign('min_gc_amount', $config['Gift_Certificates']['min_gc_amount']);
$smarty->assign('max_gc_amount', $config['Gift_Certificates']['max_gc_amount']);

x_session_save();

$smarty->assign('default_fields',
    array(
        'recipient_state' => array(
            'avail'    => 'Y',
            'required' => 'Y',
        ),
        'recipient_country' => array(
            'avail'    => 'Y',
            'required' => 'Y',
        )
    )
);

$smarty->assign('main', 'giftcert');

$location[] = array(func_get_langvar_by_name('lbl_gift_certificate', ''));

$smarty->assign('gc_templates', func_gc_get_templates($xcart_dir . $smarty_skin_dir));

$smarty->assign('allow_tpl', true);

// Assign the current location line
$smarty->assign('location', $location);

func_display('customer/home.tpl',$smarty);
?>
