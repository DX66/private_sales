{*
$Id: config_recommends.tpl,v 1.2.2.3 2010/09/17 08:10:02 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<em>{$lng.lbl_xpc_api_version_supported|substitute:"api_version":$smarty.const.XPC_API_VERSION}</em>
<table cellpadding="10" cellspacing="0" class="general-settings">
<tr>
  <td>

{if $system_requirements_errors}

  {include file="main/subheader.tpl" title=$lng.txt_xpc_requirements_failed}

  <ul>
    {foreach from=$system_requirements_errors item=e}
      <li>{$e}</li>
    {/foreach}
  </ul>
  <br />
{/if}

{if $check_sys_errs}

  {include file="main/subheader.tpl" title=$lng.txt_xpc_sys_check_failed}

  <ul>
    {foreach from=$check_sys_errs item=e}
      <li>{$e}</li>
    {/foreach}
  </ul>
  <br />
{/if}

{if $xpc_recommends}

  {include file="main/subheader.tpl" title=$lng.lbl_xpc_recommendations}

  <table cellpadding="7" cellspacing="1">

    {foreach from=$xpc_recommends key=type item=recommends}

      {foreach from=$recommends key=key item=recommendation}

        <tr{cycle values=', class="TableSubHead"'}>
          <td>
            <img src="{$ImagesDir}/{if $type eq 'E'}icon_error_small.gif{else}icon_warning_small.gif{/if}" alt="" />
          </td>
          <td>
            {if $key eq "payment_methods"}

              {$lng.lbl_xpc_recommend_payment_methods}<br />
              <ul>
                {foreach from=$recommendation item=payment_module}
                  <li>{$payment_module}</li>
                {/foreach}
              </ul>

            {else}

              {$recommendation}

            {/if}
          </td>
        </tr>

      {/foreach}

    {/foreach}

  </table>

{/if}
  </td>
</tr>
</table>

<br />

{include file="main/subheader.tpl" title=$lng.lbl_xpc_deploy_configuration_module class="black"}

<input type="hidden" id="mode" name="mode" value="" />

<table width="50%" cellspacing="3" cellpadding="3"> 
  <tr>
    <td colspan="2">{$lng.txt_xpc_deploy_description}</td>
  </tr>
  <tr>
    <td><input type="text" name="deploy_configuration" value="" size="60" /></td>
    <td><input type="button" onclick="javascript: $(this.form).unbind('submit'); submitForm(this,'deploy_configuration');" value="{$lng.lbl_xpc_deploy}" /></td>
  </tr>
</table>

