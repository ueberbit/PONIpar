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
 * A <OtherText> subitem.
 */
class OtherText extends Subitem {
	
	// TODO - add more type constants
	
	// List 33
	const TYPE_MAIN_DESCRIPTION = "01";
	const TYPE_SHORT_DESCRIPTION = "02"; 
	const TYPE_LONG_DESCRIPTION = "03";
	const TYPE_BIOGRAPHICAL_NOTE = "13";
	
	// List 34
	const FORMAT_HTML = '02';
	const FORMAT_TEXT = '06';

	/**
	 * Text data
	 */
	protected $type = null;
	protected $format = null;
	protected $value = null;

	/**
	 * Create a new OtherText.
	 *
	 * @param mixed $in The <OtherText> DOMDocument or DOMElement.
	 */
	public function __construct($in) {
		parent::__construct($in);
		
		try {$this->type = $this->_getSingleChildElementText('TextTypeCode');} catch(\Exception $e) { }
		
		// try 3.0
		if( !$this->type )
			try {$this->type = $this->_getSingleChildElementText('TextType');} catch(\Exception $e) { }
		
		try {$this->format = $this->_getSingleChildElementText('TextFormat');} catch(\Exception $e) { }
		try {$this->value = $this->_getSingleChildElementText('Text');} catch(\Exception $e) { }
		
		// Save memory.
		$this->_forgetSource();
	}

	/**
	 * Retrieve the type of this text.
	 *
	 * @return string The contents of <TextTypeCode>.
	 */
	public function getType() {
		return $this->type;
	}
	
	/**
	 * Retrieve the format of this text.
	 *
	 * @return string The contents of <TextFormat>.
	 */
	public function getFormat() {
		return $this->format;
	}

	/**
	 * Retrieve the actual value of this text.
	 *
	 * @return string The contents of <Text>.
	 */
	public function getValue() {
		return $this->value;
	}

};

