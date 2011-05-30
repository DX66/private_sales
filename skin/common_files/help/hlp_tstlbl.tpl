{*
$Id: hlp_tstlbl.tpl,v 1.1 2010/05/21 08:32:05 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{*$Id: hlp_tstlbl.tpl,v 1.1 2010/05/21 08:32:05 joy Exp $*}
<table>
<tr>
  <td>
  {$lng.usps_labels_help_1}
  <br />
  <i>{$tmp_dir}</i>
  <br /><br />
  <form action="usps_test.php" method="post">
    <input type="submit" value="{$lng.lbl_get_usps_test_labels|escape}"{if $config.Shipping_Label_Generator.usps_userid eq ''} disabled="disabled"{/if} /><br />
    {if $status eq 'E' and $error}
      <font color="#ff0000">
        {$lng.lbl_usps_labels_error}
        <br />
        {foreach from=$error item="error_txt" key="method"}
        <b>{$method}</b>: {$error_txt}<br />
        {/foreach}
      </font>
      
    {elseif $status eq 'S'}
      <font color="#087e27">{$lng.lbl_usps_labels_success}</font>
    {/if}
  </form>
  <br />
  {$lng.usps_labels_help_2}
  </td>  
</tr>
</table>
