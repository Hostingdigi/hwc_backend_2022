//window.history.forward();
var form = '';
var submitted = false;
var error = false;
var error_message = '';

/*

This function checks the empty space found in the filed and gives the
error message accordingly

*/
function onlymessage(message)
{
  
     error_message = error_message + "* " + message + "\n";
      error = true;
    
}


function check_empty(field_name,message)
{
  if (form.elements[field_name] && (form.elements[field_name].type != "hidden"))
  {

    var field_value = form.elements[field_name].value;
    
    var k
    k=replaceAll(field_value," ", "")
    
    if ((field_value == '') || (k=='') )
    {
     error_message = error_message + "* " + message + "\n";
      error = true;
    }
    
  }
}

function check_agree(field_name,message)
{
 //alert(window.document.form1.agree[1].value);
   if(window.document.form1.agree[1].checked==true)
   
   {  
      error_message = error_message + "* " + message + "\n";
      error = true;
    }
    
    
  
}


function check_value(field_name,field_value,message)
{
    if (field_name <= field_value) {
     	error_message = error_message + "* " + message + "\n";
      	error = true;
    }
}



/*

This function checks the Selected Index is empty or 0 gives the error message
 accordingly.

*/


function selected_index(field_name,message)
{
  if (form.elements[field_name] && (form.elements[field_name].type != "hidden"))
  {

    
    
    if (form.elements[field_name].selectedIndex==0)
    {
        
    
      error_message = error_message + "* " + message + "\n";
      error = true;
    }
    
  }
}




/*

This function checks the Selected value is None for particular category then it gives error message
 accordingly.

*/


function selected_value(field_name,message)
{
  if (form.elements[field_name] && (form.elements[field_name].type != "hidden"))
  {

    
    
    if (form.elements[field_name].value=="None")
    {
        
    
      error_message = error_message + "* " + message + "\n";
      error = true;
    }
    
  }
}




/*

This function checks if the checkbox is not selected then it gives the error message
 accordingly.

*/



function check_cbox(field_name,message)
{

    if (form.elements[field_name].checked==false)
    {

         error_message = error_message + "* " + message + "\n";

        error=true;
    }
}



/*

This function checks if the value given for the emailid column is valid or not.if it is not valid it gives the appropriate 
messgae
 

*/

function check_match(field_name1,field_name2,message)
{
    if (form.elements[field_name1].value != form.elements[field_name2].value) {
        error_message = error_message + "* " + message + "\n";
        error=true;
    }
}




function check_email(field_name,message)
{

 emailStr=form.elements[field_name].value;
 
        if (form.elements[field_name].value=="")
            {

               error_message = error_message + "* " + message + "\n";
                error=true;
            }
    else
    {
        
            var checkTLD=1;
            var knownDomsPat=/^(com|net|org|edu|int|mil|gov|arpa|biz|aero|name|coop|info|pro|museum)$/;
            var emailPat=/^(.+)@(.+)$/;
            var specialChars="\\(\\)><@,;:\\\\\\\"\\.\\[\\]";
            var validChars="\[^\\s" + specialChars + "\]";
            var quotedUser="(\"[^\"]*\")";
            var ipDomainPat=/^\[(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})\]$/;
            var atom=validChars + '+';
            var word="(" + atom + "|" + quotedUser + ")";
            var userPat=new RegExp("^" + word + "(\\." + word + ")*$");
            var domainPat=new RegExp("^" + atom + "(\\." + atom +")*$");
            var matchArray=emailStr.match(emailPat);
        
            if (matchArray==null)
            {
                   error_message = error_message + "* " + "Email address seems incorrect (check @ and .'s)" + "\n";
                error=true;
                return false;

            }
            
            //note from here
            
              var user=matchArray[1];
                var domain=matchArray[2];
                for (i=0; i<user.length; i++) {
                    if (user.charCodeAt(i)>127) {
                   error_message = error_message + "* " + "The emailid contains invalid characters." + "\n";
                error=true;

                    }
                }
                
                
                
                for (i=0; i<domain.length; i++) {
                    if (domain.charCodeAt(i)>127) {
                   error_message = error_message + "* " + "The domain name contains invalid characters." + "\n";
                error=true;
                    }
                }
                if (user.match(userPat)==null) {
                   error_message = error_message + "* " + "The emailid doesn't seem to be valid." + "\n";
                error=true;
                
                }
                var IPArray=domain.match(ipDomainPat);
                if (IPArray!=null) {
                    for (var i=1;i<=4;i++) {
                        if (IPArray[i]>255) {
                   error_message = error_message + "* " + "Destination IP address is invalid!" + "\n";
                error=true;
                        
                        }
                    }
                }
                var atomPat=new RegExp("^" + atom + "$");
                var domArr=domain.split(".");
                var len=domArr.length;
                for (i=0;i<len;i++) {
                    if (domArr[i].search(atomPat)==-1) {
                       error_message = error_message + "* " + "The domain name does not seem to be valid." + "\n";
                    error=true;
                    
                    }
                }
            
                if (len<2) {
                   error_message = error_message + "* " + "This emailid is missing a hostname!" + "\n";
                error=true;

                }
            
            //note upto here
            
            
            
    
    }
}



/*
This function checks whether the value given for the numeric field  column is valid numeric or not.
if it is not numeric it gives the appropriate messgae
*/



function check_numeric(field_name,message)
{
     if (form.elements[field_name] && (form.elements[field_name].type != "hidden")) 
     {

        var field_value = form.elements[field_name].value;


        if (isNaN(field_value))
        {
         error_message = error_message + "* " + message + "\n";
          error = true;
          }
  }

}




/*

This function checks whether the value given for the Date field  column is valid Date or not.
if it is not valid it gives the appropriate messgae

*/




function Date_format_check(field_name,message)
{

    var str = form.elements[field_name].value;
        
    
    if(str!="")
    {

        var i = 0, count = str.length, j = 0;
        while ((str.charAt(i) != "/" && str.charAt(i) != "-") && i < count)
        i++;
        
        if (i == count || i > 2)
        {
             error_message = error_message + "* " + message + "\n";
              error = true;
        }

        var addOne = false;
        if (i == 2) addOne = true;

        j = i+1;
        i = 0;

        while ((str.charAt(i+j) != "/" && str.charAt(j+i) != "-") && i+j < count)
        i++;

        if (i+j == count || i > 2) 
        {
         error_message = error_message + "* " + message + "\n";
              error = true;
        }

        j = i+3;
        i = 0;

        if (addOne) j++;

        while (i+j < count)
        i++;


        if (i != 2 && i != 4) 
        {
        error = true;
        }
        }
    
}



/*

This function checks whether the value given for the  field  empty or not

*/

function isEmpty(s)
{   
    return ((s == null) || (s.length == 0))
}




/*

This function checks whether the value given for the  field  empty or tabspace or whitespace and returns the New String.

*/

function replaceAll (s, fromStr, toStr)
{
    var new_s = s;
    for (i = 0; i < 100 && new_s.indexOf (fromStr) != -1; i++)
    {
        new_s = new_s.replace (fromStr, toStr);
    }
    return new_s;
}





/*

This function Converts the date format to dd/mm/yy or dd-mm-yy or dd/mm/yyyy or dd-mm-yyyy format

*/

function start_date_format(field_name,message)
{
  if ((form.arrivalMonth.value != "") && (form.arrivalDay.value != "") && (form.arrivalYear.value != "")) {
    var  startdate=form.arrivalMonth.value + "/" +form.arrivalDay.value + "/" + form.arrivalYear.value;
     return startdate
     
    }
   
    else {
        error_message = error_message + "* " + message + "\n";
    error=true;
    }

}




/*

This function Converts the date format to dd/mm/yy or dd-mm-yy or dd/mm/yyyy or dd-mm-yyyy format

*/

function end_date_format(field_name,message)
{

 if ((form.departMonth.value != "") && (form.departDay.value != "") && (form.departYear.value != "")) 
    {
       var enddate =form.departMonth.value + "/" +form.departDay.value + "/" + form.departYear.value;
       return enddate;
    }
   
    else 
    {
  
        error_message = error_message + "* " + message + "\n";
    error=true;
    }

}




/*

This function gives the error message passed from the calling function
*/


function error_message(message)
{
      error_message = error_message + "* " + message + "\n";
      error = true;
  
}




/*

This function passed the form passed in the function and validate the field value accordingly

*/


function check_form(form_name) 
{
  if (submitted == true)
  {
    alert("This form has already been submitted. Please press Ok and wait for this process to be completed.");
    return false;
  }

   error = false;
   form = form_name;

    check_validate();

  if (error == true)
  {
    if(error_message.length > 0)
	alert(error_message);
    return false;
  }
  else
  {
    submitted = true;
    return true;
  }
}

function check_validate()
{
	error_message = "Errors have occured during the process of your form.\n\nPlease make the following corrections:\n\n";

	check_empty(form.elements["quantity"].name,"Product quantity should not be empty!!");
	if(form.quantity.value.length > 0 && form.quantity.value == 0)
	{
		error_message += "* Quantity cannot be zero";
		error = true;
	}
}

function check_form2(form_name) 
{
  if (submitted == true)
  {
    alert("This form has already been submitted. Please press Ok and wait for this process to be completed.");
    return false;
  }

   error = false;
   form = form_name;

    check_validate2();

  if (error == true)
  {
    alert(error_message);
    return false;
  }
  else
  {
    submitted = true;
    return true;
  }
}





function Check_Lengthlow(field_name,message,lenval)
{
	var lenval1
	lenval1=form.elements[field_name].value
	var lval
	lval=lenval1.length;
	if(lval< lenval)
		{
		error_message = error_message + "* " + message + "\n";	
		error=true;
		}
}


function Check_Lengthhigh(field_name,message,lenval)
{
	var lenval1
	lenval1=form.elements[field_name].value
	var lval
	lval=lenval1.length;
	if(lval> lenval)
		{
		error_message = error_message + "* " + message + "\n";	
		error=true;
		}
}


function find_error(message)
{
	error_message = error_message + "* " + message + "\n";	
		error=true;
}




function showvalue(gt,ids,lt) {
    var isNetscape = false;
    if (navigator.appName == "Netscape") {
        isNetscape = true;
        document.captureEvents(Event.KEYPRESS);
        //document.captureEvents(Event.KEYUP);
    }
    gt.onkeypress=CheckKeyPress;
    function CheckKeyPress(evt) {
        gts=gt.value;
        window.document.getElementById(ids).innerHTML="<font class='disclaimer'>"+(gts.length+1)+ " of " + lt + " characters</font>"
        var myKeycode = isNetscape ? evt.which : window.event.keyCode;

        if(gts.length <= lt) {
            return true;
        }
        else {
            if  (myKeycode==8||myKeycode==0) {
                return true;
            }
            else {
                alert("Maximum length is " + lt + " Characters");
                return false;
            }
        }
    }
}



function callcounts(thisval,idval,ck,lt) {
    ccount=thisval.length
    window.document.getElementById(idval).innerHTML="<font class='disclaimer'>"+ccount+" of " + lt + " characters</font>"

    if(thisval.length > lt) {
        thisval=thisval.substring(0,lt);
        if (ck == 1) {
            window.document.form1.description.value=thisval;
        }
        if (ck == 2) {
            window.document.form1.addamen.value=thisval;
        }
        if (ck == 3) {
            window.document.form1.addact.value=thisval;
        }
        if (ck == 4) {
            window.document.form1.addrate.value=thisval;
        }
        if (ck == 5) {
            window.document.form1.notes.value=thisval;
        }

        alert("Maximum length is " + lt + " Characters");
        ccount=thisval.length
        window.document.getElementById(idval).innerHTML="<font class='disclaimer'>"+ccount+" of "+ lt + " characters</font>"
    }
}




function check_expire(expmonth,expdate,expyear,message)
{

 var now=new Date();
 var yr=now.getYear();
 var mm=now.getMonth();
 var dt=now.getDate();
 var flag=0;
 mm=mm+1;
 if (navigator.appName == "Netscape") {
 	yr += 1900;
 }

 
if (eval(form.elements[expyear].value) < eval(yr)) 
{
      
      flag=1;
}
else
{
   if (eval(form.elements[expmonth].value) < eval(mm) && eval(form.elements[expyear].value) == eval(yr)) 
   {
     flag=1;
   }
   else
   {
     if (eval(form.elements[expdate].value) < eval(dt) &&eval(form.elements[expmonth].value) == eval(mm) && eval(form.elements[expyear].value) == eval(yr))
     {
       flag=1
     }
       
   }

}

if(flag==1)
{
                error_message = error_message + "* " + message + "\n";	
 		error=true;
         	return false;

}


}


function Trim(text)
{

while(text.value.charAt(0)==' ')
		text.value=text.value.substring(1,text.value.length );

while(text.value.charAt(text.value.length-1)==' ')
		text.value=text.value.substring(0,text.value.length-1);
		
}



function check_float_value(gt)
{
		var dgt=2;
	    var isNetscape = false;
	    if (navigator.appName == "Netscape")
	    {
	        isNetscape = true;
	        document.captureEvents(Event.KEYPRESS);
	    }
	    document.onkeypress=CheckKeyPress;

	    function CheckKeyPress(evt)
	    {
			var myKeycode = isNetscape ? evt.which : window.event.keyCode;

	        if ((isNetscape) && (myKeycode == 0)) return true;

	        if (myKeycode > 47 && myKeycode < 58 || myKeycode==8 || myKeycode==46)
	        {

	            if (frm_obj.boolcheck.value=="0")
	            {
	                return true;
	            }
	            else
	            {
	                if ((dgt == 0) && (myKeycode == 46))
	                {
	                	return false;
	                }

	                if (gt.indexOf(".") > 0)
	                {
	                    if (myKeycode == 46)
	                    	return false;

	                    if (myKeycode == 8)
	                    	return true;

	                    if ((gt.length -   gt.indexOf(".")) > dgt)
	                    {
	                        return false;
	                    }
	                }
	                return true;
	            }
	        }
	        else
	        {
	            if(frm_obj.boolcheck.value=="1")
	            {
	                return false;
	            }
	            else
	            {
	                return true;
	            }
	        }
	    }
}


function check_integer_value(gt)
{
		var dgt=2;
	    var isNetscape = false;
	    if (navigator.appName == "Netscape")
	    {
	        isNetscape = true;
	        document.captureEvents(Event.KEYPRESS);
	    }
	    document.onkeypress=CheckKeyPress;
		
	    function CheckKeyPress(evt)
	    {
			var myKeycode = isNetscape ? evt.which : window.event.keyCode;
			
	        if ((isNetscape) && (myKeycode == 0)) return true;

	        if (myKeycode > 47 && myKeycode < 58 || myKeycode==8)// || myKeycode==46
	        {

	            if (frm_obj.boolcheck.value=="0")
	            {
	                return true;
	            }
	            else
	            {
	                if ((dgt == 0) && (myKeycode == 46))
	                {
	                	return false;
	                }

	                if (gt.indexOf(".") > 0)
	                {
	                   
						if (myKeycode == 46)
	                    	return false;

	                    if (myKeycode == 8)
	                    	return true;

	                    if ((gt.length -   gt.indexOf(".")) > dgt)
	                    {
	                        return false;
	                    }
	                }
	                return true;
	            }
	        }
	        else
	        {
	            if(frm_obj.boolcheck.value=="1")
	            {
	                return false;
	            }
	            else
	            {
	                return true;
	            }
	        }
	    }
}


function setbool(obj,v)
{
	obj.value = v;
}

function popup_window(url, h, w, sb, rz)
{
	
	if(h <= 0)
	h=200;
	
	if(w <= 0)
	w=200;
	
	if(sb.length <= 0)
	sb='yes';
	
	if(rz.length <= 0)
	rz='yes';
	
	var params = 'height=' + h + ',width=' + w + ',scrollbars=' + sb + ',resizable=' + rz;
	 
	window.open(url,'',params);
	
}

//zip code validation for US & CA - Start

var digits = "0123456789";
var lLetters = "abcdefghijklmnopqrstuvwxyz"
var uLetters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ"
var alphanum = lLetters + uLetters + digits;
var whitespace = " \t\n\r ";

var ProvinceDelimiter = "|";
var Provinces = new Array();
Provinces["US"] = "AL|AK|AS|AZ|AR|CA|CO|CT|DE|DC|FM|FL|GA|GU|HI|ID|IL|IN|IA|KS|KY|LA|ME|MH|MD|MA|MI|MN|MS|MO|MT|NE|NV|NH|NJ|NM|NY|NC|ND|MP|OH|OK|OR|PW|PA|PR|RI|SC|SD|TN|TX|UT|VT|VI|VA|WA|WV|WI|WY|AE|AA|AE|AE|AP";
Provinces["CA"] = "AB|BC|MB|NB|NF|NT|NS|NU|ON|PI|PQ|SK|YT";

function isWhitespace(s) {
	var i;
	if (isEmpty(s)) return true;
		for (i = 0; i < s.length; i++)
		{   
			var c = s.charAt(i);
			if (whitespace.indexOf(c) == -1) return false;
		}
	return true;
}

function StripIn (s, bag) {
	var i;
	var returnString = "";
	for (i = 0; i < s.length; i++) {   
		var c = s.charAt(i);
		if (bag.indexOf(c) == -1) returnString += c;
	}
	return returnString;
}

function StripNotIn (s, bag) {
	var i;
	var returnString = "";
	for (i = 0; i < s.length; i++) {
		 var c = s.charAt(i);
		 if (bag.indexOf(c) != -1) returnString += c;
	}
	return returnString;
}

function isLetter (c) {
	return ( ((c >= "a") && (c <= "z")) || ((c >= "A") && (c <= "Z")) )
}

function isDigit (c) {
	return ((c >= "0") && (c <= "9"))
}

function isLetterOrDigit (c) {
	return (isLetter(c) || isDigit(c))
}

function isInteger (s) {
	var i;
	if (isEmpty(s)) return false;
	for (i = 0; i < s.length; i++) {
		var c = s.charAt(i);
		if (!isDigit(c)) return false;
	}
	return true;
}

function AlphaNumeric(s) {
	var i;
	if (isEmpty(s)) return false;
	for (i = 0; i < s.length; i++) {
		var c = s.charAt(i);
		if (!isLetterorDigit(c)) return false;
	}
	return true;
}

function isLength(s, lMin, lMax) {
	if ((s.length >= lMin) && (s.length <= lMax)) return true;
	return false;
}

function isProvinceCode(sCode, sCountry) {
	if (Provinces[sCountry] != null) {
		if (!isLength(sCode, 2, 2)) return false;
		sCode = sCode.toUpperCase(); 
		return ((Provinces[sCountry].indexOf(sCode) != -1) && (sCode.indexOf(ProvinceDelimiter) == -1) && (isLength(sCode,2,2)));
	}
	return true;
}

function isZipCode(sZip, sCountry) {
	if (sCountry=="US") return isUSZipCode(sZip);
	if (sCountry=="CA") return isCAZipCode(sZip);
	return true
}

function isUSZipCode(sZip) {
	return (isInteger(sZip) && ((sZip.length==5) || (sZip.length==9)));
}

function isCAZipCode(sZip) {
	var re = new RegExp();
	re = /^[a-zA-z]\d[a-zA-z]( |-)?\d[a-zA-z]\d$/;
	return re.test(sZip);
}


function isURL(s) {
	if (isWhitespace(s)) return false;
	return true;
}

function warnInvalid (theField, s) {
	theField.focus();
	theField.select();
	alert(s);
	return false;
}

//zip code validation for US & CA - End
//290307
function check_nonnumeric(field_name,message)
{
     if (form.elements[field_name] && (form.elements[field_name].type != "hidden")) 
     {

        var field_value = form.elements[field_name].value;


        if (!isNaN(field_value))
        {
         error_message = error_message + "* " + message + "\n";
          error = true;
          }
  }

}

function Check_website(field_name,message)
{
if(form.elements[field_name]!="")
{
v1 = form.elements[field_name].value;
val1=v1.indexOf("http://")
val2=v1.indexOf("https://")

if((val1 == -1 && val2 == -1) || (val1 != 0 && val2 != 0))
{
error_message = error_message + "* " + message + "\n";
          error = true;
}
}
}



function Check_greater(field_name,message,lenval)
{
	var lenval1
	lenval1=form.elements[field_name].value
	var lval
	lval=lenval1;
	if(lval> lenval)
		{
		error_message = error_message + "* " + message + "\n";	
		error=true;
		}
}


function check_date(dt,mth,yr,force_dtsel,msg)
{
	var val_all_selection = 1;
	var bool = true;
	
	if(force_dtsel == 0)
	{
		bool = false;
		if(dt.value.length > 0 || mth.value.length > 0 || yr.value.length > 0)
		{
			force_dtsel = 1;
			bool = true;
		}
	}
	
	if(force_dtsel == 1)
	{
		if(dt.value.length <= 0 && mth.value.length <= 0 && yr.value.length <= 0)
		{
			error_message +="* " + msg[0] + "\n";
			error = true;
			bool = false;
		}
		else
		{
			val_all_selection = 0;
		}
	}

	if(val_all_selection == 0)
	{

		if(yr.value=="")
		{
			error = true;
			bool = false;			
			error_message +="* " + msg[1] + "\n";
		}
		if(mth.value=="")
		{
			error = true;
			bool = false;
			error_message +="* " + msg[2] + "\n";
		}
		if(dt.value=="")
		{
			error = true;
			bool = false;
			error_message +="* " + msg[3] + "\n";
		}
		
		var sel_dt = yr.value + "-" + mth.value + "-" + dt.value;

		if(sel_dt.length > 2 && !isDate(sel_dt))
		{
			error = true;
			bool = false;
			error_message +="* " + msg[4] + "\n";
		}
		
		
	}

	return bool;
	
}

function compare_dates(st_yr,st_mth,st_dt,end_yr,end_mth,end_dt,shld_check,msg)
{
	
	var bool = true;
	if(shld_check == 0)
	{
		bool = false;
		if(st_dt.value.length > 0 || st_mth.value.length > 0 || st_yr.value.length > 0 || end_dt.value.length > 0 || end_mth.value.length > 0 || end_yr.value.length > 0)
			shld_check = 1;
	
	}

	if(shld_check == 1)
	{
		bool = true;
		var smsg = new Array();
		var emsg = new Array();

		smsg[0] = msg[2];
		smsg[1] = msg[3];
		smsg[2] = msg[4];
		smsg[3] = msg[5];
		smsg[4] = msg[6];

		emsg[0] = msg[7];
		emsg[1] = msg[8];
		emsg[2] = msg[9];
		emsg[3] = msg[10];
		emsg[4] = msg[11];
		
		var chk_stdt = st_yr.value + st_mth.value + st_dt.value;
		var chk_enddt = end_yr.value + end_mth.value + end_dt.value;

		var beg_dt_val = false;
		var end_dt_val = false;

		if(st_dt.value.length == 0 && st_mth.value.length == 0 && st_yr.value.length == 0 && end_dt.value.length == 0 && end_mth.value.length == 0 && end_yr.value.length == 0)
		{
			error = true;
			bool = false;
			error_message +="* " + msg[0] + "\n";
		}
		//else if(st_dt.value.length == 0 || st_mth.value.length == 0 || st_yr.value.length == 0 || end_dt.value.length == 0 || end_mth.value.length == 0 || end_yr.value.length == 0)
		else
		{
			beg_dt_val = check_date(st_dt,st_mth,st_yr,1,smsg);
			end_dt_val = check_date(end_dt,end_mth,end_yr,1,emsg);
		}

		if(beg_dt_val && end_dt_val)
		{
			if(Math.abs(chk_enddt) < Math.abs(chk_stdt))
			{
				error = true;
				bool = false;
				error_message +="* " + msg[1] + "\n";
			}
		}
	}
	
	return bool;
}


function check_limit(field_name,message)
{
     if (form.elements[field_name] && (form.elements[field_name].type != "hidden")) 
     {

        var field_value = form.elements[field_name].value;


        if (field_value > 5)
        {
         error_message = error_message + "* " + message + "\n";
          error = true;
          }
  }

}

function check_integer_value_multiple(gt)
{
		var dgt=2;
	    var isNetscape = false;
	    if (navigator.appName == "Netscape")
	    {
	        isNetscape = true;
	        document.captureEvents(Event.KEYPRESS);
	    }
	    document.onkeypress=CheckKeyPress;
		
	    function CheckKeyPress(evt)
	    {
			var myKeycode = isNetscape ? evt.which : window.event.keyCode;
			
	        if ((isNetscape) && (myKeycode == 0)) return true;
			//alert(myKeycode);
	        if (myKeycode > 47 && myKeycode < 58 || myKeycode==8)// || myKeycode==46
	        {
			
	            if (window.document.getElementById('boolcheck').value=="0")
	            {
	                return true;
	            }
	            else
	            {
	                if ((dgt == 0) && (myKeycode == 46))
	                {
	                	return false;
	                }

	                if (gt.indexOf(".") > 0)
	                {
	                   
						if (myKeycode == 46)
	                    	return false;

	                    if (myKeycode == 8)
	                    	return true;

	                    if ((gt.length -   gt.indexOf(".")) > dgt)
	                    {
	                        return false;
	                    }
	                }
	                return true;
	            }
	        }
	        else
	        {
	            if(window.document.getElementById('boolcheck').value=="1")
	            {
	                return false;
	            }
	            else
	            {
	                return true;
	            }
	        }
	    }
}

function check_form1(form_name) 
{
  if (submitted == true)
  {
    alert("This form has already been submitted. Please press Ok and wait for this process to be completed.");
    return false;
  }

   error = false;
   form = form_name;

    check_validate1();

  if (error == true)
  {
    if(error_message.length > 0)
	alert(error_message);
    return false;
  }
  else
  {
    submitted = true;
    return true;
  }
}

function check_form_pwd(form_name) 
{
  if (submitted == true)
  {
    alert("This form has already been submitted. Please press Ok and wait for this process to be completed.");
    return false;
  }

   error = false;
   form = form_name;

    check_validate_pwd();

  if (error == true)
  {
    if(error_message.length > 0)
	alert(error_message);
    return false;
  }
  else
  {
    submitted = true;
    return true;
  }
}

function check_form_review(form_name) 
{
  if (submitted == true)
  {
    alert("This form has already been submitted. Please press Ok and wait for this process to be completed.");
    return false;
  }

   error = false;
   form = form_name;

    check_validate_rew();

  if (error == true)
  {
    if(error_message.length > 0)
	alert(error_message);
    return false;
  }
  else
  {
    submitted = true;
    return true;
  }
}