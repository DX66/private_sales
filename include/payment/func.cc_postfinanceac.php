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
 * Functions for "Post Finance (Advanced e-Commerce)" payment module
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: func.cc_postfinanceac.php,v 1.12.2.1 2011/01/10 13:11:53 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

/**
 * Get currencies list
 */
function func_cc_postfinanceac_get_currencies()
{
    return array(
          'AED' => 'United Arab Emirates Dirham',
          'ANG' => 'Netherlands Antillean guilder',
          'AUD' => 'Australian Dollar',
          'AWG' => 'Aruban florin',
          'BGN' => 'Bulgarian lev',
          'BRL' => 'Brazilian real',
          'BYR' => 'Belarussian ruble',
          'CAD' => 'Canadian Dollar',
          'CHF' => 'Swiss Franc',
          'CNY' => 'Yuan Renminbi',
          'CZK' => 'Czech Koruna',
          'DKK' => 'Danish Kroner',
          'EEK' => 'Estonia Kroon',
          'EGP' => 'Egyptian pound',
          'EUR' => 'EURO',
          'GBP' => 'British pound',
          'GEL' => 'Georgian Lari',
          'HKD' => 'Hong Kong Dollar',
          'HRK' => 'Croatian Kuna',
          'HUF' => 'Hungarian Forint',
          'ILS' => 'New Shekel',
          'ISK' => 'Iceland Krona',
          'JPY' => 'Japanese Yen',
          'LTL' => 'Litas',
          'LVL' => 'Lats Letton',
          'MAD' => 'Moroccan Dirham',
          'MXN' => 'Peso',
          'NOK' => 'Norwegian Kroner',
          'NZD' => 'New Zealand Dollar',
          'PLN' => 'Polish Zloty',
          'RON' => 'Romanian leu',
          'RUB' => 'Russian Rouble',
          'SEK' => 'Swedish Krone',
          'SGD' => 'Singapore Dollar',
          'SKK' => 'Couronne Slovaque',
          'THB' => 'Thai Bath',
          'TRY' => 'Turkey New Lira',
          'UAH' => 'Ukraine Hryvnia',
          'USD' => 'US Dollar',
          'XAF' => 'CFA Franc BEAC',
          'XOF' => 'CFA Franc BCEAO',
          'XPF' => 'CFP Franc',
          'ZAR' => 'South African Rand',
    );
}

?>
