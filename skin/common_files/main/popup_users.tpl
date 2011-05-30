{*
$Id: popup_users.tpl,v 1.4.2.2 2011/04/27 10:37:10 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{config_load file="$skin_config"}
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>{$lng.lbl_search_users|wm_remove|escape}</title>
{include file="meta.tpl"}
  <link rel="stylesheet" type="text/css" href="{$SkinDir}/css/skin1_admin.css" />
{include file="presets_js.tpl"}
<script type="text/javascript" src="{$SkinDir}/js/common.js"></script>
</head>
<body{$reading_direction_tag}>
<table class="Container" cellpadding="0" cellspacing="0" width="{$width|default:"100%"}" align="center">
<tr>
  <td class="PopupTitle">{$lng.lbl_search_users}</td>
</tr>
<tr>
  <td height="1"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>
<tr>
  <td class="PopupBG" height="1"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>
<tr>
  <td class="Container">
<div style="padding: 5px;">
{if $mode eq "" or $users eq ""}

<script type="text/javascript" src="{$SkinDir}/js/reset.js"></script>
<script type="text/javascript">
//<![CDATA[
var searchform_def = [];
searchform_def[0] = ['posted_data[by_username]', true];
searchform_def[1] = ['posted_data[by_firstname]', true];
searchform_def[2] = ['posted_data[by_lastname]', true];
searchform_def[3] = ['posted_data[by_email]', true];
searchform_def[4] = ['posted_data[by_company]', true];
searchform_def[5] = ['posted_data[by_username]', true];
searchform_def[6] = ['posted_data[by_username]', true];
searchform_def[7] = ['posted_data[by_username]', true];
searchform_def[8] = ['f_start_date', '{$search_prefilled.start_date|default:$smarty.now|date_format:$config.Appearance.date_format}'];
searchform_def[9] = ['f_end_date', '{$search_prefilled.end_date|default:$smarty.now|date_format:$config.Appearance.date_format}'];
//]]>
</script>

<!-- SEARCH FORM START -->

<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
var date_selected = '{if $search_prefilled.date_period eq "" or $search_prefilled.date_period eq "M"}M{else}{$search_prefilled.date_period}{/if}';
{literal}
function managedate(type, status) {

  if (type == 'address')
    var fields = new Array('posted_data[city]','posted_data[state]','posted_data[country]','posted_data[zipcode]', 'posted_data[phone]');
  else if (type == 'date')
    var fields = new Array('f_start_date', 'f_end_date');
  else if (type == 'date_type') {
    status = document.searchform.elements['posted_data[registration_date]'].checked + document.searchform.elements['posted_data[last_login_date]'].checked + document.searchform.elements['posted_data[suspended_by_admin]'].checked + document.searchform.elements['posted_data[auto_suspended]'].checked;
    status = !(status != 0);
  
    for (var i = 0; i < document.searchform.elements.length; i++)
      if (document.searchform.elements[i].name == 'posted_data[date_period]') {
        if (status) {
          $('[name="posted_data[date_period]"]').attr("disabled", true).addClass( 'ui-state-disabled' );
        } else {
          $('[name="posted_data[date_period]"]').attr("disabled", false).removeClass( 'ui-state-disabled' );
        }
      }
  
    disable_dates = false;
    
    if (status)
      disable_dates = true;
    else if (date_selected != 'C')
      disable_dates = true;
    
    managedate('date', disable_dates);
    return true;

  }
  
  for (var i in fields) {
    if (status) {
      $('[name="' + fields[i] + '"]').attr("disabled", true).addClass( 'ui-state-disabled' );
    } else {
      $('[name="' + fields[i] + '"]').attr("disabled", false).removeClass( 'ui-state-disabled' );
    }
  }
}
{/literal}
//]]>
</script>

<br />

<form name="searchform" action="popup_users.php" method="post">
<input type="hidden" name="form" value="{$form|escape}" />
<input type="hidden" name="force_submit" value="{$force_submit|escape}" />

<table cellpadding="0" cellspacing="0" width="100%">

<tr>
  <td>

<table cellpadding="4" cellspacing="0" width="100%">

<tr>
  <td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_search_for_pattern}:</td>
  <td width="10" height="10"><font class="CustomerMessage"></font></td>
  <td height="10" width="80%">
  <input type="text" name="posted_data[substring]" size="30" style="width:70%" value="{$search_prefilled.substring|escape}" />
  &nbsp;
  <input type="submit" value="{$lng.lbl_search|strip_tags:false|escape}" />
  </td>
</tr>

<tr>
  <td height="10" class="FormButton">{$lng.lbl_search_in}:</td>
  <td>&nbsp;</td>
  <td>
<table cellpadding="0" cellspacing="0">
<tr>
  <td width="5"><input type="checkbox" id="posted_data_by_username" name="posted_data[by_username]"{if $search_prefilled eq "" or $search_prefilled.by_username} checked="checked"{/if} /></td>
  <td class="OptionLabel"><label for="posted_data_by_username">{$lng.lbl_username}</label></td>

  <td width="5"><input type="checkbox" id="posted_data_by_firstname" name="posted_data[by_firstname]"{if $search_prefilled eq "" or $search_prefilled.by_firstname} checked="checked"{/if} /></td>
  <td class="OptionLabel"><label for="posted_data_by_firstname">{$lng.lbl_first_name}</label></td>

  <td width="5"><input type="checkbox" id="posted_data_by_lastname" name="posted_data[by_lastname]"{if $search_prefilled eq "" or $search_prefilled.by_lastname} checked="checked"{/if} /></td>
  <td class="OptionLabel"><label for="posted_data_by_lastname">{$lng.lbl_last_name}</label></td>

  <td width="5"><input type="checkbox" id="posted_data_by_email" name="posted_data[by_email]"{if $search_prefilled eq "" or $search_prefilled.by_email} checked="checked"{/if} /></td>
  <td class="OptionLabel"><label for="posted_data_by_email">{$lng.lbl_email}</label></td>

  <td width="5"><input type="checkbox" id="posted_data_by_company" name="posted_data[by_company]"{if $search_prefilled eq "" or $search_prefilled.by_company} checked="checked"{/if} /></td>
  <td class="OptionLabel"><label for="posted_data_by_company">{$lng.lbl_company}</label></td>
</tr>
</table>
  </td>
</tr>

<tr>
  <td colspan="2"></td>
  <td>
<table cellpadding="0" cellspacing="0">
<tr>
  <td><input type="checkbox" id="posted_data_save" name="posted_data[save]" value="Y" /></td>
  <td>&nbsp;</td>
  <td class="FormButton" nowrap="nowrap"><label for="posted_data_save">{$lng.lbl_save_search_results}</label></td>
</tr>
</table> 
  </td>
</tr>

</table>

<br />

{include file="main/visiblebox_link.tpl" mark="1" title=$lng.lbl_advanced_search_options}

<br />

<table cellpadding="4" cellspacing="0" width="100%" style="display: none;" id="box1">

<tr>
  <td colspan="3"><br />{include file="main/subheader.tpl" title=$lng.lbl_advanced_search_options}</td>
</tr>

<tr class="TableSubHead">
  <td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_search_for_user_type}:</td>
  <td width="10" height="10"><font class="CustomerMessage"></font></td>
  <td height="10">
  <select name="posted_data[membershipid]">
    <option value="">{$lng.lbl_all}</option>
{if $config.General.membership_signup eq "Y"}
    <option value="-pending_membership"{if $search_prefilled.usertype eq "" and $search_prefilled.membershipid eq "pending_membership"} selected="selected" {/if}>{$lng.lbl_pending_membership}</option>
{/if}
{foreach from=$memberships item=lvls key=k}
    <option value="{$k}-"{if $search_prefilled.usertype eq $k and $search_prefilled.membershipid eq ''} selected="selected"{/if}>{$memberships_lbls.$k}</option>
{if $config.General.membership_signup eq "Y" and $lvls ne ''}
    <option value="{$k}-pending_membership"{if $search_prefilled.usertype eq $k and $search_prefilled.membershipid eq "pending_membership"} selected="selected" {/if}>&nbsp;&nbsp;&nbsp;{$lng.lbl_pending_membership|wm_remove|escape}</option>
{/if}
{foreach from=$lvls item=v}
    <option value="{$k}-{$v.membershipid}"{if $search_prefilled.usertype eq $k and $search_prefilled.membershipid eq $v.membershipid} selected="selected"{/if}>&nbsp;&nbsp;&nbsp;{$v.membership}</option>
{/foreach}
{/foreach}
  </select>
  </td>
</tr>

<tr>
  <td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_search_by_address}:</td>
  <td width="10" height="10"><font class="CustomerMessage"></font></td>
  <td>
<table cellpadding="0" cellspacing="0">
<tr>
  <td width="5"><input type="radio" id="address_type_null" name="posted_data[address_type]" value=""{if $search_prefilled eq "" or $search_prefilled.address_type eq ""} checked="checked"{/if} onclick="javascript:managedate('address',true)" /></td>
  <td class="OptionLabel"><label for="address_type_null">{$lng.lbl_ignore_address}</label></td>

  <td width="5"><input type="radio" id="address_type_B" name="posted_data[address_type]" value="B"{if $search_prefilled.address_type eq "B"} checked="checked"{/if} onclick="javascript:managedate('address',false)" /></td>
  <td class="OptionLabel"><label for="address_type_B">{$lng.lbl_billing}</label></td>

  <td width="5"><input type="radio" id="address_type_S" name="posted_data[address_type]" value="S"{if $search_prefilled.address_type eq "S"} checked="checked"{/if} onclick="javascript:managedate('address',false)" /></td>
  <td class="OptionLabel"><label for="address_type_S">{$lng.lbl_shipping}</label></td>

  <td width="5"><input type="radio" id="address_type_all" name="posted_data[address_type]" value="All"{if $search_prefilled.address_type eq "All"} checked="checked"{/if} onclick="javascript:managedate('address',false)" /></td>
  <td class="OptionLabel"><label for="address_type_all">{$lng.lbl_all}</label></td>
</tr>
</table>
  </td>
</tr>

<tr>
  <td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_city}:</td>
  <td width="10" height="10"><font class="CustomerMessage"></font></td>
  <td height="10" width="80%">
  <input type="text" maxlength="64" name="posted_data[city]" value="{$search_prefilled.city|escape}" style="width:70%" />
  </td>
</tr>

<tr>
  <td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_state}:</td>
  <td width="10" height="10"><font class="CustomerMessage"></font></td>
  <td height="10" width="80%">
  {include file="main/states.tpl" states=$states name="posted_data[state]" default=$search_prefilled.state required="N" style="style='width:70%'"}
  </td>
</tr>

<tr>
  <td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_country}:</td>
  <td width="10" height="10"><font class="CustomerMessage"></font></td>
  <td height="10" width="80%">
  <select name="posted_data[country]" style="width:70%">
    <option value="">[{$lng.lbl_please_select_one|wm_remove|escape}]</option>
{section name=country_idx loop=$countries}
    <option value="{$countries[country_idx].country_code}"{if $search_prefilled.country eq $countries[country_idx].country_code} selected="selected"{/if}>{$countries[country_idx].country}</option>
{/section}
  </select>
  </td>
</tr>

<tr>
  <td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_zip_code}:</td>
  <td width="10" height="10"><font class="CustomerMessage"></font></td>
  <td height="10" width="80%">
  <input type="text" maxlength="16" name="posted_data[zipcode]" value="{$search_prefilled.zipcode|escape}" style="width:70%" />
  </td>
</tr>

<tr>
  <td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_phone}/{$lng.lbl_fax}:</td>
  <td width="10" height="10"><font class="CustomerMessage"></font></td>
  <td height="10" width="80%">
  <input type="text" maxlength="25" name="posted_data[phone]" value="{$search_prefilled.phone|escape}" style="width:70%" />
  </td>
</tr>

<tr>
  <td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_web_site}:</td>
  <td width="10" height="10"><font class="CustomerMessage"></font></td>
  <td height="10" width="80%">
  <input type="text" maxlength="128" name="posted_data[url]" value="{$search_prefilled.url|escape}" style="width:70%" />
  </td>
</tr>

<tr>
  <td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_orders}:</td>
  <td width="10" height="10"><font class="CustomerMessage"></font></td>
  <td height="10" width="80%">
  <table cellpadding="0" cellspacing="0"><tr>
  <td><input type="text" maxlength="18" name="posted_data[orders_min]" value="{$search_prefilled.orders_min|escape}" /></td>
  <td> - </td>
  <td><input type="text" maxlength="18" name="posted_data[orders_max]" value="{$search_prefilled.orders_max|escape}" /></td>
  </tr></table>
  </td>
</tr>

<tr class="TableSubHead">
  <td height="10" class="FormButton">{$lng.lbl_search_for_users_that_is}:</td>
  <td height="10"></td>
  <td height="10">
<table cellpadding="0" cellspacing="0">
<tr>
  <td width="5"><input type="checkbox" id="posted_data_registration_date" name="posted_data[registration_date]" value="Y"{if $search_prefilled.registration_date ne ""} checked="checked"{/if} onclick="javascript: managedate('date_type')" /></td>
  <td class="OptionLabel"><label for="posted_data_registration_date">{$lng.lbl_registered}</label></td>

  <td width="5"><input type="checkbox" id="posted_data_last_login_date" name="posted_data[last_login_date]" value="Y"{if $search_prefilled.last_login_date ne ""} checked="checked"{/if} onclick="javascript:managedate('date_type')" /></td>
  <td class="OptionLabel"><label for="posted_data_last_login_date">{$lng.lbl_last_logged_in}</label></td>

  <td width="5"><input type="checkbox" id="posted_data_suspended_by_admin" name="posted_data[suspended_by_admin]" value="Y"{if $search_prefilled.suspended_by_admin ne ""} checked="checked"{/if} onclick="javascript:managedate('date_type')" /></td>
  <td class="OptionLabel"><label for="posted_data_suspended_by_admin">{$lng.lbl_suspended_by_admin}</label></td>

  <td width="5"><input type="checkbox" id="posted_data_auto_suspended" name="posted_data[auto_suspended]" value="Y"{if $search_prefilled.auto_suspended ne ""} checked="checked"{/if} onclick="javascript:managedate('date_type')" /></td>
  <td class="OptionLabel" width="100%">
    {include file="main/tooltip_js.tpl" title=$lng.lbl_suspended_automatically text=$lng.txt_help_auto_suspended type=label idfor="posted_data_auto_suspended"}
  </td>
</tr>
</table>
  </td>
</tr>

<tr class="TableSubHead">
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_during_date_period}:</td>
  <td width="10">&nbsp;</td>
  <td>
<table cellpadding="0" cellspacing="0">
<tr>
  <td width="5"><input type="radio" id="date_period_M" name="posted_data[date_period]" value="M"{if $search_prefilled.date_period eq "" or $search_prefilled.date_period eq "M"} checked="checked"{/if} onclick="javascript:date_selected='M';managedate('date',true)" /></td>
  <td class="OptionLabel"><label for="date_period_M">{$lng.lbl_this_month}</label></td>

  <td width="5"><input type="radio" id="date_period_W" name="posted_data[date_period]" value="W"{if $search_prefilled.date_period eq "W"} checked="checked"{/if} onclick="javascript:date_selected='W';managedate('date',true)" /></td>
  <td class="OptionLabel"><label for="date_period_W">{$lng.lbl_this_week}</label></td>

  <td width="5"><input type="radio" id="date_period_D" name="posted_data[date_period]" value="D"{if $search_prefilled.date_period eq "D"} checked="checked"{/if} onclick="javascript:date_selected='D';managedate('date',true)" /></td>
  <td class="OptionLabel"><label for="date_period_D">{$lng.lbl_today}</label></td>
</tr>
<tr>
  <td width="5"><input type="radio" id="date_period_C" name="posted_data[date_period]" value="C"{if $search_prefilled.date_period eq "C"} checked="checked"{/if} onclick="javascript:date_selected='C';managedate('date',false)" /></td>
  <td colspan="7" class="OptionLabel"><label for="date_period_C">{$lng.lbl_specify_period_below}</label></td>
</tr>
</table>
  </td>
</tr>

<tr class="TableSubHead">
  <td class="FormButton" align="right" nowrap="nowrap">{$lng.lbl_from}:</td>
  <td width="10">&nbsp;</td>
  <td>
    {include file="main/datepicker.tpl" name="start_date" date=$search_prefilled.start_date}
  </td>
</tr>

<tr class="TableSubHead">
  <td class="FormButton" align="right" nowrap="nowrap">{$lng.lbl_through}:</td>
  <td width="10">&nbsp;</td>
  <td>
    {include file="main/datepicker.tpl" name="end_date" date=$search_prefilled.end_date}
  </td>
</tr>

<tr>
  <td colspan="3"><br />
{$lng.txt_users_search_note}
<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
{if $search_prefilled eq "" or $search_prefilled.address_type eq ""}
managedate('address',true);
{/if}
managedate('date_type');
{if ($search_prefilled.registration_date ne "" or $search_prefilled.last_login_date ne "" or $search_prefilled.suspended_by_admin ne "" or $search_prefilled.auto_suspended  ne "") and $search_prefilled.date_period ne "C"}
managedate('date', true);
{/if}
//]]>
</script>
  </td>
</tr>

<tr>
  <td colspan="2">&nbsp;</td>
  <td>
  <input type="submit" value="{$lng.lbl_search|strip_tags:false|escape}" />
  &nbsp;&nbsp;&nbsp;
  <input type="button" value="{$lng.lbl_reset|strip_tags:false|escape}" onclick="javascript: reset_form('searchform', searchform_def);" /></td>
</tr>

</table>

  </td>
</tr>

</table>
</form>

{if $search_prefilled.need_advanced_options}
<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
visibleBox('1');
//]]>
</script>
{/if}

<br />

<!-- SEARCH FORM DIALOG END -->

{/if}

<!-- SEARCH RESULTS SUMMARY -->

<a name="results"></a>

{if $mode eq "search"}
{if $total_items gt "0"}
{$lng.txt_N_results_found|substitute:"items":$total_items}<br />
{$lng.txt_displaying_X_Y_results|substitute:"first_item":$first_item:"last_item":$last_item}
{else}
{$lng.txt_N_results_found|substitute:"items":0}
{/if}
{/if}

{if $mode eq "search" and $users ne ""}

<!-- SEARCH RESULTS START -->

<br /><br />

<div align="right">{include file="buttons/button.tpl" button_title=$lng.lbl_search_again href="popup_users.php?form=`$form`"}</div>

{if $total_pages lt 3}
<br />
{else}
{assign var="pagestr" value="&amp;page=`$navigation_page`"}
{/if}

{include file="main/navigation.tpl"}

{include file="main/check_all_row.tpl" style="line-height: 170%;" form="processuserform" prefix="user"}

<form action="popup_users.php" method="post" name="processuserform">
<input type="hidden" name="mode" value="" />
<input type="hidden" name="form" value="{$form|escape}" />
<input type="hidden" name="force_submit" value="{$force_submit|escape}" />

<table cellpadding="2" cellspacing="1" width="100%">

<tr class="TableHead">
  <td>&nbsp;</td>
  <td>{if $search_prefilled.sort_field eq "username"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="users.php?mode=search{$pagestr|amp}&amp;sort=username">{$lng.lbl_username}</a></td>
  <td>{if $search_prefilled.sort_field eq "name"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="users.php?mode=search{$pagestr|amp}&amp;sort=name">{$lng.lbl_name}</a> / {if $search_prefilled.sort_field eq "email"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="users.php?mode=search{$pagestr|amp}&amp;sort=email">{$lng.lbl_email}</a></td>
  <td>{if $search_prefilled.sort_field eq "usertype"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="users.php?mode=search{$pagestr|amp}&amp;sort=usertype">{$lng.lbl_usertype}</a></td>
  <td>{if $search_prefilled.sort_field eq "last_login"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="users.php?mode=search{$pagestr|amp}&amp;sort=last_login">{$lng.lbl_last_logged_in}</a></td>
</tr>

{section name=cat_num loop=$users}
{assign var="_usertype" value=$users[cat_num].usertype}
{if $_usertype eq "P" and $single_mode eq ""}
{assign var="products" value=$users[cat_num].products}
{else}
{assign var="products" value=""}
{/if}

<tr{cycle values=', class="TableSubHead"'}>
  <td width="5"><input type="checkbox" name="user[{$users[cat_num].id}]"{if $users[cat_num].id eq $logged_userid} disabled="disabled" class='ui-state-disabled'{/if} /></td>
  <td><a href="{$catalogs.admin}/user_modify.php?user={$users[cat_num].id}&amp;usertype={$users[cat_num].usertype}{$pagestr|amp}" title="{$lng.lbl_modify_profile|escape}" target="_blank">{$users[cat_num].login}</a></td>
  <td><a href="{$catalogs.admin}/user_modify.php?user={$users[cat_num].id}&amp;usertype={$users[cat_num].usertype}{$pagestr|amp}" title="{$lng.lbl_modify_profile|escape}" target="_blank"><font class="ItemsList">{$users[cat_num].firstname} {$users[cat_num].lastname}</font></a> / {$users[cat_num].email}</td>
  <td>
  <span title="{$users[cat_num].membership|default:$lng.lbl_no_membership}">{$usertypes.$_usertype}</span>
{if $_usertype eq 'B'}
<br /><font class="SmallText"><i>({if $users[cat_num].status eq 'Q'}{$lng.lbl_unapproved}{elseif $users[cat_num].status eq 'D'}{$lng.lbl_declined}{elseif $users[cat_num].status eq 'Y'}{$lng.lbl_approved}{else}{$lng.lbl_disabled}{/if})</i></font>
{elseif $users[cat_num].status ne 'Y' and $users[cat_num].status ne 'A'}
<br /><font class="SmallText"><i>({$lng.lbl_account_status_suspended})</i></font>
{/if}
{if $products ne ""} <span style="white-space: nowrap;">({$lng.txt_N_products|substitute:"products":$products})</span>{/if}
  </td>
  <td nowrap="nowrap">{if ($users[cat_num].last_login ne 0)}{$users[cat_num].last_login|date_format:$config.Appearance.datetime_format}{else}{$lng.lbl_never_logged_in}{/if}</td>
</tr>

{/section}

<tr>
  <td colspan="5" class="SubmitBox">
  <input type="button" value="{$lng.lbl_save_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('^user\\[.+\\]', 'gi'))) submitForm(this, 'save');" />
  <input type="button" value="{$lng.lbl_save_all|strip_tags:false|escape}" onclick="javascript: self.location='popup_users.php?mode=search&amp;save_all=Y&amp;form={$form|escape}&amp;force_submit={$force_submit}';" />
  </td>
</tr>

</table>
</form>

<br />

{include file="main/navigation.tpl"}

<!-- SEARCH RESULTS START -->

{/if}
</div>
  </td>
</tr>
<tr>
  <td valign="bottom">{include file="popup_bottom.tpl"}</td>
</tr>
</table>
</body>
</html>
