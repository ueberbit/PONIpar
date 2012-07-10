# PONIpar – PHP ONIX Parser

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
PONIpar is currently under development. Stream opening works. The expat callbacks are there. It recognizes `<Product>` elements and calls a user-defined callback for each one found, passing a high-level `Product` object that currently allows accessing the product data via standard DOM calls, soon via high-level convenience classes and methods. The first high-level class (for ProductIdentifiers) is already there.
