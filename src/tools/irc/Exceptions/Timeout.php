<?php declare(strict_types=1);

namespace Smr\Irc\Exceptions;

/**
 * Used when the IRC client times out so that we can reconnect.
 */
class Timeout extends \Exception {
}
