 <?php
function check($filepath,$filename,$path)
{
	$file = fopen($filepath,"r");
	$lines = array();
	while(!feof($file))
	{
		$lines[] = fgets($file);
	}
	fclose($file);
	$variables = array();
	global $variables;
	$pattern = array();
	$pattern[0] = '/"/';
	$pattern[1] = "/'/";
	$pattern[2] = "/;/";
	$count = 0;
	for($i = 0; $i<count($lines); $i++)
	{
		if(preg_match('[select|SELECT|FROM|from]',$lines[$i])==true)
		{
			$line = $lines[$i];
			//echo $path.$filename;
			$offset = strpos($line,"=",0);
			//echo $offset;
			echo "<br>Line no : ".($i+1)."<br>";
			$equals_count = substr_count($line,"=",($offset+1),strlen($line)-($offset+1));
			$dollar_count = substr_count($line,"$",($offset+1),strlen($line)-($offset+1));
			echo "<br>".$line."<br>";
			echo "Equals count : ".$equals_count."<br>Dollar count : $dollar_count<br>";
			$two = 2;
			for($j=0;$j<$dollar_count;$j++)
			{
				$dollar_offset = strposOffset("$",$line,($j+$two));
				echo "\n";
				echo "Occurence of $ ".($j+1)." at ".$dollar_offset."<br><br>";
				$space_offset = strpos($line," ",$dollar_offset);
				if($space_offset==NULL) $space_offset = 0;
				echo "Space offset : $space_offset<br>";
				if($space_offset==0)
				$length = strlen($line)-$dollar_offset;
				else
				$length = $space_offset-$dollar_offset;
				
				$variables[$count] = preg_replace($pattern,"",substr($line,$dollar_offset,$length));
				$data = "@".$variables[$count]." = mysqli_real_escape_string(".$variables[$count].");";
				$line = $data."\n".$line;
				echo "<br>Line : $line";
				$lines[$i] = $line;
				$data = NULL;
				$count++;
				$two+=2;
			}
		}
	}
	var_dump($lines);
	$new_file = fopen($filepath,"w");
	foreach($lines as $new_line)
	{
		fwrite($new_file,$new_line);
	}
	fclose($new_file);
}
function strposOffset($search, $string, $offset)
{
    $arr = explode($search, $string);
    switch( $offset )
    {
        case $offset == 0:
        return false;
        break;
    
        case $offset > max(array_keys($arr)):
        return false;
        break;

        default:
        return strlen(implode($search, array_slice($arr, 0, $offset)));
    }
}

if(isset($_POST["path"]))
{
	$path = $_POST["path"];
	$files = scandir($path);
	foreach($files as $filename)
	{
	$ext = pathinfo($filename, PATHINFO_EXTENSION);	
	if($ext=="php")
	{
	//echo $filename."\n\n";
	check($path."\\".$filename,$filename,$path);
	}
	print_r($variables);
}

}
?>