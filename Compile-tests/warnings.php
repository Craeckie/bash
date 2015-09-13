<html>
<head>
<style>
* { font-family:Arial }
</style>
<?php
function startsWith($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}

class Layer {
    public $name;
    public $lines;
}
function l_cont($name) {
	echo "Searching: $name\n";
	foreach ($curLayer as $layers)
	{
		$curName = $curLayer->name;
		echo "Comparing to: $curName, ";
		if (strcmp($curName, $name))
			return true;
	}
	return false;
}
function get($name) {
	foreach ($curLayer as $layers)
	{
		$curName = $curLayer->name;
		if (strcmp($curName, $name))
			return $curLayer;
	}
	return NULL;
}

?>
</head>

<body>
<?php
$file="warnings.txt";
$file_hdl = fopen($file, "r");
$last_layer = '';
$last_class = '';

$layers = Array();

while (!feof($file_hdl)) 
{
	$line = htmlspecialchars(fgets($file_hdl));
	if (startsWith($line, "/var/impl/implementierung/master/cote/"))
	{
		$cut = substr($line, 46);
		$full_class = preg_replace('/([^:]+).*/', '$1', $cut);
		$layer_name = preg_replace('/^([a-zA-Z]+)\/.*$/', '$1', $full_class);
		$layer_name = ucfirst($layer_name);

		$text = "";
		$class = preg_replace('/^[a-zA-Z\/]+\/([a-zA-Z]+\.h).*$/', '$1', $full_class);
		
		
		if (strcmp($layer_name, $last_layer))
		{
			echo "<h1>$layer_name</h1>\n";
			//$text .= "<h1>$layer_name</h1>\n";
			$last_layer = $layer_name;
		}
		if (strcmp($class, $last_class))
		{
			echo "<h3>$class</h3>\n";
			//$text .= "<h3>$class</h3>\n";
			$last_class = $class;
		}
		
		//$styled = preg_replace('/^([^:]+)(.*)$/', "<p><b>$1</b><br />\n$2</p>\n", $cut);
		$warn = preg_replace('/^[^:]+:([0-9]+: )warning: (.*)$/', '<b>$1</b>$2', $cut);
		$bold = preg_replace("/(argument|parameter) '([^']+)'/", "$1 '<b>$2</b>'", $warn);
		$styled = "<p>\n$bold\n</p>\n";
		echo $styled;
		//$text .= $styled;

		//if (l_cont($layer_name)) {
		//	echo "In Array: $layer_name";
		//	$layer = get($layer_name);
		//	echo "Lines: $layer->lines";
		//	$layer->lines .= $text;
		//	var_dump($layer);
		//}
		//else
		//{
		//	echo "Not in Array: $layer_name";
		//	$layer = new Layer();
		//	$layer->name = $layer_name;
		//	$layer->lines = $text;
		//	var_dump($layer);
		//}
		echo "\n";
		//$last_layer = $layer_name;
	}
	else
	{
		$bold = preg_replace("/(argument|parameter) '([^']+)'/", "$1 '<b>$2</b>'", $line);
		echo "<p style=\"padding-left:5em\">$bold</p>\n";
		//$text = "<p style=\"padding-left:5em\">$bold</p>\n";
		//$layers[$last_layer] += $text;
	}
}
foreach ($layer as $layers)
{
	//echo "<h1>$name</h1>\n";
	//echo $text;
	//var_dump($layers);
}

?>
</body>
</html>
