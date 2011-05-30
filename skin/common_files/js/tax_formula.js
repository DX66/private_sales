/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Taxes editing 
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: tax_formula.js,v 1.2 2010/05/27 13:43:07 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

function addElm(name, elm, elm_type) {
var x, current_x;

  current_x = -1;
  x = -1;
  if(his.length > 0)
    for(x = 0; x < his.length; x++) {
      if(his[x][0] == name) {
        current_x = x;
        break;
      }
    }
  if(current_x == -1) {
    current_x = x+1
    his[current_x] = new Array();
    his[current_x][0] = name;
    his[current_x][1] = -1;
    his[current_x][2] = new Array();
    his[current_x][5] = new Array();
    his[current_x][3] = document.getElementById(name).value;
    his[current_x][4] = (document.getElementById(name).value == '='?'O':'V');
      
  }

  if(elm_type != '=') {
    if(his[current_x][1] == -1) {
      if(his[current_x][4] == elm_type) {
        alert(alert_message);
        return false;
      }
    } else if(his[current_x][5][his[current_x][1]] == elm_type){
      alert(alert_message);
      return false;
    }
  } else {
    document.getElementById(name).value = '';
    elm_type = 'O';
  }

  document.getElementById(name).value += elm;

    his[current_x][1]++; 
    his[current_x][2][his[current_x][1]] = document.getElementById(name).value;
  his[current_x][5][his[current_x][1]] = elm_type;
    for(x = his[current_x][1]+1; x < his[current_x][2].length; x++) {
        his[current_x][2][x] = null; 
    his[current_x][5][x] = null;
  }
}

function undoFormula(name, type) {
var x, current_x;
 
    current_x = -1;
    for(x = 0; x < his.length; x++) {
        if(his[x][0] == name) {
            current_x = x;
            break;
        } 
    }
    if(current_x == -1)
    return false;

  if(type == 'R') {
    if((his[current_x][1]+1) < his[current_x][2].length)
      his[current_x][1]++;
  } else if(his[current_x][1] > -1)
    his[current_x][1]--;

  document.getElementById(name).value = ((his[current_x][1] == -1)?his[current_x][3]:his[current_x][2][his[current_x][1]]);
}

function checkFormula(name) {
var x, current_x;

  current_x = -1;
  x = -1;
  if(his.length > 0)
    for(x = 0; x < his.length; x++) {
      if(his[x][0] == name) {
        current_x = x;
        break;
      }
    }
  if(current_x == -1) {
    current_x = x+1
    his[current_x] = new Array();
    his[current_x][0] = name;
    his[current_x][1] = -1;
    his[current_x][2] = new Array();
    his[current_x][5] = new Array();
    his[current_x][3] = document.getElementById(name).value;
    his[current_x][4] = (document.getElementById(name).value == '='?'O':'V');
      
  }

  if(his[current_x][1] == -1) {
    if(his[current_x][4] == 'O') {
      alert(alert_message);
      return false;
    }
  } else if(his[current_x][5][his[current_x][1]] == 'O'){
    alert(alert_message);
    return false;
  }
  return true;
}

function clearFormula(name) {
var x, current_x;

  current_x = -1;
  x = -1;
  if(his.length > 0)
    for(x = 0; x < his.length; x++) {
      if(his[x][0] == name) {
        current_x = x;
        break;
      }
    }

  if(current_x != -1)
    document.getElementById(name).value = his[current_x][3];
}

function isFormulaEmpty(name) {
  return document.getElementById(name).value == '=';
}
