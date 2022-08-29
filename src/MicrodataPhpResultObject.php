<?php

declare(strict_types=1);

namespace Zoon\MicrodataPHP;

final class MicrodataPhpResultObject {

	public function __construct(
		public readonly array $properties,
		public readonly ?array $type,
		public readonly ?string $id,
	) {
	}
}
