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
 * Module functions
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: spambot_arrest_func.php,v 1.17.2.1 2011/01/10 13:11:58 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if (!defined('XCART_START')) { header("Location: ../../"); die("Access denied"); }

// Generates codes(symbols) for each ACTIVE location
function func_generate_codes($pages, $codes = array())
{
    global $config, $xcart_dir;

    if (empty($config['Image_Verification']['spambot_arrest_str_generator'])) {
        $config['Image_Verification']['spambot_arrest_str_generator'] = "numbers";
    }
    include_once $xcart_dir. '/modules/Image_Verification/' .$config['Image_Verification']['spambot_arrest_str_generator'].".php";
    if (!function_exists('func_antibot_str_generator'))
        return false;

    $image_length = intval($config['Image_Verification']['spambot_arrest_image_length']);
    if ($image_length < 1) {
        $image_length = 1;
    }

    foreach ($pages as $page => $value) {

        if (
            $value == 'Y'
            && (
                !isset($codes[$page])
                || !isset($codes[$page]['used'])
                || $codes[$page]['used'] != 'N'
            )
        ) {
            $codes[$page]['code'] = func_antibot_str_generator($image_length);
            $codes[$page]['used'] = "N";
        }
    }

    return $codes;
}

// Validates code from image
function func_validate_image($code, $input_str)
{
    global $config, $show_antibot_arr, $antibot_validation_val;

    if (!isset($show_antibot_arr) || !is_array($show_antibot_arr) || !isset($show_antibot_arr[$code]) || $show_antibot_arr[$code] != 'Y')
        return false;

    if (!isset($antibot_validation_val[$code]) || $antibot_validation_val[$code]['used'] == "Y")
        return true;

    $antibot_validation_val[$code]['used'] = "Y";

    if (empty($input_str))
        return true;

    if ($config['Image_Verification']['spambot_arrest_case_sensitive'] == 'N') {
        return strtolower($antibot_validation_val[$code]['code']) != strtolower($input_str);
    } else {
        return $antibot_validation_val[$code]['code'] != $input_str;
    }
}

// Check section provided from _GET query bt:83423
function func_check_antibot_section($section)
{
    global $antibot_sections;

    if (empty($antibot_sections) || !is_array($antibot_sections))
        return false;

    $antibot_sections[] = 'image';
    return in_array($section, $antibot_sections);
}
?>
