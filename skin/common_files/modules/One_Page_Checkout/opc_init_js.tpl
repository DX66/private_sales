{*
$Id: opc_init_js.tpl,v 1.9 2010/08/03 13:27:07 igoryan Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="check_email_script.tpl"}
{include file="check_password_script.tpl"}
{include file="check_zipcode_js.tpl"}
{include file="change_states_js.tpl"}

<script type="text/javascript">
//<![CDATA[

var txt_accept_terms_err = '{$lng.txt_accept_terms_err|wm_remove|escape:"javascript"}';
var lbl_warning          = '{$lng.lbl_warning|wm_remove|escape:"javascript"}';
var msg_being_placed     = '{$lng.msg_order_is_being_placed|wm_remove|escape:"javascript"}';

var txt_opc_incomplete_profile    = '{$lng.txt_opc_incomplete_profile|wm_remove|escape:"javascript"}';
var txt_opc_payment_not_selected  = '{$lng.txt_opc_payment_not_selected|wm_remove|escape:"javascript"}';
var txt_opc_shipping_not_selected = '{$lng.txt_opc_shipping_not_selected|wm_remove|escape:"javascript"}';

var shippingid    = {$cart.shippingid|default:0};
var paymentid     = {$cart.paymentid|default:0};
var unique_key    = '{unique_key}';
var skip_cc_check = {if $config.General.disable_cc ne "Y" and $smarty.get.err ne 'fields'}true{else}false{/if};
var check_cc_num  = {if $config.General.check_cc_number eq "Y"}true{else}false{/if};
var av_error      = {if $av_error}true{else}false{/if};
var need_shipping = {if $need_shipping}true{else}false{/if};

var paypal_express_selected = {if $paypal_express_selected}true{else}false{/if};

var payments = [];
{foreach from=$payment_methods item=p name=pt}
payments[{$p.paymentid}] = {ldelim}url: '{$p.payment_script_url}', name: '{$p.payment_method|wm_remove|escape:"javascript"}'{rdelim};
{/foreach}

{literal}
function checkCCFields() {

  if (!check_cc_num) {
    return true;
  }

  var res    = true;
  var pf     = $('form[name=paymentform]');
  var _pf    = pf.get(0);
  
  if ($('#card_name', pf).length == 0) {
    return true;
  }

  if ($('#card_name', pf).parents(':hidden').length > 0) {
    return true;
  }

  var ccNum  = $('#card_number', pf).get(0);
  var ccType = $('#card_type', pf).get(0);
  var ccExpM = _pf.elements.namedItem('card_expire_Month[' + unique_key + ']');
  var ccExpY = _pf.elements.namedItem('card_expire_Year[' + unique_key + ']');
  var ccCVV2 = $('#card_cvv2', $('#opc_payment')).get(0);

  res = checkCCNumber(ccNum, ccType, skip_cc_check) && checkExpirationDate(ccExpM, ccExpY);

  if (res && ccCVV2 !== undefined) {
    res = checkCVV2(ccCVV2, ccType, skip_cc_check);
  }

  return res;
}

function checkCheckoutForm() {

  // Check if profile filled in: registerform should not exist on the page
  if ($('form[name=registerform]').length > 0) {
    xAlert(txt_opc_incomplete_profile);
    return false;
  }

  if (need_shipping && ($('input[name=shippingid]').val() <= 0 || (undefined === shippingid || shippingid <= 0))) {
    xAlert(txt_opc_shipping_not_selected);
    return false;
  }
  
  if (!paymentid && (undefined === paymentid || paymentid <= 0)) {
    xAlert(txt_opc_shipping_not_selected);
    return false;
  }

  // Check terms accepting
  var termsObj = $('#accept_terms')[0];
  if (termsObj && !termsObj.checked) {
    xAlert(txt_accept_terms_err, lbl_warning);
    return false;
  }

  // Check CC fields
  if (!checkCCFields()) {
    return false;
  }

  return true;
}
{/literal}

//]]>
</script>
