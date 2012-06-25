<?php

declare(encoding='UTF-8');
namespace PONIpar;

class Parser {

	// The stream we’re reading from. Is null if none has been defined yet.
	protected $stream = null;

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
		// Set the stream and return.
		$this->stream = $stream;
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
