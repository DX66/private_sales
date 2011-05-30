{*
$Id: help.tpl,v 1.1 2010/05/21 08:31:58 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<li>
  {$lng.lbl_help}
  <div>
    <a href="http://help.qtmsoft.com/index.php?title=X-Cart:FAQs" target="_blank">{$lng.lbl_xcart_faqs}</a>
    <a href="http://help.qtmsoft.com/index.php?title=X-Cart:User_manual_contents" target="_blank">{$lng.lbl_xcart_manuals}</a>
    <a href="http://forum.x-cart.com/" target="_blank">{$lng.lbl_community_forums}</a>
    <a href="http://secure.qtmsoft.com/" target="_blank">{$lng.lbl_support_helpdesk}</a>
    <a href="http://www.x-cart.com/software_license_agreement.html" target="_blank">{$lng.lbl_license_agreement}</a>
    {if $shop_evaluation}
      <a href="http://www.x-cart.com/purchasing_shopping_cart_software.html" target="_blank">{$lng.lbl_purchase_paid_license}</a>
    {/if}
    <a href="http://www.x-cart.com/services.html" target="_blank">{$lng.lbl_services}</a>
  </div>
</li>

