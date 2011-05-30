{*
$Id: phones.tpl,v 1.1 2010/05/21 08:32:02 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<div class="phones">

  {if $config.Company.company_phone}
    <span class="first">{$lng.lbl_phone_1_title}: {$config.Company.company_phone}</span>
  {/if}

  {if $config.Company.company_phone_2}
    <span class="last">{$lng.lbl_phone_2_title}: {$config.Company.company_phone_2}</span>
  {/if}

</div>

