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
 * Image verification module initialization
 *  
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Modules
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: init.php,v 1.26.2.1 2011/01/10 13:11:58 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_START') ) { header("Location: ../../"); die("Access denied"); }

$display_antibot = true;

$config['Image_Verification']['spambot_arrest_login_attempts'] = 3;

$show_antibot_arr = array (
    'on_send_to_friend'        => $config['Image_Verification']['spambot_arrest_on_send_to_friend'],
    'on_contact_us'         => $config['Image_Verification']['spambot_arrest_on_contact_us'],
    'on_registration'         => $config['Image_Verification']['spambot_arrest_on_registration'],
    'on_login'                 => $config['Image_Verification']['spambot_arrest_on_login'],
    'on_reviews'             => $config['Image_Verification']['spambot_arrest_on_reviews'],
    'on_surveys'             => (!empty($active_modules['Survey'])) ? $config['Image_Verification']['spambot_arrest_on_surveys'] : 'N',
    'on_news_panel'         => (!empty($active_modules['News_Management'])) ? $config['Image_Verification']['spambot_arrest_on_news'] : 'N',
    'on_ask_form'            => $config['Image_Verification']['spambot_arrest_on_ask_form'],
);

if (
    !empty($active_modules['Survey'])
    && !defined('QUICK_START')
) {

    $surveys_ids = func_query_column("SELECT surveyid FROM $sql_tbl[surveys] WHERE survey_type != 'D'");

    if (!empty($surveys_ids)) {

        foreach ($surveys_ids as $sid) {

            list($valid, $_error_messages) = func_check_survey($sid);

            if (
                $valid
                && !func_check_survey_filling($sid)
            ) {
                $show_antibot_arr['on_surveys_' . $sid] = $config['Image_Verification']['spambot_arrest_on_surveys'];
            }

        }

    }

}

require $xcart_dir . '/modules/Image_Verification/spambot_arrest_func.php';

$antibot_sections = array();

foreach (array_keys($show_antibot_arr) as $key) {
    $antibot_sections[$key] = $key;
}

if (defined('ANTIBOT_SKIP_INIT') && constant('ANTIBOT_SKIP_INIT'))
    return;

if (defined('QUICK_START'))
    return;

// Check for GD library presence
$gd_not_loaded = false;
if (!extension_loaded('gd') || !function_exists("gd_info")) {
    // Turn off ImageVerification module if GD is not installed
    unset($active_modules['Image_Verification']);
    return;
}

x_session_register('antibot_validation_val');
$antibot_validation_val = func_generate_codes($show_antibot_arr, $antibot_validation_val);
if (!empty($antibot_validation_val))
    x_session_save('antibot_validation_val');

$smarty->assign('show_antibot', $show_antibot_arr);

$smarty->assign('antibot_sections', $antibot_sections);
?>
