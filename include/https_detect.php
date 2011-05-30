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
 * Called from prepare.php
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: https_detect.php,v 1.21.2.1 2011/01/10 13:11:49 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

$HTTPS_RELAY = false;

$HTTPS = (
    (
        isset($_SERVER['HTTPS'])
        && stristr($_SERVER['HTTPS'], 'on')
    ) || (
        isset($_SERVER['HTTPS'])
        && $_SERVER['HTTPS'] == 1
    ) || (
        $_SERVER['SERVER_PORT'] == 443
    ) || (
        isset($_SERVER['SCRIPT_URI'])
        && is_string($_SERVER['SCRIPT_URI'])
        && !strncmp($_SERVER['SCRIPT_URI'], 'https://', 8)
    )
);

/**
 * Uncomment the code below if $HTTPS isn't detected correctly
 * (this may happen on some systems)
 */

// $HTTPS = $HTTPS || (isset($_SERVER['HTTP_FRONT_END_HTTPS']) && (stristr($_SERVER['HTTP_FRONT_END_HTTPS'], 'on') || $_SERVER['HTTP_FRONT_END_HTTPS'] == 1));

// ========================================================= //
// Please place your custom detection code below these lines //
// ========================================================= //
//
//
// If you wish to set X-Cart to work through an HTTPS proxy, define the proxy
// IP address here and set the variable $HTTPS to 'true'. X-Cart will match all
// the IP addresses it will receive with incoming requests against the IP
// address specified here and thus will be able to define whether a request is
// coming from HTTPS proxy or not.
// If the web path used for work via HTTPS proxy differs from the path used for
// work via HTTP (for example, HTTP xcart web root: '/xcart/'; HTTPS xcart web
// root: '/~example/xcart/'), you also need to set the variable $HTTPS_RELAY to
// 'true'.
// Please find an example of processing such a situation below (In the example,
// the HTTPS proxy IP address is 192.160.1.1):
//
// if ($_SERVER['REMOTE_ADDR'] == '192.160.1.1') {
//     $HTTPS_RELAY = true;
//     $HTTPS = true;
// }

?>
