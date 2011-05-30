{*
$Id: product_event_invoice.tpl,v 1.1 2010/05/21 08:32:23 joy Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
{if $product.event_data ne ""}
{assign var=creator value="`$product.event_data.creator_title` `$product.event_data.firstname` `$product.event_data.lastname`"}
<div class="event-details">
  {$lng.lbl_giftreg_present_for|substitute:"event_name":$product.event_data.title:"eventid":$product.event_data.event_id:"creator":$creator}
</div>
{/if}
