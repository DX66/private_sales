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
 * X-Cart installation wizard base code
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: install.php,v 1.162.2.11 2011/04/25 11:49:13 aim Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

$installation_auth_code = func_get_auth_code(); //$installation_auth_code is moved to config.php

/**
 * Changing some configuration parameters
 */
if (defined('AREA_TYPE')) return; // for internal use

if (function_exists('set_magic_quotes_runtime'))
    @set_magic_quotes_runtime(0);

error_reporting (E_ALL ^ E_NOTICE);
@set_time_limit(300);
umask(0);
/**
 * While executing sql files re-establish connection with mysql server before every Nth sql command
 */
$sql_reconnect_count = 100;

$__quotes_qpc = function_exists('get_magic_quotes_gpc') ? get_magic_quotes_gpc() : false;

if (function_exists('date_default_timezone_get') && function_exists('date_default_timezone_set'))
    @date_default_timezone_set(@date_default_timezone_get());

if (func_is_default_auth_code($installation_auth_code)) {
    $_new_auth_code = func_generate_auth_code();

    if (func_write_auth_code_to_file($_new_auth_code)) {
        $installation_auth_code = $_new_auth_code;
        if (isset($_POST['params']['auth_code'])) {
            $_POST['params']['auth_code'] = func_crypt_auth_code($_new_auth_code);
        }            
    }

    $_new_auth_code = null;
}    

function mystripslashes($var)
{
    if (!is_array($var)) return stripslashes($var);

    foreach($var as $k=>$v) {
        if (is_array($v)) $var[$k] = mystripslashes($v);
        else $var[$k] = stripslashes($v);
    }
    return $var;
}

if ($__quotes_qpc || (!$__quotes_qpc && function_exists('func_addslashes'))) {
    foreach(array('_GET', '_POST', '_COOKIE', '_SERVER') as $_k=>$__avar)
        foreach ($GLOBALS[$__avar] as $__var => $__res) $GLOBALS[$__avar][$__var] = mystripslashes($__res);
}

/**
 * Predefined common variables
 */

$templates_repository = 'skin' . XC_DS . 'common_files';
//$schemes_repository = 'schemes';
$templates_directory = 'skin' . XC_DS . 'common_files';

/**
 * Check if the unique installation key is generated.
 * If not found, then to generate it
 */
if (!func_secure_key_exists()) {
    $init_secure_key = func_generate_secure_key();
}

/**
 * start: Modules manager
 */

$error = false;

// get working parameters

$current = (int)$_POST['current'];
$params = $_POST['params'];

if (isset($_POST['auth_code']) && !empty($_POST['auth_code'])) {
    $params['auth_code'] = func_crypt_auth_code($_POST['auth_code']);
}

$orig_params = $params;

$is_cancel = ($params['flags']['ex_files_action'] == 'C');

require_once $xcart_dir.'/include/func/func.files.php';
require $xcart_dir.'/include/install_lng.php';

require_once $xcart_dir.'/include/func/func.crypt.php';
require_once $xcart_dir.'/include/blowfish.php';
$blowfish = new ctBlowfish();

if (isset($params['lngcode']) && is_array($install_languages[$params['lngcode']]))
    $install_language_code = $params['lngcode'];
else
    $install_language_code = 'US';

$install_language_charset = $install_lng_defs[$install_language_code]['charset'];

if (empty($params['flags']) || !is_array($params)) $params['flags'] = array();

if (isset($params['force_current'])) {
    $_tmp=explode(',',$params["force_current"]);
    unset($params['force_current']);
    $current = array_shift($_tmp);
    if (!empty($_tmp)) {
        $params['flags'] = array();
        foreach ($_tmp as $k=>$v) {
            $params['flags'][$v] = true;
        }
    }
}

// Existing files check: adjust step number if the user selected certain option
if ($current == 7 && !empty($params['flags']['ex_files_action']) && in_array($params['flags']['ex_files_action'], array("O", "K"))) {
    $current = 6;
}

if ($is_cancel) {
    $modules[10] = array(
        'name' => 'install_cancel',
        'comment' => 'mod_install_cancel'
    );
    $current = 10;
}

// Skip skins selection when only one skin is available
if ($current == 5 && function_exists('func_get_schemes') && count(func_get_schemes()) <= 1)
    $current = 6;

// Disable PayPal when re-installing skins
if (($current == 7 || $current == 8) && !empty($params['flags']['nopaypal']))
    $current = 9;

// Auth code incorrect
$auth_code_error = ($current > 1 && is_array($orig_params) && (empty($orig_params['auth_code']) || $orig_params['auth_code'] != func_crypt_auth_code($installation_auth_code)));

if (function_exists('func_query')) {

    // Addon installation

    $installation_product = $module_definition['name'];
    if (!$auth_code_error) {
        $params['mysqlhost'] = $sql_host;
        $params['mysqluser'] = $sql_user;
        $params['mysqlpass'] = $sql_password;
        $params['mysqlbase'] = $sql_db;

        $module_definition['sql_files'] = array(
            'sql/'.$module_definition['prefix'].'_remove.sql',
            'sql/'.$module_definition['prefix'].'.sql'
        );

        $codes = func_query_column("SELECT DISTINCT(code) FROM $sql_tbl[languages]");
        $lng_codes = array(
            'en' => 'US',
            'fr' => 'FR',
            'de' => 'DE',
            'sv' => 'SE'
        );

        foreach ($codes as $_code) {
            $_file = 'sql/'.$module_definition['prefix'].'_lng_'.$lng_codes[$_code].'.sql';
            if (file_exists($xcart_dir.'/'.$_file))
                $module_definition['sql_files'][] = $_file;
        }

    }

    // Define the installation steps

    $modules = array (
        0 => array(
            'name' => 'language',
            'sb_title' => 'title_language',
            'comment' => 'mod_language'
        ),
        1 => array(
            'name' => 'moddefault',
            'sb_title' => 'title_license',
            'comment' => 'mod_license',
            'js_next' => true
        ),
        2 => array(
            'name' => 'modinstall',
            'comment' => 'mod_modinstall',
            'sb_title' => 'title_install'
        ),
        3 => array(
            'name' => 'generate_snapshot',
            'comment' => 'mod_generate_snapshot',
            'sb_title' => 'title_generate_snapshot',
            'param' => ' ('.$module_definition['name'].' install)'
        ),
        4 => array(
            'name' => 'install_done',
            'comment' => 'install_complete',
            'param' => @$module_definition['successmessage']
        )
    );

    if ($params['install_type'] == 3) {
        $modules[2]['comment'] = 'mod_moduninstall';
        $modules[3]['param'] = str_replace('install', 'uninstall', $modules[3]['param']);
        $modules[4] = array(
            'name' => 'uninstall_done',
            'comment' => 'mod_moduninstall_done',
        );
    }

    if ($is_cancel) {
        $modules[4] = array(
            'name' => 'install_cancel',
            'comment' => 'mod_install_cancel'
        );
        $current = 4;
    }

    $sb_excludes = array(2,3);

    // Existing files check: adjust step number if the user selected certain option
    if ($current == 3 && !empty($params['flags']['ex_files_action']) && in_array($params['flags']['ex_files_action'], array("O", "K"))) {
        $current = 2;
    }

    $func_is_installed = @$module_definition['is_installed'];
    if (!empty($func_is_installed) && function_exists($func_is_installed) && $func_is_installed())
        define('ADDON_IS_INSTALLED',  true);
}

// Skip language selecting step for only one language
if ($current == 0 && count($available_install_languages) == 1) {
    list($install_language_code) = $available_install_languages;
    $params['lngcode'] = $install_language_code;
    $current++;
}

if ($current < 0 || $current >= count($modules))
    die("invalid current");

// Do not display status bar on some steps
$skip_status_bar = in_array($current, $sb_excludes);

function inst_html_entity_decode($string)
{
    static $trans_tbl = false;

    if ($trans_tbl === false) {
        $trans_tbl = get_html_translation_table(HTML_ENTITIES);
        $trans_tbl = array_flip($trans_tbl);
    }
    return strtr($string, $trans_tbl);
}

function bool_get($param)
{
    static $settings = false;

    if (!is_array($settings) && function_exists('ini_get_all')) { // For PHP >= 4.2.0
        $a = ini_get_all();
        foreach ($a as $k=>$v) {
            $value = $v['local_value'];
            $value = inst_html_entity_decode($value);
            $value = preg_replace('!Off!Si',false,$value);
            $value = preg_replace('!On!Si',true,$value);
            $value = str_replace("\x00",'',$value);
            $settings[$k] = $value;
        }
    }

    if (!is_array($settings)) {
        ob_start();
        phpinfo(INFO_CONFIGURATION);
        $lines = explode("\n",ob_get_contents());
        ob_end_clean();
        foreach ($lines as $_k=>$line) {
            if (preg_match('!<tr><td class="e">([^<]+)</td><td class="v">([^<]*)</td><td class="v">([^<]*)</td></tr>!Si', $line, $m)) {
                $m[2]=inst_html_entity_decode($m[2]);
                $m[2]=preg_replace('!Off!Si',false,@$m[2]);
                $m[2]=preg_replace('!On!Si',true,@$m[2]);
                $m[2]=str_replace("\x00",'',$m[2]);
                $settings[$m[1]] = $m[2];
            }
            else if (preg_match('!<td bgcolor="#ccccff"><b>([^<]+)</b><br[^>]*></td><td align="center">([^<]*)</td><td align="center">([^<]*)</td>!Si', $line, $m)) {
                $m[2]=inst_html_entity_decode($m[2]);
                $m[2]=preg_replace('!Off!Si',false,@$m[2]);
                $m[2]=preg_replace('!On!Si',true,@$m[2]);
                $m[2]=str_replace("\x00",'',$m[2]);
                $settings[$m[1]] = $m[2];
            }
            else if (preg_match('!(.+) => ([^ =]*) => (.*)$!S', $line, $m)) {
                $m[2]=preg_replace('!Off!Si',false,@$m[2]);
                $m[2]=preg_replace('!On!Si',true,@$m[2]);
                $m[2]=str_replace("\x00",'',$m[2]);
                $settings[$m[1]] = $m[2];
            }
        }
    }

    return isset($settings[$param]) ? $settings[$param] : false;
}

/**
 * Get directory entries matched regexp (portable glob())
 */
function get_dirents_mask($dir, $re)
{
    $rval = array();

    $dp = opendir($dir);
    if ($dp !== false) {
        while (($dirent = readdir($dp)) !== false) {
            if (preg_match($re, $dirent, $matches))
                $rval[$dirent] = $matches;
        }

        closedir($dp);
    }

    return $rval;
}

/**
 * Extract data from file matched regexp
 */
function get_file_contents_re($file, $re)
{
        ob_start();
        readfile($file);
        $contents = ob_get_contents();
        ob_end_clean();

    $rval = array();
    if (preg_match_all($re, $contents, $rval)) {
        return $rval;
    }

    return false;
}

/**
 * Make list of countries or languages based on list of files using regexp
 */
function get_lang_names_re($dir, $files_re, $current_lng_code, $mode)
{
    static $modes = array (
        'country' => '!country_(%s)\',\'(.*)\',\'Countries\'\);!',
        'language' => '!language_(%s)\',\'(.*)\',\'Languages\'\);!'
    );
    static $code_aliases = array (
        'UK' => 'GB'
    );

    global $xcart_dir;

    $files = get_dirents_mask($dir, $files_re);

    $rval = array();
    foreach ($files as $_file=>$matches) {
        // if language is not known, use code instead
        $code = $matches[1];
        if (!empty($code_aliases[$code])) $code = $code_aliases[$code];
        $rval[$matches[1]] = $code;
    }

    $re = sprintf($modes[$mode], implode($rval,'|'));

    $matches = get_file_contents_re($xcart_dir.'/sql/xcart_language_'.$current_lng_code.'.sql', $re);
    if (!empty($matches[1])) {
        // replace language codes with names
        foreach ($matches[1] as $_coden => $_code) {
            if (in_array($_code, $code_aliases)) {
                foreach ($code_aliases as $alias_key=>$alias) {
                    if ($alias == $_code) {
                        $_code = $alias_key;
                        break;
                    }
                }
            }
            $rval[$_code] = $matches[2][$_coden];
        }
    }

    asort($rval);

    return $rval;
}

// start html output

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $install_language_charset; ?>" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<title><?php echo_lng('install_wiz', 'product', $installation_product); ?></title>
<link rel="stylesheet" type="text/css" href="skin/common_files/css/install.css" />

<style type="text/css">
<!--
<?php
if ($current == 0) {
?>
.background {
  background-color: #ffffff;
  background-image: url('http://www.x-cart.com/img/logo.gif');
}
<?php
} elseif ($skip_status_bar) {
?>
#center-main {
  margin: 0px 20px 0px 20px;
}
<?php
}
?>
-->
</style>

<!--[if lt IE 7]>
<style type="text/css">
#page-container {
  height: 100%;
}
#page-container2 {
  float: left;
  height: 620px; /* bottom-expand */
}
</style>
<![endif]-->
<style type="text/css">
.cfg-error-details ul{
  word-break: break-all;
}
</style>

<script type="text/javascript" language="javascript">
//<![CDATA[
    var steps_back = 1;

<?php

// show module's according scripts

// 'back' button's script
if (@$modules[$current]['js_back']) {
    $func = 'module_'.$modules[$current]['name'].'_js_back';
    $func();
}
else
    default_js_back();

// 'next' button's script
if (@$modules[$current]['js_next']) {
    $func = 'module_'.$modules[$current]['name'].'_js_next';
    $func();
}
else
    default_js_next();

?>

//
// Opener/Closer HTML block
//
function visibleBox(id, skipOpenClose)
{
    elm1 = document.getElementById('open'+id);
    elm2 = document.getElementById('close'+id);
    elm3 = document.getElementById('box'+id);

    if(!elm3)
        return false;

    if (skipOpenClose) {
        elm3.style.display = (elm3.style.display == '')?'none':'';
    } else if(elm1) {
        if (elm1.style.display == '') {
            elm1.style.display = 'none';
            if(elm2)
                elm2.style.display = '';
            elm3.style.display = 'none';
        } else {
            elm1.style.display = '';
            if(elm2)
                elm2.style.display = 'none';
            elm3.style.display = '';
        }
    }
}

loaded = false;

function refresh()
{
    window.scroll(0, 100000);

    if (loaded == false)
        setTimeout('refresh()', 1000);
}

function scrollDown()
{
    setTimeout('refresh()', 1000);
}
//]]>
</script>
</head>
<body>
<div id="page-container">
    <div id="page-container2">

<div id="header">
    <div class="line1">
        <div class="install-header">
            <?php echo_lng('installation','product', ($installation_product == "X-Cart") ? '' : $installation_product); ?>
        </div>
        <div class="install-version">
            <?php @readfile('VERSION'); ?>
        </div>
    </div>
    <div class="line2"></div>

</div>

<div class="clearing"></div>

<div id="content-container">
    <div id="content-container2">
        <div id="center">
            <div id="center-main">

<?php
// Skip standard form if an installer module does not require it.
if (!defined('XCART_SKIP_INSTALLER_FORM')) {
?>
<?php /* common form */ ?>

<?php
/**
 * Cookie should be enabled to run the installation script
 */
if ($current == 1 && defined('ADDON_IS_INSTALLED') && constant('ADDON_IS_INSTALLED')) {
    warning_error(lng_get('warning_addon_exists'));
}
?>

<form method="post" name="ifrm" action="<?php echo $_SERVER['PHP_SELF'] ?>" onsubmit="javascript: return step_next();">

<table width="100%" cellspacing="0" cellpadding="0" align="center">

<tr>
    <td>
<?php
// auth_code must present always to prevent non-authorized reinstallation
$_tmp = $orig_params;
if (isset($_tmp['lngcode'])) unset($_tmp['lngcode']);

/**
 * Check for critical errors
 */
$critical_error = false;

// check javascript availability
?>
<noscript>
<?php critical_error('noscript_warning_message'); ?>
</noscript>
<?php

if(empty($_COOKIE)) {
    // check if the cookies are enabled
    if ($current <= 1) {
?>
<script type="text/javascript">
//<![CDATA[
if (!navigator.cookieEnabled)
    document.write('<?php critical_error("nocookie_warning_message", false); ?>');
//]]>
</script>
<?php
    } else {
        $critical_error = 'nocookie_warning_message';
    }

} elseif ((!defined('COMPATIBLE_VERSION') || constant('COMPATIBLE_VERSION') != '4.4.3') && defined('XCART_INSTALL')) {
    // incompatible version
    $critical_error = 'incompatible_version';

} elseif ($auth_code_error) {
    // auth code incorrect
    $critical_error = 'wrong_auth_code';

} elseif (!empty($_tmp) && empty($params['agree']) && $params['install_type'] != 3) {
    // user did not agree with licence
    $critical_error = 'mod_license_alert';
}

if ($critical_error) {
    // show warning and stop installation
    $lngcode = $params['lngcode'];
    $params = array('lngcode' => $params['lngcode']);

    critical_error($critical_error);
} else {
    echo '<h1>' . lng_get($modules[$current]["comment"]) . '</h1>';

    // run handler of current step
    $func = 'module_'.$modules[$current]['name'];
    $res = $func($params,@$modules[$current]['param']);
}
?>
    </td>
</tr>
<?php

// show navigation buttons

$prev = $current;

if (!$res)
    $current += 1;

if (!empty($params['flags']['ex_files_action'])) {
    unset($params['flags']['ex_files_action']);
?>
<script type="text/javascript" language="javascript">
//<![CDATA[
    steps_back = 2;
//]]>
</script>
<?php
}

if ($current < count($modules)) {
?>

<tr>
    <td align="center">
    <br />
<?php
if (!empty($params)) {
    foreach ($params as $key => $val) {
        if(is_array($val)) {
            foreach($val as $key2 => $val2) {
?><input type="hidden" name="params[<?php echo $key ?>][<?php echo $key2 ?>]" value="<?php echo $val2 ?>" />
<?php
            }
        } elseif(@in_array($key, $sql_conf_trusted_vars)) {
?><input type="hidden" name="params[<?php echo $key ?>]" value="<?php echo htmlspecialchars($val, ENT_QUOTES) ?>" />
<?php
        } else {
?><input type="hidden" name="params[<?php echo $key ?>]" value="<?php echo $val ?>" />
<?php
        }
    }
}
?>

    <input type="hidden" name="current" value="<?php echo $current ?>" />
<?php
$skip_back_button = (
    ($prev == 1 && count($available_install_languages) == 1) ||
    ($prev <= 0 && count($available_install_languages) > 1)
);

if(!$skip_back_button) {
?>
    <input type="button" value="<?php echo_lng('button_back'); ?>" onclick="javascript: return step_back();" />&nbsp;&nbsp;&nbsp;
<?php
}
if (!$critical_error)    {
?>
    <input type="submit"<?php if ($error) {?> style="color: #848683"<?php } ?> value="<?php echo_lng('button_next'); ?>"<?php if ($error) {?> disabled="disabled"<?php } ?> />
<?php
}
?>
    </td>
</tr>
<?php
}

?>

<?php /* common bottom */ ?>

</table>
</form>
<?php
} else {
    $_tmp = $orig_params;
    if (!empty($_tmp) && $_tmp['auth_code'] != func_crypt_auth_code($installation_auth_code)) {
        message(lng_get('wrong_auth_code'));
    } else {
        // Just invoke the installer module if it does not require standard installer form.
        $func = 'module_'.$modules[$current]['name'];
        $res = $func($params, @$modules[$current]['param']);
    }
} // if(!defined('XCART_SKIP_INSTALLER_FORM'))
?>
            </div><!-- center -->
        </div><!-- center-main -->

<?php /* status bar */
if (!$skip_status_bar) {
    $_current = $prev;
    if ($auth_code_error) {
        $_current = 1;
        $modules[$current]['comment'] = 'wrong_auth_code_title';
    }
?>

<div id="left-bar">
    <div class="status-box">
<ul>
    <li class="menu-heading"><?php echo_lng('installation_steps'); ?></li>
<?php
    $_current = (string)$_current;
    foreach($modules as $_mkey => $_v) {
        if ($_mkey == 0 && count($available_install_languages) == 1)
            continue;
        if (!empty($_v['sb_title'])) {
            if ($is_cancel) {
                $li_class = '';
            } else {
                $li_class = $_mkey == $_current || @$_v['is_complete'] == $_current ? 'current' : (($_mkey < $_current) ? 'passed' : '');
            }
?>
    <li<?php if ($li_class!="") echo " class=\"".$li_class."\""; ?>><?php echo_lng($_v['sb_title']); ?></li>
<?php
        }
    }
?>
</ul>
    </div>

    <div class="box-container"></div>

</div>

<?php /* end status column */
}
?>
    </div>
</div>

<div class="clearing"> </div>

<div id="footer">
    <div class="copyright"><?php echo_lng('copyright_text'); ?></div>
</div>

    </div>
</div>

</body>
</html>
<?php

/**
 * end: Modules manager
 */

/**
 * start: default navigation buttons handlers
 */

function default_js_back()
{
?>
    function step_back() {
        if (!steps_back || steps_back <= 1) {
            history.back();
        } else {
            history.go(-steps_back);
            steps_to_back = 1;
        }
        return true;
    }
<?php
}

function default_js_next()
{
?>
    function step_next() {
        return true;
    }
<?php
}

/**
 * end: default navigation buttons handlers
 */

#############################################################
/**
 * Common functions goes here
 */
#############################################################

function critical_error($txt)
{
    echo '<div id="dialog-message"><div class="box message-e" title="Error">' . lng_get($txt) . '</div></div>';
    return false;
}

function fatal_error($txt)
{
?>
<div id="dialog-message">
    <div class="box message-e" title="Error">
<a href="#" class="close-link" onclick="javascript: document.getElementById('dialog-message').style.display = 'none'; return false;"><img src="skin/common_files/images/spacer.gif" alt="Close" class="close-img" /></a>
    <?php echo_lng('fatal_error', 'error', $txt); ?>
    </div>
</div>
<?php
    return false;
}

function warning_error($txt)
{
?>
<div id="dialog-message">
    <div class="box message-w" title="Warning">
<a href="#" class="close-link" onclick="javascript: document.getElementById('dialog-message').style.display = 'none'; return false;"><img src="skin/common_files/images/spacer.gif" alt="Close" class="close-img" /></a>
    <?php echo_lng('warning', 'warning', $txt); ?>
    </div>
</div>
<?php
    return false;
}

function message($txt)
{
?>
<span class="message"><?php echo $txt ?></span>
<?php
}

function message_error($txt)
{
?>
<div id="dialog-message">
    <div class="box message-i" title="Information">
<a href="#" class="close-link" onclick="javascript: document.getElementById('dialog-message').style.display = 'none'; return false;"><img src="skin/common_files/images/spacer.gif" alt="Close" class="close-img" /></a>
    <?php echo $txt ?>
    </div>
</div>
<?php
}

function status($var)
{
    if ($var === 'warning')
        return "<font color=\"blue\">[".lng_get('status_warning')."]</font>";

    if ($var === 'skipped')
        return "<font color=\"green\">[".lng_get('status_skipped')."]</font>";

    return $var ? "<font color=\"green\">[".lng_get('status_ok')."]</font>" : "<font color=\"red\">[".lng_get('status_failed')."]</font>";
}

function on_off($var)
{
    return lng_get($var ? 'status_on' : 'status_off');
}

function myquery($command)
{
    global $params, $sql_reconnect_count;
    static $requests_count = 0;
    static $db_link;

    if (!isset($db_link) || !is_resource($db_link)) {
        if( !$db_link = @mysql_connect($params['mysqlhost'], $params['mysqluser'], $params['mysqlpass']) ) return false;

        if (strpos(strtolower($command),'database') === false)
            @mysql_select_db($params['mysqlbase'], $db_link);
    }    

    if( $sql_reconnect_count > 0 && $requests_count > $sql_reconnect_count ) {
        if (isset($db_link) && is_resource($db_link))
            @mysql_close($db_link);

        if( !$db_link = @mysql_connect($params['mysqlhost'], $params['mysqluser'], $params['mysqlpass']) ) return false;

        if( !@mysql_select_db($params['mysqlbase'], $db_link) ) return false;

        $requests_count = 0;
    }
    $requests_count++;
    return mysql_query($command, $db_link);
}

function runquery($command)
{
    myquery($command);
    $myerr = mysql_error();
    if (!empty($myerr))
        echo status(false)." ".$myerr."<br />\n";
    return empty($myerr);
}

function query_upload($filename)
{
    global $xcart_dir;

    $fp = @fopen($xcart_dir.XC_DS.$filename, 'rb');
    if ($fp === false) {
        echo_lng('upload_cannot_open', 'file', $filename, 'status', status(false));
        return 0;
    }

    $command = '';
    $counter = 0;

    echo "<br />".lng_get('please_wait')."<br />\n" . basename($xcart_dir.XC_DS.$filename) . " ";

    while (!feof($fp)) {
        $c = chop(fgets($fp, 100000));
        $c = preg_replace("/^[ \t]*(#|-- |---*).*/Ss", '', $c);

        $command .= $c;

        if (preg_match("/;$/Ss", $command)) {
            $command = preg_replace("/;$/Ss", '', $command);

            if (preg_match("/CREATE TABLE\s+([\w\d_]+)\s/S", $command, $match)) {
                $table_name = $match[1];
                echo_lng('creating_table', 'table', $table_name); func_flush();

                myquery($command);

                $myerr = mysql_error();
                if (!empty($myerr))
                    break;
                else
                    echo status(true)."<br />\n";
            } else {
                myquery($command);

                $myerr = mysql_error();
                if (!empty($myerr))
                    break;
                else {
                    $counter++;

                    if (!($counter % 20)) {
                        echo '.'; func_flush();
                    }
                }
            }

            $command = '';
            func_flush();
        }
    }

    fclose($fp);

    if (!empty($myerr))
        echo status(false)." ".$myerr."<br />\n";
    else {
        if ($counter > 19) echo "<br />\n";
        echo status(empty($myerr))."<br />\n";
    }

    return empty($myerr);
}

/**
 * Function to check for existing modified files in the skin folder
 */
function check_existing_files($templates_repository, $parent_dir="")
{
    global $templates_directory;

    return check_existing_files_sub($templates_repository.$parent_dir, $templates_directory.$parent_dir);
}

function check_existing_files_sub($srcdir, $dstdir)
{
    global $xcart_dir;

    $ex_files = array();

    if (!$handle = opendir($xcart_dir.XC_DS.$srcdir)) {
        return false;
    }

    while (($file = readdir($handle)) !== false) {

        if ($file == '.' || $file == '..' || !strcasecmp($file,'_private') || !strncasecmp($file, '_vti', 4)) continue;

        if (!strcasecmp($file, 'thumbs.db')) continue;

        if (!file_exists($dstdir)) continue;

        if (is_file($srcdir.XC_DS.$file)) {
            if (file_exists($dstdir.XC_DS.$file) && func_md5_file($srcdir.XC_DS.$file) != func_md5_file($dstdir.XC_DS.$file)) {
                $ex_files[] = $dstdir.XC_DS.$file;
            }
        } else if (is_dir($srcdir.XC_DS.$file) && $file != '.' && $file != '..') {
            if (!file_exists($dstdir.XC_DS.$file))
                continue;

            echo '.'; func_flush();
            $ex_files = array_merge($ex_files, check_existing_files_sub($srcdir.XC_DS.$file, $dstdir.XC_DS.$file));
        }
    }

    closedir($handle);

    return $ex_files;
}


/**
 * Function to copy directory tree from skin1_original to skin1
 */

function copy_files($templates_repository, $parent_dir="")
{
    global $templates_directory;

    return copy_files_sub($templates_repository.$parent_dir, $templates_directory.$parent_dir);
}

function copy_files_sub($srcdir, $dstdir)
{
    global $xcart_dir, $params;

    $status = true;

    if (!$handle = @opendir($xcart_dir.XC_DS.$srcdir)) {
        echo status(false);
        echo " - " . lng_get('err_file_dir_not_exist', 'file', $xcart_dir.XC_DS.$srcdir);
        echo "<br />\n";
        return false;
    }

    while ($status && ($file = readdir($handle)) !== false) {
        if ($file == '.' || $file == '..' || !strcasecmp($file,'_private') || !strncasecmp($file, '_vti', 4)) continue;

        if (!strcasecmp($file, 'thumbs.db')) continue;

        if (!file_exists($dstdir))
            $status = $status && create_dirs(array($dstdir));

        if (!$status) break;

        if (@is_file($srcdir.XC_DS.$file)) {
            if (file_exists($dstdir.XC_DS.$file) && (func_md5_file($srcdir.XC_DS.$file) == func_md5_file($dstdir.XC_DS.$file) || $params['flags']['ex_files_action'] == "K")) {
                $status = true;
            } elseif (!@copy($srcdir.XC_DS.$file, $dstdir.XC_DS.$file)) {
                echo lng_get('copying_file_from_to', 'src',$srcdir.XC_DS.$file,'dst',$dstdir.XC_DS.$file)." ... ".status(false);
                echo " - " . lng_get('err_wrong_permissions_files', 'dir', dirname($dstdir.XC_DS.$file), 'src', $srcdir.XC_DS.$file);
                echo "<br />\n";
                $status = false;
            }

            if ($status) {
                func_chmod_file($dstdir.XC_DS.$file);
            }

            func_flush();

        } else if (@is_dir($srcdir.XC_DS.$file) && $file != '.' && $file != '..') {

            if (!file_exists($dstdir.XC_DS.$file)) {
                if (!file_exists($dstdir))
                    $status = $status && create_dirs(array($dstdir));

                $status = $status && create_dirs(array($dstdir.XC_DS.$file));
            }

            $status = $status && copy_files_sub($srcdir.XC_DS.$file, $dstdir.XC_DS.$file);
        }
    }

    closedir($handle);

    return $status;
}

function check_dir($dir)
{
    global $xcart_dir;

    if ($dir == '') return true;

    if (file_exists($dir)) return true;

    if (!check_dir(dirname($dir))) return false;

    echo_lng('creating_directory', 'dir', $dir);
    $status = func_mkdir($xcart_dir.XC_DS.$dir);

    echo status($status);
    if (!$status) {
        echo " - " . lng_get('err_wrong_permissions', 'dir', dirname($xcart_dir.XC_DS.$dir));
    }
    echo "<br />";

    return $status;
}

function check_existing_files_plain($files)
{
    global $templates_directory;
    global $templates_repository;
    global $xcart_dir;

    $ex_files = array();
    foreach($files as $_k=>$file) {
        if (is_dir($templates_repository.XC_DS.$file)) {
            if (!$handle = opendir($xcart_dir.XC_DS.$templates_repository.XC_DS.$file)) {
                return array();
            }

            while (($item = readdir($handle)) !== false) {
                if ($item == '.' || $item == '..' || !strcasecmp($item,'_private') || !strncasecmp($item, '_vti', 4)) continue;

                if (!strcasecmp($item, 'thumbs.db')) continue;

                $ex_files = array_merge($ex_files, check_existing_files_plain(array($file.XC_DS.$item)));
            }

            closedir($handle);
        } else {
            if (file_exists($templates_directory.XC_DS.$file) && func_md5_file($templates_directory.XC_DS.$file) != func_md5_file($templates_repository.XC_DS.$file)) {
                $ex_files[] = $dstdir.XC_DS.$file;
            }
        }
        echo '.'; func_flush();
    }

    return $ex_files;
}

function copy_files_plain($files)
{
    global $templates_directory;
    global $templates_repository;
    global $xcart_dir;
    global $params;

    $status = true;
    foreach($files as $_k=>$file) {
        $status = $status && check_dir(dirname($templates_directory.XC_DS.$file));

        if (@is_dir($templates_repository.XC_DS.$file)) {
            if (!$handle = @opendir($xcart_dir.XC_DS.$templates_repository.XC_DS.$file)) {
                echo lng_get('copying_directory', 'dir', $templates_repository.XC_DS.$file, 'status', status(false));
                echo " - " . lng_get('err_file_dir_not_exist', 'file',$xcart_dir.XC_DS.$templates_repository.XC_DS.$file);
                echo "<br />\n";
                return false;
            }

            while ($status && ($item = readdir($handle)) !== false) {
                if ($item == '.' || $item == '..' || !strcasecmp($item,'_private') || !strncasecmp($item, '_vti', 4)) continue;

                if (!strcasecmp($item, 'thumbs.db')) continue;

                $status = $status && copy_files_plain(array($file.XC_DS.$item));
            }

            closedir($handle);
        } elseif (!file_exists($templates_repository.XC_DS.$file)) {
            $status = false;
            echo lng_get('checking_file_permissions','file',$templates_directory.XC_DS.$file)." - ";
            echo status($status);
            echo " - " .lng_get('err_file_dir_not_exist', 'file', $templates_repository.XC_DS.$file);
            echo "<br />\n";
            func_flush();
        } else {

            echo lng_get('copying_to_file','dst',$templates_directory.XC_DS.$file)." - ";

            if (file_exists($templates_directory.XC_DS.$file) && (func_md5_file($templates_directory.XC_DS.$file) != func_md5_file($templates_repository.XC_DS.$file)) && $params['flags']['ex_files_action'] == "K") {
                $status = 'skipped';

            } else {

                if (file_exists($templates_directory.XC_DS.$file)) {
                    @unlink($templates_directory.XC_DS.$file);
                }

                if (!@copy($templates_repository.XC_DS.$file, $templates_directory.XC_DS.$file) && basename($file)!='.htaccess') {
                    $status = false;
                }

                if ($status) {
                    func_chmod_file($templates_directory.XC_DS.$file);
                }
            }

            echo status($status);
            if (!$status) {
                echo " - " . lng_get('err_wrong_permissions_files', 'dir', dirname($xcart_dir.XC_DS.$templates_directory.XC_DS.$file), 'src', $templates_repository.XC_DS.$file);
            }

            echo "<br />\n";
               func_flush();
        }
    }

    return $status;
}

function create_dirs($dirs)
{
    global $xcart_dir;
    $status = true;

    foreach ($dirs as $_k=>$val) {
        echo_lng('creating_directory', 'dir', $val);

        if (!file_exists($val)) {
            $res = func_mkdir($xcart_dir.XC_DS.$val);
            $status &= $res;

            echo status($res);
            if (!$res) {
                echo " - " . lng_get('err_wrong_permissions', 'dir', dirname($xcart_dir.XC_DS.$val));
            }

        } else
            echo "<font color=\"blue\">[".lng_get('dir_already_exists')."]</font>";

        echo "<br />\n";
        func_flush();
    }

    return $status;
}

function create_files($files_to_create)
{
    global $xcart_dir;

    if (is_array($files_to_create)) {
        foreach($files_to_create as $file=>$content) {
            if ($fd = @fopen($xcart_dir.XC_DS.$file, 'w')) {
                @fwrite($fd, $content);
                @fclose($fd);
                func_chmod_file($xcart_dir.XC_DS.$file);
            } else {

                return warning_error(lng_get('warn_file_create_failed', 'file', $file));
            }
        }
    }

    return true;
}

function delete_files($files, $empty_files=false)
{
    global $templates_directory;
    global $xcart_dir;

    if (!is_array($files)) $files = array($files);

    $status = true;

    foreach ($files as $_k=>$file) {
        $path = $templates_directory.'/'.$file;
        $realpath = $xcart_dir.'/'.$path;
        if (!file_exists($realpath) || basename($file)=='.htaccess')
            continue;

        if (is_array($empty_files) && in_array($file, $empty_files) && @filesize($realpath)==0)
            continue;

        if (@is_dir($realpath)) {
            if (!$handle = @opendir($realpath)) {
                echo lng_get('removing_directory','dir',$path)." - ".status(false);
                echo " - " . lng_get('err_wrong_permissions', 'dir', dirname($realpath));
                echo "<br />\n";
                return false;
            }

            while ($status && ($item = readdir($handle)) !== false) {
                if ($item == '.' || $item == '..') continue;

                $status = $status && delete_files($file.'/'.$item,$empty_files);
            }

            closedir($handle);
            @rmdir($realpath);
        } else {
            echo lng_get('removing_file','file',$path)." - ";

            $file_status = true;
            if (is_array($empty_files) && in_array($file, $empty_files)) {
                if (@filesize($realpath) > 0) {
                    $fp = @fopen($realpath, 'w');
                    if ($fp === false) {
                        $file_status = false;
                    } else {
                        if (fwrite($fp, "{* *}") === false) {
                            $file_status = false;
                        }
                        @fclose($fp);
                        func_chmod_file($realpath);
                    }
                }
            } elseif (!@unlink($realpath)) {
                $file_status = false;
            }

            echo status($file_status);
            if (!$file_status) {
                echo " - " . lng_get('err_wrong_permissions', 'dir', dirname($realpath));
            }
            echo "<br />\n";
               func_flush();
            $status = $status && $file_status;
        }
    }

    return $status;
}

function files_overwrite_warning($files_to_overwrite)
{
?>
    <h2 class="cfg-warning-header"><?php echo_lng('err_existing_files_found'); ?></h2>
    <?php echo_lng('txt_existing_files_found'); ?>
    <br /><br /><br />
    <img class="toggle-img" src="skin/common_files/images/plus.gif" alt="<?php echo_lng('click_to_open'); ?>" id="closeef" onclick="javascript: visibleBox('ef');" />
    <img class="toggle-img" src="skin/common_files/images/minus.gif" alt="<?php echo_lng('click_to_close'); ?>" style="display:none;" id="openef" onclick="javascript: visibleBox('ef');" />&nbsp;
    <a href="javascript: visibleBox('ef');"><?php echo_lng('click_to_see_files_list'); ?></a><br />
    <div id="boxef" style="display: none">
        <ul>
<?php
    foreach($files_to_overwrite as $_fname) {
        echo "<li>".$_fname."</li>";
    }
?>
        </ul>
    </div>
<br /><br />
<input type="hidden" name="params[flags][ex_files_action]" value="O" />
<br />
<?php
}

function func_rename_install_script($prefix="")
{
    global $xcart_dir;

    if (!empty($prefix))
        $prefix = "-".$prefix;

    $install_name = 'install' . $prefix . "-file-" . date("y-m-d") . "-" . substr(md5(uniqid(rand(), true)), 0, 5) . '.php';

    @rename($xcart_dir . '/install' . $prefix . '.php', $xcart_dir . XC_DS . $install_name);
    @clearstatcache();

    $success_rename = false;
    if (!file_exists($xcart_dir.'/install' . $prefix . '.php') &&
    file_exists($xcart_dir.XC_DS.$install_name)) {
        $success_rename = true;
    }

    return array($success_rename, $install_name);
}

function func_secure_key_exists()
{
    return isset($_COOKIE['salt']) && !empty($_COOKIE['salt']);
}

function func_generate_secure_key()
{

    $salt = substr(md5(uniqid(rand(), true) . XC_TIME ), 0, 12);

    func_setcookie_raw('salt', $salt, 0, '/', $_SERVER['HTTP_HOST'], false);

    return $salt;
}

function func_crypt_auth_code($auth_code)
{
    global $init_secure_key;

    $salt = (!empty($init_secure_key)) ? $init_secure_key : $_COOKIE['salt'];
    $secure_hash = $salt . md5($salt . trim($auth_code));

    return $secure_hash;
}

function func_get_auth_code()
{
    global $xcart_dir;

    $config_file = $xcart_dir . XC_DS . 'config.php';

    if (
        is_readable($config_file)
        && ($fp = @fopen($config_file, "r"))
    ) {
        $config_body = fread($fp, filesize($config_file));
        fclose($fp);

        if (preg_match('/^\s*\$installation_auth_code\s*=\s*[\'"](.*)[\'"]\s*;/Umi', $config_body, $res))
            $installation_auth_code = $res[1];
    }

    if (!empty($installation_auth_code))
        return $installation_auth_code;
    else         
        return 'BG6GJH39';
}

function func_is_default_auth_code($auth_code)
{
    return $auth_code == 'BG6GJH39' || $auth_code == '%LICENSE%';
}

function func_generate_auth_code()
{
    return strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
}

function func_write_auth_code_to_file($auth_code)
{

    // Write data to config.php
    if (!($fp = @fopen('config.php', "r+")))
        return false;

    $vars = array(
        'installation_auth_code' => $auth_code,
    );

    $allfile = '';
    while (!feof($fp)) {

        $buffer = fgets($fp, 4096);
        foreach($vars as $varname => $val) {

            if (preg_match('/^\$'.$varname.' *=/', $buffer))
                $buffer = '$' . $varname . ' = \'' . str_replace("'", "\'", $val) . '\';' . PHP_EOL;

        }

        $allfile .= $buffer;
    }

    ftruncate($fp, 0);
    rewind($fp);
    $wl = fwrite($fp, $allfile);
    fclose($fp);
    return $wl && $wl == strlen($allfile);
}

#############################################################
/**
 * Modules goes here
 */
#############################################################

/**
 * prepare: Select language
 */

function module_language(&$params)
{
    global $error, $templates_directory;
    global $installation_auth_code;
    global $installation_product;
    global $available_install_languages, $install_lng_defs;

?>
<center>
<br /><br /><br />
<?php echo_lng('select_language_prompt'); ?>:
<select name="params[lngcode]">
<?php foreach ($available_install_languages as $lngcode) { ?>
    <option value="<?php echo $lngcode; ?>"><?php echo $install_lng_defs[$lngcode]['name']; ?></option>
<?php } ?>
</select>

<br /><br />

</center>

<br />

<?php
    return false;
}

/**
 * start: Default module
 * Shows Terms & Conditions
 */

function module_moddefault(&$params)
{
    global $error, $templates_directory;
    global $installation_auth_code;
    global $installation_product;
    global $xcart_dir;
    global $module_definition;
    $func_is_installed = @$module_definition['is_installed'];
?>
<center>
<div id="copyright_notice">
<pre>
<?php
ob_start();
require './COPYRIGHT';
$tmp = ob_get_contents();
ob_end_clean();
echo htmlspecialchars($tmp);
?>
</pre>
</div>

<br />
<table>
<?php if (!empty($func_is_installed) && function_exists($func_is_installed) && $func_is_installed()) {
    define('ADDON_IS_INSTALLED',  true);
?>
<tr>
    <td align="right">
        <input type="radio" id="install_type_1" name="params[install_type]" value="1" checked="checked" />
    </td>
    <td align="left">
        <label for="install_type_1"><b><?php echo_lng('new_install'); ?></b></label>
    </td>
</tr>
<tr>
    <td align="right">
        <input type="radio" id="install_type_3" name="params[install_type]" value="3" />
    </td>
    <td align="left">
        <label for="install_type_3"><b><?php echo_lng('uninstall_module'); ?></b></label>
    </td>
</tr>
<?php } else {?>
<tr style="display: none;">
    <td><input type="hidden" name="params[install_type]" value="1" /></td>
</tr>
<?php }?>
<tr>
    <td align="left">
        <strong><?php echo_lng('auth_code'); ?>:&nbsp;</strong>
    </td>
    <td align="right">
        <input type="text" name="auth_code" size="20" />
    </td>
</tr>
</table>

<div class="auth-code-note">
    <?php echo_lng('auth_code_note'); ?>
</div>

<table>
<tr>
    <td valign="middle"><input id="agree" type="checkbox" name="params[agree]" /></td>
    <td valign="middle"><label for="agree"><?php echo_lng('i_accept_license'); ?></label></td>
</tr>
</table>

</center>

<br />

<?php
    return false;
}

/**
 * 'next' button handler. checks 'agree' button checked
 */

function module_moddefault_js_next()
{
?>
    function step_next() {
        if (document.getElementById('agree').checked || (document.getElementById('install_type_3') && document.getElementById('install_type_3').checked))
            return true;

        alert("<?php echo_lng_js('mod_license_alert'); ?>");
        return false;
    }
<?php
}

/**
 * end: Default module
 */

/**
 * start: modinstall
 * Installs the module
 */

function module_modinstall($params)
{
    global $error;
    global $module_definition;
    global $var_dirs;

?>

<script type="text/javascript" language="javascript">
//<![CDATA[
scrollDown();
//]]>
</script>

<?php
    $ck_res = true;

    if (!empty($module_definition['skin_files'])) {
        if (@$params['install_type'] == 3) {
            echo "<b>".lng_get('removing_skin_files')."</b><br /><br />";
            $ck_res = delete_files($module_definition['skin_files'],@$module_definition['skin_files_empty']);
            echo status($ck_res)."<br /><br />";
            if ($ck_res) {
                echo "<b>".lng_get('deactivating_module')."</b><br />";

                $sqlfiles = $module_definition['sql_files'];
                if (!is_array($sqlfiles))
                    $sqlfiles = array($sqlfiles);

                foreach ($sqlfiles as $_k => $f) {
                    if (strpos($f,'_remove') === false)
                        continue;
                    $ck_res = $ck_res && query_upload($f);
                    if (!$ck_res)
                        break;
                }

                if ($module_definition['onuninstall'] && function_exists($module_definition['onuninstall']))
                    $module_definition['onuninstall']();

                echo status($ck_res)."<br /><br />";
            }
        }
        else {
            if ( @$params['install_type'] == 1 && (empty($params['flags']['ex_files_action']) || !in_array($params['flags']['ex_files_action'], array("O", "K", "C"))) ) {
                echo "<br /><strong>".lng_get('checking_existing_files')."</strong><br />\n";

                // Check for existing files in the skin directory

                $files_to_overwrite = array_unique (check_existing_files_plain($module_definition['skin_files']));
                if(is_array($files_to_overwrite) && !empty($files_to_overwrite)) {
                    files_overwrite_warning($files_to_overwrite);
                    return false;
                } else {
                    echo "<br />".status(true)."<br /><br />";
                }
            }

            echo "<b>".lng_get('copying_skin_files')."</b><br /><br />";

            $ck_res = copy_files_plain($module_definition['skin_files']);
            echo status($ck_res)."<br /><br />";
        }
    }

    if (@$params['install_type']==1 && $ck_res && !empty($module_definition['sql_files'])) {
        echo "<b>".lng_get('activating_module')."</b><br />";

        $sqlfiles = $module_definition['sql_files'];
        if (!is_array($sqlfiles)) $sqlfiles = array($sqlfiles);
        foreach ($sqlfiles as $_k=>$f) {
            $ck_res = $ck_res && query_upload($f);
            if (!$ck_res) break;
        }
    }

    func_rm_dir($var_dirs['cache'], true);
    func_rm_dir($var_dirs['templates_c'], true);

    $error = !$ck_res;
?>

<script type="text/javascript" language="javascript">
//<![CDATA[
    loaded = true;
//]]>
</script>

<?php
}

/**
 * end: modinstall
 */

/**
 * start: Generate_snapshot module
 */

function module_generate_snapshot(&$params, $ss_name = '')
{
    global $xcart_dir, $var_dirs, $sql_tbl, $smarty, $data_caches, $memcache;
    // To avoid 'Undefined variable' php warnings during Moving images to the file system
    global $REQUEST_METHOD, $antibot_validation_val, $config;

    if (
        file_exists($xcart_dir.'/init.php')
        && is_readable($xcart_dir.'/init.php')
    ) {
        include_once $xcart_dir.'/init.php';
    }

    x_load('snapshots','logging');

    if (!defined('XCART_INSTALL')) {
        x_load('backoffice');

        // Update the 'display_states' of xcart_countries table

        func_update_country_states('', true);
    }

    // Generate the system snapshot

    $current_time = XC_TIME;
    $md5file = f_get_md5file_name($current_time);

    echo_lng('txt_begin_generating_snapshot');
    func_flush();

    $result = func_generate_snapshot($md5file, true);
    if ($result['error']) {
        echo "<font color='red'>"; echo_lng("err_".$result["errordescr"]); echo "</font>";
    }
    else {
        $config_snapshots = f_get_snapshots();
        $config_snapshots[] = array('time'=>$current_time, 'descr'=>lng_get('installation_snapshot').$ss_name);
        f_update_snapshots($config_snapshots);
        echo "<br />";
        echo_lng('msg_snapshot_generated');
        if (!empty($result['unprocessed_files'])) {
            echo_lng('txt_N_unprocessed_files_in_snapshot', 'unproc', $result['unprocessed_files'], 'total', $result['total_files']);
            func_snapshot_add_to_log($result['unprocessed_files_list']);
        }
    }
    echo "<br /><br />";
}

/**
 * end: Generate_snapshot module
 */

/**
 * start: Install_done module
 */

function module_install_done(&$params, $modparam)
{
    global $error, $installation_auth_code, $templates_repository;
    global $xcart_dir;
    global $xcart_package;
    global $module_definition;

    if (!empty($module_definition)) {
        echo "<div class=\"remove-package-recommend\">";
        echo lng_get('distribution_warning', 'product', $module_definition['name'], 'script_name', $module_definition['script']);
        echo "</div><br /><br />";
        echo lng_get('module_installed', 'name', $module_definition['name']);
    }

    $xcart_package = file_exists((!empty($xcart_dir) ? $xcart_dir . XC_DS : '') . $templates_repository . XC_DS . 'admin' . XC_DS . 'home.tpl') ? 'PRO' : 'GOLD';

    if (!empty($modparam)) {
        if (function_exists($modparam)) $modparam();
        else echo $modparam;
    }

    if (!empty($module_definition)) {
        // Rename installation script
        preg_match('/^(install-)?([^\.php]+)/i', $module_definition["script"], $tmp);

        if ($tmp[2] && !empty($tmp[2]))
            list ($success_rename, $install_name) = func_rename_install_script($tmp[2]);
        if ($success_rename) {
            $install_rename = lng_get('module_install_rename_success', 'product', $module_definition['name'], 'script_name', $module_definition['script'], 'install_name', $install_name);
        } else {
            $install_rename = lng_get('module_install_rename_failed', 'product', $module_definition['name'], 'script_name', $module_definition['script']);
        }

        echo $install_rename;
    }

    if (
        file_exists($xcart_dir.'/config.php')
        && is_readable($xcart_dir.'/config.php')
    ) {
        @include $xcart_dir.'/config.php';
    }
    if (isset($var_dirs['templates_c']))
        func_rm_dir($var_dirs['templates_c'], true);
    return false;
}

/**
 * end: Install_done module
 */

/**
 * start: uninstall_done module
 */

function module_uninstall_done(&$params, $modparam)
{
    global $error, $installation_auth_code, $templates_repository;
    global $xcart_dir;
    global $xcart_package;
    global $module_definition;

    if (!empty($module_definition)) {
       // Rename installation script
        preg_match('/^(install-)?([^\.]+)/i', $module_definition["script"], $tmp);

        if ($tmp[2] && !empty($tmp[2]))
            list ($success_rename, $install_name) = func_rename_install_script($tmp[2]);
        if ($success_rename) {
            $install_rename = lng_get('module_install_rename_success', 'product', $module_definition['name'], 'script_name', $module_definition['script'], 'install_name', $install_name);
        } else {
            $install_rename = lng_get('module_install_rename_failed', 'product', $module_definition['name'], 'script_name', $module_definition['script']);
        }
        echo "<h3><b><font color=\"darkgreen\">".lng_get('module_uninstalled', 'name',$module_definition['name'])."</font></b></h3>";
        echo $install_rename;
    }

    return false;
}

/**
 * end: uninstall_done module
 */

/**
 * start: install_cancel module
 */

function module_install_cancel(&$params)
{
    global $installation_product;

    echo "<span id=\"top_message\">".lng_get('installation_canceled', 'name', $installation_product)."</span>";
}

?>
