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
 * Module initialization
 *
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @category   X-Cart
 * @package    Modules
 * @subpackage Sitemap
 * @version    $Id: init.php,v 1.6.2.1 2011/01/10 13:12:02 ferz Exp $
 * @since      4.4.0
 */

if (!defined('XCART_START')) { header('Location: ../../'); die('Access denied'); }

if (defined('AREA_TYPE') && in_array(constant('AREA_TYPE'), array('A', 'P'))) {
	// Process changes on the module options page
	if (isset($_GET['option']) && $_GET['option'] == 'Sitemap') {

		if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['sitemap'])) {

			if (!isset($_POST['sitemap'])) {
				$sitemap_error = func_get_langvar_by_name('lbl_permission_denied');
			} else {
				switch ($_POST['sitemap']['config']) {
					case 'add':
						$sitemap_error = sitemap_extra_addurl($_POST['sitemap']['add']);
						break;

					case 'delete':
						$sitemap_error = sitemap_extra_delurls($_POST['sitemap']['delete']);
						break;

					case 'update':
						$sitemap_error = sitemap_extra_updateurls($_POST['sitemap']['update']);
						break;

					case 'generate_cache':
						$sitemap_error = sitemap_start_generate_cache($config['sitemap']['cache_limit_general'], $config['sitemap']['cache_limit_categories']);

					default:
						break;
				}
			}

			// Store error or success message in session
			x_session_register('top_message');
			if (!empty($sitemap_error)) {
				$top_message['content'] = $sitemap_error;
				$top_message['type'] = 'E';
			} else {
				$top_message['content'] = func_get_langvar_by_name('lbl_done');
				$top_message['type'] = 'I';
			}
			func_header_location($_SERVER['REQUEST_URI']);
		} else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
			if (isset($_GET['cache_generation'])) {
				sitemap_generate_cache();
			}
			$smarty->assign('sitemap_extra', sitemap_extra_geturls());
			$smarty->assign('additional_config', 'modules/Sitemap/config.tpl');
		}
	}
}
?>
