<?php

declare(strict_types=1);

namespace Zoon\ZMicrodataPHP;

final class MicrodataPhpResult {

	public function __construct(
		/** @var list<MicrodataPhpResultObject> */
		public readonly array $items,
	) {
	}
}