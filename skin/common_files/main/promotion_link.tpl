{*
$Id: promotion_link.tpl,v 1.1 2010/05/21 08:32:18 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<div class="promotion-cell">
  <div class="promotion-link">
  	<a title="{$title|escape:"html"}" href="{$href|amp}">{$title}</a>
  </div>
  {if $promo_note ne ""}
    <div class="promo-note">
      {$promo_note}
    </div>
  {/if}
</div>
