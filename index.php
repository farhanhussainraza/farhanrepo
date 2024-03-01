<?

$FILENAME = ""; //SET YOUR FILE HERE

if (!$FILENAME || !file_exists($FILENAME)) {
	echo "Please set your target file \$FILENAME on line 2\n";
	exit();
}

include_once "class.httpdownload.php";

$object = new httpdownload;

$bandwidth = @intval(implode('',file('bandwidth.txt'))) / 1024;
if ($bandwidth > 1024)
{
	$bandwidth = round($bandwidth / 1024 , 2);
	$bandwidth .= " MB";
}
else
{
	$bandwidth .= " KB";
}

switch (@$_GET['download']) {
case 'resume_speed':
case 'noresume_speed':
case 'resume':
case 'noresume':
	$object->set_byfile($FILENAME);
	if ($_GET['download'] == 'noresume' || $_GET['download'] == 'noresume_speed') $object->use_resume = false;
	if ($_GET['download'] == 'resume_speed' || $_GET['download'] == 'noresume_speed' ) $object->speed = 100;
	$object->download();
break;
case 'data':
case 'dataresume':
	$data = implode('' , file($FILENAME));
	$object->set_bydata($data);
	if ($_SERVER['download'] != 'dataresume') $object->use_resume = false;
	$object->filename = basename($FILENAME);
	$object->download();
break;
case 'auth':
	$object->set_byfile($FILENAME);
	$object->use_auth = true;
	$object->handler['auth'] = "test_auth";
	$object->download();
break;
case 'url':
	$object->set_byurl('http://www.php.net/get/php_manual_chm.zip/from/cr.php.net/mirror');
	$object->download();
break;
}

if ($object->bandwidth > 0)
{
	error_reporting(E_NONE);
	$b = intval(implode('',file('bandwidth.txt'))) + $object->bandwidth;
	$f = fopen('bandwidth.txt','wb');
	fwrite($f,$b);
	fclose($f);
	exit;
}

function test_auth($user,$pass) { //test authentication function
	if ($user == 'user' && $pass == 'pass') return true;
	return false;
}

?>

<head>
<style>
<!--
body         { font-family: Tahoma; font-size: 12px }
a            { color: #FF0000 }
-->
</style>
</head>

<title>HTTPDownload example</title>

<h2><font color="navy">HttpDownload</font></h2>Select a link and try it with a download manager (like <a href="http://reget.com">Reget</a>) .<br><br>

Total bandwidth used : <B><?=$bandwidth?></B>

<br><br>
<a href="test.php?download=noresume">Download file</a><br>
<a href="test.php?download=noresume_speed">Download file (speed limit 100 kbs)</a><br>
<a href="test.php?download=resume">Download file with resume</a><br>
<a href="test.php?download=resume_speed">Download file with resume (speed limit 100 kbs) </a><br>
<a href="test.php?download=data">Download file data (May slow)</a><br>
<a href="test.php?download=dataresume">Download file data with resume (May slow)</a><br>
<a href="test.php?download=auth">Authentication download (user/pass)</a><br>
<a href="test.php?download=url">URL Download (simple redirect)</a><br>

<p><font size="1"><font color="#808080">( Click 
<a href="http://en.vietapi.com/wiki/index.php/PHP:_HttpDownload">
<font color="black">here</font></a><font color=""> to view class 
information )</font></p>

