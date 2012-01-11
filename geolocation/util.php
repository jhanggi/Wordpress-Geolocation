<?php 

function is_checked($field) {
	if (get_option($field))
	echo ' checked="checked" ';
}

function is_value($field, $value) {
	if (get_option($field) == $value)
	echo ' checked="checked" ';
}
?>