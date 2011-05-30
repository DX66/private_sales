<?php /* Smarty version 2.6.26, created on 2011-05-26 16:20:35
         compiled from customer/main/register.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'amp', 'customer/main/register.tpl', 66, false),array('modifier', 'escape', 'customer/main/register.tpl', 84, false),array('modifier', 'substitute', 'customer/main/register.tpl', 125, false),)), $this); ?>
<?php func_load_lang($this, "customer/main/register.tpl","lbl_account_details,lbl_enter_personal_details,lbl_create_profile,txt_modify_profile_msg,txt_anonymous_profile_msg,txt_create_profile_msg,txt_fields_are_mandatory,lbl_go_to_users_list,lbl_register,txt_terms_and_conditions_newbie_note,lbl_delete_profile,lbl_submit_n_checkout,lbl_submit_n_checkout,txt_profile_modified,txt_partner_created,txt_profile_created,txt_newbie_registration_bottom,lbl_profile_details"); ?>
<?php if ($this->_tpl_vars['main'] != 'checkout'): ?>
  <?php if ($this->_tpl_vars['login']): ?>
    <h1><?php echo $this->_tpl_vars['lng']['lbl_account_details']; ?>
</h1>
  <?php else: ?>
    <?php if ($this->_tpl_vars['anonymous'] && $this->_tpl_vars['config']['General']['enable_anonymous_checkout'] == 'Y'): ?>
      <h1><?php echo $this->_tpl_vars['lng']['lbl_enter_personal_details']; ?>
</h1>
    <?php else: ?>
      <h1><?php echo $this->_tpl_vars['lng']['lbl_create_profile']; ?>
</h1>
    <?php endif; ?>
  <?php endif; ?>
<?php endif; ?>

<?php if ($this->_tpl_vars['av_error']): ?>

  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/UPS_OnLine_Tools/register.tpl", 'smarty_include_vars' => array('address' => $this->_tpl_vars['av_error']['params'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php else: ?>

  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "check_email_script.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "check_password_script.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "check_zipcode_js.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "check_required_fields_js.tpl", 'smarty_include_vars' => array('fillerror' => $this->_tpl_vars['reg_error'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "change_states_js.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "check_registerform_fields_js.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

  <p class="register-note">

    <?php if ($this->_tpl_vars['newbie'] == 'Y' && $this->_tpl_vars['registered'] == ""): ?>
      <?php if ($this->_tpl_vars['mode'] == 'update'): ?>
        <?php echo $this->_tpl_vars['lng']['txt_modify_profile_msg']; ?>

      <?php else: ?>
        <?php if ($this->_tpl_vars['anonymous'] && $this->_tpl_vars['config']['General']['enable_anonymous_checkout'] == 'Y'): ?>
          <?php echo $this->_tpl_vars['lng']['txt_anonymous_profile_msg']; ?>

        <?php else: ?>
          <?php echo $this->_tpl_vars['lng']['txt_create_profile_msg']; ?>

        <?php endif; ?>
      <?php endif; ?>
      <br />
      <br />
    <?php endif; ?>

    <?php echo $this->_tpl_vars['lng']['txt_fields_are_mandatory']; ?>


  </p>

  <?php ob_start(); ?>

    <?php if ($this->_tpl_vars['newbie'] != 'Y' && $this->_tpl_vars['main'] != 'user_add' && $this->_tpl_vars['is_admin_user']): ?>
      <p class="right-box">
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/buttons/button.tpl", 'smarty_include_vars' => array('button_title' => $this->_tpl_vars['lng']['lbl_go_to_users_list'],'href' => "users.php?mode=search")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
      </p>
    <?php endif; ?>

    <?php if ($this->_tpl_vars['reg_error']): ?>
      <p class="error-message"><?php echo $this->_tpl_vars['reg_error']['errdesc']; ?>
</p>
    <?php endif; ?>

    <?php if ($this->_tpl_vars['registered'] == ""): ?>

      <form action="<?php echo $this->_tpl_vars['register_script_name']; ?>
?<?php echo ((is_array($_tmp=$_SERVER['QUERY_STRING'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)); ?>
" method="post" name="registerform" onsubmit="javascript: return checkRegFormFields(this);">
        <?php if ($this->_tpl_vars['config']['UA']['browser'] == 'MSIE'): ?>
<script type="text/javascript">
//<![CDATA[
<?php echo '
$(function(){
    $(\'input\').keydown(function(e){
        if (e.keyCode == 13) {
            if ($(this).parents(\'form\').get(0).fireEvent("onsubmit"))
              $(this).parents(\'form\').submit();
            return false;
        }
    });
});
'; ?>

//]]>
</script>
        <?php endif; ?>
        <input type="hidden" name="usertype" value="<?php if ($_GET['usertype'] != ""): ?><?php echo ((is_array($_tmp=$_GET['usertype'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php else: ?><?php echo $this->_tpl_vars['usertype']; ?>
<?php endif; ?>" />
        <input type="hidden" name="anonymous" value="<?php echo $this->_tpl_vars['anonymous']; ?>
" />
        <?php if ($this->_tpl_vars['config']['Security']['use_https_login'] == 'Y'): ?>
          <input type="hidden" name="<?php echo $this->_tpl_vars['XCARTSESSNAME']; ?>
" value="<?php echo $this->_tpl_vars['XCARTSESSID']; ?>
" />
        <?php endif; ?>
        <?php if ($_GET['mode'] == 'update'): ?>
          <input type="hidden" name="mode" value="update" />
        <?php endif; ?>

        <table cellspacing="1" class="data-table register-table" summary="<?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_register'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
          <tbody>

            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/main/register_personal_info.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/main/register_additional_info.tpl", 'smarty_include_vars' => array('section' => 'A')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
            
            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/main/register_address_info.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

            <?php if ($this->_tpl_vars['config']['General']['disable_cc'] != 'Y'): ?>
              <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/main/register_ccinfo.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
            <?php endif; ?>

            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/main/register_account.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

            <?php if ($this->_tpl_vars['active_modules']['News_Management'] && $this->_tpl_vars['newslists']): ?>
              <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/News_Management/customer/register_newslists.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
            <?php endif; ?>

            <?php if ($this->_tpl_vars['active_modules']['Image_Verification'] && $this->_tpl_vars['show_antibot']['on_registration'] == 'Y' && $this->_tpl_vars['display_antibot']): ?>
            <tr>
              <td colspan="3">
                <div class="center">
                  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Image_Verification/spambot_arrest.tpl", 'smarty_include_vars' => array('mode' => 'simple_column','id' => $this->_tpl_vars['antibot_sections']['on_registration'],'antibot_err' => $this->_tpl_vars['reg_antibot_err'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                </div>
              </td>
            </tr>
            <?php endif; ?>

            <?php if ($this->_tpl_vars['newbie'] == 'Y'): ?>
            <tr>
              <td colspan="3" class="register-newbie-note">
                  <?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['txt_terms_and_conditions_newbie_note'])) ? $this->_run_mod_handler('substitute', true, $_tmp, 'terms_url', ($this->_tpl_vars['xcart_web_dir'])."/pages.php?alias=conditions") : smarty_modifier_substitute($_tmp, 'terms_url', ($this->_tpl_vars['xcart_web_dir'])."/pages.php?alias=conditions")); ?>

              </td>
            </tr>
            <?php endif; ?>

            <tr>

              <?php if ($_GET['mode'] == 'update'): ?>

                <td class="button-row"><a href="register.php?mode=delete"><?php echo $this->_tpl_vars['lng']['lbl_delete_profile']; ?>
</a></td>
                <td colspan="2" class="button-row">
                  <?php if ($_GET['action'] == 'cart'): ?>
                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/buttons/submit.tpl", 'smarty_include_vars' => array('type' => 'input','additional_button_class' => "main-button",'button_title' => $this->_tpl_vars['lng']['lbl_submit_n_checkout'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                  <?php else: ?>
                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/buttons/submit.tpl", 'smarty_include_vars' => array('type' => 'input','additional_button_class' => "main-button")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                  <?php endif; ?>
                </td>

              <?php else: ?>

                <td colspan="3" class="button-row center">
                  <div class="center">
                    <?php if ($_GET['action'] == 'cart'): ?>
                      <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/buttons/submit.tpl", 'smarty_include_vars' => array('type' => 'input','additional_button_class' => "main-button",'button_title' => $this->_tpl_vars['lng']['lbl_submit_n_checkout'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                    <?php else: ?>
                      <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/buttons/submit.tpl", 'smarty_include_vars' => array('type' => 'input','additional_button_class' => "main-button")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                    <?php endif; ?>
                  </div>
                </td>

              <?php endif; ?>

            </tr>

          </tbody>
        </table>

      </form>

      <?php if (( $this->_tpl_vars['is_areas']['S'] == 'Y' || $this->_tpl_vars['is_areas']['B'] == 'Y' ) && $this->_tpl_vars['active_modules']['UPS_OnLine_Tools'] && $this->_tpl_vars['av_enabled'] == 'Y'): ?>
        <div class="register-ups-box">
          <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/UPS_OnLine_Tools/ups_av_notice.tpl", 'smarty_include_vars' => array('postoffice' => 1)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
          <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/UPS_OnLine_Tools/ups_av_notice.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
        </div>
      <?php endif; ?>

    <?php else: ?>

      <?php if ($_POST['mode'] == 'update' || $_GET['mode'] == 'update'): ?>
        <?php echo $this->_tpl_vars['lng']['txt_profile_modified']; ?>


      <?php elseif ($_GET['usertype'] == 'B' || $this->_tpl_vars['usertype'] == 'B'): ?>
        <?php echo $this->_tpl_vars['lng']['txt_partner_created']; ?>


      <?php else: ?>
        <?php echo $this->_tpl_vars['lng']['txt_profile_created']; ?>

      <?php endif; ?>

    <?php endif; ?>

    <?php if ($this->_tpl_vars['newbie'] == 'Y'): ?>
      <?php echo $this->_tpl_vars['lng']['txt_newbie_registration_bottom']; ?>

    <?php endif; ?>

  <?php $this->_smarty_vars['capture']['dialog'] = ob_get_contents(); ob_end_clean(); ?>
  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/dialog.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['lng']['lbl_profile_details'],'content' => $this->_smarty_vars['capture']['dialog'],'noborder' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php endif; ?>