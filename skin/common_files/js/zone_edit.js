/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Zones editing functions
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: zone_edit.js,v 1.2 2010/05/27 13:43:07 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

function checkZone(zone, name) {

  var codes;

  var obj = document.getElementById(name);
  if (zone == 'ALL') {
    for (var x = 0; x < obj.options.length; x++)
      obj.options[x].selected = true;
    return true;
  }

  eval('codes = zones.'+zone);
  if (codes)
    for(var x = 0; x < obj.options.length; x++)
      eval('obj.options[x].selected = codes.'+obj.options[x].value);
}
