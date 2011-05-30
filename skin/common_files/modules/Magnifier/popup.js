/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Popup window
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: popup.js,v 1.4 2010/07/23 05:45:56 joy Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

function popup_magnifier(id, max_x, max_y, imageid) {

    max_x = parseInt(max_x);
    max_y = parseInt(max_y);

    if (!max_x)
        max_x = 390;

    if (!max_y)
        max_y = 410;

  if (!imageid)
    imageid = '';
   
  return popupOpen(
    xcart_web_dir + '/popup_magnifier.php?productid=' + id + '&imageid=' + imageid,
    '',
    {
      width: max_x,
      height: max_y,
      maxHeight: 6000,
      maxWidth:  8000,
      draggable: true,
      resizable: true
    }
   );
}

function popup_create_thumbnail(productid, imageid, image_x, image_y) {

  window_width = image_x + 17;
  window_height = image_y + 5;

  return window.open('popup_create_thumbnail.php?imageid='+imageid+'&productid='+productid, 'Create_Thumbnail', 'width='+window_width+',height='+window_height+',toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no');
}
