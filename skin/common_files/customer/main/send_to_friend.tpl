{*
$Id: send_to_friend.tpl,v 1.3.2.1 2010/11/15 11:46:25 ferz Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{capture name=dialog}
<form action="product.php" method="post" name="send">
  <input type="hidden" name="mode" value="send" />
  <input type="hidden" name="productid" value="{$product.productid}" />

  <table cellspacing="0" class="data-table" summary="{$lng.lbl_send_to_friend|escape}">
    <tr>
      <td class="data-name"><label for="send_name">{$lng.lbl_send_your_name}</label>:</td>
      <td class="data-required">*</td>
      <td>
        <input class="send2friend input-required" id="send_name" type="text" name="name" value="{$send_to_friend_info.name|escape}" />
        {if $send_to_friend_info.fill_err and $send_to_friend_info.name eq ''}
          <span class="data-required">&lt;&lt;</span>
        {/if}
      </td>
    </tr>

    <tr>
      <td class="data-name"><label for="send_from">{$lng.lbl_send_your_email}</label>:</td>
      <td class="data-required">*</td>
      <td>
        <input class="send2friend input-required input-email" id="send_from" type="text" name="from" value="{$send_to_friend_info.from|escape}" />
        {if ($send_to_friend_info.fill_err and $send_to_friend_info.from eq '') or $send_to_friend_info.from_failed eq "Y"}
          <span class="data-required">&lt;&lt;</span>
        {/if}
      </td>
    </tr>

    <tr>
      <td class="data-name"><label for="send_to">{$lng.lbl_recipient_email}</label>:</td>
      <td class="data-required">*</td>
      <td>
        <input class="send2friend input-required input-email" id="send_to" type="text" name="email" value="{$send_to_friend_info.email|escape}" />
        {if ($send_to_friend_info.fill_err and $send_to_friend_info.email eq '') or $send_to_friend_info.email_failed eq "Y"}
          <span class="data-required">&lt;&lt;</span>
        {/if}
      </td>
    </tr> 

    <tr>
      <td colspan="3">
        <div class="data-name">
          <label for="is_msg">
            <input type="checkbox" id="is_msg" name="is_msg" onclick="javascript: $('#send_message_box').toggle();" value="Y"{if $send_to_friend_info.is_msg eq "Y"} checked="checked"{/if} />
              {$lng.lbl_add_personal_message}
          </label>
        </div>
        <div id="send_message_box"{if $send_to_friend_info.is_msg ne "Y"} style="display:none"{/if}>
          <textarea class="send2friend" id="send_message" name="message" cols="40" rows="4">{$send_to_friend_info.message}</textarea>
        </div>
      </td>
    </tr> 

    {include file="customer/buttons/button.tpl" type="input" button_title=$lng.lbl_send_to_friend assign="submit_button"}

    {if $active_modules.Image_Verification and $show_antibot.on_send_to_friend eq 'Y'}
      {include file="modules/Image_Verification/spambot_arrest.tpl" mode="data-table" id=$antibot_sections.on_send_to_friend antibot_err=$antibot_friend_err button_code=$submit_button}
    {else}
    <tr>
      <td colspan="2">&nbsp;</td>
      <td class="button-row">
        {$submit_button}
      </td>
    </tr>
    {/if}

  </table>

</form>

{/capture}
{if $nodialog}
  {$smarty.capture.dialog}
{else}
  {include file="customer/dialog.tpl" title=$lng.lbl_send_to_friend content=$smarty.capture.dialog additional_class="no-print send2friend-dialog"}
{/if}
