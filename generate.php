<?php

require_once realpath(dirname(__FILE__)) . '/Spyc.php';

$YAML = Spyc::YAMLLoad(realpath(dirname(__FILE__)) . '/config.yaml');

// set some constants from config
define('BASE', realpath(getcwd()));
define('DEBUG', (bool) @$YAML['debug']);
define('XMLOUT', (bool) @$YAML['xml']);

// will store the XML output (if XML === TRUE) for output at the end of the script
$XML = '';

// iterate through the config, generating and outputting the images
foreach($YAML['icons'] as $platform => $cfg)
{
	$XML .= sprintf('<platform name="%s">', $platform) . "\n";

	// define the absolute path to the source image
	$inPath = ensurePath(sprintf('%s/%s', BASE, $cfg['in']));

	// ensure the input image exists and is accessible
	if(!file_exists($inPath) || !is_readable($inPath))
		bomb(sprintf('Source icon "%s" does not exist or is not readable.', $inPath));

	// define the absolute path to the output dir
	$relOutDir = rtrim($cfg['out']['base'], '/');
	$absOutDir = ensurePath(sprintf('%s/%s', BASE, $relOutDir));

	// iterate through the "out" config for this platform
	foreach($cfg['out']['files'] as $pathname => $dims)
	{
		// define the absolute path to the output image
		$relImgPath = sprintf('%s.%s', $pathname, pathinfo($inPath, PATHINFO_EXTENSION));
		$absImgPath = sprintf('%s/%s', $absOutDir, $relImgPath);

		// ensure the dir for this particular image
		$thisAbsOutDir = ensurePath(pathinfo($absImgPath, PATHINFO_DIRNAME));

		// @debug
		debug(sprintf('Outputting to "%s"', $thisAbsOutDir));

		// generate the file
		exec(sprintf('convert %s -resize %d %s', $inPath, $dims, $absImgPath), $output, $error);

		if($error)
			bomb('Could not generate image "%s"', $absImgPath);

		$XML .= sprintf(
			'  <icon src="%s/%s" width="%d" height="%d" />', 
			$relOutDir,
			$relImgPath, 
			$dims, 
			$dims
		) . "\n";
	}

	$XML .= '</platform>' . "\n";
}

if(XMLOUT)
{
	echo "\n---------------------------- CONFIG XML \n\n";
	echo $XML;
	echo "\n-----------------------------------\n";
}

echo "\nDONE\n";

// -------

/**
 * Ensure that a directory path exists
 * 
 * @param string $absPath
 * @return string The existing absolute path
 */
function ensurePath($absPath='')
{
	if(!file_exists($absPath))
	{
		if(mkdir($absPath, 0777, TRUE))
			debug(sprintf('Path "%s" created', $absPath));
		else
			bomb(sprintf('Could not create path "%s"', $absPath));
	}
	else
		debug(sprintf('Path "%s" already exists', $absPath));

	return realpath($absPath);
}

/**
 * Outputs a debug message to the screen, if DEBUG is TRUE
 * 
 * @param string $msg
 * @return void
 */
function debug($msg='')
{
	if(DEBUG !== TRUE)
		return;

	echo $msg . "\n";
}

/**
 * End the script and output an error message
 * 
 * @param string $msg
 * @return void
 */
function bomb($msg='')
{
	exit("\n!!! ERROR: " . $msg . "\n");
}
