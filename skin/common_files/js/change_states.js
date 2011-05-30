/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Change states depending on selected country
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    $Id: change_states.js,v 1.6 2010/07/26 12:21:16 igoryan Exp $
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

/**
 * Initialization procedure
 */
function init_js_states(obj, state_name, county_name, country_id, force_run) {
  if (!obj || typeof(state_name) != 'string' || !state_name || !obj.form.elements.namedItem(state_name))
    return false;

  state_value = document.getElementById(country_id + "_state_value").value;
  county_value = document.getElementById(country_id + "_county_value").value;

  /* Get child objects  */
  obj.states = obj.form.elements.namedItem(state_name);
  obj.counties = (typeof(county_name) == 'string' && county_name) ? obj.form.elements.namedItem(county_name) : false;

  obj.lastCode = false;
  obj.lastStateCode = false;
  obj.defaultStates = [];
  if (obj.counties)
    obj.defaultCounties = [];

  /* Get default values  */
  var code = config_default_country;
  if ((obj.selectedIndex >= 0) && (obj.selectedIndex < obj.options.length)) {
    code = obj.options[obj.selectedIndex].value;
  }
  if (countries[code] && countries[code].states && countries[code].states.length > 0) {
    for (var i in countries[code].states) {
      if (!hasOwnProperty(countries[code].states, i) || countries[code].states[i].code != state_value)
        continue;

      /* Save default state  */
      obj.defaultStates[code] = i;
      if (obj.counties && countries[code].states[i].counties && countries[code].states[i].counties.length > 0) {
        for (var x in countries[code].states[i].counties) {
          if (hasOwnProperty(countries[code].states[i].counties, x) && x == county_value) {

            /* Save default county  */
            obj.defaultCounties[i] = x;
            break;
          }
        }
      }
      break;
    }
  }

  /* Save state and/or county full name  */
  if (!obj.defaultStates[code]) {
    obj.defaultStateFull = state_value;
    obj.defaultCountyFull = county_value;
  } else if (obj.counties && !obj.defaultCounties[obj.defaultStates[code]]) {
    obj.defaultCountyFull = county_value;
  }

  obj.states.countries = obj;
  obj.statesName = state_name;
  if (obj.counties) {
    obj.counties.countries = obj;
    obj.countiesName = county_name;
  }

  /* Define handler for onchange events  */
  if (obj.onchange)
    obj.oldOnchange = obj.onchange;
  obj.onchange = change_states;

  if (obj.states.onchange)  
    obj.oldStatesOnchange = obj.states.onchange;
  obj.states.onchange = change_counties;

  if (obj.counties && obj.counties.onchange) {
    obj.oldCountiesOnchange = obj.counties.onchange;
  }

  /* Object settings  */
  obj.statesInputSize = 32;
  obj.statesInputMaxLength = false;
  obj.statesSpanClass = "SmallText";
  obj.statesSpanStyle = false;
  obj.statesSelectClass = false;
  obj.statesSelectStyle = false;
  obj.statesInputClass = 'input-style';
  obj.statesInputStyle = false;
  obj.statesNoStates = window.txt_no_states ? txt_no_states : false;
  obj.countiesNoCounties = window.txt_no_counties ? txt_no_counties : false;

  if (force_run || $(obj).parents('.popup-dialog').length) {
    check_states_visibility(document.getElementById(obj.id)); 
    start_js_states(document.getElementById(obj.id));

  } else if (window.addEventListener) {
    window.addEventListener("load", new Function('', "check_states_visibility(document.getElementById('"+obj.id+"')); start_js_states(document.getElementById('"+obj.id+"'));"), false);

  } else if (window.attachEvent) {
    window.attachEvent("onload", new Function('', "check_states_visibility(document.getElementById('"+obj.id+"')); start_js_states(document.getElementById('"+obj.id+"'))"));

  } else if (localBFamily != 'MSIE' || (localPlatform != 'MacPPC' && localPlatform != 'Mac')) {
    if (window.onload)
      window.oldOnload = window.onload;
    window.onload = new Function('', "if (this.oldOnload) this.oldOnload(); start_js_states(document.getElementById('"+obj.id+"'))");

  } else {
    setTimeout("check_states_visibility(document.getElementById('"+obj.id+"')); start_js_states(document.getElementById('"+obj.id+"'))", 3000);

  }

  check_countries(obj);

  return obj;
}

if (!window.states_visibility_blockers)
  var states_visibility_blockers = [];

function check_states_visibility(obj) {
  if (localBFamily != 'Opera' || !obj.id)
    return;

  var p = obj;
  states_visibility_blockers[obj.id] = {
    list: [],
    to: false,
    func: new Function('', 'restart_states("'+obj.id+'");')
  };

  while (p && p.parentNode) {
    if (p.style.display == 'none' || p.style.visibility == 'hidden')
      states_visibility_blockers[obj.id].list.push(p);

    p = p.parentNode;
  }

  if (states_visibility_blockers[obj.id].list.length > 0)
    states_visibility_blockers[obj.id].to = setTimeout(states_visibility_blockers[obj.id].func, 400);

}

function restart_states(id) {
  if (!states_visibility_blockers[id] || states_visibility_blockers[id].list.length == 0)
    return;

  for (var i = 0; i < states_visibility_blockers[id].list.length; i++)  {
    var o = states_visibility_blockers[id].list[i];
    if (o.style.display == 'none' || o.style.visibility == 'hidden') {
      states_visibility_blockers[id].to = setTimeout(states_visibility_blockers[id].func, 400);
      return;
    }
  }

  start_js_states(document.getElementById(id));
  states_visibility_blockers[id] = false;
}

/*
  Initial object run
*/
function start_js_states(obj) {
  if (obj && obj.onchange) {
    if (localBFamily == 'Opera') {
      var p = obj;
      while (p.parentNode) {
        if (p.style.display == 'none') {
          return;
        }
        p = p.parentNode;
      }
    }

    $(obj).trigger('onchange');
  }
}

/*
  Change states list
*/
function change_states() {
  var code = config_default_country;
  if ((this.options.length > 0) && (this.selectedIndex < this.options.length)) {
    code = this.options[this.selectedIndex].value;
  }

  /* Detect input box type and get default value  */
  if (this.states.tagName.toUpperCase() == 'SPAN') {
    var type = false;

  } else if (this.states.tagName.toUpperCase() == 'SELECT') {
    var type = 'S';
    if (this.lastCode && countries[this.lastCode] && countries[this.lastCode].states && countries[this.lastCode].states.length > 0) {
      for (var i in countries[this.lastCode].states) {
        if (hasOwnProperty(countries[this.lastCode].states, i) && countries[this.lastCode].states[i].code == this.states.options[this.states.selectedIndex].value) {
          this.defaultStates[this.lastCode] = i;
          break;
        }
      }
    }

  } else if (this.states.tagName.toUpperCase() == 'INPUT') {
    var type = 'I';
    this.defaultStateFull = this.states.value;

  } else {
    return true;
  }

  if ((countries[code] == null) || (countries[code].states === false)) {
    /* If current country hasn't any states  */
    if (type !== false) {
      this.states = tag_replace(this.states, "SPAN");
      if (this.statesNoStates)
        this.states.innerHTML = this.statesNoStates;
      if (this.statesSpanClass)
        this.states.className = this.statesSpanClass;
      this.states.countries = this;
      this.states.onchange = change_counties;
    }

  } else if (countries[code].states.length == 0) {
    /* If current country has empty states list  */
    if (type != "I") {
      this.states = tag_replace(this.states, "INPUT", this.statesName);
      if (this.statesInputSize)
        this.states.size = this.statesInputSize;
      if (this.statesInputMaxLength)
        this.states.maxLength = this.statesInputMaxLength;
      if (this.statesInputClass)
        this.states.className = this.statesInputClass;
      this.states.countries = this;
      this.states.onchange = change_counties;
      this.states.oldOnchange = this.oldStatesOnchange;
    }
    
    if (this.defaultStateFull)
      this.states.value = this.defaultStateFull;

  } else if (countries[code].states.length > 0) {
    /* If current country has states list  */
    if (type != "S") {
      this.states = tag_replace(this.states, "SELECT", this.statesName);
      if (this.statesSelectClass)
        this.states.className = this.statesSelectClass;
      this.states.countries = this;
      this.states.onchange = change_counties;
      this.states.oldOnchange = this.oldStatesOnchange;

    }

    /* States list cleaning  */
    if ((this.lastCode != code || states_sort_override) && type == 'S') {
      while (this.states.options.length > 0)
        this.states.options[this.states.options.length-1] = null;
    }

    if (type != 'S' || this.lastCode != code) {
      /* Fill states list  */
      if (states_sort_override) {

        /* Sort  */
        var tmp = [];
        for (var j in countries[code].statesHash) {
          var i = countries[code].statesHash[j];

          if (hasOwnProperty(countries[code].states, i)) {
            tmp[i] = {
              name: countries[code].states[i].name,
              code: countries[code].states[i].code,
              order: countries[code].states[i].order,
              idx: i
            };
          }
        }
        tmp.sort(sort_states);

        /* Fill list  */
        for (var i in tmp) {
          if (!hasOwnProperty(tmp, i) || !tmp[i])
            continue;

          this.states.options[this.states.options.length] = new Option(tmp[i].name, tmp[i].code);
          if (this.defaultStates[code] == tmp[i].idx) {
            this.states.options[this.states.options.length-1].selected = true;
            this.states.selectedIndex = this.states.options.length-1;
          }
        }

        /* Set default state  */
      } else {
        for (var j in countries[code].statesHash) {
          var i = countries[code].statesHash[j];

          if (!hasOwnProperty(countries[code].states, i))
            continue;

          this.states.options[this.states.options.length] = new Option(countries[code].states[i].name, countries[code].states[i].code);
          if (this.defaultStates[code] == i)
            this.states.options[this.states.options.length-1].selected = true;
        }
      }

    } else if (this.defaultStates[code] && countries[code].states[this.defaultStates[code]]) {
      /* Set default state  */
      for (var i = 0; i < this.states.options.length; i++) {
        if (this.states.options[i].value == countries[code].states[this.defaultStates[code]].code) {
          this.states.options[i].selected = true;
          this.states.selectedIndex = i;
          break;
        }
      }    

    } else {
      this.states.options[0].selected = true;
      this.states.selectedIndex = 0;
    }
  }

  /* Call old onchange event handler  */
  if (this.oldOnchange) {
    $(this).trigger('oldOnchange');
  }

  /* Call counties rebuild procedure  */
  if (this.states.onchange)
    this.states.onchange();

  this.oldSelectedIndex = this.selectedIndex;

  this.lastCode = code;

}

/*
  Change counties list
*/
function change_counties() {
  if (!this.countries || !this.countries.counties)
    return true;

  var counties = this.countries.counties;
  var code = this.countries.options[this.countries.selectedIndex].value;
  var scode = false;
  if (this.options && countries[code].states && countries[code].states.length > 0 && this.options[this.selectedIndex]) {
    for (var i in countries[code].states) {
      if (hasOwnProperty(countries[code].states, i) && countries[code].states[i].code == this.options[this.selectedIndex].value) {
        scode = i;
        break;
      }
    }

  } else if (this.tagName.toUpperCase() == "INPUT") {
    scode = 0;
  }

  /* Detect input box type and get default value  */
  if (counties.tagName.toUpperCase() == 'SPAN') {
    var type = false;

  } else if (counties.tagName.toUpperCase() == 'SELECT') {
    var type = 'S';
    if (this.countries.lastCode && this.countries.lastStateCode && countries[this.countries.lastCode] && countries[this.countries.lastCode].states && countries[this.countries.lastCode].states[this.countries.lastStateCode] && countries[this.countries.lastCode].states[this.countries.lastStateCode].counties && countries[this.countries.lastCode].states[this.countries.lastStateCode].counties.length > 0) {
      for (var i in countries[this.countries.lastCode].states[this.countries.lastStateCode].counties) {
        if (hasOwnProperty(countries[this.countries.lastCode].states[this.countries.lastStateCode].counties, i) && i == counties.options[counties.selectedIndex].value) {
          this.countries.defaultCounties[this.countries.lastStateCode] = i;
          break;
        }
      }
    }

  } else if (counties.tagName.toUpperCase() == 'INPUT') {
    var type = 'I';
    this.countries.defaultCountyFull = counties.value;

  } else {
    return true;
  }

  if (scode === false) {
    /* If current country hasn't any states  and counties  */
    if (type !== false) {
      this.countries.counties = counties = tag_replace(counties, "SPAN");
      if (this.countries.countiesNoCounties)
        counties.innerHTML = this.countries.countiesNoCounties;
      if (this.countries.statesSpanClass)
        counties.className = this.countries.statesSpanClass;
    }

  } else if (!countries[code].states[scode] || countries[code].states[scode].counties.length == 0) {
    /* If current country hasn't states  or current state hasn't counties list */
    if (type != "I") {
      this.countries.counties = counties = tag_replace(counties, "INPUT", this.countries.countiesName);
      if (this.countries.statesInputSize)
        counties.size = this.countries.statesInputSize;
      if (this.countries.statesInputMaxLength)
        counties.maxLength = this.countries.statesInputMaxLength;
      if (this.countries.statesInputClass)
        counties.className = this.countries.statesInputClass;
      if (this.countries.oldCountiesOnchange)
        counties.onchange = this.countries.oldCountiesOnchange;
    }
    if (this.countries.defaultCountyFull)
      counties.value = this.countries.defaultCountyFull;

  } else if (countries[code].states[scode].counties.length > 0) {
    /* If current state has counties list  */
    if (type != "S") {
      this.countries.counties = counties = tag_replace(counties, "SELECT", this.countries.countiesName);
      if (this.countries.statesSelectClass)
        counties.className = this.countries.statesSelectClass;
      if (this.countries.oldCountiesOnchange)
        counties.onchange = this.countries.oldCountiesOnchange;
    }

    /* Clear old counties list  */
    if ((this.countries.lastStateCode != scode || states_sort_override) && type == 'S') {
      while (counties.options.length > 0)
        counties.options[0] = null;
    }

    /* Fill counties list  */
    if (this.countries.lastStateCode != scode || type != 'S') {

    if (states_sort_override) {

      /* Sort  */
      var tmp = [];
      for (var i in countries[code].states[scode].counties) {
        if (hasOwnProperty(countries[code].states[scode].counties, i)) {
          tmp[i] = {
            name: countries[code].states[scode].counties[i].name,
            order: countries[code].states[scode].counties[i].order,
            idx: i
          };
        }
      }
      tmp.sort(sort_states);

      /* Fill list  */
      for (var i in tmp) {
        if (!hasOwnProperty(tmp, i) || !tmp[i])
          continue;
        counties.options[counties.options.length] = new Option(tmp[i].name, tmp[i].idx);
        if (this.countries.defaultCounties[scode] == tmp[i].idx) {
          counties.options[counties.options.length-1].selected = true;
          counties.selectedIndex = counties.options.length-1;
        }
      }

    } else {
      for (var i in countries[code].states[scode].counties) {
        if (!hasOwnProperty(countries[code].states[scode].counties, i))
          continue;

        counties.options[counties.options.length] = new Option(countries[code].states[scode].counties[i].name, i);
        if (this.countries.defaultCounties[scode] == i)
          counties.options[counties.options.length-1].selected = true;
      }
    }

    }
  }

  this.countries.lastStateCode = scode;

  if (this.oldOnchange)
    this.oldOnchange();  

  if (counties.onchange)
    counties.onchange();
}

/*
  Replace tag by tag
*/
function tag_replace(obj, tag, tname) {
  var tmp = obj.parentNode.insertBefore(document.createElement(tag), obj);
  obj.parentNode.removeChild(obj);
  if (obj.name)
    obj.name = '';
  delete obj;
  if (tname)
    tmp.id = tmp.name = tname;
  return tmp;
}

/*
  Sort states and counties (Opera, Safari fix)
*/
function sort_states(a, b) {
  if (!a || !b || !a.order || !b.order || a.order == b.order)
    return 0;
  return a.order > b.order ? 1 : -1;
}

/*
  Check countries list (for Google Toolbar Autofill functionality)
*/
function check_countries(obj) {
  if (!obj || !obj.id)
    return;

  if (isset(obj.oldSelectedIndex) && obj.oldSelectedIndex != obj.selectedIndex && obj.onchange)
    obj.onchange();

  obj.oldSelectedIndex = obj.selectedIndex;
  obj.changeStatesTO = setTimeout("check_countries(document.getElementById('"+obj.id+"'));", 1000);
}
