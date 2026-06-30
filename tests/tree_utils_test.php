<?php
require_once __DIR__.'/../php/inc/utils.php';

function assertTreeValue($expected, $actual, $message) {
	if ($actual !== $expected) {
		fwrite(STDERR, $message."\nExpected: ".$expected."\nActual: ".$actual."\n");
		exit(1);
	}
}

$treeItems = readTextFile(__DIR__.'/../modules/tigra_tree_menu/tree_items.js');
$capturedErrors = array();

set_error_handler(function($severity, $message) use (&$capturedErrors) {
	$capturedErrors[] = $message;
	return true;
});

assertTreeValue('Alimentos', getCategory('002', $treeItems), 'Expected parent category lookup to return its category name.');
assertTreeValue('Bebidas', getCategory('003', $treeItems), 'Expected leaf category lookup to return its category name.');
assertTreeValue('', getCategory('999', $treeItems), 'Expected missing category lookup to return an empty string.');
assertTreeValue('', getChildren('999', $treeItems), 'Expected missing children lookup to return an empty string.');

restore_error_handler();

if (count($capturedErrors) > 0) {
	fwrite(STDERR, "Expected tree lookup helpers not to emit warnings.\n".implode("\n", $capturedErrors)."\n");
	exit(1);
}

echo "Tree utils test passed.\n";
?>
