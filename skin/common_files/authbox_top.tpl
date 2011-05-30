{*
$Id: authbox_top.tpl,v 1.5.2.1 2010/08/30 07:33:23 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<table cellpadding="2" cellspacing="0" border="0">
<tr>
  {if $login ne '' and $usertype eq 'B'}
    <td nowrap="nowrap" height="20" valign="top" class="partnerid-info">
      {$lng.lbl_your_partner_id}: <strong>{$logged_userid}</strong>
    </td>
  {/if}

  <td nowrap="nowrap" height="20" valign="top">
    {if $config.General.shop_closed eq "Y"}
      <div class="closed-store">{$lng.lbl_close_storefront|substitute:"STOREFRONT":$http_location:"SHOPKEY":$config.General.shop_closed_key}{if $need_storefront_link} [ <a href="{$storefront_link}">{$lng.lbl_open}</a> ]{/if}</div>
    {else}
      <div class="open-store">{$lng.lbl_open_storefront|substitute:"STOREFRONT":$http_location}{if $need_storefront_link} [ <a href="javascript:void(0);" onclick="javascript:if(confirm('{$lng.lbl_open_storefront_warning|wm_remove|escape:'javascript'}'))window.location='{$storefront_link|amp}';">{$lng.lbl_close}</a> ]{/if}</div>
    {/if}
  </td>

  <td class="AuthText" height="20" valign="top">
    <a href="{$current_area}/register.php?mode=update">{$fullname}</a>
  </td>

  <td valign="top" class="auth-text-wrapper">
    [ <a href="login.php?mode=logout" class="AuthText">{$lng.lbl_logoff}</a> ]
  </td>

  {if $need_quick_search eq "Y"}

    <td width="50">&nbsp;</td>

    <td class="quick-search-form" valign="top">
      <form name="qsform" action="" onsubmit="javascript: quick_search($('#quick_search_query').val()); return false;">
        <input type="text" class="default-value" id="quick_search_query" onkeypress="javascript:$('#quick_search_panel').hide();" onclick="javascript:$('#quick_search_panel').hide();" value="{$lng.lbl_keywords|escape}" />
      </form>
    </td>

    <td class="main-button">
      <button class="quick-search-button" onclick="javascript:quick_search($('#quick_search_query').val());return false;">{$lng.lbl_search}</button>

      {include file="main/tooltip_js.tpl" text=$lng.txt_how_quick_search_works id="qs_help" type="img" sticky=true alt_image="question_gray.png" wrapper_tag="div"}
    </td>
{/if}

</tr>
</table>
