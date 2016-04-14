<?php

/*
 * This file is part of the Panda framework Foundation component.
 *
 * (c) Ioannis Papikas <papikas.ioan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Panda\Foundation\prototypes\helpers;

/**
 * Document Object Model Helper file
 * 
 * This file is a helper containing tool functions
 * for generic functionality.
 * 
 * @version	0.1
 */
class DOMHelper
{
	/**
	 * Transforms a given css selector into an xpath query.
	 * 
	 * @param	string	$selector
	 * 		The css selector to search for in the html document.
	 * 		It does not support pseudo-* for the moment and only supports simple equality attribute-wise.
	 * 		Can hold multiple selectors separated with comma.
	 * 
	 * @return	string
	 * 		The corresponding xpath query.
	 */
	public static function CSSSelector2XPath($selector)
	{
		// _xpcm_ -> ',' {comma}
		// _xpsp_ -> ' ' {space}
		// _xpor_ -> ' or ' {xpath or clause}
	
		// Prepare css selector
		// Transform css to xpath
		$xpath = preg_replace("/[ \t\r\n\s]+/", " ", $selector);
		
		// Identify Attributes
		$xpath = str_replace("[", "[@", $xpath);
		
		// Identify IDs
		$xpath = preg_replace("/\#(-?[_a-zA-Z]+[_a-zA-Z0-9-]*)/", "[@id='$1']", $xpath);
		
		// Identify Classes
		$xpath = preg_replace("/\.(-?[_a-zA-Z]+[_a-zA-Z0-9-]*)/", "[contains(concat('_xpsp_'_xpcm_@class_xpcm_'_xpsp_')_xpcm_'_xpsp_$1_xpsp_')]", $xpath);
		//$xpath = preg_replace("/\.(-?[_a-zA-Z]+[_a-zA-Z0-9-]*)/", "[contains(@class_xpcm_'$1')]", $xpath);
		
		// Identify root
		if (empty($context)) {
			$xpath = preg_replace("/[^,]+/", "//$0", $xpath);
		}
			
		// Identify Descendants
		$xpath = preg_replace("/([^>~+])([ ])([^>~+])/", "$1//$2$3", $xpath);
		
		// Identify Children
		$xpath = str_replace(">", "/", $xpath);
		
		// Identify Direct Next siblings
		//$xpath = preg_replace("/\+[ ]+([^ >+~,\/]*(\[.*?\])?[^ >+~,\/]*)/", "/following-sibling::$1[1]", $xpath);
		// Identify Next siblings
		//$xpath = preg_replace("/\~[ ]+([^ >+~,\/]*(\[.*?\])?[^ >+~,\/]*)/", "/following-sibling::$1", $xpath);
	
		// Identify multiple selectors
		$xpath = str_replace(" ", "", $xpath);
		$xpath = str_replace(",", " | ", $xpath);
		
		// Identify "orphans" [no element, just attributes]
		$xpath = str_replace("/[", "/*[", $xpath);
		
		// Restore commas, spaces and or in functions
		$xpath = str_replace("_xpcm_", ",", $xpath);
		$xpath = str_replace("_xpsp_", " ", $xpath);
		//$xpath = str_replace("_xpor_", " or ", $xpath);
		
		// Return xpath
		return $xpath;
	}
}
?>