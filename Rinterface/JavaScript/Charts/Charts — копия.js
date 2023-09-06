function AddCharts(radioinput, boxinput, data) {
  var str = '';
  var size;
  var basesize = 1700;
  var sizemod = [1, 2, 4];
  size = basesize * sizemod[radioinput[1]];
  for (var i = 0; i < boxinput.length; i++) {
     if (boxinput[i].length > 0) {
        if (size == 0) {
           str += '<div id="chart_div_' + i + '"></div>';
        } else {
           str += '<div id="chart_div_' + i + '" style="width:' + size + 'px;"></div>';
        }
     }
  }
  document.getElementById("mainCh").innerHTML = str;
  for (var i = 0; i < boxinput.length; i++) {
     if (boxinput[i].length > 0) {
        str = 'chart_div_' + i;
        createComboChart(radioinput[0], str, boxinput[i], data[0][i][0], data[0][i][1], data[0][i][2], data[1][i]);
     }
  }
}

function createComboChart (timeframe,namediv, chartsarrray, chartlist, seriesarray, axesarrray, dataarrray) {
  //chartsarrray - список графиков, выбранных в бококовой панели
  //chartlist    - список номеров графиков, данные которых переданы с сервера
  //seriesarray  - массивы опций графиков, данные которых переданы с сервера
  //axesarrray   - массив масштабов осей графиков, данные которых переданы с сервера
  google.charts.load('current', {'packages':['corechart']});
  var servicearray = [];
  var servicedata = [];
  var head = [];
  var servicenames = [];
  var servicevalue = [];
  var twoaxis = false;
  var secondaxesnumber = 0;
  var secondaxesscale = 0;
  var counter;

  switch (timeframe) {
     case '3':
        var everyvalue = 24;
        break;
     case '2':
        var everyvalue = 3;
        break;
     case '1':
        var everyvalue = 12;
        break;
     case '0':
        var everyvalue = 24;
        break;
  }
  //Создаем массив имен колонок и определяем максимальный масштаб второй оси
  servicearray[0] = createColumn('domain', seriesarray[0].columnname);

  for (var i = 0; i < chartsarrray.length; i++) { 
     servicenames[i] = seriesarray[Number(chartsarrray[i])].columnname;
     if (Array.isArray(servicenames[i])) {
         for (var j = 0; j < servicenames[i].length; j++) {
            servicearray = servicearray.concat(createColumn('data', servicenames[i][j]));
         }
     } else {
        servicearray = servicearray.concat(createColumn('data', servicenames[i]));
     }
     servicearray = servicearray.concat(createColumn('tooltip', servicenames[i]));

     //Если текущий график в боковой панели строится по второй оси
     //chartsarrray = ["1","2, 3","4","5"]
     //chartlist = [0,1,1,1,1,2,3,4,5]
     if (axesarrray[Number(chartsarrray[i])] != 0) {
        counter = 0;
         //Если текущий график в боковой панели строится по второй оси
        for (var j = 0; j < (i + 1); j++) {
           for (var z = 0; z < chartlist.length; z++) {
              if (chartlist[z] == chartsarrray[j]) {
                 counter++;
              }
           }
           counter++;
        }
        if (axesarrray[Number(chartsarrray[i])] > secondaxesscale) {
           secondaxesnumber = counter;
           secondaxesscale = axesarrray[Number(chartsarrray[i])];
        }
     }
  }
  if ((secondaxesnumber != 0) && (chartsarrray.length > 1)) {
     for (var i = 0; i < chartsarrray.length; i++) {
        if (axesarrray[Number(chartsarrray[i])] == 0) {
           seriesarray[Number(chartsarrray[i])].targetAxisIndex = 0;
        } else {
           seriesarray[Number(chartsarrray[i])].targetAxisIndex = 1;
        }
     }
  }
  head = servicearray.slice();
  servicearray.length = 0;
  for (var i = 0; i < dataarrray.length; i++) {
     servicearray[0] = dataarrray[i][0];
     for (var j = 0; j < chartsarrray.length; j++) {
        for (var k = 0; k < chartlist.length; k++) {
           if (chartlist[k] == chartsarrray[j]){
              servicevalue = servicevalue.concat(Number(dataarrray[i][k]));
           }
        }
        servicearray = servicearray.concat(servicevalue);
        servicearray = servicearray.concat(createCustomHTMLContent(servicearray[0], servicenames[j], servicevalue));
        servicevalue.length=0;
     }
     servicedata.push(servicearray.concat());
     servicearray.length=0;
  }
  google.charts.setOnLoadCallback(drawVisualization);
  function drawVisualization() {
     var data = new google.visualization.DataTable();
     for (var i = 0; i < head.length; i++) {
        data.addColumn(head[i]);
     }
     data.addRows(servicedata);

     var options = {};
     options.legend = 'none';
     options.chartArea = {left:20,top:20,width:'100%',height:'85%'};
     options.seriesType = seriesarray[Number(chartsarrray[0])].type; 
     options.tooltip = {isHtml: true };
     options.hAxis = {
        showTextEvery: everyvalue,
        slantedText: false,
        maxAlternation: 1,
        textStyle: {fontSize: 14},
     };
     if (secondaxesnumber != 0) {
        if (chartsarrray.length > 1) {
           var mxValue = data.getColumnRange(secondaxesnumber).max * secondaxesscale;
           twoaxis = true;
           options.vAxes = {};
           options.vAxes[0] = new VAobject(true, twoaxis);
           options.vAxes[1] = new VAobject(false, twoaxis, mxValue);
           options.vAxes[1].baseline = 0;
        } else {
           options.vAxis = new VAobject(false, twoaxis);
        }
     } else {
        options.vAxis = new VAobject(true, twoaxis);
     };
     options.series = {};
     for (var i = 0; i < chartsarrray.length; i++) {
        options.series[i] = extend(seriesarray[Number(chartsarrray[i])]);
        delete options.series[i].columnname;           
     }
     var chart = new google.visualization.ComboChart(document.getElementById(namediv));
     chart.draw(data, options);
  }


  function createColumn(role, columnname) {
     switch (role) {
        case 'domain':
           var output = {type : 'string', label : columnname};
           break;
        case 'data':
           var output = {type : 'number', label : columnname};
           break;
        case 'tooltip':
           var output = {type : 'string', 'role': 'tooltip', 'p': {'html': true}};
           break;
     }
     return output;
  }

  function createCustomHTMLContent(time, name, value) {
     var output =  '<div style="padding:5px 5px 5px 5px;">' +
     '<table><tr><td>Время:</td><td>' + time + '</td></tr>';
     if (Array.isArray(name)) {
        for (var i = 0; i < name.length; i++) {
           output += '<tr><td>' + name[i] + ':</td><td>' + value[i] + '</td></tr>';
        }
     } else {
        output += '<tr><td>' + name + ':</td><td>' + value[0] + '</td></tr>';
     }  
     output += '</tr></table></div>';
     return output;
  }

  function extend (original) {
     var copy = {};
     for (key in original){
        copy[key]=original[key];
     }
     return copy;
  }

  function VAobject (mainAxis, twoaxis, mValue) {
     if ((!mainAxis) && (twoaxis) && (mValue !== "undefined")) {
        this.maxValue = mValue;
     }
     this.textPosition = 'none';
  }

}