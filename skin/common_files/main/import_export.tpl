{*
$Id: import_export.tpl,v 1.1 2010/05/21 08:32:17 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $mode eq "export"}
{include file="page_title.tpl" title=$lng.lbl_export_data}

{else}
{include file="page_title.tpl" title=$lng.lbl_import_data}
{/if}

{$lng.txt_import_data_top_text}

<br /><br />

<br />

{if $mode eq "export"}
{include file="main/export.tpl"}

{else}
{include file="main/import.tpl"}
{/if}

