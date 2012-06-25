<?php

declare(encoding='UTF-8');

// Require the PONIpar code.
require_once 'src/PONIpar.php';

// Create a new parser.
$parser = new PONIpar\Parser();



// This is how configuring the parser to read a file works. You can specify any
// stream wrapper supported by PHP here, but a plain and simple file name as
// well. This is simply a convenience method for opening a reading stream and
// passing it to useStream().
$parser->useFile('http://www.example.com/onix.xml');

// This is how configuring the parser to read from standard input works. It is
// simply a convenience method for passing 'php://stdin'' to useFile().
$parser->useStdIn();

// This is how to configure the parser to read from an already open stream
// works. We open a temporary stream for example purposes.
$parser->useStream(fopen('php://temp', 'r'));

// But for this example, we will be parsing from a string.



// Note that parsing from a string means that the whole string will have to be
// loaded in memory before parsing it. You should really take advantage of
// PONIpar’s streaming ability and pass a string only if there’s not a lot of
// data in it (less than 5 MB) and you don’t get it from a streamable source.

// The document we are going to parse.
// TODO: This should probably be a better example.
$doc = <<<ONIX
<ONIXMessage>
	<Header />
	<Product />
	<Product />
</ONIXMessage>
ONIX;

// This is how to specify a string for parsing. PONIpar will internally open a
// temporary stream (php://temp), write the string in it and rewind the file
// pointer to start reading from there.
$parser->useString($doc);

?>
