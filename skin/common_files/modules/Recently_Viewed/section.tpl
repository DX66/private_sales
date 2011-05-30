{*
$Id: section.tpl,v 1.4 2010/06/29 14:20:06 igoryan Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
{if not ($smarty.cookies.robot eq 'X-Cart Catalog Generator' and $smarty.cookies.is_robot eq 'Y')}
  {include file="modules/Recently_Viewed/content.tpl"}
{/if}
