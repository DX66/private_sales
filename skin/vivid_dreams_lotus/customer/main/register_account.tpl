{*
$Id: register_account.tpl,v 1.1 2010/05/21 08:33:04 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $config.Security.use_complex_pwd eq 'Y' and $userinfo.login|default:$userinfo.uname eq ''}
  {assign var='show_passwd_note' value='Y'}
{/if}

  {if $hide_header eq ""}
    <tr>
      <td colspan="3" class="register-section-title">
        <div>
          <label>{$lng.lbl_account_information}</label>
        </div>
      </td>
    </tr>
  {/if}

  <tr>
    <td class="data-name"><label for="email">{$lng.lbl_email}</label></td>
    <td class="data-required">*</td>
    <td>
      <input type="text" id="email" name="email" class="input-required input-email" size="32" maxlength="128" value="{$userinfo.email|escape}" />
      <div id="email_note" class="note-box" style="display: none;">{$lng.txt_email_note}</div>
    </td>
  </tr>

  {if $anonymous and $config.General.enable_anonymous_checkout eq "Y"}
    <tr>
      <td class="register-section-title register-exp-section{if not ($reg_error and $userinfo.create_account)} register-sec-minimized{/if}" colspan="3">
        <div>
          <label class="pointer" for="create_account">{$lng.txt_opc_create_account|substitute:"login_field":$login_field_name}</label>
          <input type="checkbox" id="create_account" name="create_account" value="Y"{if $reg_error and $userinfo.create_account} checked="checked"{/if} />
        </div>
      </td>
    </tr>

    </tbody>
    <tbody id="create_account_box">

    <tr>
      <td colspan="3">{$lng.txt_anonymous_account_msg}</td>
    </tr>
  {/if}

  {if $userinfo.id eq $logged_userid and $logged_userid gt 0 and $userinfo.usertype ne "C"}

      <tr style="display: none;">
        <td>
          <input type="hidden" name="membershipid" value="{$userinfo.membershipid}" />
          <input type="hidden" name="pending_membershipid" value="{$userinfo.pending_membershipid}" />
        </td>
      </tr>

  {else}

    {if $config.General.membership_signup eq "Y" and ($usertype eq "C" or $is_admin_user or $usertype eq "B") and $membership_levels}
      {include file="customer/main/membership_signup.tpl"}
    {/if}

  {/if}

  {if $config.email_as_login ne 'Y'}
    <tr>
      <td class="data-name"><label for="uname">{$lng.lbl_username}</label></td>
      {if $config.General.allow_change_login ne 'Y'}
        <td></td>
        <td>
          <b>{$userinfo.login|default:$userinfo.uname}</b>
          <input type="hidden" name="uname" value="{$userinfo.login|default:$userinfo.uname}" />
      {else}
        <td class="data-required">*</td>
        <td>
          <input type="text" id="uname" name="uname" class="input-required" size="32" maxlength="32" value="{if $userinfo.uname}{$userinfo.uname}{else}{$userinfo.login}{/if}" />
      {/if}
      </td>
    </tr>
  {/if}

  {if $allow_pwd_modify eq 'Y'}
    <tr style="display:none;"><td><input type="hidden" name="password_is_modified" id="password_is_modified" value="N" /></td></tr>
    <tr>
      <td class="data-name"><label for="passwd1">{$lng.lbl_password}</label></td>
      <td class="data-required">*</td>
      <td>
        <input type="password" id="passwd1" name="passwd1" class="input-required" size="32" maxlength="64" value="{$userinfo.passwd1|escape}" />
        {if $show_passwd_note eq 'Y'}<div id="passwd_note" class="note-box" style="display: none;">{$lng.txt_password_strength}</div>{/if}
      </td>
    </tr>

    <tr>
      <td class="data-name"><label for="passwd2">{$lng.lbl_confirm_password}</label></td>
      <td class="data-required">*</td>
      <td>
        <input type="password" id="passwd2" name="passwd2" class="input-required" size="32" maxlength="64" value="{$userinfo.passwd2|escape}" />
        <span class="validate-mark"><img src="{$ImagesDir}/spacer.gif" width="15" height="15" alt="" /></span>
      </td>
    </tr>
    {else}
    <tr>
      <td class="data-name">{$lng.lbl_password}</td>
      <td></td>
      <td><a href="change_password.php">{$lng.lbl_chpass}</a></td>
    </tr>
  {/if}
  
  {if $anonymous and $config.General.enable_anonymous_checkout eq "Y"}
    </tbody>
    <tbody>
  {/if}

  {if $is_admin_user and $userinfo.id ne $logged_userid}

      <tr>
        <td class="data-name"><label for="status">{$lng.lbl_account_status}:</label></td>
        <td>&nbsp;</td>
        <td>

          <select name="status">
            <option value="N"{if $userinfo.status eq "N"} selected="selected"{/if}>{$lng.lbl_account_status_suspended}</option>
            <option value="Y"{if $userinfo.status eq "Y"} selected="selected"{/if}>{$lng.lbl_account_status_enabled}</option>
            {if $active_modules.XAffiliate ne "" and ($userinfo.usertype eq "B" or $smarty.get.usertype eq "B")}
              <option value="Q"{if $userinfo.status eq "Q"} selected="selected"{/if}>{$lng.lbl_account_status_not_approved}</option>
              <option value="D"{if $userinfo.status eq "D"} selected="selected"{/if}>{$lng.lbl_account_status_declined}</option>
            {/if}
          </select>
        </td>
      </tr>

    {if $display_activity_box eq "Y"}
      <tr>
        <td class="data-name"><label for="activity">{$lng.lbl_account_activity}:</label></td>
        <td>&nbsp;</td>
        <td>

          <select name="activity">
            <option value="Y"{if $userinfo.activity eq "Y"} selected="selected"{/if}>{$lng.lbl_account_activity_enabled}</option>
            <option value="N"{if $userinfo.activity eq "N"} selected="selected"{/if}>{$lng.lbl_account_activity_disabled}</option>
          </select>

        </td>
      </tr>
    {/if}

      <tr>
        <td colspan="2">&nbsp;</td>
        <td>

          <label>
            <input type="checkbox" class="exclude-style" id="change_password" name="change_password" value="Y"{if $userinfo.change_password eq "Y"} checked="checked"{/if} />
            {$lng.lbl_reg_chpass}
          </label>

        </td>
      </tr>

  {if ($userinfo.usertype eq "P" or $smarty.get.usertype eq "P") and $usertype eq "A" and $active_modules.Simple_Mode eq ""}
      <tr>
        <td colspan="2">&nbsp;</td>
        <td>

          <label>
            <input type="checkbox" class="exclude-style" id="trusted_provider" name="trusted_provider" value="Y"{if $userinfo.trusted_provider eq "Y"} checked="checked"{/if} />
            {$lng.lbl_trusted_providers}
          </label>

        </td>
      </tr>
  {/if}

{/if}
