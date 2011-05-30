<?php /* Smarty version 2.6.26, created on 2011-05-26 16:20:21
         compiled from customer/news.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('insert', 'gate', 'customer/news.tpl', 6, false),array('modifier', 'date_format', 'customer/news.tpl', 24, false),)), $this); ?>
<?php func_load_lang($this, "customer/news.tpl","lbl_news,txt_no_news_available,lbl_previous_news,lbl_subscribe"); ?><?php if ($this->_tpl_vars['active_modules']['News_Management']): ?>
  <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'gate', 'func' => 'news_exist', 'assign' => 'is_news_exist', 'lngcode' => $this->_tpl_vars['shop_language'])), $this); ?>


  <?php if ($this->_tpl_vars['is_news_exist']): ?>

    <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'gate', 'func' => 'news_subscription_allowed', 'assign' => 'is_subscription_allowed', 'lngcode' => $this->_tpl_vars['shop_language'])), $this); ?>


    <div class="news-box">

      <div class="news">

        <h2><?php echo $this->_tpl_vars['lng']['lbl_news']; ?>
</h2>

        <?php if ($this->_tpl_vars['news_message'] == ""): ?>

          <?php echo $this->_tpl_vars['lng']['txt_no_news_available']; ?>


        <?php else: ?>

          <strong><?php echo ((is_array($_tmp=$this->_tpl_vars['news_message']['date'])) ? $this->_run_mod_handler('date_format', true, $_tmp, $this->_tpl_vars['config']['Appearance']['date_format']) : smarty_modifier_date_format($_tmp, $this->_tpl_vars['config']['Appearance']['date_format'])); ?>
</strong><br />
          <?php echo $this->_tpl_vars['news_message']['body']; ?>
<br /><br />
          <a href="news.php" class="prev-news"><?php echo $this->_tpl_vars['lng']['lbl_previous_news']; ?>
</a>
          <?php if ($this->_tpl_vars['is_subscription_allowed']): ?>
            &nbsp;&nbsp;
            <a href="news.php#subscribe" class="subscribe"><?php echo $this->_tpl_vars['lng']['lbl_subscribe']; ?>
</a>
          <?php endif; ?>

        <?php endif; ?>

      </div>

    </div>

  <?php endif; ?>
<?php endif; ?>