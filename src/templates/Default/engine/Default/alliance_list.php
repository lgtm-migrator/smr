<?php
if (!$ThisPlayer->hasAlliance()) { ?>
	<div class="center">
		<div class="buttonA">
			<a class="buttonA" href="<?php echo $CreateAllianceHREF; ?>">Create your own alliance!</a>
		</div>
	</div>
	<br /><br /><?php
}

if (count($Alliances) > 0) { ?>
	<table id="alliance-list" class="standard inset centered">
		<thead>
			<tr>
				<th class="sort shrink" data-sort="sort_recruit">Open Recruitment</th>
				<th class="sort" data-sort="sort_name">Alliance Name</th>
				<th class="sort shrink" data-sort="sort_totExp">Total Experience</th>
				<th class="sort shrink" data-sort="sort_avgExp">Average Experience</th>
				<th class="sort shrink" data-sort="sort_members">Members</th>
			</tr>
		</thead>

		<tbody class="list"><?php
			foreach ($Alliances as $AllianceID => $Alliance) { ?>
				<tr id="alliance-<?php echo $AllianceID; ?>" class="ajax">
					<td class="sort_recruit center"><?php if ($Alliance['OpenRecruitment']) { ?><span class="green">Yes</span><?php } else { ?><span class="red">No</span><?php } ?></td>
					<td class="sort_name"><?php echo $Alliance['Name']; ?></td>
					<td class="sort_totExp right"><?php echo number_format($Alliance['TotalExperience']); ?></td>
					<td class="sort_avgExp right"><?php echo number_format($Alliance['AverageExperience']); ?></td>
					<td class="sort_members right"><?php echo number_format($Alliance['Members']); ?></td>
				</tr><?php
			} ?>
		</tbody>
	</table>
	<p class="center">Click column table to reorder!</p>

	<?php $this->listjsInclude = 'alliance_list';
} else { ?>
	<p class="center">Currently there are no alliances.</p><?php
} ?>
