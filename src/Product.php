<?php

declare(encoding='UTF-8');
namespace PONIpar;

use PONIpar\ProductSubitem\OtherText;
use PONIpar\ProductSubitem\Subject;

/*
   This file is part of the PONIpar PHP Onix Parser Library.
   Copyright (c) 2012, [di] digitale informationssysteme gmbh
   All rights reserved.

   The software is provided under the terms of the new (3-clause) BSD license.
   Please see the file LICENSE for details.
*/

/**
 * Represents a single product in the ONIX data, provides raw access as well as
 * convenience methods.
 */
class Product {

	/**
	 * Cardinality restrictions for subitems. Default cardinality values are
	 * min=0 and max=0 (meaning "unlimited").
	 */
	protected static $allowedSubitems = array(
		'ProductIdentifier' => array('min' => 1),
	);
	
	protected static $productStatus = array(
		"00" => "Unspecified",
		"01" => "Cancelled",
		"02" => "Forthcoming",
		"03" => "Postponed indefinitely",
		"04" => "Active",
		"05" => "No longer our product",
		"06" => "Out of stock indefinitely",
		"07" => "Out of print",
		"08" => "Inactive",
		"09" => "Unknown",
		"10" => "Remaindered",
		"11" => "Withdrawn from sale",
		"12" => "Not available in this market",
		"13" => "Active, but not sold separately",
		"14" => "Active, with market restrictions",
		"15" => "Recalled",
		"16" => "Temporarily withdrawn from sale"
	);
	
	/**
	 * The version of ONIX we are parsing
	 */
	protected $version = null;

	/**
	 * Holds the DOM of our <Product>, initialized in the constructor.
	 */
	protected $dom = null;

	/**
	 * Holds the XPath instance for the DOM.
	 */
	protected $xpath = null;

	/**
	 * Holds the subitem instances in our Product.
	 */
	protected $subitems = array();

	/**
	 * Create a new instance based on a <Product> DOM document.
	 *
	 * The DOM document needs to have its elements converted to reference names.
	 *
	 * @param DOMDocument $dom The DOM with <Product> as its root.
	 */
	public function __construct(\DOMDocument $dom, $version) {
		
		$this->version = $version;
		
		// Save the DOM.
		$this->dom = $dom;
		// Get an XPath instance for that DOM.
		$this->xpath = new \DOMXPath($dom);
		// Check the cardinalities of the subitems.
		foreach (self::$allowedSubitems as $name => $opts) {
			$min = isset($opts['min']) ? (int)$opts['min'] : 0;
			$max = isset($opts['max']) ? (int)$opts['max'] : 0;
			if ($min || $max) {
				$elements = $this->xpath->query("/Product/$name");
				$count = $elements->length;
			}
			if ($min && ($count < $min)) {
				throw new XMLException(
					"expecting at least $min <$name> childs, but $count found"
				);
			}
			if ($max && ($count > $max)) {
				throw new XMLException(
					"expecting at most $max <$name> childs, but $count found"
				);
			}
		}
	}

	/**
	 * Get all child elements with the specified name as an array of either
	 * Subitem subclass objects or DOMElements.
	 *
	 * If there is a matching subclass, that will be (created and) returned,
	 * else raw DOMElements.
	 *
	 * @param  string $name The element name to retrieve.
	 * @return array  A (possibly empty) array of Subitem subclass objects or
	 *                DOMElement objects.
	 */
	public function get($name, $classname=null) {
		
		$classname = $classname ? $classname : $name;
		
		// If we don’t already have the items in the cache, create them.
		if (!isset($this->subitems[$name])) {
			$subitems = array();
			// Retrieve all matching children.
			$elements = $this->xpath->query("/Product/$name");
			
			// If we have a Subitem subclass for that element, create instances
			// and return them.
			$subitemclass = __NAMESPACE__ . "\\ProductSubitem\\{$classname}";
			if (class_exists($subitemclass)) {
				foreach ($elements as $element) {
					$subitems[] = new $subitemclass($element);
				}
			} else {
				// Else, return clones of the matched nodes.
				foreach ($elements as $element) {
					$subitems[] = $element->cloneNode(true);
				}
			}
			$this->subitems[$name] = $subitems;
		}
		return $this->subitems[$name];
	}
	
	/**
	* Gets version of ONIX being parsed
	*/
	public function getVersion(){
		return $this->version;
	}

	/**
	 * Get a copy of the original <Product> DOM.
	 *
	 * Useful for retrieving information we don’t have any convenience methods
	 * and classes for.
	 *
	 * @return DOMDocument A copy of the DOM passed to the constructor.
	 */
	public function getDOM() {
		return $this->dom->cloneNode(true);
	}

	/**
	 * Get a product identifier of the given type.
	 *
	 * ONIX allows for multiple identifiers per product. This method retrieves
	 * all <ProductIdentifier> subitems and returns the one with the given type.
	 * If there is no identifier with that type, an ElementNotFoundException
	 * will be thrown.
	 *
	 * @todo   Support passing a name for proprietary identifiers.
	 * @param  string $type The type of identifier to search for. Using one of
	 *                      ProductIdentifierProductSubitem’s TYPE_* constants
	 *                      is recommended.
	 * @return string The found product identifier.
	 */
	public function getIdentifier($type) {
		$ids = $this->get('ProductIdentifier');
		foreach ($ids as $id) {
			if ($id->getType() == $type) {
				return $id->getValue();
			}
		}
		throw new ElementNotFoundException("no identifier of type $type found");
	}
	
	
	/**
	* Get Edition
	* 
	* See list 64 for status codes
	*
	* @return string
	*/
	public function publishingStatus(){
		if( $this->version >= '3.0' )
			return $this->get('PublishingDetail/PublishingStatus')[0]->nodeValue;
		else
			return $this->get('PublishingStatus')[0]->nodeValue;
	}
	
	public function publishingStatusString(){
		$status = $this->PublishingStatus();
		return isset(self::$productStatus[$status]) ? self::$productStatus[$status] : 'Unknown';
	}
	
	public function isActive(){
		return in_array($this->publishingStatus(),['04','02']); // 'Active' and `Forthcoming` (list 64)
	}
	
	/**
	* Get Product Form
	* 
	* See list 150 for form codes
	*
	* @return string
	*/
	public function getProductForm(){
		if( $this->version >= '3.0' )
			return $this->get('DescriptiveDetail/ProductForm')[0]->nodeValue;
		else
			return $this->get('ProductForm')[0]->nodeValue;
	}
	
	/**
	* Get Titles
	*
	* @return array of Title objects
	*/
	public function getTitles(){
		if( $this->version >= '3.0' )
			return $this->get('DescriptiveDetail/TitleDetail', 'Title');
		else
			return $this->get('Title');
	}
	
	/**
	* Get Edition
	* 
	* @return array of Contributor objects
	*/
	public function getContributors(){
		if( $this->version >= '3.0' )
			return $this->get('DescriptiveDetail/Contributor', 'Contributor');
		else
			return $this->get('Contributor');
	}
	
	/**
	* Get Supply Details
	*
	* @return array of SupplyDetail objects
	*/
	public function getSupplyDetails(){
		if( $this->version >= '3.0' )
			return $this->get('ProductSupply/SupplyDetail', 'SupplyDetail');
		else
			return $this->get('SupplyDetail');
	}
	
	/**
	* Get Sales Rights
	*
	* @return array of SalesRights objects
	*/
	public function getSalesRights(){
		if( $this->version >= '3.0' )
			return $this->get('PublishingDetail/SalesRights', 'SalesRights');
		else
			return $this->get('SalesRights');
	}
	
	/**
	* Get For Sale Rights
	*
	* @return string Region of list of countries this product is for sale in
	*/
	public function getForSaleRights(){
		
		$sales_rights = $this->getSalesRights();
		$rights = '';
		
		if( count($sales_rights) == 1 ){
			$rights = $sales_rights[0]->getValue();
		}else{
			
			foreach($sales_rights as $sr){
				if( $sr->isForSale() )
					$rights .= ' '.$sr->getValue();
			}
			
			$rights = trim($rights);
			$rights = explode(' ', $rights);
			sort($rights);
			$rights = implode(' ', $rights);
		}
		
		return $rights;
	}
	
	/**
	* Get Texts
	*
	* @return array of OtherText objects
	*/
	public function getTexts(){
		if( $this->version >= '3.0' )
			return $this->get('CollateralDetail/TextContent', 'OtherText');
		else
			return $this->get('OtherText');
	}
	
	/**
	* Get Main Description
	* 
	* If no main description is found, it will return the first in the list,
	* unless `$strict` is set to `true`
	* 
	* @return string
	*/
	public function getMainDescription($strict=false){
		
		$texts = $this->getTexts();
		$description = '';
		
		foreach($texts as $text){
			
			if( $text->getType() == OtherText::TYPE_MAIN_DESCRIPTION )
				$description = $text->getValue();
			
			elseif( !$description && $strict !== true )
				$description = $text->getValue();
		}
		
		return $description;
	}
	
	/**
	* Get Review Quotes
	*
	* @return array
	*/
	public function getReviewQuotes(){

		$texts = $this->getTexts();
		$quotes = [];

		foreach($texts as $text){
			if( $text->getType() == OtherText::TYPE_REVIEW_QUOTE )
				$quotes[] = [
					'text' => $text->getValue(),
					'author' => $text->getAuthor()
				];
		}

		return $quotes;
	}
	
	/**
	* Get Bio Notes
	*
	* @return array
	*/
	public function getBiograhpicalNotes(){

		$texts = $this->getTexts();
		$notes = [];

		foreach($texts as $text){
			if( $text->getType() == OtherText::TYPE_BIOGRAPHICAL_NOTE )
				$notes[] = $text->getValue();
		}

		return $notes;
	}

	public function getPrizes(){
		if( $this->version >= '3.0' )
			return $this->get('CollateralDetail/Prize', 'Prize');
		else
			return $this->get('Prize');
	}

	public function getPrizesData(){
		return array_map(function($award){ return $award->getData(); }, $this->getPrizes());
	}


	/**
	* Get Edition
	* 
	* @return string
	*/
	public function getEdition(){
		if( $this->version >= '3.0' )
			return $this->get('DescriptiveDetail/EditionType')[0]->nodeValue;
		else
			return $this->get('EditionTypeCode')[0]->nodeValue;
	}
	
	/**
	* Get Publish Date
	* 
	* @return string
	*/
	public function getPublishDate(){
		// @TODO: 3.0 has more data such as `PublishingDateRole` that may need fleshed out
		if( $this->version >= '3.0' )
			return $this->get('PublishingDetail/PublishingDate/Date')[0]->nodeValue;
		else
			return $this->get('PublicationDate')[0]->nodeValue;
	}
	
	/**
	* Get Publish Date
	* 
	* @return string
	*/
	public function getFirstImprintName(){
		// @TODO: many imprints can be set, we should support grabbing them all
		if( $this->version >= '3.0' )
			return $this->get('PublishingDetail/Imprint/ImprintName')[0]->nodeValue;
		else
			return $this->get('Imprint/ImprintName')[0]->nodeValue;
	}
	
	/**
	* Get First Publisher Name
	* 
	* @return string
	*/
	public function getFirstPublisherName(){
		// @TODO: many publishers can be set, we should support grabbing them all
		if( $this->version >= '3.0' )
			return $this->get('PublishingDetail/Publisher/PublisherName')[0]->nodeValue;
		else
			return $this->get('Publisher/PublisherName')[0]->nodeValue;
	}
	
	/**
	* Get Copyright Year
	* 
	* @return string
	*/
	public function getCopyrightYear(){
		// @TODO: 3.0 has a more robust out `CopyrightStatement` that should probably be used
		if( $this->version >= '3.0' )
			return $this->get('PublishingDetail/CopyrightStatement/CopyrightYear')[0]->nodeValue;
		else{
			$year = $this->get('CopyrightYear')[0]->nodeValue;
			if( !$year ) $year = $this->get('CopyrightStatement/CopyrightYear')[0]->nodeValue;
			return $year;
		}
	}
	
	
	/**
	* Get Copyright Statement
	* 
	* @return string
	*/
	public function getCopyrightStatement(){
		
		$prefix = $this->version >= '3.0' ? 'PublishingDetail/CopyrightStatement' : 'CopyrightStatement';
		
		$name = $this->get($prefix.'/CopyrightOwner/CorporateName')[0]->nodeValue;
			
		if( !$name )
			$name = $this->get($prefix.'/CopyrightOwner/PersonName')[0]->nodeValue;
		
		$year = $this->getCopyrightYear();
		
		return $year.($name?' '.$name:'');
	}

	/**
	 * Get Main Subject BISAC
	 *
	 * @return string Returns the main subject category code
	 */
	public function getMainSubjectBISAC(){
		if( $this->version >= '3.0'){
			$subjects = $this->get('DescriptiveDetail/Subject', 'Subject');
			foreach($subjects as $subject){
				if( $subject->getScheme() == Subject::SCHEME_BISAC_SUBJECT_HEADING ){
					if( $subject->isMainSubject() )
						return $subject->getValue();
				}
			}
		}else{
			return $this->get('BASICMainSubject')[0]->nodeValue;
		}
	}

	/**
	 * Get Other Subject BISACs
	 *
	 * @return array Returns array of other "non-main" subject category
	 */
	public function getOtherSubjectBISACs(){
		if ($this->version >= '3.0')
			$subjects = $this->get('DescriptiveDetail/Subject', 'Subject');
		else
			$subjects = $this->get('Subject', 'Subject');

		$others = [];

		foreach($subjects as $subject){
			if( $subject->getScheme() == Subject::SCHEME_BISAC_SUBJECT_HEADING ){
				if( !$subject->isMainSubject() )
					$others[] = $subject->getValue();
			}
		}

		return $others;
	}

	/**
	 * Get Keywords
	 *
	 * @return string Returns the keywords
	 */
	public function getKeywords()
	{
		if ($this->version >= '3.0')
			$subjects = $this->get('DescriptiveDetail/Subject', 'Subject');
		else
			$subjects = $this->get('Subject', 'Subject');

		foreach ($subjects as $subject) {
			if ($subject->getScheme() == Subject::SCHEME_KEYWORDS)
				return $subject->getText();
		}
		return "";
	}
}

?>
