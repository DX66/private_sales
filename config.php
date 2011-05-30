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
 * Configuration settings
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Customer interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: config.php,v 1.448.2.9 2011/04/25 10:28:08 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: index.php"); die("Access denied"); }

/**
 * SQL database details
 *
 * This section sets up a connection between X-Cart shopping cart software
 * and your MySQL database. If X-Cart is installed using Web installation, the
 * variables of this section are configured via the Installation Wizard. If you
 * install X-Cart manually, or if, after X-Cart has been installed, your MySQL
 * server information changes, use this section to provide database access
 * information manually.
 *
 * $sql_host - DNS name or IP of your MySQL server;
 * $sql_db - MySQL database name;
 * $sql_user - MySQL user name;
 * $sql_password - MySQL password.
 *
 */
$sql_host = 'db365980883.db.1and1.com';
$sql_db = 'db365980883';
$sql_user = 'dbo365980883';
$sql_password = 'privateshop';

/**
 * X-Cart HTTP & HTTPS host and web directory
 *
 * This section defines the location of your X-Cart installation. If X-Cart is
 * installed using Web installation, the variables of this section are
 * configured via the Installation Wizard. If you install X-Cart manually, use
 * this section to provide your web server details manually.
 *
 * $xcart_http_host - Host name of the server on which your X-Cart software is
 * to be installed;
 * $xcart_https_host - Host name of the secure server that will provide access
 * to your X-Cart-based store via the HTTPS protocol;
 * $xcart_web_dir - X-Cart web directory.
 *
 * NOTE:
 * The variables $xcart_http_host and $xcart_https_host must contain hostnames
 * ONLY (no http:// or https:// prefixes, no trailing slashes).
 *
 * Web dir is the directory where your X-Cart is installed as seen from the Web,
 * not the file system.
 *
 * Web dir must start with a slash and have no slash at the end. An exception to
 * this rule is when you install X-Cart in the site root, in which case you need
 * to leave the variable empty.
 *
 * EXAMPLE 1:
 * $xcart_http_host ="www.yourhost.com";
 * $xcart_https_host ="www.securedirectories.com/yourhost.com";
 * $xcart_web_dir ="/xcart";
 * will result in the following URLs:
 * http://www.yourhost.com/xcart
 * https://www.securedirectories.com/yourhost.com/xcart
 *
 * EXAMPLE 2:
 * $xcart_http_host ="www.yourhost.com";
 * $xcart_https_host ="www.yourhost.com";
 * $xcart_web_dir ="";
 * will result in the following URLs:
 * http://www.yourhost.com/
 * https://www.yourhost.com/
 */
$xcart_http_host = 'www.dx66.de';
$xcart_https_host = 'www.dx66.de';
$xcart_web_dir = '/private_shop';

/**
 * Storing Customers' Credit Card Info
 *
 * The variable $store_cc defines whether you want the credit card info provided
 * by your customers at checkout to be stored in the database or not.
 * The credit card info that can be stored includes:
 * - Cardholder's name;
 * - Card type;
 * - Card number;
 * - Valid from (for certain card types);
 * - Exp. date;
 * - Issue No (for certain card types).
 *
 * Admissible values for $store_cc are 'true' and 'false':
 * 'true' - X-Cart will store your customers' credit card info in the order
 * details and user profiles;
 * 'false' - X-Cart will not store your customers' credit card info anywhere.
 *
 * NOTE:
 * If you are going to use 'Subscription' module or 'Credit Card' off-line payment method (manual credit card processing), set $store_cc to 'true'.
 *
 *
 * WARNING!
 * These parameters may affect your PCI compliance.
 * Before changing please read http://help.qtmsoft.com/index.php?title=X-Cart:PCI-DSS_implementation_guide
 */
$store_cc = false;

/**
 * Storing CVV2 codes
 *
 * The variable $store_cvv2 defines whether you want the CVV2 codes of your
 * customers' credit cards to be stored in the database or not.
 *
 * Admissible values for $store_cvv2 are 'true' and 'false':
 * 'true' - X-Cart will store the CVV2 codes of your customers' credit cards
 * in the order details and user profiles;
 * 'false' - X-Cart will not store the CVV2 codes of your customers' credit
 * cards anywhere.
 *
 * NOTE:
 * VISA International does not recommend storing CVV2 codes along with credit
 * card numbers.
 * If you are going to use 'Subscription' module, set $store_cvv2 to 'true'.
 *
 *
 * WARNING!
 * These parameters may affect your PCI compliance.
 * Before changing please read http://help.qtmsoft.com/index.php?title=X-Cart:PCI-DSS_implementation_guide
 */
$store_cvv2 = false;

/**
 * Storing Customers' Checking Account Details
 *
 * The variable $store_ch defines whether you want your customers checking
 * account details to be stored in the database or not.
 * The checking account details that can be stored include:
 * - Bank account number;
 * - Bank routing number;
 * - Fraction number.
 *
 * If Direct Debit is used then Account owner name is stored instead of Fraction number.
 *
 * Admissible values for $store_ch are 'true' and 'false':
 * 'true' - X-Cart will store your customers' checking account details in the
 * order details;
 * 'false' - X-Cart will not store your customers' checking account details
 * anywhere.
 *
 * WARNING!
 * These parameters may affect your PCI compliance.
 * Before changing please read http://help.qtmsoft.com/index.php?title=X-Cart:PCI-DSS_implementation_guide
 */
$store_ch = false;

/**
 * Default images
 *
 * The variable $default_image defines which image file should be used as the
 * default "No image available" picture (a picture that will appear in the
 * place of any missing image in your X-Cart-based store if no other "No image
 * available"-type picture is defined for that case).
 */
$default_image = 'default_image.gif';

/**
 * The variable $shop_closed_file defines which HTML page should be displayed
 * to anyone trying to access the Customer zone of your store when the store is
 * closed for maintenance.
 */
$shop_closed_file = 'shop_closed.html';

/**
 * Single Store mode (X-Cart PRO only)
 *
 * The variable $single_mode allows you to enable/disable Single Store mode if
 * your store is based on X-Cart PRO. Single Store mode is an operation mode in
 * which your store represents a unified environment shared by multiple
 * providers in such a way that any provider can edit the products of the other
 * providers, and shipping rates, discounts, taxes, discount coupons, etc are
 * the same for all the providers.
 *
 * Admissible values for $single_mode are 'true' and 'false':
 * 'true' - enables Single Store mode;
 * 'false' - puts your store into normal mode where each of your providers can
 * control his own products only and can have shipping rates, discounts, taxes,
 * etc different from those of the other providers.
 *
 * NOTE:
 * If your store is based on X-Cart GOLD, $single_mode must be set to 'true' at
 * all times.
 */
$single_mode = true;

/**
 * Temporary directories
 */
$var_dirs = array (
    'tmp'             => $xcart_dir . '/var/tmp',
    'templates_c'     => $xcart_dir . '/var/templates_c',
    'upgrade'         => $xcart_dir . '/var/upgrade',
);

$var_dirs_web = array (
);

/**
 * Log directory
 *
 * The variable $var_dirs['log'] defines the location of the directory where X-Cart log
 * files are stored.
 */
$var_dirs['log'] = $xcart_dir . '/var/log';

/**
 * Cache directory
 *
 * The variable $var_dirs['cache'] defines the location of the directory where
 * X-Cart cache files are stored.
 */
$var_dirs['cache'] = $xcart_dir.'/var/cache';
$var_dirs_web['cache'] = '/var/cache';

/**
 * Export directory
 *
 * The variable $export_dir defines the location of X-Cart export directory
 * (a directory on X-Cart server to which the CSV files of export packs are
 * stored).
 */
$export_dir = $var_dirs['tmp'];

/**
 *
 * DO NOT CHANGE ANYTHING BELOW THIS LINE UNLESS
 * YOU REALLY KNOW WHAT YOU ARE DOING
 *
 *
 *
 *
 * Thresholds for time (in seconds) and memory (in bytes) limits
 * Initial values:
 * $x_time_threshold = 4 seconds
 * $x_mem_threshold = 4 * 1024 * 1024 = 4194304 byte
 */
$x_time_threshold = 4;
$x_mem_threshold = 4194304;

/**
 * Comma separated list of IP for access to admin area
 * Leave empty for unrestricted access.
 * E.g.:
 *   1) access is unrestricted:
 *      $admin_allowed_ip = '';
 *   2) access allowed only from IP 192.168.0.1 and 127.0.0.1:
 *      $admin_allowed_ip = "192.168.0.1, 127.0.0.1";
 */
$admin_allowed_ip = '';

/**
 * Automatic repair of the broken indexes in mySQL tables
 */
$mysql_autorepair = true;

/**
 * Caching
 *
 * The constant USE_DATA_CACHE defines whether you want to use data caching in
 * your store.
 * Admissible values for USE_DATA_CACHE are 'true' and 'false'.
 * By default, the value of this constant is set to 'true'. You can set it to
 * 'false' if you experience problems using the store with caching enabled
 * (for example, if you get some kind of error regarding a file in the /var/cache
 * directory of your X-Cart installation).
 */
define('USE_DATA_CACHE', true);

define('USE_SQL_DATA_CACHE', false);

define('SQL_CACHE_TTL', 3600);

/**
 * Memcache routine
 * Defines whether you want to use memcache for data caching 
 */
define('USE_MEMCACHE_DATA_CACHE', false);
define('MEMCACHE_SERVER_ADDRESS', 'localhost');
define('MEMCACHE_SERVER_PORT', 11211);

/**
 * The constant SECURITY_BLOCK_UNKNOWN_ADMIN_IP allows you to enable a
 * functionality that will prevent usage of your store's back-end from IP
 * addresses unknown to the system.
 */
define('SECURITY_BLOCK_UNKNOWN_ADMIN_IP', false);

/**
 * The constant USE_SESSION_HISTORY allows you to enable synchronization of
 * user sessions on the main website of your store and on domain aliases.
 */
define('USE_SESSION_HISTORY', true);

/**
 * The constant FORM_ID_ORDER_LENGTH sets the length for the list of unique
 * form identifiers. A unique form identifier ensures that a form is valid
 * and serves as a protection from CSRF attacks. If FORM_ID_ORDER_LENGTH is
 * not declared or is set to a non-numeric value or a value less than 1,
 * it's value will be set to 100.
 */
define('FORM_ID_ORDER_LENGTH', 100);

/**
 * The constant FRAME_NOT_ALLOWED forbids calling X-Cart in IFRAME / FRAME tags.
 * If you do not use X-Cart in any pages where X-Cart is displayed through a
 * frame, this option can be enabled to enhance security. This option prevents
 * attacks in which the attacker displays X-Cart through a frame and, using web
 * browser vulnerabilities, intercepts the information being entered in it.
 */
define('FRAME_NOT_ALLOWED', false);

/**
 * The variable sets a limit for the number of redirects from HTTP to HTTPS.
 * When this limit is reached, X-Cart supposes that the HTTPS part of the store
 * does not work and stops trying to redirect to the HTTPS part.
 * If the value of the variable is not a number or less than zero,
 * redirection will always happen.
 */
$https_redirect_limit = 20;

/**
 * Error tracking code
 *
 * Turning on/off the debug mode
 * 0 - no debug info;
 * 1 - display error (and exit script - for SQL errors);
 * 2 - write errors to the log files (var/log/x-errors_*.php)
 * 3 - display error and write it to the log files.
 */
$debug_mode = 2;

/*
 * Enable this directive if you are a developer changing X-Cart source
code.
 * This directive enables function assertion http://php.net/assert
 * This directive enables all php warnings/notices
 * This directive should be disabled in production.
*/
define('DEVELOPMENT_MODE', false);

/**
 * Error reporting level:
 */
if ($debug_mode) {
    $x_error_reporting = E_ALL ^ E_NOTICE;
} else {
    $x_error_reporting = 0;
}

if (
    defined('DEVELOPMENT_MODE')
    && constant('DEVELOPMENT_MODE')
) {
    $x_error_reporting = -1;
}

/**
 * Demo mode - protects the pages essential for the functioning of X-Cart
 * from potentially harmful modifications
 */
$admin_safe_mode = false;

/**
 * Files directory
 */
$files_dir    = DIRECTORY_SEPARATOR . 'files';
$files_webdir = '/files';

/**
 * Prefix for admin/provider file directories
 * Directories will be named as follows:
 * $files_dir/{prefix}{userid}
 */
$files_dir_prefix = 'userfiles_';

/**
 * Templates repository
 * where original templates are located for 'restore' facility
 */
$templates_repository_dir = '/skin_backup';

/**
 * Templates repository root dir
 * where all Smarty templates are located
 */
$smarty_skin_root_dir = '/skin';

/**
 * Core templates repository
 * where common Smarty templates are located
 */
$smarty_skin_dir = '/skin/common_files';

/**
 * Set the session name here
 */
$XCART_SESSION_NAME = 'xid_3da07';

/**
 * Session duration (in seconds)
 *
 * Setting a very small value for this variable can cause malfunctioning
 * of some lengthy store procedures.
 * For example, HTML catalog generation and data import/export.
 * Recommended value is not less than 3600.
 */
$use_session_length = 3600;

/**
 * Search by separate words
 *
 * Maximum number of words that can be searched for when search by separate
 * words is enabled
 * (Expressions enclosed in double-quote marks are treated as single words)
 */
$search_word_limit = 10;

/**
 * Minimum word length (minimum number of significant characters a word must
 * have to be considered a word) when search by separate words is enabled
 */
$search_word_length_limit = 2;

/**
 * Skin configuration file
 */
$skin_config_file = 'skin1.conf';

/**
 * Put installation access code here
 * A person who does not know the auth code can not access the install.php installations script
 */
$installation_auth_code = '35B388A7';

/**
 * !!!NEVER CHANGE THE SETTINGS BELOW THIS LINE MANUALLY!!!
 *
 * The variable $blowfish_key contains your Blowfish encryption key automatically
 * generated by X-Cart during installation. This key is used to encrypt all the
 * sensitive data in your store including user passwords, credit card data, etc.
 *
 * NEVER try to change your Blowfish encryption key by editing the value  of the
 * $blowfish_key variable in this file: your data is already encrypted with this
 * key and X-Cart needs exactly the same key to be able to decrypt it. Changing
 * $blowfish_key manually will corrupt all the user passwords (including the
 * administrator's password), so you will not be able to use the store.
 *
 * Please be aware that a lost Blowfish key cannot be restored, so X-Cart team
 * will not be able to help you regain access to your store if you remove or
 * change the value of $blowfish_key.
 *
 * It is quite safe to use X-Cart with the Blowfish key generated during
 * installation; however, if you still want to change it, please refer to
 * X-Cart Reference Manual or contact X-Cart Tech Support for details.
 */
$blowfish_key = '251a0fca033009fd425be16e540c893e';

/**
 * WARNING :
 * Please ensure that you have no whitespaces or empty lines below this message.
 * Adding a whitespace or an empty line below this line will cause a PHP error.
 */
?>
