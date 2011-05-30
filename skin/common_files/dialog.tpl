{*
$Id: dialog.tpl,v 1.1 2010/05/21 08:31:57 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $title}
  <h2>{$title}</h2>
{/if}
<table cellspacing="0" {$extra}>
<tr>
  <td class="DialogBorder">
    <table cellspacing="1" class="DialogBox">
      <tr>
        <td class="DialogBox" valign="{$valign|default:"top"}">
          {$content}&nbsp;
        </td>
      </tr>
    </table>
  </td>
</tr>
</table>
