{*
$Id: ch_info.tpl,v 1.2 2010/07/21 13:57:48 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $hide_header ne "Y"}
  <a name="chinfo"></a>
  <h3>{$lng.lbl_check_information}</h3>
{/if}

<ul>

  <li class="single-field">
    {capture name=regfield}
      <input type="text" id="check_name" name="check_name" size="32" maxlength="20" value="{if $userinfo.lastname ne ""}{$userinfo.firstname} {$userinfo.lastname}{/if}" />  
    {/capture}
    {include file="modules/One_Page_Checkout/opc_form_field.tpl" content=$smarty.capture.regfield required="Y" name=$lng.lbl_ch_name field="check_name"}
  </li>

  <li class="single-field">
    {capture name=regfield}
      <input type="text" id="check_ban" name="check_ban" size="32" maxlength="20" value="" />
    {/capture}
    {include file="modules/One_Page_Checkout/opc_form_field.tpl" content=$smarty.capture.regfield required="Y" name=$lng.lbl_ch_bank_account field="check_ban"}
  </li>

  <li class="single-field">
    {capture name=regfield}
      <input type="text" id="check_brn" name="check_brn" size="32" maxlength="20" value="" />
    {/capture}
    {include file="modules/One_Page_Checkout/opc_form_field.tpl" content=$smarty.capture.regfield required="Y" name=$lng.lbl_ch_bank_routing field="check_brn"}
  </li>

  {if $payment_cc_data.disable_ccinfo eq "N"}

    <li class="single-field">
      {capture name=regfield}
        <input type="text" id="check_number" name="check_number" size="32" maxlength="20" value="" />
      {/capture}
      {include file="modules/One_Page_Checkout/opc_form_field.tpl" content=$smarty.capture.regfield required="Y" name=$lng.lbl_ch_number field="check_number"}
    </li>

  {/if}

</ul>
