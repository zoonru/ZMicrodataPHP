<?php

namespace Zoon\ZMicrodataPHP;

use DOMXPath;
use Webmozart\Assert\Assert;

/**
 * Extend the DOMDocument class with the Microdata API functions.
 */
final class MicrodataPhpDOMDocument extends \DOMDocument {
	/** @var \DOMXPath $xpath */
	protected $xpath;

	/**
	 * Retrieves a list of microdata items.
	 *
	 * @return list<MicrodataPhpDOMElement>
	 *   A DOMNodeList containing all top level microdata items.
	 *
	 * @todo Allow restriction by type string.
	 */
	public function getItems() {
		$result = [];
		foreach ($this->xpath()->query('//*[@itemscope and not(@itemprop)]') as $item) {
			Assert::isInstanceOf($item, MicrodataPhpDOMElement::class);
			$result[] = $item;
		}
		return $result;
	}

	/**
	 * @return list<MicrodataPhpDOMElement>
	 */
	public function getAllItems(): array {
		$result = [];
		foreach ($this->xpath()->query('//*[@itemscope]') as $item) {
			Assert::isInstanceOf($item, MicrodataPhpDOMElement::class);
			if ($item->itemProp() === []) {
				$result[] = $item;
			} elseif (!$item->hasRoot()) {
				$result[] = $item;
			}
		}
		return $result;
	}

	/**
	 * Creates a DOMXPath to query this document.
	 *
	 * @return DOMXPath
	 *   DOMXPath object.
	 */
	public function xpath() {
		if (!isset($this->xpath)) {
			$this->xpath = new \DOMXPath($this);
		}

		return $this->xpath;
	}
}
