{*
$Id: popup_magnifier.tpl,v 1.1.2.1 2011/03/11 15:05:43 ferz Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript" src="{$SkinDir}/modules/Magnifier/popup.js"></script>
<div class="magnifier-popup-link">
  <a href="popup_magnifier.php?productid={$product.productid}&amp;imageid=" onclick="javascript: winMagnifier = popup_magnifier('{$product.productid}',{$config.Magnifier.magnifier_width}+40,{$config.Magnifier.magnifier_height}+50); return false;" target="_blank">{$lng.lbl_click_to_zoom}</a>
</div>
