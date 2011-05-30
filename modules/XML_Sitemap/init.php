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
 * Module initializaition
 *
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @category   X-Cart
 * @package    Modules
 * @subpackage XML Sitemap
 * @version    $Id: init.php,v 1.11.2.1 2011/01/10 13:12:04 ferz Exp $
 * @since      4.4.0
 */

if (!defined('XCART_START')) { header('Location: ../../'); die('Access denied'); }

if (
    defined('AREA_TYPE')
    && in_array(constant('AREA_TYPE'), array('A', 'P'))
) {
    // Process changes on the module options page
    if (
        isset($_GET['option'])
        && $_GET['option'] == 'XML_Sitemap'
    ) {

        if (
            $_SERVER['REQUEST_METHOD'] == 'POST'
            && isset($_POST['xmlmap'])
        ) {
            
            switch ($_POST['xmlmap']['config']) {
                case 'add':
                    $xmlmap_error = xmlmap_extra_addurl($_POST['xmlmap']['add']);
                    break;

                case 'delete':
                    $xmlmap_error = xmlmap_extra_delurls($_POST['xmlmap']['delete']);
                    break;

                case 'update':
                    $xmlmap_error = xmlmap_extra_updateurls($_POST['xmlmap']['update']);
                    break;

                case 'generate':
                    $xmlmap_error = xmlmap_generate();
                    
                default:
                    break;
                    
            }
           
            // Store error or success message in session
            x_session_register('top_message');

            if (!empty($xmlmap_error)) {

                $top_message['content'] = $xmlmap_error;
                $top_message['type'] = 'E';

            } else {

                $top_message['content'] = func_get_langvar_by_name('lbl_done');
                $top_message['type'] = 'I';

            }

            func_header_location($_SERVER['REQUEST_URI']);

        } else {

            $smarty->assign('xmlmap_extra',          xmlmap_extra_geturls());
            $smarty->assign('additional_config',     'modules/XML_Sitemap/config.tpl');

        }

    }

    // CUID lastmod entry for modified items
    if (
        $_SERVER['REQUEST_METHOD'] == 'POST'
        && isset($_POST['mode'])
    ) {
        switch ($_POST['mode']) {
            case 'update':
                // Update lastmod entry for category
                if (isset($_POST['cat'])) {
                    xmlmap_update_lastmod('C', $_POST['cat']);
                }

                break;

            case 'product_modify':
                // Update lastmod entry for product
                if (isset($_POST['productid'])) {
                    xmlmap_update_lastmod('P', $_POST['productid']);
                }

                break;

            case 'details':
                // Update lastmod entry for manufacturer
                if (isset($_POST['manufacturerid'])) {
                    xmlmap_update_lastmod('M', $_POST['manufacturerid']);
                }

                break;

            case 'modified':
                // Update lastmod entry for static page
                if (isset($_POST['pageid'])) {
                    xmlmap_update_lastmod('S', $_POST['pageid']);
                }

                break;

            case 'delete':
                // Remove lastmod entry
                xmlmap_delete_lastmod();

                break;

            default:
                break;
        }
    }
}
?>
