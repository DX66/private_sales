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
 * Functions for XAffiliate module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.php,v 1.31.2.1 2011/01/10 13:12:04 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }

/**
 * Get partner affliates
 */
function func_get_affiliates($user, $level = -1, $parent_level = 0)
{
    global $sql_tbl, $config;

    if(!$user)
        return false;

    if($level == -1)
        $level = func_get_affiliate_level($user);

    $childs = func_query("SELECT * FROM $sql_tbl[customers] WHERE parent = '$user'");

    if ($childs) {
        for ($x = 0; $x < count($childs); $x++) {
            $childs[$x]['level'] = func_get_affiliate_level($childs[$x]['id']);
            $childs[$x]['level_delta'] = $childs[$x]['level'] - $parent_level + 1;
            $childs[$x]['sales'] = func_query_first_cell("SELECT SUM(commissions) FROM $sql_tbl[partner_payment] WHERE userid = '".$childs[$x]['id']."'");
            $tmp = func_get_affiliates($childs[$x]['id'], $level + 1, $parent_level);
            $childs_sales = 0;
            if ($tmp) {
                $childs[$x]['childs'] = $tmp;
                for ($y = 0; $y < count($tmp); $y++) {
                    $childs_sales += $tmp[$y]['sales'] + $tmp[$y]['childs_sales'];
                }
            }
            $childs[$x]['childs_sales'] = $childs_sales;
        }
    }

    return $childs;
}

/**
 * Get affiliate level
 */
function func_get_affiliate_level($user)
{
    global $sql_tbl;

    if(!$user)
        return false;

    $level = 0;
    do {
        $user = func_query_first_cell("SELECT parent FROM $sql_tbl[customers] WHERE id = '$user'");
        $user = addslashes($user);
        $level++;
    } while($user);
    return $level;
}

/**
 * Get parents array
 */
function func_get_parents($user)
{
    global $sql_tbl, $config;
    $parent = func_query_first_cell("SELECT parent FROM $sql_tbl[customers] WHERE id = '$user'");
    if($parent) {
        $parents[] = array('userid' => $parent, 'level' => func_get_affiliate_level($parent));
        $parents = func_array_merge($parents, func_get_parents($parent));
    }
    return $parents;
}

/**
 * Clear statistics
 */
function func_clear_stats_xaff($rsd_limit)
{
    global $sql_tbl;

    if (empty($rsd_limit)) {
        db_query("DELETE FROM $sql_tbl[partner_adv_clicks]");
        db_query("DELETE FROM $sql_tbl[partner_clicks]");
        db_query("DELETE FROM $sql_tbl[partner_views]");

    } else {
        db_query("DELETE FROM $sql_tbl[partner_adv_clicks] WHERE add_date < '$rsd_limit'");
        db_query("DELETE FROM $sql_tbl[partner_clicks] WHERE add_date < '$rsd_limit'");
        db_query("DELETE FROM $sql_tbl[partner_views] WHERE add_date < '$rsd_limit'");
    }

    return func_get_langvar_by_name('msg_adm_summary_aff_stat_del');
}

function func_xaff_mrb_prepare($output)
{
    global $current_location, $logged_userid, $partner, $xcart_catalogs, $bannerid, $bid, $iframe_referer, $data, $sql_tbl;

    if (empty($_GET['type'])) {
        // If the banners are displayed in the partner area
        $_partner = $logged_userid;
        $_bid = $bannerid;
        $data = func_query_first("SELECT * FROM $sql_tbl[partner_banners] WHERE bannerid = '$_bid'");
    } else {
        $_partner = $partner;
        $_bid = $bid;
    }
    $href = 'home.php?partner=' . $_partner;
    $partner_url = $xcart_catalogs['customer'] . '/' . $href . '&amp;bid=' . $_bid . ($iframe_referer ? '&amp;iframe_referer=' . $iframe_referer : "");
    $open_window = isset($_GET['type']) && $_GET['type']=='iframe' ? ($data['open_blank'] == 'Y'?'_blank':'_parent') : ($data['open_blank'] == 'Y'?'_blank':'_self');

    if (preg_match_all('/<#([a-zA-Z]?)(\d+)#>/Ss', $output, $preg) && !empty($preg[2])) {
        foreach($preg[2] as $k => $v) {
            $e = func_query_first("SELECT image_type, id, image_x, image_y FROM $sql_tbl[images_L] WHERE id = '$v'");
            if (!$e)
                continue;

            if ($e['image_type'] == "application/x-shockwave-flash") {
                $banner_url = urlencode($current_location . '/image.php?type=L&id=' . $e['id']);
                $flash_container = $current_location . '/flash_container.swf';
                $output = str_replace(
                    '<#' . $preg[1][$k] . $e['id'] . '#>',
                    '<object type="application/x-shockwave-flash" data="'.$flash_container.'" width="' . $e['image_x'] . '" height="' . $e['image_y'] . '">
    <param name="movie" value="' . $flash_container . '" />
    <param name="FlashVars" value="banner_url='.$banner_url.'&partner_url='. urlencode(str_replace('&amp;', '&', $partner_url)) .'&open_window='.$open_window.'" />
    <param name="menu" value="false" />
    <param name="loop" value="false" />
    <param name="quality" value="high" />
    <param name="allowScriptAccess" value="always" />
    <embed src="'.$flash_container.'" flashVars="banner_url='.$banner_url.'&partner_url='. urlencode(str_replace('&amp;', '&', $partner_url)) .'&open_window='.$open_window.'" quality="high" bgcolor="#ffffff" width="'.$e['image_x'].'" height="'.$e['image_y'].'" align="middle" allowScriptAccess="always" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" /></object>',
                    $output
                );

            } else {

                if ($preg[1][$k] == 'A')
                    $output = str_replace('<#A' . $e['id'] . '#>', '<#A#><#' . $e['id'] . '#><#/A#>', $output);

                $output = str_replace(
                    '<#' . $e['id'] . '#>',
                    '<img src="' . $current_location . '/image.php?type=L&amp;id=' . $e['id'] .'" border="0" width="' . $e['image_x'] . '" height="' . $e['image_y'] . '" alt="" />',
                    $output
                );
            }
        }
        $output = preg_replace('/<#\w?\d+#>/Ss', '', $output);
    }

    $output = str_replace(
        array('<#A#>', '<#/A#>'),
        array('<a href="' . $partner_url . '" style="border: 0px none;" target="' . $open_window . '">', '</a>'),
        $output
    );

    return $output;
}

function func_get_partner_plan($planid)
{
    global $sql_tbl;

    $partner_plan_info = func_query_first ("SELECT * FROM $sql_tbl[partner_plans] WHERE plan_id='$planid'");
    if (!$partner_plan_info)
        return false;

    $partner_plan_info['mlm'] = func_query("SELECT * FROM $sql_tbl[partner_tier_commissions] WHERE plan_id = '$planid' ORDER BY level");
    $partner_plan_info['mlm_count'] = is_array($partner_plan_info['mlm']) ? count($partner_plan_info['mlm']) : 0;
    $partner_plan_info['commissions'] = func_query("SELECT * FROM $sql_tbl[partner_plans_commissions] WHERE plan_id='$planid'");

    return $partner_plan_info;
}

function func_get_banner_type_text($banner_type)
{
    static $result = array();
    if (isset($result[$banner_type]))
        return $result[$banner_type];

    switch($banner_type) {
        case 'T':
            $banner_type_text = func_get_langvar_by_name('lbl_text_link');
            break;

        case 'G':
            $banner_type_text = func_get_langvar_by_name('lbl_graphic_banner');
            break;

        case 'M':
            $banner_type_text = func_get_langvar_by_name('lbl_media_rich_banner');
            break;

        case 'P':
            $banner_type_text = func_get_langvar_by_name('lbl_product_banner');
            break;

        case 'C':
            $banner_type_text = func_get_langvar_by_name('lbl_category_banner');
            break;

        case 'F':
            $banner_type_text = func_get_langvar_by_name('lbl_manufacturer_banner');
            break;
        default:
            $banner_type_text = func_get_langvar_by_name('lbl_text_link');
    }

    $result[$banner_type] = $banner_type_text;
    return $banner_type_text;
}
?>
