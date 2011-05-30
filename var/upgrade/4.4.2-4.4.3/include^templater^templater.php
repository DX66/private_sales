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
 * Templater extension
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: templater.php,v 1.29.2.1 2011/01/10 13:11:53 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../../"); die("Access denied"); }

include_once SMARTY_DIR.'Smarty.class.php';
include_once SMARTY_DIR.'Smarty_Compiler.class.php';

if (!class_exists('Smarty')) {
    func_show_error_page("Cannot find template engine!");
}

class Templater extends Smarty {

    function Templater() {
        global $xcart_dir;

        $this->strict_resources = array ();

        array_unshift($this->plugins_dir, $xcart_dir . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'templater' . DIRECTORY_SEPARATOR . 'plugins');

        $this->compiler_file  = $xcart_dir. DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'templater' . DIRECTORY_SEPARATOR . 'templater.php';
        $this->compiler_class = 'TemplateCompiler';

        $this->compile_check_md5 = false;

        $exec_mode = func_get_php_execution_mode();
        if ($exec_mode == 'privileged') {
            $this->_dir_perms  = 0711;
            $this->_file_perms = 0600;
        }

        return parent::Smarty();
    }

    function fetch($resource_name, $cache_id = null, $compile_id = null, $display = false) {
        $this->current_resource_name = $resource_name;
        return parent::fetch($resource_name, $cache_id, $compile_id, $display);
    }

    function _is_compiled($resource_name, $compile_path) {
        if (!empty($this->strict_resources)) {
            foreach ($this->strict_resources as $rule) {
                if (preg_match($rule, $resource_name)) {
                    return false;
                }
            }
        }

        $result = parent::_is_compiled($resource_name, $compile_path);
        if ($result && $this->compile_check_md5)
            return $this->_check_compiled_md5($compile_path);

        return $result;
    }

    // Test if compiled resource was changed by third party

    function _check_compiled_md5($compiled_file) {

        if ((rand() % 10) != 5) return true;

        $control_file = $compiled_file.'.md5';

        $compiled_data = $this->_read_file($compiled_file);
        if ($compiled_data === false)
            return false;

        $control_data = $this->_read_file($control_file);
        if ($control_data === false)
            return false;

        $md5 = md5($compiled_file.$compiled_data);
        return !strcmp($md5,$control_data);
    }

    function _compile_resource($resource_name, $compile_path) {
        $result = parent::_compile_resource($resource_name, $compile_path);

        if ($result && $this->compile_check_md5) {
            $tpl_source = $this->_read_file($compile_path);
            if ($tpl_source !== false) {
                $_params = array(
                    'filename' => $compile_path.'.md5',
                    'contents' => md5($compile_path.$tpl_source),
                    'create_dirs' => true
                );
                smarty_core_write_file($_params, $this);
            }
        }

        return $result;
    }

    function _smarty_include($params) {
        static $vars;

        if (isset($params['smarty_include_vars']['_include_once']) && $params['smarty_include_vars']['_include_once'] == 1) {
            if (isset($vars[$params['smarty_include_tpl_file']]))
                return '';
            $vars[$params['smarty_include_tpl_file']] = true;
        }
        parent::_smarty_include($params);
    }

    // use X-Cart internal function instead of the default one
    function clear_cache($tpl_file = null, $cache_id = null, $compile_id = null, $exp_time = null) {

        return func_rm_dir($this->cache_dir, true);
    }

    // use X-Cart internal function instead of the default one
    function clear_compiled_tpl($tpl_file = null, $compile_id = null, $exp_time = null) {

        return func_rm_dir($this->compile_dir, true);
    }

}

class TemplateCompiler extends Smarty_Compiler {
    function _compile_file($resource_name, $source_content, &$compiled_content) {
        $this->current_resource_name = $resource_name;

        return parent::_compile_file($resource_name, $source_content, $compiled_content);
    }
}

/**
 * Special parameter
 */
$_prnotice_txt=<<<OUT
shopping-cart-software
OUT;

?>
