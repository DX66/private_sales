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
 * "PayFlow - Pro" payment module (credit card processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_payflow_pro.php,v 1.19.2.1 2011/01/10 13:12:07 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

x_load(
    'http',
    'xml'
);

func_get_payflow_pro_params();

$is_preauth = ($module_params['use_preauth'] == 'Y' || func_is_preauth_force_enabled($secure_oid));

$expire = (substr($userinfo['card_expire'], 2, 2)+2000).substr($userinfo['card_expire'], 0, 2);

$tmp = array();

if ($userinfo['card_valid_from'])
    $tmp[] = "<ExData Name=\"CardStart\" Value=\"" . (substr($userinfo['card_valid_from'], 2, 2)+2000) . substr($userinfo['card_valid_from'], 0, 2) . "\" />";

if ($userinfo['card_issue_no'])
    $tmp[] = "<ExData Name=\"CardIssue\" Value=\"$userinfo[card_issue_no]\" />";

$level3invoice = !empty($tmp) ? "\t\t\t\t\t\t\t\t" . implode("\n\t\t\t\t\t\t\t\t", $tmp) . "\n" : '';

unset($tmp);

$items = array();

if (!empty($products)) {

    foreach ($products as $p) {

        $p['productcode']   = func_pfpro_xml_encode($p['productcode']);
        $p['product']       = func_pfpro_xml_encode($p['product']);

        $items[] = <<<XML
                                    <SKU>$p[productcode]</SKU>
                                    <Description>$p[product]</Description>
                                    <Quantity>$p[amount]</Quantity>
                                    <UnitPrice>$p[taxed_price]</UnitPrice>
XML;
    }
}

$items = "\n\t\t\t\t\t\t\t\t<Item>\n".implode("\n\t\t\t\t\t\t\t\t</Item>\n\t\t\t\t\t\t\t\t<Item>\n", $items)."\n\t\t\t\t\t\t\t\t</Item>\n\t\t\t\t\t\t\t";

$ship_name = func_pfpro_xml_encode($ship_name);
$bill_name = func_pfpro_xml_encode($bill_name);

$userinfo['b_address']  = func_pfpro_xml_encode($userinfo['b_address']);
$userinfo['b_city']     = func_pfpro_xml_encode($userinfo['b_city']);
$userinfo['b_state']    = func_pfpro_xml_encode($userinfo['b_state']);
$userinfo['b_zipcode']  = func_pfpro_xml_encode($userinfo['b_zipcode']);
$userinfo['b_country']  = func_pfpro_xml_encode($userinfo['b_country']);
$userinfo['email']      = func_pfpro_xml_encode($userinfo['email']);
$userinfo['phone']      = func_pfpro_xml_encode($userinfo['phone']);
$userinfo['fax']        = func_pfpro_xml_encode($userinfo['fax']);
$userinfo['s_address']  = func_pfpro_xml_encode($userinfo['s_address']);
$userinfo['s_city']     = func_pfpro_xml_encode($userinfo['s_city']);
$userinfo['s_state']    = func_pfpro_xml_encode($userinfo['s_state']);
$userinfo['s_zipcode']  = func_pfpro_xml_encode($userinfo['s_zipcode']);
$userinfo['s_country']  = func_pfpro_xml_encode($userinfo['s_country']);
$userinfo['card_name']  = func_pfpro_xml_encode($userinfo['card_name']);

$_oid = $module_params['param05'] . join("-", $secure_oid);

$transaction_type = $is_preauth ? 'Authorization' : 'Sale';

// Add PayFlow FPS data
$fps_data = '';

if (
    isset($secure_3d) 
    && $secure_3d['data'] 
    && $secure_3d['data']['status']
) {

    $fps_data = <<<XML
                                <BuyerAuthResult>
                                    <Status>{$secure_3d[data][status]}</Status>
                                    <AuthenticationId>{$secure_3d[data][authid]}</AuthenticationId>
                                    <ECI>{$secure_3d[data][eci]}</ECI>
                                    <CAVV>{$secure_3d[data][cavv]}</CAVV>
                                    <XID>{$secure_3d[data][xid]}</XID>
                                </BuyerAuthResult>
XML;
}

$post = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<XMLPayRequest Timeout='45' version="2.0">
    <RequestData>
        <Partner>$vs_partner</Partner>
        <Vendor>$vs_vendor</Vendor>
        <Transactions>
            <Transaction>
                <$transaction_type>
                    <PayData>
                        <Invoice>
                            <BillTo>
                                <Name>$bill_name</Name>
                                <Address>
                                    <Street>$userinfo[b_address]</Street>
                                    <City>$userinfo[b_city]</City>
                                    <State>$userinfo[b_state]</State>
                                    <Zip>$userinfo[b_zipcode]</Zip>
                                    <Country>$userinfo[b_country]</Country>
                                </Address>
                                <EMail>$userinfo[email]</EMail>
                                <Phone>$userinfo[phone]</Phone>
                                <Fax>$userinfo[fax]</Fax>
                            </BillTo>
                            <ShipTo>
                                <Name>$ship_name</Name>
                                <Address>
                                    <Street>$userinfo[s_address]</Street>
                                    <City>$userinfo[s_city]</City>
                                    <State>$userinfo[s_state]</State>
                                    <Zip>$userinfo[s_zipcode]</Zip>
                                    <Country>$userinfo[s_country]</Country>
                                </Address>
                            </ShipTo>
                            <TotalAmt>$cart[total_cost]</TotalAmt>
                            <Comment>$_oid</Comment>
                            <Items>$items</Items>
                        </Invoice>
                        <Tender>
                            <Card>
                                <CardNum>$userinfo[card_number]</CardNum>
                                <ExpDate>$expire</ExpDate>
                                <NameOnCard>$userinfo[card_name]</NameOnCard>
                                <CVNum>$userinfo[card_cvv2]</CVNum>
$fps_data
$level3invoice
                            </Card>
                        </Tender>
                    </PayData>
                </$transaction_type>
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

$return = func_payflow_pro_request($post);

$xml = func_xml_parse($return, $err);

func_payflow_pro_analyze_request($xml);

$bill_output = array();

$bill_output['hide_mess'] = true;

if (empty($xml)) {

    $bill_output['code']        = 2;
    $bill_output['is_error']    = true;
    $bill_output['billmes']     = "Response incorrect or empty";

} elseif ($result === '0') {

    $bill_output['code']    = 1;
    $bill_output['billmes'] = "AuthCode: ".$authcode."; PNRef: ".$pnref;

    if ($is_preauth) {
        $bill_output['is_preauth'] = true;

        $extra_order_data = array(
            'payflow_pro_pnref'     => $pnref,
            'capture_status'        => 'A',
        );
    }

} else {

    $bill_output['code']    = 2;
    $bill_output['billmes'] = "Result code: " . $result . "; ";

    if (!empty($message))
        $bill_output['billmes'] .= "Message: " . $message . "; ";

    if (!empty($authcode))
        $bill_output['billmes'] .= "AuthCode: " . $authcode . "; ";

    if (!empty($pnref))
        $bill_output['billmes'] .= "PNRef: " . $pnref . "; ";

    if (in_array($result, array('12','22','23','24'))) {

        $bill_output['hide_mess'] = false;

    } else  {

        $bill_output['is_error'] = true;

    }

}

if (!empty($avsresult))
    $bill_output['avsmes'] = "International AVS result: ".$avsresult."; AVS result: Street match: $avsresults; Zip match: $avsresultz";

if (!empty($cvsresult))
    $bill_output['cavvmes'] = $cvsresult;

?>
