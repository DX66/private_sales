{*
$Id: tools.tpl,v 1.6.2.1 2010/11/30 14:39:45 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_maintenance}

{$lng.txt_tools_top_text}

<br /><br />

<script type="text/javascript">
//<![CDATA[

var lbl_remove_test_data_confirm = "{$lng.lbl_remove_test_data_confirm|wm_remove|escape:javascript}";
var lbl_remove_test_data_alert = "{$lng.lbl_remove_test_data_alert|wm_remove|escape:javascript}";
var txt_cc_info_removal_warning = "{$lng.txt_cc_info_removal_warning|wm_remove|escape:javascript}";
var txt_regen_blowfish_key_confirm = "{$lng.txt_regen_blowfish_key_confirm|wm_remove|escape:javascript}";

{literal}
function clickMore(id) {
  if (!document.getElementById(id) || !document.getElementById(id+'_note'))
    return false;
  var disp = (document.getElementById(id).style.display == 'none');
  document.getElementById(id).style.display = disp ? "" : "none";
  document.getElementById(id+'_note').style.display = !disp ? "" : "none";
}

function changeRSD(sObj) {
  var obj = document.getElementById('tr_select_date');
  if (!obj)
    return false;

  obj.style.display = (sObj.options[sObj.selectedIndex].value == 's') ? '' : 'none';
}

function check_cc_assurance() {
  var objs = [];
  var is_checked = false;

  objs = document.formmode_clear_db.getElementsByTagName("input");

  if (objs) {
    for (var x = 1; x < objs.length; x++) {
      if (objs[x].type == 'checkbox' && objs[x].checked) {
        is_checked = true;
      }
    }
  }

  if (!is_checked) {
    alert(lbl_remove_test_data_alert);
    return false;
  }

  return confirm(lbl_remove_test_data_confirm);
}

{/literal}
//]]>
</script>

{capture name=dialog}

{if $config.SEO.clean_urls_enabled eq "Y"}

{*** Clean URLs generation section ***}

<a name="generate_clean_urls"></a>

{include file="main/subheader.tpl" title=$lng.lbl_generate_clean_urls}

<form action="tools.php" method="post" name="processform1">
<input type="hidden" name="generate_clean_urls" value="Y" />

<table cellpadding="2" cellspacing="0">

<tr>
  <td colspan="2">{$lng.txt_generating_clean_urls_note}<br /><br /></td>
</tr>

<tr>
  <td width="20"><input type="checkbox" id="generate_clean_urls_for_product" name="generate_clean_urls_for[]" value="P" /></td>
  <td><label for="generate_clean_urls_for_product">{$lng.lbl_generate_clean_urls_for|substitute:"resource_type":$lng.lbl_products}</label></td>
</tr>

<tr>
  <td width="20"><input type="checkbox" id="generate_clean_urls_for_categories" name="generate_clean_urls_for[]" value="C" /></td>
  <td><label for="generate_clean_urls_for_categories">{$lng.lbl_generate_clean_urls_for|substitute:"resource_type":$lng.lbl_categories}</label></td>
</tr>

<tr>
  <td width="20"><input type="checkbox" id="generate_clean_urls_for_manufacturers" name="generate_clean_urls_for[]" value="M" /></td>
  <td><label for="generate_clean_urls_for_manufacturers">{$lng.lbl_generate_clean_urls_for|substitute:"resource_type":$lng.lbl_manufacturers}</label></td>
</tr>

<tr>
  <td width="20"><input type="checkbox" id="generate_clean_urls_for_static_pages" name="generate_clean_urls_for[]" value="S" /></td>
  <td><label for="generate_clean_urls_for_static_pages">{$lng.lbl_generate_clean_urls_for|substitute:"resource_type":$lng.lbl_static_pages}</label></td>
</tr>

<tr>
  <td colspan="2"><input type="submit" value="{$lng.lbl_submit|strip_tags:false|escape}"/>
  </td>
</tr>

</table>
</form>

<br /><br /><br />

{/if}

{*** CLEARING CC INFO SECTION ***}

<a name="clearcc"></a>

{include file="main/subheader.tpl" title=$lng.txt_credit_card_information_removal}

<form action="tools.php" method="post" name="processform2">
<table cellpadding="2" cellspacing="0">

{if $is_subscription}
<tr>
  <td colspan="2">
<table cellspacing="0" cellpadding="2">
<tr>
  <td><img src="{$ImagesDir}/icon_warning_small.gif" alt="" /></td>
  <td>{$lng.txt_remove_cc_data_subscription_note}</td>
</tr>
</table>
  </td>
</tr>
<tr>
  <td colspan="2">&nbsp;</td>
</tr>
{/if}

<tr>
  <td width="20"><input type="checkbox" id="remove_ccinfo_profiles" name="remove_ccinfo_profiles" value="Y" /></td>
  <td><label for="remove_ccinfo_profiles">{$lng.lbl_remove_from_customer_profiles}</label></td>
</tr>

<tr>
  <td><input type="checkbox" id="remove_ccinfo_orders" name="remove_ccinfo_orders" value="Y" /></td>
  <td><label for="remove_ccinfo_orders">{$lng.lbl_remove_from_completed_orders}</label></td>
</tr>

<tr>
    <td><input type="checkbox" id="remove_ccinfo_orders_all" name="remove_ccinfo_orders_all" value="Y" /></td>
    <td><label for="remove_ccinfo_orders_all">{$lng.lbl_remove_from_all_orders}</label></td>
</tr>

<tr>
  <td><input type="checkbox" id="save_4_numbers" name="save_4_numbers" value="Y" /></td>
  <td><label for="save_4_numbers">{$lng.lbl_save_last_4_digits_cc_number}</label></td>
</tr>

<tr>
  <td colspan="2">
    <input type="submit" name="mode_clear" value="{$lng.lbl_apply|strip_tags:false|escape}" onclick='javascript: return confirm(txt_cc_info_removal_warning);' />
    <br />
    <br />
    {$lng.txt_cc_info_removal_note}<br />
    <br />
    <b>{$lng.lbl_note}:</b> {$lng.txt_remove_ccdata_from_orders_note}
  </td>
</tr>

</table>
</form>

<br /><br /><br />

{*** EMAIL AS LOGIN SWITCHER ***}

<a name="authmodelnk"></a>

{include file="main/subheader.tpl" title=$lng.lbl_change_authmode}

{if $config.email_as_login ne 'Y'}
  {$lng.lbl_current_auth_mode|substitute:"mode":$lng.lbl_username}<br /><br />
  {$lng.txt_change_auth_email_as_login}
{else}
  {$lng.lbl_current_auth_mode|substitute:"mode":$lng.lbl_email}<br /><br />
  {$lng.txt_change_auth_username_as_login}
{/if}

<br />
{if $nonuniq_accounts}
  <br />
  <font class="Star">{$lng.txt_nonuniq_accounts_warn}</font>
  <br />
  {foreach from=$nonuniq_accounts key=email item=users}
  <ul class="accounts-group-cell">
    <li>{include file="main/visiblebox_link.tpl" no_use_class="Y" mark=$email title=$email extra=' width="100%"'}</li>
      <ul class="accounts-list" style="display: none" id="box{$email}">
      {foreach from=$users item=u}
        <li{if $u.usertype ne "C"} class="req"{/if}><a href="user_modify.php?user={$u.id}&usertype={$u.usertype}">{$u.firstname} {$u.lastname} ({$u.login})</a> <i>{$usertypes[$u.usertype]}</i></li>
      {/foreach}
      </ul>
    </li>
  </ul>
  {/foreach}

{else}

  <form action="tools.php" method="post" name="authmode" onsubmit="return confirm('{$lng.lbl_change_authmode_confirm_js|escape:"javascript"|wm_remove}');">
  <table cellpadding="2" cellspacing="0" width="100%">
  <tr>
    <td class="SubmitBox">
    <input type="submit" name="mode_change_authmode" value="{$lng.lbl_change|strip_tags:false|escape}"/>
    </td>
  </tr>
  </table>
  </form>
  <br />
{/if}

<br /><br />

{*** OPTIMIZE TABLE ***}

<a name="optimdb"></a>

{include file="main/subheader.tpl" title=$lng.lbl_optimize_tables}

<form action="tools.php" method="post" name="formmode_optimize">

<table cellpadding="2" cellspacing="0" width="100%">

<tr id="optimize_tables_small">
  <td>{$lng.txt_optimize_tables_small_note}&nbsp;<a href="javascript:void(0);" onclick="javascript: clickMore('optimize_tables_small');">{$lng.lbl_more}</a></td>
</tr>
<tr id="optimize_tables_small_note" style="display: none;">
  <td>{$lng.txt_optimize_tables_note}</td>
</tr>
<tr>
  <td class="SubmitBox"><input type="submit" name="mode_optimize" value="{$lng.lbl_optimize_tables|strip_tags:false|escape}" /></td>
</tr>

</table>
</form>

<br /><br /><br />

{*** Check database integrity ***}

<a name="integrdb"></a>

{include file="main/subheader.tpl" title=$lng.lbl_check_database_integrity}

<form action="tools.php" method="post" name="formmode_check_integrity">
<table cellpadding="2" cellspacing="0" width="100%">

<tr id="check_database_integrity_small">
  <td>{$lng.txt_check_database_integrity_small_note}&nbsp;<a href="javascript:void(0);" onclick="javascript: clickMore('check_database_integrity_small');">{$lng.lbl_more}</a></td>
</tr>
<tr id="check_database_integrity_small_note" style="display: none;">
  <td>{$lng.txt_check_database_integrity_note}</td>
</tr>
<tr>
  <td class="SubmitBox"><input type="submit" name="mode_check_integrity" value="{$lng.lbl_check_database_integrity|strip_tags:false|escape}" /></td>
</tr>

{if $err_store}
<tr>
  <td><br />
{$lng.lbl_unrelated_data_found}
<br />

<table width="100%" cellspacing="1" cellpadding="2">
{foreach from=$err_store item=keys key=tbl}

{if $keys eq 'no table'}

<tr>
  <td colspan="2"><strong>{$lng.lbl_table_x_not_found|substitute:"table":$tbl}</strong></td>
</tr>

{else}

{foreach from=$keys item=rows key=tbl2}
<tr>
  <td colspan="2">{include file="main/visiblebox_link.tpl" mark=$tbl|cat:$tbl2 title=$tbl|cat:" -> "|cat:$tbl2}</td>
</tr>
<tr id="box{$tbl|cat:$tbl2}" style="display: none;">
  <td width="11"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
  <td>
{$lng.lbl_relationships_missing}:
  <table cellspacing="1" cellpadding="2" width="100%">
  <tr class="TableHead">
    <td width="50%">{$lng.lbl_records_in_table|substitute:"table":$tbl}</td>
    <td>{$lng.lbl_fields_in_table|substitute:"table":$tbl2}</td>
  </tr>
{foreach from=$rows item=row}
  <tr{cycle name=$tbl|cat:$tbl2 values=', class="TableSubHead"'}>
    <td>
{foreach from=$row.row item=v key=k}
{$k}: {$v}<br />
{/foreach}
    </td>
    <td valign="top">
{foreach from=$row.keys item=v key=k}
{$k}: {$v}<br />
{/foreach}
    </td>
  </tr>
{/foreach}
  </table></td>
</tr>
{/foreach}

{/if}

{/foreach}
</table>

</td>
</tr>
{/if}

</table>
</form>

<br /><br /><br />

{*** Force cache generation ***}

<a name="gencache"></a>

{include file="main/subheader.tpl" title=$lng.lbl_force_cache_generation}

<form action="tools.php" method="post" name="formmode_clear_cache">
<table cellpadding="2" cellspacing="0" width="100%">

<tr id="force_cache_generation_small">
  <td>{$lng.txt_force_cache_generation_small_note}&nbsp;<a href="javascript:void(0);" onclick="javascript: clickMore('force_cache_generation_small');">{$lng.lbl_more}</a></td>
</tr>
<tr id="force_cache_generation_small_note" style="display: none;">
  <td>{$lng.txt_force_cache_generation_note}</td>
</tr>
<tr>
  <td class="SubmitBox"><input type="submit" name="mode_clear_cache" value="{$lng.lbl_force_cache_generation|strip_tags:false|escape}" /></td>
</tr>

</table>
</form>

<br /><br /><br />

{*** Force categories tree re-indexing ***}

<a name="catindex"></a>

{include file="main/subheader.tpl" title=$lng.lbl_rebuild_category_indexes}

<form action="tools.php" method="post" name="formmode_rebuild_catindex">
<table cellpadding="2" cellspacing="0" width="100%">

<tr>
  <td>{$lng.txt_rebuild_category_indexes_note}</td>
</tr>
<tr>
  <td class="SubmitBox"><input type="submit" name="mode_rebuild_catindex" value="{$lng.lbl_rebuild_category_indexes|strip_tags:false|escape}" /></td>
</tr>

</table>
</form>

<br /><br /><br />



{*** CLEARING SECTION ***}

<a name="clearstat"></a>

{include file="main/subheader.tpl" title=$lng.lbl_statistics_clearing}

<form action="tools.php" method="post" name="processform3" onsubmit="javascript: return checkMarks(this, new RegExp('[a-z]+_stat', 'gi'));">
<table cellpadding="2" cellspacing="0" width="100%">

<tr id="clearing_section_small">
  <td>{$lng.txt_clearing_section_small_note}&nbsp;<a href="javascript:void(0);" onclick="javascript: clickMore('clearing_section_small');">{$lng.lbl_more}</a></td>
</tr>
<tr id="clearing_section_small_note" style="display: none;">
  <td>{$lng.txt_clearing_section_note}</td>
</tr>

<tr>
<td>
<table cellpadding="2" cellspacing="0">

<tr>
  <td><input type="checkbox" id="track_stat" name="track_stat" value="Y" /></td>
  <td><label for="track_stat">{$lng.lbl_clear_all_tracking_statistics}</label></td>
</tr>
<tr>
  <td><input type="checkbox" id="shop_stat" name="shop_stat" value="Y" /></td>
  <td><label for="shop_stat">{$lng.lbl_clear_all_store_statistics}</label></td>
</tr>
<tr>
  <td><input type="checkbox" id="referer_stat" name="referer_stat" value="Y" /></td>
  <td><label for="referer_stat">{$lng.lbl_clear_all_referrals_statistics}</label></td>
</tr>
<tr>
  <td><input type="checkbox" id="adaptive_stat" name="adaptive_stat" value="Y" /></td>
  <td><label for="adaptive_stat">{$lng.lbl_clear_all_visitors_environment_statistics}</label></td>
</tr>
<tr>
  <td><input type="checkbox" id="search_stat" name="search_stat" value="Y" /></td>
  <td><label for="search_stat">{$lng.lbl_clear_all_search_statistics}</label></td>
</tr>
<tr>
  <td><input type="checkbox" id="bench_stat" name="bench_stat" value="Y" /></td>
  <td><label for="bench_stat">{$lng.lbl_clear_all_bench_statistics}</label></td>
</tr>
{if $active_modules.XAffiliate}
<tr>
  <td><input type="checkbox" id="xaff_stat" name="xaff_stat" value="Y" /></td>
  <td><label for="xaff_stat">{$lng.lbl_clear_all_aff_statistics}</label></td>
</tr>
{/if}

<tr>
  <td colspan="2">
  <table cellspacing="1" cellpadding="2">
  <tr>
    <td>{$lng.lbl_remove_stats_date_note}</td>
    <td>
    <select name="rsd_date" onchange="javascript: changeRSD(this);">
      <option value="">{$lng.lbl_remove_stats_date_none}</option>
      <option value="s">{$lng.lbl_remove_stats_date_select}</option>
    </select>
    </td>
  </tr>
  <tr id="tr_select_date" style="display: none;">
    <td>&nbsp;</td>
    <td>{html_select_date prefix="RSD_" start_year=$rsd_start_year}</td>
  </tr>
  </table>
  </td>
</tr>

</table>
</td>
</tr>

<tr>
  <td><input type="submit" name="mode_clear" value="{$lng.lbl_apply|strip_tags:false|escape}" /><br /><br />
  {$lng.txt_clearing_statistics_note}
  </td>
</tr>

</table>
</form>

<br /><br /><br />

{*** CLEARING PRECOMPILED TEMPLATES SECTION ***}

<a name="cleartmp"></a>

{include file="main/subheader.tpl" title=$lng.lbl_clear_templates_cache}

<table cellpadding="2" cellspacing="0" width="100%">

<tr>
  <td>
  <input type="button" value="{$lng.lbl_clear|strip_tags:false|escape}" onclick="javascript: self.location='tools.php?mode=templates'" /><br /><br />
{if $templates_cache}  
  {if $templates_cache.is_large}{assign var="cache_cnt_prefix" value=$lng.lbl_more_than}{/if}
  {$lng.txt_clear_templates_cache_text|substitute:"dir":$templates_cache.dir:"files":$templates_cache.files:"size":$templates_cache.size:"more_than":$cache_cnt_prefix}
{else}
  <a href="tools.php?estimate_dir_size=Y#cleartmp">{$lng.lbl_estimate_dir_size}</a>
{/if}
  </td>
</tr>

</table>

<br /><br /><br />

{*** CLEARING TMP DIRECTORY SECTION ***}

<a name="cleartmpdir"></a>

{include file="main/subheader.tpl" title=$lng.lbl_clear_tmp_dir}

<table cellpadding="2" cellspacing="0" width="100%">

<tr>
  <td>
  <input type="button" value="{$lng.lbl_clear|strip_tags:false|escape}" onclick="javascript: self.location='tools.php?mode=tmpdir'" /><br /><br />
{if $tmp_dir}
  {if $tmp_dir.is_large}{assign var="temp_dir_cnt_prefix" value=$lng.lbl_more_than}{/if}
  {$lng.txt_clear_tmp_dir_text|substitute:"dir":$tmp_dir.dir:"files":$tmp_dir.files:"size":$tmp_dir.size:"more_than":$temp_dir_cnt_prefix}
{else}
  <a href="tools.php?estimate_dir_size=Y#cleartmpdir">{$lng.lbl_estimate_dir_size}</a>
{/if}
  </td>
</tr>

</table>

<br /><br /><br />

{if $regenerate_dpicons_allowed}

{*** Regenerate image cache ***}

<a name="regendpicons"></a>

{include file="main/subheader.tpl" title=$lng.lbl_image_cache_regenerate}

{$lng.txt_regenerate_dpicons}<br />
<br />
<form action="tools.php?regenerate_dpicons=Y" method="post">
  <input type="submit" value="{$lng.lbl_regenerate|strip_tags:false|escape}" />
</form>

<br /><br /><br />

{/if}

{if $generate_thumbnails_allowed}

<a name="generatethumbnails"></a>

{include file="main/subheader.tpl" title=$lng.lbl_generate_thumbnails}

{$lng.txt_generate_thumbnails}<br />
<br />
<form action="tools.php?generate_thumbnails=Y" method="post">
  <input type="submit" value="{$lng.lbl_generate|strip_tags:false|escape}" />
</form>

<br /><br /><br />

{/if}

{if $reslice_zimages_allowed}

{*** Reslice z-images ***}

<a name="reslicezimages"></a>

{include file="main/subheader.tpl" title=$lng.lbl_reslice_zimages}

{$lng.txt_reslice_zimages}<br />
<br />
<form action="tools.php?reslice_zimages=Y" method="post">
  <input type="submit" value="{$lng.lbl_reslice_zimages|strip_tags:false|escape}" />
</form>

<br /><br /><br />

{/if}

{if $active_modules.Magnifier}

{*** Reslice all ***}

<a name="resliceall"></a>

{include file="main/subheader.tpl" title=$lng.lbl_reslice_all}

{$lng.txt_reslice_all}<br />
<br />
<form action="tools.php?reslice_all=Y" method="post">
  <input type="submit" value="{$lng.lbl_reslice_all|strip_tags:false|escape}" />
</form>

<br /><br /><br />

{/if}

{*** Regenerating blowfish key ***}

<a name="regenbk"></a>

{include file="main/subheader.tpl" title=$lng.lbl_regenerating_blowfish_key}

<form action="tools.php" method="post" name="formmode_regen_bk" onsubmit="javascript: return confirm(txt_regen_blowfish_key_confirm);">
<table cellpadding="2" cellspacing="0" width="100%">

<tr id="regen_bk_small">
  <td colspan="2">{$lng.txt_regen_blowfish_key_small_note}&nbsp;<a href="javascript:void(0);" onclick="javascript: clickMore('regen_bk_small');">{$lng.lbl_more}</a></td>
</tr>
<tr id="regen_bk_small_note" style="display: none;">
  <td colspan="2">{$lng.txt_regen_blowfish_key_note}</td>
</tr>
{if $config_non_writable}
<tr>
  <td><img src="{$ImagesDir}/icon_warning_small.gif" alt="" />&nbsp;{$lng.txt_regen_blowfish_key_alert}</td>
</tr>
{/if}
<tr>
  <td colspan="2" class="SubmitBox"><input type="submit" name="regenerate_blowfish" value="{$lng.lbl_regenerate|strip_tags:false|escape}" />
<br />
<br />
{$lng.txt_regen_blowfish_key_warning}
  </td>
</tr>

</table>
</form>

<br /><br /><br />

{*** Remove test/demo data ***}

<a name="cleardb"></a>

{include file="main/subheader.tpl" title=$lng.lbl_remove_test_data}

<form action="tools.php" method="post" name="formmode_clear_db" onsubmit="javascript: return check_cc_assurance();">
<table cellpadding="2" cellspacing="0" width="100%">

<tr id="clear_db_small">
	<td>{$lng.txt_remove_test_data_small_note}&nbsp;<a href="javascript:void(0);" onclick="javascript: clickMore('clear_db_small');">{$lng.lbl_more}</a></td>
</tr>
<tr id="clear_db_small_note" style="display: none;">
	<td>{$lng.txt_remove_test_data_note}</td>
</tr>
<tr>
  <td style="font-weight: bold; padding-top: 10px;">{$lng.lbl_select_data_to_remove}:</td>
</tr>
<tr>
<td style="padding-left: 20px;">
{include file="main/check_all_row.tpl" form="formmode_clear_db" prefix="clear_db"}
<table cellpadding="2" cellspacing="0">
<tr>
  <td><input type="checkbox" id="products_remove" name="clear_db[products]" value="Y" /></td>
  <td><label for="products_remove">{$lng.lbl_products}</label></td>
</tr>
<tr>
  <td><input type="checkbox" id="prod_cat_remove" name="clear_db[prod_cat]" value="Y" /></td>
  <td><label for="prod_cat_remove">{$lng.lbl_categories_and_products}</label></td>         
</tr>
<tr>
  <td><input type="checkbox" id="orders_remove" name="clear_db[orders]" value="Y" /></td>
  <td><label for="orders_remove">{$lng.lbl_orders}</label></td>         
</tr>
<tr>
  <td><input type="checkbox" id="stat_pages_remove" name="clear_db[stat_pages]" value="Y" /></td> 
  <td><label for="stat_pages_remove">{$lng.lbl_static_pages}</label></td>         
</tr>
<tr>
  <td><input type="checkbox" id="discounts_remove" name="clear_db[discounts]" value="Y" /></td>
  <td><label for="discounts_remove">{$lng.lbl_discounts}</label></td>         
</tr>
<tr>
  <td><input type="checkbox" id="ship_data_remove" name="clear_db[ship_data]" value="Y" /></td>
  <td><label for="ship_data_remove">{$lng.lbl_shipping_rates}</label></td>         
</tr>
<tr>
  <td><input type="checkbox" id="clean_urls_remove" name="clear_db[clean_urls]" value="Y" /></td>
  <td><label for="clean_urls_remove">{$lng.lbl_clean_urls}</label></td>         
</tr>
{if $modules_to_delete}
<tr>
  <td colspan="2" style="font-weight: bold; padding-top: 10px;">{$lng.lbl_modules_data}:</td>
</tr>
{foreach from=$modules_to_delete item=mod_name key=mod}
<tr>
  <td><input type="checkbox" id="{$mod}" name="clear_db[{$mod}]" value="Y" /></td>
  <td><label for="{$mod}">{$mod_name}</label></td>
</tr>
{/foreach}
{/if}
</table>
</td>
</tr>
<tr>
	<td class="SubmitBox"><input type="submit" name="mode_clear_db" value="{$lng.lbl_remove_test_data|strip_tags:false|escape}" /></td>
</tr>

</table>
</form>

{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog extra='width="100%"'}
