<?php declare(strict_types=1);

namespace SmrTest\lib\DefaultGame\Chess;

use PHPUnit\Framework\TestCase;
use Smr\Chess\Colour;

/**
 * @covers Smr\Chess\Colour
 */
class ColourTest extends TestCase {

	public function test_opposite(): void {
		self::assertSame(Colour::White, Colour::Black->opposite());
		self::assertSame(Colour::Black, Colour::White->opposite());
	}

}
