/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Functions for product options module
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: func.js,v 1.2.2.1 2010/12/08 14:44:45 ferz Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

var current_taxes = [];
var product_thumbnail = document.getElementById('product_thumbnail');
var availObj = document.getElementById('product_avail');

/**
 * Rebuild page if some options is changed
 */
function check_options() {
  var local_taxes = [];
  var is_rebuild_wholesale = false;
  var variantid = false;

  if (typeof(taxes) != 'undefined') {
    for (var t in taxes) {
      if (hasOwnProperty(taxes, t))
        local_taxes[t] = taxes[t][0];
    }
  }
  price = default_price;

  /* Find variant */
  for (var x in variants) {
    if (!hasOwnProperty(variants, x) || variants[x][1].length == 0)
      continue;

    variantid = x;
    for (var c in variants[x][1]) {
      if (!hasOwnProperty(variants[x][1], c))
        continue;

      if (getPOValue(c) != variants[x][1][c]) {
        variantid = false;
        break;
      }
    }

    if (variantid)
      break;
  }

  /* If variant found ... */
  if (variantid) {
    var max_avail = variants[variantid][0][1];
    price = variants[variantid][0][0];
    orig_price = variants[variantid][0][4];
    avail = variants[variantid][0][1];

    /* Get variant wholesale prices */
    if (variants[variantid][3]) {
      product_wholesale = [];
      for (var t in variants[variantid][3]) {
        if (!hasOwnProperty(variants[variantid][3], t))
          continue;

        var _tmp = modi_price(variants[variantid][3][t][2], cloneObject(variants[variantid][3][t][3]), variants[variantid][3][t][4]);
        product_wholesale[t] = [
          variants[variantid][3][t][0], 
          variants[variantid][3][t][1], 
          _tmp[0],
          []
        ];

        /* Get variant wholesale taxes */
        for (var c in _tmp[1]) {
          if (hasOwnProperty(_tmp[1], c))
            product_wholesale[t][3][c] = _tmp[1][c];
        }
      }
      is_rebuild_wholesale = true;
    }

    /* Get variant taxes */
    for (var t in local_taxes) {
      if (hasOwnProperty(local_taxes, t) && variants[variantid][2][t])
        local_taxes[t] = parseFloat(variants[variantid][2][t]);
    }

    if (!product_thumbnail)
      product_thumbnail = document.getElementById('product_thumbnail');

    /* Change product thumbnail */
    if (product_thumbnail) {
      if (variants[variantid][0][2].src && variants[variantid][0][2].width > 0 && variants[variantid][0][2].height > 0) {
        if (getImgSrc(product_thumbnail) != variants[variantid][0][2].src) {

          if (getImgSrc(product_thumbnail) == product_image.src && typeof(product_image.isPNG) == 'undefined') {
            product_image.isPNG = isPngFix(product_thumbnail);
            product_image.width = product_thumbnail.width;
            product_image.height = product_thumbnail.height;
          }

          product_thumbnail.src = variants[variantid][0][2].src;
          product_thumbnail.width = variants[variantid][0][2]._x;
          product_thumbnail.height = variants[variantid][0][2]._y;
          if (typeof(window.saved_product_thumbnail) != 'undefined' && saved_product_thumbnail)
            saved_product_thumbnail = false;

          if (variants[variantid][0][6] && $.browser.msie)
            pngFix(product_thumbnail);
        }

      } else if (getImgSrc(product_thumbnail) != product_image.src) {
        product_thumbnail.src = product_image.src;
        if (product_image.width > 0 && product_image.height > 0) {
          product_thumbnail.width = product_image.width;
          product_thumbnail.height = product_image.height;
          if (typeof(window.saved_product_thumbnail) != 'undefined' && saved_product_thumbnail)
            saved_product_thumbnail = false;
        }

        if (product_image.isPNG)
          pngFix(product_thumbnail);
      }

      if (max_image_width > 0 && product_thumbnail.width > max_image_width) {
        product_thumbnail.height = Math.round(product_thumbnail.height*max_image_width/product_thumbnail.width);
        product_thumbnail.width = max_image_width;
      }
      if (max_image_height > 0 && product_thumbnail.height > max_image_height) {
        product_thumbnail.width = Math.round(product_thumbnail.width*max_image_height/product_thumbnail.height);
        product_thumbnail.height = max_image_height;
      }
    }

    /* Change product weight */
    if (document.getElementById('product_weight'))
      document.getElementById('product_weight').innerHTML = price_format(variants[variantid][0][3]);

    if (document.getElementById('product_weight_box'))
      document.getElementById('product_weight_box').style.display = parseFloat(variants[variantid][0][3]) > 0 ? "" : "none";

    /* Change product code */
    if (document.getElementById('product_code'))
      document.getElementById('product_code').innerHTML = variants[variantid][0][5];

  }

  if (pconf_price > 0)
    price = pconf_price;

  /* Find modifiers */
  var _tmp = modi_price(price, local_taxes, orig_price);
  price = _tmp[0];
  local_taxes = _tmp[1];
  if (!variantid) {
    product_wholesale = [];
    for (var t in _product_wholesale) {
      if (!hasOwnProperty(_product_wholesale, t))
        continue;

      _tmp = modi_price(_product_wholesale[t][2], _product_wholesale[t][3].slice(0), _product_wholesale[t][4]);
      product_wholesale[t] = [
        _product_wholesale[t][0],
        _product_wholesale[t][1],
        _tmp[0],
        _tmp[1]
      ];
    }
    is_rebuild_wholesale = true;
  }

  /* Update taxes */
  for (var t in local_taxes) {
    if (!hasOwnProperty(local_taxes, t))
      continue;

    if (document.getElementById('tax_'+t)) {
      document.getElementById('tax_'+t).innerHTML = price_format(Math.max(local_taxes[t], 0));
    }
    current_taxes[t] = local_taxes[t];
  }

  if (is_rebuild_wholesale)
    rebuild_wholesale();

  /* Update form elements */
  /* Update price */
  if (document.getElementById('product_price'))
    document.getElementById('product_price').innerHTML = price_format(Math.max(price, 0));

  /* Update alt. price */
  if (alter_currency_rate > 0 && document.getElementById('product_alt_price')) {
    var altPrice = price*alter_currency_rate;
    document.getElementById('product_alt_price').innerHTML = price_format(Math.max(altPrice, 0));
  }

  /* Update Save % */
  if (document.getElementById('save_percent') && document.getElementById('save_percent_box') && list_price > 0 && dynamic_save_money_enabled) {
    var save_percent = Math.round(100 - (price / list_price) * 100);
    if (save_percent > 0) {
      document.getElementById('save_percent_box').style.display = '';
      document.getElementById('save_percent').innerHTML = save_percent;

    } else {
      document.getElementById('save_percent_box').style.display = 'none';
      document.getElementById('save_percent').innerHTML = '0';
    }
  }

  /* Update product quantity */
  $('.product-quantity-text').html(avail > 0 ? substitute(txt_items_available, "items", (variantid ? avail : product_avail)) : lbl_no_items_available);
  $('.product-quantity-number').html(avail > 0 ? (variantid ? avail : product_avail) : 0);

  if ((mq > 0 && avail > mq+min_avail) || !is_limit)
    avail = mq + min_avail - 1;

  avail = Math.min(mq, avail);

  var select_avail = min_avail;

  /* Update product quantity selector */
  availObj = document.getElementById(quantity_input_box_enabled ? 'product_avail_input' : 'product_avail');

  if (availObj && availObj.tagName.toUpperCase() == 'SELECT') {

    // Select box
    if (!isNaN(min_avail) && !isNaN(avail)) {
      var first_value = -1;
      if (availObj.options[0])
        first_value = availObj.options[0].value;

      if (first_value == min_avail) {

        /* New and old first value in quantities list is equal */
        if ((avail-min_avail+1) != availObj.options.length) {
          if (availObj.options.length > avail-min_avail+1) {
            var cnt = availObj.options.length;
            for (var x = (avail-min_avail+1 < 0 ? 0 : avail-min_avail+1); x < cnt; x++)
              availObj.options[availObj.options.length-1] = null;

          } else {
            var cnt = availObj.options.length;
            for (var x = cnt+min_avail; x <= avail-min_avail+1; x++)
              availObj.options[cnt++] = new Option(x, x);
          }
        }
      } else {

        /* New and old first value in quantities list is differ */
        var cnt = availObj.options.length - 1;
        while (cnt >= 0)
          availObj.options[cnt--] = null;

        cnt = 0;
        for (var x = min_avail; x <= avail; x++)
          availObj.options[cnt++] = new Option(x, x);
      }
      if (availObj.options.length == 0 || min_avail > avail)
        availObj.options[0] = new Option(txt_out_of_stock, 0);
    }
    select_avail = availObj.options[availObj.selectedIndex].value;

  } else if (availObj && availObj.tagName.toUpperCase() == 'INPUT' && availObj.type.toUpperCase() == 'TEXT') {

    // Input box
        if (!isNaN(min_avail) && !isNaN(avail)) {
      availObj.minQuantity = min_avail;
      availObj.maxQuantity = max_avail;
    }

    if (isNaN(parseInt(availObj.value)) || availObj.value == 0) 
       availObj.value = min_avail;
    
      select_avail = availObj.value;
  }



  check_wholesale(select_avail);

  if (alert_msg == 'Y' && min_avail > avail)
    alert(txt_out_of_stock);
  
  /* Check exceptions */
  var ex_flag = check_exceptions();
  if (!ex_flag && (alert_msg == 'Y'))
    alert(exception_msg);

  if (document.getElementById('exception_msg')) {
    if (ex_flag) {
      document.getElementById('exception_msg').style.display = 'none';

    } else {
      document.getElementById('exception_msg').innerHTML = exception_msg_html;
      document.getElementById('exception_msg').style.display = '';
    }
  }

  return true;
}

/**
 * Calculate product price with price modificators 
 */
function modi_price(_price, _taxes, _orig_price) {
  var return_price = round(_price, 2);

  /* List modificators */
  for (var x2 in modifiers) {
    if (!hasOwnProperty(modifiers, x2))
      continue;

    var value = getPOValue(x2);
    if (!value || !modifiers[x2][value])
      continue;

    /* Get selected option */
    var elm = modifiers[x2][value];
    return_price += parseFloat(elm[1] == '$' ? elm[0] : (_price*elm[0]/100));

    /* Get tax extra charge */
    for (var t2 in _taxes) {
      if (hasOwnProperty(_taxes, t2) && elm[2][t2])
        _taxes[t2] += parseFloat(elm[1] == '$' ? elm[2][t2] : (_orig_price*elm[2][t2]/100));
    }
  }

  return [return_price, _taxes];
}

/**
 * Check product options exceptions
 */
function check_exceptions() {
  if (typeof(exceptions) === 'undefined')
    return true;

  /* List exceptions */
  for (var x in exceptions) {
    if (!hasOwnProperty(exceptions, x) || isNaN(x))
      continue;

    var found = true;
        for (var c in exceptions[x]) {
      if (!hasOwnProperty(exceptions[x], c))
        continue;

      var value = getPOValue(c);
      if (!value)
        return true;

            if (value != exceptions[x][c]) {
        found = false;
        break;
      }
    }
    if (found)
      return false;
  }

  return true;
}

/**
 * Rebuild wholesale tables
 */
function rebuild_wholesale() {
  var div = document.getElementById('wl-prices');
  var wl_table = $('table', div).get(0);
  var wl_taxes = $('div', div).get(0);

  if (!div || !wl_table || !wl_taxes)
    return false;

  /* Clear wholesale span object if product wholesale prices service array is empty */
  var i = wl_table.rows.length - 1;
  while (i > 0)
    wl_table.deleteRow(i--);

  if (!product_wholesale || product_wholesale.length == 0) {
    div.style.display = 'none';
    return false;
  }

  /* Display wholesale prices table */
  var str = '';
  var r;
  for (i in product_wholesale) {
    if (!hasOwnProperty(product_wholesale, i) || product_wholesale[i][0] == 0)
      continue;

    r = wl_table.insertRow(-1);
    insert_text = (product_wholesale[i][1] == 0) ? product_wholesale[i][0] + '+' : (product_wholesale[i][1] - product_wholesale[i][0] > 0 ? product_wholesale[i][0] + '-' + product_wholesale[i][1] : product_wholesale[i][0]);
    r.insertCell(-1).innerHTML = insert_text + '&nbsp;' + (product_wholesale[i][0] == 1 ? lbl_item : lbl_items);
    r.insertCell(-1).innerHTML = price_format(product_wholesale[i][2] < 0 ? 0 : product_wholesale[i][2], false, false, false, true);
  }

  if (wl_table.rows.length <= 1) {
        div.style.display = 'none';
    return false;
  }

    /* Display wholesale prices taxes */
    var display_taxes = false;
  if (taxes.length > 0) {
        for (i in taxes) {
            if (hasOwnProperty(taxes, i) && current_taxes[i] > 0)
        display_taxes = true;
        }
    }

  if (!display_taxes) 
     wl_taxes.style.display = 'none';
  else
    wl_taxes.style.display = '';

    div.style.display = '';

  return true;
}

/**
 * Display current wholesale price as product price
 */
function check_wholesale(qty) {

  if ((typeof(product_wholesale) == 'undefined') ||  product_wholesale.length == 0)
    return true;

  var wl_taxes = current_taxes.slice(0);
  var wl_price = price;
  for (var x = 0; x < product_wholesale.length; x++) {
    if (product_wholesale[x][0] <= qty && (product_wholesale[x][1] >= qty || product_wholesale[x][1] == 0)) {
      wl_price = product_wholesale[x][2];
      wl_taxes = product_wholesale[x][3].slice(0);
    }

    if (document.getElementById('wp' + x)) {
      var wPrice = price-default_price+product_wholesale[x][2];
      document.getElementById('wp' + x).innerHTML = price_format(Math.max(wPrice, 0));
    }
  }

  if (document.getElementById('product_price'))
    document.getElementById('product_price').innerHTML = price_format(Math.max(wl_price, 0));

  if (alter_currency_rate > 0 && document.getElementById('product_alt_price')) {
    document.getElementById('product_alt_price').innerHTML = price_format(Math.max(wl_price * alter_currency_rate, 0));
  }

  /* Update Save % */
  if (document.getElementById('save_percent') && document.getElementById('save_percent_box') && list_price > 0 && dynamic_save_money_enabled) {
    var save_percent = Math.round(100 - (Math.max(wl_price, 0) / list_price) * 100);
    if (save_percent > 0) {
      document.getElementById('save_percent_box').style.display = '';
      document.getElementById('save_percent').innerHTML = save_percent;

    } else {
      document.getElementById('save_percent_box').style.display = 'none';
      document.getElementById('save_percent').innerHTML = '0';
    }
  }


  for (var x in taxes) {
    if (hasOwnProperty(taxes, x) && document.getElementById('tax_'+x) && wl_taxes[x] && current_taxes[x]) {
      document.getElementById('tax_'+x).innerHTML = price_format(Math.max(wl_taxes[x], 0));
    }
  }

  return true;
}

/**
 * Get product option value
 */
function getPOValue(c) {
  if (!document.getElementById('po' + c) || document.getElementById('po' + c).tagName.toUpperCase() != 'SELECT')
    return false;

  return document.getElementById('po'+c).options[document.getElementById('po'+c).selectedIndex].value;
}

/**
 * Get product option object by class name / class id
 */
function product_option(classid) {
  if (!isNaN(classid))
     return document.getElementById("po" + classid);

  if (!names)
    return false;

  for (var x in names) {
    if (!hasOwnProperty(names, x) || names[x]['class_name'] != classid)
      continue;

    return document.getElementById('po' + x);
    }

  return false;
}

/**
 * Get product option value by class name / or class id
 */
function product_option_value(classid) {
  var obj = product_option(classid);
  if (!obj)
    return false;

  if (obj.type != 'select-one')
    return obj.value;

  var classid = parseInt(obj.id.substr(2));
  var optionid = parseInt(obj.options[obj.selectedIndex].value);
  if (names[classid] && names[classid]['options'][optionid])
    return names[classid]['options'][optionid];

  return false;
}

/**
 * Hide the "Options are expired message" and update product in the cart
 */
function close_opts_expire_msg(cartid) {

  var post_params = 'target=cart&mode=update&product_options=1&id=' + cartid;
  var cart_message_box = document.getElementById('cart_message_' + cartid);

  $.ajax({type: 'POST', url: 'popup_poptions.php', data: post_params});
  if (cart_message_box) {
    cart_message_box.style.display = 'none';
  }

  return false;
}

