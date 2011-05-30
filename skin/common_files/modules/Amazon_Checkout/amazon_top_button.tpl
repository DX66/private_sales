{*
$Id: amazon_top_button.tpl,v 1.1.2.1 2011/03/22 13:07:54 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $config.Amazon_Checkout.enable_amazon_top_button eq 'Y'}
<a href="cart.php?mode=acheckout"><img alt="" src="{$ImagesDir}/amazon_checkout.gif" /></a>
{/if}
