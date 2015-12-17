<?php

declare(encoding='UTF-8');
namespace PONIpar\ProductSubitem;

use PONIpar\ProductSubitem\Subitem;

/*
   This file is part of the PONIpar PHP Onix Parser Library.
   Copyright (c) 2012, [di] digitale informationssysteme gmbh
   All rights reserved.

   The software is provided under the terms of the new (3-clause) BSD license.
   Please see the file LICENSE for details.
*/

/**
 * A <ProductIdentifier> subitem.
 */
class Title extends Subitem {
	
	// TODO - add more type constants
	const TYPE_DISTINCTIVE_TITLE = "01";

	/**
	 * The type of this product identifier.
	 */
	protected $type = null;

	/**
	 * The title's values
	 */
	protected $value = array(
		'title' => null,
		'subtitle' => null
	);

	/**
	 * Create a new ProductIdentifier.
	 *
	 * @param mixed $in The <ProductIdentifier> DOMDocument or DOMElement.
	 */
	public function __construct($in) {
		parent::__construct($in);
		
		// Retrieve and check the type.
		$type = $this->_getSingleChildElementText('TitleType');
		
		if (!preg_match('/^[0-9]{2}$/', $type)) {
			throw new ONIXException('wrong format of TitleType');
		}
		$this->type = $type;
		
		try {$this->value['title'] = $this->_getSingleChildElementText('TitleText');} catch(\Exception $e) { }
		try {$this->value['subtitle'] = $this->_getSingleChildElementText('Subtitle');} catch(\Exception $e) { }
		
		// Save memory.
		$this->_forgetSource();
	}

	/**
	 * Retrieve the type of this identifier.
	 *
	 * @return string The contents of <ProductIDType>.
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * Retrieve the actual value of this identifier.
	 *
	 * @return string The contents of <IDValue>.
	 */
	public function getValue() {
		return $this->value;
	}

};

// a provider ONIX would foundn to use camelcase tag, so support this.
class_alias('TitleProductSubitem', 'titleProductSubitem');

?>
