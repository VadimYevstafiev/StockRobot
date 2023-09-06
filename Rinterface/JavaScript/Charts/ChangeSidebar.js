function ChangeSidebar() {
  document.getElementById("sidebarCh").innerHTML = '<div style="text-align: center;">Нет данных</div>';
  document.getElementById("mainCh").innerHTML = '<div style="text-align: center;">Нет данных</div>';
  var type = checktype();
  var radioinput = checkradio();
  var url = DefineUrl(0, type, radioinput);
  sendQuery(url, function ServFunc(input) {AddBoxInput(ParseJSON(input))});
}

function AddBoxInput(input) {
  var boxItems = input[0];
  var values = input[1];
  var string = {};
  for (var i = 0; i < boxItems.length; i++) {
     string[i] = '<div>';
     for (var j = 0; j < boxItems[i].length; j++) {
        string[i] += '<input type="checkbox" name="charts' + (i + 1);
        string[i] += '" value="' + values[i][j];
        string[i] += '" checked ';
        string[i] += 'onclick="MainProcess()">' + boxItems[i][j] + '<br>';
     }
     string[i] += '<div id="zero"></div></div>';
  }
  var result = string[0];
  for (var i = 1; i < boxItems.length; i++) {
     result += string[i];
  }
  document.getElementById("sidebarCh").innerHTML = result;
  MainProcess();
}