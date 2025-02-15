<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head><?php
		$this->includeTemplate('includes/Head.inc.php'); ?>
	</head>
	<body>
		<div id="Container">
			<table class="tableHeight centered">
				<tr class="topRow">
					<td class="topleftCell">
						<b><span class="smrBanner">smr</span></b>
						<br />
						<span id="tod"><?php echo $timeDisplay; ?></span>
					</td>
					<td class="topcenterCell"><?php
						if (isset($ThisPlayer)) { ?>
							<div class="TopInfo">
								<table class="fullwidth">
									<tr>
										<td>
											<div class="name noWrap">
												<span id="lvlName"><?php echo $ThisPlayer->getLevelName(); ?></span>
												<br />
												<a class="nav" href="<?php echo $PlayerNameLink; ?>"><?php echo $ThisPlayer->getDisplayName(); ?></a>
											</div>
										</td>
										<td>
											<div class="topcenterOne noWrap">
												Race: <?php echo $ThisPlayer->getColouredRaceName($ThisPlayer->getRaceID(), true); ?><br />

												Turns : <span id="turns">
													<span class="<?php echo $ThisPlayer->getTurnsLevel()->color(); ?>"><?php
															echo $ThisPlayer->getTurns() . '/' . $ThisPlayer->getMaxTurns();
														?></span>
													</span><br />

												<span id="newbieturns"><?php
													if ($ThisPlayer->hasNewbieTurns()) {
														?>Newbie Turns: <span style="color: #<?php if ($ThisPlayer->getNewbieTurns() > NEWBIE_TURNS_WARNING_LIMIT) { ?>387C44<?php } else { ?>F00<?php } ?>;"><?php echo $ThisPlayer->getNewbieTurns(); ?></span><br /><?php
													} ?>
												</span>

												Credits: <span id="creds"><?php echo number_format($ThisPlayer->getCredits()); ?></span><br />

												Experience: <span id="exp"><?php echo number_format($ThisPlayer->getExperience()); ?></span>
											</div>
										</td>
										<td>
											<div class="topcenterTwo noWrap">
												Level: <a class="nav" href="/level_requirements.php" target="levelRequirements"><span id="lvl"><?php echo $ThisPlayer->getLevelID(); ?></span></a>
												<br />
												Next Level: <?php
													$NextLevelExperience = number_format($ThisPlayer->getNextLevelExperience());
													$Experience = number_format($ThisPlayer->getExperience()); ?>
													<span id="lvlBar">
														<img src="images/bar_left.gif" width="5" height="10" title="<?php echo $Experience; ?>/<?php echo $NextLevelExperience; ?>" alt="<?php echo $Experience; ?>/<?php echo $NextLevelExperience; ?>" />
														<img src="images/blue.gif" width="<?php echo $ThisPlayer->getNextLevelPercentAcquired(); ?>" height="10" title="<?php echo $Experience; ?>/<?php echo $NextLevelExperience; ?>" alt="<?php echo $Experience; ?>/<?php echo $NextLevelExperience; ?>" />
														<img src="images/bar_border.gif" width="<?php echo $ThisPlayer->getNextLevelPercentRemaining(); ?>" height="10" title="<?php echo $Experience; ?>/<?php echo $NextLevelExperience; ?>" alt="<?php echo $Experience; ?>/<?php echo $NextLevelExperience; ?>" />
														<img src="images/bar_right.gif" width="5" height="10" title="<?php echo $Experience; ?>/<?php echo $NextLevelExperience; ?>" alt="<?php echo $Experience; ?>/<?php echo $NextLevelExperience; ?>" /><br />
													</span>

												Alignment: <span id="align"><?php echo get_colored_text($ThisPlayer->getAlignment(), number_format($ThisPlayer->getAlignment())); ?></span><br />

												Alliance: <span id="alliance"><a href="<?php echo Globals::getAllianceHREF($ThisPlayer->getAllianceID()); ?>"><?php
													echo $ThisPlayer->getAllianceDisplayName(false, true); ?></a></span>
											</div>
										</td>
									</tr>
								</table>
							</div><?php
						} ?>
					</td>
					<td class="rightCell bottom"><?php
						if (isset($ThisPlayer)) { ?>
							<div class="rightInfoMail noWrap"><?php
								$this->includeTemplate('includes/UnreadMessages.inc.php'); ?>
							</div><?php
						} ?>
					</td>
				</tr>
				<tr>
					<td class="leftCell">
						<?php $this->includeTemplate('includes/LeftPanel.inc.php'); ?>
					</td>

					<td class="centerContent">
						<div id="middle_panel" class="MainContentArea<?php if (isset($SpaceView) && $SpaceView) { ?> stars<?php } ?>"><?php
							if (isset($PageTopic)) {
								?><h1><?php echo $PageTopic; ?></h1><br /><?php
							}
							$this->includeTemplate('includes/menu.inc.php');
							$this->includeTemplate($TemplateBody); ?>
						</div>
						<div class="footer_left">
							<?php $this->includeTemplate('includes/VoteLinks.inc.php'); ?>
						</div>
						<div class="footer_right">
							<?php $this->includeTemplate('includes/copyright.inc.php'); ?>
						</div>
					</td>

					<td class="rightCell top"><?php
						if (isset($ThisPlayer)) { ?>
							<div class="rightInfoShip noWrap">
								<?php $this->includeTemplate('includes/RightPanelShip.inc.php'); ?>
							</div><?php
						} ?>
					</td>
				</tr>
			</table>
		</div>
		<?php $this->includeTemplate('includes/EndingJavascript.inc.php'); ?>
	</body>
</html>
