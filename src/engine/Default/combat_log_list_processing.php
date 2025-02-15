<?php declare(strict_types=1);

use Smr\Database;
use Smr\Request;

// If here, we have hit either the 'Save', 'Delete', or 'View' form buttons.
// Immediately return to the log list if we haven't selected any logs.
if (!Request::has('id')) {
	$container = Page::create('combat_log_list.php');
	$container['message'] = 'You must select at least one combat log!';
	$container->addVar('old_action', 'action');
	$container->go();
}

$session = Smr\Session::getInstance();
$player = $session->getPlayer();

$submitAction = Request::get('action');
$logIDs = array_keys(Request::getArray('id'));

// Do we need to save any logs (or delete any saved logs)?
if ($submitAction == 'Save' || $submitAction == 'Delete') {
	$db = Database::getInstance();
	if ($submitAction == 'Save') {
		//save the logs we checked
		// Query means people can only save logs that they are allowd to view.
		$db->write('INSERT IGNORE INTO player_saved_combat_logs (account_id, game_id, log_id)
					SELECT ' . $db->escapeNumber($player->getAccountID()) . ', ' . $db->escapeNumber($player->getGameID()) . ', log_id
					FROM combat_logs
					WHERE log_id IN (' . $db->escapeArray($logIDs) . ')
						AND game_id = ' . $db->escapeNumber($player->getGameID()) . '
						AND (
							attacker_id = ' . $db->escapeNumber($player->getAccountID()) . '
							OR defender_id = ' . $db->escapeNumber($player->getAccountID()) .
							($player->hasAlliance() ? '
								OR attacker_alliance_id = ' . $db->escapeNumber($player->getAllianceID()) . '
								OR defender_alliance_id = ' . $db->escapeNumber($player->getAllianceID())
							: '') . '
						)
					LIMIT ' . count($logIDs));
	} else { // $submitAction == 'Delete'
		$db->write('DELETE FROM player_saved_combat_logs
					WHERE log_id IN (' . $db->escapeArray($logIDs) . ')
						AND account_id = ' . $db->escapeNumber($player->getAccountID()) . '
						AND game_id = ' . $db->escapeNumber($player->getGameID()) . '
					LIMIT ' . count($logIDs));
	}

	// Now that the logs have been saved/deleted, go back to the log list
	$container = Page::create('combat_log_list.php');
	$container['message'] = $submitAction . 'd ' . $db->getChangedRows() . ' new logs.';
	$container->addVar('old_action', 'action');
	$container->go();
} elseif ($submitAction == 'View') {
	$container = Page::create('combat_log_viewer.php');
	$container['log_ids'] = $logIDs;
	sort($container['log_ids']);
	$container['current_log'] = 0;
	$container->go();
}
