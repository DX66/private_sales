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
 * Module configuration
 *
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @category   X-Cart
 * @package    Modules
 * @subpackage XML Sitemap
 * @version    $Id: config.php,v 1.10.2.1 2011/01/10 13:12:04 ferz Exp $
 * @since      4.4.0
 */

if (!defined('XCART_START')) { header('Location: ../../'); die('Access denied'); }

// Db tables added by the module
$sql_tbl['xmlmap_extra']   = 'xcart_xmlmap_extra';
$sql_tbl['xmlmap_lastmod'] = 'xcart_xmlmap_lastmod';

// Config adjustment
$config['XML_Sitemap']['filename'] = 'sitemap.xml';

/**
 * Items is a numeric array where value is an assoc array of the following options:
 * - type           - can be P(product)|C(category)|M(manufacturer)|S(static page)|H(home page)|E(extra URL)
 * - lastmod        - this value will be used _if_ item contain no entry in the xcart_xmlmap_lastmod table. Value should utilize ISO 8601 format: YYYY-MM-DDThh:mmTZD. If empty, the sitemap generation time will be used.
 * - changefreq     - can be always|hourly|daily|weekly|monthly|yearly|never.
 * - priority       - from 1.0 (extremely important) to 0.1 (not important at all).
 * - items_function - function which should return array of items for the sitemap
 * @link http://www.google.com/support/webmasters/bin/answer.py?answer=71936
 */

$config['XML_Sitemap']['items']    = array(
    0 => array(
        'type'           => 'C',
        'items_function' => 'xmlmap_get_categories',
        'properties'     => array(
            'lastmod'    => '',
            'changefreq' => 'weekly',
            'priority'   => '0.8',
        ),
    ),
    1 => array(
        'type'           => 'P',
        'items_function' => 'xmlmap_get_products',
        'properties'     => array(
            'lastmod'    => '',
            'changefreq' => 'monthly',
            'priority'   => '0.6',
        ),
    ),
    2 => array(
        'type'           => 'M',
        'items_function' => 'xmlmap_get_manufacturers',
        'properties'     => array(
            'lastmod'    => '',
            'changefreq' => 'weekly',
            'priority'   => '0.8',
        ),
    ),
    3 => array(
        'type'           => 'S',
        'items_function' => 'xmlmap_get_pages',
        'properties'     => array(
            'lastmod'    => '',
            'changefreq' => 'never',
            'priority'   => '0.2',
        ),
    ),
    4 => array(
        'type'           => 'E',
        'items_function' => 'xmlmap_get_extra',
        'properties'     => array(
            'lastmod'    => '',
            'changefreq' => 'monthly',
            'priority'   => '0.4',
        ),
    ),
    5 => array(
        'type'           => 'H',
        'items_function' => 'xmlmap_get_home',
        'properties'     => array(
            'lastmod'    => '',
            'changefreq' => 'daily',
            'priority'   => '1.0',
        ),
    ),
);
?>
