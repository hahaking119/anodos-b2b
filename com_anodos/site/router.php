<?php

defined('_JEXEC') or die;

function AnodosBuildRoute(&$query) {
	$segments = array();

	if (isset($query['task'])) {
		$segments[] = implode('/',explode('.',$query['task']));
		unset($query['task']);
	}
	if (isset($query['id'])) {
		$segments[] = $query['id'];
		unset($query['id']);
	}

	return $segments;
}

function AnodosParseRoute($segments) {

	$vars = array();

	// view is always the first element of the array
	$count = count($segments);

	if ($count) {

		$count--;
		$segment = array_pop($segments) ;
		if (is_numeric($segment)) {
			$vars['id'] = $segment;
		}
		else {
			$count--;
			$vars['task'] = array_pop($segments) . '.' . $segment;
		}
	}

	if ($count) {
		$vars['task'] = implode('.',$segments);
	}

	return $vars;
}
