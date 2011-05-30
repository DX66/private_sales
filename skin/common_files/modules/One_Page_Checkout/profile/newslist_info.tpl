{*
$Id: newslist_info.tpl,v 1.1 2010/05/21 08:32:46 joy Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
{if $active_modules.News_Management and $newslists}

  {if not $hide_header}
    <h3>{$lng.lbl_newsletter}</h3>
  {/if}

  <div class="text-block">
    {$lng.lbl_newsletter_signup_text}
  </div>

  <ul class="register-newslist">
    {foreach from=$newslists item=n}
      {assign var="listid" value=$n.listid}
      <li class="news-register-item">
        <label>
          <input type="checkbox" name="subscription[{$n.listid}]"{if $subscription[$listid] ne ""} checked="checked"{/if} />
          {$n.name}
        </label>
        <div class="news-register-item-descr">{$n.descr}</div>
      </li>
    {/foreach}
  </ul>

{/if}
