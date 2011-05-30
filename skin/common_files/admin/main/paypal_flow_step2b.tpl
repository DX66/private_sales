{*
$Id: paypal_flow_step2b.tpl,v 1.3 2010/06/08 06:17:38 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

<h2>{$lng.lbl_paypal_choose_solution_cc}</h2>

<script type="text/javascript">
//<![CDATA[
var complexProcessors = [
{foreach from=$payment_methods_complex item=id name=complex}
  '{$id}'{if not $smarty.foreach.complex.last},{/if}
{/foreach}
];
//]]>
</script>
<div class="step2b">

  <form action="payment_methods.php" method="post" name="paypalflowstep2b">
    <input type="hidden" name="mode" value="set_methods" />

    <ul class="manual">
      <li>
        <input type="checkbox" name="methods[]" value="manual" id="manual_method" />
        <label for="manual_method">{$lng.lbl_paypal_manual_cc_processing}</label>
      </li>
    </ul>

    <h3>{$lng.lbl_paypal_all_in_one}</h3>
    <ul class="complex">

      <li{if $is_paypal_enabled} class="selected"{/if}>
        <input type="checkbox" name="methods[]" value="paypal" id="paypal_method"{if $is_paypal_enabled} checked="checked"{/if} />
        <label for="paypal_method">{$lng.lbl_paypal_wp}</label>
      </li>
      <li class="sub{if $is_paypal_enabled} sub-selected{/if}">

        <ul>

          <li>
            <input type="radio" name="paypal_solution" value="wps" id="paypal_wps"{if not $is_paypal_enabled} disabled="disabled"{/if} />
            <label for="paypal_wps">{$lng.lbl_paypal_wps_short}</label>
            <div>{$lng.lbl_paypal_wps_desc}</div>
          </li>

          <li>
            <input type="radio" name="paypal_solution" value="wpp" id="paypal_wpp"{if not $is_paypal_enabled} disabled="disabled"{/if} />
            <label for="paypal_wpp">{$lng.lbl_paypal_wpp_short}</label>
            <div>{$lng.lbl_paypal_wpp_desc}</div>
          </li>

          <li class="last">
            <input type="radio" name="paypal_solution" value="wpppe" id="paypal_wpppe"{if not $is_paypal_enabled} disabled="disabled"{/if} />
            <label for="paypal_wpppe">{$lng.lbl_paypal_wpppe_short}</label>
            <div>{$lng.lbl_paypal_wpppe_desc}</div>
          </li>

        </ul>

      </li>

    </ul>

    <h3>{$lng.lbl_paypal_gateway_only}</h3>
    <ul class="gateways">

      {foreach from=$payment_methods_gateway item=p name=complex}

        <li{if $p.is_added eq 'Y'} class="selected"{/if}>
          <input type="checkbox" name="methods[]" value="{$p.id}" id="{$p.id}_method"{if $p.is_added} checked="checked"{/if} />
          <label for="{$p.id}_method">{$p.module_name}</label>
        </li>

      {/foreach}

    </ul>

    <h3>{$lng.lbl_paypal_all_other}:</h3>
    <select id="others">
      {foreach from=$payment_methods_other item=p name=complex}
        <option value="{$p.id}">{$p.module_name}</option>
      {/foreach}
    </select>
    <input type="button" onclick="javascript: addGateway();" value="{$lng.lbl_add|escape}" />

    <div class="buttons-line">
      <button type="button" class="first" onclick="javascript: history.go(-1);">{$lng.lbl_back}</button>
      <button type="submit">{$lng.lbl_next}</button>
    </div>

  </form>
</div>

<script src="{$SkinDir}/js/paypal_flow.js" type="text/javascript"></script>
