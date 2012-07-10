<?php

declare(encoding='UTF-8');
namespace PONIpar;

foreach (array(
	'Exceptions',
	'Parser',
	'Product',
	'ProductSubitem',
		'ProductIdentifierProductSubitem',
	'XMLHandler',
) as $part) {
	require_once "$part.php";
}

?>
