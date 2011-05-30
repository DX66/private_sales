{*
$Id: customer_display_bar.tpl,v 1.1 2010/05/21 08:32:49 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<img src="{$ImagesDir}/spacer.gif" alt="" class="survey-bar" {if $width gt 0} style="width: {$width}px;"{/if} />
<span class="survey-bar-label">{$percent}%</span>
