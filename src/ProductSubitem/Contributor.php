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
 * A <Contributor> subitem.
 */
class Contributor extends Subitem {

	// Mapping of constants to types.
	const ROLE_AUTHOR  		= 'A01';
	const ROLE_NARRATOR     = 'E03';
	const ROLE_READBY       = 'E07';
	const ROLE_PERFORMER    = 'E99';

	/**
	 * The type of this product identifier.
	 */
	protected $role = null;
	protected $name = null;

	/**
	 * The identifier’s value.
	 */
	protected $value = null;

	/**
	 * Create a new Contributor.
	 *
	 * @param mixed $in The <Contributor> DOMDocument or DOMElement.
	 */
	public function __construct($in) {
		
		parent::__construct($in);
		
		// Retrieve and check the type.
		$this->role = $this->_getSingleChildElementText('ContributorRole');
		
		// Get the value.
		$this->value = array();
		
		$this->value['ContributorRole'] = $this->role;
		
		try {$this->value['PersonName'] = $this->_getSingleChildElementText('PersonName');} catch(\Exception $e) { }
		try {$this->value['PersonNameInverted'] = $this->_getSingleChildElementText('PersonNameInverted');} catch(\Exception $e) { }
		try {$this->value['SequenceNumber'] = $this->_getSingleChildElementText('SequenceNumber');} catch(\Exception $e) { }
		try {$this->value['NamesBeforeKey'] = $this->_getSingleChildElementText('NamesBeforeKey');} catch(\Exception $e) { }
		try {$this->value['KeyNames'] = $this->_getSingleChildElementText('KeyNames');} catch(\Exception $e) { }
		
		// Save memory.
		$this->_forgetSource();
	}
	
	/*
		Get Name
	*/
	public function getName(){
		
		// already found
		if( $this->name )
			return $this->name();
			
		if( $this->getValue()['PersonName'] )
			return $this->name = $this->getValue()['PersonName'];
			
		if( $this->getValue()['PersonNameInverted'] ){
			return $this->name = preg_replace("/^(.+), (.+)$/", "$2 $1", $this->getValue()['PersonNameInverted']);
		}
		
		return $this->name;
	}

	/**
	 * Retrieve the type of this identifier.
	 *
	 * @return string The contents of <ProductIDType>.
	 */
	public function getRole() {
		return $this->role;
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

