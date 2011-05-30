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
 * Configuration script
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: config.php,v 1.38.2.2 2011/01/10 13:12:03 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header('Location: ../../'); die('Access denied'); }

/**
 * Global definitions for UPS_OnLine_Tools module
 */
x_session_register('ups_reg_step', 0);
x_session_register('ups_licensetext');
x_session_register('ups_userinfo');

$css_files['UPS_OnLine_Tools'][] = array();

if (basename($REQUEST_URI) != 'ups.php') {
    x_session_unregister('ups_reg_step');
    x_session_unregister('ups_licensetext');
    x_session_unregister('ups_userinfo');
}

include $xcart_dir . '/modules/UPS_OnLine_Tools/ups_func.php';

/**
 * Set up $show_XML to <true> to display all XML-queries (for debug purposes)
 */
$show_XML = false;

// Production URL
$UPS_url = 'https://onlinetools.ups.com:443/ups.app/xml/';

$devlicense = $config['UPS_OnLine_Tools']['UPS_devlicense'];

if (
    file_exists($xcart_dir . '/config.local.UPS_OnLine_Tools.php')
    && is_readable($xcart_dir . '/config.local.UPS_OnLine_Tools.php')
) {
    include $xcart_dir . '/config.local.UPS_OnLine_Tools.php';
}

// This table provides correct service codes for different origins
// <Code returned from UPS> => array (<origin> => <service_code in xcart_shipping>)
$ups_services = array(
    '01' => array(
        'US' => 5,
        'CA' => 8,
        'PR' => 5
    ),
    '02' => array(
        'US' => 1,
        'CA' => 13,
        'PR' => 1
    ),
    '03' => array(
        'US' => 4,
        'PR' => 4
    ),
    '07' => array(
        'US' => 16,
        'EU' => 8,
        'CA' => 16,
        'PR' => 16,
        'MX' => 8,
        'OTHER_ORIGINS' => 8,
        'PL' => 8
    ),
    '08' => array(
        'US' => 15,
        'EU' => 13,
        'CA' => 15,
        'PR' => 15,
        'MX' => 13,
        'OTHER_ORIGINS' => 15,
        'PL' => 13
    ),
    '11' => array(
        'US' => 14,
        'EU' => 14,
        'CA' => 14,
        'MX' => 14,
        'PL' => 14,
        'OTHER_ORIGINS' => 14
    ),
    '12' => array(
        'US' => 3,
        'CA' => 3
    ),
    '13' => array(
        'US' => 7,
        'CA' => 12
    ),
    '14' => array(
        'US' => 6,
        'CA' => 9,
        'PR' => 6
    ),
    '54' => array(
        'US' => 17,
        'CA' => 17,
        'EU' => 17,
        'PR' => 17,
        'MX' => 11,
        'OTHER_ORIGINS' => 17,
        'PL' => 17
    ),
    '59' => array(
        'US' => 2
    ),
    '65' => array(
        'US' => 12,
        'EU' => 12,
        'CA' => 12,
        'PR' => 12,
        'MX' => 12,
        'OTHER_ORIGINS' => 12,
        'PL' => 12
    ),
    '82' => array(
        'PL' => 18
    ),
    '83' => array(
        'PL' => 19
    ),
    '85' => array(
        'PL' => 21
    ),
    '86' => array(
        'PL' => 22
    )
);

/**
 * Packages parameters: weight (lbs), dimensions (inches)
 */
$ups_packages = array(
    '00' => array(
        'name' => 'Unknown',
        'limits' => array(
            'weight' => 150,
            'length' => 108,
            'width' => 108,
            'height' => 108
        )
    ),
    '01' => array(
        'name' => 'UPS Letter / UPS Express Envelope',
        'limits' => array(
            'weight' => 1,
            'length' => 9.5,
            'width' => 12.5,
            'height' => 0.25
        )
    ),
    '02' => array(
        'name' => 'Package'
    ),
    '03' => array(
        'name' => 'UPS Tube',
        'limits' => array(
            'length' => 6,
            'width' => 38,
            'height' => 6
        )
    ),
    '04' => array(
        'name' => 'UPS Pak',
        'limits' => array(
            'length' => 12.75,
            'width' => 16,
            'height' => 2
        )
    ),
    '21' => array(
        'name' => 'UPS Express Box',
        'limits' => array(
            'length' => 13,
            'width' => 18,
            'height' => 3,
            'weight' => 30
        )
    ),
    '24' => array(
        'name' => 'UPS 25 Kg Box&#174;',
        'limits' => array(
            'length' => 17.375,
            'width' => 19.375,
            'height' => 14,
            'weight' => 55.1
        )
    ),
    '25' => array(
        'name' => 'UPS 10 Kg Box&#174;',
        'limits' => array(
            'length' => 13.25,
            'width' => 16.5,
            'height' => 10.75,
            'weight' => 22
        )
    ),
    '30' => array(
        'name' => 'Pallet (for GB or PL domestic shipments only)'
    ),
    '2a' => array(
        'name' => 'Small Express Box'
    ),
    '2b' => array(
        'name' => 'Medium Express Box'
    ),
    '2c' => array(
        'name' => 'Large Express Box'
    )
);

?>
