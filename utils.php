<?php

//--------------------------------------------------------------------------------------------------
//http://www.php.net/manual/en/function.rmdir.php#107233
function rrmdir($dir) {
   if (is_dir($dir)) {
     $objects = scandir($dir);
     foreach ($objects as $object) {
       if ($object != "." && $object != "..") {
         if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
       }
     }
     reset($objects);
     rmdir($dir);
   }
 }

//--------------------------------------------------------------------------------------------------
// http://stackoverflow.com/questions/247678/how-does-mediawiki-compose-the-image-paths
function hash_to_path_array($hash)
{
	preg_match('/^(..)(..)(..)/', $hash, $matches);
	
	$hash_path = array();
	$hash_path[] = $matches[1];
	$hash_path[] = $matches[2];
	$hash_path[] = $matches[3];

	return $hash_path;
}

//--------------------------------------------------------------------------------------------------
// Return path for a hash
function hash_to_path_string($hash)
{
	$hash_path_parts = hash_to_path_array($hash);
	
	$hash_path = '/' . join("/", $hash_path_parts) . '/' . $hash;

	return $hash_path;
}

//--------------------------------------------------------------------------------------------------
// Create nested folders in folder "root" based on hash
function create_path_from_hash($hash, $root = '.')
{	
	$hash_path_parts 	= hash_to_path_array($hash);
	$hash_path 			= hash_to_path_string($hash);
	$filename 			= $root . $hash_path;
				
	// If we dont have file, create directory structure for it	
	if (!file_exists($filename))
	{
		$path = $root;
		$path .= '/' . $hash_path_parts[0];
		if (!file_exists($path))
		{
			$oldumask = umask(0); 
			mkdir($path, 0777);
			umask($oldumask);
		}
		$path .= '/' . $hash_path_parts[1];
		if (!file_exists($path))
		{
			$oldumask = umask(0); 
			mkdir($path, 0777);
			umask($oldumask);
		}
		$path .= '/' . $hash_path_parts[2];
		if (!file_exists($path))
		{
			$oldumask = umask(0); 
			mkdir($path, 0777);
			umask($oldumask);
		}

	}
	
	return $filename;
}


?>