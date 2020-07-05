<?php

// Load configuration
include(__DIR__ . '/../Configuration.php');


// Load data
include(__DIR__ . '/../DataContentTypes.php');
include(__DIR__ . '/../DataFileIcons.php');
include(__DIR__ . '/../DataFileTypes.php');


// Create arrays
$files = [];
$folders = [];


// Generic
date_default_timezone_set('UTC');
$path = __DIR__ . '/../storage' . $_SERVER['REQUEST_URI'];
$path = preg_replace('/\?.*/', '', $path);


// Check if requested resource exists
if (!file_exists($path)) {
	http_response_code(404);
	return;
}


// Check if requested resource is a file
if (is_file($path)) {
	header('Content-Type: text/plain');
	if (isset($_GET['noEscape']) && $_GET['noEscape'] == true) {
		$info = pathinfo($path);
		if (isset(CONTENT_TYPES[$info['extension']])) {
			header('Content-Type: ' . CONTENT_TYPES[$info['extension']]);
		}
	}
	echo file_get_contents($path);
	return;
}



function addFile($path, $name, $href = null) {
	if ($href == null) $href = $name;
	global $files;
	$info = pathinfo($path . $name);
	$suffix = $info['extension'];
	$size = filesize($path . $name);

	if ($size > 1E+24) $size = round($size / 1E+24, 2) . ' YB';
	elseif ($size > 1E+21) $size = round($size / 1E+21, 2) . ' ZB';
	elseif ($size > 1E+18) $size = round($size / 1E+18, 2) . ' EB';
	elseif ($size > 1E+15) $size = round($size / 1E+15, 2) . ' PB';
	elseif ($size > 1E+12) $size = round($size / 1E+12, 2) . ' TB';
	elseif ($size > 1E+9) $size = round($size / 1E+9, 2) . ' GB';
	elseif ($size > 1E+6) $size = round($size / 1E+6, 2) . ' MB';
	elseif ($size > 1E+3) $size = round($size / 1E+3, 2) . ' kB';
	else $size = $size . ' bytes';

	$files[count($files)] = [
		"name" => $name,
		"mtime" => date('l, jS \\o\\f F Y - H:i', filemtime($path . $name)),
		"type" => strpos($name, '.') !== false ? isset(FILE_TYPES[$suffix]) ? FILE_TYPES[$suffix] : "." . $suffix . " file" : "File",
		"icon" => isset(FILE_ICONS[$suffix]) ? FILE_ICONS[$suffix] : "file",
		"size" => $size,
		"href" => $href
	];
}

function addFolder($path) {
	global $folders;
	$folders[count($folders)] = $path;
}

function searchFolder($path, $search, $building_path = '') {
	$scan = scandir($path);
	for ($i = 0; $i < count($scan); $i++) {
		if ($scan[$i] == '.') continue;
		if ($scan[$i] == '..') continue;

		if (is_file($path . $scan[$i])) {
			if (strpos($scan[$i], $search) === false) continue;
			addFile($path, $scan[$i], $building_path . $scan[$i]);
		} else {
			if ($no_recursive) continue;
			searchFolder($path . $scan[$i] . '/', $search, $building_path . $scan[$i] . '/');
		}
	}
}

function searchFolderRegex($path, $regex) {
	$scan = scandir($path);
	for ($i = 0; $i < count($scan); $i++) {
		if ($scan[$i] == '.') continue;
		if ($scan[$i] == '..') continue;

		if (is_file($path . $scan[$i])) {
			if (!preg_match($regex, $scan[$i])) continue;
			addfile($path, $scan[$i]);
		} else {
			//searchFolderRegex($path . $scan[$i], $regex);
		}
	}
}



// Search?
if (isset($_GET['search']) && $_GET['search']) {
	// Search the current path
	$search_term = $_GET['search'];
	$search_regex = false;
	if (substr($search_term, 0, 6) == 'regex:') {
		$search_regex = true;
		searchFolderRegex($path, substr($search_term, 6));
	} else {
		searchFolder($path, $search_term);
	}

} else {
	// Scan the path and add files
	$scan = scandir($path);
	for ($i = 0; $i < count($scan); $i++) {
		if ($scan[$i] == '.') continue;

		if (is_file($path . $scan[$i])) {
			addfile($path, $scan[$i]);
		} else {
			addFolder($scan[$i]);
		}
	}
}


// Build page
include(__DIR__ . '/../DisplayPage.php');
