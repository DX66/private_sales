{*
$Id: pages.tpl,v 1.2.2.1 2010/10/21 13:48:30 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

<h1>{$page_data.title|amp}</h1>

{capture name=dialog}

  {if $page_content ne ''}

    {if $config.General.parse_smarty_tags eq "Y"}
      {eval var=$page_content}
    {else}
      {$page_content|amp}
    {/if}

  {/if}

{/capture}
{include file="customer/dialog.tpl" title=$page_data.title content=$smarty.capture.dialog noborder=true}
