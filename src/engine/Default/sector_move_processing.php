<?php declare(strict_types=1);

use Smr\MovementType;
use Smr\SectorLock;

require_once(LIB . 'Default/sector_mines.inc.php');

$session = Smr\Session::getInstance();
$var = $session->getCurrentVar();
$player = $session->getPlayer();
$sector = $player->getSector();

if (!$player->getGame()->hasStarted()) {
	create_error('You cannot move until the game has started!');
}

if ($var['target_sector'] == $player->getSectorID()) {
	Page::create($var['target_page'])->go();
}

if ($sector->getWarp() == $var['target_sector']) {
	$movement = MovementType::Warp;
	$turns = TURNS_PER_WARP;
} else {
	$movement = MovementType::Walk;
	$turns = TURNS_PER_SECTOR;
}

//allow hidden players (admins that don't play) to move without pinging, hitting mines, losing turns
if (in_array($player->getAccountID(), Globals::getHiddenPlayers())) {
	//make them pop on CPL
	$player->updateLastCPLAction();
	$player->setSectorID($var['target_sector']);
	$player->update();

	// get new sector object
	$sector = $player->getSector();
	$sector->markVisited($player);
	Page::create($var['target_page'])->go();
}

// you can't move while on planet
if ($player->isLandedOnPlanet()) {
	create_error('You can\'t activate your engine while you are on a planet!');
}

if ($player->getTurns() < $turns) {
	create_error('You don\'t have enough turns to move!');
}

if (!$sector->isLinked($var['target_sector'])) {
	create_error('You cannot move to that sector!');
}

// If not moving to your "green sector", you might hit mines...
if ($player->getLastSectorID() != $var['target_sector']) {
	// Update the "green sector"
	$player->setLastSectorID($var['target_sector']);
	hit_sector_mines($player);
}

// log action
$targetSector = SmrSector::getSector($player->getGameID(), $var['target_sector']);
$player->actionTaken('WalkSector', ['Sector' => $targetSector]);

// send scout msg
$sector->leavingSector($player, $movement);

// Move the user around
// TODO: (Must be done while holding both sector locks)
$player->setSectorID($var['target_sector']);
$player->takeTurns($turns, $turns);
$player->update();

// We need to release the lock on our old sector
$lock = SectorLock::getInstance();
$lock->release();

// We need a lock on the new sector so that more than one person isn't hitting the same mines
$lock->acquireForPlayer($player);

// get new sector object
$sector = $player->getSector();

//add that the player explored here if it hasnt been explored...for HoF
if (!$sector->isVisited($player)) {
	$player->increaseExperience(EXPLORATION_EXPERIENCE);
	$player->increaseHOF(EXPLORATION_EXPERIENCE, ['Movement', 'Exploration Experience Gained'], HOF_ALLIANCE);
	$player->increaseHOF(1, ['Movement', 'Sectors Explored'], HOF_ALLIANCE);
}
// make current sector visible to him
$sector->markVisited($player);

// send scout msgs
$sector->enteringSector($player, $movement);

// If you bump into mines while entering the target sector...
hit_sector_mines($player);

// otherwise
Page::create($var['target_page'])->go();
