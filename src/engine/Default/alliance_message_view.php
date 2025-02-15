<?php declare(strict_types=1);

use Smr\Database;
use Smr\Epoch;

$template = Smr\Template::getInstance();
$session = Smr\Session::getInstance();
$var = $session->getCurrentVar();
$player = $session->getPlayer();

if (!isset($var['alliance_id'])) {
	$var['alliance_id'] = $player->getAllianceID();
}

$alliance = SmrAlliance::getAlliance($var['alliance_id'], $player->getGameID());
$thread_index = $var['thread_index'];
$thread_id = $var['thread_ids'][$thread_index];

$template->assign('PageTopic', $var['thread_topics'][$thread_index]);
Menu::alliance($alliance->getAllianceID());

$db = Database::getInstance();
$db->replace('player_read_thread', [
	'account_id' => $db->escapeNumber($player->getAccountID()),
	'game_id' => $db->escapeNumber($player->getGameID()),
	'alliance_id' => $db->escapeNumber($alliance->getAllianceID()),
	'thread_id' => $db->escapeNumber($thread_id),
	'time' => $db->escapeNumber(Epoch::time() + 2),
]);

$mbWrite = true;
if ($alliance->getAllianceID() != $player->getAllianceID()) {
	$dbResult = $db->read('SELECT 1 FROM alliance_treaties
					WHERE (alliance_id_1 = ' . $db->escapeNumber($alliance->getAllianceID()) . ' OR alliance_id_1 = ' . $db->escapeNumber($player->getAllianceID()) . ')' .
					' AND (alliance_id_2 = ' . $db->escapeNumber($alliance->getAllianceID()) . ' OR alliance_id_2 = ' . $db->escapeNumber($player->getAllianceID()) . ')' .
					' AND game_id = ' . $db->escapeNumber($player->getGameID()) .
					' AND mb_write = 1 AND official = \'TRUE\'');
	$mbWrite = $dbResult->hasRecord();
}

$container = Page::create('alliance_message_view.php', $var);

if (isset($var['thread_ids'][$thread_index - 1])) {
	$container['thread_index'] = $thread_index - 1;
	$template->assign('PrevThread', ['Topic' => $var['thread_topics'][$thread_index - 1], 'Href' => $container->href()]);
}
if (isset($var['thread_ids'][$thread_index + 1])) {
	$container['thread_index'] = $thread_index + 1;
	$template->assign('NextThread', ['Topic' => $var['thread_topics'][$thread_index + 1], 'Href' => $container->href()]);
}

$thread = [];
$thread['AllianceEyesOnly'] = is_array($var['alliance_eyes']) && $var['alliance_eyes'][$thread_index];
//for report type (system sent) messages
$players = [
	ACCOUNT_ID_PLANET => 'Planet Reporter',
	ACCOUNT_ID_BANK_REPORTER => 'Bank Reporter',
];
$dbResult = $db->read('SELECT player.*
			FROM player
			JOIN alliance_thread USING (game_id)
			WHERE game_id = ' . $db->escapeNumber($player->getGameID()) . '
				AND alliance_thread.alliance_id = ' . $db->escapeNumber($alliance->getAllianceID()) . ' AND alliance_thread.thread_id = ' . $db->escapeNumber($thread_id));
foreach ($dbResult->records() as $dbRecord) {
	$accountID = $dbRecord->getInt('account_id');
	$players[$accountID] = SmrPlayer::getPlayer($accountID, $player->getGameID(), false, $dbRecord)->getLinkedDisplayName(false);
}

$dbResult = $db->read('SELECT mb_messages FROM player_has_alliance_role JOIN alliance_has_roles USING(game_id,alliance_id,role_id) WHERE ' . $player->getSQL() . ' AND alliance_id=' . $db->escapeNumber($alliance->getAllianceID()) . ' LIMIT 1');
$thread['CanDelete'] = $dbResult->record()->getBoolean('mb_messages');

$dbResult = $db->read('SELECT text, sender_id, time, reply_id
FROM alliance_thread
WHERE game_id=' . $db->escapeNumber($player->getGameID()) . '
AND alliance_id=' . $db->escapeNumber($alliance->getAllianceID()) . '
AND thread_id=' . $db->escapeNumber($thread_id) . '
ORDER BY reply_id');

$thread['CanDelete'] = $dbResult->getNumRecords() > 1 && $thread['CanDelete'];
$thread['Replies'] = [];
$container = Page::create('alliance_message_delete_processing.php', $var);
$container['thread_id'] = $thread_id;
foreach ($dbResult->records() as $dbRecord) {
	$thread['Replies'][$dbRecord->getInt('reply_id')] = ['Sender' => $players[$dbRecord->getInt('sender_id')], 'Message' => $dbRecord->getString('text'), 'SendTime' => $dbRecord->getInt('time')];
	if ($thread['CanDelete']) {
		$container['reply_id'] = $dbRecord->getInt('reply_id');
		$thread['Replies'][$dbRecord->getInt('reply_id')]['DeleteHref'] = $container->href();
	}
}

if ($mbWrite || in_array($player->getAccountID(), Globals::getHiddenPlayers())) {
	$container = Page::create('alliance_message_add_processing.php', $var);
	$container['thread_index'] = $thread_index;
	$thread['CreateThreadReplyFormHref'] = $container->href();
}
$template->assign('Thread', $thread);
if (isset($var['preview'])) {
	$template->assign('Preview', $var['preview']);
}
