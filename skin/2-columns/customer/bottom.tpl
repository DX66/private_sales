{*
$Id: bottom.tpl,v 1.5.2.1 2010/08/09 07:14:52 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<div class="box">
  <ul class="helpbox">
    <li><a href="help.php?section=contactus&amp;mode=update">{$lng.lbl_contact_us}</a></li>
    {foreach from=$pages_menu item=p}
      {if $p.show_in_menu eq 'Y'}
        <li><a href="pages.php?pageid={$p.pageid}">{$p.title|amp}</a></li>
      {/if}
    {/foreach}
  </ul>

  <div class="subbox">
    <div class="left">{include file="main/prnotice.tpl"}</div>
    <div class="right">{include file="copyright.tpl"}</div>
  </div>
</div>
