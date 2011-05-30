/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Top message functions
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: top_message.js,v 1.2 2010/05/27 13:43:07 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

/* $Id: top_message.js,v 1.2 2010/05/27 13:43:07 igoryan Exp $ */

function show_top_message(type, content) {
  $("#dialog_message").hide();
  $("#dialog_message_icon").attr({src: top_message_icon[type]});
  $("#dialog_message_title").html(top_message_title[type]);
  $("#dialog_message_content").html(content);
  $("#dialog_message").show();
}

function show_ajax_message(type, content) {
  show_top_message(type, content);
  type = type.toLowerCase();
  if (type == "")
    type = "i";

  $("#ajax_message").hide();
  $("#ajax-dialog-main").addClass("message-" + type);
  $("#ajax-dialog-img").addClass("close-img-" + type);
  $("#ajax-dialog-content").html(content);
  $("#ajax_message").show();
}
