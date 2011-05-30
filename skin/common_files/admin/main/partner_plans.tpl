{*
$Id: partner_plans.tpl,v 1.2 2010/07/22 06:37:11 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_affiliate_plans}
{$lng.txt_affiliate_plan_note}<br /><br />

<br />

{capture name=dialog}
<form action="partner_plans.php" name="plansform" method="post">
<input type="hidden" name="mode" value="update" />

<table cellpadding="2" cellspacing="2" width="100%">
<tr class="TableHead">
  <td width="5">&nbsp;</td>
  <td width="20" nowrap="nowrap">{$lng.lbl_plan_number}</td>
  <td width="70%" nowrap="nowrap">{$lng.lbl_plan_title}</td>
  <td width="30%" nowrap="nowrap">{$lng.lbl_status}</td>
</tr>

{foreach from=$partner_plans item=p}

<tr{cycle values=", class='TableSubHead'"}>
  <td><input type="checkbox" name="ids[]" value="{$p.plan_id}" /></td>
  <td align="center">{$p.plan_id}</td>
  <td>
    <input type="text" name="plans[{$p.plan_id}][plan_title]" size="45" maxlength="64" value="{$p.plan_title|escape}" />
    &nbsp;&nbsp;&nbsp;<a href="partner_plans.php?mode=edit&amp;planid={$p.plan_id}">{$lng.lbl_edit|strip_tags:false|escape}</a>
  </td>
  <td align="center">
  <select name="plans[{$p.plan_id}][status]">
    <option value="A"{if $p.status eq "A"} selected="selected"{/if}>{$lng.lbl_active}</option>
    <option value="D"{if $p.status eq "D"} selected="selected"{/if}>{$lng.lbl_disabled}</option>
  </select>
  </td>
</tr>

{foreachelse}

<tr>
  <td colspan="4" align="center">{$lng.lbl_no_affiliate_plans_defined}</td>
</tr>

{/foreach}

{if $partner_plans}
<tr>
  <td colspan="4" class="SubmitBox">
  <input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('ids', 'ig'))) submitForm(this, 'delete');" />
  <input type="button" value="{$lng.lbl_update|strip_tags:false|escape}" onclick="javascript: submitForm(this, 'edit');" />
  </td>
</tr>
{/if}

<tr><td colspan="4">&nbsp;</td></tr>

<tr><td colspan="4">{include file="main/subheader.tpl" title=$lng.lbl_add_affiliate_plan}</td></tr>

<tr>
  <td colspan="2">&nbsp;</td>
  <td><input type="text" name="new_plan_title" size="45" maxlength="64" /></td>
  <td align="center">
  <select name="new_status">
    <option value="A" selected="selected">{$lng.lbl_active}</option>
    <option value="D">{$lng.lbl_disabled}</option>
  </select>
  </td>
</tr>

<tr>
  <td colspan="4" class="SubmitBox">
  <input type="hidden" name="redirect_to_modify" />
  <input type="submit" value="{$lng.lbl_add|strip_tags:false|escape}" />
  <input type="button" value="{$lng.lbl_add_and_modify|strip_tags:false|escape}" onclick="javascript: document.plansform.redirect_to_modify.value='on'; document.plansform.submit();" />
  </td>
</tr>

</table>
</form>
{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_affiliate_plans extra='width="100%"'}
