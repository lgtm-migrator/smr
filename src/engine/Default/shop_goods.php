<?php declare(strict_types=1);

$template = Smr\Template::getInstance();
$session = Smr\Session::getInstance();
$var = $session->getCurrentVar();
$player = $session->getPlayer();
$ship = $player->getShip();

// create object from port we can work with
$port = $player->getSectorPort();

$tradeRestriction = $port->getTradeRestriction($player);
if ($tradeRestriction !== false) {
	create_error($tradeRestriction);
}

// topic
$template->assign('PageTopic', 'Port In Sector #' . $player->getSectorID());
$template->assign('Port', $port);

$player->log(LOG_TYPE_TRADING, 'Player examines port');
$searchedByFeds = false;

//The player is sent here after trading and sees this if his offer is accepted.
if (!empty($var['trade_msg'])) {
	$template->assign('TradeMsg', $var['trade_msg']);
} elseif ($player->getLastPort() != $player->getSectorID()) {
	// test if we are searched, but only if we hadn't a previous trade here

	$baseChance = PORT_SEARCH_BASE_CHANCE;
	if ($port->hasGood(GOODS_SLAVES)) {
		$baseChance -= PORT_SEARCH_REDUCTION_PER_EVIL_GOOD;
	}
	if ($port->hasGood(GOODS_WEAPONS)) {
		$baseChance -= PORT_SEARCH_REDUCTION_PER_EVIL_GOOD;
	}
	if ($port->hasGood(GOODS_NARCOTICS)) {
		$baseChance -= PORT_SEARCH_REDUCTION_PER_EVIL_GOOD;
	}

	if ($ship->isUnderground()) {
		$baseChance -= PORT_SEARCH_REDUCTION_FOR_EVIL_SHIP;
	}

	$rand = rand(1, 100);
	if ($rand <= $baseChance) {
		$searchedByFeds = true;
		$player->increaseHOF(1, ['Trade', 'Search', 'Total'], HOF_PUBLIC);
		if ($ship->hasIllegalGoods()) {
			$template->assign('IllegalsFound', true);
			$player->increaseHOF(1, ['Trade', 'Search', 'Caught', 'Number Of Times'], HOF_PUBLIC);
			//find the fine
			//get base for ports that dont happen to trade that good
			$GOODS = Globals::getGoods();
			$fine = $totalFine = $port->getLevel() *
			    (($ship->getCargo(GOODS_SLAVES) * $GOODS[GOODS_SLAVES]['BasePrice']) +
			     ($ship->getCargo(GOODS_WEAPONS) * $GOODS[GOODS_WEAPONS]['BasePrice']) +
			     ($ship->getCargo(GOODS_NARCOTICS) * $GOODS[GOODS_NARCOTICS]['BasePrice']));
			$player->increaseHOF($ship->getCargo(GOODS_SLAVES) + $ship->getCargo(GOODS_WEAPONS) + $ship->getCargo(GOODS_NARCOTICS), ['Trade', 'Search', 'Caught', 'Goods Confiscated'], HOF_PUBLIC);
			$player->increaseHOF($totalFine, ['Trade', 'Search', 'Caught', 'Amount Fined'], HOF_PUBLIC);
			$template->assign('TotalFine', $totalFine);

			if ($fine > $player->getCredits()) {
				$fine -= $player->getCredits();
				$player->decreaseCredits($player->getCredits());
				if ($fine > 0) {
					// because credits is 0 it will take money from bank
					$player->decreaseBank(min($fine, $player->getBank()));
					// leave insurance
					if ($player->getBank() < 5000) {
						$player->setBank(5000);
					}
				}
			} else {
				$player->decreaseCredits($fine);
			}

			//lose align and the good your carrying along with money
			$player->decreaseAlignment(5);

			$ship->setCargo(GOODS_SLAVES, 0);
			$ship->setCargo(GOODS_WEAPONS, 0);
			$ship->setCargo(GOODS_NARCOTICS, 0);
			$player->log(LOG_TYPE_TRADING, 'Player gets caught with illegals');

		} else {
			$template->assign('IllegalsFound', false);
			$player->increaseHOF(1, ['Trade', 'Search', 'Times Found Innocent'], HOF_PUBLIC);
			$player->increaseAlignment(1);
			$player->log(LOG_TYPE_TRADING, 'Player gains alignment at port');
		}
	}
}
$template->assign('SearchedByFeds', $searchedByFeds);

$player->setLastPort($player->getSectorID());

$container = Page::create('shop_goods_processing.php');

$boughtGoods = [];
foreach ($port->getVisibleGoodsBought($player) as $goodID) {
	$good = Globals::getGood($goodID);
	$container['good_id'] = $goodID;
	$good['HREF'] = $container->href();

	$amount = $port->getGoodAmount($goodID);
	$good['PortAmount'] = $amount;
	if ($amount < $ship->getEmptyHolds()) {
		$good['Amount'] = $amount;
	} else {
		$good['Amount'] = $ship->getEmptyHolds();
	}
	$boughtGoods[$goodID] = $good;
}

$soldGoods = [];
foreach ($port->getVisibleGoodsSold($player) as $goodID) {
	$good = Globals::getGood($goodID);
	$container['good_id'] = $goodID;
	$good['HREF'] = $container->href();

	$amount = $port->getGoodAmount($goodID);
	$good['PortAmount'] = $amount;
	if ($amount < $ship->getCargo($goodID)) {
		$good['Amount'] = $amount;
	} else {
		$good['Amount'] = $ship->getCargo($goodID);
	}
	$soldGoods[$goodID] = $good;
}

$template->assign('BoughtGoods', $boughtGoods);
$template->assign('SoldGoods', $soldGoods);

$container = Page::create('current_sector.php');
$template->assign('LeavePortHREF', $container->href());
