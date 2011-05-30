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
 * Ajax image manipulations processing library
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: ajax_image_manipulations.php,v 1.27.2.1 2011/01/10 13:11:48 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
    func_header_location('home.php');
}

if ($_GET['mode'] == 'generate_thumbnail') {

    // Generate thumbnail AJAX PHP part

    $id = intval($_GET['id']);

    global $top_message;

    x_load('image');
    func_generate_image($id, 'P', 'T', true, true);

    $result = (isset($top_message['type'])) ? $top_message['type'] : 'I';
    $message = $top_message['content'];
    unset($top_message);

    global $file_upload_data;

    if ($result != 'E')    {
        $real_dimensions = $file_upload_data['T'];
    } else {
        $real_dimensions = func_query_first("SELECT image_x, image_y, image_size FROM $sql_tbl[images_T] WHERE id='$id'");
        global $login_type;
        $message .= '. '.func_get_langvar_by_name((($login_type == "A") ? "lbl_see_log" : "lbl_contact_admin"), NULL, false, true);
    }

    list($x, $y) = func_get_proper_dimensions($real_dimensions['image_x'], $real_dimensions['image_y'], $config['images_dimensions']['T']['width'], $config['images_dimensions']['T']['height']);

    if ($real_dimensions['image_x'] <= $x && $real_dimensions['image_y'] <= $y) {
        $width = $real_dimensions['image_x'];
        $height = $real_dimensions['image_y'];
    } else {
        $width = $x;
        $height = $y;
    }

    $real_dimensions['image_type'] = 'T';

    $smarty->assign('image', $real_dimensions);

    $descr = func_display('main/image_property2.tpl', $smarty, false);

    $xml = '{"result": "' . $result . '", "message": "' . $message . '", "id": "' . $id . '", "width": "' . $width . '", "height": "' . $height . '", "descr": "' . $descr . '"}';

    header('Content-Type: text/x-json;');

    echo $xml;

} elseif ($_GET['mode'] == 'delete_image') {

    // Delete image AJAX PHP part

    $id = intval($_GET['id']);
    $type = $_GET['type'];
    $image_geid = $_GET['image_geid'];
    $thumbnail_geid = $_GET['thumbnail_geid'];

    if (!in_array($type, array('P', 'T'))) $type = 'T';

    x_load('image', 'product');

    x_session_register('file_upload_data');

    if (isset($file_upload_data[$type])) unset($file_upload_data[$type]);

    func_delete_image($id, $type);
    if (!empty($thumbnail_geid) && $type == 'T') {
        while ($pid = func_ge_each($thumbnail_geid, 100, $id)) {
            func_delete_image($pid, 'T');
        }
    }

    if (!empty($image_geid) && $type == 'P') {
        while ($pid = func_ge_each($image_geid, 100, $id)) {
            func_delete_image($pid, 'P');
        }
    }

    $result = func_get_langvar_by_name('msg_adm_product_upd', NULL, false, true);

    $xml = '{"result": "' . $result . '"}';

    header('Content-Type: text/x-json; charset=utf-8');

    echo $xml;
}

exit;
?>
