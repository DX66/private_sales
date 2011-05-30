{*
$Id: offer_conditions.tpl,v 1.1 2010/05/21 08:32:48 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<table cellpadding="3" cellspacing="1" width="100%">

<tr>
  <td>
<form action="offers.php" method="post" name="wizardform">
<input type="hidden" name="mode" value="conditions" />
<input type="hidden" name="action" value="delete" />
<input type="hidden" name="offerid" value="{$offerid}" />

{include file="modules/Special_Offers/wizard_step_w_list.tpl" items=$conditions}

</form>
  </td>
</tr>
</table>
