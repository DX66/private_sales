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
 * Cookie detection script
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: nocookie_warning.php,v 1.28.2.2 2011/01/10 13:11:49 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

if (
    defined('IS_ROBOT')
    || defined('SKIP_COOKIE_CHECK')
    || !defined('AREA_TYPE')
) {
    return;
}

if (
    (
        empty($_COOKIE)
        || isset($NO_COOKIE_WARNING)
    ) && (
        $REQUEST_METHOD == 'POST'
        || (
            $REQUEST_METHOD == 'GET'
            && (
                isset($_GET['sl'])
                && !isset($_GET['is_https_redirect'])
            ) || isset($NO_COOKIE_WARNING)
        )
    )
) {
    if (isset($NO_COOKIE_WARNING)) {

        // stage 2: check if cookies was set

        if (empty($_COOKIE)) {

            if ($NO_COOKIE_WARNING == 1) {

                // second try
                $nocookie_redirect = $PHP_SELF . '?NO_COOKIE_WARNING=2&ti=' . $ti;

            } elseif (defined('AREA_TYPE')) {

                // cookies are not enabled yet
                $nocookie_redirect = 'error_message.php?error=disabled_cookies&ti=' . $ti;

            } else {
                // cookies are not enabled yet and the user is redirected to the zone from which the initial request was made

                $save_data = func_db_tmpread(stripslashes($ti));

                $prefix = $xcart_catalogs['customer'];

                if ($save_data['__area']) {

                    switch($save_data['__area']) {

                        case 'A':
                            $prefix = $xcart_catalogs['admin'];
                            break;

                        case 'P':
                            $prefix = $xcart_catalogs['provider'];
                            break;

                        case 'B';

                            if (!empty($active_modules['XAffiliate'])) {
                                $prefix = $xcart_catalogs['partner'];
                                break;
                            }

                        default:
                            $prefix = $xcart_catalogs['customer'];

                    }

                }

                func_header_location($prefix . '/error_message.php?error=disabled_cookies&ti=' . $ti);

            }

        } else {

            $save_data = func_db_tmpread(stripslashes($ti), true);

            if (is_array($save_data)) {

                extract($save_data);

                foreach(
                    array(
                        '_GET',
                        '_POST',
                        '_SERVER',
                    ) as $__avar
                ) {

                    $reject = func_init_reject(X_REJECT_OVERRIDE);

                    func_var_cleanup($__avar);

                    func_init_reject(X_REJECT_CLEAN);

                }

            }

            return;

        }

    } else {

        // Stage 1: save the data

        // Defining a situation, in which a POST request comes from a page
        // located in a different domain, or in which a POST request is made
        // directly from a page stored locally on a user's computer

        if (empty($HTTP_REFERER)) {

            $repost = true;

        } else {

            $old_page = @parse_url($HTTP_REFERER);

            $repost = (
                !is_array($old_page)
                || (
                    $old_page['domain'] != $_SERVER['HTTP_HOST']
                    || (
                        $old_page['scheme'] == 'http'
                        && $HTTPS
                    ) || (
                        $old_page['scheme'] == 'https'
                        && !$HTTPS
                    )
                )
            );

        }

        if (
            !$repost
            && preg_match("/(?:^|\/)([\w\d_]+\.php)\??(.*)/", $REQUEST_URI, $_no_save_match)
            && $_no_save_match[1] == 'login.php'
        ) {

            $save_data = false;

            if (!empty($xcart_catalogs[$redirect]))
                $prefix = $xcart_catalogs[$redirect] . '/';

        } else {

            $save_data = array (
                'REQUEST_METHOD' => $REQUEST_METHOD,
                '_POST'          => $_POST,
                '_GET'           => $_GET,
                '_SERVER'        => $_SERVER,
                'PHP_SELF'       => $PHP_SELF,
                'QUERY_STRING'   => $QUERY_STRING,
                'HTTP_REFERER'   => $HTTP_REFERER,
                '__area'         => defined('AREA_TYPE') ? constant('AREA_TYPE') : (!empty($current_type) ? $current_type : false),
            );

        }

        $id = func_db_tmpwrite($save_data);

        if ($repost) {

            $nocookie_redirect = $PHP_SELF . '?NO_COOKIE_WARNING=2&ti=' . $id;

        } else {

            $nocookie_redirect = $prefix . 'error_message.php?error=disabled_cookies&ti=' . $id;
        }

    }

    if (isset($nocookie_redirect)) {
    
        if (func_is_ajax_request()) {

            func_reload_parent_window($nocookie_redirect);

        } else {
            
            func_header_location($nocookie_redirect);
        }
    
    }

}
?>
