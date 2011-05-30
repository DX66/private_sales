{*
$Id: checkout_payment_methods.tpl,v 1.3 2010/06/08 06:17:38 igoryan Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
<div class="checkout-payments">
  {include file="modules/`$checkout_module`/payment_methods.tpl"}
</div>

<script type="text/javascript">
//<![CDATA[
var paymentsCOD = [{strip}
{foreach from=$payment_methods item=payment name=payment_methods}
{if $payment.is_cod eq "Y"}
  {$payment.paymentid}{if not $smarty.foreach.payment_methods.last},{/if}

{/if}
{/foreach}
{/strip}];
display_cod({if $display_cod eq 'Y'}true{else}false{/if});

{literal}
function display_cod(flag) {
  for (var i = 0; i < paymentsCOD.length; i++) {
    if (paymentsCOD[i] && document.getElementById('cod_tr' + paymentsCOD[i]))
      document.getElementById('cod_tr' + paymentsCOD[i]).style.display = flag ? "" : "none";
  }

  return true;
}
{/literal}
//]]>
</script>

