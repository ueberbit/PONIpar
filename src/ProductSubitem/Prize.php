<?php

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
 * A <Prize> subitem.
 */
class Prize extends Subitem {

	// List 41
	const CODE_WINNER = '01';
	const CODE_RUNNER_UP = '02';
	const CODE_COMMENDED = '03';
	const CODE_SHORTLISTED = '04';
	const CODE_LONGLISTED = '05';
	const CODE_JOIN_WINNER = '06';
	const CODE_NOMINATED = '07';

	/**
	 * Prize data
	 */
	protected $name = null;
	protected $year = null;
	protected $country = null;
	protected $code = null;
	protected $statement = null;

	/**
	 * Create a new Prize.
	 *
	 * @param mixed $in The <Prize> DOMDocument or DOMElement.
	 */
	public function __construct($in) {
		parent::__construct($in);

		try {$this->name = $this->_getSingleChildElementText('PrizeName');} catch(\Exception $e) { }
		try {$this->year = $this->_getSingleChildElementText('PrizeYear');} catch(\Exception $e) { }
		try {$this->country = $this->_getSingleChildElementText('PrizeCountry');} catch(\Exception $e) { }
		try {$this->code = $this->_getSingleChildElementText('PrizeCode');} catch(\Exception $e) { }

		// 3.0.2
		try {$this->statement = $this->_getSingleChildElementText('PrizeStatement');} catch(\Exception $e) { }

		// Save memory.
		$this->_forgetSource();
	}

	/**
	 * Retrieve the name of this text.
	 *
	 * @return string The contents of <PrizeName>.
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Retrieve the format of this text.
	 *
	 * @return string The contents of <PrizeYear>.
	 */
	public function getYear() {
		return $this->year;
	}

	/**
	 * Retrieve code of prize
	 *
	 * @return string The contents of <PrizeCode>.
	 */
	public function getCode() {
		return $this->code;
	}

	public function getCodeName(){
		switch($this->code){
			case '01': return 'Winner of ';
			case '02': return 'Runner-up for ';
			case '04': return 'Among shortlisted titles for ';
			case '05': return 'Among longlisted titles for ';
			case '06': return 'Joint winner of ';
			case '07': return 'Nominated for ';
			default: return '';
		}
	}

	public function getSatement(){
		// 3.0 may contain a statement
		if( $this->statement )
			return $this->statement;
		// if no statement (which will always be the case in 2.1), make one
		else
			return $this->getCodeName().$this->name.($this->year?', '.$this->year:'');
	}

	public function getData(){
		return [
			'name' => $this->name,
			'year' => $this->year,
			'code' => $this->code,
			'country' => $this->country,
			'statement' => $this->getSatement(),
			'code_name' => $this->getCodeName()
		];
	}
};
