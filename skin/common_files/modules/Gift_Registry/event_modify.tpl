{*
$Id: event_modify.tpl,v 1.1 2010/05/21 08:32:23 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{capture name=dialog}

{if $eventid}
{include file="modules/Gift_Registry/event_modify_menu.tpl"}
{/if}

{include file="customer/subheader.tpl" title=$lng.lbl_event_info}

{if $error eq "fill_error"}
<div class="error-message">{$lng.err_giftreg_required_fields_msg}</div>
{/if}

<div class="text-block">{$lng.txt_fields_are_mandatory}</div>

<form action="giftreg_manage.php" method="post" name="eventdetails_form">
  <input type="hidden" name="eventid" value="{$eventid}" />

  <table cellpadding="0" class="data-table width-100" summary="{$lng.lbl_event_info|escape}">
    <tr>
      <td class="data-name">{$lng.lbl_status}:</td>
      <td class="data-required">*</td>
      <td>

        <select name="event_details[status]">
          <option value="P"{if $event_data.status eq "P"} selected="selected"{/if}>{$lng.lbl_private}</option>
          <option value="G"{if $event_data.status eq "G"} selected="selected"{/if}>{$lng.lbl_public}</option>
          <option value="D"{if $event_data.status eq "D"} selected="selected"{/if}>{$lng.lbl_disabled}</option>
        </select>

      </td>
    </tr>

    <tr>
      <td class="data-name">{$lng.lbl_giftreg_title}:</td>
      <td class="data-required">*</td>
      <td>
        <input type="text" size="50" maxlength="255" name="event_details[title]" value="{$event_data.title|escape}" />
      </td>
    </tr>

    <tr>
      <td class="data-name">{$lng.lbl_giftreg_event_date}:</td>
      <td class="data-required">*</td>
      <td>
        {inc value=$config.Company.end_year assign="end_year" inc=3}
        {include file="main/datepicker.tpl" name="event_date" date=$event_data.event_date end_year=$end_year}
      </td>
    </tr>

    <tr>
      <td class="data-name">{$lng.lbl_description}:</td>
      <td class="data-required">&nbsp;</td>
      <td>
        <textarea cols="50" rows="4" name="event_details[description]">{$event_data.description|escape}</textarea>
      </td>
    </tr>

    {if $config.Gift_Registry.enable_html_cards eq "Y"}
    <tr>
      <td class="data-name">{$lng.lbl_giftreg_html_content}:</td>
      <td class="data-required">&nbsp;</td>
      <td>
        <textarea cols="50" rows="20" name="event_details[html_content]">{$event_data.html_content|escape}</textarea>
      </td>
    </tr>
    {/if}

    <tr>
      <td class="data-name">{$lng.lbl_giftreg_guestbook}:</td>
      <td class="data-required">&nbsp;</td>
      <td>

        <select name="event_details[guestbook]">
          <option value="Y"{if $event_data.guestbook eq "Y"} selected="selected"{/if}>{$lng.lbl_enabled}</option>
          <option value="N"{if $event_data.guestbook eq "N"} selected="selected"{/if}>{$lng.lbl_disabled}</option>
        </select>

      </td>
    </tr>

    <tr>
      <td colspan="2">&nbsp;</td>
      <td class="button-row">

{if $event_data}
        {include file="customer/buttons/update.tpl" type="input"}
        {assign var="title" value="`$lng.lbl_giftreg_manage_giftregistry`: `$event_data.title`"}
{else}
        {include file="customer/buttons/button.tpl" button_title=$lng.lbl_create type="input"}
        {assign var="title" value="`$lng.lbl_giftreg_manage_giftregistry`: `$lng.lbl_giftreg_new_event`"}
{/if}

      </td>
    </tr>

  </table>

</form>

{/capture}
{include file="customer/dialog.tpl" content=$smarty.capture.dialog}
