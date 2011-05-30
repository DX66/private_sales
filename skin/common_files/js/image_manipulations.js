/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Image manipulations
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: image_manipulations.js,v 1.2.2.1 2010/08/11 10:53:27 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

/**
 * Reset descriptions and buttons when using image manipulations
 */

function reset_descr(type, id, old_width, old_height) {
  if (old_width != 0) {
    $('#' + id).attr({'width': old_width}); 
  } else {
    $('#' + id).removeAttr('width');
  }

  if (old_height != 0) {
    $('#' + id).attr({'height': old_height});
  } else {
    $('#' + id).removeAttr('height');
  }

  $('#modified_image_descr_' + type).hide();
  $('#original_image_descr_' + type).show();

  if ((document.getElementById('original_image_descr_P').style.display == '') && 
    (document.getElementById('original_image_descr_T').style.display == '')) {
    $('#image_save_msg').hide();
  }

  if (type == "P" && old_width == 0 && old_height == 0) {
    $('#tr_generate_thumbnail').hide();
  }

  $('#' + id + '_reset2').hide();
}

/**
 * AJAX callback dispatcher for image manipulations
 */
function ajax_callback_image_manipulations(data, request) {
  if (request['mode'] == 'generate_thumbnail') {
    generate_thumbnail_callback(data);
  } else if (request['mode'] == 'delete_image') {
    delete_image_callback(data, request['imgid'], request['id'], request['buttonid'], request['type']);
  }
}

/**
 * AJAX call wrapper for image manipulations
 */
function ajax_call_image_manipulations(request) {
  if ($("#fields_image").attr("checked")) {
    request['image_geid'] = geid;
  }
  if ($("#fields_thumbnail").attr("checked")) {
        request['thumbnail_geid'] = geid;
    }
  ajax_script = current_location + "/ajax_image_manipulations.php";
  $.getJSON(ajax_script, request, function (data, textStatus) { ajax_callback_image_manipulations(data, request);});
}

/**
 * Generate thumbnail callback function
 */
function generate_thumbnail_callback(data) {
  var res = data.result;
  show_ajax_message(res, data.message);
  
  var now = new Date();
  var id = data.id;
  var img = xcart_web_dir + '/image.php?type=T&ts=' + now.getTime() + '&id=' + id;

  if (res != "E") {
    img += "&tmp";
  }

  var _attr = {
    'width' : data.width,
    'height': data.height,
    'src'   : img 
  };

  $("#edit_image").attr({'src': images_dir + '/spacer.gif'}).hide().attr(_attr).show();
  $("#a_edit_image").attr({href: img});
  $("#skip_image_T").val('');
  $("#skip_image_T" + id).val('');

  if (res != "E") {
    $("#original_image_descr_T").hide();
    $("#modified_image_descr_T").html(data.descr + "&nbsp;&nbsp;<span style='color: #b51a00;'><b>" + lbl_modified + "</b></span>").show();
    $("#edit_image_reset").show();
  }
  $("#generate_thumbnail").attr("disabled", "");
  $("#image_save_msg").show();
}

/**
 * Generate thumbnail AJAX call function 
 */
function gen_thumbnail(id) {
  $("#generate_thumbnail").attr("disabled", "disabled");
  reset_image_spacer("edit_image");  
  ajax_call_image_manipulations({"mode": "generate_thumbnail", "id": id});
}

/**
 * Delete image AJAX callback function
 */
function delete_image_callback(data, imgid, id, buttonid, type) {
  var now = new Date();
  $("#" + imgid).attr({'src': images_dir + '/spacer.gif'}).hide().removeAttr("width").removeAttr("height").attr({'src': xcart_web_dir + '/image.php?type=' + type + '&ts=' + now.getTime() + '&id=' + id}).show();
  res = data.result;
  $("#" + buttonid).val(change_buttons[type]);
  $("#original_image_descr_" + type).html(lbl_no_image_uploaded);
  $("#modified_image_descr_" + type).html(lbl_no_image_uploaded);
  show_ajax_message("I", res);
}

/**
 * Reset image to loading.gif using spacer.gif
 */
function reset_image_spacer(id) {
  $("#" + id).attr({src: images_dir + '/spacer.gif'}).hide().attr({src: images_dir + '/loading.gif'}).removeAttr("width").removeAttr("height").show();
}

/**
 * Delete image AJAX call function
 */
function delete_image(imgid, type, id, buttonid) {
  $("#" + type + "image_reset").hide();
  if (type == "P") {
    $("#tr_generate_thumbnail").hide();
  }
  reset_image_spacer(imgid);
  ajax_call_image_manipulations({"mode": "delete_image", "id": id, "type": type, "imgid": imgid, "buttonid": buttonid});
}
