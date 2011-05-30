{*
$Id: payment_dd.tpl,v 1.1 2010/05/21 08:32:04 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $payment_cc_data.disable_ccinfo ne "Y"}

  <table cellspacing="0" class="data-table">

    {if $payment_cc_data.c_template ne ""}
      {include file=$payment_cc_data.c_template}
    {else}
      {include file="customer/main/register_ddinfo.tpl"}
    {/if}

  </table>

{else}

  {$lng.disable_chinfo_msg}
  <br />

{/if}
