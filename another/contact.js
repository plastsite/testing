// JS code for Contact Form - http://coursesweb.net/php-mysql/
var cform = document.getElementById('cform');       // get the form
var csubmit = document.getElementById('csubmit');   // get the submit button

// The function that create the anti-spamm code. From a number in milliseconds, taken from the current date
function set_codas() {
  var data = new Date();
  var mili_s = (data.getMilliseconds()>10) ? data.getMilliseconds() : 12;
  var re = mili_s.toString()+Math.ceil(mili_s/11);

  // Add the code in the hidden form field and in the visible text, and remove it from text field
  document.getElementById('anti_spam').value = re;
  document.getElementById('codas').innerHTML = re;
  document.getElementById('anti_spam1').value = '';

  return re;
}

// Function that take and check form data
function Validate() {
  // takes data from each field
  var file_php = cform.action;
  file_php = file_php.split('/').pop();	  	// takes the name of the php file
  var nume = cform.nume.value;
  var email = cform.email.value;
  var subject = cform.subject.value;
  var message = cform.message.value;
  var cod_as = cform.anti_spam.value;
  var cod_as1 = cform.anti_spam1.value;

  // Validate the fields data
  var regx_mail = /^([a-zA-Z0-9]+[a-zA-Z0-9._%-]*@([a-zA-Z0-9-]+\.)+[a-zA-Z]{2,4})$/;    // RegExp for e-mail address
  if (nume.length<2 || nume.length>40) {
    alert('Name must contain between 2 and 40 characters');
    cform.nume.select();
  }
  else if (email.search(regx_mail)==-1){
    alert('Add a valid e-mail address');
    cform.email.select();
  }
  else if (subject.length<3 || subject.length>70) {
    alert('Add a subject, between 3 and 70 characters');
    cform.subject.select();
  }
  else if (message.length<5 || message.length>500) {
    alert('Add a message, between 5 and 500 characters');
    cform.message.select();
  }
  else if (cod_as!=cod_as1) {
    alert('Incorrect verification code');
    cform.anti_spam1.select();
  }
  else {
    // add 'Sending...' notification in Send button, and disable it
    csubmit.value = 'Sending...';
    csubmit.setAttribute("disabled", "disabled");

    // Define a string witth data to be sent to ajax function
    var  datele = 'nume='+nume+'&email='+email+'&subject='+subject+'&message='+message+'&anti_spam='+cod_as+'&anti_spam1='+cod_as1;
    set_codas();          // Access the function to update the anti-spamm code
    ajaxrequest(file_php, datele);    // calls ajaxrequest()
  }

  return false;
}

// function that returns the XMLHttpRequest object according to browser
function get_XmlHttp() {
  // the variable that will contain the instance of the XMLHttpRequest object (initially with null value)
  var xmlHttp = null;

  if(window.XMLHttpRequest) {    // For Forefox, Opera, Safari, ...
    xmlHttp = new XMLHttpRequest();
  }
  else if(window.ActiveXObject) {  // For Internet Explorer
    xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
  }

  return xmlHttp;
}

// Ajax function, send data to a php file and display the server response
function ajaxrequest(php_file, datele) {
  var cerere_http =  get_XmlHttp();    // calls the function for the XMLHttpRequest object

  cerere_http.open("POST", php_file, true);      // Create the request

  // adds  a header to tell the PHP script to recognize the data as is sent via POST
  cerere_http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  cerere_http.send(datele);    // calls the send() method, and send data in parameter

    // Check request status. If the response is received completely, will be returned
  cerere_http.onreadystatechange = function() {
    if (cerere_http.readyState == 4) {
      // If the php script response contains "Error:", display it in the title of the form
      // Otherwise, it displays instead form
      if(cerere_http.responseText.indexOf("Error:")!=-1) {
        // add 'Send' value in submit button button, and remove disabled
        csubmit.value = 'Send';
        csubmit.removeAttribute("disabled");
        document.getElementById('fc_titlu').innerHTML = cerere_http.responseText;
      }
      else {
        cform.innerHTML = cerere_http.responseText;
      }
    }
  }
  return false;
}

cform.onsubmit = function () {return Validate();};           // register "onsubmit" event at form
var cod_as = set_codas();		// Access the function that create the anti-spamm code