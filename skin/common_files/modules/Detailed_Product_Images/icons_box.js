/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Icons box widget
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: icons_box.js,v 1.2 2010/05/27 14:09:39 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

var saved_product_thumbnail = false;
var outImgTO = false;
var dpiconMSIE6Fixed = false;

/* Icon onmouseover handler */
function dicon_over(url, w, h, isPng) {
  var img = document.getElementById('product_thumbnail');
  if (!img)
    return false;

  if (outImgTO) {
    clearTimeout(outImgTO);
    outImgTO = false;
  }

  if (!saved_product_thumbnail) {
    saved_product_thumbnail = new Image();
    saved_product_thumbnail.src = getImgSrc(img);
    saved_product_thumbnail.width = img.width;
    saved_product_thumbnail.height = img.height;
    saved_product_thumbnail.isPng = isPngFix(img);
  }

  if (!this.onmouseout)
    this.onmouseout = dicon_out;

  return dicon_set_image(url, w, h, isPng);
}

/* Icon onmouseout handler */
function dicon_out() {
  outImgTO = setTimeout(
    function() {
      dicon_set_image(
        saved_product_thumbnail.src,
        saved_product_thumbnail.width,
        saved_product_thumbnail.height,
        saved_product_thumbnail.isPng
      );
    },
    100
  );
  return true;
}

/* Change product thumbnail (temporary) */
function dicon_set_image(src, w, h, isPNG) {
  var img = document.getElementById('product_thumbnail');
  if (!img)
    return false;

  if (img.width == w && img.height == h) {
    img.src = src;
      if (isPNG)
        pngFix(img);

    return true;
  }

  img.style.display = 'none';
  img.width = w;
  img.height = h;

  if ($.browser.msie && !dpiconMSIE6Fixed && parseInt($.browser.version) < 7 && $('.image-box').height() == config_image_height) {
    $('.image-box').height(config_image_height + 2);
    dpiconMSIE6Fixed = true;
  }

  setTimeout(
    function() {
      img.onload = dicon_show;
      img.src = src;
      if (isPNG)
        pngFix(img);
    },
    50
  );

  return true;
}

/* Icon onclick handler */
function dicon_click(index) {
  if (!det_images_popup_data)
    return true;

  setTimeout(
    function() {
      imagesPreviewShow('dpi', dpimages, dpioptions, index - 1);
    },
    100
  );
  return false;
}

/* Show delayed product thumbnail */
function dicon_show() {
  this.style.display = '';
  return true;
}
