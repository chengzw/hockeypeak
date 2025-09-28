<?php

function _get_cache_file_name($category, $id) {
	$basedir = dirname(__FILE__);
	return $basedir . "/_cache/" . $category . "_" . $id . ".json";
}

function get_cache($category, $id) {
	$file = _get_cache_file_name($category, $id);
	// echo $file;
	if (file_exists($file)) {
		return file_get_contents($file);
	}
	return "";
}

function set_cache($category, $id, $content) {
	$file = _get_cache_file_name($category, $id);
	if ($file != "") {
		file_put_contents($file, $content);
	}
}

function clear_cache($category, $start, $end) {
  $start_time = strtotime($start);
  $end_time = strtotime($end);
  for ($time = $start_time; $time <= $end_time; $time = strtotime("+1 day", $time)) {
    $file = _get_cache_file_name($category, date("Y-m-d", $time));
    // echo "date: $file\n";
    if (file_exists($file)) {
      echo "remove: $file\n";
      unlink($file);
    }
  }
  return [];
}
