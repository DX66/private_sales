{*
$Id: event_guestbook.tpl,v 1.1 2010/05/21 08:32:23 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<a name="gb"></a>

{if $main_mode eq "manager"}
{assign var="script_name" value="giftreg_manage.php"}
{else}
{assign var="script_name" value="giftregs.php"}
{/if}

{capture name=dialog}

{if $main_mode eq "manager"}
{include file="modules/Gift_Registry/event_modify_menu.tpl"}
{/if}

{if $guestbook}
{if $event_data.guestbook ne "Y"}<b>{$lng.lbl_note}: </b>{$lng.txt_guestbook_disabled}<br /><br />{/if}

{include file="customer/subheader.tpl" title=$lng.lbl_giftreg_posted_messages}

{include file="customer/main/navigation.tpl"}

{foreach from=$guestbook item=g name=guestbook}

<div{if $g.moderator eq "Y"} class="giftreg-gb-hl"{/if}>
[{$g.post_date|date_format:"%A, %B %e, %Y - %T"}] -
<b>{$g.name}</b>
{if $main_mode eq "manager"}
- [<a href="giftreg_manage.php?eventid={$eventid}&amp;mode=gb&amp;mesid={$g.message_id}&amp;action=delete">{$lng.lbl_delete}</a>]
{/if}
<br />
<b>{$g.subject}</b>
<br />
{$g.message}
</div>

{if not $smarty.foreach.guestbook.last}
<hr />
{/if}

{/foreach}

{include file="customer/main/navigation.tpl"}

{else}

<div>{$lng.lbl_giftreg_gb_message} {$event_data.gb_count} {$lng.lbl_giftreg_messages}.</div>
<div class="button-row">
  {include file="customer/buttons/button.tpl" button_title=$lng.lbl_giftreg_view_guestbook href="`$script_name`?eventid=`$eventid`&mode=gb#gb" style="link"}
</div>

{/if}

<br />

{include file="customer/subheader.tpl" title=$lng.lbl_giftreg_post_new_message}

<form action="{$script_name}" method="post" name="gbadd_form">
  <input type="hidden" name="eventid" value="{$event_data.event_id}" />
  <input type="hidden" name="mode" value="guestbook" />

  <table cellspacing="0" class="data-table">

    <tr>
      <td class="data-name">{$lng.lbl_your_name}:</td>
      <td class="data-required">*</td>
      <td>
        <input type="text" size="40" name="gb_details[name]" />
      </td>
    </tr>

    <tr>
      <td class="data-name">{$lng.lbl_subject}:</td>
      <td class="data-required">*</td>
      <td>
        <input type="text" size="40" name="gb_details[subject]" />
      </td>
    </tr>

    <tr>
      <td class="data-name">{$lng.lbl_your_message}:</td>
      <td class="data-required">*</td>
      <td>
        <textarea cols="55" rows="7" name="gb_details[message]"></textarea>
      </td>
    </tr>

    <tr>
      <td colspan="2">&nbsp;</td>
      <td class="button-row">
        {include file="customer/buttons/submit.tpl" type="input"}
      </td>
    </tr>

  </table>

</form>

{/capture}
{include file="customer/dialog.tpl" title=$lng.lbl_giftreg_guestbook content=$smarty.capture.dialog}
