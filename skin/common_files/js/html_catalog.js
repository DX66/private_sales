/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * HTML catalog scripts
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: html_catalog.js,v 1.2 2010/05/27 13:43:07 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

function hcSwitchTemplateEditor(obj) {
  if (!obj || !obj.tagName)
    return false;

  switch (obj.tagName.toUpperCase()) {
    case 'A':
      var t = obj.id.replace(/^example_/, '');
      break;

    case 'IMG':
      var m = obj.id.match(/^mark_(.+)_(plus|minus)$/);
      if (!m)
        return false;

      t = m[1]
      break;

    default:
      return false;
  }

  var div = document.getElementById('edit_' + t);
  var iplus = document.getElementById('mark_' + t + '_plus');
  var iminus = document.getElementById('mark_' + t + '_minus');

  if (!div || !iplus || !iminus)
    return false;

  var isEdit = div.style.display != 'none';

  div.style.display = isEdit ? 'none' : '';
  iminus.style.display = isEdit ? 'none' : '';
  iplus.style.display = !isEdit ? 'none' : '';

  return true;
}

function hcApplyTemplate(obj) {
  var t = obj.id.replace(/^apply_/, '');

  var inp = document.getElementById('template_' + t);
  var a = document.getElementById('example_' + t);
  if (!inp || !a)
    return false;

  var store = inp.form.elements.namedItem('templates[' + t + ']');
  if (!store)
    return false;

  var status = hcCheckTemplate(t, inp.value);
  if (status.status) {
    a.innerHTML = inp.value + '.html';
    store.value = inp.value;
  }

  return status.status;
}

function hcSwitchKeyword(t, obj) {
  var kw = obj.innerHTML;
  var inp = document.getElementById('template_' + t);

  if (!inp || !templates[t])
    return false;

  var found = false;
  for (var i = 0; i < templates[t].keywords.length && !found; i++) {
    if (kw == templates[t].keywords[i].keyword)
      found = true;
  }

  if (!found)
    return false;

  var re = new RegExp('\{' + kw + '\}', 'g');

  if (inp.value.search(re) == -1) {
    hcInsertText(inp, '{' + kw + '}');

  } else {
    inp.value = inp.value.replace(re, '');
  }

  if (inp.focus)
    inp.focus();

  return hcCheckTemplateStatus(inp);
}

function hcCheckTemplateStatus(obj) {
  var t = obj.id.replace(/^template_/, '');

  if (!templates[t])
    return false;

  var err = document.getElementById('error_' + t);
  var apply = document.getElementById('apply_' + t);
  if (!apply)
    return false;

  var status = hcCheckTemplate(t, obj.value);

  if (!status.status) {
    obj.className = 'HCTemplateInvalid';
    apply.disabled = true;

        if (err) {
            if (status.err == 2)
                err.innerHTML = substitute(lbl_required_tags_are_missing, "tags", '{' + status.requiredExcept.join('}, {') + '}');
            else if (status.err == 1)
                err.innerHTML = lbl_format_template_is_invalid;
        }

  } else {
    obj.className = 'HCTemplateValid';
    apply.disabled = false;
    if (err)
      err.innerHTML = '';
  }

  if (!obj.keywords) {
    var keywords = [];
    for (var i = 0; i < templates[t].keywords.length; i++) {
      var k = document.getElementById('keyword_' + t + '_' + templates[t].keywords[i].keyword);
      if (!k)
        return false;

      keywords[keywords.length] = {
        keyword: templates[t].keywords[i].keyword,
        obj: k,
        re: new RegExp('\{' + templates[t].keywords[i].keyword + '\}', 'g'),
        required: templates[t].keywords[i].required,
        data: templates[t].keywords[i].data
      };

    }
    obj.keywords = keywords;
  }

  for (var i = 0; i < obj.keywords.length; i++) {
    if (obj.value.search(obj.keywords[i].re) != -1) {
      obj.keywords[i].obj.parentNode.className = 'HCKeywordExists';

    } else if (obj.keywords[i].required) {
      obj.keywords[i].obj.parentNode.className = 'HCKeywordRequired';

    } else {
      obj.keywords[i].obj.parentNode.className = 'HCKeyword';
    }

  }

  return status.status;
}

function hcCheckTemplate(t, txt) {
    if (!templates[t])
        return {status: false};

  var requiredExist = true;
  var requiredExcept = [];
    for (var i = 0; i < templates[t].keywords.length; i++) {
    var kw = templates[t].keywords[i].keyword;
    var re = new RegExp('\{' + kw + '\}', 'g');
    var found = txt.search(re) != -1;

    if (found) {
      txt = txt.replace(re, '___');

    } else if (templates[t].keywords[i].required) {
      requiredExist = false;
      requiredExcept[requiredExcept.length] = kw;
    }
    }

  var valid = requiredExist && txt.search(/^[a-zA-Z0-9_\-\.]+$/) === 0 && txt.length < 201;

  if (valid)
    return {status: true, err: 0};

  if (!requiredExist)
    return {status: false, err: 2, requiredExcept: requiredExcept};

  return {status: false, err: 1};
}

function hcInsertText(inp, txt) {
  if (document.selection) {

    // IE support
    inp.focus();
    var sel = document.selection.createRange();
    sel.text = txt;

  } else if (inp.selectionStart || inp.selectionStart == 0) {

    // Mozilla support

    var startPos = inp.selectionStart;
    var endPos = inp.selectionEnd;
    inp.value = inp.value.substring(0, startPos) + txt + inp.value.substring(endPos, inp.value.length);

  } else {
    inp.value += txt;
  }

  return true;
}

function hcReset(t) {
  var e = document.getElementById('template_' + t);
  if (!e)
    return false;


  var d = e.form.elements.namedItem('templates[' + t + ']');
  if (!d)
    return false;

  e.value = d.value;

  hcCheckTemplateStatus(e);

  return true;
}
