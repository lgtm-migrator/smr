<?php declare(strict_types=1);

use Smr\Database;

$template = Smr\Template::getInstance();
$session = Smr\Session::getInstance();
$account = $session->getAccount();
$var = $session->getCurrentVar();
$player = $session->getPlayer();

$alliance_id = $var['alliance_id'] ?? $player->getAllianceID();

$alliance = SmrAlliance::getAlliance($alliance_id, $player->getGameID());
$template->assign('PageTopic', $alliance->getAllianceDisplayName(false, true));
Menu::alliance($alliance_id);

$container = Page::create('alliance_stat_processing.php');
$container['alliance_id'] = $alliance_id;

$role_id = $player->getAllianceRole($alliance->getAllianceID());

$db = Database::getInstance();
$dbResult = $db->read('SELECT * FROM alliance_has_roles WHERE alliance_id = ' . $db->escapeNumber($alliance_id) . ' AND game_id = ' . $db->escapeNumber($player->getGameID()) . ' AND role_id = ' . $db->escapeNumber($role_id));
if ($dbResult->hasRecord()) {
	$dbRecord = $dbResult->record();
	$change_mod = $dbRecord->getBoolean('change_mod');
	$change_pass = $dbRecord->getBoolean('change_pass');
} else {
	$change_mod = false;
	$change_pass = false;
}
$change_chat = $player->getAllianceID() == $alliance_id && $player->isAllianceLeader();

$template->assign('FormHREF', $container->href());
$template->assign('Alliance', $alliance);

$template->assign('CanChangeDescription', $change_mod || $account->hasPermission(PERMISSION_EDIT_ALLIANCE_DESCRIPTION));
$template->assign('CanChangePassword', $change_pass);
$template->assign('CanChangeChatChannel', $change_chat);
$template->assign('CanChangeMOTD', $change_mod);
$template->assign('HidePassword', $alliance->getRecruitType() != SmrAlliance::RECRUIT_PASSWORD);
