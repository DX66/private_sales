/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * PNG fix for IE
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: iepngfix.js,v 1.2 2010/05/27 13:43:07 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

$.event.add(
  window,
  "load",
  function() {
    $('img.png-image').each(
      function() {
        if (this.src && this.src.search(/\.png($|\?)/) != -1) {
          if (this.currentStyle.width == 'auto')
            this.style.width = this.offsetWidth + 'px';

          if (this.currentStyle.height == 'auto')
            this.style.height = this.offsetHeight + 'px';

          pngFix(this);
        }
      }
    );
  }
);
