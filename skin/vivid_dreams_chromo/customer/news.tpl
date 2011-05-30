{*
$Id: news.tpl,v 1.1 2010/05/21 08:33:01 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $active_modules.News_Management}
  {insert name="gate" func="news_exist" assign="is_news_exist" lngcode=$shop_language}

  {if $is_news_exist}

    {insert name="gate" func="news_subscription_allowed" assign="is_subscription_allowed" lngcode=$shop_language}

    <td>
      <div class="news-box">

        <h2 id="news_title">{$lng.lbl_news}</h2>

      <div class="news">

        {if $news_message eq ""}

          {$lng.txt_no_news_available}

        {else}

          <strong>{$news_message.date|date_format:$config.Appearance.date_format}</strong><br />
          {$news_message.body}<br /><br />
          <a href="news.php" class="prev-news">{$lng.lbl_previous_news}</a>
          {if $is_subscription_allowed}
            &nbsp;&nbsp;
            <a href="news.php#subscribe" class="subscribe">{$lng.lbl_subscribe}</a>
          {/if}

        {/if}

      </div>

      </div>
    </td>
  {/if}
{/if}
