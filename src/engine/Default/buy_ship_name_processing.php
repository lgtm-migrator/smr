<?php declare(strict_types=1);

use Smr\Request;

function checkShipLogo(string $filename): void {
	// check if we have an image
	if ($_FILES['photo']['error'] != UPLOAD_ERR_OK) {
		create_error('Error while uploading');
	}

	// get dimensions
	$size = getimagesize($_FILES['photo']['tmp_name']);
	if ($size === false) {
		create_error('Uploaded file must be an image!');
	}

	// check if we really have a jpg
	$allowed_types = [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG];
	if (!in_array($size[2], $allowed_types)) {
		create_error('Only gif, jpg or png-image allowed! s = ' . $size[2]);
	}

	// check if width > MAX_IMAGE_WIDTH
	if ($size[0] > MAX_IMAGE_WIDTH) {
		create_error('Image is wider than ' . MAX_IMAGE_WIDTH . ' pixels!');
	}

	// check if height > MAX_IMAGE_HEIGHT
	if ($size[1] > MAX_IMAGE_HEIGHT) {
		create_error('Image is taller than ' . MAX_IMAGE_HEIGHT . ' pixels!');
	}
	if (filesize($_FILES['photo']['tmp_name']) > MAX_IMAGE_SIZE * 1024) {
		create_error('Image is bigger than ' . MAX_IMAGE_SIZE . 'k.');
	}

	if (!move_uploaded_file($_FILES['photo']['tmp_name'], UPLOAD . $filename)) {
		create_error('Failed to upload file');
	}
}

function checkTextShipName(string $name, int $max_len): void {
	if (empty($name)) {
		create_error('Please enter a ship name!');
	}

	// disallow certain ascii chars
	foreach (str_split($name) as $char) {
		if (!ctype_print($char) || in_array(ord($char), [37, 39, 59, 92, 63, 42])) {
			create_error('The ship name contains invalid characters! ' . $char);
		}
	}

	if (strlen($name) > $max_len) {
		create_error('That won\'t fit on your ship!');
	}
}

function checkHtmlShipName(string $name): void {
	//check for some bad html
	if (preg_match('/(\<span[^\>]*id\s*=)|(class\s*=\s*"[^"]*ajax)/i', $name) > 0) {
		create_error('You have used html that is not allowed.');
	}
	$bad = ['<form', '<applet', '<a ', '<bgsound', '<body', '<meta', '<dd', '<dir', '<dl', '<!doctype', '<dt', '<embed', '<frame', '<head', '<hr', '<iframe', '<ilayer', '<img', '<input', '<isindex', '<layer', '<li', '<link', '<map', '<menu', '<nobr', '<no', '<object', '<ol', '<opt', '<p', '<script', '<select', '<sound', '<td', '<text', '<t', '<ul', '<h', '<br', '<marquee', 'size', 'width', 'height', '<div', 'width='];
	foreach ($bad as $check) {
		if (stristr($name, $check)) {
			$check .= '*>';
			if ($check != '<h*>') {
				create_error(htmlentities($check, ENT_NOQUOTES, 'utf-8') . ' tag is not allowed in ship names.<br /><small>If you believe the name is appropriate please contact an admin.</small>');
			} else {
				create_error('Either you used the ' . htmlentities($check, ENT_NOQUOTES, 'utf-8') . ' tag which is not allowed or the ' . htmlentities('<html>', ENT_NOQUOTES, 'utf-8') . ' tag which is not needed.');
			}
		}
	}

	// Check for valid HTML by parsing the name with DOMDocument
	$doc = new DOMDocument();
	$use_errors = libxml_use_internal_errors(true);
	$doc->loadHTML('<html>' . $name . '</html>');
	libxml_use_internal_errors($use_errors);
	$error = libxml_get_last_error();
	if (!empty($error)) {
		create_error('Your ship name must not contain invalid HTML!<br /><small>If you think you received this message in error, please contact an admin.</small>');
	}

	// Make sure all tags are closed (since DOMDocument allows some tags,
	// e.g. <span>, to be unclosed).
	$opening_matches = null;
	preg_match_all('|<([^/>]+)>|', $name, $opening_matches);
	$closing_matches = null;
	preg_match_all('|</([^>]+)>|', $name, $closing_matches);
	sort($opening_matches[1]);
	sort($closing_matches[1]);
	if ($opening_matches[1] != $closing_matches[1]) {
		create_error('You must close all HTML tags.  (i.e a &lt;font color="red"&gt; tag must have a &lt;/font&gt; tag somewhere after it).<br /><small>If you think you received this message in error please contact an admin.</small>');
	}
}

//-----------------------------------------------------

$session = Smr\Session::getInstance();
$var = $session->getCurrentVar();
$account = $session->getAccount();
$player = $session->getPlayer();

$action = Request::get('action');

$cred_cost = $var['costs'][$action];
if ($account->getTotalSmrCredits() < $cred_cost) {
	create_error('You don\'t have enough SMR Credits. These can be earned by donating to SMR!');
}

if ($action == 'logo') {
	$filename = $player->getAccountID() . 'logo' . $player->getGameID();
	checkShipLogo($filename);
	$name = '<img style="padding:3px;" src="upload/' . $filename . '">';
} else {
	// Player submitted a text or HTML ship name
	$name = Request::get('ship_name');
	if ($action == 'text') {
		checkTextShipName($name, 48);
		$name = htmlentities($name, ENT_NOQUOTES, 'utf-8');
	} elseif ($action == 'html') {
		checkTextShipName($name, 128);
		checkHtmlShipName($name);
		$container = Page::create('buy_ship_name_preview.php');
		$container['ShipName'] = $name;
		$container['cost'] = $cred_cost;
		$container->go();
	}
}

$player->setCustomShipName($name);
$account->decreaseTotalSmrCredits($cred_cost);

$container = Page::create('current_sector.php');
$container['msg'] = 'Thanks for your purchase! Your ship is ready!';
$container->go();
