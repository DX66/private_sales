/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Functions related to product variants
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: product_variants.js,v 1.2 2010/05/27 14:09:39 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

var old_cross = new Array();

function rebuildWP() {
  var tbl = document.getElementById('wp_table');
  var y, z, objTr, objTd, objInput, found;
  var cross = new Array();
  var vidsWP = new Array();
  var prefix = "wprices";
  var prefix_rg = new RegExp(prefix+'_', 'g');

  if (!tbl)
    return false;

  for (var x in vwprices) {
    if (hasOwnProperty(vwprices, x) && !isNaN(x) && vids[x] && vids[x][0].checked)
      vidsWP[vidsWP.length] = x;
  }

  if (vidsWP.length > 0)  
    cross = vwprices[vidsWP[0]].slice(0);

  if (vidsWP.length > 1) {
    for (x = 1; x < vidsWP.length; x++) {
      if (!vwprices[vidsWP[x]]) {
        cross = new Array();
        break;
      }
      for (y = cross.length-1; y >= 0; y--) {
        found = false;
        for (z = 0; z < vwprices[vidsWP[x]].length; z++) {
          if (cross[y][0] == vwprices[vidsWP[x]][z][0] && cross[y][1] == vwprices[vidsWP[x]][z][1])
            found = true;
        }
        if (!found) 
          cross.splice(y, 1);
      }
    }
  }

  if (cross.length == 0)
    old_cross = new Array();

  if (old_cross.length == cross.length && old_cross.length > 0) {
    for (x = 0; x < cross.length; x++) {
      found = false;
      for (y = 0; y < old_cross.length; y++) {
        if (cross[x][0] == old_cross[y][0] && cross[x][1] == old_cross[y][1])
          found = true;
      }
      if (!found) {
        old_cross = new Array();
        break;
      }
    }
  } else {
    old_cross = new Array();
  }

  if (old_cross.length > 0)
    return true;

  if (tbl.rows.length > 2) 
    for (x = tbl.rows.length-1; x > 0; x--) {
      if (tbl.rows[x].id.search(prefix_rg) == 0) 
        tbl.deleteRow(x);
    }

  var firstRow = tbl.rows[0].rowIndex ? tbl.rows[0].rowIndex+1 : 1;
  for (x = 0; x < cross.length; x++) {
    objTr = tbl.insertRow(firstRow);
    objTr.id = prefix+"_"+x;
    objTd = objTr.insertCell(0);
    objTd.innerHTML = cross[x][0];

    objTd = objTr.insertCell(1);

    objInput = document.createElement("INPUT");
    objInput.type = "text";
    objTd.appendChild(objInput);
    objInput.size = 7;
    objInput.name = "wprices["+x+"][price]";
    objInput.value = cross[x][2];

    objInput = document.createElement("INPUT");
    objInput.type = "hidden";
    objTd.appendChild(objInput);
    objInput.name = "wprices["+x+"][quantity]";
    objInput.value = cross[x][0];

    objInput = document.createElement("INPUT");
    objInput.type = "hidden";
    objTd.appendChild(objInput);
    objInput.name = "wprices["+x+"][membershipid]";
    objInput.value = cross[x][1];

    objTd = objTr.insertCell(2);
    if (cross[x][1] == 0) {
      objTd.innerHTML = lbl_all;
    } else if (memberships[cross[x][1]]) {
      objTd.innerHTML = memberships[cross[x][1]];
    } else {
      objTd.innerHTML = cross[x][1];
    }

    objTd = objTr.insertCell(3);
    objInput = document.createElement("INPUT");
    objInput.type = "button";
    objTd.appendChild(objInput);
    objInput.value = lbl_delete;
    objInput.id = x;
    objInput.onclick = new Function('', 'if (old_cross[this.id]) { document.productvariantsform.mode.value = "delete_wprice"; document.productvariantsform.delete_wprice_quantity.value = old_cross[this.id][0]; document.productvariantsform.delete_wprice_membershipid.value = old_cross[this.id][1]; document.productvariantsform.submit(); }');

    firstRow++;
  }

  old_cross = cross.slice(0);  
}

function vidsChecked() {
  for (var v in vids) {
    if (hasOwnProperty(vids, v) && vids[v][0].checked)
      return true;
  }

  return false;
}

function addWImage() {
  try {
    if (window.pwindow && !pwindow.closed) {
      setTimeout("addWImage()", 200);
      return;

    }
  } catch (e) {
    imgTStmap = oldTStmap;
    return;
  }

  if (document.getElementById('imageW_onunload').value != 'Y') {
    imgTStmap = oldTStmap;
    return;
  }

  document.productvariantsform.tstamp.value = imgTStmap;
  document.getElementById('imageW_onunload').value = '';
  _getById('wimg_update_action').value = "A";

  for (var v in vids) {
    if (hasOwnProperty(vids, v)) 
      displayImage(vids[v][0], v);
  }
}

function resetWImage() {
  if (!vidsChecked()) {
    alert(msg_adm_warn_variants_sel);
  } else {
    _getById('wimg_update_action').value = "";

    for (var v in vids) {
      if (hasOwnProperty(vids, v))
        vids[v][1].src = current_location+"/image.php?type=W&id="+v;
    }

    obj = document.getElementById('skip_image_W');
    if (obj)
      obj.value = "Y";

    oldTStmap = imgTStmap = null;
    _getById('imageW_reset').style.display = 'none';
    _getById('imageW_text').style.display = 'none';
  }
  return false;
}

function displayImage(obj, id) {
  var d = new Date();

  if (!obj.checked) {
    vids[id][1].src = current_location+"/image.php?type=W&id="+id+"&tmp="+d.getTime();
  } else if (_getById('wimg_update_action').value == "D") {
    vids[id][1].src = current_location+"/image.php?type=P&id="+productid;
  } else if (imgTStmap && pwindow && pwindow.closed) {
    vids[id][1].src = current_location+"/image.php?type=W&id="+imgTStmap+"&tmp="+d.getTime();
  }
}

function updateWImage() {
  if (!vidsChecked()) {
    alert(msg_adm_warn_variants_sel);
  }
    else if (!pwindow || pwindow.closed) {
    dateObj = new Date();
    oldTStmap = imgTStmap;
    imgTStmap = dateObj.getTime();
    pwindow = popup_image_selection('W', imgTStmap, 'imageW');
    addWImage();
  } 
  return false;
}

function deleteWImage() {
  if (!vidsChecked()) {
    alert(msg_adm_warn_variants_sel);
  }
  else if (vidsChecked() && (!pwindow || pwindow.closed)) {

    _getById('wimg_update_action').value = "D";

    for (var v in vids) {
      if (hasOwnProperty(vids, v)) {
        displayImage(vids[v][0], v);
      }
    }

    _getById('imageW_reset').style.display = '';
    _getById('imageW_text').style.display = '';

  }
  return false;
}
