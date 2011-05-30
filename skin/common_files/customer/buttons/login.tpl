{*
$Id: login.tpl,v 1.1 2010/05/21 08:32:03 joy Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
{if $is_popup}
  {assign var="href" value="javascript: popupOpen('`$login_url`');"}
{else}
  {assign var="href" value="`$login_url`"}
{/if}
{assign var=bn_title value=$button_title|default:$lng.lbl_sign_in}
{include file="customer/buttons/button.tpl" button_title=$bn_title style="link" href=$href link_href="login.php"}
