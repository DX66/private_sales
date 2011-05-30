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
 * This script gets advanced tracking info
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: atracking.php,v 1.48.2.1 2011/01/10 13:11:48 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

global $active_modules;

if (
    defined('IS_ROBOT')
    || empty($active_modules['Advanced_Statistics'])
    || $config['Advanced_Statistics']['enable_tracking_statistics'] != 'Y'
) {
    return;
}

global $stats_pageid, $stats_page_time, $stats_pages_string;
global $stats_transactionid, $stats_transaction_step, $cart;
global $PHP_SELF, $mode, $action;

x_session_register('stats_pageid');
x_session_register('stats_page_time');
x_session_register('stats_pages_string');

// Session variables for cart funnel statistics
x_session_register('stats_transactionid');
x_session_register('stats_transaction_step');
x_session_register('cart');

function update_statistics()
{
    global $stats_pageid, $stats_page_time, $stats_pages_string, $REQUEST_URI, $PHP_SELF, $QUERY_STRING, $sql_tbl;
    global $config, $clean_url_data, $xcart_web_dir;

    $old_stats_pageid = $stats_pageid;

    $curtime = XC_TIME;

    if ($stats_pageid && $stats_page_time) {

        // Update statistics of previous page views and average time user spent on that page

        $time_avg = $curtime - $stats_page_time;

        db_query("UPDATE $sql_tbl[stats_pages_views] SET time_avg='$time_avg' WHERE pageid='$stats_pageid' AND date='$stats_page_time'");

    }

    $stats_page_time = $curtime;

    // Insert/update statistics of current page views

    if (preg_match("/\/payment\//i", $PHP_SELF))
        return;

    if (preg_match("/cart\.php/i", $PHP_SELF)) {

        $page = $PHP_SELF;

        if (preg_match("/mode=wishlist/", $QUERY_STRING)) {

            $page .= "?mode=wishlist";

        }

    } else {

        $page = preg_replace("/^(.+)[\?&]" . preg_quote($XCART_SESSION_NAME) . "=[0-9a-hA-H]+(&.*)?$/", "\\1\\2", $REQUEST_URI);

    }

    if ($config['SEO']['clean_urls_enabled'] == 'Y') {

        // restore original request
        switch ($clean_url_data['resource_type']) {
            case 'C':
                $original_script = 'home.php';
                break;

            case 'P':
                $original_script = 'product.php';
                break;

            case 'M':
                $original_script = 'manufacturers.php';
                break;

            case 'S':
                $original_script = 'pages.php';
                break;

            default:
                $original_script = false;
        }

        $page = (
            $original_script
                ? $xcart_web_dir . '/' . $original_script . "?" . $QUERY_STRING
                : $page
        );

    }

    $stats_pageid = '';

    $page = addslashes($page);

    $stats_pageid = func_query_first_cell("SELECT pageid FROM $sql_tbl[stats_pages] WHERE page='$page'");

    if (empty($stats_pageid)) {

        $stats_pageid = func_array2insert(
            'stats_pages',
            array(
                'page' => $page,
            )
        );

    }

    func_array2insert(
        'stats_pages_views',
        array(
            'pageid'     => $stats_pageid,
            'date'         => $curtime,
        )
    );

    // Insert/update statistics of current pages path views

    // if reload - return
    if ($old_stats_pageid == $stats_pageid)
        return;

    if ($stats_pages_string) {

        $stats_pages_string .= "-" . $stats_pageid;

    } else {

        $stats_pages_string = $stats_pageid;

    }

    $paths = explode("-", $stats_pages_string);

    // Maximum length of the pages path

    $max_len_path = 5;

    $offset = (count($paths) <= $max_len_path)
        ? 0
        : (count($paths) - $max_len_path);

    $paths = array_slice($paths, $offset);

    $string = '';

    $paths_cnt = count($paths);

    for ($i = $paths_cnt - 1; $i >= 0 ; $i--) {

        if ($string) {

            $string = $paths[$i] . "-" . $string;

        } else {

            $string = $paths[$i];

        }

        if (strstr($string, "-") !== false) {

            func_array2insert(
                'stats_pages_paths',
                array(
                    'path' => $string,
                    'date' => $curtime
                )
            );

        }

    }

    $stats_pages_string = implode($paths, '-');

} // function end

/**
 * Get statistics about shopping cart funnel
 */
function update_statistics_cart_funnel()
{
    global $stats_transactionid, $stats_transaction_step, $cart;
    global $mode, $paymentid, $logged_userid, $sql_tbl, $action;

    if (
        empty($mode)
        && !func_is_cart_empty($cart)
    ) {

        $stats_field = 'start_page';

    } elseif (
        (
            $mode == 'checkout'
            && empty($logged_userid)
            || (
                $mode == 'update'
                && $action == 'cart'
            )
        )
        && !func_is_cart_empty($cart)
    ) {

        $stats_field = 'step1';

    } elseif (
        $mode == 'checkout'
        && $paymentid != ''
        && !func_is_cart_empty($cart)
    ) {

        $stats_field = 'step3';

    } elseif (
        $mode == 'checkout'
        && !func_is_cart_empty($cart)
    ) {

        $stats_field = 'step2';

    } elseif ($mode == 'order_message') {

        $stats_field = 'final_page';

    } else {

        return '';

    }

    if ($stats_transaction_step == $stats_field)
        return '';

    $stats_transaction_step = $stats_field;

    if (
        $stats_transactionid == ''
        && (
            $stats_field == 'start_page'
            || $stats_field == 'step1'
            || $stats_field == 'step2'
        )
    ) {

        $stats_transactionid = func_array2insert(
            'stats_cart_funnel',
            array(
                'userid'         => $logged_userid,
                $stats_field     => 1,
                'date'             => XC_TIME,
            )
        );

        return $stats_transactionid . ": " . $stats_field . " (create transaction)";

    }

    $stats_field_value = func_query_first_cell("SELECT $stats_field FROM $sql_tbl[stats_cart_funnel] WHERE transactionid='$stats_transactionid'");

    $stats_field_value++;

    if (
        $stats_transactionid
        && (
            $stats_field == 'step1'
            || $stats_field == 'step2'
            || $stats_field == 'step3'
            || $stats_field == 'final_page'
        )
    ) {
        func_array2update(
            'stats_cart_funnel',
            array(
                $stats_field => $stats_field_value,
            ),
            "transactionid = '$stats_transactionid'"
        );
    }

    $trid = $stats_transactionid;

    if ($stats_field == 'final_page')
        $stats_transactionid = '';

    return $trid . ": " . $stats_field . " ($stats_field_value)";

} // function end

update_statistics();

if (
    strpos($PHP_SELF, 'cart.php') !== false
    || (
        strpos($PHP_SELF, 'register.php') !== false
        && $mode == 'update'
        && $action == 'cart'
    )
) {

    $stats_cart_funnel = update_statistics_cart_funnel();

} elseif(strpos($PHP_SELF, '/payment/') === false) {

    $stats_transactionid = '';

}

// Save statistics data to X-Cart session
x_session_save(
    'stats_pageid',
    'stats_page_time',
    'stats_pages_string',
    'stats_transactionid',
    'stats_transaction_step'
);

?>
