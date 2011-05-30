{*
$Id: check_registerform_fields_js.tpl,v 1.7.2.4 2010/10/28 10:07:54 aim Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript">
//<![CDATA[
var is_run = false;
var unique_key = '{unique_key}';

function checkRegFormFields(form) {ldelim}

  if (is_run) {ldelim}
    return false;
  {rdelim}

  var is_valid_card_number = true;
  var is_valid_cvv2 = true;

  {if $usertype eq 'C' and $config.General.check_cc_number eq 'Y' and $config.General.disable_cc ne 'Y'}
    var card_number = document.registerform.elements.namedItem('card_number[' + unique_key + ']');
    var card_type = document.registerform.elements.namedItem('card_type[' + unique_key + ']');
    var card_cvv2 = document.registerform.elements.namedItem('card_cvv2[' + unique_key + ']');
    if (card_number && card_type && card_number.value.length > 0)
      is_valid_card_number = checkCCNumber(card_number, card_type, false);
    
    if (card_cvv2 && card_type && card_cvv2.value.length > 0 && is_valid_card_number)    
      is_valid_cvv2 = checkCVV2(card_cvv2, card_type, false);
  {/if}

  is_run = {if $is_opc}false{else}true{/if};
  if (
      check_zip_code(form)
      && is_valid_card_number 
      && is_valid_cvv2
      {if $config.Security.use_complex_pwd eq 'Y'} && checkPasswordStrength(form.passwd1, form.passwd2){/if}
  ) {ldelim}
    return true;
  {rdelim}

  is_run = false;

  return false;
{rdelim}

{if $usertype eq 'C'}

var anonymousFlag = {if $anonymous and $config.General.enable_anonymous_checkout eq 'Y'}true{else}false{/if};

{literal}

$(function() {
  $('#email')
    .live('blur submit', function(){
      $('#email_note').hide();
    })
    .live('focus', function(){
      showNote('email_note', this)
    });

  $('#passwd1, #passwd2')
    .bind('change', function() {
      $('#password_is_modified').val('Y');
    })
    .bind('keydown', function() {
    })
    .bind('blur', function() {
      $('#passwd_note').hide();
    })
    .bind('focus', function() {
      showNote('passwd_note', this)
    });

  $('#passwd1, #passwd2')
    .bind('change', function() {

      var pwd1 = $('#passwd1').val();
      var pwd2 = $('#passwd2').val();
      var vm   = $('#passwd2').parent().find('span.validate-mark');

      if (vm === undefined) {
        return true;
      }

      if (pwd1 == '' || pwd2 == '') {
        vm.removeClass('validate-matched validate-non-matched');
      } else if (pwd1 != pwd2) {
        vm.removeClass('validate-matched').addClass('validate-non-matched');
      } else {
        vm.removeClass('validate-non-matched').addClass('validate-matched');
      }
    });


  $('#create_account, #ship2diff')
    .bind('click', function(){
      if ($(this).is(':checked')) {
        $('#' + $(this).attr('id') + '_box').show();
        $(this).parents('.register-exp-section').removeClass('register-sec-minimized'); 
      }
      else {
        $('#' + $(this).attr('id') + '_box').hide();
        $(this).parents('.register-exp-section').addClass('register-sec-minimized'); 
      }
      {/literal}
      {if $checkout_module eq 'Fast_Lane_Checkout'}
      $('#content-container').css('height', 'auto');
      $('#page-container2').css('height', 'auto');
      {/if}
      {literal}
    });

{/literal}

{if not $ship2diff}
$('#ship2diff_box').hide();
{/if}
{if not ($reg_error and $userinfo.create_account) and $config.General.enable_anonymous_checkout eq 'Y'}
$('#create_account_box').hide();
{/if}

{literal}
});
{/literal}

{/if}
//]]>
</script>
