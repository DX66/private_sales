{*
$Id: pages_menu.tpl,v 1.1 2010/05/21 08:31:58 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{section name=pg loop=$pages_menu}
  <a href="pages.php?pageid={$pages_menu[pg].pageid}" class="VertMenuItems">{$pages_menu[pg].title}</a><br />
{/section}
