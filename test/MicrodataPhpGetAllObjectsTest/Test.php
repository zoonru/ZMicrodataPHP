<?php

declare(strict_types=1);

namespace Zoon\MicrodataPHP\Test\MicrodataPhpGetAllObjectsTest;

use PHPUnit\Framework\TestCase;
use Zoon\MicrodataPHP\MicrodataPhp;

final class Test extends TestCase {

	public function testWithRoot(): void {
		$microdataPhp = new MicrodataPhp(['html' => file_get_contents(__DIR__ . '/example/WithRoot.html')]);
		self::assertSame('{"items":[{"properties":{"author":[{"properties":{"name":["Name"]},"type":["https:\/\/schema.org\/Person"],"id":null}],"datePublished":["2022-01-01"],"reviewRating":[{"properties":{"ratingValue":["5"]},"type":["http:\/\/schema.org\/Rating"],"id":null}],"reviewBody":["Good site"]},"type":["https:\/\/schema.org\/Review"],"id":null},{"properties":{"author":[{"properties":{"name":["Name 2"]},"type":["https:\/\/schema.org\/Person"],"id":null}],"datePublished":["2022-01-01"],"reviewRating":[{"properties":{"ratingValue":["5"]},"type":["http:\/\/schema.org\/Rating"],"id":null}],"reviewBody":["Good site 2"]},"type":["https:\/\/schema.org\/Review"],"id":null}]}', json_encode($microdataPhp->getAllObjects()));
	}

	public function testWithoutRoot(): void {
		$microdataPhp = new MicrodataPhp(['html' => file_get_contents(__DIR__ . '/example/WithoutRoot.html')]);
		self::assertSame('{"items":[{"properties":{"author":[{"properties":{"name":["Name"]},"type":["https:\/\/schema.org\/Person"],"id":null}],"datePublished":["2022-01-01"],"reviewRating":[{"properties":{"ratingValue":["5"]},"type":["http:\/\/schema.org\/Rating"],"id":null}],"reviewBody":["Good site"]},"type":["https:\/\/schema.org\/Review"],"id":null},{"properties":{"name":["Name"]},"type":["https:\/\/schema.org\/Person"],"id":null},{"properties":{"ratingValue":["5"]},"type":["http:\/\/schema.org\/Rating"],"id":null},{"properties":{"author":[{"properties":{"name":["Name 2"]},"type":["https:\/\/schema.org\/Person"],"id":null}],"datePublished":["2022-01-01"],"reviewRating":[{"properties":{"ratingValue":["5"]},"type":["http:\/\/schema.org\/Rating"],"id":null}],"reviewBody":["Good site 2"]},"type":["https:\/\/schema.org\/Review"],"id":null},{"properties":{"name":["Name 2"]},"type":["https:\/\/schema.org\/Person"],"id":null},{"properties":{"ratingValue":["5"]},"type":["http:\/\/schema.org\/Rating"],"id":null}]}', json_encode($microdataPhp->getAllObjects()));
	}
}
