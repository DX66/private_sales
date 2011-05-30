{*
$Id: ps_paypal_group.tpl,v 1.5 2010/07/30 08:32:11 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>PayPal</h1>

{$lng.txt_cc_configure_top_text}
<br />
<script type="text/javascript">
//<![CDATA[
var pp_promo = {ldelim}
  uk: '{$lng.lbl_paypal_api_promo_uk|wm_remove|escape:javascript}',
  pro: '{$lng.lbl_paypal_api_promo_pro|wm_remove|escape:javascript}',
  ipn: '{$lng.lbl_paypal_api_promo_ipn|wm_remove|escape:javascript}',
  express: '{$lng.lbl_paypal_api_promo_express|wm_remove|escape:javascript}'
{rdelim};
var paypal_solution = '{$config.paypal_solution|default:'ipn'|escape:javascript}';
//]]>
</script>
<script type="text/javascript" src="{$SkinDir}/js/ps_paypal_group.js"></script>

<br />

{capture name=dialog}

  <table width="100%" cellpadding="5">
    <tr>
      <td width="100%">
  
        {$lng.txt_paypal_solution_title}

        <form action="cc_processing.php" method="post">
          <input type="hidden" name="cc_processor" value="{$smarty.get.cc_processor|escape:"url"}" />

          <table cellpadding="5" cellspacing="5" width="100%" id="paypal-settings">

            {* main switch *}
            <tr valign="top">
              <td width="20">
                <input id="r_sol_ipn" type="radio" name="paypal_solution" onclick="view_solution('ipn');" value="ipn"{if $config.paypal_solution eq "ipn"} checked="checked"{/if} />
              </td>
              <td width="100%">
                <h3><label for="r_sol_ipn">{$lng.lbl_paypal_sol_std}</label></h3>
                {$lng.txt_paypal_sol_std_note}
              </td>
            </tr>

            <tr valign="top">
              <td>
                <input id="r_sol_pro" type="radio" name="paypal_solution" onclick="view_solution('pro');" value="pro"{if $config.paypal_solution eq "pro"} checked="checked"{/if} />  
              </td>
              <td>
                <h3><label for="r_sol_pro">{$lng.lbl_paypal_sol_pro}</label></h3>
                {$lng.txt_paypal_sol_pro_note}<br />
                <a href="javascript:void(0);" onclick="javascript:window.open('http://help.qtmsoft.com/index.php?title=X-Cart:PayPal','PPEC_HELP');">{$lng.lbl_paypal_guidelines_click}</a>
                </td>
            </tr>

            <tr valign="top">
              <td>
                <input id="r_sol_uk" type="radio" name="paypal_solution" onclick="view_solution('uk');" value="uk"{if $config.paypal_solution eq "uk"} checked="checked"{/if} />
              </td>
              <td>
                <h3><label for="r_sol_uk">{$lng.lbl_paypal_sol_uk}</label></h3>
                {$lng.txt_paypal_sol_pro_note_uk}
              </td>
            </tr>

            <tr valign="top">
              <td>
                <input id="r_sol_express" type="radio" name="paypal_solution" onclick="view_solution('express');" value="express"{if $config.paypal_solution eq "express"} checked="checked"{/if} />
              </td>
              <td>
                <h3><label for="r_sol_express">{$lng.lbl_paypal_sol_express}</label></h3>
                {$lng.txt_paypal_sol_express_note}<br />
                <a href="javascript:void(0);" onclick="javascript:window.open('http://help.qtmsoft.com/index.php?title=X-Cart:PayPal','PPEC_HELP');">{$lng.lbl_paypal_guidelines_click}</a>
              </td>
            </tr>

            <tr>
              <td colspan="2">
                <hr size="1" noshade="noshade" />
                <div id="pp_promo"></div>
              </td>
            </tr>

            {* configuration boxes *}
            <tr id="sol_pro"{if $config.paypal_solution ne "pro"} style="display: none;"{/if}>
              <td>&nbsp;</td>
              <td>
                {include file="payments/ps_paypal_pro.tpl" conf_prefix="conf_data[pro]" module_data=$conf_data.pro}
              </td>
            </tr>

            <tr id="sol_express"{if $config.paypal_solution ne "express"} style="display: none;"{/if}>
              <td>&nbsp;</td>
              <td>
                {include file="payments/ps_paypal_express.tpl" conf_prefix="conf_data[express]" module_data=$conf_data.pro}
              </td>
            </tr>

            <tr id="sol_uk"{if $config.paypal_solution ne "uk"} style="display: none;"{/if}>
              <td>&nbsp;</td>
              <td>
                {include file="payments/ps_paypal_uk.tpl" conf_prefix="conf_data[uk]" module_data=$conf_data.uk}
              </td>
            </tr>

            <tr id="sol_ipn"{if $config.paypal_solution ne "ipn"} style="display: none;"{/if}>
              <td>&nbsp;</td>
              <td>
                {include file="payments/ps_paypal.tpl" conf_prefix="conf_data[ipn]" module_data=$conf_data.ipn}
              </td>
            </tr>

            <tr>
              <td colspan="2"><hr size="1" noshade="noshade" /></td>
            </tr>

            <tr>
              <td>&nbsp;</td>
              <td>
                <table width="100%">
                  <tr valign="top">
                    <td><input id="r_suppress_encoding" type="checkbox" name="paypal_suppress_encoding" value="Y"{if $config.paypal_suppress_encoding eq "Y"} checked="checked"{/if} /></td>
                    <td>
                      <label for="r_suppress_encoding">
                        <strong>{$lng.lbl_paypal_suppress_encoding}</strong><br />
                        {$lng.txt_paypal_suppress_encoding}
                      </label>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>

            <tr>
              <td>&nbsp;</td>
              <td>
                <table width="100%">
                  <tr valign="top">
                    <td><input id="r_paypal_amex" type="checkbox" name="paypal_amex" value="Y"{if $config.paypal_amex eq "Y"} checked="checked"{/if} /></td>
                    <td>
                      <label for="r_paypal_amex">
                        <strong>{$lng.lbl_paypal_amex}</strong><br />
                        {$lng.txt_paypal_amex}
                      </label>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>

            <tr>
              <td colspan="2" align="center">
                <input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
              </td>
            </tr>

          </table>
        </form>
      </td>
      <td valign="top">{include file="payments/ps_paypal_logo.tpl"}</td>
    </tr>
  </table>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
