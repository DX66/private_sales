<?php /* Smarty version 2.6.26, created on 2011-05-27 11:08:41
         compiled from main/edit_product_image.tpl */ ?>
<?php func_load_lang($this, "main/edit_product_image.tpl","lbl_view_full_size"); ?><?php if ($this->_tpl_vars['idtag'] == ''): ?><?php $this->assign('idtag', 'edit_image'); ?><?php endif; ?>
<table cellpadding="0" cellspacing="0" style="<?php if ($this->_tpl_vars['type'] == 'P'): ?>width: <?php echo $this->_tpl_vars['config']['images_dimensions']['P']['width']; ?>
px; height: <?php echo $this->_tpl_vars['config']['images_dimensions']['P']['height']; ?>
px<?php else: ?>width: <?php echo $this->_tpl_vars['config']['images_dimensions']['T']['width']; ?>
px; height: <?php echo $this->_tpl_vars['config']['images_dimensions']['T']['height']; ?>
px<?php endif; ?>">
<tr><td class="ProductDetailsImage" align="center" valign="middle">
<a title="<?php echo $this->_tpl_vars['lng']['lbl_view_full_size']; ?>
" id='a_<?php echo $this->_tpl_vars['idtag']; ?>
' href="<?php echo $this->_tpl_vars['xcart_web_dir']; ?>
/image.php?type=<?php echo $this->_tpl_vars['type']; ?>
&amp;id=<?php echo $this->_tpl_vars['id']; ?>
&amp;ts=<?php echo XC_TIME; ?>
<?php if ($this->_tpl_vars['already_loaded']): ?>&amp;tmp=Y<?php endif; ?>" target="_blank">
<img id="<?php echo $this->_tpl_vars['idtag']; ?>
" src="<?php echo $this->_tpl_vars['xcart_web_dir']; ?>
/image.php?type=<?php echo $this->_tpl_vars['type']; ?>
&amp;id=<?php echo $this->_tpl_vars['id']; ?>
&amp;ts=<?php echo XC_TIME; ?>
<?php if ($this->_tpl_vars['already_loaded']): ?>&amp;tmp=Y<?php endif; ?>"<?php if ($this->_tpl_vars['image_x'] != 0): ?> width="<?php echo $this->_tpl_vars['image_x']; ?>
"<?php endif; ?><?php if ($this->_tpl_vars['image_y'] != 0): ?> height="<?php echo $this->_tpl_vars['image_y']; ?>
"<?php endif; ?> alt="<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "main/image_property.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>"/>
</a>
<input id="skip_image_<?php echo $this->_tpl_vars['type']; ?>
" type="hidden" name="skip_image[<?php echo $this->_tpl_vars['type']; ?>
]" value="" />
</td></tr>
</table>
