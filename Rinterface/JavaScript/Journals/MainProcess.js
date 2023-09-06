function MainProcess() {
  var type = checktype();
  var radioinput = checkradio();
  if (radioinput.length == 3) {
     document.getElementById("mainCh").innerHTML = '<div style="text-align: center;">Нет данных</div>';
     var url = DefineUrl(2, type, radioinput);
     sendQuery(url, AddText);
  } else {
     document.getElementById("mainCh").innerHTML = '<div style="text-align: center;">Нет данных</div>';
     var url = DefineUrl(1, type, radioinput);
     sendQuery(url, function ServFunc(input) {WriteTable(ParseJSON(input), radioinput[0])});
  }
}

function checkradio() {
  var output = [];
  var str = ["protype", "data", "num"];
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

function AddText(input) {
  var string = '<input type="button" onclick="MainProcess()" value="Назад">';
  string += input;
  document.getElementById("mainCh").innerHTML = string;
}

function WriteTable(input, protype) {
  var str = '<table id = "protocolstable">';
  str += AddHead(input[0].size, input[0].head);
  for (key in input[1]) {
     var data = Object.values(input[1][key]);
     if (data.length > 0) {
        str += '<tr><td colspan="' + Object.keys(input[0].head).length;
        str += '" <div style="margin-left: 300;">' + input[0]["items"][key] + ' заявки</div></td></tr>';
        for (var i = 0; i < data.length; i++) {
           str += AddRow(input[0].size, data[i]);
        }
     }
  }
  str += '</table>';
  document.getElementById("mainCh").innerHTML = str;
}

function AddHead(size, input) {   
  var output = '<tr>';
  for (key in input) {
     output += '<th><div style="width:' + size[key] + 'px; font-size: small;">' + input[key] + '</div></th>';
  }
  output += '</tr>';
  return output;
}

function AddRow(size, input) {
  var output = '<tr>';
  var string;
  for (key in input) {
     if (key == "Legend") {
        string = '<input type="radio" name="num" value="' + input["BidID"];
        string += '" onclick="MainProcess()">Показать';
     } else {
        string = input[key];
     }
     output += '<td><div style="width:' + size[key] + 'px; font-size: small;">' + string + '</div></td>';
  }
  output += '</tr>';
  return output;
}

function DefineUrl(datatype, type, radioinput) {
  var output = "http://kvartquest.info/Rinterface/SendJournalsData.php?id=" + type[0];
  output += "&para=" + type[1] +"&data=" + datatype + "&protype=" + radioinput[0];
  if (radioinput[0] != 0) {
     switch (datatype) {
        case 0:
           break;
        case 1:
           output += "&type=" + radioinput[1];
           break;
        case 2:
           output += "&num=" + radioinput[2];
           break;
     }
  }
  return output;
}