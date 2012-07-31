<?php

declare(encoding='UTF-8');
namespace PONIpar;

/*
   This file is part of the PONIpar PHP Onix Parser Library.
   Copyright (c) 2012, [di] digitale informationssysteme gmbh
   All rights reserved.

   The software is provided under the terms of the new (3-clause) BSD license.
   Please see the file LICENSE for details.
*/

/**
 * The main class that users of PONIpar should interact with.
 *
 * Configure it with your input stream and high-level handlers.
 */
class Parser {

	/**
	 * The number of bytes we’re reading from the stream at a time. Defaults
	 * to 10KiB.
	 */
	protected $chunksize = 10240;

	/**
	 * The stream we’re reading from. Is null if none has been defined yet.
	 */
	protected $stream = null;

	/**
	 * The XML handler we’re using. Will be created when setting a stream.
	 */
	protected $xmlhandler = null;

	/**
	 * The callable to invoke when a new Product has been parsed.
	 */
	protected $productHandler = null;

	/**
	 * Set a new input stream.
	 *
	 * @param  resource $stream The stream to use.
	 * @return Parser $this
	 */
	protected function setStream($stream) {
		// Sanity checks.
		if (!is_resource($stream)) {
			throw new InternalException('parameter is not a resource');
		}
		// TODO: Find out whether we should check get_resource_type() as well.
		// If there already is an open stream, at least try to close it.
		if ($this->stream !== null) {
			@fclose($this->stream);
		}
		// Create a new XML handler (because the old one may have state etc.).
		$xml = new XMLHandler();
		// Set the instance variables and return.
		$this->xmlhandler = $xml;
		$this->stream     = $stream;
		return $this;
	}

	/**
	 * Run the parsing process until the stream is empty.
	 *
	 * @return Parser $this
	 */
	public function parse() {
		// Check if we have a stream at all.
		if ($this->stream === null) {
			throw new ReadException('cannot parse without a configured input stream');
		}
		// Bind the product handler to the XML handler.
		$xml = $this->xmlhandler;
		$productHandler = $this->productHandler;
		$xml->setProductHandler(
			is_callable($productHandler)
			? function ($product) use ($productHandler) {
				call_user_func($productHandler, $product);
			}
			: null
		);
		// Read chunks and feed them to the XMLHandler.
		while (!feof($this->stream)) {
			$chunk = fread($this->stream, $this->chunksize);
			$xml->parse($chunk);
		}
		// Tell the XMLHandler that there’s nothing left.
		$xml->parse('', true);
		// Finish.
		return $this;
	}

	/**
	 * Set the handler for found Product instances.
	 *
	 * @param  callable $cb The handler that should be called. Receives a
	 *                      Product instance as its first parameter. Can be set
	 *                      to null to remove a possibly set handler.
	 * @return Parser $this
	 */
	public function setProductHandler($cb) {
		if (!(is_callable($cb) || ($cb === null))) {
			throw new InternalException('no valid callback specified for setProductHandler');
		}
		$this->productHandler = $cb;
		return $this;
	}

	/**
	 * Define a file as input source.
	 *
	 * @param  string $name The file’s name. Since it will be directly passed to
	 *                      fopen(), PHP’s stream wrappers are supported.
	 * @return Parser $this
	 */
	public function useFile($name) {
		// Open the file.
		$fh = fopen($name, 'r');
		// If the file could not be opened, throw an exception.
		if ($fh === false) {
			throw new ReadException("could not open file: $name");
		}
		// Set the stream and return.
		return $this->useStream($fh);
	}

	/**
	 * Define stdin as input source.
	 *
	 * @return Parser $this
	 */
	public function useStdIn() {
		// Simply call useFile with the stdin URL.
		return $this->useFile('php://stdin');
	}

	/**
	 * Define a stream as input source.
	 *
	 * @param  resource $stream The stream to use.
	 * @return Parser   $this
	 */
	public function useStream($stream) {
		// We don’t do any checking at all, setStream() does that already.
		return $this->setStream($stream);
	}

	/**
	 * Define a string as input source.
	 *
	 * Internally, this string will be written to a php://temp stream. That
	 * stream will then be rewound and passed to useStream().
	 *
	 * @param  string $string The string to use.
	 * @return Parser $this
	 */
	public function useString($string) {
		// Open a temp stream for writing and reading.
		$fh = fopen('php://temp', 'w+');
		// If the stream could not be opened, throw an exception.
		if ($fh === false) {
			throw new WriteException('could not open temp stream');
		}
		// Write to the stream and check for errors.
		if (fwrite($fh, $string) === false) {
			throw new WriteException('could not write to the temp stream');
		}
		// Rewind the stream.
		if (!rewind($fh)) {
			throw new WriteException('could not rewind the temp stream');
		}
		// Pass the stream to useStream().
		return $this->useStream($fh);
	}

}

?>
