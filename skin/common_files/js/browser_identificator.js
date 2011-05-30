/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Browser identificator script, sends statistics
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: browser_identificator.js,v 1.2 2010/05/27 13:43:06 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

var scriptNode = document.createElement("script");
scriptNode.type = "text/javascript";
setTimeout(
  function() {
    if (!scriptNode)
      return;

    scriptNode.src = xcart_web_dir + "/adaptive.php?send_browser=" +
      (localIsDOM ? "Y" : "N") + (localIsStrict ? "Y" : "N") + (localIsJava ? "Y" : "N") + "|" + 
      localBrowser + "|" + 
      localVersion + "|" + 
      localPlatform + "|" + 
      (localIsCookie ? "Y" : "N") + "|" + 
      screen.width + "|" + 
      screen.height + "|" + 
      current_area;
    document.getElementsByTagName('head')[0].appendChild(scriptNode);
  },
  3000
);
