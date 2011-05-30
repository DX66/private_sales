<?php /* Smarty version 2.6.26, created on 2011-05-26 16:20:35
         compiled from check_registerform_fields_js.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'unique_key', 'check_registerform_fields_js.tpl', 8, false),)), $this); ?>
<script type="text/javascript">
//<![CDATA[
var is_run = false;
var unique_key = '<?php echo smarty_function_unique_key(array(), $this);?>
';

function checkRegFormFields(form) {

  if (is_run) {
    return false;
  }

  var is_valid_card_number = true;
  var is_valid_cvv2 = true;

  <?php if ($this->_tpl_vars['usertype'] == 'C' && $this->_tpl_vars['config']['General']['check_cc_number'] == 'Y' && $this->_tpl_vars['config']['General']['disable_cc'] != 'Y'): ?>
    var card_number = document.registerform.elements.namedItem('card_number[' + unique_key + ']');
    var card_type = document.registerform.elements.namedItem('card_type[' + unique_key + ']');
    var card_cvv2 = document.registerform.elements.namedItem('card_cvv2[' + unique_key + ']');
    if (card_number && card_type && card_number.value.length > 0)
      is_valid_card_number = checkCCNumber(card_number, card_type, false);
    
    if (card_cvv2 && card_type && card_cvv2.value.length > 0 && is_valid_card_number)    
      is_valid_cvv2 = checkCVV2(card_cvv2, card_type, false);
  <?php endif; ?>

  is_run = <?php if ($this->_tpl_vars['is_opc']): ?>false<?php else: ?>true<?php endif; ?>;
  if (
      check_zip_code(form)
      && is_valid_card_number 
      && is_valid_cvv2
      <?php if ($this->_tpl_vars['config']['Security']['use_complex_pwd'] == 'Y'): ?> && checkPasswordStrength(form.passwd1, form.passwd2)<?php endif; ?>
  ) {
    return true;
  }

  is_run = false;

  return false;
}

<?php if ($this->_tpl_vars['usertype'] == 'C'): ?>

var anonymousFlag = <?php if ($this->_tpl_vars['anonymous'] && $this->_tpl_vars['config']['General']['enable_anonymous_checkout'] == 'Y'): ?>true<?php else: ?>false<?php endif; ?>;

<?php echo '

$(function() {
  $(\'#email\')
    .live(\'blur submit\', function(){
      $(\'#email_note\').hide();
    })
    .live(\'focus\', function(){
      showNote(\'email_note\', this)
    });

  $(\'#passwd1, #passwd2\')
    .bind(\'change\', function() {
      $(\'#password_is_modified\').val(\'Y\');
    })
    .bind(\'keydown\', function() {
    })
    .bind(\'blur\', function() {
      $(\'#passwd_note\').hide();
    })
    .bind(\'focus\', function() {
      showNote(\'passwd_note\', this)
    });

  $(\'#passwd1, #passwd2\')
    .bind(\'change\', function() {

      var pwd1 = $(\'#passwd1\').val();
      var pwd2 = $(\'#passwd2\').val();
      var vm   = $(\'#passwd2\').parent().find(\'span.validate-mark\');

      if (vm === undefined) {
        return true;
      }

      if (pwd1 == \'\' || pwd2 == \'\') {
        vm.removeClass(\'validate-matched validate-non-matched\');
      } else if (pwd1 != pwd2) {
        vm.removeClass(\'validate-matched\').addClass(\'validate-non-matched\');
      } else {
        vm.removeClass(\'validate-non-matched\').addClass(\'validate-matched\');
      }
    });


  $(\'#create_account, #ship2diff\')
    .bind(\'click\', function(){
      if ($(this).is(\':checked\')) {
        $(\'#\' + $(this).attr(\'id\') + \'_box\').show();
        $(this).parents(\'.register-exp-section\').removeClass(\'register-sec-minimized\'); 
      }
      else {
        $(\'#\' + $(this).attr(\'id\') + \'_box\').hide();
        $(this).parents(\'.register-exp-section\').addClass(\'register-sec-minimized\'); 
      }
      '; ?>

      <?php if ($this->_tpl_vars['checkout_module'] == 'Fast_Lane_Checkout'): ?>
      $('#content-container').css('height', 'auto');
      $('#page-container2').css('height', 'auto');
      <?php endif; ?>
      <?php echo '
    });

  // Remove passwords if create_account is unchecked  
  if (current_area == \'C\') {
    $(\'#create_account\')
      .bind(\'click\', function(){
        if (!$(this).is(\':checked\')) {
          $(\'#passwd1\').val(\'\');
          $(\'#passwd2\').val(\'\');
        }
      });

    // Do not submit existing_s/new_s checkboxes for hided S address section
    $(\'#ship2diff\')
      .bind(\'click\', function(){
        if (!$(this).is(\':checked\')) {
          $(\'#existing_S\').attr(\'checked\', false);
          $(\'#new_S\').attr(\'checked\', false);
        }
      });
  }    

'; ?>


<?php if (! $this->_tpl_vars['ship2diff']): ?>
$('#ship2diff_box').hide();
<?php endif; ?>
<?php if (! ( $this->_tpl_vars['reg_error'] && $this->_tpl_vars['userinfo']['create_account'] ) && $this->_tpl_vars['config']['General']['enable_anonymous_checkout'] == 'Y'): ?>
$('#passwd1').val('');
$('#passwd2').val('');
$('#create_account_box').hide();
<?php endif; ?>

<?php echo '
});
'; ?>


<?php endif; ?>
//]]>
</script>