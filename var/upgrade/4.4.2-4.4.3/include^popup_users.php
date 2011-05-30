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
 * Popup users library
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage Lib
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @copyright  Copyright (c) 2001-2011 Ruslan R. Fazlyev <rrf@x-cart.com>
 * @license    http://www.x-cart.com/license.php X-Cart license agreement
 * @version    $Id: popup_users.php,v 1.20.2.1 2011/01/10 13:11:50 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

if (empty($form)) {
    func_close_window();
}

x_session_register('popup_search_users_formats');
x_session_register('search_data');

if (isset($_GET['format']))
    $popup_search_users_formats[$form] = $_GET['format'];

$format = empty($popup_search_users_formats[$form]) ? "~~firstname~~ ~~lastname~~ (~~email~~)" : $popup_search_users_formats[$form];

$advanced_options = array('usertype', 'membershipid', 'registration_type', 'address_type', 'phone', 'url', 'registration_date', 'last_login_date', 'suspended_by_admin', 'auto_suspended');

function func_set_saved_users($users)
{
    global $format, $form, $force_submit;

    $useids = array();
    foreach ($users as $u) {
        $useids[] = strtr($u['id'], array('\\'=>'\\\\',"'"=>"\\'",'"'=>'\\"',"\r"=>'\\r',"\n"=>'\\n','</'=>'<\/'));
    }
    $useids = implode(";", $useids);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<body>
<script type="text/javascript">
<!--
if (window.opener && window.opener.document.<?php echo $form; ?> && window.opener.document.<?php echo $form; ?>.userids) {

    var f = window.opener.document.<?php echo $form; ?>;
    f.userids.value = '<?php echo $useids; ?>';

    if (f.users) {
        var isSelect = (f.users.tagName.toUpperCase() == 'SELECT');
        if (isSelect) {
            while (f.users.options.length > 0)
                f.users.options[0] = null;
        } else {
            f.users.value = '';
        }

        var i = 0;
        with (f.users) {
<?php
    foreach ($users as $u) {
        $str = $format;
        foreach ($u as $fn => $fv) {
            $str = str_replace("~~".$fn."~~", $fv, $str);
        }
        $str = strtr($str, array('\\'=>'\\\\',"'"=>"\\'",'"'=>'\\"',"\r"=>'\\r',"\n"=>'\\n','</'=>'<\/'));
        $l = strtr($u['id'], array('\\'=>'\\\\',"'"=>"\\'",'"'=>'\\"',"\r"=>'\\r',"\n"=>'\\n','</'=>'<\/'));
?>
            if (isSelect)
                options[i++] = new Option('<?php echo $str; ?>', '<?php echo $l; ?>');
            else
                value += (value.length == 0 ? '' : "\n") + "<?php echo $str; ?>";
<?php
    }

?>
        }
    }

    if (window.opener.document.getElementById('<?php echo $form; ?>_users_count'))
        window.opener.document.getElementById('<?php echo $form; ?>_users_count').innerHTML = '<?php echo count($users); ?>';

<?php
if ($force_submit) {
?>
    f.submit();
<?php
}
?>
}
-->
</script>
</body>
</html>
<?php
}

if ($mode == 'save') {

    // Save selected users
    if (!empty($user)) {
        $users = func_query("SELECT * FROM $sql_tbl[customers] WHERE id IN ('".implode("','", array_keys($user))."')");
        func_set_saved_users($users);
        if ($force_submit)
            func_close_window();
    }

    func_close_window();

} elseif ($REQUEST_METHOD == 'POST') {

    /**
     * Update the session $search_data variable from $posted_data
     */
    if (!empty($posted_data)) {

        $need_advanced_options = false;
        foreach ($posted_data as $k=>$v) {
            if (!is_array($v) && !is_numeric($v))
                $posted_data[$k] = stripslashes($v);
            if (in_array($k, $advanced_options) && !empty($v))
                $need_advanced_options = true;
        }

        $posted_data['need_advanced_options'] = $need_advanced_options;

        if (empty($search_data['users']['sort_field'])) {
            $posted_data['sort_field'] = 'last_login';
            $posted_data['sort_direction'] = 1;
        }
        else {
            $posted_data['sort_field'] = $search_data['users']['sort_field'];
            $posted_data['sort_direction'] = $search_data['users']['sort_direction'];
        }

        if ($start_date) {
            $posted_data['start_date'] = func_prepare_search_date($start_date);
            $posted_data['end_date']   = func_prepare_search_date($end_date, true);
        }

        if (!empty($posted_data['membershipid'])) {
            list($posted_data['usertype'], $posted_data['membershipid']) = explode("-", $posted_data['membershipid']);
        }

        $search_data['users'] = $posted_data;

    }

    func_header_location("popup_users.php?mode=search&form=".$form."&force_submit=".$force_submit);
}

if ($mode == 'search') {
/**
 * Perform search and display results
 */

    $data = array();

/**
 * Prepare the search data
 */
    if (!empty($sort) && in_array($sort, array('username','name','email','usertype','last_login'))) {
        $search_data['users']['sort_field'] = $sort;
        $search_data['users']['sort_direction'] = abs(intval($search_data['users']['sort_direction']) - 1);
        $flag_save = true;
    }

    if (!empty($page) && $search_data['users']['page'] != intval($page)) {
        // Store the current page number in the session
        $search_data['users']['page'] = $page;
        $flag_save = true;
    }

    if ($flag_save)
        x_session_save('search_data');

    if (is_array($search_data['users'])) {
        $data = $search_data['users'];
        foreach ($data as $k=>$v)
            if (!is_array($v) && !is_numeric($v))
                $data[$k] = addslashes($v);
    }

    $data['_objects_per_page'] = $config["Appearance"]["users_per_page_admin"];
    $data['usertype'] = 'C';

    if ($save_all) {
        $data['_objects_per_page'] = 0;
    }

    include $xcart_dir.'/include/search_users.php';

    if ($data['save'] || $save_all) {
        // Save all found users

        if (!empty($users))
            func_set_saved_users($users);

        func_close_window();
    }

    if (!empty($users)) {
        // Assign the Smarty variables
        $smarty->assign('navigation_script', "popup_users.php?mode=search&form=".$form."&force_submit=".$force_submit);
        $smarty->assign('users', $users);
        $smarty->assign('first_item', $first_page+1);
        $smarty->assign('last_item', min($first_page+$objects_per_page, $total_items));
    }

    $smarty->assign('total_items', $total_items);
    $smarty->assign('mode', $mode);

}

if (empty($users)) {
/**
 * Get the states and countries list for search form
 */
    include $xcart_dir.'/include/states.php';
    include $xcart_dir.'/include/countries.php';
}

$smarty->assign('usertypes',$usertypes);

$smarty->assign('search_prefilled', $search_data['users']);

$memberships = array('C' => array());
if (!empty($active_modules['XAffiliate'])) {
    $memberships['B'] = array();
}
$tmp = func_query("SELECT $sql_tbl[memberships].area, $sql_tbl[memberships].membershipid, IFNULL($sql_tbl[memberships_lng].membership, $sql_tbl[memberships].membership) as membership FROM $sql_tbl[memberships] LEFT JOIN $sql_tbl[memberships_lng] ON $sql_tbl[memberships].membershipid = $sql_tbl[memberships_lng].membershipid AND $sql_tbl[memberships_lng].code = '$shop_language' WHERE $sql_tbl[memberships].active = 'Y' AND $sql_tbl[memberships].area IN ('".implode("','", array_keys($memberships))."') ORDER BY IF(FIELD($sql_tbl[memberships].area, 'A','P','C','B') > 0, FIELD($sql_tbl[memberships].area, 'A','P','C','B'), 100), $sql_tbl[memberships].orderby");
if (!empty($tmp)) {
    foreach ($tmp as $v) {
        $memberships[$v['area']][] = $v;
    }
}
$smarty->assign('memberships', $memberships);

$memberships_lbls = array();
foreach ($memberships as $k => $v) {
    $memberships_lbls[$k] = func_get_langvar_by_name('lbl_'.$k.'_usertype');
}
$smarty->assign('memberships_lbls', $memberships_lbls);

$smarty->assign('form', $form);
$smarty->assign('force_submit', $force_submit);
?>
