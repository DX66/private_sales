{*
$Id: quick_menu.tpl,v 1.1 2010/05/21 08:32:18 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $quick_menu}

<a name="menu"></a>
{capture name=dialog}

{$lng.txt_quick_menu_text}

<br /><br />

<table cellpadding="5" cellspacing="1">

{foreach key=group item=items from=$quick_menu}
<tr>
  <td class="FormButton">{$group}:</td>
  <td>
  <select style='width: 200px;' onchange="javascript: if (this.selectedIndex != 0) self.location=this.value;">
    <option value="">[{$lng.lbl_select_target|wm_remove|escape}]</option>
{section name=id loop=$items}
{if $items[id].title eq ""}
    <option value="">------------------------------------</option>
{else}
    <option value="{$items[id].link}">{$items[id].title}</option>
{/if}
{/section}
  </select>
  </td>
</tr>
{/foreach}

</table>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_quick_menu content=$smarty.capture.dialog extra='width="100%"'}

<br /><br />

{/if}

