{*
$Id: payment_giftcert.tpl,v 1.1 2010/05/21 08:32:46 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<ul>
  <li class="single-field">
    {capture name=regfield}
      <input type="text" size="32" id="gcid" name="gcid" />
      {include file="customer/buttons/button.tpl" type="input" style="image" button_id="apply_gc_button"}
    {/capture}
    {include file="modules/One_Page_Checkout/opc_form_field.tpl" content=$smarty.capture.regfield name=$lng.lbl_gift_certificate field="gcid" required="Y"}
  </li>
</ul>
