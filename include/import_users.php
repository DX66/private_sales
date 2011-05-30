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
 * Users import/export library
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>. All rights reserved
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: import_users.php,v 1.41.2.2 2011/01/10 13:11:49 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load('crypt','mail','user');

/******************************************************************************
Used cache format:
Memberships:
    data_type:     M
    key:        <Membership name>
    value:        <Membership ID>
Users (by ID):
    data_type:     UI
    key:        <id>
    value:        <id | RESERVED>
Users (by login):
    data_type:     UL
    key:        <Login>
    value:        <Login | RESERVED>

Note: RESERVED is used if ID is unknown
******************************************************************************/

if ($import_step == 'define') {

    $import_specification['USERS'] = array(
        'script'        => '/include/import_users.php',
        'tpls'            => array(
            'main/import_option_password_crypt.tpl'
        ),
        'permissions'    => 'A',
        'is_range'        => $xcart_web_dir . DIR_ADMIN . '/users.php?is_range',
        'export_sql'    => 'SELECT id FROM ' . $sql_tbl['customers'],
        'table'            => 'customers',
        'key_field'        => 'id',
        'orderby'       => 15,
        'columns'        => array(
            'id'        => array(
                'is_key'    => true,
                'required'  => 0,
                'inherit'   => 0,
                'type'      => 'N',
                'default'   => 0
            ),
            'login'                    => array(
                'required'    => true
            ),
            'email'                    => array(
                'required'    => true
            ),
            'usertype'                => array(
                'required'    => true,
                'type'        => 'E',
                'variants'    => array(
                    'C', 'A', 'P', 'B'
                )
            ),
            'password'                => array(
                'required'    => true
            ),
            'title'                    => array(),
            'firstname'                => array(),
            'lastname'                => array(),
            'company'                => array(),
            'url'                    => array(),
            'status'                => array(
                'type'        => 'E',
                'variants'    => array(
                    'N', 'Y', 'Q', 'D', 'A'
                )
            ),
            'referer'                => array(),
            'ssn'                    => array(),
            'language'                => array(
                'type'        => 'C'
            ),
            'change_password'        => array(
                'type'        => 'B'
            ),
            'activity'                => array(
                'type'        => 'B'
            ),
            'membership'            => array(),
            'pending_membership'    => array(),
            'tax_number'            => array(),
            'tax_exempt'            => array(
                'type'        => 'B'
            ),
            'last_login'            => array(
                'type'        => 'D'
            ),
            'first_login'            => array(
                'type'        => 'D'
            ),
            'suspend_date'            => array(
                'type'        => 'D'
            ),
            'change_password_date'    => array(
                'type'        => 'D'
            )
        )
    );
}
elseif ($import_step == 'process_row') {

    /**
     * PROCESS ROW from import file
     */

    // Check ID
    if (
        !isset($values['id'])
        || !is_numeric($values['id'])
        || abs(intval($values['id'])) != $values['id']
    ) {
        $values['id'] = '';
    }

    if (
        !isset($values['login'])
        || empty($values['login'])
    ) {
        $values['login'] = $values['email'];
        $values['username'] = $values['email'];
    }
    elseif ($config['email_as_login'] == 'Y') {
        $values['username'] = $values['login'];
        $values['login'] = $values['email'];
    }

    $values['login'] = preg_replace(
        '/' . func_login_validation_regexp(true) . '/s',
        '',
        $values['login']
    );

    if (
        (
            isset($values['id'])
            && $values['id'] == $logged_userid
        )
        || (
            $values['login'] == $login
            && $values['usertype'] == $login_type
        )
    ) {
        return false;
    }

    $tmp = func_import_get_cache('UI', $values['id']);
    if (is_null($tmp)) {
        func_import_save_cache('UI', $values['id'], '');
    }

    // Check login

    $values['login'] = preg_replace(
        '/' . func_login_validation_regexp(true) . '/s',
        '',
        $values['login']
    );

    if ($logged_userid == $values['id']) {
        return false;
    }

    $tmp = func_import_get_cache('UL' . $values['usertype'], $values['login']);
    if (is_null($tmp)) {
        func_import_save_cache('UL' . $values['usertype'], $values['login']);
        if ($values['usertype'] == 'P') {
            func_import_save_cache('P', $values['login']);
        }
    }

    // Check parent
    if (
        !empty($values['parent'])
        && is_numeric($values['parent'])
    ) {
        $_parent = func_import_get_cache('UI', $values['parent']);
        if (is_null($_parent)) {
            $_parent = func_query_first_cell("SELECT id FROM $sql_tbl[customers] WHERE id = '$values[parent]'");
            if (empty($_parent)) {
                $_parent = NULL;
            }
            else {
                func_import_save_cache('UI', $values['parent'], $_parent);
            }
        }

        if (
            is_null($_parent)
            || (
                $action == 'do'
                && empty($_parent)
            )
        ) {
            func_import_module_error(
                'msg_err_import_log_message_29',
                array(
                    'login' => $values['parent']
                )
            );
            return false;
        }
    }

    // Check membership
    $values['membershipid'] = false;
    if (!empty($values['membership'])) {
        $_membershipid = func_import_get_cache('M', $values['membership']);
        if (empty($_membershipid)) {
            $_membershipid = func_detect_membership($values['membership'], $values['usertype']);
            if ($_membershipid == 0) {
                // Membership is specified but does not exist
                func_import_module_error(
                    'msg_err_import_log_message_5',
                    array(
                        'membership' => $values['membership']
                    )
                );
                return false;
            }
            else {
                func_import_save_cache('M', $values['membership'], $_membershipid);
            }
        }

        if (!empty($_membershipid))
            $values['membershipid'] = $_membershipid;
    }

    // Check pending membership
    $values['pending_membershipid'] = false;
    if (!empty($values['pending_membership'])) {
        $_membershipid = func_import_get_cache('M', $values['pending_membership']);
        if (empty($_membershipid)) {
            $_membershipid = func_detect_membership($values['pending_membership'], $values['usertype']);
            if ($_membershipid == 0) {
                // Membership is specified but does not exist
                func_import_module_error(
                    'msg_err_import_log_message_5',
                    array(
                        'membership' => $values['pending_membership']
                    )
                );
                return false;
            }
            else {
                func_import_save_cache('M', $values['pending_membership'], $_membershipid);
            }
        }

        if (!empty($_membershipid))
            $values['pending_membershipid'] = $_membershipid;
    }

    // Check email
    if (!empty($values['email'])) {
        if (!func_check_email($values['email'])) {
            func_import_module_error(
                'msg_err_import_log_message_28',
                array(
                    'email' => $values['email']
                )
            );
            return false;
        }
    }

    // Check title
    foreach (array('title','s_title','b_title') as $k) {
        if (empty($values[$k])) continue;

        if (func_detect_title($values[$k]) === false) {
            func_import_module_error(
                'msg_err_import_log_message_30',
                array(
                    'title' => $values[$k]
                )
            );

            return false;
        }
    }

    $data_row[] = $values;
}
elseif ($import_step == 'finalize') {

    /**
     * FINALIZE rows processing: update database
     */

    // Drop old data
    if ($import_file['drop'][strtolower($section)] == 'Y') {

        $users = db_query("SELECT id, login, usertype FROM $sql_tbl[customers] WHERE id <> '$logged_userid'");
        if (!empty($users)) {
            while ($user = db_fetch_array($users)) {
                func_delete_profile($user['id'], $user['usertype'], false, false);
            }
        }

        $import_file['drop'][strtolower($section)] = "";
    }

    // Import users
    foreach ($data_row as $row) {

        if (
            (
                isset($row['id'])
                && $row['id'] == $logged_userid
            )
            || (
                $row['login'] == $login
                && $row['usertype'] == $login_type
            )
        ) {
            continue;
        }

        func_unset($row, 'membership', 'pending_membership');
        if ($import_file['crypt_password'] != 'Y') {
            $row['password'] = text_crypt($row['password']);
        }
        $data = func_addslashes($row);

        $_userid = null;
        if (
            isset($data['id'])
            && !empty($data['id'])
        ) {
            $_userid = $data['id'];
        }
        else {
            $_userid = func_query_first_cell("SELECT id FROM $sql_tbl[customers] WHERE login = '" . addslashes($data['login']) . "' AND usertype='$data[usertype]'");
        }

        if ($_userid) {
            $_uexists = func_query_first_cell("SELECT COUNT(id) FROM $sql_tbl[customers] WHERE id = '$data[id]'") > 0;
        }

        if ($_userid && $_uexists) {

            // Update user by ID

            func_unset($data['id']);
            func_array2update(
                'customers',
                $data,
                "id='$_userid'"
            );
            $result[strtolower($section)]['updated']++;

        }
        else {

            // Add user
            $_userid = func_array2insert(
                'customers',
                $data,
                true
            );
            $result[strtolower($section)]['added']++;
        }

        func_import_save_cache('UI', $_userid, $_userid);

        if ($data['usertype'] == 'P') {
            if (!$single_mode) {
                func_mkdir(func_get_files_location($_userid, $data['usertype']));
            }
            
            func_import_save_cache('P', $_userid, $_userid);
        }

        func_flush('. ');
    }
}
elseif ($import_step == 'export') {

    /**
     * Export data
     */

    while (($id = func_export_get_row($data)) !== false) {

        if (empty($id)) {
            continue;
        }

        // Get data

        $row = func_query_first("SELECT $sql_tbl[customers].*, m.membership FROM $sql_tbl[customers] LEFT JOIN $sql_tbl[memberships] as m ON m.membershipid = $sql_tbl[customers].membershipid WHERE $sql_tbl[customers].id = '$id'");

        if (empty($row)) {
            continue;
        }

        $row['pending_membership'] = func_query_first_cell("SELECT membership FROM $sql_tbl[memberships] WHERE membershipid = '$row[pending_membershipid]'");

        func_unset($row, 'membershipid', 'pending_membershipid');

        // Time zone offset correction
        $row['last_login']  += $config['Appearance']['timezone_offset'];
        $row['first_login'] += $config['Appearance']['timezone_offset'];

        // Write row
        if (!func_export_write_row($row)) {
            break;
        }
    }
}

?>
