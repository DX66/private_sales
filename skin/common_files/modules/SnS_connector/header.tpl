{*
$Id: header.tpl,v 1.1 2010/05/21 08:32:47 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $sns_collector_path_url ne ''}
  <script src="{$sns_collector_path_url}/tracker.js.{$config.SnS_connector.sns_script_extension}" type="text/javascript"></script>
  <noscript>
    <img style="display: none" src="{$sns_collector_path_url}/static.{$config.SnS_connector.sns_script_extension}" alt="" />
  </noscript>
{/if}

