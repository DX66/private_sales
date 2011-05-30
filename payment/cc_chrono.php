<?php
/* vim: set ts=4 sw=4 sts=4 et: */
/*****************************************************************************\
+-----------------------------------------------------------------------------+
| X-Cart                                                                      |
| Copyright (c) 2001-2005 Ruslan R. Fazlyev <rrf@x-cart.com>                      |
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
 * "ChronoPay" payment module (credit card processor)
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Payment interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: cc_chrono.php,v 1.17.2.3 2011/01/10 13:12:05 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!isset($REQUEST_METHOD))
    $REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];

if ($REQUEST_METHOD == 'POST' && !empty($_POST['transaction_id']) && !empty($_POST['cs1'])) {

    require './auth.php';

    $bill_output['code'] = $_POST['transaction_type'] == 'onetime' ? 1 : 2;

    if(!empty($_POST['transaction_type'])) 
        $bill_output['billmes'].= " (Transaction type: ".$_POST['transaction_type'].") ";

    if(!empty($_POST['transaction_id']))   
        $bill_output['billmes'].= " (Transaction ID: ".$_POST['transaction_id'].") ";

    if(!empty($_POST['customer_id']))      
        $bill_output['billmes'].= " (Customer ID: ".$_POST['customer_id'].") ";

    if(!empty($_POST['site_id']))          
        $bill_output['billmes'].= " (Site ID: ".$_POST['site_id'].") ";

    if(!empty($_POST['product_id']))       
        $bill_output['billmes'].= " (Product ID: ".$_POST['product_id'].") ";

    if(!empty($_POST['username']))         
        $bill_output['billmes'].= " (Username/Password: ".$_POST['username'].'/'.$_POST['password'].") ";

    if (isset($total)) {
        $payment_return = array(
            'total' => $total
        );
    }

    if ($_POST['sign']) {

        $currency = $_POST['nbx_currency_code'];

        $secret_key = func_query_first_cell("select param02 from $sql_tbl[ccprocessors] where processor='cc_chrono.php'");

        // Callback: sharedsec.customer_id.trans_id.transaction_type.total
        $valid_sign = md5($secret_key.$_POST['customer_id'].$_POST['transaction_id'].$_POST['transaction_type'].$_POST['total']);

        if ($valid_sign != $_POST['sign']) {
            $bill_output['billmes'] .= " (Warning: MD5 checksum NOT MATCHED, order declined)";
            $bill_output['code'] = 2;
        } else {
            $bill_output['billmes'] .= " (MD5 checksum matched)";
        }
    }

    $skey = $_POST['cs1'];

    include($xcart_dir.'/payment/payment_ccmid.php');
    include($xcart_dir.'/payment/payment_ccwebset.php');

} elseif (!empty($cs1)) {

    require './auth.php';

    $skey = $cs1;

    $tmp = func_query_first("select sessionid, trstat from $sql_tbl[cc_pp3_data] where ref='".$skey."'");

    $tmp_ = explode("|", $tmp['trstat']);

    if($tmp_[0] == 'GO') {

        $bill_output['sessid'] = $tmp['sessionid'];
        $bill_output['code'] = $decline ? 2 : 3;

        if($decline)
            $bill_output['billmes'] = 'Declined';

        include($xcart_dir.'/payment/payment_ccend.php');

    } else {

        include($xcart_dir.'/payment/payment_ccview.php');

    }

} else {

    if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

    // Prepare data for the transaction

    $_orderids = $module_params['param05'].join("-",$secure_oid);

    if (!$duplicate)
        db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref, sessionid, param1, trstat) VALUES ('".$_orderids."', '".$XCARTSESSID."', '".$cart["total_cost"]."', 'GO|".implode('|',$secure_oid)."')");

    $userinfo['b_country_'] = func_query_first_cell("select code_A3 from $sql_tbl[countries] where code='".$userinfo["b_country"]."'");

    $cb_url      = $current_location.'/payment/cc_chrono.php';
    $shared_key  = $module_params['param02'];
    $serviceid   = $module_params['param01'];
    $curr        = $module_params['param03'];
    $lang        = $module_params['param04'];
    $description = "Order(s) #" . $_orderids;

//    Payment page: product_id-product_price-Sharedsec
    $sign = md5($serviceid."-".$cart['total_cost']."-".$shared_key);

    $post = array();
    $post['product_id']             = $serviceid;
    $post['product_name']           = $description;
    $post['product_price']          = $cart['total_cost'];
    $post['product_price_currency'] = $curr;
    $post['language']               = $lang;
    $post['f_name']                 = $userinfo['b_firstname'];
    $post['s_name']                 = $userinfo['b_lastname'];
    $post['street']                 = $userinfo['b_address'];
    $post['city']                   = $userinfo['b_city'];
    $post['state']                  = (!empty($userinfo['b_state'])) ? $userinfo['b_state'] : 'N/A';
    $post['zip']                    = $userinfo['b_zipcode'];
    $post['country']                = $userinfo['b_country_'];
    $post['phone']                  = $userinfo['phone'];
    $post['email']                  = $userinfo['email'];
    $post['cs1']                    = $_orderids;
    $post['sign']                   = $sign;
    $post['cb_url']                 = $cb_url;
    $post['decline_url']            = $cb_url . "?decline=1&cs1=" . $_orderids;
    $post['cb_type']                = 'P';

    func_create_payment_form("https://secure.chronopay.com/index_shop.cgi", $post, $module_params['module_name']);

    exit();
}

?>
