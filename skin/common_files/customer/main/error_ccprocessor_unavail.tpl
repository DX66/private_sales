{*
$Id: error_ccprocessor_unavail.tpl,v 1.1.2.1 2010/12/23 13:52:55 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

<h1>{$lng.lbl_order_processing_error}</h1>

<div class="error-message text-block">
  {$lng.err_payment_cc_not_available}<br />
  {$smarty.get.bill_message|escape|nl2br}
</div>

{include file="customer/buttons/go_back.tpl" href="`$catalogs.customer`/cart.php?mode=checkout"}
