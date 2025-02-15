<?php declare(strict_types=1);

use Smr\Database;
use Smr\Epoch;

$session = Smr\Session::getInstance();
$var = $session->getCurrentVar();
$player = $session->getPlayer();

// Send the bank report to the alliance message board
$alliance_id = $var['alliance_id'];
$text = $var['text'];

// Check if the "Bank Statement" thread exists yet
$db = Database::getInstance();
$dbResult = $db->read('SELECT thread_id FROM alliance_thread_topic WHERE game_id = ' . $db->escapeNumber($player->getGameID()) . ' AND alliance_id = ' . $db->escapeNumber($alliance_id) . ' AND topic = \'Bank Statement\' LIMIT 1');

if ($dbResult->hasRecord()) {
	// Update the existing "Bank Statement" thread
	$thread_id = $dbResult->record()->getInt('thread_id');
	$db->write('UPDATE alliance_thread SET time = ' . $db->escapeNumber(Epoch::time()) . ', text = ' . $db->escapeString($text) . ' WHERE thread_id = ' . $db->escapeNumber($thread_id) . ' AND alliance_id = ' . $db->escapeNumber($alliance_id) . ' AND game_id = ' . $db->escapeNumber($player->getGameID()) . ' AND reply_id = 1');
	$db->write('DELETE FROM player_read_thread WHERE thread_id = ' . $db->escapeNumber($thread_id) . ' AND game_id = ' . $db->escapeNumber($player->getGameID()) . ' AND alliance_id = ' . $db->escapeNumber($alliance_id));
} else {
	// There is no "Bank Statement" thread yet
	$dbResult = $db->read('SELECT IFNULL(MAX(thread_id)+1, 0) AS next_id FROM alliance_thread_topic WHERE game_id = ' . $db->escapeNumber($player->getGameID()) . ' AND alliance_id = ' . $db->escapeNumber($alliance_id));
	$thread_id = $dbResult->record()->getInt('next_id');
	$db->insert('alliance_thread_topic', [
		'game_id' => $db->escapeNumber($player->getGameID()),
		'alliance_id' => $db->escapeNumber($alliance_id),
		'thread_id' => $db->escapeNumber($thread_id),
		'topic' => $db->escapeString('Bank Statement'),
	]);
	$db->insert('alliance_thread', [
		'game_id' => $db->escapeNumber($player->getGameID()),
		'alliance_id' => $db->escapeNumber($alliance_id),
		'thread_id' => $db->escapeNumber($thread_id),
		'reply_id' => 1,
		'text' => $db->escapeString($text),
		'sender_id' => $db->escapeNumber(ACCOUNT_ID_BANK_REPORTER),
		'time' => $db->escapeNumber(Epoch::time()),
	]);
}

$container = Page::create('bank_report.php');
$container->addVar('alliance_id');
$container['sent_report'] = true;
$container->go();
