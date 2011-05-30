{*
$Id: general.tpl,v 1.1 2010/05/21 08:32:03 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

<h1>{$lng.lbl_help_zone}</h1>

<p class="text-block">{$lng.txt_help_zone_title}</p>

{capture name=dialog}

  <ul class="help-index">

    <li class="first-item">{include file="customer/buttons/button.tpl" button_title=$lng.lbl_recover_password href="help.php?section=Password_Recovery" style="link"}</li>
    <li>{include file="customer/buttons/button.tpl" button_title=$lng.lbl_contact_us href="help.php?section=contactus&mode=update" style="link"}</li>

    {foreach from=$pages_menu item=p name=pages}
      <li>{include file="customer/buttons/button.tpl" button_title=$p.title href="pages.php?pageid=`$p.pageid`" style="link"}</li>
    {/foreach}

  </ul>

{/capture}
{include file="customer/dialog.tpl" title=$lng.lbl_help_zone content=$smarty.capture.dialog noborder=true}
