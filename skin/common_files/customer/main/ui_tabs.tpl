{*
$Id: ui_tabs.tpl,v 1.5.2.1 2011/03/09 10:47:40 aim Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript">
//<![CDATA[
$(function() {ldelim}
  var tOpts = {ldelim}
    idPrefix: '{$prefix|default:"ui-tabs-"}', cookie: {ldelim} expires: 1 {rdelim}{if $selected}, selected: '{$selected}'{/if}
  {rdelim};
  $('#{$prefix}container').tabs(tOpts);
{rdelim});
//]]>
</script>

<div id="{$prefix}container">

  <ul>
  {foreach from=$tabs item=tab key=ind}
    {inc value=$ind assign="ti"}
    <li><a href="{if $tab.url}{$tab.url|amp}{else}#{$prefix}{$tab.anchor|default:$ti}{/if}">{$tab.title|wm_remove|escape}</a></li>
  {/foreach}
  </ul>

  {foreach from=$tabs item=tab key=ind}
    {if $tab.tpl}
      {inc value=$ind assign="ti"}
      <div id="{$prefix}{$tab.anchor|default:$ti}">
        {include file=$tab.tpl nodialog=true}
      </div>
    {/if}
  {/foreach}

</div>
