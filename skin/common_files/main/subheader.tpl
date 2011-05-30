{*
$Id: subheader.tpl,v 1.1 2010/05/21 08:32:18 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $class eq 'grey'}
<table cellspacing="0" class="SubHeaderGrey">
<tr>
  <td class="SubHeaderGrey">{$title}</td>
</tr>
<tr>
  <td class="SubHeaderGreyLine"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>
</table>
{elseif $class eq "red"}
<table cellspacing="0" class="SubHeaderRed">
<tr>
  <td class="SubHeaderRed">{$title}</td>
</tr>
<tr>
  <td class="SubHeaderRedLine"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /><br /></td>
</tr>
</table>
{elseif $class eq "black"}
<table cellspacing="0" class="SubHeaderBlack">
<tr>
  <td class="SubHeaderBlack">{$title}</td>
</tr>
<tr>
  <td class="SubHeaderBlackLine"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /><br /></td>
</tr>
</table>
{else}
<table cellspacing="0" class="SubHeader">
<tr>
  <td class="SubHeader">{$title}</td>
</tr>
<tr>
  <td class="SubHeaderLine"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /><br /></td>
</tr>
</table>
{/if}

