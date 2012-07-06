<?php

declare(encoding='UTF-8');
namespace PONIpar;

/**
 * Represents a single product in the ONIX data, provides raw access as well as
 * convenience methods.
 */
class Product {

	/**
	 * Cardinality restrictions for subitems. Default cardinality values are
	 * min=0 and max=0 (meaning "unlimited").
	 */
	protected static $allowedSubitems = array(
		'ProductIdentifier' => array('min' => 1),
	);

	/**
	 * Holds the DOM of our <Product>, initialized in the constructor.
	 */
	protected $dom = null;

	/**
	 * Holds the XPath instance for the DOM.
	 */
	protected $xpath = null;

	/**
	 * Holds the subitem instances in our Product.
	 */
	protected $subitems = array();

	/**
	 * Create a new instance based on a <Product> DOM document.
	 *
	 * The DOM document needs to have its elements converted to reference names.
	 *
	 * @param DOMDocument $dom The DOM with <Product> as its root.
	 */
	public function __construct(\DOMDocument $dom) {
		// Save the DOM.
		$this->dom = $dom;
		// Get an XPath instance for that DOM.
		$this->xpath = new \DOMXPath($dom);
		// Check the cardinalities of the subitems.
		foreach (self::$allowedSubitems as $name => $opts) {
			$min = isset($opts['min']) ? (int)$opts['min'] : 0;
			$max = isset($opts['max']) ? (int)$opts['max'] : 0;
			if ($min || $max) {
				$elements = $this->xpath->query("/Product/$name");
				$count = $elements->length;
			}
			if ($min && ($count < $min)) {
				throw new XMLException(
					"expecting at least $min <$name> childs, but $count found"
				);
			}
			if ($max && ($count > $max)) {
				throw new XMLException(
					"expecting at most $max <$name> childs, but $count found"
				);
			}
		}
	}

	/**
	 * Get all child elements with the specified name as an array of either
	 * Subitem subclass objects or DOMElements.
	 *
	 * If there is a matching subclass, that will be (created and) returned,
	 * else raw DOMElements.
	 *
	 * @param  string $name The element name to retrieve.
	 * @return array  A (possibly empty) array of Subitem subclass objects or
	 *                DOMElement objects.
	 */
	public function get($name) {
		// If we don’t already have the items in the cache, create them.
		if (!isset($this->subitems[$name])) {
			$subitems = array();
			// Retrieve all matching children.
			$elements = $this->xpath->query("/Product/$name");
			// If we have a Subitem subclass for that element, create instances
			// and return them.
			$subitemclass = __NAMESPACE__ . "\\{$name}ProductSubitem";
			if (class_exists($subitemclass)) {
				foreach ($elements as $element) {
					$subitems[] = new $subitemclass($element);
				}
			} else {
				// Else, return clones of the matched nodes.
				foreach ($elements as $element) {
					$subitems[] = $element->cloneNode(true);
				}
			}
			$this->subitems[$name] = $subitems;
		}
		return $this->subitems[$name];
	}

	/**
	 * Get a copy of the original <Product> DOM.
	 *
	 * Useful for retrieving information we don’t have any convenience methods
	 * and classes for.
	 *
	 * @return DOMDocument A copy of the DOM passed to the constructor.
	 */
	public function getDOM() {
		return $this->dom->cloneNode(true);
	}

}

?>
