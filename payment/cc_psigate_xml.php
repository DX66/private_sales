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
 * "PSiGate - XML Direct" payment module (credit card processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_psigate_xml.php,v 1.24.2.1 2011/01/10 13:12:07 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

x_load('http','xml');

$items = array();
if (!empty($products)) {
    foreach ($products as $p) {
        $tmp = '
        <ItemID>'.$p['productcode'].'</ItemID>
        <ItemDescription>'.$p['product'].'</ItemDescription>
        <ItemQty>'.$p['amount'].'</ItemQty>
        <ItemPrice>'.price_format($p['price']).'</ItemPrice>
';
        if (!empty($p['product_options'])) {
            $tmp .= "        <Option>\n";
            foreach ($p['product_options'] as $po) {
                $tmp .= "            <".$po['class'].">".$po['option_name']."</".$po['class'].">\n";
            }
            $tmp .= "        </Option>\n";
        }
        $items[] = $tmp;
    }
}
if (!empty($cart['giftcerts'])) {
    foreach ($cart['giftcerts'] as $k => $p) {
        if (empty($p['gcid']))
            $p['gcid'] = $k+1;
        $items[] = '
        <ItemID>'.$p['gcid'].'</ItemID>
        <ItemDescription>GIFT CERTIFICATE</ItemDescription>
        <ItemQty>1</ItemQty>
        <ItemPrice>'.price_format($p['amount']).'</ItemPrice>';
    }
}

$cmpi_post = '';
if(isset($cmpi_result)) {
    $cmpi_post = '    <CardXid>'.$cmpi_result['Xid'].'</CardXid>
    <CardECI>'.$cmpi_result['EciFlag'].'</CardECI>
    <CardCavv>'.$cmpi_result['Cavv'].'</CardCavv>
';
}

$post = '<?xml version="1.0" encoding="UTF-8"?>
<Order>
    <StoreID>'.$module_params['param01'].'</StoreID>
    <Passphrase>'.$module_params['param02'].'</Passphrase>
    <OrderID>'.substr($module_params["param04"].join("-",$secure_oid), 0, 100).'</OrderID>
    <Subtotal>'.price_format($cart['total_cost']).'</Subtotal>
    <PaymentType>CC</PaymentType>
    <CardAction>0</CardAction>
    <CardNumber>'.$userinfo["card_number"].'</CardNumber>
    <CardExpMonth>'.substr($userinfo["card_expire"], 0, 2).'</CardExpMonth>
    <CardExpYear>'.substr($userinfo["card_expire"], 2).'</CardExpYear>
    <CardIDNumber>'.$userinfo["card_cvv2"].'</CardIDNumber>
'.$cmpi_post.'
    <Userid>'.$userinfo['login'].'</Userid>

    <Bname>'.$bill_name.'</Bname>
    <Bcompany>'.$userinfo['company'].'</Bcompany>
    <Baddress1>'.$userinfo['b_address'].'</Baddress1>
    <Baddress2>'.$userinfo['b_address_2'].'</Baddress2>
    <Bcity>'.$userinfo['b_city'].'</Bcity>
    <Bprovince>'.$userinfo['b_state'].'</Bprovince>
    <Bpostalcode>'.$userinfo['b_zipcode'].'</Bpostalcode>
    <Bcountry>'.$userinfo['b_country'].'</Bcountry>

    <Sname>'.$userinfo['s_firstname'].' '.$userinfo['s_lastname'].'</Sname>
    <Scompany>'.$userinfo['company'].'</Scompany>
    <Saddress1>'.$userinfo['s_address'].'</Saddress1>
    <Saddress2>'.$userinfo['s_firstname_2'].'</Saddress2>
    <Scity>'.$userinfo['s_city'].'</Scity>
    <Sprovince>'.$userinfo['s_state'].'</Sprovince>
    <Spostalcode>'.$userinfo['s_zipcode'].'</Spostalcode>
    <Scountry>'.$userinfo['s_country'].'</Scountry>

    <Phone>'.$userinfo['phone'].'</Phone>
    <Fax>'.$userinfo['fax'].'</Fax>
    <Email>'.$userinfo['email'].'</Email>
    <Comments>'.$module_params["param04"].join("-",$secure_oid).'</Comments>
    <CustomerIP>'.func_get_valid_ip($REMOTE_ADDR).'</CustomerIP>

</Order>';

$url = ($module_params['testmode'] == 'Y') ? "https://dev.psigate.com:7989/Messenger/XMLMessenger" : "https://secure.psigate.com:7934/Messenger/XMLMessenger";

list($a, $return) = func_https_request('POST', $url, array($post), '');

$return = func_xml2hash($return);
$return = $return['Result'];

if ($return['Approved'] == 'APPROVED') {
    $bill_output['code'] = 1;
    $bill_output['billmes'] = "Approved: OrderID: ".$return['OrderID']."; Transaction ID: ".$return['TransRefNumber'];

} elseif($return['Approved'] == 'DECLINED') {
    $bill_output['code'] = 2;
    $bill_output['billmes'] = "Declined: OrderID: ".$return['OrderID']."; Transaction ID: ".$return['TransRefNumber']."; ReturnCode: ".$return['ReturnCode'];

    if (!empty($return['ErrMsg'])) {
        $bill_output['billmes'] .= "; Error: ".$return['ErrMsg'];
    }

} else {
    $bill_output['code'] = 2;
    $bill_output['billmes'] = "Error: ".$return['ErrMsg']."; Transaction ID: ".$return['TransRefNumber']."; ReturnCode: ".$return['ReturnCode'];
}

$cvv2res = array(
    'M' => 'Match',
    'N' => "No match",
    'P' => "Not processed",
    'S' => "Not passed",
    'U' => "Issuer does not support CardID verification"
);

$avsres = array(
    'X' => "Exact match, 9-digit zip",
    'Y' => "Exact match, 5-digit zip",
    'A' => "Address match",
    'W' => "9-digit zip match only",
    'Z' => "5-digit zip match only",
    'N' => "No address or zip match",
    'U' => "Address unavailable",
    'R' => "Card Issuer system unavailable",
    'E' => "Not a MOTO order",
    'S' => "Service not supported"
);

if(!empty($return['CardIDResult']) && !empty($cvv2res[$return['CardIDResult']]))
    $bill_output['cvvmes'] = $cvv2res[$return['CardIDResult']];

if(!empty($return['AVSResult']) && !empty($avsres[$return['AVSResult']]))
    $bill_output['avsmes'] = $avsres[$return['AVSResult']];

if (!empty($return['IPResult'])) {
    $bill_output['billmes'] .= "; IP fraud result: Country: ".(substr($return['IPResult'], 0, 1) == 'Y' ? "Match" : "No match")." (".$return['IPCountry'].")".
        "; Region: ".(substr($return['IPResult'], 1, 1) == 'Y' ? "Match" : "No match")." (".$return['IPRegion'].")".
        "; City: ".(substr($return['IPResult'], 2, 1) == 'Y' ? "Match" : "No match")." (".$return['IPCity'].")";
}

?>
