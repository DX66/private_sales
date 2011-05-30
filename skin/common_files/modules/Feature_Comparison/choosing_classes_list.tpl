{*
$Id: choosing_classes_list.tpl,v 1.1 2010/05/21 08:32:21 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

<h1>{$lng.lbl_what_are_you_looking_for}</h1>

<p class="text-block">{$lng.txt_choosing_by_features_note_step1}</p>

{capture name=dialog}

  {foreach from=$classes item=v key=k name=classes}

    <div style="width: {$percent}%;" class="fcomp-class-cell">

      {if $v.is_image}
        <a href="choosing.php?fclassid={$v.fclassid}" class="image">{include file="modules/Feature_Comparison/feature_image.tpl" fclassid=$v.fclassid class=$v.class image_x=$config.Feature_Comparison.feature_image_width tmbn_url=$v.image_url image_x=$v.image_x image_y=$v.image_y}</a><br />
      {/if}
      <a href="choosing.php?fclassid={$v.fclassid}">{$v.class}</a>

    </div>

    {if $smarty.foreach.classes.iteration % $rate eq 0}
      <div class="clearing"></div>
    {/if}

  {/foreach}

  <div class="clearing"></div>

{/capture}
{include file="customer/dialog.tpl" title=$lng.lbl_what_are_you_looking_for content=$smarty.capture.dialog noborder=true} 
