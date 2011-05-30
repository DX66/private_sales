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
 * Functions for PayFlow Pro FPS
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_payflow_pro.func.php,v 1.16.2.1 2011/01/10 13:12:07 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

x_load(
    'http', 
    'xml'
);

function func_cc_payflow_pro_check() 
{
    global $module_params;

    return $module_params['param06'] == 'Y';
}

function func_cc_payflow_pro_verify($tranid, $return_url) 
{
    global $cart, $userinfo, $module_params;

    $vs_user        = func_xml_escape($module_params['param01']);
    $vs_vendor      = func_xml_escape($module_params['param02']);
    $vs_partner     = func_xml_escape($module_params['param03']);
    $vs_pwd         = func_xml_escape($module_params['param04']);
    $vs_fps_host    = $module_params['testmode'] != 'N' 
        ? "pilot-payflowpro.paypal.com" : 
        'payflowpro.paypal.com';
    $expire         = (substr($userinfo['card_expire'], 2, 2)+2000).substr($userinfo['card_expire'], 0, 2);

    $post = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<XMLPayRequest Timeout='45' version="2.0">
    <RequestData>
        <Partner>$vs_partner</Partner>
        <Vendor>$vs_vendor</Vendor>
        <Transactions>
            <Transaction>
                <VerifyEnrollment>
                    <PayData>
                        <Invoice>
                            <TotalAmt Currency="840">$cart[total_cost]</TotalAmt>
                        </Invoice>
                        <Tender>
                            <Card>
                                <CardNum>$userinfo[card_number]</CardNum>
                                <ExpDate>$expire</ExpDate>
                            </Card>
                        </Tender>
                    </PayData>
                </VerifyEnrollment>
            </Transaction>
        </Transactions>
    </RequestData>
    <RequestAuth>
        <UserPass>
            <User>$vs_user</User>
            <Password>$vs_pwd</Password>
        </UserPass>
    </RequestAuth>
</XMLPayRequest>
XML;

    $headers = array(
        "X-VPS-REQUEST-ID" => $tranid.'_'.XC_TIME,
        "X-VPS-VIT-CLIENT-CERTIFICATION-ID" => '7894b92104f04ffb4f38a8236ca48db3'
    );

    list($a, $return) = func_https_request("POST", "https://".$vs_fps_host.'/transaction', array($post), '', '', 'text/xml', '', '', '', $headers);

    $xml = func_xml_parse($return, $err);

    $result     = func_array_path($xml, "XMLPayResponse/ResponseData/TransactionResults/TransactionResult/Result/0/#");
    $message    = func_array_path($xml, "XMLPayResponse/ResponseData/TransactionResults/TransactionResult/Message/0/#");
    $status     = func_array_path($xml, "XMLPayResponse/ResponseData/TransactionResults/TransactionResult/BuyerAuthResult/Status/0/#");
    $authid     = func_array_path($xml, "XMLPayResponse/ResponseData/TransactionResults/TransactionResult/BuyerAuthResult/AuthenticationId/0/#");
    $eci        = func_array_path($xml, "XMLPayResponse/ResponseData/TransactionResults/TransactionResult/BuyerAuthResult/ECI/0/#");
    $pareq      = func_array_path($xml, "XMLPayResponse/ResponseData/TransactionResults/TransactionResult/BuyerAuthResult/PAReq/0/#");
    $acsurl     = func_array_path($xml, "XMLPayResponse/ResponseData/TransactionResults/TransactionResult/BuyerAuthResult/ACSUrl/0/#");

    $r = array();

    if (
        $result == 0 
        && $status == 'E'
    ) {

        $r['form_url'] = $acsurl;

        $r['form_data'] = array(
            'TermUrl'   => $return_url,
            'MD'        => $tranid,
            'PaReq'     => $pareq,
        );

        $r['md'] = $tranid;

    } elseif ($message) {

        $r['error_msg'] = $message." [$result]";

    } else {

        $r['error_msg'] = "Internal error";

    }

    return $r;
}

function func_cc_payflow_pro_validate() 
{
    global $module_params, $PaRes, $MD;

    $vs_user        = func_xml_escape($module_params['param01']);
    $vs_vendor      = func_xml_escape($module_params['param02']);
    $vs_partner     = func_xml_escape($module_params['param03']);
    $vs_pwd         = func_xml_escape($module_params['param04']);
    $vs_fps_host    = $module_params['testmode'] != 'N' 
        ? "pilot-payflowpro.paypal.com" 
        : 'payflowpro.paypal.com';

    $post = <<<XML
<?xml version="1.0"?>
<XMLPayRequest>
    <RequestData>
        <Vendor>$vs_vendor</Vendor>
        <Partner>$vs_partner</Partner>
        <Transactions>
            <Transaction>
                <ValidateAuthentication>
                    <PARes>$PaRes</PARes>
                </ValidateAuthentication>
            </Transaction>
        </Transactions>
    </RequestData>
    <RequestAuth>
        <UserPass>
            <User>$vs_user</User>
            <Password>$vs_pwd</Password>
        </UserPass>
    </RequestAuth>
</XMLPayRequest>
XML;

    $headers = array(
        "X-VPS-REQUEST-ID"                  => $tranid . '_' . XC_TIME,
        "X-VPS-VIT-CLIENT-CERTIFICATION-ID" => '7894b92104f04ffb4f38a8236ca48db3',
    );

    list($a, $return) = func_https_request("POST", "https://".$vs_fps_host.'/transaction', array($post), '', '', 'text/xml', '', '', '', $headers);

    $xml = func_xml_parse($return, $err);

    $result     = func_array_path($xml, "XMLPayResponse/ResponseData/TransactionResults/TransactionResult/Result/0/#");
    $message    = func_array_path($xml, "XMLPayResponse/ResponseData/TransactionResults/TransactionResult/Message/0/#");
    $status     = func_array_path($xml, "XMLPayResponse/ResponseData/TransactionResults/TransactionResult/BuyerAuthResult/Status/0/#");
    $authid     = func_array_path($xml, "XMLPayResponse/ResponseData/TransactionResults/TransactionResult/BuyerAuthResult/AuthenticationId/0/#");
    $eci        = func_array_path($xml, "XMLPayResponse/ResponseData/TransactionResults/TransactionResult/BuyerAuthResult/ECI/0/#");
    $cavv       = func_array_path($xml, "XMLPayResponse/ResponseData/TransactionResults/TransactionResult/BuyerAuthResult/CAVV/0/#");
    $xid        = func_array_path($xml, "XMLPayResponse/ResponseData/TransactionResults/TransactionResult/BuyerAuthResult/XID/0/#");

    $r = array();

    if (
        $result == 0 
        && (
            $status == 'Y' 
            || $status == 'A'
        )
    ) {

        $r['data'] = array(
            'cavv'      => $cavv,
            'eci'       => $eci,
            'xid'       => $xid,
            'status'    => $status,
            'authid'    => $authid,
        );

    } elseif ($message) {

        $r['error_msg'] = $message . " [$result]";

    } else {

        $r['error_msg'] = "Internal error";

    }

    return $r;
}
?>
