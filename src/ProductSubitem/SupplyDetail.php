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
class SupplyDetail extends Subitem {
	
	protected $availability_codes = array(
		'IP' => 'Available',
		'NP' => 'Not yet available',
		'OP' => 'Terminated',
		'AB' => 'Cancelled',
		'CS' => 'Contact supplier' 
	);
	
	protected $product_availabilities = array(
		'20' => 'Available',
		'10' => 'Not yet available',
		'51' => 'Terminated',
		'01' => 'Cancelled',
		'99' => 'Contact supplier'
	);

	/**
	 * Status of the availability
	 */
	protected $availability_code = null;
	protected $product_availability = null; // preferred by ONIX 2.1
	
	protected $on_sale_date = null;

	/**
	 * The identifier’s value.
	 */
	protected $prices = array();

	/**
	 * Create a new ProductIdentifier.
	 *
	 * @param mixed $in The <ProductIdentifier> DOMDocument or DOMElement.
	 */
	public function __construct($in) {
		
		parent::__construct($in);
		
		// Retrieve and check the type.
		try{ $this->availability_code = $this->_getSingleChildElementText('AvailabilityCode'); } catch(\Exception $e) { }
		try{ $this->product_availability = $this->_getSingleChildElementText('ProductAvailability'); } catch(\Exception $e) { }
		
		try{ $this->on_sale_date = $this->_getSingleChildElementText('OnSaleDate');} catch(\Exception $e) { }
		
		if( !$this->on_sale_date ){
			try{ $this->on_sale_date = $this->_getSingleChildElementText('SupplyDate/Date');} catch(\Exception $e) { }
		}
		
		// Get the prices.
		$this->prices = array();
		
		$prices = $this->xpath->query("/*/Price");
		
		foreach($prices as $price){
			//error_log(print_r($price, true));
			
			$this->prices[] = array(
				'PriceTypeCode' => $this->_getPriceData($price, 'PriceTypeCode'),
				'PriceAmount' => $this->_getPriceData($price, 'PriceAmount'),
				'CurrencyCode' => $this->_getPriceData($price, 'CurrencyCode'),
				'PriceEffectiveFrom' => $this->_getPriceData($price, 'PriceEffectiveFrom')
			);			
		}
		
		// Save memory.
		$this->_forgetSource();
	}
	
	protected function _getPriceData($node, $key, $default=null){
		$list = $node->getElementsByTagName($key);
		
		if( $list->length > 0 )
			return $list->item(0)->textContent;
			
		return $default;
	}
	

	/**
	 * Retrieve the availability of this supply detail
	 *
	 * @return string The contents of <ProductIDType>.
	 */
	public function getAvailability() {
		
		if( $this->product_availability  )
			return isset($this->product_availabilities[$this->product_availability])
					? $this->product_availabilities[$this->product_availability]
					: null;
			
		if( $this->$availability_code  )
			return isset($this->availability_codes[$this->product_availability])
					? $this->availability_codes[$this->product_availability]
					: null;
		
		return null;
	}
	
	/**
	 * Retrieve the actual value of this identifier.
	 *
	 * @return string The contents of <IDValue>.
	 */
	public function getOnSaleDate() {
		return $this->on_sale_date;
	}

	/**
	 * Retrieve the actual value of this identifier.
	 *
	 * @return string The contents of <IDValue>.
	 */
	public function getPrices() {
		return $this->prices;
	}

}

?>
