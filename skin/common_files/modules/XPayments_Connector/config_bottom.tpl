{*
$Id: config_bottom.tpl,v 1.1.2.1 2010/09/10 10:48:14 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

{if $is_module_configured}

  <br />
  <br />

  <a name="test_module"></a>

  {include file="main/subheader.tpl" title=$lng.lbl_xpc_test_module class="black"}

  {$lng.txt_xpc_test_module_note}

  <br />
  <br />

  <input type="button" name="test_module" value="{$lng.lbl_xpc_test_module|strip_tags:false|escape}" onclick="javascript: self.location='configuration.php?option=XPayments_Connector&amp;mode=test_module';" />

  <br />
  <br />
  <br />

  <a name="import"></a>

  {include file="main/subheader.tpl" title=$lng.lbl_xpc_import_payment_methods class="black"}

  {$lng.txt_xpc_import_payment_modules_note}

  <br />
  <br />

  <input type="button" name="import_payment_methods" value="{$lng.lbl_xpc_request_payment_methods|strip_tags:false|escape}" onclick="javascript: self.location='configuration.php?option=XPayments_Connector&amp;mode=export#export';" />

  {if $pm_list}

    <br />
    <br />

    {$lng.txt_xpc_returned_payment_methods}

    <br />
    <br />

    <table cellpadding="5" cellspacing="1" border="0">

      <tr>
        <td colspan="6" align="right"><a href="configuration.php?option=XPayments_Connector&amp;mode=clear">Clear</a></td>
      </tr>

      <tr class="TableHead">
        <td>{$lng.lbl_payment_method}</td>
        <td>{$lng.lbl_xpc_pm_id}</td>
        <td>{$lng.lbl_xpc_sale}</td>
        <td>{$lng.lbl_xpc_auth}</td>
        <td>{$lng.lbl_xpc_capture}</td>
        <td>{$lng.lbl_xpc_void}</td>
        <td>{$lng.lbl_xpc_refund}</td>
      </tr>

      {foreach from=$pm_list item=pm}
        <tr{cycle values=', class="TableSubHead"'}>
          <td>{$pm.name}</td>
          <td>{$pm.id}</td>
          <td>{if $pm.transactionTypes[$smarty.const.XPC_TRAN_TYPE_SALE]}{$lng.lbl_yes}{else}{$lng.lbl_no}{/if}</td>
          <td>{if $pm.transactionTypes[$smarty.const.XPC_TRAN_TYPE_AUTH]}{$lng.lbl_yes}{else}{$lng.lbl_no}{/if}</td>
          <td>{if $pm.transactionTypes[$smarty.const.XPC_TRAN_TYPE_CAPTURE]}{$lng.lbl_yes}{else}{$lng.lbl_no}{/if}</td>
          <td>{if $pm.transactionTypes[$smarty.const.XPC_TRAN_TYPE_VOID]}{$lng.lbl_yes}{else}{$lng.lbl_no}{/if}</td>
          <td>{if $pm.transactionTypes[$smarty.const.XPC_TRAN_TYPE_REFUND]}{$lng.lbl_yes}{else}{$lng.lbl_no}{/if}</td>
        </tr>
      {/foreach}

    </table>

    <br />
    <br />

    <input type="button" name="import" value="{$lng.lbl_xpc_import_payment_methods|strip_tags:false|escape}" onclick="javascript: self.location='configuration.php?option=XPayments_Connector&amp;mode=import';" />

    {if $pm_found}
      <br />
      {$lng.txt_xpc_import_payment_methods_warn}
    {/if}

  {/if}

{/if}
