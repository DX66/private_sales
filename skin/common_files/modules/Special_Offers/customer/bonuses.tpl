{*
$Id: bonuses.tpl,v 1.1 2010/05/21 08:32:48 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

{if $mode eq "points"}
  {assign var="dialog_title" value=$lng.lbl_sp_convert_points}
{else}
  {assign var="dialog_title" value=$lng.lbl_sp_my_bonuses}
{/if}

<h1>{$dialog_title}</h1>

<p class="text-block">
  {if $active_modules.Gift_Certificates}
    {$lng.txt_sp_my_bonuses_desc}
  {else}
    {$lng.txt_sp_my_bonuses_desc_wo_gs}
  {/if}
</p>

{capture name="dialog"}

  {if $bonus eq "" or ($bonus.points le "0" and $bonus.memberships eq "")}
    <p class="text-block text-pre-block">{$lng.lbl_sp_no_bonuses_earned}</p>

    <div class="right-box">
      {include file="customer/buttons/button.tpl" button_title=$lng.lbl_sp_learn_more href="offers.php" style="link"}
    </div>

  {elseif $mode eq "points"}

    {include file="modules/Special_Offers/customer/bonuses_points2gc.tpl"}

  {else}

    {include file="customer/buttons/button.tpl" button_title=$lng.lbl_sp_learn_more href="offers.php" style="link"}

    {include file="modules/Special_Offers/customer/bonuses_view.tpl"}

  {/if}

{/capture}
{include file="customer/dialog.tpl" title=$dialog_title content=$smarty.capture.dialog additional_class="offers-bonuses-page" noborder=true}
