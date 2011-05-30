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
 * Gift registry events search facility
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Customer interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: giftregs.php,v 1.25.2.2 2011/01/10 13:11:42 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

require './auth.php';

if (empty($active_modules['Gift_Registry'])) {
    func_page_not_found();
}

x_session_register('access_status', array());

$eventid = isset($eventid) ? intval($eventid) : 0;

if (
    $mode == 'preview' 
    && $config['Gift_Registry']['enable_html_cards'] == 'Y'
) {
    
    $html_content = func_query_first_cell("SELECT html_content FROM $sql_tbl[giftreg_events] WHERE event_id='$eventid'");

    if (!empty($html_content))
        echo $html_content;
    else
        echo "<br /><br /><br /><br /><h3 align=\"center\">" . func_get_langvar_by_name('lbl_no_html_content', false, false, true) . "</h3>";
    exit;
}

include $xcart_dir . '/include/common.php';

if (!empty($cc)) {

    // Confirm/Decline the participation by recipient
    // $cc - is a confirmation code passed via GET request
    include $xcart_dir . '/modules/Gift_Registry/giftreg_confirm.php';

}

if (!empty($eventid)) {

    if (!empty($wlid)) {

        if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[giftreg_events] WHERE userid='$wlid' AND event_id='$eventid'")) {

            x_session_register('wlid_eventid');

            $wlid_eventid = $eventid;

            x_session_save('wlid_eventid');

        }

    }

    include $xcart_dir . '/modules/Gift_Registry/event_guestbook.php';

    include $xcart_dir . '/modules/Gift_Registry/giftreg_display.php';

} else {

    include $xcart_dir . '/modules/Gift_Registry/giftreg_search.php';

}

// Assign the current location line
$smarty->assign('location', $location);

func_display('customer/home.tpl',$smarty);
?>
