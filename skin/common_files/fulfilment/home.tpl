{*
$Id: home.tpl,v 1.2 2010/07/27 07:35:42 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{config_load file="$skin_config"}
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>{$lng.txt_site_title}</title>
{include file="meta.tpl"}
<link rel="stylesheet" type="text/css" href="{$SkinDir}/css/skin1_admin.css" />
</head>
<body{$reading_direction_tag}{if $login eq ""} class="not-logged-in"{/if}>
{include file="rectangle_top.tpl"}
{include file="head_admin.tpl"}
{if $login ne ""}
{include file="fulfilment/menu_box.tpl"}
{/if}
<!-- main area -->
<table width="100%" cellpadding="0" cellspacing="0" align="center">
<tr>
<td valign="top" class="central-space{if $dialog_tools_data}-dtools{/if}">
<!-- central space -->
{include file="location.tpl"}

{if $main eq "authentication"}

{include file="main/authentication.tpl" login_title=$lng.lbl_admin_login_title}

{elseif $smarty.get.mode eq "subscribed"}
{include file="main/subscribe_confirmation.tpl"}

{elseif $main eq "ups_import"}
{include file="modules/Order_Tracking/ups_import.tpl"}

{elseif $main eq "order_edit"}
{include file="modules/Advanced_Order_Management/order_edit.tpl"}

{elseif $main eq "statistics"}
{include file="admin/main/statistics.tpl"}

{elseif $smarty.get.mode eq "unsubscribed"}
{include file="main/unsubscribe_confirmation.tpl"}

{elseif $main eq "home" and $login ne ""}
{include file="main/orders.tpl"}

{elseif $main eq "slg"}
{include file="modules/Shipping_Label_Generator/generator.tpl"}

{elseif $main eq "register"}
{include file="admin/main/register.tpl"}

{elseif $main eq "change_mpassword"}
{include file="admin/main/change_mpassword.tpl"}

{elseif $main eq "change_password"}
{include file="main/change_password.tpl"}

{elseif $main eq "import_export"}
{include file="main/import_export.tpl"}

{else}
{include file="common_templates.tpl"}
{/if}

<!-- /central space -->
&nbsp;
</td>

<td valign="top">
  {include file="dialog_tools.tpl"}
</td>

</tr>
</table>
{include file="rectangle_bottom.tpl"}
</body>
</html>
