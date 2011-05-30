<?php
/* vim: set ts=4 sw=4 sts=4 et: */
/*****************************************************************************\
+-----------------------------------------------------------------------------+
| X-Cart                                                                      |
| Copyright (c) 2001-2011 Ruslan R. Fazliev <rrf@rrf.ru>                      |
| All rights reserved.                                                        |
+-----------------------------------------------------------------------------+
| PLEASE READ  THE FULL TEXT OF SOFTWARE LICENSE AGREEMENT IN THE "COPYRIGHT" |
| FILE PROVIDED WITH THIS DISTRIBUTION. THE AGREEMENT TEXT IS ALSO AVAILABLE  |
| AT THE FOLLOWING URL: http://www.x-cart.com/license.php                     |
|                                                                             |
| THIS  AGREEMENT  EXPRESSES  THE  TERMS  AND CONDITIONS ON WHICH YOU MAY USE |
| THIS SOFTWARE   PROGRAM   AND  ASSOCIATED  DOCUMENTATION   THAT  RUSLAN  R. |
| FAZLIEV (hereinafter  referred to as "THE AUTHOR") IS FURNISHING  OR MAKING |
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
| The Initial Developer of the Original Code is Ruslan R. Fazliev             |
| Portions created by Ruslan R. Fazliev are Copyright (C) 2001-2011           |
| Ruslan R. Fazliev. All Rights Reserved.                                     |
+-----------------------------------------------------------------------------+
\*****************************************************************************/

/**
 * Functions for the Mailchimp subscription 
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.php,v 1.8.2.1 2011/01/10 13:11:59 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

/**
 * Subscription wrapper for Mailchimp service  (listGrowthHistory method)
 *
 * @param mixed $listid id of Mailchimp accout
 * @param mixed $apikey apikey of Mailchimp accout
 *
 * @return array
 * @see    ____func_see____
 * @since  1.0.0
 */
function func_mailchimp_list_history($listid = false, $apikey = false)
{
    global $config;

    if (!$apikey) {
        $apikey = $config['Mailchimp_Subscription']['mailchimp_apikey'];
    }

    if (!$listid) {
        $listid = $config['Mailchimp_Subscription']['mailchimp_id'];
    }

    $mailchimp_api = new MCAPI($apikey);

    $mailchimp_return = $mailchimp_api->listGrowthHistory($listid);

    if ($mailchimp_api->errorCode) {

        $mailchimp_response['Error_code']         = $mailchimp_api->errorCode;
        $mailchimp_response['Error_message']     = $mailchimp_api->errorMessage;

    } else {

        $mailchimp_response['Response']         = $mailchimp_return;

    }

    return $mailchimp_response;
}

/**
 * Subscription wrapper for Mailchimp service  (listSubscribe method)
 *
 * @param string $email_address E-mail
 * @param mixed  $listid        id of Mailchimp account
 * @param mixed  $apikey        apikey of Mailchimp account
 *
 * @return array
 * @see    ____func_see____
 * @since  1.0.0
 */
function func_mailchimp_subscribe($email_address, $listid = false, $apikey = false)
{
    global $config;

    if (false === $apikey) {
        $apikey = $config['Mailchimp_Subscription']['mailchimp_apikey'];
    }

    if (false === $listid) {
        $listid = $config['Mailchimp_Subscription']['mailchimp_id'];
    }

    $mailchimp_api = new MCAPI($apikey);

    $mailchimp_merge_vars = array('');

    $mailchimp_response = array();

    $mailchimp_return = $mailchimp_api->listSubscribe($listid, $email_address, $mailchimp_merge_vars);

    if ($mailchimp_api->errorCode) {

        $mailchimp_response['Error_code']         = $mailchimp_api->errorCode;
        $mailchimp_response['Error_message']     = $mailchimp_api->errorMessage;

    } else {
        $mailchimp_response['Response']         = $mailchimp_return;
    }

    return $mailchimp_response;
}

?>
