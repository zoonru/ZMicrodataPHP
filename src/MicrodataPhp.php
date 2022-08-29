<?php
/**
 * MicrodataPHP
 * http://github.com/linclark/MicrodataPHP
 * Copyright (c) 2011 Lin Clark
 * Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php
 *
 * Based on MicrodataJS
 * http://gitorious.org/microdatajs/microdatajs
 * Copyright (c) 2009-2011 Philip Jägenstedt
 */

namespace Zoon\ZMicrodataPHP;

/**
 * Extracts microdata from HTML.
 *
 * Currently supported formats:
 *   - PHP object
 *   - JSON
 */
final class MicrodataPhp {

	public readonly MicrodataPhpDOMDocument $dom;

	/**
	 * Constructs a MicrodataPhp object.
	 *
	 * @param array|string $config
	 *   The configuration options used in setting up the MicrodataPhpDOMDocument.
	 *   Options include:
	 *   - url: The url of the page to fetch.
	 *   - html: Alternatively, an HTML string can be used to set up the DOM.
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct($config) {
		// Convert a string to a config object for backwards compatibility.
		if (is_string($config)) {
			$config = array('url' => $config);
		}

		$dom = new MicrodataPhpDOMDocument();
		$dom->registerNodeClass('DOMDocument', MicrodataPhpDOMDocument::class);
		$dom->registerNodeClass('DOMElement', MicrodataPhpDOMElement::class);
		$dom->preserveWhiteSpace = false;

		// Prepare the DOM using either the URL or HTML string.
		if (isset($config['url'])) {
			@$dom->loadHTMLFile($config['url']);
		}
		else if (isset($config['html'])) {
			@$dom->loadHTML($config['html']);
		}
		else {
			throw new \InvalidArgumentException("Either a URL or an HTML string must be passed into the constructor.");
		}

		$this->dom = $dom;
	}

	/**
	 * Retrieve microdata as a PHP object.
	 *
	 * @return MicrodataPhpResult
	 *   An object with an 'items' property, which is an array of top level
	 *   microdata items as objects with the following properties:
	 *   - type: An array of itemtype(s) for the item, if specified.
	 *   - id: The itemid of the item, if specified.
	 *   - properties: An array of itemprops. Each itemprop is keyed by the
	 *     itemprop name and has its own array of values. Values can be strings
	 *     or can be other items, represented as objects.
	 *
	 * @todo MicrodataJS allows callers to pass in a selector for limiting the
	 *   parsing to one section of the document. Consider adding such
	 *   functionality.
	 */
	public function obj(): MicrodataPhpResult {
		return $this->getObjects($this->dom->getItems());
	}

	public function getAllObjects(): MicrodataPhpResult {
		return $this->getObjects($this->dom->getAllItems());
	}

	/**
	 * @param list<MicrodataPhpDOMElement> $elements
	 */
	private function getObjects(array $elements): MicrodataPhpResult {
		$items = [];
		foreach ($elements as $element) {
			$items[] = $this->getObject($element, []);
		}
		return new MicrodataPhpResult($items);
	}

	/**
	 * Retrieve microdata in JSON format.
	 *
	 * @return string
	 *   See obj().
	 *
	 * @todo MicrodataJS allows callers to pass in a function to format the JSON.
	 * Consider adding such functionality.
	 */
	public function json() {
		return json_encode($this->obj());
	}

	/**
	 * Helper function.
	 *
	 * In MicrodataJS, this is handled using a closure. PHP 5.3 allows closures,
	 * but cannot use $this within the closure. PHP 5.4 reintroduces support for
	 * $this. When PHP 5.3/5.4 are more widely supported on shared hosting,
	 * this function could be handled with a closure.
	 */
	protected function getObject(MicrodataPhpDOMElement $item, $memory): MicrodataPhpResultObject {
		$properties = [];
		foreach ($item->properties() as $elem) {
			if ($elem->itemScope()) {
				// Cannot use in_array() for comparison when values are arrays, so
				// iterate and check for equality with each item in memory.
				foreach ($memory as $memory_item) {
					if ($memory_item === $elem) {
						$value = 'ERROR';
					}
				}
				// If the item is not in memory, there are no cycles, and thus no error.
				// Recurse into the item to build out its properties.
				if (!isset($value)) {
					$memory[] = $item;
					$value = $this->getObject($elem, $memory);
					array_pop($memory);
				}
			} else {
				$value = $elem->itemValue();
			}
			foreach ($elem->itemProp() as $prop) {
				$properties[$prop][] = $value;
			}

			$value = null;
		}
		return new MicrodataPhpResultObject($properties, $item->itemType(), $item->itemId());
	}

}
