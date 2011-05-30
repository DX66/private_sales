{*
$Id: partner_banners.tpl,v 1.3.2.3 2011/04/11 12:22:35 ferz Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_banners_management}

<script type="text/javascript" src="{$SkinDir}/js/popup_image_selection.js"></script>

{if $banner_type eq ''}
  {if $usertype eq 'B'}
    {$lng.txt_banners_note_partner}
  {else}
    {$lng.txt_banners_note}
  {/if}

{elseif $banner_type eq 'T' and $usertype ne 'B'}
  {$lng.txt_banners_text_link_note}

{elseif $banner_type eq 'G' and $usertype ne 'B'}
  {$lng.txt_banners_graphic_banner_note}

{elseif $banner_type eq 'M'}
  {$lng.txt_banners_media_rich_banner_note}

{elseif $banner_type eq 'P'}
  {if $get and not $productid and not $products}
    {$lng.txt_xaff_find_products_page_note}
  {elseif $get and $products}
    {$lng.txt_xaff_list_products_page_note}
  {elseif $productid}
    {$lng.txt_xaff_product_banner_page_note}
  {else}
    {$lng.txt_banners_product_link_note}
  {/if}

{elseif $banner_type eq 'C'}
  {if $get and not $categoryid}
    {$lng.txt_xaff_list_categories_page_note}
  {elseif $categoryid}
    {$lng.txt_xaff_category_banner_page_note}
  {else}
    {$lng.txt_banners_category_link_note}
  {/if}

{elseif $banner_type eq 'F'}
  {if $get and not $manufacturerid}
    {$lng.txt_xaff_list_manufactures_page_note}
  {elseif $get and $manufacturerid}
    {$lng.txt_xaff_manufacturer_banner_page_note}
  {else}
    {$lng.txt_banners_manufacturer_link_note}
  {/if}
{/if}

<br /><br />
{if $config.XAffiliate.display_as_iframe eq 'Y'}
  {assign var="local_type" value="iframe"}
{else}
  {assign var="local_type" value="js"}
{/if}

{if not $banner_type and not $banner and $usertype eq 'B'}
  <br />
  {$lng.txt_banner_html_code_register_note}<br />
  <strong>{$lng.lbl_link}:</strong><br />
  <textarea cols="100" rows="3" readonly="readonly">&lt;a href="{$catalogs.partner}/register.php?parent={$logged_userid}"&gt;{$lng.lbl_register}&lt;/a&gt;</textarea>
  <br /><br />
{/if}

{if $banners ne "" and $banner_type eq ''}

  {capture name=dialog}
    {foreach from=$banners item=v}

      <div class="xaff-banner-item{cycle values=", xaff-banner-item-odd"}">

        <strong>{$v.banner_type_text}:</strong>
        <a href="partner_banners.php?bannerid={$v.bannerid}&amp;get=1" class="link" target="_blank">{$v.banner}</a>
        {if $v.can_edit}
          &nbsp;&nbsp;&nbsp;
          <a href="partner_banners.php?bannerid={$v.bannerid}" class="modify" target="_blank">{$lng.lbl_modify}</a>
          &nbsp;&nbsp;&nbsp;
          <a href="partner_banners.php?bannerid={$v.bannerid}&amp;mode=delete" class="delete">{$lng.lbl_delete}</a>
        {/if}
        &nbsp;&nbsp;&nbsp;
        <a href="partner_banners.php?bannerid={$v.bannerid}&amp;get=1" target="_blank">{$lng.lbl_get_banner_html_code}</a>
        {if $v.userid gt 0 and $usertype ne 'B'}
        &nbsp;&nbsp;&nbsp;
        {$lng.lbl_created_by}&nbsp;<a href="{$catalogs.admin}/user_modify.php?user={$v.userid|escape:"url"}&amp;usertype=B">{$v.user} ({$lng.lbl_id} #{$v.userid})</a>
        {/if}
        <br />
        <table cellspacing="1" cellpadding="0" class="banner-box">
          <tr>
            <td class="banner-box">{include file="main/display_banner.tpl" assign="ban" banner=$v type=$local_type partner='' test_area=true}{$ban|amp}</td>
          </tr>
        </table>


      </div>

    {/foreach}

  {/capture}
  {include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_available_banners extra='width="100%"'}

{elseif $banner_type ne ''}

  {if $banner.banner_type ne ''}
    <br />

    {if $get}

      {if $banner.banner_type eq 'P' and not $productid}

        {include file="main/affiliate_search_result.tpl"}

      {elseif $banner.banner_type eq 'C'}

        {include file="main/affiliate_search_category.tpl"}

      {elseif $banner.banner_type eq 'F' and not $manufacturerid}

        {include file="main/affiliate_search_manufacturer.tpl"}

      {else}

        {capture name=dialog}

          {if $banner.banner_type eq 'P'}
            <div align="right">
              <a href="partner_banners.php?bannerid={$banner.bannerid}&amp;get=1">{$lng.lbl_search_again}</a>
              &nbsp;&nbsp;&nbsp;
              <a href="partner_banners.php?bannerid={$banner.bannerid}&amp;get=1&amp;mode=search&amp;page={$smarty.get.page}">{$lng.lbl_products_list}</a>
            </div>
          {/if}

          {if $banner.userid eq 0}
            {if $current_partner}
              {include file="main/banner_html_code.tpl" partner=$current_partner}
            {else}
              {include file="main/banner_html_code.tpl" partner='PARTNER'}
              <br />
              {$lng.txt_replace_with_real_login}
            {/if}  
          {else}
            {include file="main/banner_html_code.tpl" partner=$banner.userid}
            {if $usertype ne 'B'}
              <br />
              {$lng.lbl_created_by}&nbsp;<a href="{$catalogs.admin}/user_modify.php?user={$banner.userid}&amp;usertype=B">{$banner.user} ({$lng.lbl_id} #{$banner.userid})</a>
            {/if}
          {/if}

        {/capture}
        {include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_banner_html_code extra='width="100%"'}

      {/if}

    {else}

      {capture name=dialog}

        <table cellspacing="1" cellpadding="0" bgcolor="#000000">
          <tr bgcolor="#ffffff">
            <td>{include file="main/display_banner.tpl" assign="ban" type=$local_type partner='' test_area=true}{$ban|trim|amp}</td>
          </tr>
        </table>

      {/capture}
      {include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_preview|cat:' '|cat:$banner.banner_type_text extra='width="100%"'}

    {/if}

  {/if}

  {if not $get and (not $banner.banner_type or $banner.can_edit)}

  <br />

  <form action="{$current_location}/banner.php?type=preview&amp;test={$smarty.now}" method="post" name="previewform" target="previewwin">
    <input type="hidden" name="preview"  value="" />
  </form>

  {capture name=dialog}

    <script type="text/javascript">
    //<![CDATA[
    var requiredFields = [
      ['banner', "{$lng.lbl_banner_name|strip_tags|wm_remove|escape:javascript}", false], 
      {if $banner_type eq 'T' },['banner_body', "{$lng.lbl_text|strip_tags|wm_remove|escape:javascript}", false]{/if}
      {if $banner_type eq 'M' },['banner_body', "{$lng.lbl_body|strip_tags|wm_remove|escape:javascript}", false]{/if}

    ]
    //]]>
    </script>

    {include file="check_required_fields_js.tpl"}

    <form action="partner_banners.php" method="post" enctype="multipart/form-data" name="edit_banner" onsubmit="javascript: return checkRequired(requiredFields)">
      <input type="hidden" name="mode" value="add" />
      <input type="hidden" name="mode2" value="" />
      <input type="hidden" name="banner_type" value="{$banner_type}" />
      <input type="hidden" name="bannerid" value="{$banner.bannerid}" />

      <table cellpadding="0" cellspacing="3" width="100%">
        <tr{cycle values=", class='TableSubHead'"}>
          <td><label for="banner">{$lng.lbl_banner_name}:</label></td>
          <td><input type="text" maxlength="128" size="40" id="banner" name="add[banner]" value="{$banner.banner|escape}" />{if $err_field eq 'banner'}<font class="Star">&lt;&lt;</font>{/if}</td>
        </tr>

        <tr{cycle values=", class='TableSubHead'"}>
          <td><label>{$lng.lbl_banner_size}:</label></td>
          <td>
            <input type="text" size="5" id="banner_x" value="{$banner.banner_x|default:$config.XAffiliate.xaff_def_banner_x}" name="add[banner_x]" />
            &nbsp;x&nbsp;
            <input type="text" size="5" id="banner_y" value="{$banner.banner_y|default:$config.XAffiliate.xaff_def_banner_y}" name="add[banner_y]" />{if $err_field eq 'banner_xy'}<font class="Star">&lt;&lt;</font>{/if}
          </td>
        </tr>

        <tr{cycle values=", class='TableSubHead'"}>
          <td><label for="avail">{$lng.lbl_availability}:</label></td>
          <td><input type="checkbox" value="Y" id="avail" name="add[avail]"{if $banner.avail eq 'Y' or ($banner.bannerid eq '' and $err_field eq '')} checked="checked"{/if} /></td>
        </tr>

        <tr{cycle values=", class='TableSubHead'"}>
          <td><label for="open_blank">{$lng.lbl_open_in_new_window}:</label></td>
          <td><input type="checkbox" value="Y" id="open_blank" name="add[open_blank]"{if $banner.open_blank eq 'Y' or ($banner.bannerid eq '' and $err_field eq '')} checked="checked"{/if} /></td>
        </tr>

        {if $banner_type eq 'T'}

          <tr{cycle values=", class='TableSubHead'"}>
            <td><label for="banner_body">{$lng.lbl_text}:</label></td>
            <td><textarea cols="50" rows="3" name="add[body]" id="banner_body">{$banner.body}</textarea>{if $err_field eq 'body'}<font class="Star">&lt;&lt;</font>{/if}</td>
          </tr>

        {elseif $banner_type eq 'G'}

          <tr{cycle values=", class='TableSubHead'"}>
            <td><label for="legend">{$lng.lbl_text}</label> ({$lng.lbl_optional}):</td>
            <td><textarea cols="50" rows="3" id="legend" name="add[legend]">{$banner.legend}</textarea></td>
          </tr> 

          <tr{cycle values=", class='TableSubHead'"}>
            <td><label for="alt">{$lng.lbl_alt_tag}</label> ({$lng.lbl_optional}):</td> 
            <td><textarea cols="50" rows="3" id="alt" name="add[alt]">{$banner.alt}</textarea></td>
          </tr>  

          <tr{cycle values=", class='TableSubHead'"}>
            <td><label for="direction">{$lng.lbl_text_location}:</label></td>
            <td>
              <select id="direction" name="add[direction]">
                <option value="U"{if $banner.direction eq 'U' or $banner.direction eq ''} selected="selected"{/if}>{$lng.lbl_above}</option>
                <option value="L"{if $banner.direction eq 'L'} selected="selected"{/if}>{$lng.lbl_left}</option>
                <option value="R"{if $banner.direction eq 'R'} selected="selected"{/if}>{$lng.lbl_right}</option>
                <option value="D"{if $banner.direction eq 'D'} selected="selected"{/if}>{$lng.lbl_below}</option>
              </select>
            </td>
          </tr>  

          <tr{cycle values=", class='TableSubHead'"}>
            <td>{$lng.lbl_image}:</td> 
            <td>
              {include file="main/edit_image.tpl" type="B" id=$banner.bannerid button_name=$lng.lbl_save_banner}
            </td>
          </tr>  

        {elseif $banner_type eq 'P' or $banner_type eq 'C' or $banner_type eq 'F'}

          <tr{cycle values=", class='TableSubHead'"}>
            <td><label for="is_image">{$lng.lbl_display_product_image|substitute:"element":$elements_names[$banner_type]}:</label></td>
            <td><input type="checkbox" id="is_image" name="add[is_image]" value='Y'{if $banner.is_image eq 'Y' or ($banner.bannerid eq '' and $err_field eq '')} checked="checked"{/if} />{if $err_field eq 'banner_attributes'}<font class="Star">&lt;&lt;</font>{/if}</td>
          </tr>

          <tr{cycle values=", class='TableSubHead'"}>
            <td><label for="is_name">{$lng.lbl_display_product_title|substitute:"element":$elements_names[$banner_type]}:</label></td>
            <td><input type="checkbox" name="add[is_name]" id="is_name" value='Y'{if $banner.is_name eq 'Y'} checked="checked"{/if} />{if $err_field eq 'banner_attributes'}<font class="Star">&lt;&lt;</font>{/if}</td>
          </tr>  

          <tr{cycle values=", class='TableSubHead'"}> 
            <td><label for="is_descr">{$lng.lbl_display_product_description|substitute:"element":$elements_names[$banner_type]}:</label></td>
            <td><input type="checkbox" id="is_descr" name="add[is_descr]" value='Y'{if $banner.is_descr eq 'Y'} checked="checked"{/if} />{if $err_field eq 'banner_attributes'}<font class="Star">&lt;&lt;</font>{/if}</td>
          </tr>

          {if $banner_type eq 'P'}

            <tr{cycle values=", class='TableSubHead'"}>
              <td><label for="is_add">{$lng.lbl_add_to_cart_link}:</label></td>
              <td><input type="checkbox" id="is_add" name="add[is_add]" value='Y'{if $banner.is_add eq 'Y'} checked="checked"{/if} />{if $err_field eq 'banner_attributes'}<font class="Star">&lt;&lt;</font>{/if}</td>
            </tr>

          {/if}

        {elseif $banner_type eq 'M'}

          <tr{cycle values=", class='TableSubHead'"}> 
            <td>

<script type="text/javascript">
//<![CDATA[
{literal}
function preview_body() {
  var ta = document.getElementById('banner_body');
  var f = document.forms.previewform;
  if (ta && f) {
    window.open('', 'previewwin', 'width=600,height=460,toolbar=no,status=no,scrollbars=yes,resizable=yes,menubar=no,location=no,direction=no');
    f.preview.value = ta.value;
    f.submit();
  }
}
{/literal}
//]]>
</script>

              <label for="banner_body">{$lng.lbl_body}:</label>
            </td>
            <td>
              <textarea cols="60" rows="10" name="add[body]" id="banner_body">{$banner.body}</textarea>{if $err_field eq 'body'}<font class="Star">&lt;&lt;</font>{/if}<br />

              <a href="javascript:void(0);" class="a-open-tag" onclick="javascript: insert2TA(document.getElementById('banner_body'), '&lt;#A#&gt;');">{$lng.lbl_add_link_opening_tag}</a>
              <a href="javascript:void(0);" class="a-close-tag" onclick="javascript: insert2TA(document.getElementById('banner_body'), '&lt;#/A#&gt;');">{$lng.lbl_add_link_closing_tag}</a>
              <a href="javascript:void(0);" class="preview-banner" onclick="javascript: preview_body();">{$lng.lbl_preview}</a>
            </td>
          </tr>

          {if $has_elements}
            <tr{cycle values=", class='TableSubHead'"}>
              <td>&nbsp;</td>
              <td>
                <br />  
                <strong>{$lng.lbl_media_library}:</strong>
                <br />
                {if $usertype ne 'B'}
                  <iframe width="100%" height="300" src="{$catalogs.admin}/partner_element_list.php"></iframe>
                {else}
                  <iframe width="100%" height="300" src="{$catalogs.partner}/partner_element_list.php"></iframe>
                {/if}
              </td>
            </tr>
          {/if}

        {/if}

        <tr>
          <td>&nbsp;</td>
          <td class="SubmitBox"><input type="submit" value="{$lng.lbl_save_banner|strip_tags:false|escape}" />
          {if $banner.bannerid ne ''}
          {if $banner_type eq 'P' or $banner_type eq 'C' or $banner_type eq 'F'}
          <input type="button" value="{$lng.lbl_save_banner_choose_product|strip_tags:false|escape|substitute:"element":$elements_names[$banner_type]}" onclick="javascript: document.edit_banner.mode2.value='choose_elem';document.edit_banner.submit();" />
          {else}
          <input type="button" value="{$lng.lbl_save_banner_get_html_code|strip_tags:false|escape}" onclick="javascript: document.edit_banner.mode2.value='choose_elem';document.edit_banner.submit();" />
          {/if}
          {/if}</td>
        </tr>

      </table>
    </form>

    {if $banner_type eq 'M'}
      <br />

      {include file="main/subheader.tpl" title=$lng.lbl_add_media_object}

      <form action="partner_banners.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="mode" value="upload" />
        <input type="hidden" name="banner_type" value="{$banner_type}" />
        <input type="hidden" name="bannerid" value="{$banner.bannerid}" />

        <table cellpadding="0" cellspacing="3">

          <tr>
            <td>{$lng.lbl_media_object}:</td>
            <td>
              {include file="main/edit_image.tpl" type="L" id=0 button_name=$lng.lbl_add}
            </td>
          </tr>

          <tr> 
            <td colspan="2">
              <br />
              <strong>{$lng.txt_flash_note}</strong>
            </td>
          </tr>

          <tr> 
            <td>{$lng.lbl_image_size}:</td>
            <td>
              <input type="text" size="5" name="width" />
              &nbsp;x&nbsp;
              <input type="text" size="5" name="height" />
            </td>
          </tr>

          <tr>
            <td>&nbsp;</td>
            <td><input type="submit" value="{$lng.lbl_add|strip_tags:false|escape}" /></td>
          </tr>

        </table>
      </form>

    {/if}
  {/capture}

  {if $banner.banner_type ne ''}
    {assign var="title" value=$lng.lbl_modify_banner}
  {else}
    {assign var="title" value=$lng.lbl_add_banner}
  {/if}

  {include file="dialog.tpl" content=$smarty.capture.dialog title=$title extra='width="100%"'}

  {/if}

{/if}
