{*
$Id: ask_question.tpl,v 1.4.2.1 2010/11/15 11:46:25 ferz Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>{$lng.lbl_ask_question_about_product}</h1>

<form action="{$xcart_web_dir}/popup_ask.php" method="post" name="askform">
  <input type="hidden" name="mode" value="send_email" />
  <input type="hidden" name="productid" value="{$productid|escape}" />

  <table cellspacing="1" cellpadding="3" class="product-ask-form">

    <tr>
      <td class="data-name"><label for="uname">{$lng.lbl_name}</label></td>
      <td class="data-required">*</td>
      <td>
        <input type="text" name="uname" id="uname" value="{if $login ne ''}{$fullname|default:$login|escape}{/if}" size="30" />
      </td>
    </tr>

    <tr>
      <td class="data-name"><label for="email">{$lng.lbl_email}</label></td>
      <td class="data-required">*</td>
      <td>
        <input type="text" class="input-email" name="email" id="email" value="{if $config.email_as_login eq 'Y' and $login ne ''}{$login|escape}{/if}" size="30" />
      </td>
    </tr>

    <tr>
      <td class="data-name"><label for="phone">{$lng.lbl_phone}</label></td>
      <td>&nbsp;</td>
      <td>
        <input type="text" name="phone" id="phone" value="" size="30" />
      </td>
    </tr>

    <tr>
      <td colspan="3">
        <div class="field-container">
          <div class="data-name">
            <label for="question" class="data-required">{$lng.lbl_your_question}</label>
            <span class="star">*</span>
          </div>

          <div class="data-value">
            <textarea name="question" id="question" rows="8" cols="50"></textarea>
          </div>
        </div>
      </td>
    </tr>

    {include file="customer/buttons/submit.tpl" type="input" assign="submit_button"}

    {if $active_modules.Image_Verification and $show_antibot.on_ask_form eq 'Y'}
      {include file="modules/Image_Verification/spambot_arrest.tpl" mode="data-table" id=$antibot_sections.on_ask_form button_code=$submit_button}
    {else}
    <tr>
      <td align="center" colspan="3">
        {$submit_button}
      </td>
    </tr>
    {/if}

  </table>

</form>
