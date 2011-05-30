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
 * Memcache functions
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.memcache.php,v 1.2.2.1 2011/01/10 13:11:52 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

function func_get_mcache($name, $params = array())
{
    global $data_caches;

    $func_get_data = $data_caches[$name]['func'];

    $ttl = $data_caches[$name]['ttl'];

    $key = 'inner_cache_' . $name;

    $data = func_get_mcache_data($key);

    $store_data = false;

    if (false === $data) {

        $data = call_user_func_array($func_get_data, $params);

        $store_data = true;

    }

    if (isset($data_caches[$name]['func_after_cache']))
        $data = call_user_func($data_caches[$name]['func_after_cache'], $data);

    if ($store_data) {

        $result = func_store_mcache_data($key, $data, isset($data_caches[$name]['ttl']) ? $data_caches[$name]['ttl'] : null);

    }

    return $data;
}

function func_get_mcache_data($key)
{
    global $memcache;

    return $memcache->get($key);
}

function func_store_mcache_data($key, $value, $ttl = null)
{
    global $memcache;

    if ($memcache) {

        return $memcache->set($key, $value, MEMCACHE_COMPRESSED, is_null($ttl) ? 0 : $ttl);

    }
}

function func_delete_mcache_data($key)
{
    global $memcache;

    if ($memcache) {

        return $memcache->delete($key);

    }
}

function func_flush_mcache()
{
    global $memcache;

    if ($memcache) {

        return $memcache->flush();

    }

}

function func_init_mcache()
{
    global $memcache;

    if (class_exists('Memcache')) {

        $memcache = new Memcache;

        return $memcache->connect(constant('MEMCACHE_SERVER_ADDRESS'), constant('MEMCACHE_SERVER_PORT'));

    }
}

function func_remove_mcache_config()
{
    if (defined('REMOVE_MCACHE_CONFIG')) {
        func_delete_mcache_data('inner_config');
        func_delete_mcache_data('inner_schemes');
    }
}

if (
    defined('USE_MEMCACHE_DATA_CACHE')
    && true === constant('USE_MEMCACHE_DATA_CACHE')
) {

    func_init_mcache();

}

?>
