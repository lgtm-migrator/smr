<?php declare(strict_types=1);

use Smr\MovementType;
use Smr\Request;
use Smr\SectorLock;

$session = Smr\Session::getInstance();
$var = $session->getCurrentVar();
$player = $session->getPlayer();
$sector = $player->getSector();

if (!$player->getGame()->hasStarted()) {
	create_error('You cannot move until the game has started!');
}

$target = Request::getVarInt('target');

//allow hidden players (admins that don't play) to move without pinging, hitting mines, losing turns
if (in_array($player->getAccountID(), Globals::getHiddenPlayers())) {
	$player->setSectorID($target);
	$player->update();
	$sector->markVisited($player);
	Page::create('current_sector.php')->go();
}

// you can't move while on planet
if ($player->isLandedOnPlanet()) {
	create_error('You are on a planet! You must launch first!');
}

// if no 'target' is given we forward to plot
if (empty($target)) {
	create_error('Where do you want to go today?');
}

if ($player->getSectorID() == $target) {
	create_error('Hmmmm...if ' . $player->getSectorID() . '=' . $target . ' then that means...YOU\'RE ALREADY THERE! *cough*you\'re real smart*cough*');
}

if (!SmrSector::sectorExists($player->getGameID(), $target)) {
	create_error('The target sector doesn\'t exist!');
}

// If the Calculate Turn Cost button was pressed
if (Request::get('action', '') == 'Calculate Turn Cost') {
	$container = Page::create('sector_jump_calculate.php');
	$container['target'] = $target;
	$container->go();
}

if ($sector->hasForces()) {
	foreach ($sector->getForces() as $forces) {
		if ($forces->hasMines() && !$player->forceNAPAlliance($forces->getOwner())) {
			create_error('You cannot jump when there are hostile mines in the sector!');
		}
	}
}

// create sector object for target sector
$targetSector = SmrSector::getSector($player->getGameID(), $target);

$jumpInfo = $player->getJumpInfo($targetSector);
$turnsToJump = $jumpInfo['turn_cost'];
$maxMisjump = $jumpInfo['max_misjump'];

// check for turns
if ($player->getTurns() < $turnsToJump) {
	create_error('You don\'t have enough turns for that jump!');
}

// send scout msg
$sector->leavingSector($player, MovementType::Jump);

// Move the user around
// TODO: (Must be done while holding both sector locks)
$misjump = rand(0, $maxMisjump);
if ($misjump > 0) { // we missed the sector
	$distances = Plotter::findDistanceToX('Distance', $targetSector, false, null, null, $misjump);
	while (count($distances[$misjump]) == 0) {
		$misjump--;
	}

	$misjumpSector = array_rand($distances[$misjump]);
	$player->setSectorID($misjumpSector);
	unset($distances);
} else { // we hit it. exactly
	$player->setSectorID($targetSector->getSectorID());
}
$player->takeTurns($turnsToJump, $turnsToJump);

// log action
$player->log(LOG_TYPE_MOVEMENT, 'Jumps to sector: ' . $target . ' but hits: ' . $player->getSectorID());

$player->update();

// We need to release the lock on our old sector
$lock = SectorLock::getInstance();
$lock->release();

// We need a lock on the new sector so that more than one person isn't hitting the same mines
$lock->acquireForPlayer($player);

// get new sector object
$sector = $player->getSector();

// make current sector visible to him
$sector->markVisited($player);

// send scout msg
$sector->enteringSector($player, MovementType::Jump);

// If the new sector has mines...
require_once(LIB . 'Default/sector_mines.inc.php');
hit_sector_mines($player);

Page::create($var['target_page'])->go();
