{*
$Id: dialog_tools.tpl,v 1.4 2010/07/12 13:08:18 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $dialog_tools_data}

{assign var="left" value=$dialog_tools_data.left}
{assign var="right" value=$dialog_tools_data.right}

{/if}

<table cellpadding="0" cellspacing="0" width="100%" class="dialog-tools-table">

<tr>
  <td height="40" valign="top">

{if ($top_message.type eq "" or $top_message.type eq "I") and $newid eq "" and $top_message.content ne ""}
  <div class="top-message-info hidden ui-corner-all" onclick="javascript: $(this).hide();">

    {if $top_message.content}
      {$top_message.content}
      {if $top_message.anchor ne ""}
        <div class="anchor">
          <a href="#{$top_message.anchor}">{$lng.lbl_go_to_last_edit_section}<img src="{$ImagesDir}/spacer.gif" alt="" /></a>
        </div>
      {/if}
    {/if}

    {assign var="top_message" value=""}
  </div>
{/if}

  </td>
</tr>

{if $dialog_tools_data}
<tr>
  <td>

  <div class="dialog-tools">

      <ul class="dialog-tools-header">
{if $left}
        <li class="dialog-header-left{if $dialog_tools_data.show eq "right"} dialog-tools-nonactive{/if}" onclick="javascript: dialog_tools_activate('left', 'right');">
        {if $left.title}{$left.title}{else}{$lng.lbl_in_this_section}{/if}
        </li>
{/if}
{if $right}
        <li class="dialog-header-right{if $left and $dialog_tools_data.show ne "right"} dialog-tools-nonactive{/if}" onclick="javascript: dialog_tools_activate('right', 'left');">
        {if $right.title}{$right.title}{else}{$lng.lbl_see_also}{/if}
        </li>
{/if}
      </ul>

    <div class="clearing">&nbsp;</div>

    <div class="dialog-tools-box">

{if $left}
{if $left.data}
{assign var=left value=$left.data}
{/if}
      <ul class="dialog-tools-content dialog-tools-left{if $dialog_tools_data.show eq "right"} hidden{/if}">
{foreach from=$left item=cell}
      {include file="dialog_tools_cell.tpl" cell=$cell}
{/foreach}
      </ul>
{/if}

{if $right}
{if $right.data}
{assign var=right value=$right.data}
{/if}
      <ul class="dialog-tools-content dialog-tools-right{if $left and $dialog_tools_data.show ne "right"} hidden{/if}">
{foreach from=$right item=cell}
      {include file="dialog_tools_cell.tpl" cell=$cell}
{/foreach}
      </ul>
{/if}

    </div>

  </div>

  </td>
</tr>
{/if}

</table>
