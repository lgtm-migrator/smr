<?php declare(strict_types=1);

namespace Smr;

use SmrPlayer;

/**
 * Collection of functions to help with displaying bounties at HQ.
 */
class Bounties {

	/**
	 * Returns a list of all active (not claimable) bounties for given location $type.
	 *
	 * @return array<array<string, mixed>>
	 */
	public static function getMostWanted(BountyType $type): array {
		$db = Database::getInstance();
		$session = Session::getInstance();
		$dbResult = $db->read('SELECT * FROM bounty WHERE game_id = ' . $db->escapeNumber($session->getGameID()) . ' AND type =' . $db->escapeString($type->value) . ' AND claimer_id = 0 ORDER BY amount DESC');
		$bounties = [];
		foreach ($dbResult->records() as $dbRecord) {
			$bounties[] = [
				'player' => SmrPlayer::getPlayer($dbRecord->getInt('account_id'), $session->getGameID()),
				'credits' => $dbRecord->getInt('amount'),
				'smr_credits' => $dbRecord->getInt('smr_credits'),
			];
		}
		return $bounties;
	}

}
