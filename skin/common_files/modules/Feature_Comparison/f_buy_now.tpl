{*
$Id: f_buy_now.tpl,v 1.1 2010/05/21 08:32:21 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<a class="fcomp-real-image" {if $js_link eq "Y"}href="javascript:void(0);" onclick="{$href} show_fake_image(this);"{else}href="{$href}"{/if} ><img src="{$ImagesDir}/spacer.gif" class="fcomp-real-image fcomp-in-cart" alt="" /></a>{if $fake_image eq "Y"}<img src="{$ImagesDir}/spacer.gif" class="fcomp-fake-image fcomp-in-cart" alt="" />{/if}
