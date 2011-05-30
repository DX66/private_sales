{*
$Id: wishlists_pcounts.tpl,v 1.1 2010/05/21 08:32:23 joy Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
<br /><br />
<strong>{$lng.lbl_gift_registry}</strong>:
<ul class="wishlists-events">
{foreach from=$pcounts item=count key=eventid}
{if $eventid gt 0}
<li>{$events[$eventid].title}: <a href="wishlists.php?mode=wishlist&amp;customer={$v.userid}&amp;eventid={$eventid}">{$lng.lbl_n_items|substitute:"items":$count}</a></li>
{/if}
{/foreach}
</ul>
