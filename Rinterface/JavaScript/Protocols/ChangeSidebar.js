function ChangeSidebar() {
  var type = checktype();
  var radioinput = checkradio();
  var checklist = checkbox();
  document.getElementById("sidebarCh").innerHTML = '<div style="text-align: center;">Нет данных</div>';
  document.getElementById("mainCh").innerHTML = '<div style="text-align: center;">Нет данных</div>';
  var url = DefineUrl(0, type, radioinput);
  sendQuery(url, function ServFunc(input) {AddBoxInput(ParseJSON(input), checklist, radioinput[0])});
}

function AddBoxInput(input, checklist, protype) {
  var boxItems = input[0].reverse();
  var values = input[1].reverse();
  var serBoxItems = [];
  var serValueItems = [];
  var service = [];
  var summary = [];
  var string = '<div>';
  var z;
  for (var i = 0; i < boxItems.length; i++) {
     summary[i] = [];
     string += '<input type="checkbox" name="days" ';
     string += 'value="' + i;
     if (checklist.indexOf(i) != - 1) {
        string += '" checked ';
     }
     string += '" onclick="ChangeSidebar()">' + boxItems[i][0] + '<br>'; 
     summary[i][0] = [];
     summary[i][0][0] = boxItems[i][0];
     summary[i][0][1] = values[i].length;
     summary[i][0][2] = 0;
     summary[i][0][3] = 0;
     summary[i][0][4] = 0;
     for (var j = 0; j < values[i].length; j++) {
        if (values[i][j][1] >=  "2") {
           summary[i][0][2]++;
        } else if (values[i][j][1] ==  "1") {
           summary[i][0][3]++;
        } else if (values[i][j][1] ==  "0") {
           summary[i][0][4]++;
        }
     }
     if (checklist.indexOf(i) != - 1) {
        summary[i][1] = [];
        serBoxItems = boxItems[i][1].reverse();
        serValueItems = values[i].reverse();
        counter = 0;
        for (var j = 0; j < serBoxItems.length; j++) {
           service = [];
           service[0] = serBoxItems[j][0];
           service[1] = 0;
           service[2] = 0;
           service[3] = 0;
           for (var z = counter; z < serValueItems.length; z++) {
              if (serValueItems[z][0] >= serBoxItems[j][1]) {
                 if (serValueItems[z][1] >=  "2") {
                    service[1]++;
                 } else if (serValueItems[z][1] ==  "1"){
                    service[2]++;
                 } else if (serValueItems[z][1] ==  "0"){
                    service[3]++;
                 }
                 counter++;
              }
           }           
           if ((service[1] + service[2]+ service[3]) > 0) {
              summary[i][1][summary[i][1].length] = service;
              string += '<li class="sidebar"><input type="radio" name="hours" ';
              string += 'value="' + serBoxItems[j][1];
              string += '" onclick="MainProcess()">  ' + serBoxItems[j][0] + '</li><br>';
           }

        }
     }
  }
  string += '<div id="zero"></div></div>';
  WriteTitleText(summary, protype);
  document.getElementById("sidebarCh").innerHTML = string;
}

function checkbox () {
  var output = [];
  var i = 0;
  var input = document.getElementsByName("days");
  for (var j = 0; j < input.length; j++) {
     if (input[j].type == "checkbox" && input[j].checked) {
        output[i] = j;
        i++;
     }
  }
  return output;
}