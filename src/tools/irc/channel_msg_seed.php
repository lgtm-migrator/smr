<?php declare(strict_types=1);

/**
 * @param resource $fp
 */
function channel_msg_seed($fp, string $rdata, AbstractSmrPlayer $player): bool {
	if (preg_match('/^:(.*)!(.*)@(.*)\sPRIVMSG\s(.*)\s:!seed\s$/i', $rdata, $msg)) {

		$nick = $msg[1];
		$user = $msg[2];
		$host = $msg[3];
		$channel = $msg[4];

		echo_r('[SEED] by ' . $nick . ' in ' . $channel);

		$result = shared_channel_msg_seed($player);
		foreach ($result as $line) {
			fwrite($fp, 'PRIVMSG ' . $channel . ' :' . $line . EOL);
		}

		return true;
	}

	return false;
}

/**
 * @param resource $fp
 */
function channel_msg_seedlist($fp, string $rdata): bool {
	if (preg_match('/^:(.*)!(.*)@(.*)\sPRIVMSG\s(.*)\s:!seedlist(\s*help)?\s$/i', $rdata, $msg)) {

		$nick = $msg[1];
		$user = $msg[2];
		$host = $msg[3];
		$channel = $msg[4];

		echo_r('[SEEDLIST] by ' . $nick . ' in ' . $channel);

		fwrite($fp, 'PRIVMSG ' . $channel . ' :The !seedlist command enables alliance leader to add or remove sectors to the seedlist' . EOL);
		fwrite($fp, 'PRIVMSG ' . $channel . ' :The following sub commands are available:' . EOL);
		fwrite($fp, 'PRIVMSG ' . $channel . ' :  !seedlist add <sector1> <sector2> ...       Adds <sector> to the seedlist' . EOL);
		fwrite($fp, 'PRIVMSG ' . $channel . ' :  !seedlist del <sector1> <sector2> ...       Removes <sector> from seedlist' . EOL);

		return true;
	}

	return false;
}

/**
 * @param resource $fp
 */
function channel_msg_seedlist_add($fp, string $rdata, AbstractSmrPlayer $player): bool {
	if (preg_match('/^:(.*)!(.*)@(.*)\sPRIVMSG\s(.*)\s:!seedlist add (.*)\s$/i', $rdata, $msg)) {

		$nick = $msg[1];
		$user = $msg[2];
		$host = $msg[3];
		$channel = $msg[4];
		$sectors = explode(' ', $msg[5]);

		echo_r('[SEEDLIST_ADD] by ' . $nick . ' in ' . $channel);

		$result = shared_channel_msg_seedlist_add($player, $sectors);
		foreach ($result as $line) {
			fwrite($fp, 'PRIVMSG ' . $channel . ' :' . $line . EOL);
		}

		return true;
	}

	return false;
}

/**
 * @param resource $fp
 */
function channel_msg_seedlist_del($fp, string $rdata, AbstractSmrPlayer $player): bool {
	if (preg_match('/^:(.*)!(.*)@(.*)\sPRIVMSG\s(.*)\s:!seedlist del (.*)\s$/i', $rdata, $msg)) {

		$nick = $msg[1];
		$user = $msg[2];
		$host = $msg[3];
		$channel = $msg[4];
		$sectors = explode(' ', $msg[5]);

		echo_r('[SEEDLIST_DEL] by ' . $nick . ' in ' . $channel);

		$result = shared_channel_msg_seedlist_del($player, $sectors);
		foreach ($result as $line) {
			fwrite($fp, 'PRIVMSG ' . $channel . ' :' . $line . EOL);
		}

		return true;
	}

	return false;
}
