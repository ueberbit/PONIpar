<?php

declare(encoding='UTF-8');
namespace PONIpar;

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

?>
