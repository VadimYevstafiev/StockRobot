function ChangeSidebar() {
  var type = checktype();
  var radioinput = checkradio();
  document.getElementById("sidebarCh").innerHTML = '<div style="text-align: center;">Нет данных</div>';
  document.getElementById("mainCh").innerHTML = '<div style="text-align: center;">Нет данных</div>';
  if (radioinput[0] == 0) {
     MainProcess();
  } else {
     var url = DefineUrl(0, type, radioinput);
     sendQuery(url, function ServFunc(input) {AddBoxInput(ParseJSON(input))});
  }
}

function AddBoxInput(input) {
  var names = Object.values(input);
  var values = Object.keys(input);
  var string = '<div>';
  for (var i = 0; i < values.length; i++) {
     string += '<input type="radio" name="data" value="' + values[i] + '"';
     if (i == 0) {
        string += ' checked';
     }
     string += ' onclick="MainProcess()">' + names[i] + '<br>';
  }
  string += '<div id="zero"></div></div>';
  document.getElementById("sidebarCh").innerHTML = string;
  MainProcess();
}