{*
$Id: event_send.tpl,v 1.3 2010/06/08 06:17:40 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{capture name=dialog}

  {include file="modules/Gift_Registry/event_modify_menu.tpl"}

  {include file="customer/subheader.tpl" title=$lng.lbl_giftreg_email_notification}

  {if $event_data.allow_to_send gt 0}

    {if $event_data.sent_date gt 0}
      {assign var="s_date" value=$event_data.sent_date|date_format:"%A, %B %e, %Y"}
      {assign var="s_time" value=$event_data.sent_date|date_format:"%T"}
      <center>{$lng.txt_giftreg_already_sent_notification_msg|substitute:"s_date":$s_date:"s_time":$s_time}</center><br />
    {/if}

<script type="text/javascript">
//<![CDATA[
{literal}
function messagePreview() {
  document.notiform.action.value = 'preview';
  document.notiform.target = 'eventpreview';
  document.notiform.submit();
  var f = document.notiform;
  setTimeout(
    function() {
      f.action.value = 'go';
      f.target = '';
    },
    500
  );
}
{/literal}
//]]>
</script>

    <form action="giftreg_manage.php" method="post" name="notiform">
      <input type="hidden" name="mode" value="send" />
      <input type="hidden" name="action" value="go" />
      <input type="hidden" name="eventid" value="{$eventid}" />

      <table cellspacing="1" cellpadding="3" class="data-table" width="100%">

        <tr>
          <td class="data-name">{$lng.lbl_subject}:</td>
        </tr>
        <tr>
          <td><input type="text" size="70" maxlength="255" name="posted_mail_subj" value="{$mail_data.subj|escape}" /></td>
        </tr>

        <tr>
          <td class="data-name">{$lng.lbl_message}{if $config.Email.html_mail eq "Y"} ({$lng.txt_giftreg_send_message_help}{/if}):</td>
        </tr>
        <tr>
          <td><textarea cols="70" rows="25" name="posted_mail_message">{$mail_data.message|escape}</textarea></td>
        </tr>

        <tr>
          <td class="buttons-row">
            {include file="customer/buttons/submit.tpl" type="input"}
            <div class="button-separator"></div>
            {include file="customer/buttons/button.tpl" button_title=$lng.lbl_preview href="javascript: messagePreview();"}
          </td>
        </tr>

      </table>

    </form>

  {else}

    {$lng.err_giftreg_no_recipients_msg}

  {/if}

{/capture}
{include file="customer/dialog.tpl" title="`$lng.lbl_giftreg_manage_giftregistry`: `$event_data.title`" content=$smarty.capture.dialog}
