{*
$Id: welcome.tpl,v 1.1.2.2 2010/08/09 06:39:52 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $display_greet_visitor_name}

  <h1>{$lng.lbl_welcome_back|substitute:"name":$display_greet_visitor_name} </h1>

{elseif $lng.lbl_site_title}

  <h1>{$lng.lbl_welcome_to|substitute:"company":$lng.lbl_site_title|amp}</h1>

{else}

  <h1>{$lng.lbl_welcome_to|substitute:"company":$config.Company.company_name|amp}</h1>

{/if}

{$lng.txt_welcome}<br />

{if $active_modules.Bestsellers and $config.Bestsellers.bestsellers_menu ne "Y"}
  {include file="modules/Bestsellers/bestsellers.tpl"}<br />
{/if}

{include file="customer/main/featured.tpl"}
