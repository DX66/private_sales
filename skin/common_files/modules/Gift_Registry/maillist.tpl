{*
$Id: maillist.tpl,v 1.1 2010/05/21 08:32:23 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

{capture name=dialog}

{include file="modules/Gift_Registry/event_modify_menu.tpl"} 

{include file="customer/subheader.tpl" title=$lng.lbl_giftreg_recipients_list}

<form action="giftreg_manage.php" method="post" name="maillistform">
  <input type="hidden" name="mode" value="maillist" />
  <input type="hidden" name="eventid" value="{$eventid}" />
  <input type="hidden" name="action" value="update" />

  {if $mailing_list}

    <table cellspacing="1" class="data-table width-100">

      <tr class="head-row">
        <th>&nbsp;</th>
        <th>{$lng.lbl_recipient_name}</th>
        <th>{$lng.lbl_recipient_email}</th>
        <th class="giftreg-recipient-status">{$lng.lbl_status}</th>
      </tr>

      {foreach from=$mailing_list item=r}

        <tr{cycle values=', class="subhead-row"'}>
          <td><input type="checkbox" name="recipient_details[{$r.regid}][checked]" value="Y" /></td>
          <td><input type="text" size="40" maxlength="255" name="recipient_details[{$r.regid}][recipient_name]" value="{$r.recipient_name|escape}" /></td>
          <td><input type="text" size="26" maxlength="50" name="recipient_details[{$r.regid}][recipient_email]" value="{$r.recipient_email|escape}" /></td>
          <td>
            {if $r.status eq "P"}
              {$lng.lbl_pending}

            {elseif $r.status eq "S"}
              {$lng.lbl_sent}

            {elseif $r.status eq "Y"}
              {$lng.lbl_confirmed}

            {else}
              {$lng.lbl_declined}
            {/if}

            {if $r.status ne "P"}
              <span class="small-note">[{$r.status_date|date_format:$config.Appearance.datetime_format}]</span>
            {/if}
          </td>
          {if $r.is_error}
            <td class="data-required">&lt;&lt;</td>
          {/if}
        </tr>

      {/foreach}

    </table>

    <div class="buttons-row">
      {include file="customer/buttons/button.tpl" button_title=$lng.lbl_send_confirmation_request href="javascript: document.maillistform.action.value = 'send_conf'; document.maillistform.submit();"}
      <div class="button-separator"></div>
      {include file="customer/buttons/delete.tpl" href="javascript: document.maillistform.action.value = 'delete'; document.maillistform.submit();"}
    </div>
    <div class="clearing"></div>

  {else}

    <div class="text-block">{$lng.txt_no_recipients}</div>

  {/if}

  {if $recipients_limit_reached eq ""}

    <br />

    {include file="customer/subheader.tpl" title=$lng.lbl_add_new_recipient}

    <table cellspacing="1" class="data-table">

      <tr class="head-row">
        <th>{$lng.lbl_recipient_name}</th>
        <th>{$lng.lbl_recipient_email}</th>
      </tr>

      <tr>
        <td><input type="text" size="40" maxlength="255" name="new_recipient_name" value="{$new_recipient.name|escape}" /></td>
        <td><input type="text" size="26" maxlength="50" name="new_recipient_email" value="{$new_recipient.email|escape}" /></td>
      </tr>

    </table>

  {else}

    <div class="text-block">{$lng.txt_giftreg_max_allowed_recipients_msg}</div>

  {/if}

  <div class="button-row">
    {include file="customer/buttons/update.tpl" type="input"}
  </div>

</form>

{if $config.Gift_Registry.hide_import_export_recipients eq "N"}

  <br />

  {include file="customer/subheader.tpl" title=$lng.lbl_import_recipients_list}

  <form action="giftreg_manage.php" method="post" name="importform" enctype="multipart/form-data">
    <input type="hidden" name="mode" value="maillist" />
    <input type="hidden" name="action" value="import" />
    <input type="hidden" name="eventid" value="{$eventid}" />

    <p class="text-block">{$lng.txt_giftreg_import_note}</p>

    <strong>{$lng.lbl_column_order}:</strong><br />
    {$lng.txt_column_order_note}<br />

    {section name=col loop=$columns}

      <label class="input-block">
        {$smarty.section.col.index}:
        <select name="cols[{$smarty.section.col.index}]">
          <option value="">NULL</option>
          {section name=col2 loop=$columns}
            <option value="{$columns[col2]}"{if $smarty.section.col.index eq $smarty.section.col2.index} selected="selected"{/if}>{$columns[col2]|escape}</option>
          {/section}
        </select>
      </label>
      <br />

    {/section}

    <br />

    <strong>{$lng.lbl_csv_delimiter}:</strong><br />
    {include file="provider/main/ie_delimiter.tpl"}
    <br />
    <br />

    <strong>{$lng.lbl_csv_file_upload}:</strong><br />
    <input type="file" size="32" name="userfile" />
    <br />
    <br />

    <label>
      <input type="checkbox" name="override" value="Y" />
      {$lng.lbl_giftreg_override_recipient}
    </label>
    <br />
    <br />

    <div class="button-row">
      {include file="customer/buttons/button.tpl" button_title=$lng.lbl_import type="input"}
    </div>

  </form>

  <br />

  {include file="customer/subheader.tpl" title=$lng.lbl_export_recipients_list}

  <form action="giftreg_manage.php" method="post" name="exportform">
    <input type="hidden" name="mode" value="maillist" />
    <input type="hidden" name="action" value="export" />
    <input type="hidden" name="eventid" value="{$eventid}" />

    <p class="text-block">{$lng.txt_giftreg_export_note}</p>

    <strong>{$lng.lbl_csv_delimiter}:</strong><br />
    {include file="provider/main/ie_delimiter.tpl"}
    <br />
    <br />

    <div class="button-row">
      {include file="customer/buttons/button.tpl" button_title=$lng.lbl_export type="input"}
    </div>

  </form>

{/if}

{/capture}
{include file="customer/dialog.tpl" title="`$lng.lbl_giftreg_manage_giftregistry`: `$event_data.title`" content=$smarty.capture.dialog extra='width="100%"'}
