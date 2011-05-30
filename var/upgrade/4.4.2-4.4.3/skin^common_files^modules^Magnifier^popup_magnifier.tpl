{*
$Id: popup_magnifier.tpl,v 1.1 2010/05/21 08:32:43 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript" src="{$SkinDir}/modules/Magnifier/popup.js"></script>
<div class="magnifier-popup-link">
  <a href="popup_magnifier.php?productid={$product.productid}&amp;imageid=" onclick="javascript: winMagnifier = popup_magnifier('{$product.productid}','{$config.Magnifier.magnifier_width}','{$config.Magnifier.magnifier_height}'); return false;" target="_blank">{$lng.lbl_click_to_zoom}</a>
</div>
