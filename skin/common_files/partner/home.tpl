{*
$Id: home.tpl,v 1.4 2010/07/27 07:35:42 igoryan Exp $
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
{include file="partner/menu_box.tpl"}
{/if}
<!-- main area -->
<table width="100%" cellpadding="0" cellspacing="0" align="center">
<tr>
<td valign="top" class="central-space{if $dialog_tools_data}-dtools{/if}">
<!-- central space -->
{include file="location.tpl"}

{if $main eq "authentication"}
{include file="main/authentication.tpl" login_title=$lng.lbl_partner_login_title is_register=$config.XAffiliate.partner_register}

{elseif $main eq "stats"}
{include file="partner/main/stats.tpl"}

{elseif $main eq "module_disabled"}
{include file="partner/main/module_disabled.tpl"}

{elseif $main eq "banner_info"}
{include file="partner/main/banner_info.tpl"}

{elseif $main eq "referred_sales"}
{include file="main/referred_sales.tpl"}

{elseif $main eq "register"}
{include file="partner/main/register.tpl"}

{elseif $main eq "payment_history"}
{include file="partner/main/payment_history.tpl"}

{elseif $main eq "affiliates"}
{include file="main/affiliates.tpl"}

{elseif $main eq "partner_banners"}
{include file="main/partner_banners.tpl"}

{elseif $main eq "products"}
{include file="main/affiliate_search_result.tpl"}

{elseif $main eq "product"}
{include file="partner/main/product.tpl"}

{elseif $main eq "home" and $login ne ""}
{include file="partner/main/promotions.tpl"}

{elseif $main eq "home" and $mode eq 'profile_created'}
{include file="partner/main/welcome_queued.tpl"}

{elseif $main eq "change_password"}
{include file="main/change_password.tpl"}

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
