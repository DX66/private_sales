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
 * Manage giftregistry events/properties/wishlist
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Customer interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: giftreg_manage.php,v 1.33.2.2 2011/05/03 05:38:33 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

define('USE_TRUSTED_POST_VARIABLES',1);
$trusted_post_variables = array('event_details', 'posted_mail_message');

// Save HTML card content
// (bypass to the "remove phishing" functionality in 'prepare.php')

if(isset($_POST['event_details']['html_content'])) {
    define('HTML_CARD_CONTENT', $_POST['event_details']['html_content']);
    unset($_POST['event_details']['html_content']);
}

require './auth.php';

if (empty($active_modules['Gift_Registry'])) {
    func_page_not_found();
}

if (!empty($eventid) && $eventid != 'new') {
    // Check for valid event id
    $valid_event = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[giftreg_events] WHERE event_id = '".intval($eventid)."' AND userid='$logged_userid'");
    if (!$valid_event) {
        func_403();
    }
} elseif (empty($eventid) && !empty($mode) && $REQUEST_METHOD == 'GET')
    func_header_location("giftreg_manage.php?eventid=new");

/**
 * Restore HTML card content and purify it
 */
if(defined('HTML_CARD_CONTENT') && $config["Gift_Registry"]["enable_html_cards"]=="Y") {
    $event_details['html_content'] = trim(HTML_CARD_CONTENT);

    if(!empty($event_details['html_content'])) {
        $event_details['html_content'] = func_xss_free($event_details['html_content']);
        if ($event_details['html_content'] != trim(HTML_CARD_CONTENT))
            $event_details['html_content'] = addslashes($event_details['html_content']);
    }
}

$_remember_varnames = array('mode', 'ids', 'eventid', 'post_data', 'StartMonth', 'StartDay', 'StartYear', 'EndMonth', 'EndDay', 'EndYear', 'event_details', 'event_details_Month', 'event_details_Day', 'event_details_Year', 'new_recipient_name', 'new_recipient_email', 'action', 'recipient_details', 'gb_details', 'wlitem', 'move_quantity');

require $xcart_dir.'/include/remember_user.php';

include $xcart_dir . '/include/common.php';

include $xcart_dir.'/include/security.php';

$location[] = array(func_get_langvar_by_name('lbl_gift_registry'), 'giftreg_manage.php');

if ($REQUEST_METHOD == 'POST' && $mode == 'move_product')
    include $xcart_dir.'/modules/Gift_Registry/giftreg_wishlist.php';
else {
    if (!empty($eventid) && ($mode == 'gb' || $mode == 'guestbook')) {
        $modify_mode = true;
        include $xcart_dir.'/modules/Gift_Registry/event_guestbook.php';
    }
    include $xcart_dir.'/modules/Gift_Registry/event_modify.php';
}

// Assign the current location line
$smarty->assign('location', $location);

func_display('customer/home.tpl',$smarty);
?>
