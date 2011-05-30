{* $Id: event_message.tpl,v 1.1.2.1 2011/01/04 15:55:56 aim Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
{assign var=details value=$record.details}
<div class="subhead">
{$record.date_time|date_format:$config.Appearance.datetime_format}
{if not $order_status_changed and not $order_initial_status} <span class="status">({include file="main/order_status.tpl" mode="static" status=$details.new_status})</span>{/if}
:
</div>

<div class="subhead-comment">
{if $record.event_header}
  {$record.event_header}
{elseif $details.type eq "X" and not $details.diff.X and $details.comment ne ""}
  {$lng.lbl_aom_comment_added}
{elseif $details.type ne "R"}
  {$lng.lbl_aom_order_modified}
{else}
  {if $details.status_changed ne ""}
    {$lng.lbl_aom_return_status_changed_to|substitute:"returnid":$details.returnid} <strong>{include file="modules/RMA/return_status.tpl" mode="static" status=$details.status_changed}</strong><br />
  {/if}
  {if $details.credit and $details.amount}
    {$lng.lbl_aom_credit_created|substitute:"returnid":$details.returnid:"amount":$details.amount:"gcid":$details.gcid:"curr":$config.General.currency_symbol}
  {elseif $details.deleted ne ""}
    {$lng.lbl_aom_return_deleted|substitute:"returnid":$details.returnid}
  {elseif $details.added ne ""}
    {$lng.lbl_aom_return_added|substitute:"returnid":$details.returnid}
  {else}
    {$lng.lbl_aom_return_modified|substitute:"returnid":$details.returnid}
  {/if}
{/if}
{if $record.login ne ''} {$lng.lbl_aom_by|substitute:"user":$record.login}{/if}
{if $record.status_note}
  <div class="subhead-note">{$record.status_note}</div>
{/if}
</div>

{assign var=diff value=$details.diff}
{*** common data ***}
{if $diff.X ne ""}
  <ul>
  {foreach from=$diff.X key=field item=val}
    {if $field eq "details" or $field eq "customer_notes" or $field eq "notes"}
      {assign var=val value="<p>`$val`</p>"}
    {/if}
    <li>{$lng.lbl_aom_changed_to|substitute:"property":$fields[$field]:"value":$val|nl2br}</li>
  {/foreach}
  </ul>
{/if}
{*** /common data ***}

{*** customer info ****}
{if $diff.U ne ""}
  <div class="section-subhead">{$lng.lbl_aom_customer_information}:</div>
  <ul>
  {foreach from=$diff.U key=field item=val}
    {if $field eq "membershipid"}
      <li>{$lng.lbl_aom_changed_to|substitute:"property":$fields[$field]:"value":$memberships[$val].membership}</li>
    {elseif $field|regex_replace:"/^[bs]_/":"" eq "country"}
      {assign var=country value=$countries.$val|default:$lng.lbl_other}
      <li>{$lng.lbl_aom_changed_to|substitute:"property":$fields[$field]:"value":$country}</li>
    {elseif $field|regex_replace:"/^[bs]_/":"" eq "state"}
      {assign var=country_field value=$field|regex_replace:"/state/":"country"}
      {assign var=country value=$diff.U.$country_field}
      {assign var=state value=$states.$country.$val|default:$lng.lbl_other}
      <li>{$lng.lbl_aom_changed_to|substitute:"property":$fields[$field]:"value":$state}</li>
    {else}
      <li>{$lng.lbl_aom_changed_to|substitute:"property":$fields[$field]:"value":$val}</li>
    {/if}
  {/foreach}
  </ul>
{/if}
{***/ customer info ****}

{*** products ****}
{if $diff.P ne ""}
  <div class="section-subhead">{$lng.lbl_aom_ordered_products}:</div>
  <ul>
  {foreach from=$diff.P key=k item=product}
    {if $product.deleted}
      <li>{$lng.lbl_aom_product_deleted|substitute:"product":$product.productcode}</li>
    {elseif $product.new}
      <li>{$lng.lbl_aom_product_added|substitute:"product":$product.productcode}</li>
    {else}
      {if $product.price ne $product.old_price}
        <li>{$lng.lbl_aom_product_price_changed|substitute:"product":$product.productcode:"value":"`$config.General.currency_symbol``$product.price`"}</li>
      {/if}
      {if $product.amount ne $product.old_amount}
        <li>{$lng.lbl_aom_product_qty_changed|substitute:"product":$product.productcode:"value":$product.amount}</li>
      {/if}
    {/if}
  {/foreach}
  </ul>
{/if}
{***/ products ****}

{*** giftcerts ****}
{if $diff.G ne ""}
  <div class="section-subhead">{$lng.lbl_aom_ordered_giftcerts}:</div>
  <ul>
  {foreach from=$diff.G key=k item=gc}
    {if $gc.deleted ne ""}
      <li>{$lng.lbl_aom_gc_deleted|substitute:"gc":$gc.gcid}
    {elseif $gc.amount ne $gc.old_amount}
      <li>{$lng.lbl_aom_gc_amount_changed|substitute:"gc":$gc.gcid:"value":"`$config.General.currency_symbol``$val`"}</li>
    {/if}
  {/foreach}
  </ul>
{/if}
{***/ giftcerts ****}

{*** totals ****}
{if $diff.T ne ""}
  <div class="section-subhead">{$lng.lbl_aom_order_totals}:</div>
  <ul>
  {foreach from=$diff.T key=field item=val}
    {if $field eq "coupon"}
      <li>{$lng.lbl_aom_coupon_applied|substitute:"value":$val}</li>
    {elseif $field ne "shipping" and $field ne "payment_method"}
      <li>{$lng.lbl_aom_changed_to|substitute:"property":$fields[$field]:"value":"`$config.General.currency_symbol``$val`"}</li>
    {else}
      {assign var=val value=$val|trademark:"use_alt"}
      <li>{$lng.lbl_aom_changed_to|substitute:"property":$fields[$field]:"value":$val}</li>
    {/if}
  {/foreach}
  </ul>
{/if}
{*** /totals ****}

{*** RMA ****}
{if $diff.R ne ""}
  <div class="section-subhead">{$lng.lbl_aom_return_details|substitute:"returnid":$details.returnid}:</div>
  <ul>
  {foreach from=$diff.R key=field item=val}
    {if $field eq "reason"}
      <li>{$lng.lbl_aom_changed_to|substitute:"property":$fields[$field]:"value":$rma_reasons[$val]}</li>
    {elseif $field eq "action"}
      <li>{$lng.lbl_aom_changed_to|substitute:"property":$fields[$field]:"value":$rma_actions[$val]}</li>
    {elseif $field eq "comment"}
      {assign var=val value=$val|nl2br}
      <li>{$lng.lbl_aom_changed_to|substitute:"property":$fields[$field]:"value":"<br /><i>`$val`</i>"}</li>
    {elseif $field eq "amount"}
      <li>{$lng.lbl_aom_changed_to|substitute:"property":$fields[$field]:"value":$val}</li>
    {/if}
  {/foreach}
  </ul>
{/if}
{*** /RMA ****}

{if $details.comment ne "" and ($usertype ne "C" or $details.is_public eq "Y")}
  <div class="section-subhead">{$lng.lbl_aom_comment}:</div>
  <p>{$details.comment|nl2br}</p>
{/if}
<br />
