{*
$Id: news_archive.tpl,v 1.1 2010/05/21 08:32:45 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $news_messages eq ""}
{$lng.txt_no_news_available}
{else}
{section name=idx loop=$news_messages}
{capture name=dialog}
<b>{$news_messages[idx].subject}</b>
<br /><br />
{if $news_messages[idx].allow_html eq "N"}
{$news_messages[idx].body|replace:"\n":"<br />"}
{else}
{$news_messages[idx].body}
{/if}
{/capture}
{include file="dialog.tpl" title=$news_messages[idx].date|date_format:$config.Appearance.date_format content=$smarty.capture.dialog extra='width="100%"'}
<br />
{/section}
{/if}
