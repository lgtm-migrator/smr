<?php declare(strict_types=1);

$template = Smr\Template::getInstance();
$session = Smr\Session::getInstance();
$var = $session->getCurrentVar();
$player = $session->getPlayer();

$template->assign('PageTopic', 'BlackJack');
Menu::bar();

if ($player->hasNewbieTurns()) {
	$maxBet = 100;
	$maxBetMsg = 'Since you have newbie protection, your max bet is ' . $maxBet . '.';
} else {
	$maxBet = 10000;
	$maxBetMsg = 'Max bet is ' . $maxBet . '.';
}
$template->assign('MaxBet', $maxBet);
$template->assign('MaxBetMsg', $maxBetMsg);

$container = Page::create('bar_gambling_processing.php');
$container->addVar('LocationID');
$template->assign('PlayHREF', $container->href());
