{*
$Id: offers.tpl,v 1.4 2010/07/21 11:58:50 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{capture name="dialog"}

<script type="text/javascript">
//<![CDATA[
var txt_sp_delete_offers_text = "{$lng.txt_sp_delete_offers_text|wm_remove|escape:javascript}";
//]]>
</script>

{if $offers ne ""}

<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
checkboxes_form = 'selectoffersform';
checkboxes = new Array({foreach from=$offers item=v key=k}{if $k gt 0},{/if}'to_delete[{$v.offerid}]'{/foreach});

function delete_offers(form) {ldelim}

  if ((checkMarks(form, new RegExp('^to_delete', 'gi'))) && confirm(txt_sp_delete_offers_text)) {ldelim}
    submitForm(form, 'delete');
  {rdelim}

{rdelim}
 
//]]> 
</script>
<script type="text/javascript" src="{$SkinDir}/js/change_all_checkboxes.js"></script>

<div style="line-height:170%"><a href="javascript:change_all(true);">{$lng.lbl_check_all}</a> / <a href="javascript:change_all(false);">{$lng.lbl_uncheck_all}</a></div>

{/if}

<form action="offers.php" method="post" name="selectoffersform">
<input type="hidden" name="mode" value="update" />
<table cellpadding="3" cellspacing="1" width="100%">

<tr class="TableHead">
<td width="15">&nbsp;</td>
<td width="70%">{$lng.lbl_sp_offer_short_name}</td>
<td width="10%">{$lng.lbl_sp_offer_functioning}</td>
<td width="20%" align="center">{$lng.lbl_active}</td>
</tr>

{if $offers ne ""}

{foreach name=offers from=$offers item=offer}
{if $offer.valid}
  {if $offer.expired}
    {assign var="tmp_title" value=$lng.lbl_sp_offer_status_expired}
    {assign var="tmp_link_style" value='style="COLOR: black;" '}
  {else}
    {if $offer.upcoming}
      {assign var="tmp_title" value=$lng.lbl_sp_offer_status_upcoming}
    {else}
      {assign var="tmp_title" value=$lng.lbl_sp_offer_status_ok}
    {/if}
    {assign var="tmp_link_style" value='style="COLOR: green;" '}
  {/if}
{else}
{assign var="tmp_link_style" value='style="COLOR: red;" '}
{if $offer.incorrect_period}
{assign var="tmp_title" value=$lng.lbl_sp_offer_status_incorrect_period}
{else}
{assign var="tmp_title" value=$lng.lbl_sp_offer_status_fail}
{/if}
{/if}

<tr{cycle values=", class='TableSubHead'"}>
<td><input type="checkbox" name="to_delete[{$offer.offerid}]" /></td>
<td><b><a href="offers.php?offerid={$offer.offerid}" title="{$lng.lbl_sp_click_for_details|escape}">{$offer.offer_name}</a></b></td>
<td align="center">
  <a {$tmp_link_style}href="offers.php?mode=status&amp;offerid={$offer.offerid}" title="{$lng.lbl_sp_click_for_details|escape}">{$tmp_title}</a>
</td>
<td align="center">
{if $offer.invalid}
  <input type="checkbox" disabled="disabled" />
{else}
  <input type="checkbox" name="posted_data[{$offer.offerid}][avail]"{if $offer.avail eq "Y"} checked="checked"{/if} />
{/if}
</td>
</tr>

{/foreach}

{else}

<tr>
<td colspan="4" align="center">
{$lng.lbl_sp_no_offers_defined}
</td>
</tr>

{/if}

<tr>
<td colspan="4"><br />
{if $offers ne ""}
<input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: delete_offers(this.form);" />
&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" value="{$lng.lbl_update|strip_tags:false|escape}" onclick='javascript: document.selectoffersform.submit();' />
<br /><br />
{/if}
<br />
<input type="button" value="{$lng.lbl_add_new|strip_tags:false|escape}" onclick="javascript: self.location = 'offers.php?mode=create';" />
</td>
</tr>

</table>
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_sp_offers content=$smarty.capture.dialog extra='width="100%"'}
