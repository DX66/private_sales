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
 * Functions for the news management module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.php,v 1.19.2.1 2011/01/10 13:11:59 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../../"); die("Access denied"); }

function func_news_get($lngcode, $only_first=false, $only_count=false, $limit=false)
{
    global $sql_tbl, $config;

    $query = "
SELECT
    ".($only_count ? "COUNT($sql_tbl[newsletter].listid)": "$sql_tbl[newsletter].*")."
FROM
    $sql_tbl[newslists], $sql_tbl[newsletter]
WHERE
    $sql_tbl[newslists].avail='Y' AND
    $sql_tbl[newslists].show_as_news='Y' AND
    $sql_tbl[newslists].lngcode='$lngcode' AND
    $sql_tbl[newslists].listid=$sql_tbl[newsletter].listid AND
    $sql_tbl[newsletter].show_as_news='Y'
ORDER BY $sql_tbl[newsletter].date DESC";

    if ($limit !== false) {
        $query .= " LIMIT $limit";
    } elseif ($only_first) {
        $query .= " LIMIT 1";
    }

    if ($only_count)
        return func_query_first_cell($query);

    $result = func_query($query);
    if (!is_array($result) || empty($result))
        return false;

    foreach ($result as $k=>$row) {
        $result[$k]['send_date'] += $config["Appearance"]["timezone_offset"];
    }

    return     $only_first ? $result[0] : $result;
}

function insert_news_subscription_allowed($params)
{
    global $sql_tbl;

    return func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[newslists] WHERE avail='Y' AND subscribe='Y' AND lngcode='$params[lngcode]'");
}

function insert_news_exist($params)
{
    global $sql_tbl;

    return func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[newslists] WHERE avail='Y' AND show_as_news='Y' AND lngcode='$params[lngcode]'");
}

?>
