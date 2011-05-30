{*
$Id: paypal_flow_step3.tpl,v 1.1 2010/05/21 08:32:00 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

<h2>{$lng.lbl_paypal_setup_other_payment_options}</h2>

<div class="step3">

  <form action="payment_methods.php" method="post" name="paypalflowstep3">
    <input type="hidden" name="mode" value="save_methods" />

    <ul>
      <li class="selected">
        <input type="checkbox" name="methods[]" value="paypal" id="method_paypal" checked="checked"{if $paypal_enabled} disabled="disabled"{/if} />
        <label for="method_paypal">{$lng.lbl_paypal_pec}</label>
        <div>
          {$lng.lbl_paypal_pec_desc}<br />
          <a href="javascript:popup('http://www.paypal.com/en_US/m/demo/18077_ec.html',355,560);">{$lng.lbl_paypal_see_quick_demo}</a>
        </div>

        {if $paypal_enabled and $paypal_solution}
          <input type="hidden" name="methods[]" value="paypal" />
          <input type="hidden" name="paypal_solution" value="{$paypal_solution}" />
        {else}
          <input type="hidden" name="paypal_solution" value="express" />
        {/if}
      </li>

      {foreach from=$payment_methods item=p}

        <li{if $p.selected} class="selected"{/if}>
          <input type="checkbox" name="methods[]" value="{$p.id}" id="method_{$p.id}"{if $p.selected} checked="checked"{/if} />
          <label for="method_{$p.id}">{$p.payment_method|default:$p.module_name}</label>
          {if $p.payment_details}
            <div>{$p.payment_details}</div>
          {/if}
        </li>

      {/foreach}

    </ul>

    <div class="buttons-line">
      <button type="button" onclick="javascript: history.go(-1);" class="first">{$lng.lbl_back}</button>
      <button type="submit">{$lng.lbl_finish}</button>
    </div>

  </form>

</div>

<script type="text/javascript" src="{$SkinDir}/js/paypal_flow.js"></script>
