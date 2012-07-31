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
 * Does the actual XML parsing and creates high-level objects, which will be
 * passed back to the Parser class.
 */
class XMLHandler {

	/**
	 * Short tags to reference names mapping.
	 *
	 * Generated with:
	 * grep -E '^[	 ]*(refname|shortname)' ONIX_BookProduct_3.0_reference.dtd | cut -f 2 | cut -d \  -f 1,4 | sed -e 's/ /=/' -e "/^shortname/s/\$/; echo \"'\\\$shortname' => '\\\$refname',\"/" | sh
	 */
	protected static $short2ref = array(
		'addressee' => 'Addressee',
		'addresseeidentifier' => 'AddresseeIdentifier',
		'm380' => 'AddresseeIDType',
		'x300' => 'AddresseeName',
		'b046' => 'Affiliation',
		'agentidentifier' => 'AgentIdentifier',
		'j400' => 'AgentIDType',
		'j401' => 'AgentName',
		'j402' => 'AgentRole',
		'alternativename' => 'AlternativeName',
		'ancillarycontent' => 'AncillaryContent',
		'x423' => 'AncillaryContentType',
		'x424' => 'AncillaryContentDescription',
		'audience' => 'Audience',
		'b073' => 'AudienceCode',
		'b204' => 'AudienceCodeType',
		'b205' => 'AudienceCodeTypeName',
		'b206' => 'AudienceCodeValue',
		'b207' => 'AudienceDescription',
		'audiencerange' => 'AudienceRange',
		'b075' => 'AudienceRangePrecision',
		'b074' => 'AudienceRangeQualifier',
		'b076' => 'AudienceRangeValue',
		'barcode' => 'Barcode',
		'x312' => 'BarcodeType',
		'batchbonus' => 'BatchBonus',
		'j264' => 'BatchQuantity',
		'bible' => 'Bible',
		'b352' => 'BibleContents',
		'b354' => 'BiblePurpose',
		'b356' => 'BibleReferenceLocation',
		'b357' => 'BibleTextFeature',
		'b355' => 'BibleTextOrganization',
		'b353' => 'BibleVersion',
		'b044' => 'BiographicalNote',
		'k169' => 'BookClubAdoption',
		'j375' => 'CBO',
		'x434' => 'CitationNote',
		'citedcontent' => 'CitedContent',
		'x430' => 'CitedContentType',
		'b209' => 'CityOfPublication',
		'collateraldetail' => 'CollateralDetail',
		'collection' => 'Collection',
		'collectionidentifier' => 'CollectionIdentifier',
		'x344' => 'CollectionIDType',
		'collectionsequence' => 'CollectionSequence',
		'x481' => 'CollectionSequenceNumber',
		'x479' => 'CollectionSequenceType',
		'x480' => 'CollectionSequenceTypeName',
		'x329' => 'CollectionType',
		'comparisonproductprice' => 'ComparisonProductPrice',
		'complexity' => 'Complexity',
		'b078' => 'ComplexityCode',
		'b077' => 'ComplexitySchemeIdentifier',
		'b289' => 'ComponentNumber',
		'b288' => 'ComponentTypeName',
		'conference' => 'Conference',
		'b341' => 'ConferenceAcronym',
		'b054' => 'ConferenceDate',
		'b052' => 'ConferenceName',
		'b053' => 'ConferenceNumber',
		'b055' => 'ConferencePlace',
		'b051' => 'ConferenceRole',
		'conferencesponsor' => 'ConferenceSponsor',
		'b391' => 'ConferenceSponsorIDType',
		'conferencesponsoridentifier' => 'ConferenceSponsorIdentifier',
		'b342' => 'ConferenceTheme',
		'x299' => 'ContactName',
		'x427' => 'ContentAudience',
		'contentdate' => 'ContentDate',
		'x429' => 'ContentDateRole',
		'contentdetail' => 'ContentDetail',
		'contentitem' => 'ContentItem',
		'contributor' => 'Contributor',
		'contributordate' => 'ContributorDate',
		'x417' => 'ContributorDateRole',
		'b048' => 'ContributorDescription',
		'contributorplace' => 'ContributorPlace',
		'x418' => 'ContributorPlaceRelator',
		'b035' => 'ContributorRole',
		'b049' => 'ContributorStatement',
		'k168' => 'CopiesSold',
		'copyrightowner' => 'CopyrightOwner',
		'b392' => 'CopyrightOwnerIDType',
		'copyrightowneridentifier' => 'CopyrightOwnerIdentifier',
		'copyrightstatement' => 'CopyrightStatement',
		'b087' => 'CopyrightYear',
		'b047' => 'CorporateName',
		'x443' => 'CorporateNameInverted',
		'x449' => 'CountriesIncluded',
		'x451' => 'CountriesExcluded',
		'b251' => 'CountryCode',
		'x316' => 'CountryOfManufacture',
		'b083' => 'CountryOfPublication',
		'j152' => 'CurrencyCode',
		'x475' => 'CurrencyZone',
		'b306' => 'Date',
		'j260' => 'DateFormat',
		'm186' => 'DefaultCurrencyCode',
		'm184' => 'DefaultLanguageOfText',
		'x310' => 'DefaultPriceType',
		'a199' => 'DeletionText',
		'descriptivedetail' => 'DescriptiveDetail',
		'discount' => 'Discount',
		'x469' => 'DiscountAmount',
		'j364' => 'DiscountCode',
		'j363' => 'DiscountCodeType',
		'j378' => 'DiscountCodeTypeName',
		'discountcoded' => 'DiscountCoded',
		'x317' => 'EpubTechnicalProtection',
		'b057' => 'EditionNumber',
		'b058' => 'EditionStatement',
		'x419' => 'EditionType',
		'b217' => 'EditionVersionNumber',
		'j272' => 'EmailAddress',
		'b325' => 'EndDate',
		'epubusageconstraint' => 'EpubUsageConstraint',
		'epubusagelimit' => 'EpubUsageLimit',
		'x319' => 'EpubUsageStatus',
		'x318' => 'EpubUsageType',
		'x321' => 'EpubUsageUnit',
		'j302' => 'ExpectedDate',
		'extent' => 'Extent',
		'b218' => 'ExtentType',
		'b220' => 'ExtentUnit',
		'b219' => 'ExtentValue',
		'x421' => 'ExtentValueRoman',
		'j271' => 'FaxNumber',
		'x440' => 'FeatureNote',
		'x439' => 'FeatureValue',
		'b286' => 'FirstPageNumber',
		'j265' => 'FreeQuantity',
		'x412' => 'FromLanguage',
		'header' => 'Header',
		'b233' => 'IDTypeName',
		'b244' => 'IDValue',
		'x422' => 'Illustrated',
		'b062' => 'IllustrationsNote',
		'imprint' => 'Imprint',
		'imprintidentifier' => 'ImprintIdentifier',
		'x445' => 'ImprintIDType',
		'b079' => 'ImprintName',
		'k167' => 'InitialPrintRun',
		'b040' => 'KeyNames',
		'language' => 'Language',
		'b252' => 'LanguageCode',
		'b253' => 'LanguageRole',
		'b287' => 'LastPageNumber',
		'x446' => 'LatestReprintNumber',
		'b042' => 'LettersAfterNames',
		'b284' => 'LevelSequenceNumber',
		'x432' => 'ListName',
		'locationidentifier' => 'LocationIdentifier',
		'j377' => 'LocationIDType',
		'j349' => 'LocationName',
		'x425' => 'MainSubject',
		'b063' => 'MapScale',
		'market' => 'Market',
		'marketdate' => 'MarketDate',
		'j408' => 'MarketDateRole',
		'marketpublishingdetail' => 'MarketPublishingDetail',
		'j407' => 'MarketPublishingStatus',
		'x406' => 'MarketPublishingStatusNote',
		'measure' => 'Measure',
		'c094' => 'Measurement',
		'x315' => 'MeasureType',
		'c095' => 'MeasureUnitCode',
		'm183' => 'MessageNote',
		'm180' => 'MessageNumber',
		'm181' => 'MessageRepeat',
		'j263' => 'MinimumOrderQuantity',
		'nameassubject' => 'NameAsSubject',
		'nameidentifier' => 'NameIdentifier',
		'x415' => 'NameIDType',
		'b041' => 'NamesAfterKey',
		'b039' => 'NamesBeforeKey',
		'x414' => 'NameType',
		'newsupplier' => 'NewSupplier',
		'x411' => 'NoCollection',
		'n339' => 'NoContributor',
		'n386' => 'NoEdition',
		'a002' => 'NotificationType',
		'b257' => 'Number',
		'x323' => 'NumberOfCopies',
		'b125' => 'NumberOfIllustrations',
		'x322' => 'NumberOfItemsOfThisForm',
		'b061' => 'NumberOfPages',
		'ONIXmessage' => 'ONIXMessage',
		'j350' => 'OnHand',
		'j351' => 'OnOrder',
		'onorderdetail' => 'OnOrderDetail',
		'j144' => 'OrderTime',
		'j145' => 'PackQuantity',
		'pagerun' => 'PageRun',
		'x410' => 'PartNumber',
		'b337' => 'Percent',
		'j267' => 'DiscountPercent',
		'b036' => 'PersonName',
		'b037' => 'PersonNameInverted',
		'x433' => 'PositionOnList',
		'x313' => 'PositionOnProduct',
		'b247' => 'PrefixToKey',
		'price' => 'Price',
		'j151' => 'PriceAmount',
		'x468' => 'PriceCode',
		'pricecoded' => 'PriceCoded',
		'x465' => 'PriceCodeType',
		'x477' => 'PriceCodeTypeName',
		'pricecondition' => 'PriceCondition',
		'priceconditionquantity' => 'PriceConditionQuantity',
		'x464' => 'PriceConditionQuantityType',
		'x463' => 'PriceConditionType',
		'pricedate' => 'PriceDate',
		'x476' => 'PriceDateRole',
		'j239' => 'PricePer',
		'j261' => 'PriceQualifier',
		'j266' => 'PriceStatus',
		'x462' => 'PriceType',
		'j262' => 'PriceTypeDescription',
		'x416' => 'PrimaryContentType',
		'x457' => 'PrimaryPart',
		'x301' => 'PrintedOnProduct',
		'prize' => 'Prize',
		'g129' => 'PrizeCode',
		'g128' => 'PrizeCountry',
		'g343' => 'PrizeJury',
		'g126' => 'PrizeName',
		'g127' => 'PrizeYear',
		'product' => 'Product',
		'j396' => 'ProductAvailability',
		'productclassification' => 'ProductClassification',
		'b275' => 'ProductClassificationCode',
		'b274' => 'ProductClassificationType',
		'x314' => 'ProductComposition',
		'productcontact' => 'ProductContact',
		'productcontactidentifier' => 'ProductContactIdentifier',
		'x483' => 'ProductContactIDType',
		'x484' => 'ProductContactName',
		'x482' => 'ProductContactRole',
		'b385' => 'ProductContentType',
		'b012' => 'ProductForm',
		'b014' => 'ProductFormDescription',
		'b333' => 'ProductFormDetail',
		'productformfeature' => 'ProductFormFeature',
		'b336' => 'ProductFormFeatureDescription',
		'b334' => 'ProductFormFeatureType',
		'b335' => 'ProductFormFeatureValue',
		'b221' => 'ProductIDType',
		'productidentifier' => 'ProductIdentifier',
		'b225' => 'ProductPackaging',
		'productpart' => 'ProductPart',
		'x455' => 'ProductRelationCode',
		'productsupply' => 'ProductSupply',
		'professionalaffiliation' => 'ProfessionalAffiliation',
		'b045' => 'ProfessionalPosition',
		'k165' => 'PromotionCampaign',
		'k166' => 'PromotionContact',
		'publisher' => 'Publisher',
		'publisheridentifier' => 'PublisherIdentifier',
		'x447' => 'PublisherIDType',
		'b081' => 'PublisherName',
		'publisherrepresentative' => 'PublisherRepresentative',
		'publishingdate' => 'PublishingDate',
		'x448' => 'PublishingDateRole',
		'publishingdetail' => 'PublishingDetail',
		'b291' => 'PublishingRole',
		'b394' => 'PublishingStatus',
		'b395' => 'PublishingStatusNote',
		'x320' => 'Quantity',
		'x466' => 'QuantityUnit',
		'x467' => 'DiscountType',
		'a001' => 'RecordReference',
		'recordsourceidentifier' => 'RecordSourceIdentifier',
		'x311' => 'RecordSourceIDType',
		'a197' => 'RecordSourceName',
		'a194' => 'RecordSourceType',
		'b398' => 'RegionCode',
		'x450' => 'RegionsIncluded',
		'x452' => 'RegionsExcluded',
		'reissue' => 'Reissue',
		'j365' => 'ReissueDate',
		'j366' => 'ReissueDescription',
		'relatedmaterial' => 'RelatedMaterial',
		'relatedproduct' => 'RelatedProduct',
		'relatedwork' => 'RelatedWork',
		'religioustext' => 'ReligiousText',
		'religioustextfeature' => 'ReligiousTextFeature',
		'b359' => 'ReligiousTextFeatureCode',
		'b360' => 'ReligiousTextFeatureDescription',
		'b358' => 'ReligiousTextFeatureType',
		'b376' => 'ReligiousTextIdentifier',
		'k309' => 'ReprintDetail',
		'x436' => 'ResourceContentType',
		'resourcefeature' => 'ResourceFeature',
		'x438' => 'ResourceFeatureType',
		'x441' => 'ResourceForm',
		'x435' => 'ResourceLink',
		'x437' => 'ResourceMode',
		'resourceversion' => 'ResourceVersion',
		'resourceversionfeature' => 'ResourceVersionFeature',
		'x442' => 'ResourceVersionFeatureType',
		'x456' => 'ROWSalesRightsType',
		'j269' => 'ReturnsCode',
		'j268' => 'ReturnsCodeType',
		'x460' => 'ReturnsCodeTypeName',
		'returnsconditions' => 'ReturnsConditions',
		'salesrights' => 'SalesRights',
		'b089' => 'SalesRightsType',
		'salesoutlet' => 'SalesOutlet',
		'salesoutletidentifier' => 'SalesOutletIdentifier',
		'b393' => 'SalesOutletIDType',
		'b382' => 'SalesOutletName',
		'salesrestriction' => 'SalesRestriction',
		'x453' => 'SalesRestrictionNote',
		'b381' => 'SalesRestrictionType',
		'x420' => 'ScriptCode',
		'sender' => 'Sender',
		'm379' => 'SenderIDType',
		'senderidentifier' => 'SenderIdentifier',
		'x298' => 'SenderName',
		'x307' => 'SentDateTime',
		'b034' => 'SequenceNumber',
		'x330' => 'SourceName',
		'x428' => 'SourceTitle',
		'x431' => 'SourceType',
		'b324' => 'StartDate',
		'stock' => 'Stock',
		'j297' => 'StockQuantityCode',
		'j293' => 'StockQuantityCodeType',
		'j296' => 'StockQuantityCodeTypeName',
		'stockquantitycoded' => 'StockQuantityCoded',
		'b389' => 'StudyBibleType',
		'subject' => 'Subject',
		'b069' => 'SubjectCode',
		'b070' => 'SubjectHeadingText',
		'b067' => 'SubjectSchemeIdentifier',
		'b171' => 'SubjectSchemeName',
		'b068' => 'SubjectSchemeVersion',
		'b029' => 'Subtitle',
		'b248' => 'SuffixToKey',
		'supplier' => 'Supplier',
		'x458' => 'SupplierCodeType',
		'x459' => 'SupplierCodeValue',
		'supplieridentifier' => 'SupplierIdentifier',
		'j345' => 'SupplierIDType',
		'j137' => 'SupplierName',
		'supplierowncoding' => 'SupplierOwnCoding',
		'j292' => 'SupplierRole',
		'supplydate' => 'SupplyDate',
		'x461' => 'SupplyDateRole',
		'supplydetail' => 'SupplyDetail',
		'supportingresource' => 'SupportingResource',
		'tax' => 'Tax',
		'x474' => 'TaxAmount',
		'x471' => 'TaxRateCode',
		'x472' => 'TaxRatePercent',
		'x473' => 'TaxableAmount',
		'x470' => 'TaxType',
		'j270' => 'TelephoneNumber',
		'territory' => 'Territory',
		'd104' => 'Text',
		'd107' => 'TextAuthor',
		'textcontent' => 'TextContent',
		'textitem' => 'TextItem',
		'b285' => 'TextItemIDType',
		'textitemidentifier' => 'TextItemIdentifier',
		'b290' => 'TextItemType',
		'b374' => 'TextSourceCorporate',
		'x426' => 'TextType',
		'b369' => 'ThesisPresentedTo',
		'b368' => 'ThesisType',
		'b370' => 'ThesisYear',
		'titledetail' => 'TitleDetail',
		'titleelement' => 'TitleElement',
		'x409' => 'TitleElementLevel',
		'b030' => 'TitlePrefix',
		'b043' => 'TitlesAfterNames',
		'b038' => 'TitlesBeforeNames',
		'x478' => 'TitleStatement',
		'b203' => 'TitleText',
		'b202' => 'TitleType',
		'b031' => 'TitleWithoutPrefix',
		'x413' => 'ToLanguage',
		'b384' => 'TradeCategory',
		'b249' => 'UnnamedPersons',
		'j192' => 'UnpricedItemType',
		'website' => 'Website',
		'b294' => 'WebsiteDescription',
		'b295' => 'WebsiteLink',
		'b367' => 'WebsiteRole',
		'workidentifier' => 'WorkIdentifier',
		'b201' => 'WorkIDType',
		'x454' => 'WorkRelationCode',
		'b020' => 'YearOfAnnual',
	);

	/**
	 * Whether the document that’s being parsed uses short tags or not.
	 */
	protected $shorttags = null;

	/**
	 * Holds an array of all the currently open elements. [0] is the root, the
	 * last value is the most recently opened element.
	 */
	protected $openelements = array();

	/**
	 * Holds the Expat XML parser, initialized in the constructor.
	 */
	protected $parser = null;

	/**
	 * Our DOMImplementation instance, created in the constructor.
	 */
	protected $domImpl = null;

	/**
	 * When a <Product> element appears, it will be converted into a DOM
	 * document, which will be stored here. If this is null, we’re not currently
	 * reading a <Product>.
	 */
	protected $productDOM = null;

	/**
	 * The most recently created element in $productDOM.
	 */
	protected $productElement = null;

	/**
	 * The callback to invoke for each found Product.
	 */
	protected $productHandler = null;

	/**
	 * The element “key” may only be opened directly under the element “value”.
	 * “value” may be '' meaning “root”. Use reference names for “key” and
	 * “value”. This is not intended to replace XSD checking of the input
	 * document, but rather some rudimentary checks.
	 */
	protected $restrictions = array(
		'ONIXMessage' => '',
		'Header'  => 'ONIXMessage@1',
		'Product' => 'ONIXMessage@1',
	);

	/**
	 * Retrieve the (translated) name of the most recently opened element.
	 *
	 * @return string The name of the element or an empty string if no element
	 *                has been opened yet. Will be passed through doc2ref().
	 */
	protected function getCurrentElementName() {
		$count = count($this->openelements);
		return $count
		     ? $this->doc2ref($this->openelements[$count - 1])
		     : '';
	}

	/**
	 * Handles an opened XML element.
	 *
	 * @param  resource $parser The XML parser.
	 * @param  string   $name   The name of the opened XML element.
	 * @param  array    $attrs  Associative array of the attributes.
	 * @return null
	 */
	protected function handleElementOpen($parser, $name, $attrs) {
		$level = count($this->openelements);
		// If this is the root element, set whether short tags are used or not.
		if ($level == 0) {
			switch ($name) {
				case 'ONIXMessage':
					$this->shorttags = false;
					break;
				case 'ONIXmessage':
					$this->shorttags = true;
					break;
				default:
					throw new ONIXException('the root element has to be ONIXMessage or ONIXmessage');
			}
		}
		// Translate the name, if needed and possible.
		$trans = $this->doc2ref($name);
		// If the element’s occurence is restricted, enforce it.
		if (array_key_exists($trans, $this->restrictions)) {
			$current = $this->getCurrentElementName();
			// Load and split restriction.
			$split = explode('@', $this->restrictions[$trans], 2);
			// Check whether the parent is allowed.
			$allowedParent = $split[0];
			if ($current != $allowedParent) {
				throw new XMLException("element '$trans' not allowed under '$current'");
			}
			// Check whether the level is allowed.
			$allowedLevel = (count($split) > 1)
			              ? (int)$split[1]
			              : null;
			if (($allowedLevel !== null) && ($level != $allowedLevel)) {
				throw new XMLException("element '$trans' not allowed in level $level");
			}
		}
		// Push the new element onto $this->openelements.
		array_push($this->openelements, $name);
		// Handle the actual parsing of elements we’re interested in.
		if ($trans == 'Product') {
			// If a <Product> element opens, start collecting its data.
			$this->productDOM = $this->domImpl->createDocument(null, $trans);
			$this->productElement = $this->productDOM->documentElement;
		} elseif ($this->productDOM) {
			// If we’re in a product, create and append the element.
			$el = $this->productDOM->createElement($trans);
			foreach ($attrs as $k => $v) {
				$el->setAttribute($k, $v);
			}
			$this->productElement->appendChild($el);
			$this->productElement = $el;
		}
	}

	/**
	 * Handles a closed XML element.
	 *
	 * @param  resource $parser The XML parser.
	 * @param  string   $name   The name of the closed XML element.
	 * @return null
	 */
	protected function handleElementClose($parser, $name) {
		// Expat guarantees that the element that’s being closed was acutally
		// the most recently opened one. Therefore, we can simply remove the
		// element from $this->openelements.
		array_pop($this->openelements);
		// If we’re currently in a product …
		if ($this->productDOM) {
			// Point to the parent element.
			$this->productElement = $this->productElement->parentNode;
			// If we just closed the <Product> element, normalize it (e.g. to
			// concatenate adjacent text nodes), fire up the handler and reset
			// $this->productDOM.
			if ($name == 'Product') {
				$this->productDOM->normalizeDocument();
				$this->handleProduct($this->productDOM);
				$this->productDOM = null;
			}
		}
	}

	/**
	 * Handles XML text data.
	 *
	 * @param  resource $parser The XML parser.
	 * @param  string   $text   The text.
	 * @return null
	 */
	protected function handleText($parser, $text) {
		// If we’re in a product, create and append a text node.
		if ($this->productDOM) {
			$text = $this->productDOM->createTextNode($text);
			$this->productElement->appendChild($text);
		}
	}

	/**
	 * Handles a complete <Product> DOM tree.
	 *
	 * @param  DOMDocument $dom The DOM document with a root node of <Product>.
	 * @return null
	 */
	protected function handleProduct($dom) {
		if ($this->productHandler) {
			$product = new Product($dom);
			call_user_func($this->productHandler, $product);
		}
	}

	/**
	 * Translates a short tag to a reference name, if the document is in short
	 * tag format.
	 *
	 * If the document is not in short tag format or there is no translation
	 * known, returns the input unmodified. Therefore you can pass reference
	 * names to this method without thinking about it.
	 *
	 * @param  string $tag The short tag to translate.
	 * @return string The (possibly) translated name.
	 */
	protected function doc2ref($tag) {
		return $this->usesShortTags()
		     ? $this->short2ref($tag)
		     : $tag;
	}

	/**
	 * Translates a short tag to a reference name.
	 *
	 * If there is no translation known, returns the input unmodified.
	 *
	 * @param  string $tag The short tag to translate.
	 * @return string The (possibly) translated name.
	 */
	protected function short2ref($tag) {
		$trimmed = trim((string)$tag);
		return array_key_exists($trimmed, self::$short2ref)
		     ? self::$short2ref[$trimmed]
		     : $tag;
	}

	/**
	 * Returns whether the document uses short tags or not.
	 *
	 * @return bool True if the root element is <ONIXmessage>, false if it is
	 *              <ONIXMessage>. Returns null if the root element has not been
	 *              read yet.
	 */
	protected function usesShortTags() {
		return $this->shorttags;
	}

	/**
	 * Create and initialize a new instance.
	 */
	public function __construct() {
		// Have a DOMImplementation handy.
		$this->domImpl = new \DOMImplementation();
		// Create a parser that outputs UTF-8.
		$parser = xml_parser_create('UTF-8');
		if (!is_resource($parser)) {
			throw new XMLException('could not create the XML parser');
		}
		// Configure the parser to not do case-folding.
		if (!xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0)) {
			throw new XMLException('could not disable case folding in the XML parser');
		}
		// Bind the parser’s callbacks on this object.
		if (!xml_set_object($parser, $this)) {
			throw new XMLException('could not bind the XML parser on this object');
		}
		// Bind handlers.
		if (!xml_set_element_handler($parser, 'handleElementOpen', 'handleElementClose')) {
			throw new XMLException('could not bind the XML element handlers');
		}
		if (!xml_set_character_data_handler($parser, 'handleText')) {
			throw new XMLException('could not bind the XML character data handlers');
		}
		// Store the parser.
		$this->parser = $parser;
	}

	/**
	 * Free resources used by this instance.
	 */
	public function __destruct() {
		// Free the XML parser.
		if ($this->parser !== null) {
			xml_parser_free($this->parser);
		}
	}

	/**
	 * Parse a chunk of data.
	 *
	 * @param  string  $data  A UTF-8 string of arbitrary length to parse.
	 * @param  bool    $final Whether this is the final piece of data.
	 * @return XMLHandler $this
	 */
	public function parse($data, $final = false) {
		// Simply pass the data to Expat.
		if (xml_parse($this->parser, $data, $final) != 1) {
			// An error occured. Retrieve info and throw an exception.
			$parser = $this->parser;
			$code = xml_get_error_code($parser);
			throw new XMLException(sprintf(
				'XML parsing error %d at %d:%d (byte %d): %s',
				$code,
				xml_get_current_line_number($parser),
				xml_get_current_column_number($parser),
				xml_get_current_byte_index($parser),
				xml_error_string($code)
			), $code);
		}
	}

	/**
	 * Set the handler for found Product instances.
	 *
	 * @param  callable $cb The handler that should be called. Receives a
	 *                      Product instance as its first parameter. Can be set
	 *                      to null to remove a possibly set handler.
	 * @return XMLHandler $this
	 */
	public function setProductHandler($cb) {
		if (!(is_callable($cb) || ($cb === null))) {
			throw new InternalException('no valid callback specified for setProductHandler');
		}
		$this->productHandler = $cb;
		return $this;
	}

}

?>
