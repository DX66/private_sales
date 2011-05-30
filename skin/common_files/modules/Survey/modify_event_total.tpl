{*
$Id: modify_event_total.tpl,v 1.1 2010/05/21 08:32:50 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="main/visiblebox_link.tpl" mark="total" title=$lng.lbl_survey_order_total_exceeds visible=$survey.event_elements.T}
<div id="boxtotal" style="padding-left: 20px;{if not $survey.event_elements.T} display: none;{/if}">

<table cellspacing="1" cellpadding="2">
<tr>
  <td>{$lng.lbl_order_total}:</td>
  <td><input type="text" size="10" name="new_element[T][0]" value="{if $survey.event_elements.T}{$survey.event_elements.T.0|formatprice}{else}{$zero}{/if}"/></td>
</tr>
</table>

</div>
