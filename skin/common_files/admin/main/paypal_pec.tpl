{*
$Id: paypal_pec.tpl,v 1.1 2010/05/21 08:32:00 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

<div class="paypal-pec">
  <h2>{$lng.lbl_paypal_using_pec}</h2>
  {$lng.lbl_paypal_pec_desc}<br />
  <br />
  <a href="javascript:popup('http://www.paypal.com/en_US/m/demo/18077_ec.html',570,365);">{$lng.lbl_paypal_see_quick_demo}</a>
  <img src="{$ImagesDir}/paypal_ec.png" alt="" />
  <form action="payment_methods.php" method="post" name="addpaypalform">
    <input type="hidden" name="mode" value="save_methods" />
    <input type="hidden" name="methods[]" value="paypal" />
    <input type="hidden" name="paypal_solution" value="express" />
    <input type="hidden" name="submode" value="add_paypal" />

    <button type="submit">{$lng.lbl_paypal_add_paypal}</button>
  </form>
</div>
