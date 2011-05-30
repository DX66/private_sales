/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Demo preview functions
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: admin_preview.js,v 1.2 2010/05/27 13:43:06 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

$.event.add(
  window,
  "load",
  function() {
    $('form').unbind('submit').bind(
      'submit',
      function() {
        alert(txt_this_form_is_for_demo_purposes);
        return false;
      }
    );
    $('a').unbind('click').bind(
      'click',
      function(e) {
        if (this.href && this.href.search(/javascript:/) != -1)
          return false;

        if (!e)
          e = event;

        if (e.stopPropagation)
          e.stopPropagation();
        else
          e.cancelBubble = true;

        alert(txt_this_link_is_for_demo_purposes);
        return false;
      }
    );
  }
);
