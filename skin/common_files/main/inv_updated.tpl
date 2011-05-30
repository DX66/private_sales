{*
$Id: inv_updated.tpl,v 1.2 2010/07/19 13:30:01 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $updated_items gt 0}
  {$lng.txt_inv_updated}
  <br />
{/if}
{if $err_rows}
  <font class="Star">{$lng.txt_inv_invalid_format}</font>
  <br />
  {foreach from=$err_rows item=err}
    <pre>{$err}</pre>
  {/foreach}
  <br />
{/if}
{include file="buttons/go_back.tpl"}
