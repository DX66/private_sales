{*
$Id: 403.tpl,v 1.1 2010/05/21 08:32:03 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<div class="subcontainer">
  <div class="code-number">403</div>
  <div class="description">

    <h1>{$lng.err_access_denied}</h1>
    <p class="reason">{$lng.err_access_denied_msg}</p>

    {if $id}
      <p>Id: {$id}</p>
    {/if}

    <hr />
    {$lng.txt_403_links_note}:
    <ul class="links">
      <li><a href="cart.php">{$lng.lbl_view_cart}</a></li>
      <li><a href="help.php">{$lng.lbl_help}</a></li>
    </ul>
  </div>
</div>
