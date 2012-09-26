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
class DirectoryParser {

	/**
	 * The name of the file that is currently being parsed.
	 */
	protected $currentFile = null;

	/**
	 * The directory to use.
	 */
	protected $dir = null;

	/**
	 * Our Parser instance, created in the constructor.
	 */
	protected $parser = null;

	/**
	 * The regex files have to match.
	 */
	protected $regex = '#\.xml$#i';

	/**
	 * Retrieve the list of matching files.
	 *
	 * @return array The list of files.
	 */
	protected function getFileList() {
		$d = dir($this->dir);
		if (!$d) {
			throw new ReadException("could not open '{$this->dir}' for reading");
		}
		$files = array();
		while (($entry = $d->read()) !== false) {
			if (preg_match($this->regex, $entry)) {
				$files[] = $entry;
			}
		}
		sort($files, SORT_STRING);
		return $files;
	}

	/**
	 * Create a new DirectoryParser.
	 *
	 * Will construct and prepare a Parser instance.
	 */
	public function __construct() {
		$this->parser = new Parser();
	}

	/**
	 * Retrieve the name of the file that is currently being parsed.
	 *
	 * @return string The name of the file that is being parsed.
	 */
	public function getCurrentFile() {
		return $this->currentFile;
	}

	/**
	 * Parse all the files.
	 *
	 * @return DirectoryParser $this
	 */
	public function parse() {
		$files = $this->getFileList();
		foreach ($files as $file) {
			$this->currentFile = $file;
			$this->parser->useFile(
				$this->dir . DIRECTORY_SEPARATOR . $file
			);
			$this->parser->parse();
		}
		return $this;
	}

	/**
	 * Set the handler for found Product instances.
	 *
	 * @param  callable $cb The handler that should be called. Receives a
	 *                      Product instance as its first parameter. Can be set
	 *                      to null to remove a possibly set handler.
	 * @return DirectoryParser $this
	 */
	public function setProductHandler($cb) {
		$this->parser->setProductHandler($cb);
		return $this;
	}

	/**
	 * Set the directory to use.
	 *
	 * @param  string $dir The directory to use.
	 * @return DirectoryParser $this
	 */
	public function useDirectory($dir) {
		if (!is_string($dir) || !is_dir($dir) || !is_readable($dir)) {
			throw new ReadException("'$dir' is not a readable directory");
		}
		$this->dir = $dir;
		return $this;
	}

}

?>
