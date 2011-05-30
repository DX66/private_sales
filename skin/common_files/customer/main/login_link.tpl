{*
$Id: login_link.tpl,v 1.3 2010/06/28 13:29:39 igoryan Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
<a href="{$authform_url}" title="{$lng.lbl_sign_in|escape}" {if not (($smarty.cookies.robot eq 'X-Cart Catalog Generator' and $smarty.cookies.is_robot eq 'Y') or ($config.Security.use_https_login eq 'Y' and not $is_https_zone))} onclick="javascript: return !popupOpen('login.php');"{/if}{if $classname} class="{$classname|escape}"{/if}>{$lng.lbl_sign_in}</a>
