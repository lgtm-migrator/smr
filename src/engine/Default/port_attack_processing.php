<?php declare(strict_types=1);

use Smr\Database;
use Smr\Epoch;
use Smr\SectorLock;

$session = Smr\Session::getInstance();
$account = $session->getAccount();
$player = $session->getPlayer();
$ship = $player->getShip();
$sector = $player->getSector();

if ($player->hasNewbieTurns()) {
	create_error('You are under newbie protection!');
}
if ($player->hasFederalProtection()) {
	create_error('You are under federal protection!');
}
if ($player->isLandedOnPlanet()) {
	create_error('You cannot attack ports whilst on a planet!');
}
if ($player->getTurns() < TURNS_TO_SHOOT_PORT) {
	create_error('You do not have enough turns to attack this port!');
}
if (!$ship->hasWeapons() && !$ship->hasCDs()) {
	create_error('What are you going to do? Insult it to death?');
}
if (!$player->canFight()) {
	create_error('You are not allowed to fight!');
}

$port = $sector->getPort();

if (!$port->exists()) {
	create_error('This port does not exist.');
}


if ($port->isDestroyed()) {
	Page::create('port_attack.php')->go();
}


// ********************************
// *
// * P o r t   a t t a c k
// *
// ********************************

$results = ['Attackers' => ['TotalDamage' => 0]];

$attackers = $sector->getFightingTradersAgainstPort($player, $port);

$port->attackedBy($player, $attackers);

// take the turns and decloak all attackers
foreach ($attackers as $attacker) {
	$attacker->takeTurns(TURNS_TO_SHOOT_PORT);
	$attacker->getShip()->decloak();
}

$totalShieldDamage = 0;
foreach ($attackers as $attacker) {
	$playerResults = $attacker->shootPort($port);
	$results['Attackers']['Traders'][$attacker->getAccountID()] = $playerResults;
	$results['Attackers']['TotalDamage'] += $playerResults['TotalDamage'];
	foreach ($playerResults['Weapons'] as $weapon) {
		if (isset($weapon['ActualDamage'])) { // Only set if the weapon hits
			$totalShieldDamage += $weapon['ActualDamage']['Shield'];
		}
	}
}

// Planet downgrades only occur on non-shield damage
$downgradeDamage = $results['Attackers']['TotalDamage'] - $totalShieldDamage;
$results['Attackers']['Downgrades'] = $port->checkForDowngrade($downgradeDamage);

$results['Port'] = $port->shootPlayers($attackers);

$account->log(LOG_TYPE_PORT_RAIDING, 'Player attacks port, the port does ' . $results['Port']['TotalDamage'] . ', their team does ' . $results['Attackers']['TotalDamage'] . ' and downgrades ' . $results['Attackers']['Downgrades'] . ' levels.', $port->getSectorID());

$port->update();

$db = Database::getInstance();
$logId = $db->insert('combat_logs', [
	'game_id' => $db->escapeNumber($player->getGameID()),
	'type' => $db->escapeString('PORT'),
	'sector_id' => $db->escapeNumber($port->getSectorID()),
	'timestamp' => $db->escapeNumber(Epoch::time()),
	'attacker_id' => $db->escapeNumber($player->getAccountID()),
	'attacker_alliance_id' => $db->escapeNumber($player->getAllianceID()),
	'defender_id' => $db->escapeNumber(ACCOUNT_ID_PORT),
	'defender_alliance_id' => $db->escapeNumber(PORT_ALLIANCE_ID),
	'result' => $db->escapeObject($results, true),
]);

$sectorMessage = '[ATTACK_RESULTS]' . $logId;
foreach ($attackers as $attacker) {
	if (!$player->equals($attacker)) {
		$db->replace('sector_message', [
			'account_id' => $db->escapeNumber($attacker->getAccountID()),
			'game_id' => $db->escapeNumber($attacker->getGameID()),
			'message' => $db->escapeString($sectorMessage),
		]);
	}
}

// If player died they are now in another sector, and thus locks need reset
if ($player->isDead()) {
	saveAllAndReleaseLock(updateSession: false);
	// Grab the lock in the new sector to avoid reloading session
	SectorLock::getInstance()->acquireForPlayer($player);
}

// If they died on the shot they get to see the results
$container = Page::create('port_attack.php', skipRedirect: $player->isDead());
$container['results'] = $results;
$container->go();
