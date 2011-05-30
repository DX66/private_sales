{*
$Id: permanent_warning.tpl,v 1.1 2010/05/21 08:31:58 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $permanent_warning}
{strip}
<ol class="pw">
{foreach from=$permanent_warning item=pw key=k}
<li{if $k eq 0} class="first-child"{/if}>{$pw}</li>
{/foreach}
</ol>
{/strip}
{/if}
