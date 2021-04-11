<?php declare(strict_types=1);

$template = Smr\Template::getInstance();

$template->assign('PageTopic', 'Enable New Games');

// If we have just forwarded from the processing file, pass its message.
if (isset($var['processing_msg'])) {
	$template->assign('ProcessingMsg', $var['processing_msg']);
}

// Get the list of disabled games
$db = Smr\Database::getInstance();
$db->query('SELECT game_name, game_id FROM game WHERE enabled=' . $db->escapeBoolean(false));
$disabledGames = array();
while ($db->nextRecord()) {
	$disabledGames[$db->getInt('game_id')] = $db->getField('game_name');
}
krsort($disabledGames);
$template->assign('DisabledGames', $disabledGames);

// Create the link to the processing file
$linkContainer = Page::create('enable_game_processing.php', '');
$template->assign('EnableGameHREF', $linkContainer->href());
