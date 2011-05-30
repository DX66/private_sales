{*
$Id: opc_profile.tpl,v 1.3.2.3 2010/11/15 11:46:25 ferz Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}

<div id="opc_profile">

  <h2>{$lng.lbl_name_and_address}</h2>
  
  {if $userinfo ne '' and not $reg_error and not $force_change_address}
    
    {include file="modules/One_Page_Checkout/profile/profile_details_html.tpl"}
  
  {else}
  
    {if $reg_error}
      <p class="error-message">{$reg_error.errdesc}</p>
    {/if}

    <form class="skip-auto-validation" action="cart.php?mode=checkout" method="post" name="registerform">
      <fieldset id="personal_details" class="registerform">

        <input type="hidden" name="usertype" value="C" />
        <input type="hidden" name="anonymous" value="{$anonymous}" />
        {if $config.Security.use_https_login eq "Y"}
          <input type="hidden" name="{$XCARTSESSNAME}" value="{$XCARTSESSID}" />
        {/if}
  
        {include file='modules/One_Page_Checkout/profile/address_info.tpl' type='B' hide_header=true first=true}
  
        {include file='modules/One_Page_Checkout/profile/account_info.tpl' hide_header=true}
        
        {include file='modules/One_Page_Checkout/profile/address_info.tpl' type='S' hide_header=true first=true}
        
        {include file='modules/One_Page_Checkout/profile/personal_info.tpl' first=true}
  
        {include file='modules/One_Page_Checkout/profile/additional_info.tpl' section='A' first=true}
  
        {if $config.General.disable_cc ne 'Y'}
          {include file="modules/One_Page_Checkout/profile/cc_info.tpl"}
        {/if}
        
        {*** uncomment if you need to enable newsletter signup    
        {include file='modules/One_Page_Checkout/profile/newslist_info.tpl' hide_header=true}
        ***}

        {include file="customer/buttons/button.tpl" button_title=$lng.lbl_apply additional_button_class="main-button update-profile" type="input" assign="submit_button"}
  
        {if $active_modules.Image_Verification and $show_antibot.on_registration eq 'Y' and $display_antibot}
          {include file="modules/Image_Verification/spambot_arrest.tpl" mode="simple" id=$antibot_sections.on_registration antibot_err=$reg_antibot_err button_code=$submit_button}
        {else}
        <div class="button-row" align="center">
            {$submit_button}
        </div>
        {/if}
  
      </fieldset>
    </form>
    
    {include file="check_registerform_fields_js.tpl" is_opc=true}
  
  {/if}

</div>
