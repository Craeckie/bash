<html>
<head>
<title>Compile and test status</title>
<style>
* {
	font-family: Tahoma;
}
.mytable tr > *:nth-child(1) { width:3%; }
.mytable tr > *:nth-child(2) { width:10%; }
.mytable tr > *:nth-child(3) { width:40%; }
.mytable tr > *:nth-child(4) { width:40%; }
table, th, td {
    border: 1px solid black;
    border-collapse: collapse;
    font-family: Arial;
}
tr:nth-child(even) {
    background-color: lightgray;
}
span, b {
    font-family:Arial;
}
input {
	float:right;
}

.redbg {
	background-color:red;
}
.red {
	color:red;
}
.green {
	color:green;
}
.greenbg {
	background-color:lime;
}
</style>
<script src="bower_components/jquery-2.1.3.min/index.js"></script>
<script>
function runScript(branch) {
	window.location.href="?run=true&branch=" + branch;
}
var lastRunning = false;
function checkBuildingStatus() {
	$.ajax({
	  	url: "results/running",
		success: function(text) {
			if (text.indexOf("STOPPED") > -1) {
				var text = '<span class="green">Not Building</span>';
				$("input").prop("disabled", false);
				if (lastRunning == true) {
					text = '<span class="green">Not Building, <a href="javascript:location.reload(true);">Reload</a></span>';
				}
				$("#status").html(text);
				lastRunning = false;
			} else {
				
				$("#status").html('<span class="red">Building ' + text + '</span>');
				$("input").prop("disabled", true);
				lastRunning = true;
			}
		},
		cache: false
	});
}
window.setInterval("checkBuildingStatus()", 5000);
checkBuildingStatus();
		
</script>
<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
</head>
<body>
<b>Status:</b>
<div id="status">N/A</div>
<?php

//$building=file_exists("results/running");

if (isset($_GET['run'])) {
  # This code will run if ?run=true is set.
  $name=htmlspecialchars($_GET['branch']);
  if ( preg_match("/^[a-zA-Z0-9_-]+$/", "$name" ) ) {
	$branchname=$name;
	if ( preg_match("/^[0-9]+-.*/",  "$name" ) ) {
		$branchname=preg_replace('/^[0-9]+-(.*)/', '$1', $name);
	}
	echo "<p>Starting Build of \"".$branchname."\"</p>";
	//$success = chdir("/var/impl/implementierung/$branchName/");
	//echo $success;
	//$res = exec("/var/impl/implementierung/master/./compile", $out, $err);
	//exec("echo '/var/impl/implementierung/$branchName/./compile'|at now");
	//exec("bash -c 'exec nohup setsid /var/impl/implementierung/$branchname/./compile $name > /dev/null 2>&1 &'");
	//$res = exec("/var/impl/implementierung/master/build/test/./test.sh", $out, $err);
	//echo "<p>Result: $res<br />"; //Out: ".implode($out, "<br />")."<br />Error: $err<br /></p>";
	echo '<script>window.setTimeout("checkBuildingStatus()", 5000);</script>';
  }
}

?>
<br />
<br />
<table  class="mytable" border="1">
<!--<colgroup>
    <col span="1" style="width: 5%;">
    <col span="1" style="width: 30%;">
    <col span="1" style="width: 65%;">
</colgroup>-->
<thead>
    <tr>
	<th>Rank</th>
	<th>Name</th>
	<th>Compile</th>
	<th>Test</th>
    </tr>
</thead>
<tbody>

<?php
function contains($text, $search) {
	return strpos($text, $search) !== false;
}
function writeFile($file, $isTest) {
    //echo $file.', '.$isTest;
	if (!file_exists($file)) {
		echo '<p class="red"><b>File not found! Currently building?</b></p>';
	}
	else
	{
		$file_hdl = fopen($file, "r");
		$count = 0;
		while (!feof($file_hdl)) {
			$text = htmlspecialchars(fgets($file_hdl));
			$line = substr($text, 0, 350);
			if (strlen($text) != strlen($line))
				$line .= "...";

			if ($isTest) {
				if (contains($line, "FAIL")) {
		 			echo '<span class="red">'. $line.'</span><br />';
					$count++;
				} else if (contains($line, "No such file") || contains($line, "No tests available")) {
					echo '<span class="red">No tests found!</span>';
					$count++;
				} else if (contains($line, "real") && contains($line, "user")) {
					echo '<span>'.$line.'</span>';
					$count++;
				}
			}
			else {
				$class = "";
				$count++;
				if (strpos($line, "Successful") !== false) {
					$class = "greenbg";
				}
				else if (strpos($line, "Error") !== false || strpos($line, "exited with status ") !== false) {
					$class = "redbg";
				}
				echo '<span class="'.$class.'">'.$line.'</span><br />';	
			}

			if ($count > 5) {
				echo "...";
				break;
			}
		}

		fclose($file_hdl);
		//echo $count;
		if ($isTest && $count === 0)
			echo '<span class="greenbg">All passed!</span>';
	}
}
?>
<tr>
<td></td>
<td>
Cron-Build
</td>
<td>
<?php
$text = file_get_contents("last-cron.txt");
//$diff = strtotime($text
//$last = date_parse($text);
//$now = date();
//$interval = date_diff($last, $now);
//echo 'diff: '.$interval->format('%H:%M:%S');
echo "Last Build: <a href='cron.txt'>$text</a>";
?>
</td><td></td>
</tr>
<?php
$path = "results";
$files = scandir($path);
$names = preg_filter('#^build-([0-9]+-.*).txt#', '$1', $files);
foreach ($names as $filename):
$name=preg_replace('/^[0-9]+-(.*)/', '$1', $filename);
$rank=preg_replace('/^([0-9]+)-.*/', '$1', $filename);
$build_name = 'build-'.$filename.'.txt';
$test_name = 'test-'.$filename.'.txt';
?>
<tr>
<td width="10">
<?php
echo '<span style="font-weight: bold;">'.$rank.'</span>';
?>
</td>
<td>
<?php
echo '<span style="font-weight: bold;" id="'.$name.'">'.$name.'</span>';
?>
</td>
<td>
<input type="button" disabled value="Rebuild now!" onclick="javascript:runScript('<?php echo $filename; ?>')">

<?php 
$temp=$path."/".$build_name;
writeFile($temp, FALSE); ?>
<a href="<?php echo $temp; ?>">Full log</a>
</td>
<td>
<?php 
$temp=$path."/".$test_name;
writeFile($temp, TRUE); ?>
<br />
<a href="<?php echo $temp; ?>">Full log</a>
</td>
</tr>
<?php
endforeach;
?>
</tbody>
</table>
</body>
</html>
