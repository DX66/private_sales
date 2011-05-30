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
 * Functions for "First Data Global Gateway - Connect" payment module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.cc_fdg_connect.php,v 1.18.2.1 2011/01/10 13:11:52 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

define('FDG_CONNECT_APPROVAL_TOKEN', "####approval_code####");

$fdg_sec3dcodes = array(
    '1' => 'Successful authentication',
    '2' => 'Successful authentication without AVV',
    '3' => 'Authentication failed / incorrect password',
    '4' => 'Authentication attempt',
    '5' => 'Unable to authenticate / Directory Server not responding',
    '6' => 'Unable to authenticate / Access Control Server not responding',
    '7' => 'Cardholder not enrolled for 3D Secure',
    '8' => 'Merchant not enabled for 3D Secure (transaction declined)',
);

function fdg_trim($str, $length)
{

    $result = substr(htmlspecialchars(trim($str)), 0, $length);

    return $result;
}

function func_cc_fdg_encrypt($str)
{

    $result = sha1(bin2hex($str));

    return $result;
}

function func_cc_fdg_parse_response($code)
{

    if (empty($code)) return '';

    $avs_err = array(
        'YYA' => "Address and zip code match",
        'YYY' => "Address and zip code match",
        'NYZ' => "Only the zip code matches",
        'YNA' => "Only the address matches",
        'YNY' => "Only the address matches",
        'NNN' => "Neither the address nor the zip code match",
        'XXW' => "Card number not on file",
        'XXU' => "Address information not verified for domestic transaction",
        'XXR' => "Retry - system unavailable",
        'XXS' => "Service not supported",
        'XXE' => "AVS not allowed for card type",
        'XX'  => "Address verification has been requested, but not received",
        'XXG' => "Global non-AVS participant. Normally an international transaction",
        'YNB' => "Street address matches for international transaction; Postal code not verified",
        'NNC' => "Street address and Postal code not verified for international transaction",
        'YYD' => "Street address and Postal code match for international transaction",
        'YYF' => "Street address and Postal code match for international transaction (UK Only)",
        'NNI' => "Address information not verified for international transaction",
        'YYM' => "Street address and Postal code match for international transaction",
        'NYP' => "Postal codes match for international transaction; Street address not verified",
    );

    $cvv_err = array(
        'M' => "Card code matches",
        'N' => "Card code does not match",
        'P' => "Not processed",
        'S' => "Merchant has indicated that the card code is not present on the card",
        'U' => "Issuer is not certified and/or has not provided encryption keys",
        'X' => "No response from the credit card association was received",
    );

    $module_params = func_get_pm_params('cc_fdg_connect.php');
    $fdg_region = $module_params['param01'];

    $tmp = explode(":",$code);
    $result = array();
    $msg = '';

    $app_num= ($fdg_region == 'NA') ? substr($tmp[1], 0, 6) : $tmp[1];

    if ($fdg_region == 'NA') {

        $result = array(
            "Approval number"      => substr($tmp[1], 0, 6),
            "Reference number"     => substr($tmp[1], 6, 10),
            "Leaseline identifier" => $tmp[3],
            "AVS check result"     => $avs_err[substr($tmp[2], 0, 3)],
            "CVV2 check result"    => $cvv_err[substr($tmp[2], 3, 1)],
        );

    } elseif ($fdg_region == 'EMEA') {

        $result = array(
            "Approval number"   => $tmp[1],
            "Trace number"      => substr($tmp[4], 0, 6),
            "Receipt number"    => substr($tmp[4], 6, 4),
            "AVS check result"  => $avs_err[substr($tmp[3], 0, 3)],
            "CVV2 check result" => $cvv_err[substr($tmp[3], 3, 1)],
        );

    }

    foreach ($result as $label => $value) {

        $msg .= "\n".$label.": ".(empty($value) ? 'N/A' : $value);

    }

    return $msg;
}

function func_cc_fdg_get_gateway_url()
{

    $module_params = func_get_pm_params('cc_fdg_connect.php');
    $fdg_region = $module_params['param01'];

    $url = '';

    if ($fdg_region == 'NA') {

        $url .= ($module_params['testmode'] == 'Y') ? 'www.staging.yourpay.com' : 'secure.linkpt.net';
        $url .= '/lpc/servlet/lppay';

    } elseif ($fdg_region == 'EMEA') {

        $url .= ($module_params['testmode'] == 'Y') ? 'test' : 'www';
        $url .= ".ipg-online.com/connect/gateway/processing";

    }

    if (!empty($url)) $url = "https://".$url;

    return $url;
}

/**
 * Return array with necessary data to prepare a
 * cc details form
 *
 * @return array
 * @access public
 * @see    ____func_see____
 * @since  4.4.0
 */
function func_cc_fdg_connect_get_cc_fields()
{

    $supported_card_types = array(
        'VISA' => 'V',
        'MC'   => 'M',
        'DINO' => 'D',
        'AMEX' => 'A',
        'JCB'  => 'J',
        'DICL' => 'C'
    );

    $cc_fields = array(
        'card_type' => array(
            'field_name' => 'cctype',
            'required' => 'N',
            'cc_types' => $supported_card_types
        ),
        'card_number' => array(
            'field_name' => 'cardnumber',
            'required' => 'Y'
        ),
        'exp_month' => array(
            'field_name' => 'expmonth',
            'required' => 'Y'
        ),
        'exp_year' => array(
            'field_name' => 'expyear',
            'required' => 'Y'
        ),
        'card_cvv2' => array(
            'field_name' => 'cvm',
            'required' => 'N'
        ),
    );

    return $cc_fields;
}

?>
