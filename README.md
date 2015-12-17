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

You _can_ use it in a production environment if you want to, but you still have to do manual XML parsing of nearly all the `<Product>` properties.

## TODO

* Convert to [Composer package](https://getcomposer.org/)
* Add support for ONIX 3.0
* Add more `ProductSubitems`

## Requirements
PONIpar requires at least PHP 5.3 with the “XML Parser” extension.

## License
The software is provided under the terms of the new (3-clause) BSD license. Please see the file LICENSE for details.

## Author
PONIpar is authored by [UEBERBIT GmbH](http://www.ueberbit.de) with additional development by [Blackstone Audio, Inc.](http://www.blackstoneaudio.com)
