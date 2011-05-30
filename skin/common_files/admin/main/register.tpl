{*
$Id: register.tpl,v 1.4.2.1 2011/03/03 10:53:08 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="check_email_script.tpl"}
{include file="check_password_script.tpl"}
{include file="check_zipcode_js.tpl"}
{include file="check_required_fields_js.tpl" fillerror=$reg_error}
{include file="change_states_js.tpl"}
{include file="check_registerform_fields_js.tpl"}

{if $newbie eq "Y"}
  {assign var="title" value=$lng.lbl_account_details}
{else}
  {if $main eq "user_add"}

    {if $smarty.get.usertype eq 'C'}
      {assign var="title" value=$lng.lbl_create_customer_profile}
    {else}
      {assign var="title" value=$lng.lbl_create_admin_profile}
    {/if}

  {else}

    {if $smarty.get.usertype eq 'C'}
      {assign var="title" value=$lng.lbl_modify_customer_profile}
    {else}
      {assign var="title" value=$lng.lbl_modify_admin_profile}
    {/if}

  {/if}
{/if}

{include file="page_title.tpl" title=$title}

<font class="Text">

{if $newbie ne "Y"}
<br />
  {if $main eq "user_add"}

    {if $smarty.get.usertype eq 'C'}
      {$lng.txt_create_customer_profile}
    {else}
      {$lng.txt_create_admin_profile}
    {/if}

  {else}

    {if $smarty.get.usertype eq 'C'}
      {$lng.txt_modify_customer_profile}
    {else}
      {$lng.txt_modify_admin_profile}
    {/if}

  {/if}
<br /><br />
{/if}

{$lng.txt_fields_are_mandatory}

</font>

<br /><br />

{capture name=dialog}

{if $newbie ne "Y" and $main ne "user_add" and $is_admin_user}
  <div align="right">{include file="buttons/button.tpl" button_title=$lng.lbl_go_to_users_list href="users.php?mode=search"}</div>
{/if}

{if $registered eq ""}

{if $reg_error}
  <font class="Star">{$reg_error.errdesc}</font>
  <br />
{/if}

<form action="{$register_script_name}?{$smarty.server.QUERY_STRING|amp}" method="post" name="registerform" onsubmit="javascript: return checkRegFormFields(this);" {if $config.User_Profiles.skip_js_validation_admin eq "Y"}class="skip-auto-validation"{/if}>

{if $config.Security.use_https_login eq "Y"}
  <input type="hidden" name="{$XCARTSESSNAME}" value="{$XCARTSESSID}" />
{/if}

<table cellspacing="1" cellpadding="2" width="100%">
<tbody>
  {include file="main/register_personal_info.tpl" userinfo=$userinfo}

  {include file="main/register_address_book.tpl" addresses=$userinfo.addresses}

  {include file="main/register_additional_info.tpl" section='A'}

  {include file="main/register_account.tpl" userinfo=$userinfo}

  {if $active_modules.Special_Offers}
    {include file="modules/Special_Offers/customer/register_bonuses.tpl"}
  {/if}

  {if $active_modules.News_Management and $newslists}
    {include file="modules/News_Management/register_newslists.tpl"}
  {/if}

<tr>
  <td colspan="2">
    <br />
    {if $newbie eq "Y"}
      <a href="register.php?mode=delete" class="delete-profile-link">{$lng.lbl_delete}</a>
    {/if}
  </td>
  <td>
    <br />
    <font class="FormButton">
      {if $smarty.get.mode eq "update"}
        <input type="hidden" name="mode" value="update" />
      {/if}
      <input type="submit" value=" {if $userinfo.id gt 0}{$lng.lbl_update|escape}{else}{$lng.lbl_register|escape}{/if} " />
    </font>
  </td>
</tr>
</tbody>
</table>

<input type="hidden" name="usertype" value="{if $smarty.get.usertype ne ""}{$smarty.get.usertype|escape:"html"}{else}{$usertype}{/if}" />

</form>

<br /><br />

{else}

  {if $smarty.post.mode eq "update" or $smarty.get.mode eq "update"}
    {$lng.txt_profile_modified}
  {else}
    {$lng.txt_profile_created}
  {/if}

{/if}

{/capture}
{include file="dialog.tpl" title=$lng.lbl_profile_details content=$smarty.capture.dialog extra='width="100%"'}
