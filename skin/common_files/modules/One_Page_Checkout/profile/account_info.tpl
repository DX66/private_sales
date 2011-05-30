{*
$Id: account_info.tpl,v 1.3.2.1 2010/09/24 12:31:03 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $config.Security.use_complex_pwd eq 'Y' and $userinfo.login|default:$userinfo.uname eq ''}
  {assign var='show_passwd_note' value='Y'}
{/if}

{if not $hide_header}
  <h3>{$lng.lbl_account_information}</h3>
{/if}

<ul>
  <li class="single-field">
    {capture name=regfield}
      <input type="text" id="email" name="email" class="input-required input-email" size="32" maxlength="128" value="{$userinfo.email|escape}" />
      {if $config.email_as_login ne 'Y' and $login ne ''}
       <input type="hidden" id="uname" name="uname" value="{$userinfo.login|default:$userinfo.uname|escape}" /><br/>
      {/if}
      <div id="email_note" class="note-box" style="display: none;">{$lng.txt_email_note}</div>
    {/capture}
    {include file="modules/One_Page_Checkout/opc_form_field.tpl" content=$smarty.capture.regfield required=true name=$lng.lbl_email field="email"}
  </li>
</ul>

{if $login eq ''}

  {if $config.General.enable_anonymous_checkout eq 'Y'}
    <div class="optional-label">
      <label class="pointer" for="create_account">
        <input type="checkbox" id="create_account" name="create_account" value="Y"{if $reg_error and $userinfo.create_account} checked="checked"{/if} />
        {$lng.txt_opc_create_account|substitute:"login_field":$login_field_name}
      </label>
    </div>
  {/if}

  <ul id="create_account_box">

  {if $config.General.membership_signup eq "Y" and $membership_levels}
    <li class="single-field">
      {capture name=regfield}
      <select name="pending_membershipid" id="pending_membershipid">
        <option value="">{$lng.lbl_not_member}</option>
        {foreach from=$membership_levels item=v}
          <option value="{$v.membershipid}"{if $userinfo.pending_membershipid eq $v.membershipid} selected="selected"{/if}>{$v.membership}</option>
        {/foreach}
        </select>
      {/capture}
      {include file="modules/One_Page_Checkout/opc_form_field.tpl" content=$smarty.capture.regfield name=$lng.lbl_signup_for_membership field='pending_membershipid'}
    </li>
  {/if}

  {if $config.email_as_login ne 'Y'}
    <li class="single-field">
      {capture name=regfield}
        <input type="text" id="uname" name="uname" size="32" maxlength="32" value="{if $userinfo.uname}{$userinfo.uname}{else}{$userinfo.login}{/if}" />
    {/capture}
    {include file="modules/One_Page_Checkout/opc_form_field.tpl" content=$smarty.capture.regfield required=true name=$lng.lbl_username field='uname'}
    </li>
  {/if}

  {if $allow_pwd_modify eq 'Y'}
    <li class="single-field">
      {capture name=regfield}
        <input type="hidden" name="password_is_modified" id="password_is_modified" value="N" />
        <input type="password" id="passwd1" name="passwd1" size="32" maxlength="64" value="{$userinfo.passwd1|escape}" />
        {if $show_passwd_note eq 'Y'}<div id="passwd_note" class="note-box" style="display: none;">{$lng.txt_password_strength}</div>{/if}
      {/capture}
      {include file="modules/One_Page_Checkout/opc_form_field.tpl" content=$smarty.capture.regfield required=true name=$lng.lbl_password field='passwd1'}

      {capture name=regfield}
        <input type="password" id="passwd2" name="passwd2" size="32" maxlength="64" value="{$userinfo.passwd2|escape}" />
        <span class="validate-mark"><img src="{$ImagesDir}/spacer.gif" width="15" height="15" alt="" /></span>
      {/capture}
      {include file="modules/One_Page_Checkout/opc_form_field.tpl" content=$smarty.capture.regfield required=true name=$lng.lbl_confirm_password field='passwd2'}

    </li>
  {/if}

  </ul>
{/if}
