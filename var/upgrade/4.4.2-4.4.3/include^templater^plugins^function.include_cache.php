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
 * Smarty {include_cache} function plugin
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: function.include_cache.php,v 1.1.2.2 2011/01/10 13:11:53 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

/**
 * Smarty {include_cache} function plugin
 *
 * Type:     function
 * Name:     include_cache
 * Purpose:  Use cached include if possible instead real include
 * @param array parameters
 * @param Smarty
 * @return null
 */

if (!defined('XCART_START')) { header("Location: ../../../"); die("Access denied"); }

function smarty_function_include_cache($params, &$smarty)
{
    global $config; 
    static $result = array();

	$file = $params['file'];
    $use_static_var = isset($params['use_static_var']);

	if (empty($file)) {
		$smarty->trigger_error("include_cache: missing 'file' parameter");
		return;
	}

    $_data_cache_ttl = $config['General']['data_cache_ttl'] > 0 ? $config['General']['data_cache_ttl'] : 3;
    $_data_cache_ttl *= 3600;

	$saved_cache_lifetime = $smarty->cache_lifetime;
	$cache_lifetime = isset($params['cache_lifetime']) ? $params['cache_lifetime'] : $_data_cache_ttl;

	func_unset($params, 'file', 'cache_lifetime');

	$cache_id = 'smarty_|' . md5(serialize($params));

    $md5_key = $cache_id . $file;
    if (
        $use_static_var 
        && isset($result[$md5_key])
    ) {
        return $result[$md5_key];
    }

	// Save global smarty settings and variables
    $smarty->cache_lifetime = $cache_lifetime;
	$saved_caching = $smarty->caching;
	$smarty->caching = 2;

    if (is_array($params))
	foreach($params as $k => $v) {
		$saved_params[$k] = $smarty->get_template_vars($k);
	}

	$smarty->assign($params);

	//Fetch HTML content
    $content = $smarty->fetch($file, $cache_id);

	// Restore global smarty settings and variables
    $smarty->assign($saved_params);
	$smarty->cache_lifetime = $saved_cache_lifetime;
	$smarty->caching = $saved_caching;
    
    if ($use_static_var) {
        $result[$md5_key] = $content;
    }   

    return $content;
}
?>
