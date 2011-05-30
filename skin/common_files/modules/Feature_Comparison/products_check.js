/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Products check
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: products_check.js,v 1.2 2010/05/27 14:09:39 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

var fe_form;

function fe_create_form(obj, url, mode) {
  var dialog = obj;
  while (dialog) {
    if (dialog.tagName && dialog.tagName.toUpperCase() == 'DIV' && dialog.className.search(/^dialog( |$)/) !== -1)
      break;

    dialog = dialog.parentNode;
  }

  if (!dialog)
    dialog = document;

  var ids = [];
  $('input').each(function() {
    var m;
    if (this.id && (m = this.id.match(/^fe_pid_([0-9]+)$/)) && this.checked)
      ids[ids.length] = m[1];
  });

  if (ids.length == 0)
    return false;

  var form = document.createElement('FORM');
  form.name = 'dyncompareform';
  form.action = xcart_web_dir + '/' + url;
  form.method = 'post';

  document.body.appendChild(form);

  var inp = document.createElement('INPUT');
  inp.type = 'hidden';
  inp.name = 'mode';
  inp.value = mode;

  form.appendChild(inp);

  for (var i = 0; i < ids.length; i++) {
    inp = document.createElement('INPUT');
    inp.type = 'hidden';
    inp.name = 'productids[' + ids[i] + ']';
    inp.value = 'Y';

    form.appendChild(inp);
  }

  if (localBFamily == 'MSIE') {
    setTimeout(getMethod(form.submit, form), 500);

  } else {
    form.submit();
  }

  return true;
}

function fe_check(obj, id) {
  var p = document.getElementById('fe_pid_' + id);

    if (!p)
        return false;

  p.value = p.value == 'Y' ? '' : 'Y';

  if (obj)
    obj.className = (p.value == '' ? 'button' : 'button checked-button');

    return true;
}
