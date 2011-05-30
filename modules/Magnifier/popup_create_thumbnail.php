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
 * Create a thumbnail for X-Magnifier image
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: popup_create_thumbnail.php,v 1.19.2.1 2011/01/10 13:11:58 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }

if (empty($imageid)) {
    func_close_window();
}

if (empty($productid))
    $productid = func_query_first_cell("SELECT id FROM $sql_tbl[images_Z] WHERE imageid = '$imageid'");

if (empty($productid)) {
    func_close_window();
}

if (!func_check_dir_permissions($xcart_dir.'/images/Z/'.$productid.'/'.$imageid.'/')) {
    echo func_get_langvar_by_name('msg_adm_incorrect_store_n_files_perms', array('path' => $xcart_dir.'/images/Z/'.$productid.'/'.$imageid.'/'), false, true);
    echo '<br /><br /><a href="javascript:window.close()">'.func_get_langvar_by_name("lbl_close_window", false, false, true).'</a>';
    exit;
}

$image = func_query_first("SELECT * FROM $sql_tbl[images_Z] WHERE imageid='$imageid'");

$image_path = $image['image_path'];
if (empty($image_path)) {
    $dir = @opendir($xcart_dir.'/images/Z/'.$productid.'/'.$imageid);
    if ($dir) {
        while ($f = readdir($dir)) {
            if (strncmp('level_0.', $f, 8) === 0 && is_file($xcart_dir.'/images/Z/'.$productid.'/'.$imageid.'/'.$f)) {
                $image_path = $xcart_dir.'/images/Z/'.$productid.'/'.$imageid.'/'.$f;
                break;
            }
        }
        closedir($dir);
    }

} else {
    $tmp = pathinfo($image_path);
    $image_path = $xcart_dir.'/images/Z/'.$productid.'/'.$imageid.'/level_0.'.$tmp['extension'];
}

if (empty($image_path) || !file_exists($image_path)) {
    echo func_get_langvar_by_name('msg_adm_no_thumbnail_image', array(), false, true);
    echo '<br /><br /><a href="javascript:window.close()">'.func_get_langvar_by_name("lbl_close_window", false, false, true).'</a>';
    exit;
}

/**
 * Check input data
 */
if ($REQUEST_METHOD == 'POST') {
    $productid = func_query_first_cell("SELECT id FROM $sql_tbl[images_Z] WHERE imageid = '$imageid'");

    $tmp = pathinfo($image_path);
    func_create_thumbnail($image_path, $x0, $y0, $x1, $y1, $xcart_dir.'/images/Z/'.$image['id'].'/'.$imageid.'/', $tmp['extension']);

    echo "<script type=\"text/javascript\">
    var i = window.opener.document.getElementById('thmb_$imageid');
    if (i)
        i.getElementsByTagName('IMG')[0].src = \"".$current_location."/images/Z/".$image["id"]."/".$imageid."/thumbnail.".$tmp["extension"]."?".XC_TIME."\";
</script>";

    func_close_window();
}

$smarty->assign('level0_path', $current_location.'/images/Z/'.$productid.'/'.$imageid.'/'.basename($image_path));
$smarty->assign('imageid', $imageid);
$smarty->assign('productid', $productid);

func_display('modules/Magnifier/popup_create_thumbnail.tpl', $smarty);
?>
