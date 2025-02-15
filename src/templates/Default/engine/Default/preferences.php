<?php
if (isset($Reason)) {
	?><p><span class="big bold red"><?php echo $Reason; ?></span></p><?php
}

if (isset($GameID)) { ?>
	<form class="standard" id="PlayerPreferencesForm" method="POST" action="<?php echo $PlayerPreferencesFormHREF; ?>">
		<table>
			<tr>
				<th colspan="2">Player Preferences (For Current Game)</th>
			</tr>

			<tr>
				<td>Combat drones kamikaze on mines</td>
				<td>
					Yes: <input type="radio" name="kamikaze" value="Yes"<?php if ($ThisPlayer->isCombatDronesKamikazeOnMines()) { ?> checked="checked"<?php } ?> /><br />
					No: <input type="radio" name="kamikaze" value="No"<?php if (!$ThisPlayer->isCombatDronesKamikazeOnMines()) { ?> checked="checked"<?php } ?> />
				</td>
			</tr>

			<tr>
				<td>&nbsp;</td>
				<td><input type="submit" name="action" value="Change Kamikaze Setting" /></td>
			</tr>

			<tr>
				<td>Receive force change messages</td>
				<td>
					Yes: <input type="radio" name="forceDropMessages" value="Yes"<?php if ($ThisPlayer->isForceDropMessages()) { ?> checked="checked"<?php } ?> /><br />
					No: <input type="radio" name="forceDropMessages" value="No"<?php if (!$ThisPlayer->isForceDropMessages()) { ?> checked="checked"<?php } ?> />
				</td>
			</tr>

			<tr>
				<td>&nbsp;</td>
				<td><input type="submit" name="action" value="Change Message Setting" /></td>
			</tr>

			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>

			<tr>
				<td>Player Name</td>
				<td>
					<input type="text" maxlength="32" name="PlayerName" value="<?php echo htmlentities($ThisPlayer->getPlayerName()); ?>" size="32"><br/><?php
					if ($ThisPlayer->isNameChanged()) {
						?>(You have already changed your name for free, further changes will cost <?php echo CREDITS_PER_NAME_CHANGE; ?> SMR Credits)<?php
					} else {
						?>(You can change your name for free once)<?php
					} ?>
				</td>
			</tr>

			<tr>
				<td>&nbsp;</td>
				<td><button type="submit" name="action" value="change_name">Alter Player Name <?php if ($ThisPlayer->isNameChanged()) { ?>(<?php echo CREDITS_PER_NAME_CHANGE; ?> SMR Credits) <?php } ?></button></td>
			</tr>

			<tr>
				<td colspan="2">&nbsp;</td>
			</tr><?php

			if ($ThisPlayer->canChangeRace()) { ?>
				<tr>
					<td>Player Race</td>
					<td>
						<select name="race_id"><?php
							foreach ($ThisPlayer->getGame()->getPlayableRaceIDs() as $RaceID) {
								?><option value="<?php echo $RaceID; ?>" <?php if ($RaceID == $ThisPlayer->getRaceID()) { ?> selected<?php } ?>><?php echo Smr\Race::getName($RaceID); ?></option><?php
							} ?>
						</select>
						<br />
						(This will mostly reset your trader! You may only change your race once per game, and only during the first <?php echo format_time(TIME_FOR_RACE_CHANGE); ?> of the game.)
			</tr>

				<tr>
					<td>&nbsp;</td>
					<td><button type="submit" name="action" value="change_race">Alter Player Race</button></td>

				<tr>
					<td colspan="2">&nbsp;</td>
				</tr><?php
			} ?>

			<tr>
				<td>Chat Sharing:</td>
				<td><div class="buttonA"><a href="<?php echo $ChatSharingHREF; ?>" class="buttonA">Manage Chat Sharing Settings</a></div></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>These settings specify who you share your game information with in supported chat services.</td>
			</tr>

		</table>
	</form>
	<br /><?php
} ?>
<form id="AccountPreferencesForm" method="POST" action="<?php echo $AccountPreferencesFormHREF; ?>">
	<table>
		<tr>
			<th colspan="2">Account Preferences</th>
		</tr>

		<tr>
			<td>Referral Link:</td>
			<td><b><?php echo $ThisAccount->getReferralLink(); ?></b></td>
		</tr>

		<tr>
			<td>Login:</td>
			<td><b><?php echo $ThisAccount->getLogin(); ?></b></td>
		</tr>

		<tr>
			<td>ID:</td>
			<td><?php echo $ThisAccount->getAccountID(); ?></td>
		</tr>

		<tr>
			<td>SMR&nbsp;Credits:</td>
			<td><?php echo $ThisAccount->getSmrCredits(); ?></td>
		</tr>

		<tr>
			<td>SMR&nbsp;Reward&nbsp;Credits:</td>
			<td><?php echo $ThisAccount->getSmrRewardCredits(); ?></td>
		</tr>

		<tr>
			<td>Ban Points:</td>
			<td><?php echo $ThisAccount->getPoints(); ?></td>
		</tr>

		<tr>
			<td>Friendly Colour:</td>
			<td><div id="friendlyColorSelector">
				<div class="preview" style="background-color: #<?php echo $ThisAccount->getFriendlyColour(); ?>"></div>
				<input type="hidden" name="friendly_color" value="<?php echo $ThisAccount->getFriendlyColour(); ?>"/></div></td>
		</tr>

		<tr>
			<td>Neutral Colour:</td>
			<td><div id="neutralColorSelector">
				<div class="preview" style="background-color: #<?php echo $ThisAccount->getNeutralColour(); ?>"></div>
				<input type="hidden" name="neutral_color" value="<?php echo $ThisAccount->getNeutralColour(); ?>"/></div></td>
		</tr>

		<tr>
			<td>Enemy Colour:</td>
			<td><div id="enemyColorSelector">
				<div class="preview" style="background-color: #<?php echo $ThisAccount->getEnemyColour(); ?>"></div>
				<input type="hidden" name="enemy_color" value="<?php echo $ThisAccount->getEnemyColour(); ?>"/></div></td>
		</tr>

		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="action" value="Update Colours" /></td>
		</tr>

		<tr>
			<td>Current Password:</td>
			<td><input type="password" name="old_password" size="25" /></td>
		</tr>

		<tr>
			<td>New Password:</td>
			<td><input type="password" name="new_password" size="25" /></td>
		</tr>

		<tr>
			<td>Verify New Password:</td>
			<td><input type="password" name="retype_password" size="25" /></td>
		</tr>

		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="action" value="Change Password" /></td>
		</tr>

		<tr><td colspan="2">&nbsp;</td></tr>

		<tr>
			<td>Email address:</td>
			<td><input type="email" name="email" value="<?php echo htmlspecialchars($ThisAccount->getEmail()); ?>" size="50" /></td>
		</tr>

		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="action" value="Save and resend validation code" /></td>
		</tr>

		<tr><td colspan="2">&nbsp;</td></tr>

		<tr>
			<td>Hall of Fame Name:</td>
			<td><input type="text" name="HoF_name" value="<?php echo $ThisAccount->getHofDisplayName(); ?>" size="50" /></td>
		</tr>

		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="action" value="Change Name" /></td>
		</tr>

		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>

		<tr>
			<td>Discord User ID:</td>
			<td><input type="text" name="discord_id" value="<?php echo htmlspecialchars($ThisAccount->getDiscordId() ?? ''); ?>" size=50 /></td>
		</tr>

		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="action" value="Change Discord ID" /></td>
		</tr>

		<tr>
			<td>IRC Nick:</td>
			<td><input type="text" name="irc_nick" value="<?php echo htmlspecialchars($ThisAccount->getIrcNick() ?? ''); ?>" size="50" /></td>
		</tr>

		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="action" value="Change IRC Nick" /></td>
		</tr>

		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>

		<tr>
			<td>Timezone:</td>
			<td>
				<select name="timez"><?php
				$time = Smr\Epoch::time();
				$offset = $ThisAccount->getOffset();
				for ($i = -12; $i <= 11; $i++) {
					?><option value="<?php echo $i; ?>"<?php if ($offset == $i) { ?> selected="selected"<?php } ?>><?php echo date($ThisAccount->getTimeFormat(), $time + $i * 3600); ?></option><?php
				} ?>
				</select>
			</td>
		</tr>

		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="action" value="Change Timezone" /></td>
		</tr>

		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>

		<tr>
			<td>Date Format:</td>
			<td><input type="text" name="dateformat" value="<?php echo htmlspecialchars($ThisAccount->getDateFormat()); ?>" /><br />(Default: '<?php echo DEFAULT_DATE_FORMAT; ?>')</td>
		</tr>

		<tr>
			<td>Time Format:</td>
			<td><input type="text" name="timeformat" value="<?php echo htmlspecialchars($ThisAccount->getTimeFormat()); ?>" /><br />(Default: '<?php echo DEFAULT_TIME_FORMAT; ?>')</td>
		</tr>

		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="action" value="Change Date Formats" /></td>
		</tr>

		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>

		<tr>
			<td>Use AJAX (Auto&nbsp;Refresh):</td>
			<td>
				<button type="submit" name="action" value="Toggle Ajax"><?php echo $ThisAccount->isUseAJAX() ? 'Disable' : 'Enable'; ?> AJAX</button> (Currently <?php echo $ThisAccount->isUseAJAX() ? 'Enabled' : 'Disabled'; ?>)<br />
			</td>
		</tr>

		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>

		<tr>
			<td>Display Ship Images:</td>
			<td>
				Yes: <input type="radio" name="images" value="Yes"<?php if ($ThisAccount->isDisplayShipImages()) { ?> checked="checked"<?php } ?> /><br />
				No: <input type="radio" name="images" value="No"<?php if (!$ThisAccount->isDisplayShipImages()) { ?> checked="checked"<?php } ?> /><br />
			</td>
		</tr>

		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="action" value="Change Images" /></td>
		</tr>

		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>

		<tr>
			<td>Center Galaxy Map On Player:</td>
			<td>
				Yes: <input type="radio" name="centergalmap" value="Yes"<?php if ($ThisAccount->isCenterGalaxyMapOnPlayer()) { ?> checked="checked"<?php } ?> /><br />
				No: <input type="radio" name="centergalmap" value="No"<?php if (!$ThisAccount->isCenterGalaxyMapOnPlayer()) { ?> checked="checked"<?php } ?> /><br />
			</td>
		</tr>

		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="action" value="Change Centering" /></td>
		</tr>

		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>

		<tr>
			<td>Font size:</td>
			<td><input type="number" size="4" name="fontsize" value="<?php echo $ThisAccount->getFontSize(); ?>" /> Minimum font size is 50%</td>
		</tr>

		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="action" value="Change Size" /></td>
		</tr>

		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>

		<tr>
			<td>CSS Template:</td>
			<td>
				<select name="template"><?php
					foreach (Globals::getAvailableTemplates() as $Template) {
						foreach (Globals::getAvailableColourSchemes($Template) as $ColourScheme) {
							$selected = ($ThisAccount->getTemplate() == $Template &&
							             $ThisAccount->getColourScheme() == $ColourScheme &&
							             $ThisAccount->isDefaultCSSEnabled()) ? 'selected' : '';
							$name = $Template . ' - ' . $ColourScheme;
							?><option value="<?php echo $name; ?>" <?php echo $selected; ?>><?php echo $name; ?></option><?php
						}
					} ?>
					<option value="None" <?php if (!$ThisAccount->isDefaultCSSEnabled()) { echo 'selected'; } ?>>None</option>
				</select>
			</td>
		</tr>

		<tr>
			<td class="top">Add CSS Link:</td>
			<td>
				<input type="url" size="50" name="csslink" value="<?php echo htmlspecialchars($ThisAccount->getCssLink() ?? ''); ?>"><br />
				Specifies a CSS file to load in addition to the CSS Template.<br />
				If trying to link to a local file you may have to change your browser's security settings.<br />
				Warning: only add a CSS link if you know what you're doing!
			</td>
		</tr>

		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="action" value="Change CSS Options" /></td>
		</tr>
	</table>
</form><br />

<form id="HotkeyPreferencesForm" method="POST" action="<?php echo $AccountPreferencesFormHREF; ?>">
	<table>
		<tr>
			<th colspan="2">Hotkeys (Use space to separate multiple hotkeys)</th>
		</tr><?php
		$MovementTypes = ['Up', 'Left', 'Right', 'Down', 'Warp'];
		$MovementSubTypes = ['Move', 'Scan'];
		foreach ($MovementTypes as $MovementType) {
			foreach ($MovementSubTypes as $MovementSubType) {
				$FullMovement = $MovementSubType . $MovementType; ?>
				<tr>
					<td><?php echo $MovementSubType, ' ', $MovementType; ?>:</td>
					<td>
						<input type="text" size="50" name="<?php echo $FullMovement; ?>" value="<?php echo htmlentities(implode(' ', $ThisAccount->getHotkeys($FullMovement))); ?>"><br />
					</td>
				</tr><?php
			}
		} ?>
		<tr>
			<td>Scan Current Sector:</td>
			<td>
				<input type="text" size="50" name="ScanCurrent" value="<?php echo htmlentities(implode(' ', $ThisAccount->getHotkeys('ScanCurrent'))); ?>"><br />
			</td>
		</tr>
		<tr>
			<td>Show Current Sector:</td>
			<td>
				<input type="text" size="50" name="CurrentSector" value="<?php echo htmlentities(implode(' ', $ThisAccount->getHotkeys('CurrentSector'))); ?>"><br />
			</td>
		</tr>
		<tr>
			<td>Show Local Map:</td>
			<td>
				<input type="text" size="50" name="LocalMap" value="<?php echo htmlentities(implode(' ', $ThisAccount->getHotkeys('LocalMap'))); ?>"><br />
			</td>
		</tr>
		<tr>
			<td>Show Plot Course:</td>
			<td>
				<input type="text" size="50" name="PlotCourse" value="<?php echo htmlentities(implode(' ', $ThisAccount->getHotkeys('PlotCourse'))); ?>"><br />
			</td>
		</tr>
		<tr>
			<td>Show Current Players:</td>
			<td>
				<input type="text" size="50" name="CurrentPlayers" value="<?php echo htmlentities(implode(' ', $ThisAccount->getHotkeys('CurrentPlayers'))); ?>"><br />
			</td>
		</tr>
		<tr>
			<td>Enter Port:</td>
			<td>
				<input type="text" size="50" name="EnterPort" value="<?php echo htmlentities(implode(' ', $ThisAccount->getHotkeys('EnterPort'))); ?>"><br />
			</td>
		</tr>
		<tr>
			<td>Attack Trader/Continue Attack</td>
			<td>
				<input type="text" size="50" name="AttackTrader" value="<?php echo htmlentities(implode(' ', $ThisAccount->getHotkeys('AttackTrader'))); ?>"><br />
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="action" value="Save Hotkeys" /></td>
		</tr>
	</table>
</form><br />

<form id="TransferSMRCreditsForm" method="POST" action="<?php echo $PreferencesConfirmFormHREF; ?>">
	<table>
		<tr>
			<th colspan="2">SMR Credits</th>
		</tr>
		<tr>
			<td>Transfer Credits:</td>
			<td>
				<input type="number" name="amount" class="center" style="width:50px;" /> credits to <?php if (!isset($GameID)) { ?>the account with HoF name of <?php } ?>
				<select name="account_id"><?php
					foreach ($TransferAccounts as $AccID => $HofDisplayName) {
						?><option value="<?php echo $AccID; ?>"><?php echo $HofDisplayName; ?></option><?php
					} ?>
				</select>
			</td>
		</tr>

		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="action" value="Transfer" /></td>
		</tr>
	</table>
</form>

<?php $this->addJavascriptSource('/js/colorpicker.js'); ?>
