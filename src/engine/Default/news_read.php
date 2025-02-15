<?php declare(strict_types=1);

use Smr\Database;
use Smr\News;
use Smr\Request;

$template = Smr\Template::getInstance();
$session = Smr\Session::getInstance();
$var = $session->getCurrentVar();

$gameID = $var['GameID'] ?? $session->getPlayer()->getGameID();

$min_news = Request::getInt('min_news', 1);
$max_news = Request::getInt('max_news', 50);
if ($min_news > $max_news) {
	create_error('The first number must be lower than the second number!');
}
$template->assign('MinNews', $min_news);
$template->assign('MaxNews', $max_news);

$template->assign('PageTopic', 'Reading The News');

Menu::news($gameID);

News::doBreakingNewsAssign($gameID);
News::doLottoNewsAssign($gameID);

$template->assign('ViewNewsFormHref', Page::create('news_read.php', ['GameID' => $gameID])->href());

$db = Database::getInstance();
$dbResult = $db->read('SELECT * FROM news WHERE game_id = ' . $db->escapeNumber($gameID) . ' AND type != \'lotto\' ORDER BY news_id DESC LIMIT ' . ($min_news - 1) . ', ' . ($max_news - $min_news + 1));
$template->assign('NewsItems', News::getNewsItems($dbResult));
