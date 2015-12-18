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
 * A <Extent> subitem.
 */
class Extent extends Subitem {
	
	// TODO - add more type constants
	// list 23
	const TYPE_PAGE_COUNT 		= "00";
	const TYPE_NUMBER_OF_WORDS	= "02";
	const TYPE_DURATION			= "09";
	
	// TODO - add more unit constants
	// list 24
	const UNIT_WORDS 	= "02";
	const UNIT_HOURS 	= "04";
	const UNIT_MINUTES	= "05";
	const UNIT_SECONDS	= "06";
	const UNIT_TRACKS	= "11";
	const UNIT_HHH		= "14"; // hours with leading zeros
	const UNIT_HHHMM	= "15"; // hours and minutes
	const UNIT_HHHMMSS	= "16"; // hours minutes seconds

	/**
	 * The type of this product identifier.
	 */
	protected $type = null;
	
	/**
	 * The unit of this product identifier.
	 */
	protected $unit = null;

	/**
	 * The value of this product identifier.
	 */
	protected $value = null;

	/**
	 * Create a new Extent.
	 *
	 * @param mixed $in The <Extent> DOMDocument or DOMElement.
	 */
	public function __construct($in) {
		parent::__construct($in);
		
		$this->type = $this->_getSingleChildElementText('ExtentType');
		$this->value = $this->_getSingleChildElementText('ExtentValue');
		$this->unit = $this->_getSingleChildElementText('ExtentUnit');
		
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
	
	/**
	 * Retrieve the actual value of this identifier.
	 *
	 * @return string The contents of <IDValue>.
	 */
	public function getUnit() {
		return $this->unit;
	}
	
	/*
		Test type of extent
	*/
	public function isPageCount(){
		return $this->getType() == self::TYPE_PAGE_COUNT;
	}
	
	public function isNumberOfWords(){
		return $this->getType() == self::TYPE_NUMBER_OF_WORDS;
	}
	
	public function isDuration(){
		return $this->getType() == self::TYPE_DURATION;
	}
	
	/*
		Get Value for Duration (converts to different units)
	*/
	public function getValueInHours(){
		return $this->_convertValue('hours');
	}
	
	public function getValueInMinutes(){
		return $this->_convertValue('minutes');
	}
	
	public function getValueInSeconds(){
		return $this->_convertValue();
	}
	
	private function _convertValue($to='seconds'){
		
		$val = $this->getValue();
		
		// make sure value is in seconds
		switch($this->getUnit()){
			
			case self::UNIT_MINUTES: $val = $val * 60; break;
			
			case self::UNIT_HOURS:
			case self::UNIT_HHH: $val = intval($val) * 60 * 60; break;
			
			case self::UNIT_HHHMM:
				preg_match("/(\d{3})(\d{2})/", $val, $matches);
				list($val, $h, $m) = $matches;
				$val = (intval($m)*60) + (intval($h)*60*60);
				break;
		
			case self::UNIT_HHHMMSS:
				preg_match("/(\d{3})(\d{2})(\d{2})/", $val, $matches);
				list($val, $h, $m, $s) = $matches;
				$val = intval($s) + (intval($m)*60) + (intval($h)*60*60);
				break;
				
			default: break;
		}
		
		switch($to){
			case 'hours': return $val / 60 / 60; break;
			case 'minutes': return $val / 60; break;
			case 'seconds': return $val; break;
		}
		
		return $val;
	}

};

?>
