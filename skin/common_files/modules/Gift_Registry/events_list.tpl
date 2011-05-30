{*
$Id: events_list.tpl,v 1.1 2010/05/21 08:32:23 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

{if not $is_internal}
  <h1>{$lng.lbl_giftreg_events_list}</h1>
{/if}

{capture name=dialog}

  <form action="giftreg_manage.php" method="post" name="giftregmanageform">
    <input type="hidden" name="mode" value="" />
    
    {if $events_list}

    <table cellspacing="1" class="data-table giftreg-events-list width-100" summary="{$lng.lbl_giftreg_events_list|escape}">

      <tr class="head-row">
        <th>&nbsp;</th>
        <th>{$lng.lbl_event}</th>
        {if $config.Gift_Registry.enable_html_cards eq "Y"}
          <th>{$lng.lbl_giftreg_html_card}</th>
        {/if}
        <th>{$lng.lbl_status}</th>
        <th>{$lng.lbl_giftreg_products}</th>
        <th>{$lng.lbl_recipients}</th>
        <th>{$lng.lbl_sent}</th>
      </tr>

      {foreach from=$events_list item=e}

        <tr{if $e.event_id eq $eventid} class="subhead-row"{/if}>
          <td class="giftreg-event-mark"><input type="checkbox" name="ids[]" value="{$e.event_id}" /></td>
          <td class="giftreg-event-name"><a href="giftreg_manage.php?eventid={$e.event_id}" title="{$lng.lbl_event_info|escape}">[{$e.event_date|date_format:"%B %e, %Y"}] - {$e.title}</a></td>

          {if $config.Gift_Registry.enable_html_cards eq "Y"}
          <td class="giftreg-event-center">
            {if $e.html_content ne ""}
              <a href="giftregs.php?eventid={$e.event_id}&amp;mode=preview" target="event{$e.event_id}" title="{$lng.lbl_preview|escape}">{$lng.lbl_yes}</a>
            {else}
              {$lng.lbl_no}
            {/if}
          </td>
          {/if}

          {if $e.status eq "P"}
            <td class="giftreg-private-status"><img src="{$ImagesDir}/spacer.gif" alt="{$lng.lbl_private|escape}" /></td>

          {elseif $e.status eq "G"}
            <td class="giftreg-public-status"><img src="{$ImagesDir}/spacer.gif" alt="{$lng.lbl_public|escape}" /></td>

          {else}
            <td class="giftreg-access-denied-status"><img src="{$ImagesDir}/spacer.gif" alt="{$lng.lbl_disabled|escape}" /></td>
          {/if}

          <td class="giftreg-event-center"><a href="giftreg_manage.php?eventid={$e.event_id}&amp;mode=products" title="{$lng.lbl_wish_list|escape}">{$e.products}</a></td>
          <td class="giftreg-event-center"><a href="giftreg_manage.php?eventid={$e.event_id}&amp;mode=maillist" title="{$lng.lbl_giftreg_recipients_list|escape}">{$e.emails}</a></td>
          <td class="giftreg-event-center giftreg-event-date">

            {if $e.sent_date gt 0}
              <span title="{$lng.lbl_sent_date|escape}: {$e.sent_date|date_format:$config.Appearance.datetime_format|escape}">{$e.sent_date|date_format:$config.Appearance.date_format}</span>

            {elseif $e.allow_to_send}
              <a href="giftreg_manage.php?eventid={$e.event_id}&amp;mode=send" title="{$lng.lbl_giftreg_send_notification|escape}">{$lng.lbl_go}</a>

            {else}
              {$lng.txt_not_available}
            {/if}

          </td>
        </tr>

      {/foreach}

    </table>

    {else}
      <center>{$lng.txt_giftreg_no_events_defined}</center>
    {/if}

    <div class="buttons-row">
      {if $events_list}
        {include file="customer/buttons/button.tpl" button_title=$lng.lbl_delete_selected href="javascript: submitForm(document.giftregmanageform, 'delete');"}
        <div class="button-separator"></div>
      {/if}
      {if $events_lists_count lt $config.Gift_Registry.events_lists_limit}
        {include file="customer/buttons/button.tpl" button_title=$lng.lbl_giftreg_add_new_event href="giftreg_manage.php?eventid=new"}
      {/if}
    </div>
    {if $events_lists_count gte $config.Gift_Registry.events_lists_limit}
      {$lng.txt_giftreg_max_allowed_events_msg}
    {/if}
    <div class="clearing"></div>

  </form>

  <p class="giftreg-events-counter">
    {$lng.lbl_event_used_max}: {$events_lists_count|default:"0"}/{$config.Gift_Registry.events_lists_limit}
  </p>

{/capture}
{if $is_internal}
  {assign var="_noborder" value=false}
{else}
  {assign var="_noborder" value=true}
{/if}
{include file="customer/dialog.tpl" title=$lng.lbl_giftreg_events_list content=$smarty.capture.dialog noborder=$_noborder}
