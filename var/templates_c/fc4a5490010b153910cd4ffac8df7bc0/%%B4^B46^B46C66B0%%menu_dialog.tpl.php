<?php /* Smarty version 2.6.26, created on 2011-05-26 16:20:21
         compiled from customer/menu_dialog.tpl */ ?>
<div class="menu-dialog<?php if ($this->_tpl_vars['additional_class']): ?> <?php echo $this->_tpl_vars['additional_class']; ?>
<?php endif; ?>">
  <div class="title-bar <?php if ($this->_tpl_vars['link_href']): ?> link-title<?php endif; ?>">
    <?php echo ''; ?><?php if ($this->_tpl_vars['link_href']): ?><?php echo '<span class="title-link"><a href="'; ?><?php echo $this->_tpl_vars['link_href']; ?><?php echo '" class="title-link"><img src="'; ?><?php echo $this->_tpl_vars['ImagesDir']; ?><?php echo '/spacer.gif" alt=""  /></a></span>'; ?><?php endif; ?><?php echo '<img class="icon ajax-minicart-icon" src="'; ?><?php echo $this->_tpl_vars['ImagesDir']; ?><?php echo '/spacer.gif" alt="" /><h2>'; ?><?php echo $this->_tpl_vars['title']; ?><?php echo '</h2>'; ?>

  </div>
  <div class="content">
    <?php echo $this->_tpl_vars['content']; ?>

  </div>
</div>