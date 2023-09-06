function MainProcess() {
  var type = checktype();
  var radioinput = checkradio();
  document.getElementById("mainCh").innerHTML = '<div style="text-align: center;">Нет данных</div>';
  if (radioinput.length == 3) {
     var url = DefineUrl(2, type, radioinput);
     sendQuery(url, AddText);
  } else {
     var url = DefineUrl(1, type, radioinput);
     sendQuery(url, function ServFunc(input) {WriteTable(ParseJSON(input), radioinput[0])});
  }
}

function checkradio() {
  var output = [];
  var str = ["protype", "hours", "texts"];
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

function WriteTitleText(input, protype) {
  var output = '';
  var variable = DefineVariable(protype);
  var values;
  for (var i = 0; i < input.length; i++) {
     output += '<div><h3>' + input[i][0][0] + '</h3>';
     output += '<p> Количество запусков процедуры обновления данных ' + variable + ': <b>' + input[i][0][1] + '</b></p>';
     output += '<p> Из них: </p>';
     output += '<p> Успешных запусков: <b style="color:#0000ff">' + input[i][0][2] + '</b></p>';
     output += '<p> Неудачных запусков: <b style="color:#ff0000">' + input[i][0][4] + '</b></p>';
     if (input[i].length > 1) {
        output += '<p> В том числе: </p>';
        output += '<table><tr><td>';
        output += '<table id = "protocolstable" align="left">';
        output += '<tr><th>Период времени</th><th>Успешно</th><th>Выполняется</th><th>Неудачно</th></tr>';
        for (var j = 0; j < input[i][1].length; j++) {
           values = input[i][1][j];
           output += '<tr><td>' + values[0] + '</td>';
           for (var z = 1; z < values.length; z++) {
              if (values[z] == 0) {
                 output += '<td> - </td>';
              } else {
                 output += '<td><b>' + values[z] + '</b></td>';
              }
           }
           output += '</tr>';
        }  
        output += '</td></tr></table>';
        output += '</table>'
     }
     output += '</div>';
  }
  document.getElementById("mainCh").innerHTML = output;
}

function WriteTable(input, protype) {
  var string = '';
  var variable = DefineVariable(protype);
  if (input.length == 1) {
     string += '<h3>' + input[0][0] + '. Период времени: '+ input[0][1] + '</h3>';
     string += '<p>В указанный период времени процедура ' + variable + ' не запускалась</p>';
  } else {
     string += '<table id = "protocolstable"><caption>';
     string += '<h3>' + input[0][0] + '. Период времени: '+ input[0][1] + '</h3></caption>';
     string += '<tr><th>Время запуска</th><th>Результат запуска</th><th>Текст протокола</th></tr>';
     var values = input[1].reverse();
     for (var i = 0; i < values.length; i++) {
        string += '<tr><td>' + values[i][0] + '</td>';
        if (protype == "2") {
           switch (values[i][1]) {
              case "6":
                 string += '<td><b style="color:#560319">Процедура ' + variable + ' выполнена успешно<br>';
                 string += 'Закрытие активной заявки в режиме тейкера</b></td>';
                 break;
              case "5":
                 string += '<td><b style="color:#560319">Процедура ' + variable + ' выполнена успешно<br>';
                 string += 'Закрытие активной заявки в режиме мейкера</b></td>';
                 break;
              case "4":
                 string += '<td><b style="color:#556832">Процедура ' + variable + ' выполнена успешно<br>';
                 string += 'Активная заявка в режиме удержания позиции</b></td>';
                 break;
              case "3":
                 string += '<td><b style="color:#556832">Процедура ' + variable + ' выполнена успешно<br>';
                 string += 'Активная заявка в режиме ожидания позиции</b></td>';
                 break;
              case "2":
                 string += '<td><b style="color:#0000ff">Процедура ' + variable + ' выполнена успешно<br>';
                 string += 'Активных заявок нет</b></td>';
                 break;
              case "1":
                 string += '<td><b style="color:#ffff00">Процедура ' + variable + ' выполняется</b></td>';
                 break;
              case "0":
                 string += '<td><b style="color:#ff0000">Не удалось выполнить процедуру ' + variable + '</b></td>';
                 break;
           }
        } else {
           switch (values[i][1]) {
              case "2":
                 string += '<td><b style="color:#0000ff">Процедура ' + variable + ' выполнена успешно</b></td>';
                 break;
              case "1":
                 string += '<td><b style="color:#ffff00">Процедура ' + variable + ' выполняется</b></td>';
                 break;
              case "0":
                 string += '<td><b style="color:#ff0000">Не удалось выполнить процедуру ' + variable + '</b></td>';
                 break;
           }
        }

        string += '<td><input type="radio" name="texts" ';
        string += 'value="' + values[i][2];
        string += '" onclick="MainProcess()">Открыть протокол</td></tr>';
     }
     string += '</table>';
  }
  document.getElementById("mainCh").innerHTML = string;
}

function DefineVariable(input) {
  var output;
  switch (input) {
     case "0":
        output = 'обновления данных слушателя';
        break;
     case "1":
        output = 'обновления данных эксперта';
        break;
     case "2":
        output = 'торгов';
        break;
     case "3":
        output = 'обновления данных комплектатора интерфейса';
        break;
  }
  return output;
}

function DefineUrl(datatype, type, radioinput) {
  var output = "http://kvartquest.info/Rinterface/SendProtocolData.php?id=" + type[0];
  output += "&para=" + type[1] +"&data=" + datatype + "&protype=" + radioinput[0];
  switch (datatype) {
     case 0:
        break;
     case 1:
        output += "&time=" + radioinput[1];
        break;
     case 2:
        output += "&time=" + radioinput[2];
        break;
  }
  return output;
}