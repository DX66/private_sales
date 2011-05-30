/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * CC number checking functions 
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: check_cc_number_script.js,v 1.3 2010/08/03 13:42:44 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

// $Id: check_cc_number_script.js,v 1.3 2010/08/03 13:42:44 igoryan Exp $

function isSwitchSolo(cardType, cardNbr) {

  if (cardType != "SW" && cardType != "SO")
    return false;

  switch(cardType) {
    case "SW":
      var thisRules = ["490302,490309,18,1","490335,490339,18,1","491101,491102,16,1","491174,491182,18,1","493600,493699,19,1","564182,564182,16,2","633300,633300,16,0","633301,633301,19,1","633302,633349,16,0","675900,675900,16,0","675901,675901,19,1","675902,675904,16,0","675905,675905,19,1","675906,675917,16,0","675918,675918,19,1","675919,675937,16,0","675938,675940,18,1","675941,675949,16,0","675950,675962,19,1","675963,675997,16,0","675998,675998,19,1","675999,675999,16,0"];
      break;
    case "SO":
      var thisRules = ["633450,633453,16,0","633454,633457,16,0","633458,633460,16,0","633461,633461,18,1","633462,633472,16,0","633473,633473,18,1","633474,633475,16,0","633476,633476,19,1","633477,633477,16,0","633478,633478,18,1","633479,633480,16,0","633481,633481,19,1","633482,633489,16,0","633490,633493,16,1","633494,633494,18,1","633495,633497,16,2","633498,633498,19,1","633499,633499,18,1","676700,676700,16,0","676701,676701,19,1","676702,676702,16,0","676703,676703,18,1","676704,676704,16,0","676705,676705,19,1","676706,676707,16,2","676708,676711,16,0","676712,676715,16,0","676716,676717,16,0","676718,676718,19,1","676719,676739,16,0","676740,676740,18,1","676741,676749,16,0","676750,676762,19,1","676763,676769,16,0","676770,676770,19,1","676771,676773,16,0","676774,676774,18,1","676775,676778,16,0","676779,676779,18,1","676780,676781,16,0","676782,676782,18,1","676783,676794,16,0","676795,676795,18,1","676796,676797,16,0","676798,676798,19,1","676799,676799,16,0"];
  }

  for (var ndx = 0; ndx < thisRules.length; ndx++) {
    thisRule = thisRules[ndx];
    var ruleDetails = thisRule.split(",");
        
    var hiPrefix        = ruleDetails[0];
    var loPrefix        = ruleDetails[1];
    var valLength       = parseInt(ruleDetails[2]);
    var issueLength     = ruleDetails[3];
    var startDateLength = ruleDetails[4];

    var cardPrefix = cardNbr.substr(0, hiPrefix.length);
    if (cardPrefix >= hiPrefix && cardPrefix <= loPrefix && cardNbr.length == valLength)
      return true;
  }

  return false;
}

/*
* This function cheks the checksum of CreditCard
* @param cc - the string with credit card number
* @param accepted - the array of allowed CC types
* @param skip_cc_check - boolean for wildcarded card_number
* if(accepted==null) { all CC types are allowed }
*/
function isCreditCard(cc, accepted, skip_cc_check) {
  cc = String(cc);
  if (cc.length < 4 || cc.length > 30)
    return false; 

/*Return true if this is wildcarded card number like ************4565 */
  if (skip_cc_check && cc.match(/^\*+\d{4}$/g))
    return true;
    
/* Start the Mod10 checksum process... */
  var checksum = 0;

/* Add even digits in even length strings or odd digits in odd length strings. */
  for (var location = 1-(cc.length%2); location < cc.length; location += 2) {
    var digit = parseInt(cc.substring(location, location+1));
    if (isNaN(digit)) 
      return false; 
    checksum += digit;
  }

/* Analyze odd digits in even length strings or even digits in odd length strings. */
  for (var location = (cc.length%2); location < cc.length; location+=2) {
    var digit = parseInt(cc.substring(location,location+1));
    if (isNaN(digit)) 
      return false;
    if (digit < 5) 
      checksum += digit*2;
    else 
      checksum+=digit*2-9;
  }

  if (checksum % 10 != 0) 
    return false;

  if (accepted != null) {
    var checkPresent = false;
    var accepted_array = ["Diners Club", "American Express", "JCB", "Carte Blanche", "Visa", "MasterCard", "Australian BankCard", "Discover/Novus", "Switch", "Solo", "enRoute"];

    for (var i in accepted_array) {
      if (hasOwnProperty(accepted_array, i) && accepted[0] == accepted_array[i]) 
        checkPresent = true;
    }

      var type = "not";
    var accept = false;

    if (checkPresent == true) {
      var t = parseInt(cc.substring(0,4));
      var l = cc.length;
      
      if (t >= 3000 && t < 3060 && l == 14) 
        type="Diners Club";
      else if (isSwitchSolo("SW", cc.substring(0, cc.length))) 
        type = "Switch";
      else if (isSwitchSolo("SO", cc.substring(0, cc.length))) 
        type = "Solo"; 
      else if (t >= 3400 && t < 3500 && l == 15) 
        type = "American Express";
      else if ( t>= 3528 && t < 3590 && l == 16) 
        type = "JCB";
      else if (t >= 3600 && t < 3700 && l == 14) 
        type = "Diners Club";
      else if (t >= 3700 && t < 3800 && l == 15) 
        type = "American Express";
      else if (t >= 3800 && t < 3890 && l == 14) 
        type="Diners Club";
      else if (t >= 3890 && t < 3900 && l == 14) 
        type="Carte Blanche";
      else if (t >= 4000 && t < 5000 && (l == 13 || l == 16)) 
        type="Visa";
      else if (t >= 5100 && t < 5600 && l == 16) 
        type="MasterCard";
      else if (t == 5610 && l == 16) 
        type="Australian BankCard";
      else if (t == 6011 && l == 16) 
        type="Discover/Novus";
      else if (t == 2014 && l == 15) 
        type="enRoute";
      else if (t == 2149 && l == 15) 
        type="enRoute";
      else 
        type="not";  

/* accepted and recognized types are not equal */
    } else { 
/* we don't know this card's type so pass it as correct */
      return true;
    }

    return (accepted[0] == type);
  }
  
  return true;
}

/*
  Check CC number
*/
function checkCCNumber(field_cc, field_accepted, skip_cc_check) {

  if (!isset(skip_cc_check)) { var skip_cc_check = false; }
  
  var cc = field_cc.value;
  var accepted = (field_accepted != null) ? [card_types[field_accepted.value]] : null;

  if (isCreditCard(cc, accepted, skip_cc_check))
      return true;

  xAlert(txt_cc_number_invalid);
  field_cc.focus();
  field_cc.select();
  return false;
}

/*
  This function checks CVV2 field 
*/
function checkCVV2(cvv2, cc, skip_cc_check) {
  if (!isset(skip_cc_check)) { var skip_cc_check = false; }

  if(!cvv2 || !cc)
    return true;
  var num = cc.value;
  
  if (card_cvv2[num] == '' && !force_cvv2)
    return true; 
  cvv2 = cvv2.value;  
  cvv2 = String(cvv2);  

  var is_wildcard_cvv2 = (skip_cc_check && cvv2.search(/^\*+$/) != -1) ? true : false;

  if (cvv2.length == 0) {
    xAlert(lbl_cvv2_is_empty);
    return false;

  } else if (cvv2.length != 3 && cvv2.length != 4) {
    xAlert(lbl_cvv2_isnt_correct);
    return false;

  } else if (cvv2.search(/^\d+$/) == -1 && !is_wildcard_cvv2) {
    xAlert(lbl_cvv2_must_be_number);
    return false;
  }

  return true;
}

/*
* This function checks expiration CC date
*/
function checkExpirationDate(ed_month, ed_year) {
  var yy = ed_year ? parseInt(ed_year.value.replace(/^0/gi, "")) : current_year;
  var mm = ed_month ? parseInt(ed_month.value.replace(/^0/gi, "")) : current_month;

  if (yy < 1000)
    yy += 2000;
  if (yy < current_year || (yy == current_year && mm < current_month)) {
    xAlert(lbl_is_this_card_expired);
    return false;
  }

  return true;
}

/*
* Mark/Unmark CVV2 field
*/
function markCVV2(cc) {
  if (document.getElementById('cvv2_star') && cc)
    document.getElementById('cvv2_star').innerHTML = ((card_cvv2[cc.value] == '' && !force_cvv2) ? "&nbsp;" : "*");
}

