{*
$Id: product_event.tpl,v 1.1 2010/05/21 08:32:23 joy Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
{if $product.event_data ne ""}
{assign var=creator value="`$product.event_data.creator_title` `$product.event_data.firstname` `$product.event_data.lastname` (<a href=\"user_modify.php?user=`$product.event_data.userid`&amp;usertype=C\">`$product.event_data.login`</a>)"}
<tr>
  <td colspan="2" class="product-event">
    {$lng.lbl_giftreg_event_note|substitute:"event_name":$product.event_data.title:"eventid":$product.event_data.event_id:"customer":$product.event_data.login:"creator":$creator}</span>
  </td>
</tr>
{/if}
