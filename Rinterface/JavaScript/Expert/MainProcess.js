function MainProcess() {
  var url = DefineUrl();
  sendQuery(url, AddText);
}

function AddText(input) {
  document.getElementById("main").innerHTML = WriteTitleText(ParseJSON(input));
}

function WriteTitleText(input) {
  var output = '<p> Текущая дата: '+ input[0] + '</p>';
  output += '<p> Текущий временной интервал: '+ input[1] + '</p>';
  output += '<p> Направление тренда: ';
  var str = ['<p> Покупка (тип 2): ', '<p> Продажа (тип 1): ', '<p> Стоп (тип '];
  switch (input[2]) {
     case '2':
        output += ' Вверх</p>';
        str[2] += ' 1): ';
        break;
     case '1':
        output += ' Ожидание вверх</p>';
        break;
     case '0':
        output += ' Флэт</p>';
        break;
     case '-1':
        output += ' Ожидание вниз</p>';
        break;
     case '-2':
        output += ' Вниз</p>';
        str[2] += ' 2): ';
        break;
  }
  for (var i = 0; i < str.length; i++) { 
     output += str[i];
     if (input[i + 3] != 'null') {
        output += input[i + 3];
     }
     output += '</p>';
  }
  return output;
}

function DefineUrl() {
  var output = "http://kvartquest.info/Rinterface/PHP/SendExpertData.php";
  return output;
}