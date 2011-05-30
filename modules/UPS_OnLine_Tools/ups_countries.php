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
 * UPS countries declaration
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: ups_countries.php,v 1.16.2.1 2011/01/10 13:12:03 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header('Location: ../'); die('Access denied'); }

$ups_countries = array(
    'AR' => 'Argentina',
    'AU' => 'Australia',
    'AT' => 'Austria',
    'BE' => 'Belgium',
    'BR' => 'Brazil',
    'CA' => 'Canada',
    'CL' => 'Chile',
    'CN' => 'China',
    'CR' => "Costa Rica",
    'CZ' => "Czech Republic",
    'DK' => 'Denmark',
    'DO' => "Dominican Republic",
    'FI' => 'Finland',
    'FR' => 'France',
    'DE' => 'Germany',
    'GR' => 'Greece',
    'GT' => 'Guatemala',
    'HK' => "Hong Kong",
    'HU' => 'Hungary',
    'IN' => 'India',
    'ID' => 'Indonesia',
    'IE' => 'Ireland',
    'IL' => 'Israel',
    'IT' => 'Italy',
    'JP' => 'Japan',
    'MO' => 'Macau',
    'MY' => 'Malaysia',
    'MX' => 'Mexico',
    'NL' => 'Netherlands',
    'NZ' => "New Zealand",
    'NO' => 'Norway',
    'PA' => 'Panama',
    'PH' => 'Philippines',
    'PL' => 'Poland',
    'PT' => 'Portugal',
    'PR' => "Puerto Rico",
    'SG' => 'Singapore',
    'KR' => "South Korea",
    'ES' => 'Spain',
    'SE' => 'Sweden',
    'CH' => 'Switzerland',
    'TW' => 'Taiwan',
    'TH' => 'Thailand',
    'TR' => 'Turkey',
    'GB' => 'United Kingdom (Great Britain)',
    'US' => 'United States',
    'VN' => 'Vietnam'
);

$smarty->assign('ups_countries', $ups_countries);

?>
