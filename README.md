# PONIpar – PHP ONIX Parser

![status](https://img.shields.io/badge/Status-Under%20Development-blue.svg)

## Main Features
* stream based: can read ONIX files of arbitrary length, because it does not keep the whole file in memory
* convenient variety of inputs: pass your data from a file, URL, stream, stdin or string
* callback based: you define a callable (function, method, closure) for every aspect of the ONIX file you are interested in, which will then be called by PONIpar while parsing (currently, only the Product callback is implemented)
* universal: uses reference names internally, but reads (and converts) short tags as well (not complete, currently only reference names fully work)
* high-level: PONIpar handles the XML parsing events and provides your callbacks with already parsed, high level object instances like “Product” and “Contributor” (not complete yet)
* modern: namespaced PHP 5.3 code
* flexible: since PONIpar doesn’t force you into a certain way of handling the data, you are free to code the way that best matches your requirements
* international: converts every input charset to UTF-8 and thus provides you with UTF-8 strings only (not implemented yet)

## Current Status
PONIpar is currently under development. It recognizes `<Product>` elements and calls a user-defined callback for each one found, passing a high-level `Product` object that currently allows accessing the product data via standard DOM calls and one or two high-level convenience classes and methods. The first high-level class (for ProductIdentifiers) is already there.

Other high-level classes have been built but are a work in progress and will likely need improving. You can see which ones are available by looking in [ProductSubitem](https://github.com/kjantzer/PONIpar/tree/master/src/ProductSubitem) directory.

You _can_ use it in a production environment but you'll want to run tests to make sure the data you need is being parsed correctly. Some of the `<Product>` properties will have to be retrieved manually.

## TODO

* Add more `ProductSubitems`

## Example Usage

Set which classes we are going to reference at the top of your file. We do this so we can use shorter class names.

```php
use PONIpar\Parser;
use PONIpar\ProductSubitem\ProductIdentifier;
use PONIpar\ProductSubitem\Title;
use PONIpar\ProductSubitem\Contributor;
use PONIpar\ProductSubitem\Extent;
use PONIpar\ProductSubitem\SupplyDetail;
```

Create a function to handle getting the data from each `<Product>`

```php
parse_product = function($product){
	
	$isbn_13 = $product->getIdentifier(ProductIdentifier::TYPE_ISBN13);
	
	// there can be multiple titles
	$titles = $product->getTitles();
	$main_title = '';
	
	// find the  main title
	foreach ($titles as $item) {
		if( $item->getType() == Title::TYPE_DISTINCTIVE_TITLE )
			$main_title = $item->getValue();
	}
	
	// get list of contributor names
	$contributors = $product->getContributors();
	$contributor_names = array_map(function($c){
		return $c->getName();
	}, $contributors);
	
	$bisac = $product->getMainSubjectBISAC();
	
	$description = $product->getMainDescription();
	
	$is_active = $product->isActive();
	
	// get supply info
	$supply_details = $product->getSupplyDetails();
	
	$supply_detail = $supply_details[0];
	
	$supply_detail->getOnSaleDate();
	$supply_detail->getPrices();
}
```

Begin parsing an ONIX file. The `parse_product` function above will be called for every `<Product>`.

```php
$parser = new Parser();	
$parser->useFile($file);
$parser->setProductHandler(parse_product);
$parser->parse();
```

## Requirements
PONIpar requires at least PHP 5.3 with the “XML Parser” extension.

## License
The software is provided under the terms of the new (3-clause) BSD license. Please see the file LICENSE for details.

## Author
PONIpar is authored by [UEBERBIT GmbH](http://www.ueberbit.de) with additional development by [Blackstone Audio, Inc.](http://www.blackstoneaudio.com)
