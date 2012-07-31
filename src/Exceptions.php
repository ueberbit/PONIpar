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
 * Base class for all PONIpar exceptions.
 */
class PONIparException extends \Exception { }

/**
 * Internal sanity check failed.
 */
class InternalException extends PONIparException { }

/**
 * Opening or reading the input stream failed.
 */
class ReadException extends PONIparException { }

/**
 * Writing to a temp stream failed.
 */
class WriteException extends PONIparException { }

/**
 * Initializing or using the XML parser failed.
 */
class XMLException extends PONIparException { }

/**
 * Something in the ONIX data violates the standard.
 */
class ONIXException extends PONIparException { }

/**
 * An XML element that was being looked for was not found.
 */
class ElementNotFoundException extends PONIparException { }

/**
 * Too many XML elements were found (e.g. when only looking for one).
 */
class TooManyElementsFoundException extends PONIparException { }

?>
