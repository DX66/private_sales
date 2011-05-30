{*
$Id: register.tpl,v 1.9.2.2 2010/09/21 12:13:24 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="check_email_script.tpl"}
{include file="check_password_script.tpl"}
{include file="check_zipcode_js.tpl"}
{include file="check_required_fields_js.tpl" fillerror=$reg_error}
{include file="change_states_js.tpl"}

{include file="check_registerform_fields_js.tpl"}

{if $newbie eq "Y"}
{if $login ne ""}
{assign var="title" value=$lng.lbl_account_details}
{else}
{assign var="title" value=$lng.lbl_create_profile}
{/if}
{else}
{if $main eq "user_add"}
{if $active_modules.Simple_Mode}
{assign var="title" value=$lng.lbl_create_admin_profile}
{else}
{assign var="title" value=$lng.lbl_create_provider_profile}
{/if}
{else}
{if $active_modules.Simple_Mode}
{assign var="title" value=$lng.lbl_modify_admin_profile}
{else}
{assign var="title" value=$lng.lbl_modify_provider_profile}
{/if}
{/if}
{/if}

{include file="page_title.tpl" title=$title}

<font class="Text">

{if $newbie ne "Y"}
<br />
{if $active_modules.Simple_Mode}
{if $main eq "user_add"}
{$lng.txt_create_admin_profile}
{else}
{$lng.txt_modify_admin_profile}
{/if}
{else}
{if $main eq "user_add"} 
{$lng.txt_create_provider_profile}
{else} 
{$lng.txt_modify_provider_profile}
{/if} 
{/if}
<br /><br />
{/if}

{$lng.txt_fields_are_mandatory}

</font>

<br /><br />

{include file="provider/main/profile_menu.tpl"}

{if $smarty.get.submode eq 'seller_address'}
{include file="provider/main/register_provider.tpl"}

{else}
{capture name=dialog}

{if $newbie ne "Y" and $main ne "user_add" and $is_admin_user}
<div align="right">{include file="buttons/button.tpl" button_title=$lng.lbl_go_to_users_list href="users.php?mode=search"}</div>
{/if}

{if $registered eq ""}

{if $reg_error}
<font class="Star">{$reg_error.errdesc}</font>
<br />
{/if}

<form action="{$register_script_name}?{$smarty.server.QUERY_STRING|amp}" method="post" name="registerform" onsubmit="javascript: return checkRegFormFields(this);" >
{if $config.Security.use_https_login eq "Y"}
<input type="hidden" name="{$XCARTSESSNAME}" value="{$XCARTSESSID}" />
{/if}

<table cellspacing="1" cellpadding="2" width="100%">

{include file="main/register_personal_info.tpl" userinfo=$userinfo}

{include file="main/register_address_book.tpl" addresses=$userinfo.addresses}

{include file="main/register_additional_info.tpl" section='A'}

{include file="main/register_account.tpl" userinfo=$userinfo}

{if $active_modules.News_Management and $newslists}
{include file="modules/News_Management/register_newslists.tpl" userinfo=$userinfo}
{/if}

{if $active_modules.Image_Verification and $show_antibot.on_registration eq 'Y' and $display_antibot}
  {include file="modules/Image_Verification/spambot_arrest.tpl" mode="data-table" id=$antibot_sections.on_registration antibot_err=$reg_antibot_err}
{/if}

<tr>
  <td colspan="2">
    <br />
    {if $newbie eq "Y" and $login ne ""}
      <a href="register.php?mode=delete" class="delete-profile-link">{$lng.lbl_delete}</a>
    {/if}
  </td>
  <td>

    <br />

    {if $smarty.get.mode eq "update"}
      <input type="hidden" name="mode" value="update" />
    {/if}
    <input type="submit" value=" {if $userinfo.id gt 0}{$lng.lbl_update|escape}{else}{$lng.lbl_register|escape}{/if} " />

  </td>
</tr>

</table>
<input type="hidden" name="usertype" value="{if $smarty.get.usertype ne ""}{$smarty.get.usertype|escape:"html"}{else}{$usertype}{/if}" />
</form>

<br /><br />

{$lng.txt_newbie_registration_bottom}

<br />

{else}

{if $smarty.post.mode eq "update" or $smarty.get.mode eq "update"}
{$lng.txt_profile_modified}
{else}
{$lng.txt_profile_created}
{/if}

{/if}

{/capture}
{include file="dialog.tpl" title=$lng.lbl_profile_details content=$smarty.capture.dialog extra='width="100%"'}

{if $userinfo.status eq "Q" and $usertype ne "P"}

<br />

{capture name=dialog}

<form action="{$register_script_name}?{$smarty.server.QUERY_STRING|amp}" method="post" name="decisionform">

  <div id="decision">
    <input type="radio" id="opt_approved" name="mode" value="approved" onclick="javascript: this.form.submit();" />
    <label for="opt_approved">
      {$lng.lbl_approve}
    </label>
    <input type="radio" id="opt_declined" name="mode" value="declined" onclick="javascript: $('#decline_reason').show();$('#apply_reason').show();" />
    <label for="opt_declined">
      {$lng.lbl_decline}
    </label>
  
  </div>

  <br />
  <textarea id="decline_reason" style="display:none" name="reason" cols="40" rows="5" onfocus="javascript:if (this.value == '{$lng.txt_decline_reason|wm_remove|escape:javascript}') this.value='';">{$lng.txt_decline_reason}</textarea>

<script type="text/javascript">
//<![CDATA[
  $(function() {ldelim}
    $("#decision").buttonset();
  {rdelim});
//]]>
</script>

  <br /><br />
  <input type="submit" id="apply_reason" style="display:none" value="{$lng.lbl_apply|strip_tags:false|escape}" />

</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_approve_or_decline_provider_profile content=$smarty.capture.dialog extra='width="100%"'}
{/if}

{/if}
