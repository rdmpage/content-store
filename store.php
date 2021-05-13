<?php

// Store hashed files on disk

require_once (dirname(__FILE__) . '/utils.php');

$index = dirname(__FILE__) . '/index.tsv';


//----------------------------------------------------------------------------------------
function file_to_sha1($root, $filename, $force = false)
{
	$hash_dir = $root . '/sha1';

	$sha1 = sha1_file($filename);
	$path = create_path_from_hash($sha1, $hash_dir);
	$savefilename = $hash_dir . '/' . $path;

	// only write file if doesn't exist or we are forcing
	if (!file_exists($savefilename) || $force)
	{
		copy ($filename, $path);
	}
	
	return $sha1;
}

//----------------------------------------------------------------------------------------
function file_to_md5($root, $filename, $force = false)
{
	$hash_dir = $root . '/md5';

	$md5 = md5_file($filename);
	$path = create_path_from_hash($md5, $hash_dir);
	
	$savefilename = $hash_dir . '/' . $path;
	
	// only write file if doesn't exist or we are forcing
	if (!file_exists($savefilename) || $force)
	{
		copy ($filename, $path);
	}
	
	return $md5;
}

//----------------------------------------------------------------------------------------
function have_hash($root, $hash_type, $hash)
{
	$path = hash_to_path_string($hash);
	
	$savefilename = $root . '/' . $hash_type . '/' . $path;
	
	// echo $savefilename . "\n";
	
	return file_exists($savefilename);
}

//----------------------------------------------------------------------------------------
function have_file_hash($root, $hash_type, $filename)
{
	$hash = '';
	
	switch ($hash_type)
	{
		case 'md5':
			$hash = md5_file($filename);
			break;

		case 'sha1':
		default:
			$hash = sha1_file($filename);
			break;
	}		
	
	return have_hash($root, $hash_type, $hash);
}


//----------------------------------------------------------------------------------------
function get($url, $filename)
{
	$command = "wget '$url' -O '$filename'";
	echo $command . "\n";
	
	$result_code = null;
	system ($command, $result_code);
	
	/*
	
	https://gist.github.com/cosimo/5747881
	
	This is the list of exit codes for wget:

	0       No problems occurred
	1       Generic error code
	2       Parse error — for instance, when parsing command-line options, the .wgetrc or .netrc…
	3       File I/O error
	4       Network failure
	5       SSL verification failure
	6       Username/password authentication failure
	7       Protocol errors
	8       Server issued an error response
		
	*/
	
	return $result_code;
}

//----------------------------------------------------------------------------------------
function add_tag_to_index($hash, $tag)
{
	global $index;
	
	$row = array($tag, $hash);
	
	file_put_contents($index, join("\t", $row) . "\n", FILE_APPEND);
}

//----------------------------------------------------------------------------------------
function add_pdf_id($hash, $filename)
{
	$command = "mutool show '" . $filename . "'";
	$output = array();
	$return_var = 0;
	exec($command, $output, $return_var);

	print_r($output);
	
	$id_one = $id_two = '';
	
	// fingerprint of document
	// /ID[<1DF5C424B06409960AD7E8C67DC4DC9D><C10EFB2E302C62F886DC2F4C11E8492C>]
	foreach ($output as $line)
	{
	
		if (preg_match('/\/ID\s*\[\s*\<(?<one>[^\>]+)\>\s*\<(?<two>[^\>]+)\>\s*\]/', $line, $m))
		{
			$id_one = $m['one'];
			$id_two = $m['two'];
			
			// print_r($m);
		}

	}	
	
	if ($id_one != '')
	{
		add_tag_to_index($hash, $id_one);
	}

	if ($id_two != '')
	{
		add_tag_to_index($hash, $id_two);
	}
	
	
}


//----------------------------------------------------------------------------------------
function add_metadata_to_index($hash, $filename)
{
	global $index;
	
	// can we get any metadata?
	
	$finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
	$mime_type = finfo_file($finfo, $filename);
	finfo_close($finfo);
	
	switch ($mime_type)
	{
		case 'application/pdf':
			add_pdf_id($hash, $filename);
			break;	
	
		default:
			break;
	}
}

//----------------------------------------------------------------------------------------


$root = dirname(__FILE__) . '/hash';
//$hash_type = 'sha1';
//$hash_type = 'md5';

//$filename = 'adb5d616e36dd2489bc2fc4eff2f3c878f29d8e1.pdf';
//$filename = 'figure-2.png';

//$filename = '63814.xml';

if (1)
{
	$filename = uniqid();

	$url = 'http://www.bjc.sggw.pl/arts/2020v20n2/06.pdf';
	$url = 'https://binary.pensoft.net/fig/539491';
	$url = 'http://bionames.org/bionames-archive/pdf/b9/25/8f/b9258f9214ba9e63ebfb9296a4a8d87c7c2281cd/b9258f9214ba9e63ebfb9296a4a8d87c7c2281cd.pdf';
	
	//$url = 'https://zookeys.pensoft.net/article/62034/download/pdf/539456';
	
	//$url = 'https://journals.co.za/doi/pdf/10.10520/AJA0000008_24';
	
	//$url = 'http://www.boldsystems.org/pics/INHYM/INDOBIOSYS-CCDB25941-F05%2B1458234574.jpg';
	
	//$url = 'https://zenodo.org/record/4745038/files/figure.png?download=1;';

	$url = 'http://natuurtijdschriften.nl/download?type=document;docid=597117';

	// dead links
	$url = 'http://www.zoores.ac.cn/CN/article/downloadArticleFile.do?attachType=PDF&id=174';
	$url = 'http://rmbr.nus.edu.sg/rbz/biblio/50/50rbz459-464.pdf';
	
	$url = 'http://www.amphibia.be/pubs/Zeylanica_2001.pdf';
	$url = 'https://www.jstage.jst.go.jp/article/hsj2000/22/2/22_2_51/_pdf';
	$url = 'http://www.biomedcentral.com/content/pdf/1756-0500-2-241.pdf';
	$url = 'http://faunaofindia.nic.in/PDFVolumes/records/086/03-04/0505-0513.pdf';

	$result_code = get($url, $filename);
	
	if ($result_code != 0)
	{
		echo "*** Badness occurred: $result_code ***\n";
	}
	else
	{
		$hash_types = array('md5', 'sha1');
	
		foreach ($hash_types as $hash_type)
		{	
			if (have_file_hash($root, $hash_type, $filename))
			{
				echo "Have $filename with $hash_type already\n";
			}
			else
			{	
	
				switch ($hash_type)
				{
					case 'md5':
						$hash = file_to_md5($root, $filename);
						break;

					case 'sha1':
					default:
						$hash = file_to_sha1($root, $filename);
						break;
				}		
	
				echo $hash . "\n";

				echo "Add to index\n";
				add_tag_to_index($hash, $url);
				add_metadata_to_index($hash, $filename);
			}
		}
	}
	
	unlink($filename);
	
	// add to index
}

// just a file
if (0)
{

	$hash_type = 'md5';
	$filename = 'examples/figure-2.png';
	//$filename = 'ZK_article_62034_en_1.pdf';


	if (have_file_hash($root, $hash_type, $filename))
	{
		echo "Have $filename with $hash_type already\n";
	}
	else
	{

		switch ($hash_type)
		{
			case 'md5':
				$hash = file_to_md5($root, $filename);
				break;

			case 'sha1':
			default:
				$hash = file_to_sha1($root, $filename);
				break;
		}
		
		echo $hash . "\n";
		
		echo "Add to index\n";
		add_metadata_to_index($hash, $filename);
	}
	
	
}






?>

