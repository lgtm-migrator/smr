<?php declare(strict_types=1);

use Smr\Race;
use Smr\SectorLock;

// We can release the sector lock now because we know that the following
// code is read-only. This will help reduce sector lag and possible abuse.
SectorLock::getInstance()->release();

$session = Smr\Session::getInstance();
$var = $session->getCurrentVar();
$player = $session->hasGame() ? $session->getPlayer() : null;

$gameID = $var['AdminCreateGameID'] ?? $player->getGameID();
$adminCreate = isset($var['AdminCreateGameID']);

// NOTE: If the format of this file is changed in an incompatible way,
// make sure to update the SMR_FILE_VERSION!

$file = ';SMR1.6 Sectors File v' . SMR_FILE_VERSION . '
; Created on ' . date(DEFAULT_DATE_TIME_FORMAT) . '
[Races]
; Name = ID' . EOL;
foreach (Race::getAllNames() as $raceID => $raceName) {
	$file .= inify($raceName) . '=' . $raceID . EOL;
}

$file .= '[Goods]
; ID = Name, BasePrice' . EOL;
foreach (Globals::getGoods() as $good) {
	$file .= $good['ID'] . '=' . inify($good['Name']) . ',' . $good['BasePrice'] . EOL;
}

$file .= '[Weapons]
; Weapon = Race,Cost,Shield,Armour,Accuracy,Power level,Restriction
; Restriction: 0=none, 1=good, 2=evil, 3=newbie, 4=port, 5=planet' . EOL;
foreach (SmrWeaponType::getAllWeaponTypes() as $weapon) {
	$file .= inify($weapon->getName()) . '=' . inify($weapon->getRaceName()) . ',' . $weapon->getCost() . ',' . $weapon->getShieldDamage() . ',' . $weapon->getArmourDamage() . ',' . $weapon->getAccuracy() . ',' . $weapon->getPowerLevel() . ',' . $weapon->getBuyerRestriction()->value . EOL;
}

$file .= '[ShipEquipment]
; Name = Cost' . EOL;
$hardwares = Globals::getHardwareTypes();
foreach ($hardwares as $hardware) {
	$file .= inify($hardware['Name']) . '=' . $hardware['Cost'] . EOL;
}

$file .= '[Ships]
; Name = Race,Cost,TPH,Hardpoints,Power,Class,+Equipment (Optional),+Restrictions(Optional)
; Restrictions:Align(Integer)' . EOL;
foreach (SmrShipType::getAll() as $ship) {
	$file .= inify($ship->getName()) . '=' . inify($ship->getRaceName()) . ',' . $ship->getCost() . ',' . $ship->getSpeed() . ',' . $ship->getHardpoints() . ',' . $ship->getMaxPower() . ',' . $ship->getClass()->name;
	$shipEquip = [];
	foreach ($ship->getAllMaxHardware() as $hardwareID => $maxHardware) {
		$shipEquip[] = $hardwares[$hardwareID]['Name'] . '=' . $maxHardware;
	}
	if (!empty($shipEquip)) {
		$file .= ',ShipEquipment=' . implode(';', $shipEquip);
	}
	$file .= ',Restrictions=' . $ship->getRestriction()->value;
	$file .= EOL;
}

$file .= '[Locations]
; Name = +Sells' . EOL;
foreach (SmrLocation::getAllLocations() as $location) {
	$file .= inify($location->getName()) . '=';
	$locSells = '';
	if ($location->isWeaponSold()) {
		$locSells .= 'Weapons=';
		foreach ($location->getWeaponsSold() as $locWeapon) {
			$locSells .= $locWeapon->getName() . ';';
		}
		$locSells = substr($locSells, 0, -1) . ',';
	}
	if ($location->isHardwareSold()) {
		$locSells .= 'ShipEquipment=';
		foreach ($location->getHardwareSold() as $locHardware) {
			$locSells .= $locHardware['Name'] . ';';
		}
		$locSells = substr($locSells, 0, -1) . ',';
	}
	if ($location->isShipSold()) {
		$locSells .= 'Ships=';
		foreach ($location->getShipsSold() as $locShip) {
			$locSells .= $locShip->getName() . ';';
		}
		$locSells = substr($locSells, 0, -1) . ',';
	}
	if ($location->isBank()) {
		$locSells .= 'Bank=,';
	}
	if ($location->isBar()) {
		$locSells .= 'Bar=,';
	}
	if ($location->isHQ()) {
		$locSells .= 'HQ=,';
	}
	if ($location->isUG()) {
		$locSells .= 'UG=,';
	}
	if ($location->isFed()) {
		$locSells .= 'Fed=,';
	}
	if ($locSells != '') {
		$file .= substr($locSells, 0, -1);
	}
	$file .= EOL;
}

// Everything below here must be valid INI syntax (safe to parse)
$game = SmrGame::getGame($gameID);
$file .= '[Metadata]
FileVersion=' . SMR_FILE_VERSION . '
[Game]
Name=' . inify($game->getName()) . '
[Galaxies]
';
$galaxies = $game->getGalaxies();
foreach ($galaxies as $galaxy) {
	$file .= $galaxy->getGalaxyID() . '=' . $galaxy->getWidth() . ',' . $galaxy->getHeight() . ',' . $galaxy->getGalaxyType() . ',' . inify($galaxy->getName()) . ',' . $galaxy->getMaxForceTime() . EOL;
}


foreach ($galaxies as $galaxy) {
	// Efficiently construct the caches before proceeding
	$galaxy->getLocations();
	$galaxy->getPlanets();
	$galaxy->getForces();

	foreach ($galaxy->getSectors() as $sector) {
		$file .= '[Sector=' . $sector->getSectorID() . ']' . EOL;

		if (!$sector->isVisited($player) && $adminCreate === false) {
			continue;
		}

		foreach ($sector->getLinks() as $linkName => $link) {
			$file .= $linkName . '=' . $link . EOL;
		}
		if ($sector->hasWarp()) {
			$file .= 'Warp=' . $sector->getWarp() . EOL;
		}
		if (($adminCreate !== false && $sector->hasPort()) || is_object($player) && $sector->hasCachedPort($player)) {
			if ($adminCreate !== false) {
				$port = $sector->getPort();
			} else {
				$port = $sector->getCachedPort($player);
			}
			$file .= 'Port Level=' . $port->getLevel() . EOL;
			$file .= 'Port Race=' . $port->getRaceID() . EOL;
			if (!empty($port->getSellGoodIDs())) {
				$file .= 'Buys=' . implode(',', $port->getSellGoodIDs()) . EOL;
			}
			if (!empty($port->getBuyGoodIDs())) {
				$file .= 'Sells=' . implode(',', $port->getBuyGoodIDs()) . EOL;
			}
		}
		if ($sector->hasPlanet()) {
			$planetType = $sector->getPlanet()->getTypeID();
			$file .= 'Planet=' . $planetType . EOL;
		}
		if ($sector->hasLocation()) {
			$locationsString = 'Locations=';
			foreach ($sector->getLocations() as $location) {
				$locationsString .= inify($location->getName()) . ',';
			}
			$file .= substr($locationsString, 0, -1) . EOL;
		}
		if ($adminCreate === false && $sector->hasFriendlyForces($player)) {
			$forcesString = 'FriendlyForces=';
			foreach ($sector->getFriendlyForces($player) as $forces) {
				$forcesString .= inify($forces->getOwner()->getPlayerName()) . '=' . inify(Globals::getHardwareName(HARDWARE_MINE)) . '=' . $forces->getMines() . ';' . inify(Globals::getHardwareName(HARDWARE_COMBAT)) . '=' . $forces->getCDs() . ';' . inify(Globals::getHardwareName(HARDWARE_SCOUT)) . '=' . $forces->getSDs() . ',';
			}
			$file .= substr($forcesString, 0, -1) . EOL;
		}
	}
	SmrPort::clearCache();
	SmrForce::clearCache();
	SmrPlanet::clearCache();
	SmrSector::clearCache();
}

$size = strlen($file);

header('Pragma: public');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Cache-Control: private', false);
header('Content-Type: application/force-download');
header('Content-Disposition: attachment; filename="' . $game->getName() . '.smr"');
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . $size);

echo $file;

exit;
