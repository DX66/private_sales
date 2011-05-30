{*
$Id: quick_search.tpl,v 1.1 2010/05/21 08:31:58 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript" src="{$SkinDir}/js/quick_search.js"></script>
{* FIX for position:fixed in IE6*}
{literal}
<!--[if gte IE 5.5]>
<![if lt IE 7]>
<style type="text/css">
div#quick_search_panel {
  position: absolute; right: 20px; bottom: 10px;
  right: auto; bottom: auto;
  left: expression((-20-quick_search_panel.offsetWidth+(document.documentElement.clientWidth ? document.documentElement.clientWidth : document.body.clientWidth)+(ignoreMe2=document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft))+'px');
  top: expression((-10-(quick_search_panel).offsetHeight+(document.documentElement.clientHeight ? document.documentElement.clientHeight : document.body.clientHeight)+(ignoreMe=document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop))+'px');
}
</style>
<![endif]>
<![endif]-->
{/literal}
<div id="quick_search_panel" style="display:none;">
  <div class="quick-search-panel-main">

    <div class="quick-search-body" id="quick_search_body1">
      <span id="quick_search_results">{$lng.lbl_keywords}</span>
      <span id="quick_search_no_results" style="display:none;">{$lng.lbl_no_results_found}</span>
      <span id="quick_search_no_pattern" style="display:none;">{$lng.lbl_quick_search_nopattern}</span><br />
    </div>

    <div class="quick-search-body" id="quick_search_body2" style="display:none;">
      {$lng.lbl_searching}...<br /><br />
      <img src="{$ImagesDir}/quick_search_searching.gif" alt="" />
    </div>

    <div class="quick-search-close" onclick="javascript:close_quick_search();"></div>

  </div>
</div>
