<?php declare(strict_types=1);

$db = Smr\Database::getInstance();
$session = Smr\Session::getInstance();
$var = $session->getCurrentVar();
$player = $session->getPlayer();

//only if we are coming from the bank screen do we unexempt selection first
if (isset($var['minVal'])) {
	$db->query('UPDATE alliance_bank_transactions SET exempt = 0 WHERE game_id = ' . $db->escapeNumber($player->getGameID()) . ' AND alliance_id = ' . $db->escapeNumber($player->getAllianceID()) . '
				AND transaction_id BETWEEN ' . $db->escapeNumber($var['minVal']) . ' AND ' . $db->escapeNumber($var['maxVal']));
}

if (Request::has('exempt')) {
	$trans_ids = array_keys(Request::getArray('exempt'));
	$db->query('UPDATE alliance_bank_transactions SET exempt = 1, request_exempt = 0 WHERE game_id = ' . $db->escapeNumber($player->getGameID()) . ' AND alliance_id = ' . $db->escapeNumber($player->getAllianceID()) . '
				AND transaction_id IN (' . $db->escapeArray($trans_ids) . ')');
}

$container = Page::create('skeleton.php');
if (isset($var['minVal'])) {
	$container['body'] = 'bank_alliance.php';
} else {
	$container['body'] = 'alliance_exempt_authorize.php';
}
$container->go();
