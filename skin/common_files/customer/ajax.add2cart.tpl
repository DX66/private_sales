{*
$Id: ajax.add2cart.tpl,v 1.2 2010/06/29 14:20:06 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if not ($smarty.cookies.robot eq 'X-Cart Catalog Generator' and $smarty.cookies.is_robot eq 'Y')}
{capture name="add2cart"}
var lbl_added = '{$lng.lbl_added|wm_remove|escape:javascript}';
var lbl_error = '{$lng.lbl_error|wm_remove|escape:javascript}';
var redirect_to_cart = {if $config.General.redirect_to_cart eq "Y"}true{else}false{/if};
{/capture}
{load_defer file="add2cart" direct_info=$smarty.capture.add2cart type="js"}
{load_defer file="js/ajax.add2cart.js" type="js"}
{load_defer file="js/ajax.product.js" type="js"}
{load_defer file="js/ajax.products.js" type="js"}
{/if}
