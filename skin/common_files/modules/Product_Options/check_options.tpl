{*
$Id: check_options.tpl,v 1.4.2.1 2010/09/10 08:26:45 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript">
//<![CDATA[

/*
variants array:
  0 - array:
    0 - taxed price
    1 - quantity
    2 - variantid if variant have thumbnail
    3 - weight
    4 - original price (without taxes)
    5 - productcode
    6 - W_is_png
  1 - array: variant options as classid => optionid
  2 - array: taxes as taxid => tax amount
  3 - wholesale prices array:
    0 - quantity
    1 - next quantity
    2 - taxed price
    3 - taxes array: as taxid => tax amount
    4 - original price (without taxes)
*/
var variants = [];
{if $variants ne ''}
{foreach from=$variants item=v key=k}
variants[{$k}] = [{strip}
  [
    {$v.taxed_price|default:$v.price|default:$product.taxed_price|default:$product.price},
    {$v.avail|default:0},
    new Image(),
    '{$v.weight|default:0}',
    {$v.price|default:$product.price|default:'0'},
    '{$v.productcode|wm_remove|escape:javascript}',
    {$v.W_is_png|default:0}
  ],
  {ldelim}{rdelim},
  {ldelim}{rdelim},
  []
{/strip}];
{foreach from=$v.wholesale item=w key=p}
variants[{$k}][3][variants[{$k}][3].length] = [{strip}
  {$w.quantity|default:0},
  {if $w.next_quantity}{dec value=$w.next_quantity}{else}0{/if},
  {$w.taxed_price|default:$product.taxed_price},
  {ldelim}{rdelim},
  {$w.price|default:$product.price}
{/strip}];
{foreach from=$w.taxes item=t key=kt}
variants[{$k}][3][variants[{$k}][3].length-1][3][{$kt}] = {$t|default:0};
{/foreach}
{/foreach}
{foreach from=$v.options item=o}
{if $o ne ''}
variants[{$k}][1][{$o.classid|default:0}] = {$o.optionid|default:0};
{/if}
{/foreach}
{foreach from=$v.taxes item=t key=id}
variants[{$k}][2][{$id}] = {$t|default:0};
{/foreach}
{if $v.is_image}
variants[{$k}][0][2].src = "{if $v.image_url ne ''}{$v.image_url}{else}{if $full_url}{$http_location}{else}{$xcart_web_dir}{/if}/image.php?id={$k}&type=W{/if}"; 
variants[{$k}][0][2]._x = {$v.variant_image_x|default:0};
variants[{$k}][0][2]._y = {$v.variant_image_y|default:0};
{/if}
{/foreach}
{/if}

/*
modifiers array: as clasid => array: as optionid => array:
  0 - price_modifier
  1 - modifier_type
  2 - taxes array: as taxid => tax amount
*/
var modifiers = [];
/* names array: as classid => class name */
var names = [];
{foreach from=$product_options item=v key=k}
names[{$v.classid}] = {ldelim}class_name: "{$v.class_orig|default:$v.class|wm_remove|escape:javascript}", options: []{rdelim};
{foreach from=$v.options item=o name=opts}
names[{$v.classid}]['options'][{$o.optionid}] = "{$o.option_name_orig|default:$o.option_name|wm_remove|escape:javascript}";
{/foreach}
{if $v.is_modifier eq 'Y'}
modifiers[{$v.classid}] = {ldelim}{rdelim};
{foreach from=$v.options item=o name=opts}
modifiers[{$v.classid}][{$o.optionid}] = [{strip}
  {$o.price_modifier|default:"0.00"}, 
  '{$o.modifier_type|default:"$"}',
  {ldelim}{rdelim}
{/strip}];
{foreach from=$o.taxes item=t key=id name=optt}
modifiers[{$v.classid}][{$o.optionid}][2][{$id}] = {$t|default:0};
{/foreach}
{/foreach}
{/if}
{/foreach}

/*
taxes array: as taxid => array()
  0 - calculated tax value for default product price
  1 - tax name
  2 - tax type ($ or %)
  3 - tax value
*/
var taxes = [];
{if $product.taxes}
{foreach from=$product.taxes key=tax_name item=tax}
{if $tax.display_including_tax eq "Y" and ($tax.display_info eq 'A' or $tax.display_info eq 'V')}
taxes[{$tax.taxid}] = [{$tax.tax_value|default:0}, "{$tax.tax_display_name}", "{$tax.rate_type}", "{$tax.rate_value}"];
{/if}
{/foreach}
{/if}

/* exceptions array: as exctionid => array: as clasid => optionid */
var exceptions = [];
{if $product_options_ex ne ''}
{foreach from=$product_options_ex item=v key=k}
exceptions[{$k}] = [];
{foreach from=$v item=o}
exceptions[{$k}][{$o.classid}] = {$o.optionid};
{/foreach} 
{/foreach} 
{/if}

/*
_product_wholesale array: as id => array:
  0 - quantity
  1 - next quantity
  2 - taxed price
  3 - taxes array: as taxid => tax amount
  4 - original price (without taxes)
*/
var product_wholesale = [];
var _product_wholesale = [];
{if $product_wholesale ne ''}
{foreach from=$product_wholesale item=v key=k}
_product_wholesale[{$k}] = [{$v.quantity|default:0},{$v.next_quantity|default:0},{$v.taxed_price|default:$product.taxed_price}, [], {$v.price|default:$product.price}];
{if $v.taxes ne ''}
{foreach from=$v.taxes item=t key=kt}
_product_wholesale[{$k}][3][{$kt}] = {$t|default:0};
{/foreach}
{/if}
{/foreach}
{/if}

var product_image = new Image();
product_image.src = "{if $product.images.P.url and (not $product.images.P.is_default or $product.images.T.is_default)}{$product.images.P.url}{elseif $product.images.T.url and $product.images.P.is_default and not $product.images.T.is_default}{$product.images.T.url}{else}{if $full_url}{$http_location}{else}{$xcart_web_dir}{/if}/image.php?id={$product.productid}&type=P{/if}";
var exception_msg = "{$lng.txt_exception_warning|strip_tags|wm_remove|escape:javascript}";
var exception_msg_html = "{$lng.txt_exception_warning|wm_remove|escape:javascript}";
var txt_out_of_stock = "{$lng.txt_out_of_stock|strip_tags|wm_remove|escape:javascript}";
var pconf_price = {$taxed_total_cost|default:0};
var default_price = {$product.taxed_price|default:"0"};
var alter_currency_rate = {$config.General.alter_currency_rate|default:"0"};
var lbl_no_items_available = "{$lng.lbl_no_items_available|wm_remove|escape:javascript}";
var txt_items_available = "{$lng.txt_items_available|wm_remove|escape:javascript}";
var list_price = {$product.list_price|default:0};
var price = {$product.taxed_price|default:"0"};
var orig_price = {$product.price|default:$product.taxed_price|default:"0"};
var mq = {if $config.Appearance.max_select_quantity gt $product.appearance.min_quantity}{$config.Appearance.max_select_quantity}{else}{$product.appearance.min_quantity}{/if};
var dynamic_save_money_enabled = {if $config.Product_Options.dynamic_save_money_enabled eq 'Y'}true{else}false{/if};
var quantity_input_box_enabled = {if $product.appearance.quantity_input_box_enabled}true{else}false{/if};
var max_image_width = {$max_image_width|default:0};
var max_image_height ={$max_image_height|default:0};

var lbl_item = "{$lng.lbl_item|wm_remove|escape:javascript}";
var lbl_items = "{$lng.lbl_items|wm_remove|escape:javascript}";
var lbl_quantity = "{$lng.lbl_quantity|wm_remove|escape:javascript}";
var lbl_price = "{$lng.lbl_price_per_item|wm_remove|escape:javascript}";
var txt_note = "{$lng.txt_note|wm_remove|escape:javascript}";
var lbl_including_tax = "{$lng.lbl_including_tax|wm_remove|escape:javascript}";

//]]>
</script>

