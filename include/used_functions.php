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
 * Used functions definition
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: used_functions.php,v 1.17.2.1 2011/01/10 13:11:51 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: index.php"); die("Access denied"); }

$used_functions = array(
    'stripslashes','define','defined','header','split','sprintf','count','strstr','serialize','trim',
    'is_array','intval','explode','strlen','array_merge','array_unique','array_keys','preg_match','unlink','in_array',
    'urlencode','rand','addslashes','unserialize','is_null','floor','array_search','implode','file_exists','parse_url',
    'opendir','readdir','is_dir','closedir','time','str_replace','array_values','strpos','rtrim','constant',
    'preg_quote','join','array_filter','key','func_set_time_limit','fwrite','mysql_num_fields','mysql_num_rows','mysql_field_name','mysql_field_type',
    'mysql_fetch_row','flush','fflush','strtoupper','fclose','feof','fgets','chop','preg_replace','mysql_error',
    'ini_get','is_readable','substr','phpinfo','php_uname','sort','is_writable','extension_loaded','function_exists','gd_info',
    'phpversion','mysql_get_server_info','mysql_get_client_info','mktime','date','uniqid','each','array_sum','fopen','filetype',
    'md5','preg_match_all','max','array_pop','ceil','round','filemtime','is_file','fgetcsv',
    'sizeof','ord','chr','array_unshift','is_numeric','filesize','substr_count','strcmp','usort','basename',
    'array_shift','htmlspecialchars','fread','move_uploaded_file','fputs','file','is_link','strftime','str_repeat',
    'strtr','rename','copy','ltrim','min','end','sleep','abs','doubleval',
    'strtolower','is_uploaded_file','dirname','ob_start','ob_get_contents','ob_end_clean','array_reverse','is_string','mt_srand',
    'ftruncate','rewind','array_flip','func_get_args','reset','arsort','extract','strip_tags','is_resource',
    'is_int','umask','chmod','ini_set','strcasecmp','getenv','base64_encode','ob_flush',
    'ob_get_length','ob_end_flush','func_num_args','strrpos','is_object','is_bool','strncmp','is_double','number_format',
    'call_user_func_array','is_float','register_shutdown_function','preg_grep','setcookie','array_slice','strncasecmp','is_scalar',
    'ucfirst','iconv','crc32','dechex','mysql_connect','mysql_select_db','mysql_query','error_log','mysql_errno','mysql_result',
    'mysql_fetch_array','mysql_fetch_field','mysql_free_result','mysql_insert_id','mysql_affected_rows','preg_split','current','trigger_error','array_diff',
    'debug_backtrace','rmdir','pathinfo','readlink','is_executable','tempnam','realpath','readfile','array_splice','clearstatcache',
    'fsockopen','mkdir','md5_file','preg_replace_callback','array_push','is_integer','microtime','putenv',
    'chunk_split','mail','pow','ip2long','gmmktime','parse_str','urldecode','get_resource_type','base64_decode','addcslashes','range',
    'stristr','rsort','htmlentities','rawurlencode','strtotime','class_exists','unpack','fseek','pack','srand','bin2hex','checkdate',
    'getdate','uasort','array_map','ftell','ksort','krsort','fpassthru','error_reporting','get_magic_quotes_gpc',
    'get_html_translation_table','ini_get_all','asort','mysql_close','set_error_handler','strrchr','stat','octdec',
    'is_writeable','touch','gmdate','ucwords','str_pad','compact','chdir','dir','headers_sent','sqrt',
    'get_defined_vars','call_user_func','parse_ini_file','ftp_connect','ftp_login','ftp_pasv','ftp_fput','ftp_quit','utf8_encode',
    'mt_rand','sin','getimagesize','uksort','shuffle','html_entity_decode','hexdec','strrev','array_rand','get_defined_functions','fileperms','php_sapi_name',
    'connection_status','mysql_fetch_assoc','mysql_list_tables','printf','is_callable','array_key_exists','eval'
);
