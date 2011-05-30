{*
$Id: menu.tpl,v 1.2.2.1 2010/08/09 07:14:52 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{capture name=menu}

  <ul>
    <li><a href="help.php">{$lng.lbl_help_zone}</a></li>
    <li><a href="help.php?section=contactus&amp;mode=update">{$lng.lbl_contact_us}</a></li>
    {foreach from=$pages_menu item=p}
      {if $p.show_in_menu eq 'Y'}
        <li><a href="pages.php?pageid={$p.pageid}">{$p.title|amp}</a></li>
      {/if}
    {/foreach}
  </ul>

{/capture}
{include file="customer/menu_dialog.tpl" title=$lng.lbl_need_help content=$smarty.capture.menu additional_class="menu-help"}
