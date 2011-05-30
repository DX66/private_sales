{*
$Id: clean_urls.tpl,v 1.3 2010/07/21 11:58:50 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{* vim: set ts=2 sw=2 sts=2 et: *}
{if $clean_urls_history}

  {include file="check_clean_url.tpl"}

  <a name="clean_url_history"></a>

  {capture name=dialog}

  <script type="text/javascript" language="JavaScript 1.2">//<![CDATA[
  var clean_urls_history = new Array({foreach from=$clean_urls_history item=v key=k}'clean_urls_history[{$k}]',{/foreach}'');
  //]]></script>

  {if $config.SEO.clean_urls_enabled eq "Y"}
    <form action="{$clean_url_action}" method="post" name="clean_urls_history_form">
    <input type="hidden" name="{$resource_name}" value="{$resource_id}" />
    <input type="hidden" name="mode" value="{$clean_urls_history_mode}" />
    {include file="main/check_all_row.tpl" style="line-height: 170%;" form="clean_urls_history_form" prefix="clean_urls_history"}
  {/if}

  <table cellpadding="2" cellspacing="1" border="0">
    <tr class="TableHead">
      {if $config.SEO.clean_urls_enabled eq "Y"}
        <th width="15">&nbsp;</th>
      {/if}
      <th>{$lng.lbl_clean_url_value}</th>
    </tr>
    {foreach from=$clean_urls_history item=url key=kurl}
      <tr{cycle values=" , class='TableSubHead'"}>
        {if $config.SEO.clean_urls_enabled eq "Y"}
          <td valign="top"><input type="checkbox" name="clean_urls_history[{$kurl}]" value="{$kurl|escape}" /></td>
        {/if}
        <td valign="top" width="300">{$url}</td>
      </tr>
    {/foreach}
    {if $config.SEO.clean_urls_enabled eq "Y"}
      <tr>
        <td colspan="2">
          <input type="button" value="{$lng.lbl_delete_selected}" onclick="javascript: if (checkMarks(this.form, new RegExp('clean_urls_history', 'ig'))) document.clean_urls_history_form.submit();" />
        </td>
      </tr>
    {/if}
  </table>
  {if $config.SEO.clean_urls_enabled eq "Y"}
    </form>
  {/if}
  {/capture}
  {include file="dialog.tpl" title=$lng.lbl_clean_url_history content=$smarty.capture.dialog extra='width="100%"'}
{/if}
