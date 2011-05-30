{*
$Id: giftreg_notification.tpl,v 1.1.2.1 2010/12/15 09:44:39 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if not $display_only_body}
  {config_load file="$skin_config"}
  {include file="mail/html/mail_header.tpl"}
{/if}
{if $mail_data}
<br />{$mail_data.message|nl2br}
{else}
<br />{$lng.eml_giftreg_notification}

{foreach from=$wl_products item=wl_product}
<br />===========================================

<br />{$wl_product.product}

<br />{$wl_product.descr|truncate:200:"..."}

<br />{$lng.lbl_price}: {currency value=$wl_product.price plain_text_message="Y"}

{/foreach}

{if $wl_giftcerts ne ""}

{foreach from=$wl_giftcerts item=gc key=gcindex}

{if $g.amount_purchased lte 1}
<br />===========================================

<br />{$lng.lbl_gift_certificate}

<br />{$lng.lbl_recipient}: {$gc.recipient}

{if $gc.send_via eq "E"}
<br />{$lng.lbl_email}: {$gc.recipient_email}
{elseif $gc.send_via eq "P"}
<br />{$lng.lbl_mail_address}: 
<p/ >{$gc.recipient_address}, {$gc.recipient_city}, {if $config.General.use_counties eq "Y"}{$gc.recipient_countyname} {/if}{$gc.recipient_state}
<br />{$gc.recipient_country} {$gc.recipient_zipcode}
{if $gc.recipient_phone}
<br />{$lng.lbl_phone}: {$gc.recipient_phone}
{/if}
{/if}

<br />{$lng.lbl_amount}: {currency value=$gc.amount plain_text_message="Y"}

{/if}
{/foreach}
{/if}

<br />===========================================

<br />{$lng.eml_giftreg_click_to_view}:

<br /><a href="{$catalogs.customer}/giftregs.php?eventid={$eventid}&amp;wlid={$wlid}">{$catalogs.customer}/giftregs.php?eventid={$eventid}&wlid={$wlid}</a>
{/if}
{if not $display_only_body}
{include file="mail/html/signature.tpl"}
{/if}
