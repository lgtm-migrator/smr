<?php declare(strict_types=1);

$template = Smr\Template::getInstance();
$session = Smr\Session::getInstance();
$player = $session->getPlayer();

$template->assign('PageTopic', 'View Forces');

$db = Smr\Database::getInstance();
$db->query('SELECT *
			FROM sector_has_forces
			WHERE owner_id = ' . $db->escapeNumber($player->getAccountID()) . '
			AND game_id = ' . $db->escapeNumber($player->getGameID()) . '
			AND expire_time >= '.$db->escapeNumber(Smr\Epoch::time()) . '
			ORDER BY sector_id ASC');

$forces = array();
while ($db->nextRecord()) {
	$forces[] = SmrForce::getForce($player->getGameID(), $db->getInt('sector_id'), $db->getInt('owner_id'), false, $db);
}
$template->assign('Forces', $forces);
