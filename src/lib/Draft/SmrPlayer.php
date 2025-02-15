<?php declare(strict_types=1);

class SmrPlayer extends AbstractSmrPlayer {

	public function getHome(): int {
		if ($this->hasAlliance()) {
			$leaderID = $this->getAlliance()->getLeaderID();
			$dbResult = $this->db->read('SELECT home_sector_id FROM draft_leaders WHERE account_id = ' . $this->db->escapeNumber($leaderID) . ' AND game_id = ' . $this->db->escapeNumber($this->getGameID()));
			if ($dbResult->hasRecord()) {
				return $dbResult->record()->getInt('home_sector_id');
			}
		}
		// Fallback to the standard home sector
		return parent::getHome();
	}

}
