<?php /* Smarty version 2.6.26, created on 2011-05-26 16:19:11
         compiled from rectangle_bottom.tpl */ ?>
</td>
</tr>

<tr>
  <td class="BottomRow">
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "bottom.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
  </td>
</tr>

</table>
<?php if ($this->_tpl_vars['config']['UA']['browser'] == 'MSIE' && ( $this->_tpl_vars['config']['UA']['version'] == "6.0" || $this->_tpl_vars['config']['UA']['version'] == "7.0" )): ?>
<script type="text/javascript">
//<![CDATA[
<?php echo '
$("#horizontal-menu li").hover(
  function () {
    $(this).find("div").toggleClass(\'horizontal-menu-li-hover-div\');
  }
);
$("#horizontal-menu").css(\'top\', \'49px\');
'; ?>

//]]>
</script>
<?php endif; ?>