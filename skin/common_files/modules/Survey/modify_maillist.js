/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Modify maillist functions
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: modify_maillist.js,v 1.2 2010/05/27 14:09:40 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

function check_surveys(form) {
  var found = 0;
  for (var i = 0; i < form['surveylist[]'].options.length; i++)
    if (form['surveylist[]'].options[i].selected)
      found++;

  if (found == 0)
    alert(txt_survey_list_is_empty);

  return (found > 0);
}

function check_newslist(form) {
  var found = 0;
  for (var i = 0; i < form['newslist[]'].options.length; i++)
    if (form['newslist[]'].options[i].selected)
      found++;

  if (found == 0)
    alert(lbl_news_list_is_empty);

  return (found > 0);
}

function check_import(form) {
  if (form.userfile.value.length == 0)
    alert(txt_import_file_wasnt_assigned);

  return (form.userfile.value.length > 0);
}

function check_emailslist(form) {
  return true;
}

var tabsArray = {};
function selectTab(obj, boxName, partName) {
  var part = document.getElementById(boxName+'_'+partName+'_part');
  if (!part)
    return false;

  if (tabsArray[boxName]) {
    if (tabsArray[boxName].current)
      tabsArray[boxName].current.className = 'SurveyTab';

    if (tabsArray[boxName].currentPart)
      tabsArray[boxName].currentPart.style.display = 'none';

  } else if (obj.tagName && obj.tagName.toUpperCase() == 'TD') {

    var tr = obj.parentNode;
    for (var i = 0; i < tr.cells.length; i++) {
      if (tr.cells[i].className == 'SurveyTabSelected') {
        tr.cells[i].className = 'SurveyTab';
        var id = tr.cells[i].id.replace(/_switch$/, '')+"_part";
        if (document.getElementById(id))
          document.getElementById(id).style.display = 'none';
        break;
      }
    }
  }

  tabsArray[boxName] = {
    current: obj,
    currentPart: part
  };

  tabsArray[boxName].current.className = 'SurveyTabSelected';
  tabsArray[boxName].currentPart.style.display = '';

  return true;
}

function popup_product_multi(obj) {
  var td = obj.parentNode;
  var obj_id, obj_name;
  for (var i = 0; i < td.childNodes.length; i++ ) {
    if (!td.childNodes[i].name || !td.childNodes[i].tagName)
      continue;

    if (td.childNodes[i].name.search(/new_element/) != -1 && td.childNodes[i].type == 'hidden')
      obj_id = td.childNodes[i];

    if (td.childNodes[i].name.search(/newproduct/) != -1 && td.childNodes[i].type == 'text')
      obj_name = td.childNodes[i];
  }

  if (!obj_id || !obj_name)
    return false;

  obj_id.id = obj_id.name.replace(/[\[\]]/g, "_");
  obj_name.id = obj_name.name.replace(/[\[\]]/g, "_");

  return popup_product(obj_id.id, obj_name.id);
}
