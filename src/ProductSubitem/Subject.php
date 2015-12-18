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
 * A <Subject> subitem.
 */
class Subject extends Subitem {
	
	// TODO - add more constants
	// list 27
	const SCHEME_BISAC_SUJECT_HEADING = "10";
	const SCHEME_KEYWORDS = "20";

	/**
	 * The scheme of this subject
	 */
	protected $scheme = null;

	/**
	 * The value (code) of this subject
	 */
	protected $value = null;
	
	protected $mainSubject = false;

	/**
	 * Create a new Subject.
	 *
	 * @param mixed $in The <Subject> DOMDocument or DOMElement.
	 */
	public function __construct($in) {
		parent::__construct($in);
		
		try{ $this->scheme = $this->_getSingleChildElementText('SubjectSchemeIdentifier'); } catch(\Exception $e) { }
		try{ $this->value = $this->_getSingleChildElementText('SubjectCode'); } catch(\Exception $e) { }
		
		try{ $this->_getSingleChildElementText('MainSubject'); $this->mainSubject = true; } catch(\Exception $e) {
			$this->mainSubject = false;
		}
		
		// Save memory.
		$this->_forgetSource();
	}

	/**
	 * Retrieve the type of this identifier.
	 *
	 * @return string The contents of <SubjectSchemeIdentifier>.
	 */
	public function getScheme() {
		return $this->scheme;
	}

	/**
	 * Retrieve the value of 
	 *
	 * @return string The contents of <SubjectCode>.
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * Is this the <MainSubject>?
	 *
	 * @return bool
	 */
	public function isMainSubject() {
		return $this->mainSubject;
	}
	
};

?>
