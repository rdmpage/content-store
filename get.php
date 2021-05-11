<?php

// Fetch file with a given hash

require_once (dirname(__FILE__) . '/utils.php');

$root = dirname(__FILE__) . '/hash';

$hash_type = 'sha1';

$hash = 'adb5d616e36dd2489bc2fc4eff2f3c878f29d8e1';
$hash = '667737200c4e279e4312d921ac4e2352';
$hash = 'cbe93e7594ad79ca6d9304d68d36fea9cde3eb82';
//$hash = 'b05cff497b554b9cf0cee5cf988dc77333aaba06';
//$hash = '78afe7dd850504ba04bbb696d80f0ead313d71a4';

$hash = 'b5a9d420f4ab32f6c2820a9325ad38d7';

if (isset($_GET['hash']))
{
	$hash = $_GET['hash'];
}

// sanity check

if (strlen($hash) == 32)
{
	$hash_type = 'md5';
}

if (strlen($hash) == 40)
{
	$hash_type = 'sha1';
}

if (0)
{
	$hash_type = 'md5';
	$hash = '667737200c4e279e4312d921ac4e2352';
}

$hash_dir = $root . '/' . $hash_type;

$path = hash_to_path_string($hash, $hash_dir);


$filename = $hash_dir . '/' . $path;

//echo $filename;

$mime_type = '*';

if (file_exists($filename))
{
	$finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
	$mime_type = finfo_file($finfo, $filename);
	finfo_close($finfo);

	header("Content-Type: " . $mime_type);	
	header('Content-Length: ' . filesize($filename));
	
	ob_start();
	readfile($filename);
	ob_end_flush();
	
	
	exit();
	
	
}

header('HTTP/1.1 404 Not Found');


?>

