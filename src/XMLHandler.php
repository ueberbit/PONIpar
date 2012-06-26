<?php

declare(encoding='UTF-8');
namespace PONIpar;

/**
 * Does the actual XML parsing and creates high-level objects, which will be
 * passed back to the Parser class.
 */
class XMLHandler {

	/**
	 * Holds the Expat XML parser, initialized in the constructor.
	 */
	protected $parser = null;

	/**
	 * Handles an opened XML element.
	 *
	 * @param  resource $parser The XML parser.
	 * @param  string   $name   The name of the opened XML element.
	 * @param  array    $attrs  Associative array of the attributes.
	 * @return null
	 */
	protected function handleElementOpen($parser, $name, $attrs) {
		// TODO: Implement.
	}

	/**
	 * Handles a closed XML element.
	 *
	 * @param  resource $parser The XML parser.
	 * @param  string   $name   The name of the closed XML element.
	 * @return null
	 */
	protected function handleElementClose($parser, $name) {
		// TODO: Implement.
	}

	/**
	 * Handles XML text data.
	 *
	 * @param  resource $parser The XML parser.
	 * @param  string   $text   The text.
	 * @return null
	 */
	protected function handleText($parser, $text) {
		// TODO: Implement.
	}

	/**
	 * Create and initialize a new instance.
	 */
	public function __construct() {
		// Create a parser that outputs UTF-8.
		$parser = xml_parser_create('UTF-8');
		if (!is_resource($parser)) {
			throw new XMLException('could not create the XML parser');
		}
		// Configure the parser to not do case-folding.
		if (!xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0)) {
			throw new XMLException('could not disable case folding in the XML parser');
		}
		// Bind the parser’s callbacks on this object.
		if (!xml_set_object($parser, $this)) {
			throw new XMLException('could not bind the XML parser on this object');
		}
		// Bind handlers.
		if (!xml_set_element_handler($parser, 'handleElementOpen', 'handleElementClose')) {
			throw new XMLException('could not bind the XML element handlers');
		}
		if (!xml_set_character_data_handler($parser, 'handleText')) {
			throw new XMLException('could not bind the XML character data handlers');
		}
		// Store the parser.
		$this->parser = $parser;
	}

	/**
	 * Free resources used by this instance.
	 */
	public function __destruct() {
		// Free the XML parser.
		if ($this->parser !== null) {
			xml_parser_free($this->parser);
		}
	}

	/**
	 * Parse a chunk of data.
	 *
	 * @param  string  $data  A UTF-8 string of arbitrary length to parse.
	 * @param  bool    $final Whether this is the final piece of data.
	 * @return XMLHandler $this
	 */
	public function parse($data, $final = false) {
		// Simply pass the data to Expat.
		if (xml_parse($this->parser, $data, $final) != 1) {
			// An error occured. Retrieve info and throw an exception.
			$parser = $this->parser;
			$code = xml_get_error_code($parser);
			throw new XMLException(sprintf(
				'XML parsing error %d at %d:%d (byte %d): %s',
				$code,
				xml_get_current_line_number($parser),
				xml_get_current_column_number($parser),
				xml_get_current_byte_index($parser),
				xml_error_string($code)
			), $code);
		}
	}

}

?>
