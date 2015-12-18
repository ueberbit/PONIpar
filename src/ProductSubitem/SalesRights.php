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
 * A <SalesRights> subitem.
 */
class SalesRights extends Subitem {
	
	// TODO - add more type constants
	const TYPE_UNKNOWN = "00";
	const TYPE_FOR_SALE_EXCLUSIVE = "01";
	const TYPE_FOR_SALE_NONEXCLUSIVE = "02";
	const TYPE_NOT_FOR_SALE = "03";

	/**
	 * The values of this sales right
	 */
	protected $type = null;
	protected $country = null;
	protected $territory= null;
	

	/**
	 * Create a new SalesRights.
	 *
	 * @param mixed $in The <SalesRights> DOMDocument or DOMElement.
	 */
	public function __construct($in) {
		parent::__construct($in);
		
		try {$this->type = $this->_getSingleChildElementText('SalesRightsType');} catch(\Exception $e) { }
		try {$this->country = $this->_getSingleChildElementText('RightsCountry');} catch(\Exception $e) { }
		try {$this->territory = $this->_getSingleChildElementText('RightsTerritory');} catch(\Exception $e) { }
		
		// try 3.0
		if( !$this->country && !$this->territory ){
			try {$this->country = $this->_getSingleChildElementText('Territory/CountriesIncluded');} catch(\Exception $e) { }
		}
		
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
		return $this->country ? $this->country : $this->territory;
	}
	
	/*
		Is For Sale
	*/
	public function isForSale(){
		return $this->getType() == self::TYPE_FOR_SALE_EXCLUSIVE
		|| $this->getType() == self::TYPE_FOR_SALE_NONEXCLUSIVE;
	}

};

