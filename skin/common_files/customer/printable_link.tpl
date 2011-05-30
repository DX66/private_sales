{*
$Id: printable_link.tpl,v 1.4 2010/08/02 10:12:15 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $printable_link_visible}
  <div class="printable-bar">
    <a href="{$php_url.url}?printable=Y{if $php_url.query_string ne ''}&amp;{$php_url.query_string|escape|amp}{/if}">{$lng.lbl_printable_version}</a>
  </div>
{/if}
