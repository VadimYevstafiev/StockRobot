function ChangeSelect() {
  AddSelectInput();
  ChangeSidebar();
}

function checktype() {
  var output = [];
  output[0] = checkselect("id");
  output[1] = checkselect("para");
  return output;
}

function checkselect(name) {
  var input = document.getElementById(name);
  var i = input.options.selectedIndex;
  var output = input.options[i].value;
  return output;
}

function AddSelectInput() {
  var id = checkselect("id");
  var string = 'Торговая пара:';
  string += '                             <select id="para" size="1"  onchange="ChangeSidebar()">';
  switch (id) {
     case "bf":
        string += AddSelectOptions(["btcusd", "ltcusd"]);
        break;
     case "wm":
        string += AddSelectOptions(["WmzWmr", "WmzWme", "WmeWmr", "WmzWmx", "WmrWmx"]);
        break;
  }
 string += '                             </select>';
  document.getElementById("select").innerHTML = string;
}

function AddSelectOptions(input) {
  var output = '';
  for (var i = 0; i < input.length; i++) {
     output += '                                <option ';
     if (i ==  0) {
        output += 'selected ';
     }
     output += 'value="' + input[i] + '">'+ input[i] + '</option>';
  }
  return output;
}