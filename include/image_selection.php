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
 * Image selection library
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: image_selection.php,v 1.79.2.2 2011/01/10 13:11:49 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load('backoffice','files', 'image');

x_session_register('file_upload_data');
x_session_register('upload_warning_message');

$service_fields = array('file_path', 'source', 'image_x', 'image_y', 'image_size', 'image_type', 'dir_upload', 'id', 'type', 'date', 'filename');

$config_data = $config['setup_images'][$type];
$userfiles_dir = func_get_files_location() . XC_DS;

/**
 * Check post_max_size exceeding
 */
$_max_filesize = func_max_upload_image_size($config_data, false, $type);
func_check_uploaded_files_sizes('userfile', 1016, $_max_filesize);

if (!isset($config['available_images'][$type]) || empty($type)) {
    func_close_window();
}

/**
 * POST method
 */
if ($REQUEST_METHOD == 'POST') {
    $upload_warning_message = '';

    $max_filesize = 0;
    $data = array();
    $data['is_copied'] = false; // file is not a copy and should not deleted

    $error = '';

    switch($source) {
    case 'S': // server path (user's files)
        $max_filesize = func_max_upload_image_size($config_data, true, $type);
        $newpath = trim(urldecode($newpath));
        if (!zerolen($newpath)) {
            $data['file_path'] = $userfiles_dir.$newpath;
        } else {
            // The file is not specified
            $error = 'empty_file';
        }
        break;
    case 'U': // URL
        $max_filesize = func_max_upload_image_size($config_data, true, $type);
        $fileurl = trim($fileurl);
        if (!zerolen($fileurl) && func_url_is_exists($fileurl)) {
            if (strpos($fileurl, '/') === 0) {
                $fileurl = $http_location.$fileurl;
            } elseif (!is_url($fileurl)) {
                $fileurl = "http://".$fileurl;
                if (!is_url($fileurl))
                    break;
            }

            $tmp = @parse_url(urldecode($fileurl));
            if (empty($tmp['path']))
                break;

            $data['file_path'] = $fileurl;
            $tmp = explode('/', $tmp['path']);
            $data['filename'] = array_pop($tmp);
        } elseif(!zerolen($fileurl)) {
            // The url cannot be loaded or http/https module is not worked
            $error = 'url_not_loadable';
        } else {
            // The url is not specified
            $error = 'empty_file';
        }
        break;

    case 'L': // uploaded file
        $max_filesize = func_max_upload_image_size($config_data, false, $type);
        if (zerolen($userfile)) {
            // The file is not specified
            $error = 'empty_file';
            break;
        }
        $_FILES['userfile']['name'] = substr($_FILES['userfile']['name'], -200);

        if (func_is_image_userfile($userfile, $userfile_size, $userfile_type)) {
            $data['is_copied'] = true; // can be deleted
            $data['filename'] = basename(stripslashes($_FILES['userfile']['name']));
            $userfile = func_move_uploaded_file('userfile');
            $data['file_path'] = $userfile;
        } else {
            // The file is not image
            $error = 'not_image';
        }
    }

    if (isset($data['file_path']) && !func_is_allowed_file($data['file_path'])) {
        // cannot accept this file
        if ($data['is_copied'])
            unlink($data['file_path']);

        unset($data['file_path']);
        #The type of file is disabled by admin
        $error = 'not_allowed';
    }

    if (!empty($error)) {
        $top_message['content'] = func_get_langvar_by_name("err_upload_" . $error);
        $top_message['type'] = 'W';
        func_header_location($HTTP_REFERER);
    }

    list(
        $data['file_size'],
        $data['image_x'],
        $data['image_y'],
        $data['image_type']) = func_get_image_size($data['file_path']);

    if (!$data['file_size'] && $source == 'U') {
        $top_message['content'] = func_get_langvar_by_name("txt_upload_url_warning");
        $top_message['type'] = 'W';
        func_header_location($HTTP_REFERER);
    }

    if ($data['file_size'] == 0) {
        // Ignore non readable or zero-sized
        if ($data['is_copied'])
            unlink($data['file_path']);

        $data['file_path'] = '';
        $data['is_copied'] = false;
    }

    if (!isset($data['filename'])) {
        $data['filename'] = basename($data['file_path']);
    }

    if ($max_filesize && $data['file_size'] > $max_filesize) {
        $upload_warning_message = func_get_langvar_by_name('txt_max_file_size_warning2',array('size1' =>$max_filesize, 'size2' => $data['file_size']),false,true);
        x_session_save();
        @unlink($data['file_path']);
        func_header_location($HTTP_REFERER);
    }

    $data['source'] = $source;
    $data['id'] = $id;
    $data['type'] = $type;
    $data['date'] = XC_TIME;

    $file_upload_data[$type] = $data;

    x_session_save();

    $image_data = array(
        'image_x' => $data['image_x'],
        'image_y' => $data['image_y'],
        'image_type' => $data['image_type'],
        'image_size' => $data['file_size']
    );
    $smarty->assign('image', $image_data);
    $alt = func_display('main/image_property.tpl', $smarty, false);

    $add_descr = $add_dimensions = '';
    if ($type == 'P' || $type == 'T') {
        $max_x = $config['images_dimensions'][$type]['width'];
        $max_y = $config['images_dimensions'][$type]['height'];

        if ($data['image_x'] > $max_x || $data['image_y'] > $max_y) {
            list($max_x, $max_y) = func_get_proper_dimensions ($data['image_x'], $data['image_y'], $max_x, $max_y);
            $add_dimensions .= "window.opener.document.getElementById('".$imgid."').height='$max_y';
                window.opener.document.getElementById('".$imgid."').width='$max_x';";
        } else {
            $add_dimensions .= "window.opener.document.getElementById('".$imgid."').height='$data[image_y]';
                window.opener.document.getElementById('".$imgid."').width='$data[image_x]';";
        }

        $smarty->assign('show_modified', 1);
        $descr = str_replace(array("\n","\r",'"'), array("\\n","",'\"'), func_display("main/image_property2.tpl", $smarty, false));

        $add_descr = "if (window.opener.document.getElementById('original_image_descr_$type')) {
            window.opener.document.getElementById('original_image_descr_$type').style.display = \"none\";
        }
        if (window.opener.document.getElementById('modified_image_descr_$type')) {
            window.opener.document.getElementById('modified_image_descr_$type').innerHTML = \"$descr\";
            window.opener.document.getElementById('modified_image_descr_$type').style.display = \"\";
        }";

        $add_descr .= "if (window.opener.document.getElementById('".$type."image_reset')) {
            window.opener.document.getElementById('".$type."image_reset').disabled = \"\";
        }";

        $add_descr .= "if (window.opener.document.getElementById('a_".$imgid."')) {
            window.opener.document.getElementById('a_".$imgid."').href = '".$xcart_web_dir."/image.php?type=".$type."&id=".$id."&tmp=".XC_TIME."';
        }";

        $add_descr .= "if (window.opener.document.getElementById('image_save_msg')) {
            window.opener.document.getElementById('image_save_msg').style.display = '';
        }";

        $add_descr .= "if (window.opener.document.getElementById('".$imgid."_reset')) {
            window.opener.document.getElementById('".$imgid."_reset').style.display = \"\";
        }";

        if ($type == 'P') {
            $add_descr .= "if (window.opener.document.getElementById('tr_generate_thumbnail')) {
                window.opener.document.getElementById('tr_generate_thumbnail').style.display = \"\";
            }";
        }
    }

    echo "<script type=\"text/javascript\">
<!--
function pngFix(elm)
{
    if (!elm || !elm.tagName || elm.tagName.toUpperCase() != 'IMG')
        return false;

    var src = elm.src.replace(/\(/g, '%28').replace(/\)/g, '%29');
    var w = elm.width;
    var h = elm.height
    elm.src = '" . $smarty->get_template_vars('ImagesDir'). "/spacer.gif';
    elm.style.filter = 'progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\"' + src + '\",sizingMethod=\"scale\")';
    elm.width = elm._w;
    elm.height = elm._h;

    return true;
}

if (window.opener.document.getElementById('".$imgid."')) {
    $add_dimensions
    window.opener.document.getElementById('".$imgid."').onload = function() {
        this._w = this.width;
        this._h = this.height;
        if (typeof(window.pngFix) != 'undefined')
            pngFix(this);
        this.onload = false;
    }
    window.opener.document.getElementById('".$imgid."').src = '".$xcart_web_dir."/image.php?type=".$type."&id=".$id."&tmp=".XC_TIME."';
    window.opener.document.getElementById('".$imgid."').alt = \"".str_replace(array("\n","\r",'"'), array("\\n","",'\"'), $alt)."\";
    var i = window.opener.document.getElementById('".$imgid."');
    $add_descr

} else if (window.opener.document.getElementById('".$imgid."_0')) {
    var cnt = 0;
    while (window.opener.document.getElementById('".$imgid."_'+cnt)) {
        window.opener.document.getElementById('".$imgid."_'+cnt).onload = function() {
            this._w = this.width;
            this._h = this.height;
            if (typeof(window.pngFix) != 'undefined')
                pngFix(this);
            this.onload = false;
        }
        window.opener.document.getElementById('".$imgid."_'+cnt).src = '".$xcart_web_dir."/image.php?type=".$type."&id=".$id."&tmp=".XC_TIME."';
        var i = window.opener.document.getElementById('".$imgid."_'+cnt);
        cnt++;
    }
}

if (window.opener.document.getElementById('".$imgid."_text')) {
    window.opener.document.getElementById('".$imgid."_text').style.display = '';
    var cnt = 1;
    while (window.opener.document.getElementById('".$imgid."_text' + cnt)) {
        window.opener.document.getElementById('".$imgid."_text' + cnt).style.display = '';
        cnt++;
    }
}

if (window.opener.document.getElementById('skip_image_".$type."')) {
    window.opener.document.getElementById('skip_image_".$type."').value = '';

} else if (window.opener.document.getElementById('skip_image_".$type."_".$id."')) {
    window.opener.document.getElementById('skip_image_".$type."_".$id."').value = '';
}

if (window.opener.document.getElementById('".$imgid."_reset'))
    window.opener.document.getElementById('".$imgid."_reset').style.display = '';

if (window.opener.document.getElementById('".$imgid."_onunload'))
    window.opener.document.getElementById('".$imgid."_onunload').value = 'Y';

window.close();
-->
</script>";
    exit;
}

$_table = $sql_tbl['images_'.$type];
$_field = $config['available_images'][$type] == 'U' ? "id" : "imageid";

$smarty->assign('type', $type);
$smarty->assign('imgid', $imgid);
$smarty->assign('id', $id);
$smarty->assign('config_data', $config_data);

if ($upload_warning_message != '') {
    $upload_warning = $upload_warning_message;
    $upload_warning_message = '';
    x_session_save();
}
else {
    $max_filesize = func_convert_to_megabyte(func_max_upload_image_size($config_data, false, $type));
    $upload_warning = func_get_langvar_by_name('txt_max_file_size_warning',array('size' =>$max_filesize),false,true);
}

$smarty->assign('upload_warning', $upload_warning);

func_display('main/popup_image_selection.tpl',$smarty);

?>
