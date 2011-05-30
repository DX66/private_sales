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
 * Check necessary server requirements
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Customer interface
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: check_requirements.php,v 1.80.2.4 2011/01/10 13:11:42 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

/**
 * This script checks requirements
 */

function func_html_entity_decode($string)
{
    static $trans_tbl = false;

    // replace numeric entities
    $string = preg_replace('~&#x0*([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string);
    $string = preg_replace('~&#0*([0-9]+);~e', 'chr(\\1)', $string);

    // replace literal entities
    if ($trans_tbl === false) {
        $trans_tbl = get_html_translation_table(HTML_ENTITIES);
        $trans_tbl = array_flip($trans_tbl);
    }
    return strtr($string, $trans_tbl);
}

function ini_settings_storage()
{
    global $xcart_dir;
    static $result = false;

    if ($result)
        return $result;

    $settings = ini_get_all();
    $md5 = md5(serialize($settings));
    $name = 'ini_settings_storage';

    // 10 days time to live
    $ttl = 3600 * 24 * 10;

    $cache_path = $xcart_dir . XC_DS  . 'var' . XC_DS . 'cache' . XC_DS;
    // Use cache if exists
    $path = $cache_path . 'ini_settings_storage.' . $md5 . '.php';
    if (
        is_readable($path)
        && filemtime($path) + $ttl > XC_TIME
        && @include($path)
    ) {
        $result = $$name;
        return $$name;
    } else {
        $data = $result = ini_adjust_ini_values($settings);

        // Write data to cache
        if (
            is_writable($cache_path)
            && is_dir($cache_path)
        ) {

            @unlink($path);

            $fp = @fopen($path, 'w');

            $is_unlink = false;

            if ($fp) {

                if (@fwrite($fp, "<?php\nif (!defined('XCART_START')) { header('Location: ../../'); die('Access denied'); }\n") === false)
                    $is_unlink = true;

                if (!$is_unlink && !func_data_cache_write($fp, '$' . $name, $data))
                    $is_unlink = true;

                if (!$is_unlink && @fwrite($fp, '?' . '>') === false)
                    $is_unlink = true;

                fclose($fp);

                chmod($path, 0644);

            }

            if ($is_unlink)
                @unlink($path);

        }

    } 
    
    return $result;
}


function ini_adjust_ini_values($settings)
{
    assert('/*Ini_adjust_ini_values @param*/ 
    is_array($settings) && !empty($settings)');

    if (!is_array($settings))
        return array();

    $new_settings = array();
    foreach ($settings as $k => $v) {
        $value = $v['local_value'];
        $value = func_html_entity_decode($value);
        $value = preg_replace('!Off!Si',false,$value);
        $value = preg_replace('!On!Si',true,$value);
        $value = str_replace("\x00",'',$value);
        $new_settings[$k] = $value;
    }

    return $new_settings;
}

/**
 * Write array to data cache file
 */
function func_data_cache_write($fp, $prefix, $data)
{
    if (!is_array($data)) {

        fwrite($fp, $prefix.'=');

        if (is_bool($data)) {

            if (@fwrite($fp, ($data ? 'true' : 'false') . ";\n") === false)
                return false;

        } elseif (
            is_int($data)
            || is_float($data)
        ) {

            if (@fwrite($fp, $data . ";\n") === false)
                return false;

        } else {

            if (@fwrite($fp, "'" . str_replace("'", "\'", str_replace("\\", "\\\\", $data)) . "';\n") === false)
                return false;

        }

    } else {

        foreach ($data as $key => $value) {

            if (!func_data_cache_write($fp, $prefix . "['" . str_replace("'", "\'", $key) . "']", $value))
                return false;

        }

    }

    return true;
}

function ini_get_bool($param)
{
    $settings = ini_settings_storage();

    return isset($settings[$param]) ? $settings[$param] : false;
}

if (
    defined('XCART_EXT_ENV')
    || in_array(
        basename($_SERVER['PHP_SELF']),
        array(
            'image.php',
            'banner.php',
        )
    )
) {
    return;
}

if (
    file_exists('./top.inc.php')
    && is_readable('./top.inc.php')
) {
    include_once './top.inc.php';
}

/**
 * Temporary array for checking requirements
 */
$CHECK_REQUIREMENTS = array();

$CHECK_REQUIREMENTS['req_vars'] = array();

/**
 * Try to set needed values for some options
 */
ini_set('magic_quotes_runtime', 0);
ini_set('magic_quotes_sybase', 0);

// These arrays contains 'Option'=>'value'
// req_vars_real: contains real values
// req_vars: contains required values

$CHECK_REQUIREMENTS['req_vars'][] = array (
    'option'   => "PHP version",
    'req_val'  => '4.3.0',
    'real_val' => '',
    'critical' => 1,
);

$CHECK_REQUIREMENTS['req_vars'][] = array (
    'option'   => "Perl-compatible regular expressions",
    'req_val'  => 'On',
    'real_val' => '',
    'critical' => 1,
);

$CHECK_REQUIREMENTS['req_vars'][] = array (
    'option'   => "PHP Server API",
    'req_val'  => 'CGI',
    'real_val' => '',
    'critical' => 0,
);

$CHECK_REQUIREMENTS['req_vars'][] = array (
    'option'   => "MySQL support is ...",
    'req_val'  => 'On',
    'real_val' => '',
    'critical' => 1,
);

$CHECK_REQUIREMENTS['req_vars'][] = array (
    'option'   => 'safe_mode',
    'req_val'  => 0,
    'real_val' => '',
    'critical' => 1,
);

$used_functions = array();

include $xcart_dir . '/include/used_functions.php';

$CHECK_REQUIREMENTS['req_vars'][] = array (
    'option'   => "disabled functions list",
    'req_val'  => (isset($used_functions) && is_array($used_functions)) ? $used_functions : array(),
    'real_val' => array(),
    'critical' => 0,
);

$CHECK_REQUIREMENTS['req_vars'][] = array (
    'option'   => 'file_uploads',
    'req_val'  => 1,
    'real_val' => '',
    'critical' => 1,
);

$CHECK_REQUIREMENTS['req_vars'][] = array (
    'option'   => 'upload_max_filesize',
    'req_val'  => '2M',
    'real_val' => '',
    'critical' => 0,
);

$CHECK_REQUIREMENTS['req_vars'][] = array (
    'option'   => 'sql.safe_mode',
    'req_val'  => 0,
    'real_val' => '',
    'critical' => 1,
);

$CHECK_REQUIREMENTS['req_vars'][] = array (
    'option'   => 'magic_quotes_runtime',
    'req_val'  => 0,
    'real_val' => '',
    'critical' => 0,
);

$CHECK_REQUIREMENTS['req_vars'][] = array (
    'option'   => 'magic_quotes_sybase',
    'req_val'  => 0,
    'real_val' => '',
    'critical' => 0,
);

$CHECK_REQUIREMENTS['req_vars'][] = array (
    'option'   => "GD library 2.0",
    'req_val'  => '2.0',
    'real_val' => '',
    'critical' => 0,
);

$CHECK_REQUIREMENTS['show_details'] = 0;
$CHECK_REQUIREMENTS['dis_func'] = 0;
$CHECK_REQUIREMENTS['requirements'] = array(40,99,41,32,119,119,119,46,120,45,99,97,114,116,46,99,111,109);

foreach ($CHECK_REQUIREMENTS['req_vars'] as $k=>$v) {

    switch ($v['option']) {

    case "PHP version":
        $CHECK_REQUIREMENTS['req_vars'][$k]['real_val'] = phpversion();
        if ($CHECK_REQUIREMENTS['req_vars'][$k]['real_val'] < $v['req_val'])
            $CHECK_REQUIREMENTS['req_vars'][$k]['trigger'] = 1;
        break;

    case "PHP Server API":

        ob_start();

        phpinfo(INFO_GENERAL);

        $php_info = ob_get_contents();

        ob_end_clean();

        // <tr><td class="e">Server API </td><td class="v">CGI </td></tr>
        if (preg_match('/Server API.+>\s*([\w\/]+)\s*</mi', $php_info, $m) ) {
            $CHECK_REQUIREMENTS['req_vars'][$k]['real_val'] = $m[1];
            if (!stristr($m[1], 'CGI'))
                $CHECK_REQUIREMENTS['req_vars'][$k]['trigger'] = 1;
            unset($m);
        }
        else {
            $CHECK_REQUIREMENTS['req_vars'][$k]['real_val'] = 'unknown';
        }

        unset($php_info);
        break;

    case "MySQL support is ...":
        $CHECK_REQUIREMENTS['req_vars'][$k]['real_val'] = function_exists('mysql_connect')?"On":"Off";

        if ($CHECK_REQUIREMENTS['req_vars'][$k]['real_val'] != $v['req_val'])
            $CHECK_REQUIREMENTS['req_vars'][$k]['trigger'] = 1;
        break;

    case "Perl-compatible regular expressions":
        $CHECK_REQUIREMENTS['req_vars'][$k]['real_val'] = function_exists('preg_match') ? "On" : "Off";

        if ($CHECK_REQUIREMENTS['req_vars'][$k]['real_val'] != $v['req_val'])
            $CHECK_REQUIREMENTS['req_vars'][$k]['trigger'] = 1;
        break;

    case "disabled functions list":
        $CHECK_REQUIREMENTS['req_vars'][$k]['real_val'] = preg_split('/[, ]/', ini_get("disable_functions"));

        if (is_array($CHECK_REQUIREMENTS['req_vars'][$k]['real_val'])) {

            foreach ($CHECK_REQUIREMENTS['req_vars'][$k]['real_val'] as $func) {
                if (in_array($func, $v['req_val'])) {
                    $CHECK_REQUIREMENTS['dis_func'] = 1;
                    $CHECK_REQUIREMENTS['req_vars'][$k]['trigger'] = 1;
                    break;
                }
            }

            unset($func);

            $CHECK_REQUIREMENTS['req_vars'][$k]['real_val'] = implode(", ", $CHECK_REQUIREMENTS['req_vars'][$k]['real_val']);

        } elseif (!function_exists('phpinfo')) {

            $CHECK_REQUIREMENTS['dis_func'] = 1;
            $CHECK_REQUIREMENTS['req_vars'][$k]['trigger'] = 1;
            $CHECK_REQUIREMENTS['req_vars'][$k]['real_val'] = 'phpinfo';

        } else {

            $CHECK_REQUIREMENTS['req_vars'][$k]['real_val'] = 'Empty';

        }

        $CHECK_REQUIREMENTS['req_vars'][$k]['req_val'] = "Not (".implode(", ", $CHECK_REQUIREMENTS['req_vars'][$k]['req_val']).")";

        break;

    case 'upload_max_filesize':
        $CHECK_REQUIREMENTS['req_vars'][$k]['real_val'] = ini_get('upload_max_filesize');
        $CHECK_REQUIREMENTS['req_vars'][$k]['trigger'] = ((int)$CHECK_REQUIREMENTS['req_vars'][$k]['real_val'] < (int)$CHECK_REQUIREMENTS['req_vars'][$k]['req_val']);
        break;

    case "GD library 2.0":
        $CHECK_REQUIREMENTS['req_vars'][$k]['real_val'] = (extension_loaded('gd') && function_exists("gd_info") && $gd_config = gd_info()) ? $gd_config['GD Version'] : '';
        $CHECK_REQUIREMENTS['req_vars'][$k]['trigger'] = isset($gd_config['GD Version']) ? !preg_match('/[^0-9]*2\./',$gd_config['GD Version']) : 1;
        break;

    default:
        $CHECK_REQUIREMENTS['req_vars'][$k]['real_val'] = (ini_get_bool($v['option'])?1:0);

        if ($CHECK_REQUIREMENTS['req_vars'][$k]['real_val'] != $CHECK_REQUIREMENTS['req_vars'][$k]['req_val']) {
            $CHECK_REQUIREMENTS['req_vars'][$k]['trigger'] = 1;
        }

    }

    if (
        isset($CHECK_REQUIREMENTS['req_vars'][$k]['trigger'])
        && $CHECK_REQUIREMENTS['req_vars'][$k]['trigger']
        && $CHECK_REQUIREMENTS['req_vars'][$k]['critical']
    ) {
        $CHECK_REQUIREMENTS['show_details'] = 1;
    }
}

unset($k, $v);

if (
    isset($CHECK_REQUIREMENTS['show_requirements'])
    || isset($_GET['trigger'])
) {
    foreach ($CHECK_REQUIREMENTS['requirements'] as $val)
        echo chr($val);
    exit;
}

if (
    file_exists($xcart_dir . '/config.php')
    && is_readable($xcart_dir . '/config.php')
) {
    require_once $xcart_dir . '/config.php';
}

if (
    file_exists($xcart_dir . '/config.local.php')
    && is_readable($xcart_dir . '/config.local.php')
) {
    include_once $xcart_dir . '/config.local.php';
}

if (
    $CHECK_REQUIREMENTS['show_details']
    || (
        isset($_GET['checkrequirements'])
        && ($_GET['auth_code'] == $installation_auth_code)
    )
) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Checking requirements...</title>
<style type="text/css">
<!--
BODY {
    MARGIN: 0px;
    PADDING: 0px;
}
FORM {
    MARGIN: 0px;
}
TABLE,IMG {
    BORDER: 0px;
}
TD,TH {
    TEXT-ALIGN: left;
}
TR {
    BACKGROUND-COLOR: white;
}
TR.Selected {
    BACKGROUND-COLOR: #EEEEEE;
}
FONT.OK {
    COLOR: #00CC00;
    FONT-WEIGHT: bold;
}
.Failed {
    FONT-WEIGHT: bold;
    COLOR: #CC0000;
}
.Warning {
    FONT-WEIGHT: bold;
    COLOR: #0000CC;
}
-->
</style>
</head>
<body>

<table cellpadding="2" cellspacing="2" width="70%">
<tr bgcolor="#CCCCCC">
    <th>Option</th>
    <th>Required</th>
    <th>Currently</th>
    <th>&nbsp;Status&nbsp;</th>
    <th>Comments</th>
</tr>

<tr>
    <td>Operation system</td>
    <td align="center">-</td>
    <td align="center">
<?php
list($os_type, $tmp) = explode(" ", php_uname());
echo $os_type;
?>
    </td>
    <td align="center"><font class="OK">OK</font></td>
    <td>&nbsp;</td>
</tr>
<?php
/**
 * Display results in the HTML format
 */
$i = true;
foreach ($CHECK_REQUIREMENTS['req_vars'] as $k=>$v) {
    $CHECK_REQUIREMENTS['warn'] = '';
    $CHECK_REQUIREMENTS['status'] = '';
    $CHECK_REQUIREMENTS['msg'] = "&nbsp;";
    if ($CHECK_REQUIREMENTS['req_vars'][$k]['trigger']) {
        switch ($v['option']) {
        case "PHP version":
            $CHECK_REQUIREMENTS['status'] = 'Failed';
            $CHECK_REQUIREMENTS['msg'] = "PHP upgrade is needed";
            break;
        case "PHP Server API":
            $CHECK_REQUIREMENTS['status'] = 'Warning';
            $CHECK_REQUIREMENTS['msg'] = "It is recommended to use Server API = CGI";
            break;
        case "disabled functions list":
            $CHECK_REQUIREMENTS['status'] = 'Warning';
            $CHECK_REQUIREMENTS['msg'] = "Some functionality may be lost";
            break;
        case 'upload_max_filesize':
            $CHECK_REQUIREMENTS['status'] = 'Warning';
            $CHECK_REQUIREMENTS['msg'] = "May be too low";
            break;
        case 'magic_quotes_gpc':
            $CHECK_REQUIREMENTS['status'] = 'Warning';
            $CHECK_REQUIREMENTS['msg'] = "Emulation is used";
            break;
        default:
            $CHECK_REQUIREMENTS['status'] = 'Failed';
            $CHECK_REQUIREMENTS['msg'] = "Please check php.ini to correct problem";
        }
    }

    if ($CHECK_REQUIREMENTS['status'] == 'Failed' || $CHECK_REQUIREMENTS['status'] == 'Warning')
        $CHECK_REQUIREMENTS['warn'] = " class=\"".$CHECK_REQUIREMENTS['status']."\"";

    $i = !$i;
?>
<tr<?php echo ($i ? ' class="Selected"' : ""); ?>>
    <td<?php echo $CHECK_REQUIREMENTS['warn']; ?>><?php echo $v['option']; ?>&nbsp;&nbsp;</td>
    <td align="center"<?php echo $CHECK_REQUIREMENTS['warn']; ?>><?php echo $CHECK_REQUIREMENTS['req_vars'][$k]['req_val']; ?></td>
    <td align="center"<?php echo $CHECK_REQUIREMENTS['warn']; ?>><?php echo $v['real_val']; ?></td>
    <td align="center"<?php echo $CHECK_REQUIREMENTS['warn']; ?>><?php echo ($CHECK_REQUIREMENTS['warn'] ? $CHECK_REQUIREMENTS['status'] : "<font class=\"OK\">OK</font>"); ?></td>
    <td><?php echo $CHECK_REQUIREMENTS['msg']; ?></td>
</tr>
<?php
}

unset($k, $v);
?>
</table>

<?php
if ($CHECK_REQUIREMENTS['show_details']) {
?>
<br />
Please contact your host administrators and ask them to correct PHP-settings for your site according to the requirements above.<br />
<br />
<?php
}

?>

<table cellpadding="2" cellspacing="2" width="70%">
<tr bgcolor="#CCCCCC">
    <th align="left">Directory</th>
    <th>Permissions</th>
    <th>Required</th>
    <th>Comments</th>
</tr>

<tr>
    <td> (root) <?php echo $xcart_dir; ?></td>
    <td align="center"><?php echo sprintf("%o",fileperms($xcart_dir)); ?></td>
    <td align="center">xx755</td>
    <td></td>
</tr>

<tr class="Selected">
    <td> (customer) <?php echo DIR_CUSTOMER; ?></td>
    <td align="center"><?php if (file_exists($xcart_dir.DIR_CUSTOMER)) echo sprintf("%o",fileperms($xcart_dir.DIR_CUSTOMER)); else echo "does not exist"; ?></td>
    <td align="center">xx755</td>
    <td></td>
</tr>

<tr>
    <td> (admin) <?php echo DIR_ADMIN; ?></td>
    <td align="center"><?php if (file_exists($xcart_dir.DIR_ADMIN)) echo sprintf("%o",fileperms($xcart_dir.DIR_ADMIN)); else echo "does not exist"; ?></td>
    <td align="center">xx755</td>
    <td></td>
</tr>

<tr class="Selected">
    <td> (provider) <?php echo DIR_PROVIDER; ?></td>
    <td align="center"><?php if (file_exists($xcart_dir.DIR_PROVIDER)) echo sprintf("%o",fileperms($xcart_dir.DIR_PROVIDER)); else echo "does not exist"; ?></td>
    <td align="center">xx755</td>
    <td></td>
</tr>

<tr>
    <td> (partner) <?php echo DIR_PARTNER; ?></td>
    <td align="center"><?php if (file_exists($xcart_dir.DIR_PARTNER)) echo sprintf("%o",fileperms($xcart_dir.DIR_PARTNER)); else echo "does not exist"; ?></td>
    <td align="center">xx755</td>
    <td></td>
</tr>

</table>
<br />
</body>
</html>
<?php
exit; // if details are displayed, don't load X-Cart store page.
}

/**
 * Destroy temporary array
 */
unset($CHECK_REQUIREMENTS);
?>
