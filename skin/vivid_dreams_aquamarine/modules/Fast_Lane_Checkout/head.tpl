{*
$Id: head.tpl,v 1.2 2010/06/28 08:00:14 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<div class="checkout-head-bg">
  <div class="checkout-head-bg2">
    <div class="phones">
      {if $config.Company.company_phone}
        <span>{$lng.lbl_phone_1_title}: {$config.Company.company_phone}</span>
      {/if}

      {if $config.Company.company_phone_2}
        <span>{$lng.lbl_phone_2_title}: {$config.Company.company_phone_2}</span>
      {/if}
    </div>
    <div class="clearing"></div>
    <div class="logo"><a href="{$catalogs.customer}/home.php"><img src="{$AltImagesDir}/vivid_dreams/logo_check.gif" alt="" /></a></div>
    {if $speed_bar}
  		<div class="flc-tabs-top">
	  	  <ul>
		  	{foreach from=$speed_bar item=sb name="tabs"}
			    <li{if $smarty.foreach.tabs.first} class="last"{/if}><a href="{$sb.link|amp}">{$sb.title}</a></li>
  			{/foreach}
	  	  </ul>
		  </div>
  	{/if}

{if $active_modules.SnS_connector and $sns_collector_path_url ne '' and $config.SnS_connector.sns_display_button eq 'Y'}
  <div class="checkout-sns-button">
    <div class="valign-middle">
      <img src="{$ImagesDir}/rarrow.gif" alt="" /><strong>{include file="modules/SnS_connector/button.tpl" text_link="Y"}</strong>
    </div>
  </div>
{/if}

{if $login ne ""}
  <div class="checkout-top-login">

    <form action="{$authform_url}" method="post" name="toploginform">
      <input type="hidden" name="mode" value="logout" />
      <input type="hidden" name="redirect" value="{$redirect|amp}" />
      <input type="hidden" name="usertype" value="{$auth_usertype|escape}" />

      <span class="checkout-top-login-text">
        <strong><a href="register.php?mode=update" title="{$lng.lbl_my_account|escape}">{$fullname|default:$login}</a></strong>
      </span>
      {include file="customer/buttons/logout_menu.tpl" additional_button_class="menu-button3" style="link"}

    </form>

  </div>
{/if}

  </div>
</div>
