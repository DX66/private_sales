{*
$Id: cc_xpc.tpl,v 1.1.2.1 2010/09/10 10:48:15 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>X-Payments payment methods</h1>

<br />
<br />

{capture name=dialog}

<img src="{$ImagesDir}/xpc_logo.png" width="130" height="55" alt="X-Payments logo" />

<br />
<br />
<br />

{$lng.txt_xpc_pm_config_note}

<br />
<br />

<table cellpadding="5" cellspacing="1" border="0">

  <tr class="TableHead">
    <td>{$lng.lbl_payment_method}</td>
    <td>{$lng.lbl_xpc_pm_id}</td>
    <td>{$lng.lbl_xpc_sale}</td>
    <td>{$lng.lbl_xpc_auth}</td>
    <td>{$lng.lbl_xpc_capture}</td>
    <td>{$lng.lbl_xpc_void}</td>
    <td>{$lng.lbl_xpc_refund}</td>
  </tr>

  {foreach from=$cc_processors item=pm}
  <tr{cycle values=', class="TableSubHead"'}>
    <td>{$pm.module_name}</td>
    <td>{$pm.param01}</td>
    <td>{if $pm.param06 eq "Y"}{$lng.lbl_yes}{else}{$lng.lbl_no}{/if}</td>
    <td>{if $pm.has_preauth eq "Y"}{$lng.lbl_yes}{else}{$lng.lbl_no}{/if}</td>
    <td>{if $pm.param02 eq "Y"}{$lng.lbl_yes}{else}{$lng.lbl_no}{/if}</td>
    <td>{if $pm.is_refund eq "Y"}{$lng.lbl_yes}{else}{$lng.lbl_no}{/if}</td>
  </tr>
  {/foreach}

</table>

<br />
<br />

{$lng.txt_xpc_pm_config_note_2}

<br />
<br />

<a href="configuration.php?option=XPayments_Connector">{$lng.lbl_xpc_xpayments_connector_settings}</a>

<br />
<br />

{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
