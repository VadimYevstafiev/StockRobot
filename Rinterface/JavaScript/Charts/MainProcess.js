function MainProcess() {
  document.getElementById("mainCh").innerHTML = '<div style="text-align: center;">Нет данных</div>';
  var type = checktype();
  var radioinput = checkradio();
  var url = DefineUrl(1, type, radioinput);
  sendQuery(url, function ServFunc(input) {
                            var boxinput = checkbox();
                            AddCharts(radioinput, boxinput, ParseJSON(input));});
}

function checkradio() {
  var output = [];
  var str = ["timeframe", "period", "type"];
  for (var i = 0; i < str.length; i++) {
     var input = document.getElementsByName(str[i]);
     for (var j = 0; j < input.length; j++) {
        if (input[j].type == "radio" && input[j].checked) {
           output[i] = input[j].value
        }
     }
  }
  return output;
}

function checkbox () {
  var output = [];
  var str;
  for (var i = 0; i < 4; i++) {
     output[i] = [];
     str = 'charts' + (i + 1);
     var input = document.getElementsByName(str);
     for (var j = 0; j < input.length; j++) {
        if (input[j].type == "checkbox" && input[j].checked) {
           output[i] = output[i].concat(input[j].value.split(', '));
        }
     }
  }
  return output;
}

function DefineUrl(datatype, type, radioinput) {
  var output = "http://kvartquest.info/Rinterface/SendChartsData.php?id=" + type[0];
  output += "&para=" + type[1] +"&data=" + datatype;
  output += "&time=" + radioinput[0] + "&type=" + radioinput[2] + "&period=" + radioinput[1];
  return output;
}