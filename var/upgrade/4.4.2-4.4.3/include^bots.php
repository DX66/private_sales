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
 * Bot identificator module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: bots.php,v 1.26.2.2 2011/01/10 13:11:48 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

# IN $HTTP_USER_AGENT

# OUT    IS_ROBOT ROBOT constants
#        is_robot robot sess vars
#        is_robot smarty var

x_session_register('is_robot');
x_session_register('robot');

if(
    !empty($HTTP_USER_AGENT)
    && !defined('IS_ROBOT')
    && empty($is_robot)
) {

    $ua = array(
        "X-Cart info"    => array(
            "X-Cart info",
            "X-Cart Catalog Generator",
        ),
        'Google'        => array(
            'Googlebot',
            "Mediapartners-Google",
            "Googlebot-Mobile",
            "Googlebot-Image",
            "Adsbot-Google",
        ),
        'Yahoo'            => array(
            'Slurp',
            'YahooSeeker',
        ),
        'Microsoft'        => array(
            'MSNBot',
            "MSNBot-Media",
            "MSNBot-NewsBlogs",
            "MSNBot-Products",
            "MSNBot-Academic",
        ),
        'Ask'            => array(
            'Teoma',
        ),
    );

    foreach ($ua as $k => $v) {

        foreach ($v as $u) {

            if (stristr($HTTP_USER_AGENT, $u) !== false) {

                define('IS_ROBOT', true);
                define('ROBOT', $k);

                break;

            }

        }

        if (defined('IS_ROBOT')) break;

    }

    unset($ua);

    if (defined('IS_ROBOT')) {

        $is_robot = 'Y';
        $robot = ROBOT;

    } else {

        $is_robot = 'N';

    }

} elseif (defined('IS_ROBOT')) {

    $is_robot = 'Y';

    if (defined('ROBOT')) {

        $robot = constant('ROBOT');

    }

} elseif ($is_robot == 'Y') {

    define('IS_ROBOT', true);
    define('ROBOT', $robot);

}

$smarty->assign('is_robot', $is_robot);

function func_is_external_robot()
{
    if (!defined('IS_ROBOT'))
        return false;

    $robot = defined('ROBOT') ? constant('ROBOT') : '';
    
    $is_external_robot = strpos($robot, 'X-Cart') === false;

    return $is_external_robot;
}

?>
