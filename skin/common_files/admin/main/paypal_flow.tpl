{*
$Id: paypal_flow.tpl,v 1.1 2010/05/21 08:31:59 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

<div class="paypal-flow">
  {include file="page_title.tpl" title=$lng.lbl_payment_methods}

  {if $submode eq 'finalize'}
    {include file="admin/main/paypal_flow_step3.tpl"}

  {elseif $list_is_empty}
    {if not $paypal_flow_accept}
      {include file="admin/main/paypal_flow_step1.tpl"}

    {elseif $paypal_flow_accept eq 'paypal'}
      {include file="admin/main/paypal_flow_step2a.tpl"}

    {elseif $paypal_flow_accept eq 'cc'}
      {include file="admin/main/paypal_flow_step2b.tpl"}

    {/if}

  {else}
    {include file="admin/main/payment_methods.tpl"}
  {/if}
</div>
