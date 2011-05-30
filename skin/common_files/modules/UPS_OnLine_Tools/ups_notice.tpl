{*
$Id: ups_notice.tpl,v 1.1 2010/05/21 08:32:50 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $usertype eq "C" or ($usertype ne "C" and $main eq "order_edit")}

<table summary="{$lng.lbl_ups_online_tools|escape}">
  <tr>
    <td><div class="ups-logo-cell">{include file="modules/UPS_OnLine_Tools/ups_logo.tpl"}</div></td>
    <td class="small-note ups-notice">
      {if $ups_av_error ne ""}
        {$lng.txt_ups_av_notice}
      {else}
        {$lng.txt_ups_rates_notice|substitute:"company":$config.Company.company_name}
      {/if}
      <br /><br />
      {$lng.txt_ups_trademark_text}
    </td>
  </tr>
</table>

{else}

<div align="center">{$lng.txt_ups_trademark_text}</div>

{/if}
