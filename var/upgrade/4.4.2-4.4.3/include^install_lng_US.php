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
 * EN language library for the installation wizard
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: install_lng_US.php,v 1.85.2.5 2011/01/10 13:45:33 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

$install_lng_defs["US"] = array("name" => "English", "charset" => "iso-8859-1");

/**
 * Declaration of $install_languages for English language
 */
$install_languages["US"] = array (
    "status_on" => "On",
    "status_off" => "Off",
    "status_ok" => "OK",
    "status_failed" => "FAILED",
    "status_warning" => "WARNING",
    "status_skipped" => "SKIPPED",
    "fatal_error" => "Fatal error: {{error}}.<br />Please correct the error(s) before proceeding to the next step.",
    "warning" => "Warning: {{warning}}",
    "customer_area" => "Customer area",
    "admin_area" => "Administrator area",
    "provider_area" => "Product providers area",
    "partner_area" => "Partners area",
    "username" => "username",
    "password" => "password",
    "install_complete" => "Installation complete",
    "new_install" => "New installation",
    "reinstall_skins" => "Re-install skin files",
    "uninstall_module" => "Uninstall the module",
    "auth_code" => "Auth code",
    "auth_code_note" => "This is a protection from unauthorized use of the installation script.<br /><br />The Auth code is included into the email message that you received after the X-Cart installation. If you do not have this email message and do not know the Auth code, find the code in the \"Summary\" section of your store's Admin area.",
    "wrong_auth_code" => "Wrong auth code! You can not proceed.",
    "wrong_auth_code_title" => "Wrong auth code",
    "incompatible_version" => "The version of the add-on module being installed does not match the version of your X-Cart installation.",
    "i_accept_license" => "I accept the License Agreement",
    "thank_you" => "Thank you for choosing {{product}}, the powerful and reliable platform for your on-line business",
    "push_next_button" => "Push the \"Next\" button below to continue",
    "push_next_button_to_install" => "Push the \"Next\" button below to begin the installation",
    "button_back" => "&lt; Back",
    "button_next" => "Next &gt;",
    "lbl_yes" => "Yes",
    "lbl_no" => "No",
    "no_license_file" => "License agreement file is not found. Installation aborted",
    "select_language_prompt" => "Please select language for installation wizard",
    "installation" => "{{product}} Installation",
    "noscript_warning_message" => "The Installation script requires JavaScript, which is currently disabled in your web browser. Please enable JavaScript and continue the installation.",
    "nocookie_warning_message" => "The Installation script requires that your browser accept cookies, which is currently disabled in your web browser. Please enable cookies in your browser and continue the installation.",
    "copyright_text" => "&copy; 2001-2011 <a href=\"http://www.x-cart.com\">X-Cart.com</a><br />&copy; 2001-2011 Ruslan R. Fazlyev &lt;rrf@x-cart.com&gt;",
    "check_cfg_failed" => "(failed)",
    "check_cfg_passed" => "(passed)",
    // install.php modules
    "install_wiz" => "{{product}} Installation Wizard",
    "install_step" => "Step {{num}}: {{comment}}",
    "mod_language" => "Selecting installation language",
    "mod_license" => "License agreement",
    "mod_license_alert" => "It is necessary to agree to the terms of X-Cart License Agreement to be able to continue the installation. If you do not wish to be bound by this agreement, do not install the sofware.",
    "mod_check_cfg" => "Checking PHP configuration <span id=\"check_status\"></span>",
    "mod_cfg_install_db" => "Preparing to install X-Cart database",
    "mod_install_db" => "Installing X-Cart database",
    "mod_cfg_install_dirs" => "Layout settings",
    "mod_install_dirs" => "Setting Layout",
    "mod_cfg_enable_paypal" => "PayPal payment processing",
    "mod_enable_paypal" => "Enabling PayPal payment processing",
    "mod_install_done" => "Installation complete",
    "mod_generate_snapshot" => "Generating a system fingerprint",
    "mod_check_error" => "Check error message",
    "mod_send_problem_report" => "Technical problems report",
    // menu titles
    "title_language" => "Installation language",
    "title_license" => "License agreement",
    "title_check_cfg" => "PHP Configuration",
    "title_install_db" => "Installing X-Cart database",
    "title_install_dirs" => "Setting Layout",
    "title_enable_paypal" => "PayPal payment processing",
    "title_generate_snapshot" => "System fingerprint",
    "installation_steps" => "Installation steps",
    "title_install" => "Installation",
    "title_uninstall_done" => "Uninstall completed",

    // module_check_error
    "err_unknown_check_error" => "Unknown check error code",

    // module_send_problem_report
    "technical_problems_report" => "Technical problems report",
    "techrep_intro" => '<p>Our testing has identified some problems. Do you wish to send a report about your server configuration<br /> and test results so we can analyse it and fix the problems? Please fill in all the required fields below.</p><p>You can find more information about X-Cart software at <a href="http://help.qtmsoft.com/index.php?title=X-Cart:FAQs" target="_blank">X-Cart: Frequently asked questions</a> page.</p>',
    "techrep_your_email" => "Your contact e-mail",
    "techrep_user_note" => "Additional comments",
    "techrep_send_report" => "Send report",
    "techrep_send_note" => "<strong>NOTE: </strong>The report will be sent to our Support HelpDesk.<br /> A regular support ticket will be created on your behalf. Please log in to your HelpDesk account<br />to receive a solution for this problem. Note that it will reduce your support points balance.",
    "techrep_err_empty_email" => "Please fill in your contact email",
    "techrep_no_errors" => "Your server and environment meet X-Cart system requirements.",

    // module_check_cfg
    "cheÓking_results" => "Inspecting server configuration",
    "critical_dependencies" => "Checking critical dependencies",
    "non_critical_dependencies" => "Checking non-critical dependencies",
    "checking_file_permissions" => "Checking file permissions",
    "status" => "Status",
    "php_ver_min" => "PHP Version (min {{version}} required)",
    "php_safe_mode_is" => "PHP Safe mode is",
    "pcre_extension_is" => "PCRE extension is",
    "php_disabled_funcs" => "Disabled functions list",
    "php_disabled_funcs_none" => "none",
    "php_fileuploads_are" => "File uploads are",
    "php_mysql_support_is" => "MySQL database support is",
    "php_upload_maxsize_is" => "Maximum file size for upload is",
    "php_test_blowfish" => "Checking blowfish encryption mode",
    "php_gd" => "GD library 2.0",
    "php_ini_set_presence" => "ini_set PHP function is",
    "magic_quotes_sybase_is" => "magic_quotes_sybase PHP option is",
    "magic_quotes_sybase_title" => "PHP directive magic_quotes_sybase is enabled",
    "magic_quotes_sybase_descr" => "When enabled, magic_quotes_sybase may cause incorrect processing of input data. For example, every single-quote (') will be escaped with another single-quote instead of a backslash (\). And X-Cart does not work with quotes of this type.",
    "sql_safe_mode_is" => "sql.safe_mode PHP option is",
    "sql_safe_mode_title" => "PHP directive sql.safe_mode is enabled",
    "sql_safe_mode_descr" => "When sql.safe_mode is enabled, PHP attempts to connect to a database server using the default settings. This will cause that X-Cart will not be able to connect to the database using the settings that you specify during the installation.",
    "memory_limit_is" => "memory_limit PHP option is",
    "memory_limit_title" => "PHP directive memory_limit has the value of less than 32 MB",
    "memory_limit_descr" => "X-Cart needs that the maximum amount of memory is at least 32MB. Increase the value of memory_limit to 32MB or greater.",
    "memory_limit_set" => "Changing the value of memory_limit is",
    "memory_limit_set_title" => "Changing the value of the PHP directive memory_limit is not allowed.",
    "memory_limit_set_descr" => "For certain types of operations X-Cart requires more memory than 32MB. This implies that, if necessary, X-Cart should be able to increase the value of memory_limit. Since changing the value of memory_limit is not currently supported on your server, some X-Cart features (e.g., creating an HTML catalog) may be unavailable.",
    "memory_limit_none_title" => "Cannot check memory limitations",
    "memory_limit_none_descr" => "It is impossible to check and modify memory limitations on the server, using PHP.",
    "php_test_fopen" => "fopen() function can open URLs",
    "access_perm_note" => "Before the installation starts, please ensure that you have properly configured file access permissions (UNIX only):",
    "test_found_errors" => "Our testing has identified some problems. Do you wish to send a report about your server configuration and test results so we can analyse it and fix the problems?",
    "bool_off" => "Disabled",
    "bool_on" => "Enabled",
    "env_checking" => "Environment checking",
    "verification_steps" => "Verification steps",
    "int_check_files" => "Verifying the integrity of essential files",
    "env_checks_failed" => "Environment checking error",
    "critical_deps_failed" => "Critical dependencies failed",
    "non_critical_deps_failed" => "Non-critical dependencies failed",
    "click_to_open" => "Click to open",
    "click_to_close" => "Click to close",
    "err_show_details" => 'See details',
    "send_report" => "Send report",
    "int_check_files_title" => "<b>Some essential files may be damaged, modified or missing.</b> Make sure that all the files are correct.",
    "int_check_files_descr" => '<p>Here is a list of files that failed integrity checks:</p>{{value}}<br /><strong>Legend:</strong><br /><ul><li><strong>[CHECKSUM ERROR]</strong> - Make sure that the files of your X-Cart distribution were uploaded to the hosting server using FTP binary mode and that the archiver software that was used to decompress the archived files (winzip, winrar, etc) did not convert UNIX end of line characters in X-Cart files into end of line characters used on Windows or Mac.</li><li><strong>[NOT FOUND]</strong> - X-Cart was not able to find the file. Make sure that X-Cart distribution was uploaded to your computer or hosting server correctly.</li><li><strong>[NOT READABLE]</strong> - X-Cart was not able to read the file contents. Make sure the web server has permissions to read the file.</li></ul><p>You can find more information about X-Cart software at <a href="http://help.qtmsoft.com/index.php?title=X-Cart:FAQs" target="_blank">X-Cart: Frequently asked questions</a> page.</p>',
    "bytes" => "byte(s)",
    "int_check_ok" => "[OK]",
    "int_check_md5_nok" => "[CHECKSUM ERROR]",
    "int_check_not_readable" => "[NOT READABLE]",
    "int_check_file_not_found" => "[NOT FOUND]",
    "dep_php_ver_title" => "<b>Unsupported version of PHP - {{value}}.</b> Web server must support PHP 4.1.0 or better. PHP5 is also supported.",
    "dep_php_ver_descr" => '<p>This version of X-Cart will work on any OS where PHP/MySQL meets the minimum <a href="http://help.qtmsoft.com/index.php?title=X-Cart:Server_Requirements_(latest_X-Cart_version)" target="_blank">system requirements</a>.<br /><br />You can find more information about X-Cart software at <a href="http://help.qtmsoft.com/index.php?title=X-Cart:FAQs" target="_blank">X-Cart: Frequently asked questions</a> page.</p>',
    "dep_pcre_title" => "<b>PCRE extension is disabled.</b> PCRE PHP extension must be enabled for correct operation of X-Cart application.",
    "dep_pcre_descr" => '<p>To activate PCRE extension:</p><p><b>1. If you are using a hosting provider.</b><br />You need to contact hosting administrators and ask them to enable support for PCRE PHP extension in the PHP engine installed on your hosting server.</p><p><b>2. If you manage your own server.</b><br />You need to recompile PHP with support for PCRE (Perl Compatible Regular Expressions) library. You can find instructions on how to compile PHP for your operating system at <a href="http://www.php.net/manual/en/install.php" target="_blank">http://www.php.net/manual/en/install.php</a></p>',
    "dep_safe_mode_title" => "<b>Safe Mode is enabled.</b> Safe Mode must be turned off for correct operation of X-Cart application.",
    "dep_safe_mode_descr" => '<p>To disable Safe Mode:</p><p><b>1. If you have access to php.ini file</b><br /> Locate the <code>{{php_ini_path}}</code> file, find and edit the following line within the file: <br /><br /> <code>safe_mode = On</code> <br /><br /> change to: <br /><br /> <code>safe_mode = Off</code> <br /><br /> Save the file, then restart your web server application for the changes to take effect.</p><p><b>2. If you do not have access to php.ini file</b><br />Please contact your Hosting Support team to adjust this parameter.</p>',
    "dep_ini_set_title" => "<b>ini_set PHP function is disabled.</b> ini_set function must not be disabled for correct operation of X-Cart application.",
    "dep_ini_set_descr" => '<p>To activate ini_set function:</p><p><b>1. If you have access to php.ini file</b><br /> Locate the <code>{{php_ini_path}}</code> file, find the following line within the file: <br /><br /> <code>disable_functions = {{value}} </code> <br /><br /> and delete ini_set function from the list of disabled functions.<br /> Save the file, then restart your web server application for the changes to take effect.</p><p><b>2. If you do not have access to php.ini file</b><br />Please contact your Hosting Support team and ask them to remove ini_set PHP function from the list of disabled PHP functions.</p>',
    "dep_uploads_title" => "<b>File Uploads are disabled</b>. File Uploads must be enabled for correct operation of X-Cart application.",
    "dep_uploads_descr" => '<p>To enable file uploads:</p><p><b>1. If you have access to php.ini file</b><br /> Locate the <code>{{php_ini_path}}</code> file, find and edit the following line within the file: <br /><br /> <code>file_uploads = {{value}}</code> <br /><br /> change to: <br /><br /> <code>file_uploads = On</code> <br /><br /> Save the file, then restart your web server application for the changes to take effect.</p><p><b>2. If you do not have access to php.ini file</b><br />Please contact your Hosting Support team to adjust this parameter.</p>',
    "dep_mysql_title" => "<b>MySQL database support is disabled.</b> MySQL PHP extension must be enabled for correct operation of X-Cart application.",
    "dep_mysql_descr" => '<p>To activate MySQL extension:</p><p><b>1. If you are using a hosting provider.</b><br />You need to contact hosting administrators and ask them to enable support for MySQL in the PHP engine installed on your hosting server.</p><p><b>2. If you manage your own server.</b><br />You need to recompile PHP with MySQL support or activate MySQL extension in <code>{{php_ini_path}}</code> if it was compiled as dynamically loadable extension, but was not activated. You can find instructions on how to compile PHP for your operating system at <a href="http://www.php.net/manual/en/install.php" target="_blank">http://www.php.net/manual/en/install.php</a>, <a href="http://www.php.net/mysql" target="_blank">http://www.php.net/mysql</a>. More information about MySQL database can be found at <a href="http://www.mysql.com/" target="_blank">http://www.mysql.com/</a>.</p>',
    "dep_disable_funcs_title" => "<b>Some PHP functions ({{value}}) are disabled for execution.</b> Certain parts of X-Cart's functionality rely on the availability of these functions  and might be unavailable.",
    "dep_disable_funcs_descr" => '<br /><ul><li>PHP function "exec()" must be allowed for the correct functioning of most of the CC payment processing modules used with X-Cart (CyberCash, CyberPac (LaCaixa), PayFlow Pro, PayBox, CyberSource, PaySystems Client, VaultX), HTTPS modules (Net::SSLeay, CURL, OpenSSL, https_cli), GnuPG/PGP.</li><li>PHP functions "popen()" &amp; "pclose()" must be allowed for the correct functioning of some HTTPS modules (CURL, Net::SSLeay), payment modules(Saferpay, CyberSource, iDeb), shipping modules (DHL/Airborne, UPS Online Tools, Intershipper, AntiFraud module).</li><li>PHP function "ini_set()" enhances security and is used for the session mechanism and templates processing.</li><li>PHP function "fsockopen()" is used for HTML catalog generation and realtime shipping rates gathering (USPS, Australia Post, Canada Post).</li></ul><p>To re-adjust the list of disabled functions:</p><p><b>1. If you have access to php.ini file</b><br /> Locate the <code>{{php_ini_path}}</code> file, find the following line within the file: <br /><br /> <code>disable_functions = &lt;here goes the list of disabled functions&gt; </code> <br /><br /> and delete <code>{{value}}</code> from the list.<br /> Save the file, then restart your web server application for the changes to take effect.</p><p><b>2. If you do not have access to php.ini file</b><br />Please contact your Hosting Support team and ask them to re-adjust the list of disabled functions for you.</p>',
    "dep_upl_max_title" => "<b>Maximum upload file size limit is too low.</b> Please adjust the PHP configuration variable upload_max_filesize.",
    "dep_upl_max_descr" => '<p>To increase the maximum uploaded file size limit:</p><p><b>1. If you have access to php.ini file</b><br /> Locate the <code>{{php_ini_path}}</code> file, find and edit the following line within the file: <br /><br /> <code>upload_max_filesize = {{value}}</code> <br /><br /> change to, for example: <br /><br /> <code>upload_max_filesize = 8M</code> <br /><br /> Save the file, then restart your web server application for the changes to take effect.</p><p><b>2. If you do not have access to php.ini file</b><br />Please contact your Hosting Support team to adjust this parameter.</p>',
    "dep_fopen_title" => "<b>fopen PHP function is not allowed to open URLs.</b> Please allow opening of remote files using fopen function calls by setting the PHP configuration variable <code>allow_url_fopen</code> to 'On'.",
    "dep_fopen_descr" => '<p>To allow opening of remote files using the fopen function:</p><p><b>1. If you have access to php.ini file</b><br /> Locate the <code>{{php_ini_path}}</code> file, find and edit the following line within the file: <br /><br /> <code>allow_url_fopen = {{value}}</code> <br /><br /> change to, for example: <br /><br /> <code>allow_url_fopen = On</code> <br /><br /> Save the file, then restart your web server application for the changes to take effect.</p><p><b>2. If you do not have access to php.ini file</b><br />Please contact your Hosting Support team to adjust this parameter.</p>',
    "dep_blowfish_title" => "<b>The PHP engine installed on your server has known issues with bitwise operations.</b> Please install mcrypt PHP extension or upgrade PHP to the latest stable version.",
    "dep_blowfish_descr" => '<p>Certain PHP versions have known defects in processing of bitwise operations that are used during generation of encrypted data using Blowfish encryption method. X-Cart utilizes bitwise operators emulation on these PHP versions, but this leads to slow generation of encrypted data and can have negative impact on X-Cart performance.<br />We strongly recommend you upgrade PHP to the latest stable version or install mcrypt PHP extension to your hosting server.<br />1. If you are using a hosting provider, please contact hosting support team regarding this matter.<br />2. In other case, please refer to PHP documentation on how to recompile PHP  <a href="http://www.php.net/manual/en/install.php" target="_blank">http://www.php.net/manual/en/install.php</a>, or install the mcrypt PHP extension <a href="http://www.php.net/mcrypt" target="_blank">http://www.php.net/mcrypt</a>.</p>',
    "dep_gd_title" => "<b>GD library 2.0 is not installed.</b>",
    "dep_gd_descr" => '<p>GD library 2.0 or better is required for the following X-Cart features:</p><ul><li>Automatic product thumbnail generation.</li><li>Antibot \'Image Verification\' module.</li><li>Cache generation for \'Detailed Product Images\' module.</li></ul>',
    "perm_check_entity" => 'Checking whether {{entity_type}} "{{entity}}" is {{entity_mode}}',
    "permissions_title" => "Installation cannot be continued, because some vital files and directories have wrong permissions..",
    "non_critical_permissions_title" => "<b>Vital files and directories do not have correct file permissions.</b>",
    "permissions_descr" => '<p>Here is a list of files/directories that failed permissions check:</p>{{value}}<p>You can find more information about X-Cart software at <a href="http://help.qtmsoft.com/index.php?title=X-Cart:FAQs" target="_blank">X-Cart: Frequently asked questions</a> page.</p>',
    "non_critical_permissions_descr" => '<p>Here is a list of files/directories that failed permissions check:</p>{{value}}<p>You can find more information about X-Cart software at <a href="http://help.qtmsoft.com/index.php?title=X-Cart:FAQs" target="_blank">X-Cart: Frequently asked questions</a> page.</p>',
    "permission_directory_writable" => "Please grant writable permissions on the \"{{entity}}\" directory.<br/>\nOn a UNIX operating system this can be done with the help of the following command:<br />\n<code>chmod {{permissions}} {{entity_full_path}}</code>",
    "permission_file_writable" => "Please grant writable permissions on the \"{{entity}}\" file.<br/>\nOn a UNIX operating system this can be done with the help of the following command:<br />\n<code>chmod {{permissions}} {{entity_full_path}}</code>",
    "permission_file_executable" => "Please grant executable permissions on the \"{{entity}}\" file.<br/>\nOn a UNIX operating system this can be done with the help of the following command:<br />\n<code>chmod {{permissions}} {{entity_full_path}}</code>",
    "check_env_srv_settings_js" => '<input type="button" class="check-again" onclick="javascript: this.form.current.value={{current}}; this.form.submit();" value="Check once again" />',

    // cfg_install_db
    "install_web_mysql" => "Please enter your web server details and MySQL database information",
    "install_http_name" => "<b>Server host name</b><br />Host name of your server (e.g. www.example.com)",
    "install_https_name" => "<b>Secure server host name</b><br />Host name of your secure (HTTPS) server (e.g. secure.example.com)",
    "install_webdir" => "<b>X-Cart web directory</b><br />Web directory where X-Cart files are located. Leave empty for webroot. (e.g. /xcart)",
    "install_mysqlhost" => "<b>MySQL host name</b><br />Host name of MySQL server. It can be host name or IP address",
    "install_mysqlhost_alert" => "You must enter MySQL host name",

    "install_mysqluser" => "<b>MySQL user name</b><br />The name of the MySQL user",
    "install_mysqluser_alert" => "You must enter MySQL user name",

    "install_mysqldb" => "<b>MySQL database name</b><br />The name of the database you connect to",
    "install_mysqldb_alert" => "You must enter MySQL database name",
    "install_mysql_version_alert" => "The version of MySQL ({{version}}) which is currently used contains known bugs, that is why X-Cart may operate incorrectly. We recommend to update MySQL to a more stable version.",
    "install_mysql_min_version" => "MySQL v3.23 or later is required for X-Cart to work properly (versions
    5.0.50 and 5.0.51 are not recommended due to known bugs).",
    "check_crypted_data" => "Checking encrypted data in the current database",
    "check_crypted_data_failed" => "The database contains data that cannot be decrypted using the current key! ",
    "check_w_oldkey_crypted_data_failed" => "The database contains data that cannot be decrypted using the specified blowfish key! ",

    "install_mysqlpass" => "<b>MySQL password</b><br />Which password to use for MySQL",
    "install_email" => "<b>Your e-mail address</b><br />This address will be used as default for company options",
    "install_languages" => "<b>Languages</b><br />Languages you want to install (use Ctrl key to select multiple options)",
    "install_states" => "<b>States table</b><br />States of the country where the shop is located (use Ctrl key to select multiple options)",
    "install_demodata" => "<b>Sample categories/products</b><br />Would you like to setup sample categories and products?",
    "install_configuration" => "<b>Configuration settings</b><br />Apply pre-configured settings to selected country",
    "install_update_config" => "<b>Update config.php only</b><br />Tick this if you want to skip database setup (no data will be installed in the database)",
    "install_email_as_login" => "<b>Email as login authorization mode</b><br />Tick this if you want users to log in using email and not to prompt them complete the user name (login) field during registration.",
    "install_blowfish_key" => "<b>Blowfish key</b><br />If you want to use the database from another X-Cart installation (trial, etc.), enter the blowfish key which was used to encrypt the data in your database.",
    "install_store_images_in" => "<b>Store images in</b><br />Select where you want to store your images.<br />Recommended value is 'File system'",
    "install_store_images_db" => "Database",
    "install_store_images_fs" => "File system",
    "moving_images_to_fs" => "Moving images to the file system",

    "error_connect" => "Can't connect to the MySQL server. Press 'BACK' button and check database info again",
    "error_select_db" => "Installer couldn't find database \"{{db}}\". You should ask your system administrator to create one or choose another name",
    "error_check_write_config" => "Cannot open file \"config.php\" for writing. You should grant writable permissions for the \"config.php\".",
    "error_check_email" => "You have specified an incorrect e-mail address",
    "warning_db_tables_exists" => "Installation Wizard has found existing X-Cart tables in your database. If you continue, they will be deleted.",
    "warning_addon_exists" => "The add-on module is already installed. If you proceed with the installation, all the database tables and template files used by the currently installed copy of the add-on module will be overwritten (existing data will be lost).",

    "updating_config_file" => "Updating config.php file... ",
    "error_cannot_open_config" => "Can't open file \"config.php\" for reading/writing<br />Please, check permissions and restart installation.",
    "upload_cannot_open" => "Uploading file '{{file}} : {{status}} Cannot open file<br />'",

    "fatal_error_install_db" => "A fatal error occurred during the installation of the database.<br />Make sure all the conditions required for the installation of the database are met and try again.",

    "error_unexp_connect" => "Cannot connect to MySQL server. This is unexpected error, so please start installation again.",

    "error_unexp_select_db" => "Couldn't find database \"{{db}}\". This is unexpected error, so please start installation again.",

    "creating_tables" => "Creating tables...",
    "creating_table" => "Creating table: [{{table}}] ... ",

    "importing_data" => "Importing data...",
    "importing_languages" => "Importing languages...",
    "importing_states" => "Importing states...",

    "importing_demodata" => "Setting up sample categories and products...",

    "please_wait" => "Please wait ...",

    // cfg_install_dirs
    "select_color_n_layout" => "Layout settings",
    "select_layout" => "<b>Select a skin</b>",

    // install_dirs
    "creating_directories" => "Creating directories...",
    "creating_directory" => "Creating directory: [{{dir}}] ... ",
    "dir_already_exists" => "Already exists",
    "warn_file_create_failed" => "Creating of file '{{file}}' is failed",
    "copying_file_from_to" => "Copying {{src}} to {{dst}}",
    "copying_to_file" => "Copying to file {{dst}}",
    "copying_directory" => "Copying directory: {{dir}} - {{status}}",
    "copying_templates" => "Copying templates...",
    "removing_directory" => "Removing directory: {{dir}}",
    "removing_file" => "Removing file {{file}}",
    "err_wrong_permissions" => "Please make sure the account, under which the web server runs has write permissions for the directory <strong>{{dir}}</strong>.",
    "err_wrong_permissions_files" => "Please make sure the account, under which the web server runs has write permissions for the directory <strong>{{dir}}</strong> and the <strong>{{src}}</strong> file is readable by your web server.",
    "err_wrong_permissions_dirs" => "Please make sure the account, under which the web server runs has write permissions for the directory <strong>{{dir}}</strong> and the <strong>{{src}}</strong> directory is readable by your web server.",

    "creating_layout" => "Creating layout...",
    "error_creating_directories" => "Fatal error occured while creating directories. Please check permissions and try again",

    "color_layout_preview" => "Layout preview",
    "click_to_refresh" => "click to refresh",
    "err_file_dir_not_exist" => "Please make sure the <strong>{{file}}</strong> file/directory is readable by your web server.",

    "default" => "default",
    "checking_existing_files" => "Checking existing files",
    "err_existing_files_found" => "One or more files in the X-Cart skin directory skin1/ will be overwritten during the installation",
    "txt_existing_files_found" => "The installation script has detected that one or more files in the X-Cart skin directory skin1/ will be overwritten during the installation. If you didn't change any of the listed files, and your store doesn't use a customized skin, simply ignore the current message and continue the installation.<br /><br />However, if the original skin file has been modified, or the store uses a custom design, or you simply don't know, we recommend you backup the listed files before you proceed.",
    "click_to_see_files_list" => "Click here to see the file(s)",
    "cancel_install" => "Cancel the installation",
    "installation_canceled" => "The installation was canceled. To resume the installation, run the script install.php again.<br /><br /><b><a href=\"install.php\">Run install.php</a></b>",
    "mod_install_cancel" => "Installation Canceled",

    // module_generate_snapshot
    "txt_begin_generating_snapshot" => "Generating the system fingerprint.<br />This may take several minutes, please be patient...<br />",
    "msg_snapshot_generated" => "System fingerprint is successfully generated",
    "txt_N_unprocessed_files_in_snapshot" => "<br /><font color=\"red\">Warning! {{unproc}} files out of {{total}} are ignored (cannot be read)</font><br />",
    "installation_snapshot" => "Installation system fingerprint",
    "err_snpst_write_file" => "System fingerprint file cannot be created: permission is denied",
    "err_snpst_no_files" => "System fingerprint file cannot be created: no files found",

    // mod_cfg_enable_paypal
    "paypal_question" => "Do you wish to enable PayPal payment processing now?",

    // mod_enable_paypal
    "install_web_paypal" => "The Installation Wizard needs to know the email address you wish to use for PayPal registration or the email address your current PayPal account is registered for",
    "install_paypal_account" => "<b>E-Mail address for PayPal</b><br />leave blank, if you do not wish to configure PayPal at this time",
    "install_web_paypal_comment" => "<p>After you click on 'Next' a verification message will be sent to the email address specified here. Please follow the web link in this message to enable PayPal payments in your store.</p><p><b>Notes:</b><ol><li>Please make sure the spam filter on this mail box is configured to accept notifications from PayPal &amp; X-Cart.</li><li>PayPal and other payment gateways can be configured on the \"Payment methods\" page in X-Cart administrator area at any time later.</li></p>",

    // final page
    "change_auth_code" => "<b>We strongly recommend you to change 'Auth code' in the config.php file. (that is the \$installation_auth_code variable).</b>",
    "evaluation_notice" => 'Your X-Cart Gold installation is licensed for evaluation purposes only. For details refer to <a href="http://www.x-cart.com/xcart_license_agreement.html" target="_blank" >X-Cart license agreement.</a><br /><br />To upgrade your license please purchase <a href="http://www.x-cart.com/buy_gold.html" target="_blank" >X-Cart Gold paid license</a> and register your copy as follows:<br /><ol><li>Make sure that no HTTP-authorization and firewall restrictions are set on incoming HTTP-connections to your X-Cart installation.</li><li>Login to your <a href="https://secure.qtmsoft.com/" target="_blank">private members area</a> with your username and password.</li><li>Go to <a href="https://secure.qtmsoft.com/customer.php?area=customer_licenses&amp;target=customer_licenses" target="_blank" >"My licenses" section.</a></li><li>Check whether the license URL is <b>{{http_location}}</b>. If it is not, follow the "Change URL" link to the right from the license URL and replace the license URL with <b>{{http_location}}</b>.</li><li>Follow "Register" link to the right from the license URL.</li><li><a href="{{http_location}}/admin/general.php" target="_blank">Open</a> in order to ensure that registration was successful.</li><li>In case of any problems with purchase or registration process please <a href="http://www.x-cart.com/contact_us.html?reason=registration_problems">contact us.</a></li></ol> <br />',
    "install_paypal_mail_note" => "A verification message with the necessary instructions was sent to the email address you specified for PayPal account.",
    "blowfish_key" => "Blowfish key: <b>{{key}}</b>",
    "distribution_warning" => "To prevent unauthorized access to the {{product}} source code, remove the archive with the {{product}} distribution package from web-accessible directories on your server.",
    "post_install_permissions_notice_intro" => "Before you start using {{product}}, please restore secure permissions as advised below:",
    "post_install_permissions_notice_intro_windows" => "To ensure the security of your server, it is recommended that you disable write permission for the user {{user}} to all directories and files (recursively) excluding var, skin1, catalog, files.<br /><br />Setup the necessary file permissions using a suitable facility supplied by your hosting provider. For instructions on how to use a particular facility, consult the documentation for the facility or contact your hosting team.<br /><br /> An overview of file permissions in Windows is available at:<br /><ul><li><a href='http://support.microsoft.com/kb/131780'>http://support.microsoft.com/kb/131780</a></li><li><a href='http://support.microsoft.com/kb/308419'>http://support.microsoft.com/kb/308419</a></li></ul><br />If you are not sure you can setup file permissions correctly, contact our Support team using your personal HelpDesk at <a href='https://secure.qtmsoft.com'>https://secure.qtmsoft.com</a>",
    "install_rename_success" => "Installation script install.php renamed to <b>{{install_name}}</b>.<br /><br />The file has been renamed to prevent unauthorized access to the {{product}} installation script install.php. In the future, if you wish to reinstall {{product}} or change a skin, rename the file back to install.php first.",
    "install_rename_failed" => "The <b>install.php</b> script could not be renamed. To ensure the security of your {{product}} installation and prevent the unallowed use of this script, you should manually rename or delete it.",
    "keys_information" => '
<br />
In the process of installation the following security keys have been generated:<br /><br />
<ul>
<li>Auth code: <b>{{installation_auth_code}}{{change_auth_code}}</b><br /></li>
<li>Blowfish key: <b>{{blowfish_key}}</b><br /></li>
</ul>
<br />
<b>Note:</b><br />
Auth code will be used to prevent unauthorized access to {{product}} installation script install.php. If, in the future, you decide to completely re-install {{product}}, change your store\'s skin set or install some {{product}} add-ons, you will be required to enter this code at the time of installation. Please be aware that this code is stored for you in include/install.php.<br />
<br />
Blowfish key will be used to encrypt all sensitive data in your store including user passwords, order details, etc. This code is supposed to be stored permanently in config.php (the variable $blowfish_key). Please DO NOT change this key manually.<br />
',
    "xcart_final_note" => "{{product}} installed successfully. To access your store, use the links below:<br />{{interfaces}}<br />
Auth code is <strong>{{code}}</strong>.{{change_auth_code}}<br /><br />
Auth code is used to prevent unauthorized access to the {{product}} installation scripts. If you decide to reinstall {{product}}, change a skin or install an add-on module, you will be asked to enter this code first. It is not necessary to remember this code: when needed, you can find it in the Summary section of the {{product}} Admin area. Besides, the auth code together with other info is sent to your email address <strong>{{email}}</strong><br /><br />
<br />{{install_rename}}
<br /><br />{{post_install_permissions_notice}}
<br /><br />
<center><span class=\"thank-you-message\">Thank you for choosing {{product}}, the powerful and reliable platform for your online business.</span></center>
",
    "final_email_message" => '
<h1>Installation complete</h1>
<br />
Congratulations! {{product}} e-commerce software has been successfully installed.<br />
<br />
You can access your {{product}}-based store at the following URLs:<br />
{{interfaces}}
<br />
{{post_install_permissions_notice}}
  <div id="dialog-message">
    <div class="box message-w">
        We strongly recommend you remove {{product}} distribution package archive from your web directory to prevent unauthorized access to {{product}} source code.
    </div>
  </div>
<br />
{{install_rename}}
<br />
{{keys_information}}
',
    // some modules messages
    "removing_skin_files" => "Removing skin files ...",
    "deactivating_module" => "Deactivating the module ...",
    "copying_skin_files" => "Copying skin files ...",
    "activating_module" => "Activating the module ...",
    "module_installed" => "{{name}} installed successfully. To start using the module:<br />",
    "module_install_rename_success" => "Installation script {{script_name}} renamed to <b>{{install_name}}</b>.<br /><br />The file has been renamed to prevent unauthorized access to the {{product}} installation script. In the future, if you wish to reinstall/uninstall {{product}}, rename the file back to {{script_name}} first.",
    "module_install_rename_failed" => "The <strong>{{script_name}}</strong> script could not be renamed. To ensure the security of your {{product}} installation and prevent the unallowed use of this script, you should manually rename or delete it.",
    "module_uninstalled" => "{{name}} module has been successfully uninstalled",
    "mod_modinstall" => "Installing and configuring the module",
    "mod_moduninstall" => "Uninstalling the module",
    "mod_moduninstall_done" => "Uninstallation complete",
    // install-x* labels
    "xaff_admin_note" => "A new menu item, 'Affiliates', will appear in your admin interface. Use it to set up your commission plans, to manage your affiliates, etc.",
    "xaff_partner_note" => "Using this URL your partners can register, upload new banners, view stats etc.",
    "xaom_admin_note" => "( \"Orders\" :: \"Order details\" =&gt; \"Modify\" )",
    "xfancycat_admin_note" => "( \"Administration\" :: \"Modules\" )<br />( \"Administration\" :: \"General settings\" )",
    "xgiftreg_customer_note" => "( \"Gift Registry\" menu)",
    "xpconf_provider_note" => "( \"Products\" :: \"Product Configurator\" )",
    "modules_admin_note" => "( \"Administration\" :: \"Modules\" )",
    "modules_x_admin_note" => "( \"Administration\" :: \"Modules\" :: \"{{module}}\")",
);

?>
