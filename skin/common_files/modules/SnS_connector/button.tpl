{*
$Id: button.tpl,v 1.3 2010/06/08 06:17:41 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $config.SnS_connector.sns_display_button eq 'Y' and $sns_collector_path_url ne ''}

  {if $text_link ne "Y"}

    <div class="sns-button center">
      <img style="display: none;" onclick="javascript: window.open('{$sns_collector_path_url}/openChat.{$config.SnS_connector.sns_script_extension}', '_blank', 'status=yes,toolbar=no,menubar=no,location=no,width=500,height=400')" src="{$sns_collector_path_url}/operatorButton.js.{$config.SnS_connector.sns_script_extension}" id="snsOperatorButton" alt="Powered by Sales-n-Stats" />
<script type="text/javascript">
//<![CDATA[
if (document.getElementById('snsOperatorButton'))
  document.getElementById('snsOperatorButton').style.display = '';
//]]>
</script>
      <noscript>
        <a href="{$sns_collector_path_url}/leaveMessage.{$config.SnS_connector.sns_script_extension}?noscript=true" target="_blank"><img src="{$sns_collector_path_url}/operatorButton.js.{$config.SnS_connector.sns_script_extension}?script=no" alt="Powered by Sales-n-Stats" /></a>
      </noscript>
      <div class="text">
        <a href="http://www.sales-n-stats.com" target="_blank">Powered by Sales-n-Stats</a>
      </div>
    </div>

  {else}

    <script src="{$sns_collector_path_url}/operatorButton.js.{$config.SnS_connector.sns_script_extension}?mode=text" type="text/javascript"></script>
    <noscript>
      <a href="{$sns_collector_path_url}/leaveMessage.{$config.SnS_connector.sns_script_extension}?noscript=true" target="_blank">{$lng.lbl_sns_click_for_live_help}</a>
    </noscript>

  {/if}

{/if}
