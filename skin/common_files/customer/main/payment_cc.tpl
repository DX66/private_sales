{*
$Id: payment_cc.tpl,v 1.4 2010/07/01 07:54:35 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $payment_cc_data.disable_ccinfo ne "Y"}
<script type="text/javascript">
//<![CDATA[
{literal}
  $(document).ready(function(){
    $("input,select").attr({ 
          autocomplete: "off"
        });
  });
{/literal}
//]]>
</script>

  {if $checkout_module neq 'One_Page_Checkout'}
    <table cellspacing="0" class="data-table" summary="{$lng.lbl_credit_card_information|escape}">
  {/if}

    {if $payment_cc_data.c_template ne ""}
      {include file=$payment_cc_data.c_template}
    {else}
      {include file="customer/main/register_ccinfo.tpl"}
    {/if}

  {if $checkout_module neq 'One_Page_Checkout'}
    </table>
  {/if}

{else}
  {if $payment_cc_data.background eq 'I'}
    {$lng.disable_ccinfo_iframe_msg}
  {else}
    {$lng.disable_ccinfo_msg}
  {/if}
  <br />

{/if}
