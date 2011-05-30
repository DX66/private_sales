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

//$Id: shipping_cache.php,v 1.13.2.1 2011/01/10 13:12:10 ferz Exp $

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

function func_is_shipping_result_in_cache($md5_str)
{
    global $sql_tbl, $XCARTSESSID;

    return func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[shipping_cache] WHERE md5_request='$md5_str' AND session_id='$XCARTSESSID'") > 0;
}

function func_save_shipping_result_to_cache($md5_str, $result, $init_result = array())
{
    global $sql_tbl, $XCARTSESSID;

    if (!empty($init_result) && is_array($init_result)) {
        foreach (array_keys($init_result) as $k)
            func_unset($result, $k);
    }

    if (empty($result))
        return;

    func_array2insert('shipping_cache', array('md5_request' => $md5_str, 'session_id' => $XCARTSESSID, 'response' => addslashes(serialize($result))), true);
}

function func_get_shipping_result_from_cache($md5_str, $result = array())
{
    global $sql_tbl, $XCARTSESSID;

    if (!is_array($result))
        $result = array();

    $return = unserialize(func_query_first_cell("SELECT response FROM $sql_tbl[shipping_cache] WHERE md5_request='$md5_str' AND session_id='$XCARTSESSID'"));
    if (empty($result))
        return $return;

    if (!empty($return) && is_array($return)) {
        foreach ($return as $v)
            $result[] = $v;
    }

    return $result;
}
?>
