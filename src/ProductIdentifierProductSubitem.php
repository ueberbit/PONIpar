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
 * A <ProductIdentifier> subitem.
 */
class ProductIdentifierProductSubitem extends ProductSubitem {

	// Mapping of constants to types.
	const TYPE_PROPRIETARY  = '01';
	const TYPE_ISBN10       = '02';
	const TYPE_GTIN13       = '03';
	const TYPE_UPC          = '04';
	const TYPE_ISMN10       = '05';
	const TYPE_DOI          = '06';
	const TYPE_LCCN         = '13';
	const TYPE_GTIN14       = '14';
	const TYPE_ISBN13       = '15';
	const TYPE_LEGALDEPOSIT = '17';
	const TYPE_URN          = '22';
	const TYPE_OCLC         = '23';
	const TYPE_COPUBISBN13  = '24';
	const TYPE_ISMN13       = '25';

	/**
	 * The type of this product identifier.
	 */
	protected $type = null;

	/**
	 * If type is “proprietary”, the name of the type.
	 */
	protected $typename = null;

	/**
	 * The identifier’s value.
	 */
	protected $value = null;

	/**
	 * Create a new ProductIdentifier.
	 *
	 * @param mixed $in The <ProductIdentifier> DOMDocument or DOMElement.
	 */
	public function __construct($in) {
		parent::__construct($in);
		// Retrieve and check the type.
		$type = $this->_getSingleChildElementText('ProductIDType');
		if (!preg_match('/^[0-9]{2}$/', $type)) {
			throw new ONIXException('wrong format of ProductIDType');
		}
		$this->type = $type;
		// Retrieve the type name (if proprietary type).
		if ($type == self::TYPE_PROPRIETARY) {
			$typename = $this->_getSingleChildElementText('IDTypeName');
			$this->typename = $typename;
		} // TODO: else: forbid IDTypeName
		// Get the value.
		$this->value = $this->_getSingleChildElementText('IDValue');
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
	 * Retrieve the type name of this identifier, if the type is
	 * TYPE_PROPRIETARY.
	 *
	 * @return string The contents of <IDTypeName>.
	 */
	public function getTypeName() {
		return $this->typename;
	}

	/**
	 * Retrieve the actual value of this identifier.
	 *
	 * @return string The contents of <IDValue>.
	 */
	public function getValue() {
		return $this->value;
	}

}

?>
