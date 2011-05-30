{*
$Id: login_form.tpl,v 1.4.2.1 2010/11/15 11:46:25 ferz Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<form action="{$authform_url}" method="post" name="authform">
  <input type="hidden" name="{$XCARTSESSNAME}" value="{$XCARTSESSID}" />
  <input type="hidden" name="is_remember" value="{$is_remember}" />
  <input type="hidden" name="mode" value="login" />

  <table cellspacing="0" class="data-table" summary="{$lng.lbl_authentication|escape}">
    <tr> 
      <td class="data-name"><label for="username">{$login_field_name}</label></td>
      <td class="data-required">*</td>
      <td>
        <input type="text" id="username" name="username"{if $config.email_as_login eq 'Y'} class="input-email"{/if} size="30" value="{#default_login#|default:$username|escape}" />
      </td>
    </tr>

    <tr> 
      <td class="data-name"><label for="password">{$lng.lbl_password}</label></td>
      <td class="data-required">*</td>
      <td><input type="password" id="password" name="password" size="30" maxlength="64" value="{#default_password#}" /></td>
    </tr>

    {if $active_modules.Remember_Me ne "" and $auth_cookie_allowed eq "Y"}

      <tr>
        <td colspan="2">&nbsp;</td>
        <td>
          <input type="checkbox" id="autologin" name="autologin" value="Y" /> 
          <label for="autologin">{$lng.lbl_remember_me_q}</label>
        </td>
      </tr>

    {/if}

    {include file="customer/buttons/submit.tpl" type="input" additional_button_class="main-button" assign="submit_button"}

    {if not $is_modal_popup and $active_modules.Image_Verification and $show_antibot.on_login eq 'Y' and $login_antibot_on and $main ne 'disabled'}
      {include file="modules/Image_Verification/spambot_arrest.tpl" mode="data-table" id=$antibot_sections.on_login button_code=$submit_button}

      {if $antibot_err}
        <tr>
          <td colspan="2">&nbsp;</td>
          <td class="error-message">{$lng.msg_err_antibot}</td>
        </tr>
      {/if}

   {else}

    <tr> 
      <td colspan="2">&nbsp;</td>
      <td class="button-row">{$submit_button}</td>
    </tr>
    
    {/if}
    
    {if not $is_modal_popup}
      <tr>
        <td colspan="2">&nbsp;</td>
        <td>{include file="customer/buttons/button.tpl" href="help.php?section=Password_Recovery" button_title=$lng.lbl_recover_password style="link"}</td>
      </tr>
    {/if}

  </table>

</form>
