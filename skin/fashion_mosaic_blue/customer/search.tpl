{*
$Id: search.tpl,v 1.2 2010/06/22 06:34:48 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<div class="search">
    <form method="post" action="search.php" name="productsearchform">

      <input type="hidden" name="simple_search" value="Y" />
      <input type="hidden" name="mode" value="search" />
      <input type="hidden" name="posted_data[by_title]" value="Y" />
      <input type="hidden" name="posted_data[by_descr]" value="Y" />
      <input type="hidden" name="posted_data[by_sku]" value="Y" />
      <input type="hidden" name="posted_data[search_in_subcategories]" value="Y" />
      <input type="hidden" name="posted_data[including]" value="all" />

      {strip}

        <div class="search-top-box">
        <input type="text" name="posted_data[substring]" class="text{if not $search_prefilled.substring} default-value{/if}" value="{$search_prefilled.substring|default:$lng.lbl_search|escape}" />
        {include file="customer/buttons/button.tpl" type="input" style="image"}
        </div>
        <div class="search-link">
          <a href="search.php">{$lng.lbl_advanced_search}</a>
        </div>
      {/strip}

    </form>

</div>
