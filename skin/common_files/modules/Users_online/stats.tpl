{*
$Id: stats.tpl,v 1.2.2.2 2011/01/20 08:03:46 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $statistics}
<table cellpadding="0" cellspacing="0" width="100%">
<tr>
  <td bgcolor="#000000">
<table cellpadding="2" cellspacing="1" width="100%">
<tr style="height: 16px;">
  <th class="TableHead" align="left" nowrap="nowrap" rowspan="2">{$lng.lbl_customer}</th>
  <th class="TableHead" align="left" nowrap="nowrap" colspan="2"><center>{$lng.lbl_date}</center></th>
  <th class="TableHead" nowrap="nowrap" rowspan="2">{$lng.lbl_current_page}</th>
  <th class="TableHead" nowrap="nowrap" rowspan="2">{$lng.lbl_cart_content}</th>
</tr>
<tr style="height: 16px;">
  <th class="TableHead" align="left" nowrap="nowrap">{$lng.lbl_first_entry}</th>
  <th class="TableHead" align="left" nowrap="nowrap">{$lng.lbl_last_entry}</th>
</tr>

{foreach from=$statistics item=v}
<tr>
  <td bgcolor="#FFFFFF" nowrap="nowrap" valign="top">{if $v.userinfo ne ''}<a href="{$catalogs.admin}/user_modify.php?user={$v.userinfo.id}&amp;usertype=C">{$v.userinfo.firstname} {$v.userinfo.lastname}</a>{if $v.userinfo.status eq 'A'}<br /><i>({$lng.lbl_anonymous_customer})</i>{/if}{else}{$lng.lbl_unregistered_customer}{/if}</td>
  <td bgcolor="#FFFFFF" nowrap="nowrap" valign="top" align="center">{$v.session_create_date|date_format:$config.Appearance.date_format}<br />{$v.session_create_date|date_format:$config.Appearance.time_format}</td>
  <td bgcolor="#FFFFFF" nowrap="nowrap" valign="top" align="center">{$v.current_date|date_format:$config.Appearance.date_format}<br />{$v.current_date|date_format:$config.Appearance.time_format}</td>
  <td bgcolor="#FFFFFF" valign="top"><a href="{$v.current_url_page}" target="_blank">{$v.display_url_page|amp}</a></td>
  <td bgcolor="#FFFFFF" nowrap="nowrap" valign="top">
  {if $v.products ne ''}
  <table cellpadding="2" cellspacing="1" width="100%">
  {foreach from=$v.products item=p}
  <tr{cycle values=", class='TableSubHead'" name=$v.last_date}>
    <td>
    <table cellpadding="2" cellspacing="1">
    <tr>
      <td nowrap="nowrap" valign="top"><a href="product_modify.php?productid={$p.productid}"><b>{$p.product}</a>:</b></td>
    </tr>
    <tr>
      <td>{$p.amount} x {currency value=$p.price} = {if $p.price gt 0}{multi assign="total" x=$p.amount y=$p.price}{else}{assign var="total" value=0}{/if}{currency value=$total}</td>
    </tr>
    </table>
  {if $p.product_options ne ''}
    <table cellpadding="0" cellspacing="1">
    <tr>
      <td width="15"><img src="{$ImagesDir}/spacer.gif" width="15" height="1" border="0" alt="" /></td>
      <td>{include file="modules/Product_Options/display_options.tpl" options=$p.product_options}</td>
    </tr>
    </table>
  {/if}
    </td>
  </tr>
  {/foreach}
  </table>
  {else}
  {/if}
  </td>
</tr>
{/foreach}

</table>
  </td>
</tr>
</table>

{else}

<br />
<div align="center">{$lng.txt_no_statistics}</div>

{/if}

