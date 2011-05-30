{*
$Id: tab.tpl,v 1.1 2010/05/21 08:32:02 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<table cellpadding="0" cellspacing="0" dir="ltr">
<tr>
<td class="TabLeftCornerTop"><img src="{$ImagesDir}/spacer.gif" class="TabCorner" alt="" /></td>
<td class="TabTop"><img src="{$ImagesDir}/spacer.gif" width="85" height="5" alt="" /></td>
<td class="TabRightCornerTop"><img src="{$ImagesDir}/spacer.gif" class="TabCorner" alt="" /></td>
</tr>
<tr>
<td class="TabLeftSide"><img src="{$ImagesDir}/spacer.gif" class="TabSide" alt="" /></td>
<td class="Tab"{$reading_direction_tag}><a href="{$link}">{$title}</a></td>
<td class="TabRightSide"><img src="{$ImagesDir}/spacer.gif" class="TabSide" alt="" /></td>
</tr>
<tr>
<td class="TabLeftCornerBot"><img src="{$ImagesDir}/spacer.gif" class="TabCorner" alt="" /></td>
<td class="TabCenter"><img src="{$ImagesDir}/spacer.gif" class="TabCorner" alt="" /></td>
<td class="TabRightCornerBot"><img src="{$ImagesDir}/spacer.gif" class="TabCorner" alt="" /></td>
</tr>
</table>
