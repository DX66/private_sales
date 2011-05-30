/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Popup image selection
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: popup_image_selection.js,v 1.2 2010/05/27 13:43:07 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

/**
 * Display popup window
 */
function popup_image_selection (type, id, imgid) {
  return window.open("image_selection.php?type="+type+"&id="+id+"&imgid="+imgid,"selectimage","width=500,height=350,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no");
}

/**
 * Reset new selected image
 */
function popup_image_selection_reset (type, id, imgid) {
  if (document.getElementById(imgid)) {
    var ts = new Date();
    document.getElementById(imgid).src = xcart_web_dir+"/image.php?type="+type+"&id="+id+"&ts="+ts.getTime();
    if (document.getElementById(imgid+'_text')) {
      document.getElementById(imgid+'_text').style.display = 'none';
      for (var cnt = 1; true; cnt++) {
        if (!document.getElementById(imgid+'_text'+cnt))
          break;
        window.opener.document.getElementById(imgid+'_text'+cnt).style.display = 'none';
      }
    }

    if (document.getElementById('skip_image_'+type))
      document.getElementById('skip_image_'+type).value = 'Y';
    else if (document.getElementById('skip_image_'+type+"_"+id))
      document.getElementById('skip_image_'+type+"_"+id).value = 'Y';

    if (document.getElementById(imgid+'_reset'))
      document.getElementById(imgid+'_reset').style.display = 'none';
  }
}

