<html>
<head>
<title>Compile and test status</title>
<style>
* {
	font-family: Tahoma;
}
.mytable tr > *:nth-child(1) { width:15%; }
.mytable tr > *:nth-child(2) { width:40%; }
.mytable tr > *:nth-child(3) { width:45%; }
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
</style>
</head>
<body>
<table  class="mytable" border="1">
<!--<colgroup>
    <col span="1" style="width: 5%;">
    <col span="1" style="width: 30%;">
    <col span="1" style="width: 65%;">
</colgroup>-->
<thead>
    <tr>
	<th>Name</th>
	<th>Compile</th>
	<th>Test</th>
    </tr>
</thead>
<tbody>

<?php
function contains($text, $search) {
	return strpos($line, $search) !== false;
}
function writeFile($file, $isTest) {
    //echo $file.', '.$isTest;
    $file_hdl = fopen($file, "r");
    $count = 0;
    while (!feof($file_hdl)) {
	$line = substr(htmlspecialchars(fgets($file_hdl)), 0, 350);
	if ($isTest) {
	    if (contains($line, "FAIL")) {
 			echo '<span style="color:red;">'. $line.'</span>';
			$count++;
        } if (contains($line, "No such file")) {
			echo '<span style="color:red;">No tests found!</span>';
			$count++;
        }
	}
	else {
	$style = "";
	$count++;
	if (strpos($line, "Successful") !== false) {
		$style = "background-color:lime";
	}
	else if (strpos($line, "Error") !== false || strpos($line, "exited with status ") !== false) {
		$style = "background-color:red";
	}
		echo '<span style="'.$style.'">'.$line.'</span><br />';
        }
	
	if ($count > 5) {
		break;
	}
}
fclose($file_hdl);
//echo $count;
if ($isTest && $count === 0)
	echo '<span style="background-color:lime">All passed!</span>';
}
?>
<tr>
<td>
Cron-Build
</td>
<td>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$text = file_get_contents("last-cron.txt");
//$diff = strtotime($text
//$last = date_parse($text);
//$now = date();
//$interval = date_diff($last, $now);
//echo 'diff: '.$interval->format('%H:%M:%S');
echo "Last Build: $text";
?>
</td><td></td>
</tr>
<?php
$path = "results";
$files = scandir($path);
$names = preg_filter('#^build-(.*).txt#', '$1', $files);
foreach ($names as $name):
?>
<tr>
<td width="80">
<?php
//    if (preg_match('#^build-(.*).txt#', $entry, $match_out)) {
//	$match = $match_out[1];
$build_name = 'build-'.$name.'.txt';
$test_name = 'test-'.$name.'.txt';
echo '<span><b>'.$name.'</b></span>';
?>
</td>
<td>
<?php writeFile($path."/".$build_name, FALSE); ?>
</td>
<td>
<?php writeFile($path."/".$test_name, TRUE); ?>
</td>
</tr>
<?php
endforeach;
?>
</tbody>
</table>
</body>
</html>
