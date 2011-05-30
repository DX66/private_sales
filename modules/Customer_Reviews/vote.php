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
 * Product voting / adding a review interface
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: vote.php,v 1.26.2.2 2011/03/09 07:13:03 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }

x_load('user');

if (empty($mode))
    $mode = '';

$stars = func_get_vote_stars();

$allow_review = func_is_allow_add_review();
$allow_vote = func_is_allow_add_rate();

x_session_register('review_store_place');

if ($mode == 'add_vote') {

    // Add vote

    $add_vote = false;
    $already_voted = false;

    if ($allow_vote && $vote > 0 && $vote <= $stars['max']) {

        if (!func_is_allow_add_rate($productid)) {
            $top_message = array(
                'type' => 'E',
                'content' => func_get_langvar_by_name('txt_already_voted')
            );
            $already_voted = true;

        } else {
            $add_vote = true;
        }
    }

    if ($add_vote) {

        // Add vote
        func_array2insert(
            'product_votes',
            array(
                'remote_ip' => $REMOTE_ADDR,
                'vote_value' => $vote,
                'productid' => $productid
            )
        );

        $top_message = array(
            'content' => func_get_langvar_by_name('txt_vote_has_been_counted')
        );

    }

    func_register_ajax_message(
           'addVote',
           array(
            'productid' => $productid,
               'status' => $add_vote ? 1 : ($already_voted ? 2 : 3)
           )
    );

    func_header_location($HTTP_REFERER ? $HTTP_REFERER : func_get_resource_url('product', $productid));

} elseif ($mode == 'add_review') {

    // Add review

    $add_review = false;

    if ($allow_review) {

        // Check review

        x_session_register('antibot_err');
        $antibot_err = (!empty($active_modules['Image_Verification']) && func_validate_image("on_reviews", $antibot_input_str_on_reviews));
        $result = func_query_first("SELECT * FROM $sql_tbl[product_reviews] WHERE remote_ip = '$REMOTE_ADDR' AND productid = '$productid'");
        $review_author = trim($review_author);
        $review_message = trim($review_message);

        if (empty($review_author) || empty($review_message)) {
            $top_message = array(
                'type' => 'E',
                'content' => func_get_langvar_by_name('err_filling_form')
            );
            $review_store_place = array(
                'author' => $review_author,
                'message' => $review_message,
                'error' => true
            );

        } elseif ($antibot_err) {
            $top_message = array(
                'type' => 'E',
                'content' => func_get_langvar_by_name('msg_err_antibot')
            );
            $review_store_place = array(
                'author' => $review_author,
                'message' => $review_message,
                'antibot_err' => $antibot_err,
                'error' => true
            );

        } elseif ($result) {

            $review_store_place = array();
            $top_message = array(
                'type' => 'E',
                'content' => func_get_langvar_by_name('txt_already_reviewed')
            );

        } else {
            $add_review = true;
        }
    }

    if ($add_review) {

        // Add review
        func_array2insert(
            'product_reviews',
            array(
                'remote_ip' => $REMOTE_ADDR,
                'email' => $review_author,
                'message' => $review_message,
                'productid' => $productid
            )
        );
        if (!empty($active_modules['SnS_connector'])) {
            func_generate_sns_action('WriteReview');
        }
        $top_message = array(
            'content' => func_get_langvar_by_name('txt_review_has_been_added')
        );

        $review_store_place = array();
    }

    func_header_location(func_get_resource_url('product', $productid));
}

$smarty->assign('rating', func_get_product_rating($productid));

$customer_info = false;
if (!empty($login)) {
    $customer_info = func_userinfo($logged_userid, $login_type);
    $smarty->assign('customer_info', $customer_info);
}

$reviews = func_query("SELECT * FROM $sql_tbl[product_reviews] WHERE productid = '$productid'");
if ($reviews)
    $smarty->assign('reviews', $reviews);

if (!empty($review_store_place)) {
    $smarty->assign('review', func_stripslashes($review_store_place));
    $review_store_place = false;

} elseif ($customer_info) {
    $smarty->assign(
        'review',
        array(
            'author' => $customer_info['firstname'] . " " . $customer_info['lastname']
        )
    );
}

$smarty->assign('allow_review', $allow_review);
$smarty->assign('allow_vote', $allow_vote);

$already_review = func_query_first("SELECT * FROM $sql_tbl[product_reviews] WHERE remote_ip = '$REMOTE_ADDR' AND productid = '$productid'");
$smarty->assign('allow_add_review', !$already_review && $allow_review);

$smarty->assign('stars', $stars);
?>
