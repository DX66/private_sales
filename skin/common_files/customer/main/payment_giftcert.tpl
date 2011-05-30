{*
$Id: payment_giftcert.tpl,v 1.3 2010/06/08 06:17:39 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $checkout_module eq 'One_Page_Checkout'}
  
  {include file="modules/One_Page_Checkout/payment/payment_giftcert.tpl"}
  
{else}
  
  <script type="text/javascript">
  //<![CDATA[
  requiredFields = [
    ["gcid", "{$lng.lbl_gift_certificate}"]
  ];
  //]]>
  </script>
  
  <table cellspacing="0" class="data-table">
  
    {if $hide_header ne "Y"}
  
      <tr>
        <td class="register-section-title" colspan="3"><label>{$lng.lbl_gc_information}</label></td>
      </tr>
  
    {/if}
  
    {if $smarty.get.err eq "gc_used"}
      <tr>
        <td colspan="3" class="error-message">{$lng.err_gc_used}</td>
      </tr>
    {/if}
  
    <tr>
      <td class="data-name">{$lng.lbl_gift_certificate}</td>
      <td class="data-required">*</td>
      <td><input type="text" size="32" id="gcid" name="gcid" /></td>
    </tr>
  
  </table>

{/if}
