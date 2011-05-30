{*
$Id: new_offers_message.tpl,v 1.1 2010/05/21 08:32:48 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $new_offers_message.content}
  {$new_offers_message.content}
{else}
  {xoffers_promo mode="random"}
{/if}
