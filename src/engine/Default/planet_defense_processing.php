<?php declare(strict_types=1);

use Smr\Request;

$session = Smr\Session::getInstance();
$var = $session->getCurrentVar();
$player = $session->getPlayer();
$ship = $player->getShip();

if (!$player->isLandedOnPlanet()) {
	create_error('You are not on a planet!');
}

$amount = Request::getInt('amount');
if ($amount <= 0) {
	create_error('You must actually enter an amount > 0!');
}
if ($player->getNewbieTurns() > 0) {
	create_error('You can\'t drop defenses under newbie protection!');
}
// get a planet from the sector where the player is in
$planet = $player->getSectorPlanet();

$type_id = $var['type_id'];
$action = Request::get('action');
// transfer to ship
if ($action == 'Ship') {
	if ($type_id == HARDWARE_SHIELDS) {
		// do we want transfer more than we have?
		if ($amount > $planet->getShields()) {
			create_error('You can\'t take more shields from planet than are on it!');
		}

		// do we want to transfer more than we can carry?
		if ($amount > $ship->getMaxShields() - $ship->getShields()) {
			create_error('You can\'t take more shields than you can carry!');
		}

		// now transfer
		$planet->decreaseShields($amount);
		$ship->increaseShields($amount);
		$player->log(LOG_TYPE_PLANETS, 'Player takes ' . $amount . ' shields from planet.');
	} elseif ($type_id == HARDWARE_COMBAT) {
		// do we want transfer more than we have?
		if ($amount > $planet->getCDs()) {
			create_error('You can\'t take more drones from planet than are on it!');
		}

		// do we want to transfer more than we can carry?
		if ($amount > $ship->getMaxCDs() - $ship->getCDs()) {
			create_error('You can\'t take more drones than you can carry!');
		}

		// now transfer
		$planet->decreaseCDs($amount);
		$ship->increaseCDs($amount);
		$player->log(LOG_TYPE_PLANETS, 'Player takes ' . $amount . ' drones from planet.');
	} elseif ($type_id == HARDWARE_ARMOUR) {
		// do we want transfer more than we have?
		if ($amount > $planet->getArmour()) {
			create_error('You can\'t take more armour from planet than are on it!');
		}

		// do we want to transfer more than we can carry?
		if ($amount > $ship->getMaxArmour() - $ship->getArmour()) {
			create_error('You can\'t take more armour than you can carry!');
		}

		// now transfer
		$planet->decreaseArmour($amount);
		$ship->increaseArmour($amount);
		$player->log(LOG_TYPE_PLANETS, 'Player takes ' . $amount . ' armour from planet.');
	}

} elseif ($action == 'Planet') {
	if ($type_id == HARDWARE_SHIELDS) {
		// does the user wants to transfer shields?

		// do we want transfer more than we have?
		if ($amount > $ship->getShields()) {
			create_error('You can\'t transfer more shields than you carry!');
		}

		// do we want to transfer more than the planet can hold?
		if ($amount + $planet->getShields() > $planet->getMaxShields()) {
			create_error('The planet can\'t hold more than ' . $planet->getMaxShields() . ' shields!');
		}

		// now transfer
		$planet->increaseShields($amount);
		$ship->decreaseShields($amount);
		$player->log(LOG_TYPE_PLANETS, 'Player puts ' . $amount . ' shields on planet.');
	} elseif ($type_id == HARDWARE_COMBAT) {
		// does the user wants to transfer drones?

		// do we want transfer more than we have?
		if ($amount > $ship->getCDs()) {
			create_error('You can\'t transfer more combat drones than you carry!');
		}

		// do we want to transfer more than we can carry?
		if ($amount + $planet->getCDs() > $planet->getMaxCDs()) {
			create_error('The planet can\'t hold more than ' . $planet->getMaxCDs() . ' drones!');
		}

		// now transfer
		$planet->increaseCDs($amount);
		$ship->decreaseCDs($amount);
		$player->log(LOG_TYPE_PLANETS, 'Player puts ' . $amount . ' drones on planet.');
	} elseif ($type_id == HARDWARE_ARMOUR) {
		// does the user wish to transfare armour?

		// do we want transfer more than we have?
		if ($amount >= $ship->getArmour()) {
			create_error('You can\'t transfer more armour than what you carry minus one!');
		}

		// do we want to transfer more than we can carry?
		if ($amount + $planet->getArmour() > $planet->getMaxArmour()) {
			create_error('The planet can\'t hold more than ' . $planet->getMaxArmour() . ' armour!');
		}

		// now transfer
		$planet->increaseArmour($amount);
		$ship->decreaseArmour($amount);
		$player->log(LOG_TYPE_PLANETS, 'Player puts ' . $amount . ' armour on planet.');
	}

} else {
	create_error('You must choose if you want to transfer to planet or to the ship!');
}

Page::create('planet_defense.php')->go();
