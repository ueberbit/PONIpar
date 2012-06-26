<?php

declare(encoding='UTF-8');
namespace PONIpar;

foreach (array(
	'Exceptions',
	'Parser',
	'XMLHandler',
) as $part) {
	require_once "$part.php";
}

?>
