var xhr = createQuery();

function createQuery() {
  var xhr;
  //Internet Explorer
  if (window.ActiveXObject) {
     try {
        xhr = new ActiveXObject("Microsoft.XMLHTTP");
     } catch (e) {
        xhr = false;
     }
  } else {
     try {
        xhr = new XMLHttpRequest();
     } catch (e) {
        xhr = false;
     }
  }
  if (!xhr) {
     alert("Ошибка при создании JSON-запроса");
  } else {
     return xhr;
  }
}

function sendQuery(url, UseResponse) {
  if (xhr.readyState == 4 || xhr.readyState == 0) {
     xhr.open('GET', url, true);
     xhr.onreadystatechange = function () {
        if (xhr.readyState == 4) {
           if (xhr.status == 200) {
              var response = xhr.responseText;
              UseResponse(response);
           } else {
              alert(xhr.status + ": " + xhr.statusText );
           }
        }
     };
     xhr.send();
  } else {
     setTimeout("sendQuery()", 1000);
  }
}

function ParseJSON(response) {
  response = response.replace(/[\u200B-\u200D\uFEFF]/g, "");
  var result = JSON.parse(response);
  return result;
}