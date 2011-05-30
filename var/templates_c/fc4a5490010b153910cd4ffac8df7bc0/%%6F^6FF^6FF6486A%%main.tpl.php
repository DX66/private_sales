<?php /* Smarty version 2.6.26, created on 2011-05-26 16:19:10
         compiled from admin/main/main.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'substitute', 'admin/main/main.tpl', 56, false),array('modifier', 'escape', 'admin/main/main.tpl', 58, false),array('modifier', 'date_format', 'admin/main/main.tpl', 165, false),array('modifier', 'truncate', 'admin/main/main.tpl', 183, false),array('modifier', 'replace', 'admin/main/main.tpl', 187, false),array('function', 'cycle', 'admin/main/main.tpl', 114, false),array('function', 'currency', 'admin/main/main.tpl', 125, false),array('function', 'inc', 'admin/main/main.tpl', 251, false),)), $this); ?>
<?php func_load_lang($this, "admin/main/main.tpl","lbl_visit_qteam,lbl_do_not_show_message,txt_your_password_insecured,lbl_chpass,lbl_chpass,lbl_chpass,lbl_chpass,txt_default_passwords_insecured,lbl_users_management,lbl_users_management,txt_blowfish_key_expired,lbl_regenerating_blowfish_key,lbl_regenerating_blowfish_key,txt_db_backup_expired,lbl_backup_database,lbl_backup_database,txt_rma_new_requests_avail_note,lbl_rma_check_new,lbl_rma_check_new,txt_top_info_orders,lbl_status,lbl_since_last_log_in,lbl_today,lbl_this_week,lbl_this_month,lbl_complete,lbl_processed,lbl_queued,lbl_failed,lbl_declined,lbl_not_finished,lbl_gross_total,lbl_total_paid,lbl_search_orders,lbl_search_orders,lbl_last_order,lbl_order_id,lbl_order_date,lbl_order_status,lbl_customer,lbl_ordered,lbl_price,lbl_quantity,lbl_options,lbl_gift_certificate,lbl_price,lbl_order_details_label,lbl_order_details_label,lbl_orders_info,txt_top_info_top_sellers,lbl_top_N_products,lbl_since_last_log_in,lbl_today,lbl_this_week,lbl_this_month,txt_no_top_products_statistics,lbl_top_N_categories,lbl_since_last_log_in,lbl_today,lbl_this_week,lbl_this_month,txt_no_top_categories_statistics,txt_no_statistics,lbl_search_orders,lbl_search_orders,txt_how_setup_store_bottom,lbl_top_sellers"); ?><?php if ($this->_tpl_vars['show_adv'] && $this->_tpl_vars['txt_reg_not_registered'] == ""): ?>
<?php ob_start(); ?>
<table width="100%">
<tr>
<td nowrap="nowrap">
<img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/spacer.gif" alt="" class="qteam-img" width="173" height="47" /><br />
<a href="http://www.qtmsoft.com" target="_blank"><?php echo $this->_tpl_vars['lng']['lbl_visit_qteam']; ?>
</a>
</td>
<td><?php echo $this->_tpl_vars['show_adv']; ?>
</td>
</tr>
<tr>
<td colspan="2"></td>
</tr>
<?php if (! $this->_tpl_vars['shop_evaluation']): ?>
<tr>
<td nowrap="nowrap"><a href="?do_not_show_adv=Y"><?php echo $this->_tpl_vars['lng']['lbl_do_not_show_message']; ?>
</a></td>
<td>&nbsp;</td>
</tr>
<?php endif; ?>
</table>
<?php $this->_smarty_vars['capture']['adv_dialog'] = ob_get_contents(); ob_end_clean(); ?>
<div class="adv">
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "location.tpl", 'smarty_include_vars' => array('location' => "",'alt_content' => $this->_smarty_vars['capture']['adv_dialog'],'extra' => 'width="100%"','newid' => 'adv_qteam','alt_type' => 'I','image_none' => 'Y')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>
<?php endif; ?>

<?php if ($this->_tpl_vars['current_passwords_security'] || $this->_tpl_vars['default_passwords_security'] || $this->_tpl_vars['blowfish_key_expired'] || $this->_tpl_vars['db_backup_expired'] || $this->_tpl_vars['new_rma_requests']): ?>
<?php ob_start(); ?>
<?php if ($this->_tpl_vars['current_passwords_security']): ?>
<div class="SecurityWarning">
<?php echo $this->_tpl_vars['lng']['txt_your_password_insecured']; ?>

<br /><br />
<?php if ($this->_tpl_vars['active_modules']['Simple_Mode']): ?>
<div align="left"><a class="simple-button" title="<?php echo $this->_tpl_vars['lng']['lbl_chpass']; ?>
" href="<?php echo $this->_tpl_vars['catalogs']['provider']; ?>
/change_password.php"><?php echo $this->_tpl_vars['lng']['lbl_chpass']; ?>
</a></div>
<?php else: ?>
<div align="left"><a class="simple-button" title="<?php echo $this->_tpl_vars['lng']['lbl_chpass']; ?>
" href="change_password.php"><?php echo $this->_tpl_vars['lng']['lbl_chpass']; ?>
</a></div>
<?php endif; ?>
</div>
<?php endif; ?>

<?php if ($this->_tpl_vars['default_passwords_security']): ?>
<div class="SecurityWarning">
<?php ob_start(); ?>
<?php unset($this->_sections['acc']);
$this->_sections['acc']['name'] = 'acc';
$this->_sections['acc']['loop'] = is_array($_loop=$this->_tpl_vars['default_passwords_security']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['acc']['show'] = true;
$this->_sections['acc']['max'] = $this->_sections['acc']['loop'];
$this->_sections['acc']['step'] = 1;
$this->_sections['acc']['start'] = $this->_sections['acc']['step'] > 0 ? 0 : $this->_sections['acc']['loop']-1;
if ($this->_sections['acc']['show']) {
    $this->_sections['acc']['total'] = $this->_sections['acc']['loop'];
    if ($this->_sections['acc']['total'] == 0)
        $this->_sections['acc']['show'] = false;
} else
    $this->_sections['acc']['total'] = 0;
if ($this->_sections['acc']['show']):

            for ($this->_sections['acc']['index'] = $this->_sections['acc']['start'], $this->_sections['acc']['iteration'] = 1;
                 $this->_sections['acc']['iteration'] <= $this->_sections['acc']['total'];
                 $this->_sections['acc']['index'] += $this->_sections['acc']['step'], $this->_sections['acc']['iteration']++):
$this->_sections['acc']['rownum'] = $this->_sections['acc']['iteration'];
$this->_sections['acc']['index_prev'] = $this->_sections['acc']['index'] - $this->_sections['acc']['step'];
$this->_sections['acc']['index_next'] = $this->_sections['acc']['index'] + $this->_sections['acc']['step'];
$this->_sections['acc']['first']      = ($this->_sections['acc']['iteration'] == 1);
$this->_sections['acc']['last']       = ($this->_sections['acc']['iteration'] == $this->_sections['acc']['total']);
?>
<?php if ($this->_tpl_vars['default_passwords_security'][$this->_sections['acc']['index']] != $this->_tpl_vars['current_passwords_security']['0']): ?>
<?php $this->assign('display_default_passwords_security', '1'); ?>
&nbsp;&nbsp;&nbsp;<?php echo $this->_tpl_vars['default_passwords_security'][$this->_sections['acc']['index']]; ?>
<br />
<?php endif; ?>
<?php endfor; endif; ?>
<?php $this->_smarty_vars['capture']['accounts'] = ob_get_contents(); ob_end_clean(); ?>
<?php if ($this->_tpl_vars['display_default_passwords_security']): ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['txt_default_passwords_insecured'])) ? $this->_run_mod_handler('substitute', true, $_tmp, 'accounts', $this->_smarty_vars['capture']['accounts']) : smarty_modifier_substitute($_tmp, 'accounts', $this->_smarty_vars['capture']['accounts'])); ?>

<br /><br />
<div align="left"><a class="simple-button" title="<?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_users_management'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" href="users.php"><?php echo $this->_tpl_vars['lng']['lbl_users_management']; ?>
</a></div>
<?php endif; ?>
</div>
<?php endif; ?>

<?php if ($this->_tpl_vars['blowfish_key_expired']): ?>
<div class="SecurityWarning">
<?php echo $this->_tpl_vars['lng']['txt_blowfish_key_expired']; ?>

<br /><br />
<div align="left"><a class="simple-button" title="<?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_regenerating_blowfish_key'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" href="tools.php#regenbk"><?php echo $this->_tpl_vars['lng']['lbl_regenerating_blowfish_key']; ?>
</a></div>
</div>
<?php endif; ?>

<?php if ($this->_tpl_vars['db_backup_expired']): ?>
<div class="SecurityWarning">
<?php echo $this->_tpl_vars['lng']['txt_db_backup_expired']; ?>

<br /><br />
<div align="left"><a class="simple-button" title="<?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_backup_database'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" href="db_backup.php"><?php echo $this->_tpl_vars['lng']['lbl_backup_database']; ?>
</a></div>
</div>
<?php endif; ?>

<?php if ($this->_tpl_vars['new_rma_requests']): ?>
<div class="SecurityWarning">
<?php echo $this->_tpl_vars['lng']['txt_rma_new_requests_avail_note']; ?>

<br /><br />
<div align="left"><a class="simple-button" title="<?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_rma_check_new'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" href="returns.php?new"><?php echo $this->_tpl_vars['lng']['lbl_rma_check_new']; ?>
</a></div>
</div>
<?php endif; ?>
<?php $this->_smarty_vars['capture']['dialog'] = ob_get_contents(); ob_end_clean(); ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "location.tpl", 'smarty_include_vars' => array('location' => "",'alt_content' => $this->_smarty_vars['capture']['dialog'],'extra' => 'width="100%"','newid' => 'password_security','alt_type' => 'W')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>
<br />
<!-- QUICK MENU -->
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/quick_menu.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<!-- QUICK MENU -->

<a name="orders"></a>
<?php ob_start(); ?>
<?php echo $this->_tpl_vars['lng']['txt_top_info_orders']; ?>

<br /><br />
<div align="center">
<table cellpadding="0" cellspacing="0" width="90%">
<tr>
<td class="TableHead">

<table cellpadding="3" cellspacing="1" width="100%">
<tr class="TableHead">
<td><?php echo $this->_tpl_vars['lng']['lbl_status']; ?>
</td>
<td nowrap="nowrap" align="center"><?php echo $this->_tpl_vars['lng']['lbl_since_last_log_in']; ?>
</td>
<td align="center"><?php echo $this->_tpl_vars['lng']['lbl_today']; ?>
</td>
<td nowrap="nowrap" align="center"><?php echo $this->_tpl_vars['lng']['lbl_this_week']; ?>
</td>
<td nowrap="nowrap" align="center"><?php echo $this->_tpl_vars['lng']['lbl_this_month']; ?>
</td>
</tr>

<?php $_from = $this->_tpl_vars['orders']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
<tr class="<?php echo smarty_function_cycle(array('values' => 'SectionBox,TableSubHead'), $this);?>
">
<td nowrap="nowrap" align="left"><?php if ($this->_tpl_vars['key'] == 'C'): ?><?php echo $this->_tpl_vars['lng']['lbl_complete']; ?>
<?php elseif ($this->_tpl_vars['key'] == 'P'): ?><?php echo $this->_tpl_vars['lng']['lbl_processed']; ?>
<?php elseif ($this->_tpl_vars['key'] == 'Q'): ?><?php echo $this->_tpl_vars['lng']['lbl_queued']; ?>
<?php elseif ($this->_tpl_vars['key'] == 'F' || $this->_tpl_vars['key'] == 'D'): ?><?php echo $this->_tpl_vars['lng']['lbl_failed']; ?>
/<?php echo $this->_tpl_vars['lng']['lbl_declined']; ?>
<?php elseif ($this->_tpl_vars['key'] == 'I'): ?><?php echo $this->_tpl_vars['lng']['lbl_not_finished']; ?>
<?php endif; ?>:</td>
<?php unset($this->_sections['period']);
$this->_sections['period']['name'] = 'period';
$this->_sections['period']['loop'] = is_array($_loop=$this->_tpl_vars['item']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['period']['show'] = true;
$this->_sections['period']['max'] = $this->_sections['period']['loop'];
$this->_sections['period']['step'] = 1;
$this->_sections['period']['start'] = $this->_sections['period']['step'] > 0 ? 0 : $this->_sections['period']['loop']-1;
if ($this->_sections['period']['show']) {
    $this->_sections['period']['total'] = $this->_sections['period']['loop'];
    if ($this->_sections['period']['total'] == 0)
        $this->_sections['period']['show'] = false;
} else
    $this->_sections['period']['total'] = 0;
if ($this->_sections['period']['show']):

            for ($this->_sections['period']['index'] = $this->_sections['period']['start'], $this->_sections['period']['iteration'] = 1;
                 $this->_sections['period']['iteration'] <= $this->_sections['period']['total'];
                 $this->_sections['period']['index'] += $this->_sections['period']['step'], $this->_sections['period']['iteration']++):
$this->_sections['period']['rownum'] = $this->_sections['period']['iteration'];
$this->_sections['period']['index_prev'] = $this->_sections['period']['index'] - $this->_sections['period']['step'];
$this->_sections['period']['index_next'] = $this->_sections['period']['index'] + $this->_sections['period']['step'];
$this->_sections['period']['first']      = ($this->_sections['period']['iteration'] == 1);
$this->_sections['period']['last']       = ($this->_sections['period']['iteration'] == $this->_sections['period']['total']);
?>
<td align="center"><?php echo $this->_tpl_vars['item'][$this->_sections['period']['index']]; ?>
</td>
<?php endfor; endif; ?>
</tr>
<?php endforeach; endif; unset($_from); ?>

<tr class="<?php echo smarty_function_cycle(array('values' => 'SectionBox,TableSubHead'), $this);?>
">
<td align="right"><b><?php echo $this->_tpl_vars['lng']['lbl_gross_total']; ?>
:</b></td>
<?php unset($this->_sections['period']);
$this->_sections['period']['name'] = 'period';
$this->_sections['period']['loop'] = is_array($_loop=$this->_tpl_vars['gross_total']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['period']['show'] = true;
$this->_sections['period']['max'] = $this->_sections['period']['loop'];
$this->_sections['period']['step'] = 1;
$this->_sections['period']['start'] = $this->_sections['period']['step'] > 0 ? 0 : $this->_sections['period']['loop']-1;
if ($this->_sections['period']['show']) {
    $this->_sections['period']['total'] = $this->_sections['period']['loop'];
    if ($this->_sections['period']['total'] == 0)
        $this->_sections['period']['show'] = false;
} else
    $this->_sections['period']['total'] = 0;
if ($this->_sections['period']['show']):

            for ($this->_sections['period']['index'] = $this->_sections['period']['start'], $this->_sections['period']['iteration'] = 1;
                 $this->_sections['period']['iteration'] <= $this->_sections['period']['total'];
                 $this->_sections['period']['index'] += $this->_sections['period']['step'], $this->_sections['period']['iteration']++):
$this->_sections['period']['rownum'] = $this->_sections['period']['iteration'];
$this->_sections['period']['index_prev'] = $this->_sections['period']['index'] - $this->_sections['period']['step'];
$this->_sections['period']['index_next'] = $this->_sections['period']['index'] + $this->_sections['period']['step'];
$this->_sections['period']['first']      = ($this->_sections['period']['iteration'] == 1);
$this->_sections['period']['last']       = ($this->_sections['period']['iteration'] == $this->_sections['period']['total']);
?>
<td align="center"><?php echo smarty_function_currency(array('value' => $this->_tpl_vars['gross_total'][$this->_sections['period']['index']]), $this);?>
</td>
<?php endfor; endif; ?> 
</tr>

<tr class="<?php echo smarty_function_cycle(array('values' => 'SectionBox,TableSubHead'), $this);?>
">
<td align="right"><b><?php echo $this->_tpl_vars['lng']['lbl_total_paid']; ?>
:</b></td>
<?php unset($this->_sections['period']);
$this->_sections['period']['name'] = 'period';
$this->_sections['period']['loop'] = is_array($_loop=$this->_tpl_vars['total_paid']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['period']['show'] = true;
$this->_sections['period']['max'] = $this->_sections['period']['loop'];
$this->_sections['period']['step'] = 1;
$this->_sections['period']['start'] = $this->_sections['period']['step'] > 0 ? 0 : $this->_sections['period']['loop']-1;
if ($this->_sections['period']['show']) {
    $this->_sections['period']['total'] = $this->_sections['period']['loop'];
    if ($this->_sections['period']['total'] == 0)
        $this->_sections['period']['show'] = false;
} else
    $this->_sections['period']['total'] = 0;
if ($this->_sections['period']['show']):

            for ($this->_sections['period']['index'] = $this->_sections['period']['start'], $this->_sections['period']['iteration'] = 1;
                 $this->_sections['period']['iteration'] <= $this->_sections['period']['total'];
                 $this->_sections['period']['index'] += $this->_sections['period']['step'], $this->_sections['period']['iteration']++):
$this->_sections['period']['rownum'] = $this->_sections['period']['iteration'];
$this->_sections['period']['index_prev'] = $this->_sections['period']['index'] - $this->_sections['period']['step'];
$this->_sections['period']['index_next'] = $this->_sections['period']['index'] + $this->_sections['period']['step'];
$this->_sections['period']['first']      = ($this->_sections['period']['iteration'] == 1);
$this->_sections['period']['last']       = ($this->_sections['period']['iteration'] == $this->_sections['period']['total']);
?>
<td align="center"><?php echo smarty_function_currency(array('value' => $this->_tpl_vars['total_paid'][$this->_sections['period']['index']]), $this);?>
</td>
<?php endfor; endif; ?>
</tr>
</table>

</td>
</tr>
</table>
</div>

<br /><br />

<div align="right"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('button_title' => $this->_tpl_vars['lng']['lbl_search_orders'],'href' => "orders.php",'title' => $this->_tpl_vars['lng']['lbl_search_orders'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></div>

<?php if ($this->_tpl_vars['last_order']): ?>
<br /><br />

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/subheader.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['lng']['lbl_last_order'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<table cellpadding="3" cellspacing="1" width="100%">

<tr>
<td>&nbsp;&nbsp;</td>
<td>
<table cellpadding="3" cellspacing="1">

<tr>
<td class="FormButton"><?php echo $this->_tpl_vars['lng']['lbl_order_id']; ?>
:</td>
<td>#<?php echo $this->_tpl_vars['last_order']['orderid']; ?>
</td>
</tr>

<tr>
<td class="FormButton"><?php echo $this->_tpl_vars['lng']['lbl_order_date']; ?>
:</td>
<td><?php echo ((is_array($_tmp=$this->_tpl_vars['last_order']['date'])) ? $this->_run_mod_handler('date_format', true, $_tmp, $this->_tpl_vars['config']['Appearance']['datetime_format']) : smarty_modifier_date_format($_tmp, $this->_tpl_vars['config']['Appearance']['datetime_format'])); ?>
</td>
</tr>

<tr>
<td class="FormButton"><?php echo $this->_tpl_vars['lng']['lbl_order_status']; ?>
:</td>
<td><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/order_status.tpl", 'smarty_include_vars' => array('status' => $this->_tpl_vars['last_order']['status'],'mode' => 'static')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></td>
</tr>

<tr>
<td class="FormButton"><?php echo $this->_tpl_vars['lng']['lbl_customer']; ?>
:</td>
<td><?php echo $this->_tpl_vars['last_order']['title']; ?>
 <?php echo $this->_tpl_vars['last_order']['firstname']; ?>
 <?php echo $this->_tpl_vars['last_order']['lastname']; ?>
</td>
</tr>

<tr>
<td class="FormButton" valign="top"><?php echo $this->_tpl_vars['lng']['lbl_ordered']; ?>
:</td>
<td>
<?php if ($this->_tpl_vars['last_order']['products']): ?>
<?php unset($this->_sections['product']);
$this->_sections['product']['name'] = 'product';
$this->_sections['product']['loop'] = is_array($_loop=$this->_tpl_vars['last_order']['products']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['product']['show'] = true;
$this->_sections['product']['max'] = $this->_sections['product']['loop'];
$this->_sections['product']['step'] = 1;
$this->_sections['product']['start'] = $this->_sections['product']['step'] > 0 ? 0 : $this->_sections['product']['loop']-1;
if ($this->_sections['product']['show']) {
    $this->_sections['product']['total'] = $this->_sections['product']['loop'];
    if ($this->_sections['product']['total'] == 0)
        $this->_sections['product']['show'] = false;
} else
    $this->_sections['product']['total'] = 0;
if ($this->_sections['product']['show']):

            for ($this->_sections['product']['index'] = $this->_sections['product']['start'], $this->_sections['product']['iteration'] = 1;
                 $this->_sections['product']['iteration'] <= $this->_sections['product']['total'];
                 $this->_sections['product']['index'] += $this->_sections['product']['step'], $this->_sections['product']['iteration']++):
$this->_sections['product']['rownum'] = $this->_sections['product']['iteration'];
$this->_sections['product']['index_prev'] = $this->_sections['product']['index'] - $this->_sections['product']['step'];
$this->_sections['product']['index_next'] = $this->_sections['product']['index'] + $this->_sections['product']['step'];
$this->_sections['product']['first']      = ($this->_sections['product']['iteration'] == 1);
$this->_sections['product']['last']       = ($this->_sections['product']['iteration'] == $this->_sections['product']['total']);
?>
<b><?php echo ((is_array($_tmp=$this->_tpl_vars['last_order']['products'][$this->_sections['product']['index']]['product'])) ? $this->_run_mod_handler('truncate', true, $_tmp, '30', "...") : smarty_modifier_truncate($_tmp, '30', "...")); ?>
</b>
[<?php echo $this->_tpl_vars['lng']['lbl_price']; ?>
: <?php echo smarty_function_currency(array('value' => $this->_tpl_vars['last_order']['products'][$this->_sections['product']['index']]['price']), $this);?>
, <?php echo $this->_tpl_vars['lng']['lbl_quantity']; ?>
: <?php echo $this->_tpl_vars['last_order']['products'][$this->_sections['product']['index']]['amount']; ?>
]
<?php if ($this->_tpl_vars['last_order']['products'][$this->_sections['product']['index']]['product_options']): ?>
<br />
<?php echo $this->_tpl_vars['lng']['lbl_options']; ?>
: <?php echo ((is_array($_tmp=$this->_tpl_vars['last_order']['products'][$this->_sections['product']['index']]['product_options'])) ? $this->_run_mod_handler('replace', true, $_tmp, "\n", "; ") : smarty_modifier_replace($_tmp, "\n", "; ")); ?>

<?php endif; ?>
<br />
<?php endfor; endif; ?>
<?php endif; ?>
<?php if ($this->_tpl_vars['last_order']['giftcerts']): ?>
<?php unset($this->_sections['gc']);
$this->_sections['gc']['name'] = 'gc';
$this->_sections['gc']['loop'] = is_array($_loop=$this->_tpl_vars['last_order']['giftcerts']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['gc']['show'] = true;
$this->_sections['gc']['max'] = $this->_sections['gc']['loop'];
$this->_sections['gc']['step'] = 1;
$this->_sections['gc']['start'] = $this->_sections['gc']['step'] > 0 ? 0 : $this->_sections['gc']['loop']-1;
if ($this->_sections['gc']['show']) {
    $this->_sections['gc']['total'] = $this->_sections['gc']['loop'];
    if ($this->_sections['gc']['total'] == 0)
        $this->_sections['gc']['show'] = false;
} else
    $this->_sections['gc']['total'] = 0;
if ($this->_sections['gc']['show']):

            for ($this->_sections['gc']['index'] = $this->_sections['gc']['start'], $this->_sections['gc']['iteration'] = 1;
                 $this->_sections['gc']['iteration'] <= $this->_sections['gc']['total'];
                 $this->_sections['gc']['index'] += $this->_sections['gc']['step'], $this->_sections['gc']['iteration']++):
$this->_sections['gc']['rownum'] = $this->_sections['gc']['iteration'];
$this->_sections['gc']['index_prev'] = $this->_sections['gc']['index'] - $this->_sections['gc']['step'];
$this->_sections['gc']['index_next'] = $this->_sections['gc']['index'] + $this->_sections['gc']['step'];
$this->_sections['gc']['first']      = ($this->_sections['gc']['iteration'] == 1);
$this->_sections['gc']['last']       = ($this->_sections['gc']['iteration'] == $this->_sections['gc']['total']);
?>
<b><?php echo $this->_tpl_vars['lng']['lbl_gift_certificate']; ?>
 #<?php echo $this->_tpl_vars['last_order']['giftcerts'][$this->_sections['gc']['index']]['gcid']; ?>
</b>
[<?php echo $this->_tpl_vars['lng']['lbl_price']; ?>
: <?php echo smarty_function_currency(array('value' => $this->_tpl_vars['last_order']['giftcerts'][$this->_sections['gc']['index']]['amount']), $this);?>
]
<br />
<?php endfor; endif; ?>
<?php endif; ?>
</td>
</tr>

</table>
</td>
</tr>

</table>

<br />

<div align="right"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('button_title' => $this->_tpl_vars['lng']['lbl_order_details_label'],'href' => "order.php?orderid=".($this->_tpl_vars['last_order']['orderid']),'title' => $this->_tpl_vars['lng']['lbl_order_details_label'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></div>

<?php endif; ?>

<?php $this->_smarty_vars['capture']['dialog'] = ob_get_contents(); ob_end_clean(); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "dialog.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['lng']['lbl_orders_info'],'content' => $this->_smarty_vars['capture']['dialog'],'extra' => 'width="100%"')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<br /><br />

<a name="topsellers"></a>
<?php ob_start(); ?>

<?php echo $this->_tpl_vars['lng']['txt_top_info_top_sellers']; ?>


<br /><br />

<div class="TopLabel" align="center"><?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_top_N_products'])) ? $this->_run_mod_handler('substitute', true, $_tmp, 'N', $this->_tpl_vars['max_top_sellers']) : smarty_modifier_substitute($_tmp, 'N', $this->_tpl_vars['max_top_sellers'])); ?>
</div>

<br />

<table cellpadding="0" cellspacing="0" width="100%">
<tr>
<td class="TableHead">
<table cellpadding="3" cellspacing="1" width="100%">

<tr class="TableHead">
  <td width="25%" nowrap="nowrap" align="center"><?php echo $this->_tpl_vars['lng']['lbl_since_last_log_in']; ?>
</td>
  <td width="25%" nowrap="nowrap" align="center"><?php echo $this->_tpl_vars['lng']['lbl_today']; ?>
</td>
  <td width="25%" nowrap="nowrap" align="center"><?php echo $this->_tpl_vars['lng']['lbl_this_week']; ?>
</td>
  <td width="25%" nowrap="nowrap" align="center"><?php echo $this->_tpl_vars['lng']['lbl_this_month']; ?>
</td>
</tr>

<?php ob_start(); ?>
<tr class="SectionBox">
<?php $_from = $this->_tpl_vars['top_sellers']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
<td align="center"<?php if ($this->_tpl_vars['item']): ?> valign="top"<?php endif; ?>>
<?php if ($this->_tpl_vars['item']): ?>
<?php $this->assign('is_top_products', '1'); ?>
<table cellpadding="2" cellspacing="1" width="100%">
<?php unset($this->_sections['period']);
$this->_sections['period']['name'] = 'period';
$this->_sections['period']['loop'] = is_array($_loop=$this->_tpl_vars['item']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['period']['show'] = true;
$this->_sections['period']['max'] = $this->_sections['period']['loop'];
$this->_sections['period']['step'] = 1;
$this->_sections['period']['start'] = $this->_sections['period']['step'] > 0 ? 0 : $this->_sections['period']['loop']-1;
if ($this->_sections['period']['show']) {
    $this->_sections['period']['total'] = $this->_sections['period']['loop'];
    if ($this->_sections['period']['total'] == 0)
        $this->_sections['period']['show'] = false;
} else
    $this->_sections['period']['total'] = 0;
if ($this->_sections['period']['show']):

            for ($this->_sections['period']['index'] = $this->_sections['period']['start'], $this->_sections['period']['iteration'] = 1;
                 $this->_sections['period']['iteration'] <= $this->_sections['period']['total'];
                 $this->_sections['period']['index'] += $this->_sections['period']['step'], $this->_sections['period']['iteration']++):
$this->_sections['period']['rownum'] = $this->_sections['period']['iteration'];
$this->_sections['period']['index_prev'] = $this->_sections['period']['index'] - $this->_sections['period']['step'];
$this->_sections['period']['index_next'] = $this->_sections['period']['index'] + $this->_sections['period']['step'];
$this->_sections['period']['first']      = ($this->_sections['period']['iteration'] == 1);
$this->_sections['period']['last']       = ($this->_sections['period']['iteration'] == $this->_sections['period']['total']);
?>
<tr<?php echo smarty_function_cycle(array('name' => "col`%period.index%`",'values' => ', class="TableSubHead"'), $this);?>
>
  <td><?php echo smarty_function_inc(array('value' => $this->_sections['period']['index']), $this);?>
.</td>
  <td align="left"><a href="product_modify.php?productid=<?php echo $this->_tpl_vars['item'][$this->_sections['period']['index']]['productid']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['item'][$this->_sections['period']['index']]['product'])) ? $this->_run_mod_handler('truncate', true, $_tmp, '20', "...") : smarty_modifier_truncate($_tmp, '20', "...")); ?>
</a></td>
  <td><?php echo $this->_tpl_vars['item'][$this->_sections['period']['index']]['count']; ?>
</td>
</tr>
<?php endfor; endif; ?>
</table>
<?php else: ?>
<?php echo $this->_tpl_vars['lng']['txt_no_top_products_statistics']; ?>

<?php endif; ?>
</td>
<?php endforeach; endif; unset($_from); ?>
</tr>
<?php $this->_smarty_vars['capture']['top_products'] = ob_get_contents(); ob_end_clean(); ?>

<?php if ($this->_tpl_vars['is_top_products']): ?>

<?php echo $this->_smarty_vars['capture']['top_products']; ?>


</table>
</td>
</tr>
</table>

<br />

<div class="TopLabel" align="center"><?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_top_N_categories'])) ? $this->_run_mod_handler('substitute', true, $_tmp, 'N', $this->_tpl_vars['max_top_sellers']) : smarty_modifier_substitute($_tmp, 'N', $this->_tpl_vars['max_top_sellers'])); ?>
</div>

<br />

<table cellpadding="0" cellspacing="0" width="100%">
<tr>
<td class="TableHead">
<table cellpadding="3" cellspacing="1" width="100%">

<tr class="TableHead">
  <td width="25%" nowrap="nowrap" align="center"><?php echo $this->_tpl_vars['lng']['lbl_since_last_log_in']; ?>
</td>
  <td width="25%" nowrap="nowrap" align="center"><?php echo $this->_tpl_vars['lng']['lbl_today']; ?>
</td>
  <td width="25%" nowrap="nowrap" align="center"><?php echo $this->_tpl_vars['lng']['lbl_this_week']; ?>
</td>
  <td width="25%" nowrap="nowrap" align="center"><?php echo $this->_tpl_vars['lng']['lbl_this_month']; ?>
</td>
</tr>

<tr class="SectionBox">
<?php $_from = $this->_tpl_vars['top_categories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
<td align="center"<?php if ($this->_tpl_vars['item']): ?> valign="top"<?php endif; ?>>
<?php if ($this->_tpl_vars['item']): ?>
<table cellpadding="2" cellspacing="1" width="100%">
<?php unset($this->_sections['period']);
$this->_sections['period']['name'] = 'period';
$this->_sections['period']['loop'] = is_array($_loop=$this->_tpl_vars['item']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['period']['show'] = true;
$this->_sections['period']['max'] = $this->_sections['period']['loop'];
$this->_sections['period']['step'] = 1;
$this->_sections['period']['start'] = $this->_sections['period']['step'] > 0 ? 0 : $this->_sections['period']['loop']-1;
if ($this->_sections['period']['show']) {
    $this->_sections['period']['total'] = $this->_sections['period']['loop'];
    if ($this->_sections['period']['total'] == 0)
        $this->_sections['period']['show'] = false;
} else
    $this->_sections['period']['total'] = 0;
if ($this->_sections['period']['show']):

            for ($this->_sections['period']['index'] = $this->_sections['period']['start'], $this->_sections['period']['iteration'] = 1;
                 $this->_sections['period']['iteration'] <= $this->_sections['period']['total'];
                 $this->_sections['period']['index'] += $this->_sections['period']['step'], $this->_sections['period']['iteration']++):
$this->_sections['period']['rownum'] = $this->_sections['period']['iteration'];
$this->_sections['period']['index_prev'] = $this->_sections['period']['index'] - $this->_sections['period']['step'];
$this->_sections['period']['index_next'] = $this->_sections['period']['index'] + $this->_sections['period']['step'];
$this->_sections['period']['first']      = ($this->_sections['period']['iteration'] == 1);
$this->_sections['period']['last']       = ($this->_sections['period']['iteration'] == $this->_sections['period']['total']);
?>
<tr<?php echo smarty_function_cycle(array('name' => "col`%period.index%`",'values' => ", class='TableSubHead'"), $this);?>
>
  <td><?php echo smarty_function_inc(array('value' => $this->_sections['period']['index']), $this);?>
.</td>
  <td align="left"><a href="category_modify.php?cat=<?php echo $this->_tpl_vars['item'][$this->_sections['period']['index']]['categoryid']; ?>
"><?php echo $this->_tpl_vars['item'][$this->_sections['period']['index']]['category']; ?>
</a></td>
  <td><?php echo $this->_tpl_vars['item'][$this->_sections['period']['index']]['count']; ?>
</td>
</tr>
<?php endfor; endif; ?>
</table>
<?php else: ?>
<?php echo $this->_tpl_vars['lng']['txt_no_top_categories_statistics']; ?>

<?php endif; ?>
</td>
<?php endforeach; endif; unset($_from); ?>
</tr>

<?php else: ?>

<tr class="SectionBox">
  <td colspan="4" align="center"><?php echo $this->_tpl_vars['lng']['txt_no_statistics']; ?>
</td>
</tr>

<?php endif; ?>

</table>
</td>
</tr>
</table>

<br /><br />

<div align="right"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('button_title' => $this->_tpl_vars['lng']['lbl_search_orders'],'href' => "orders.php",'title' => $this->_tpl_vars['lng']['lbl_search_orders'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></div><?php echo $this->_tpl_vars['lng']['txt_how_setup_store_bottom']; ?>


<?php $this->_smarty_vars['capture']['dialog'] = ob_get_contents(); ob_end_clean(); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "dialog.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['lng']['lbl_top_sellers'],'content' => $this->_smarty_vars['capture']['dialog'],'extra' => 'width="100%"')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>