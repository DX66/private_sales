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
 * "RBS WorldPay - Global Gateway" payment module (credit card processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_bibit.func.php,v 1.1.2.2 2011/01/10 13:12:05 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }


function func_cc_bibit_verify($tranid, $return_url)
{
    $res = array();
    list($headers, $response) = func_cc_bibit_request();
    
    if (func_cc_bibit_error($headers, $response, $error)) {
        // An error has been detected
        $res['data'] = array('error' => $error);

    } elseif(preg_match("/<payment>(.*)<\/payment>/U", $response, $o)) {
        // 3D-Secure is disabled in merchant account,
        // but enabled in X-Cart
        $res['error_msg'] = '3D-Secure is disabled in merchant account';

    } elseif (preg_match("/(<requestInfo.*)<\/requestInfo>/U", $response, $o)) {
        // 3D-Secure is enabled
        $res['service_data'] = array();

        if (preg_match("/<echoData>(.*)<\/echoData>/U", $response, $o2)) 
            $res['service_data']['echoData'] = $o2[1];

        $response = $o[1];
        if(preg_match("/<paRequest>(.*)<\/paRequest>/U", $response, $o)) 
            $res['service_data']['paRequest'] = $o[1];

        if(preg_match("/<issuerURL><!\[CDATA\[(.*)\]\]><\/issuerURL>/U", $response, $o)) 
            $res['form_url'] = $o[1];

        $res['no_iframe'] = 'Y';
        $res['form_data'] = array(
            'PaReq' => $res['service_data']['paRequest'],
            'TermUrl' => $return_url
        );
        $res['md'] = $tranid;
    }

    return $res;
}

function func_cc_bibit_validate()
{
    global $PaRes, $data;
    
    if ($PaRes != 'IDENTIFIED') {
        return array('error_msg' => $PaRes);
    }

    $_data = array(
        'PaRes' => $PaRes,
        'echoData' => $data['service_data']['echoData']
    );

    return array('data' => $_data);
}

?>
