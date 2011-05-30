{*
$Id: checkout_btn.tpl,v 1.1 2010/05/21 08:32:19 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<div align="center">
<a href="cart.php?mode=acheckout"><img alt="" src="https://{$amazon_host}/gp/cba/button?color=orange&cartOwnerId={$config.Amazon_Checkout.amazon_mid}&size={if $btn_size eq ''}large{else}{$btn_size}{/if}&background=white" /></a>
</div>
