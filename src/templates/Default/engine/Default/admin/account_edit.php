<form name="form_acc" method="POST" action="<?php echo $EditFormHREF; ?>">
	<table cellpadding="3" border="0">
		<tr>
			<td class="right bold">Account ID:</td>
			<td><?php echo $EditingAccount->getAccountID(); ?></td>
		</tr>
		<tr>
			<td class="right bold">Login:</td>
			<td><?php echo $EditingAccount->getLogin(); ?></td>
		</tr>
		<tr>
			<td class="right bold">Validation Code:</td>
			<td><?php
					echo $EditingAccount->getValidationCode(); ?></td>
		</tr>
		<tr>
			<td class="right bold">Email:</td>
			<td><?php echo $EditingAccount->getEmail(); ?></td>
		</tr>
		<tr>
			<td class="right bold">HoF Name:</td>
			<td><?php echo $EditingAccount->getHofDisplayName(); ?></td>
		</tr>

		<tr>
			<td class="right bold">Points:</td>
			<td><?php echo $EditingAccount->getPoints(); ?></td>
		</tr>
		<tr>
			<td class="top right bold">Status:</td>
			<td><?php
				if ($Disabled) { ?>
					<span class="red">CLOSED</span> (<?php echo $Disabled['Reason']; ?>)<br />
					The account is set to <?php
					if ($Disabled['Time'] > 0) { ?>
						reopen on <?php echo date($ThisAccount->getDateTimeFormat(), $Disabled['Time']); ?>.<?php
					} else { ?>
						never reopen.<?php
					}
				} else { ?>
					<span class="green">OPEN</span><?php
				} ?>
			</td>
		</tr>

		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>

		<tr>
			<td valign="top" class="right bold">Player:</td>
				<td><?php
					if (count($EditingPlayers)) { ?>
						<a onclick="$('#accountPlayers').fadeToggle(600);">Show/Hide</a>
						<table id="accountPlayers" style="display:none"><?php
							foreach ($EditingPlayers as $CurrentPlayer) {
								$CurrentShip = $CurrentPlayer->getShip(); ?>
								<tr>
									<td class="right">Game ID:</td>
									<td><?php echo $CurrentPlayer->getGameID(); ?></td>
								</tr>
								<tr>
									<td class="right">Name:</td>
									<td><input type=text name=player_name[<?php echo $CurrentPlayer->getGameID(); ?>] placeholder="<?php echo htmlentities($CurrentPlayer->getPlayerName()); ?>" />(<?php echo $CurrentPlayer->getPlayerID(); ?>)</td>
								</tr>
								<tr>
									<td class="right">Experience:</td>
									<td><?php echo number_format($CurrentPlayer->getExperience()); ?></td>
								</tr>
								<tr>
									<td class="right">Ship:</td>
									<td><?php echo $CurrentShip->getName(); ?> (<?php echo $CurrentShip->getAttackRating(); ?>/<?php echo $CurrentShip->getDefenseRating(); ?>)</td>
								</tr>
								<tr>
									<td><input type="radio" name="delete[<?php echo $CurrentPlayer->getGameID(); ?>]" value="TRUE" unchecked="unchecked">Yes<input type="radio" name="delete[<?php echo $CurrentPlayer->getGameID(); ?>]" value="FALSE" checked="checked">No</td>
									<td>Delete player</td>
								</tr><?php
							} ?>
						</table><?php
					} else { ?>
						Joined no active games<?php
					} ?>
			</td>

		</tr>

		<tr>
			<td>&nbsp;</td><td><hr noshade style="height:1px; border:1px solid white;"></td>
		</tr>

		<tr>
			<td valign="top" class="right bold">Donation:</td>
			<td><input type="number" name="donation" size="5" class="center">$</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="checkbox" name="smr_credit" checked="checked"> Grant SMR Credits</td>
		</tr>

		<tr>
			<td valign="top" class="right bold">Grant Reward SMR Credits:</td>
			<td><input type="number" name="grant_credits" size="5" class="center"> Credits</td>
		</tr>

		<tr>
			<td>&nbsp;</td>
			<td><hr noshade style="height:1px; border:1px solid white;"></td>
		</tr>

		<tr>
			<td valign="top" class="right bold">Close Account:</td>
			<td>
				<table>
					<tr>
						<td><input type="radio" name="special_close" value="<?php echo CLOSE_ACCOUNT_BY_REQUEST_REASON; ?>"></td>
						<td>
							<b>Close by User Request</b><br />
							Users will be able to re-open their account by themselves if this
							account closing method is used. It is useful if, e.g., they do not
							want to receive any more newsletters.
						</td>
					</tr>
					<tr>
						<td><input type="radio" name="special_close" value="<?php echo CLOSE_ACCOUNT_INVALID_EMAIL_REASON; ?>"></td>
						<td>
							<b>Close due to Invalid E-mail</b><br />
							Use this if the e-mail address for this account no longer exists,
							e.g. if we get a newsletter bounce. Users can re-open their account
							by providing a new e-mail address.
						</td>
					</tr>
				</table>
				<p>Note (optional): <input type="text" name="close_by_request_note" /></p>
			</td>
		</tr>

		<tr>
			<td>&nbsp;</td>
			<td><hr noshade style="height:1px; border:1px solid white;"></td>
		</tr>

		<script>
			function go() {
				var val = window.document.form_acc.reason_pre_select.value;
				if (val == 2) {
					alert("Please use the following syntax when you enter the multi closing suspicion: 'Match list:1+5+7' Thanks");
					window.document.form_acc.suspicion.value = 'Match list:';
					window.document.form_acc.suspicion.focus();
				} else {
					window.document.form_acc.suspicion.value = '';
				}
				window.document.form_acc.choise[0].checked = true;
			}
		</script>
		<tr>
			<td valign="top" class="right bold">Ban Points:</td>
			<td>
				<p>
					<input type="radio" name="choise" value="pre_select">
					Existing Reason: <select name="reason_pre_select" onchange="go()">
						<option value="0">[Please Select]</option><?php
						foreach ($BanReasons as $ReasonID => $BanReason) { ?>
							<option value="<?php echo $ReasonID; ?>"<?php if ($Disabled !== false && $ReasonID == $Disabled['ReasonID']) { ?> selected="selected"<?php } ?>><?php echo $BanReason; ?></option><?php
						} ?>
					</select>
				</p>
				<p>
					<input type="radio" name="choise" value="individual">
					New Reason: <input type="text" name="reason_msg" style="width:400px;">
				</p>
				<p><input type="radio" name="choise" value="reopen"> Reopen! (Will remove ban points, if specified)</p>
				<p>Suspicion: <input type="text" name="suspicion" style="width:300px;" maxlength="255" placeholder="Add any private details about ban here"></p>
				<p>Ban Points: <input type="number" name="points" class="center" style="width:40px;"> points</p>
			</td>
		</tr>

		<tr>
			<td>&nbsp;</td>
			<td><hr noshade style="height:1px; border:1px solid white;"></td>
		</tr>

		<tr>
			<td class="right bold">Mail ban:</td>
			<td>
				Current mail ban: <?php
				if ($EditingAccount->isMailBanned()) { ?>
					<span class="red">For <?php echo format_time($EditingAccount->getMailBanned() - Smr\Epoch::time()); ?></span><?php
				} else { ?>
					<span class="green">None</span><?php
				} ?>
				<br /><br />
				<input type="radio" name="mailban" value="add_days" />
				Increase mail ban by <input type="number" name="mailban_days" class="center" style="width:40px" /> days
				<br />
				<input type="radio" name="mailban" value="remove" /> Remove mail ban
			</td>
		</tr>

		<tr>
			<td>&nbsp;</td>
			<td><hr noshade style="height:1px; border:1px solid white;"></td>
		</tr>

		<tr>
			<td valign="top" class="right bold">Closing History:</td>
			<td><?php
				if (count($ClosingHistory) > 0) {
					foreach ($ClosingHistory as $Action) {
						echo date($ThisAccount->getDateTimeFormat(), $Action['Time']); ?> - <?php echo $Action['Action']; ?> by <?php echo $Action['AdminName']; ?><br /><?php
					}
				} else { ?>
					No activity.<?php
				} ?>
			</td>
		</tr>

		<tr>
			<td>&nbsp;</td>
			<td><hr noshade style="height:1px; border:1px solid white;"></td>
		</tr>

		<tr>
			<td valign="top" class="right bold">Exception:</td>
			<td><?php
				if (isset($Exception)) {
					echo $Exception;
				} else { ?>
					This account is not listed.<br /><input type="text" name="exception_add" placeholder="Add An Exception"><?php
				} ?>
			</td>
		</tr>

		<tr>
			<td>&nbsp;</td>
			<td><hr noshade style="height:1px; border:1px solid white;"></td>
		</tr>

		<tr>
			<td valign="top" class="right bold">Forced Veteran:</td>
			<td><input type="radio" name="veteran_status" value="TRUE"<?php if ($EditingAccount->isVeteranForced()) { ?> checked="checked"<?php } ?>>Yes</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="radio" name="veteran_status" value="FALSE"<?php if (!$EditingAccount->isVeteranForced()) { ?> checked="checked"<?php } ?>>No</td>
		</tr>

		<tr>
			<td>&nbsp;</td>
			<td><hr noshade style="height:1px; border:1px solid white;"></td>
		</tr>

		<tr>
			<td valign="top" class="right bold">Logging:</td>
			<td><input type="radio" name="logging_status" value="TRUE"<?php if ($EditingAccount->isLoggingEnabled()) { ?> checked="checked"<?php } ?>>Yes</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="radio" name="logging_status" value="FALSE"<?php if (!$EditingAccount->isLoggingEnabled()) { ?> checked="checked"<?php } ?>>No</td>
		</tr>

		<tr>
			<td>&nbsp;</td>
			<td><hr noshade style="height:1px; border:1px solid white;"></td>
		</tr>

		<tr>
			<td valign="top" class="right bold">Last IP's:</td>
			<td><?php
				if (count($RecentIPs) > 0) { ?>
					<a onclick="$('#recentIPs').fadeToggle(600);">Show/Hide</a>
					<table id="recentIPs" style="display:none"><?php
						foreach ($RecentIPs as $RecentIP) { ?>
							<tr>
								<td><?php echo date($ThisAccount->getDateTimeFormat(), $RecentIP['Time']); ?></td>
								<td>&nbsp;</td>
								<td><?php echo $RecentIP['IP']; ?></td>
								<td>&nbsp;</td>
								<td><?php echo $RecentIP['Host']; ?></td>
							</tr><?php
						} ?>
					</table><?php
				} ?>
			</td>
		</tr>

	</table>

	<br />
	<input type="submit" name="action" value="Edit Account" />&nbsp;&nbsp;
	<div class="buttonA"><a class="buttonA" href="<?php echo $ResetFormHREF; ?>">Reset Form</a></div>
</form>
