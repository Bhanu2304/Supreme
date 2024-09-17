function ajax(url,uqry,div)
{ 
	var xmlhttp = false;
	
	if (window.XMLHttpRequest)
	{
		xmlhttp=new XMLHttpRequest();
	}
	else
	{
        xmlHttpReq = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange=function()
	{
		if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{  //alert(xmlhttp.responseText);
		   // var str = xmlhttp.responseText;
		    
			document.getElementById(div).innerHTML = xmlhttp.responseText;
		}
	}
	
	xmlhttp.open("POST",url,true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send(uqry);
}
