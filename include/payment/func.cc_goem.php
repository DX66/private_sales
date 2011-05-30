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
 * Functions for the GoEmerchant - EZ Payment Gateway Direct payment module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.cc_goem.php,v 1.9.2.1 2011/01/10 13:11:52 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header('Location: ../../'); die('Access denied'); }

/**
 * Returns error description
 *
 * @param string $code Error code
 *
 * @return string
 * @see    ____func_see____
 */
function func_goem_avs_response($code ='')
{

    $code = trim($code);

    if (empty($code)) {
        return false;
    }

    $avserr = array(
       'A' =>' Address matches - Zip Code does not',
       'B' =>' Street address match, Postal code in wrong format. (international issuer)',
       'C' =>' Street address and postal code in wrong formats',
       'D' =>' Street address and postal code match (international issuer)',
       'E' =>' AVS Error',
       'G' =>' Service not supported by non-US issuer',
       'I' =>' Address information not verified by international issuer.',
       'M' =>' Street Address and Postal code match (international issuer)',
       'N' =>' No match on address or Zip Code',
       'O' =>' No Response sent',
       'P' =>' Postal codes match, Street address not verified due to incompatible formats.',
       'R' =>' Retry - system is unavailable or timed out',
       'S' =>' Service not supported by issuer',
       'U' =>' Address information is unavailable',
       'W' =>' 9-digit Zip Code matches - address does not',
       'X' =>' Exact match',
       'Y' =>' Address and 5-digit Zip Code match',
       'Z' =>' 5-digit zip matches - address does not',
       '0' =>' No Response sent'
    );

    return ($avserr[$code] ? $avserr[$code] : 'AVSCode: ' . $code);
}

/**
 * Return array with necessary data to prepare a
 * cc details form
 *
 * @return void
 * @see    ____func_see____
 */
function func_cc_goem_get_cc_fields()
{

    $supported_card_types = array(
        'VISA' => 'Visa',
        'MC'   => 'MasterCard',
        'DINO' => 'Discover',
        'AMEX' => 'Amex',
        'JCB'  => 'JCB',
        'DICL' => 'Dine'
    );

    $cc_fields = array(
        'card_type' => array(
            'field_name' => 'Cardname',
            'required' => 'Y',
            'cc_types' => $supported_card_types
        ),
        'card_name' => array(
            'field_name' => 'NameonCard',
            'required' => 'Y',
        ),
        'card_number' => array(
            'split' => 'Y',
            'required' => 'Y',
            'fields' => array(
                'Cardnum1',
                'Cardnum2',
                'Cardnum3',
                'Cardnum4'
            ),
        ),
        'exp_month' => array(
            'field_name' => 'CardexpM',
            'required' => 'Y',
            'format' => '%02d'
        ),
        'exp_year' => array(
            'field_name' => 'CardexpY',
            'required' => 'Y',
            'year_format' => '%y',
        ),
        'card_cvv2' => array(
            'field_name' => 'CVV2',
            'required' => 'N',
        ),
    );

    return $cc_fields;
}

?>
