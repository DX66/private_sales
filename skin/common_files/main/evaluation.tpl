{*
$Id: evaluation.tpl,v 1.1 2010/05/21 08:32:17 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $shop_evaluation and $main eq "top_info"}
<div class="evaluation-notice">
{if $shop_evaluation eq "WRONG_DOMAIN"}
  {if $txt_reg_wrong_domain}
  {$txt_reg_wrong_domain}
  {else}
  {$lng.txt_reg_wrong_domain|substitute:"license_url":$license_url:"wrong_domain":$wrong_domain:"http_location":$http_location}
  {/if}
{else}
  {if $txt_reg_not_registered}
  {$txt_reg_not_registered}
  {else}
  {$lng.txt_reg_not_registered|substitute:"http_location":$http_location}
  {/if}
{/if}
</div>
{/if}
