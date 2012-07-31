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
 * Base class for <Product> subitems.
 *
 * Protected methods are prefixed with an underscore, since especially the “get”
 * prefix will possibly be used for attributes and subitems etc, therefore
 * everything starting with “get” should definitely be available for public
 * methods in subclasses.
 */
class ProductSubitem {

	/**
	 * A DOM implementation.
	 */
	protected static $impl = null;

	/**
	 * The subitem DOM document.
	 */
	protected $doc = null;

	/**
	 * An XPath instance.
	 */
	protected $xpath = null;

	/**
	 * Throw away the DOM document and XPath instance to save memory.
	 *
	 * Note that afterwards, other methods might cease to work. Therefore this
	 * method is protected: It should only be called by relatively simple
	 * subclasses that don’t need the source DOM after having been initialized.
	 *
	 * @return null
	 */
	protected function _forgetSource() {
		$this->doc = $this->xpath = null;
	}

	/**
	 * Retrieve a single child element with the specified name. If there is more
	 * than one element by that name, throw an exception.
	 *
	 * @todo   Make this method short tag compatible.
	 * @param  string     $name The name of the element to search.
	 * @param  DOMElement $root The root element under which to search for. If
	 *                          not specified, the root of this subitem will be
	 *                          used.
	 * @return DOMElement A single element by that name. If not found, an
	 *                    ElementNotFoundException will be thrown. If more than
	 *                    one was found, a TooManyElementsFoundException will be
	 *                    thrown.
	 */
	protected function _getSingleChildElement($name, \DOMElement $root = null) {
		// If no root is specified, use the document element of our DOM.
		if ($root === null) {
			$root = $this->doc->documentElement;
		}
		// TODO: The name should be escaped.
		$res = $this->xpath->query("/*/$name");
		if ($res === false) {
			throw new InternalException('XPath query returned false');
		}
		$length = $res->length;
		if ($length == 0) {
			throw new ElementNotFoundException("no $name element found");
		}
		if ($length != 1) {
			throw new TooManyElementsFoundException("more than one $name element found");
		}
		return $res->item(0);
	}

	/**
	 * Retrieve the text contents of a single child element with the specified
	 * name. If there is more than one element by that name, throw an exception.
	 *
	 * @param  string     $name The name of the element to search.
	 * @param  DOMElement $root The root element under which to search for. If
	 *                          not specified, the root of this subitem will be
	 *                          used.
	 * @return string The text contents of a single element by that name. If not
	 *                    found, an ElementNotFoundException will be thrown. If
	 *                    more than one was found, a
	 *                    TooManyElementsFoundException will be thrown.
	 */
	protected function _getSingleChildElementText($name, \DOMElement $root = null) {
		return $this->_getSingleChildElement($name, $root)->textContent;
	}

	/**
	 * Create a new Product subitem based on a DOM document or element
	 * containing only that subitem.
	 *
	 * Will work on a clone of the element, which means that you don’t have to
	 * clone before calling the constructor.
	 *
	 * @param mixed $in A DOM document or element that contains this instance’s
	 *                  data. (DOMElement instances will be converted.)
	 */
	public function __construct($in) {
		// Check $doc’s type.
		if (!(($in instanceof \DOMDocument) || ($in instanceof \DOMElement))) {
			throw new InternalException('$doc needs to be a DOMDocument or DOMElement instance');
		}
		// If the static DOM implementation is not created yet, do it.
		if (!self::$impl) {
			self::$impl = new \DOMImplementation();
		}
		// Create a new DOM document
		$doc = self::$impl->createDocument();
		// Append a clone of either the element or the document’s root element.
		$doc->appendChild($doc->importNode(
			  ($in instanceof \DOMDocument)
			? $in->documentElement
			: $in
		, true));
		// Check if the root element’s name matches. Please don’t hack this to
		// allow other namespaces or naming schemes. Instead, send a patch to
		// make PONIpar more flexible! :)
		$called = get_called_class();
		$classwithoutns = substr(strrchr(__CLASS__, '\\'), 1);
		$shouldbe = __NAMESPACE__ . '\\' . $doc->documentElement->tagName . $classwithoutns;
		if ($called != $shouldbe) {
			throw new InternalException(
				"trying to extend " . __CLASS__ . " from invalidly named class $called (should be $shouldbe)"
			);
		}
		// Store document.
		$this->doc = $doc;
		// Create an XPath instance and store it.
		$this->xpath = new \DOMXPath($doc);
	}

}

?>
