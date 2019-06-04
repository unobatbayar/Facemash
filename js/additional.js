function goBack() {
    window.history.back()
  }
  
function rankedError() {
    alert("To take exam you must complete 10 practice games!");
  }

// "PHP - AJAX and MySQL", W3schools, 2018 Available at: [https://www.w3schools.com/php/php_ajax_database.asp]
// Accessed [2018 Dec 29]
//////////////////////////////////////////////////////////////////////////////////////////
function showHint(str) {
    if (str.length == 0) { 
        document.getElementById("output").innerHTML = "";
        return;
    } else {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("output").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET", "../include/search.php?user=" + str, true);
        xmlhttp.send();
    }
}

///////////////////////////////////////////////////////////////////////////////////////////

