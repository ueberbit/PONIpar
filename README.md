# PONIpar – PHP ONIX Parser

## Main Features
* stream based: can read ONIX files of arbitrary length, because it does not keep the whole file in memory
* convenient variety of inputs: pass your data from a file, stream, stdin or string
* callback based: you define a class containing methods for every aspect of the ONIX file you are interested in, which will then be called by PONIpar while parsing
* universal: uses reference names internally, but reads (and converts) short tags as well
* high-level: PONIpar handles the XML parsing events and provides your callbacks with already parsed, high level object instances like “Product” and “Contributor”
* modern: namespaced PHP 5.3 code
* flexible: since PONIpar doesn’t force you into a certain way of handling the data, you are free to code the way that best matches your requirements
* international: converts every input charset to UTF-8 and thus provides you with UTF-8 strings only

## Current Status
PONIpar is currently under development. It can _not_ parse anything yet. Stream opening works. Next thing will be adding the expat callbacks.
