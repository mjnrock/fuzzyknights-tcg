<?php
	require_once "{$_SERVER["DOCUMENT_ROOT"]}/models/Card.php";
	
	$Card = new Card($Card);
?>
<div class="row ba b--black-20 br2 shadow-5">
	<ul tcg="card-id" card-id="<?= $Card->ID; ?>">
		<li>
			<h4 tcg="card-name" class="tc"><?= $Card->Name; ?></h4>
		</li>
		<li>
			<img tcg="card-picture" src="<?= $Card->Picture; ?>" alt="">
		</li>
		<li>
			<h6 tcg="card-name" class="tc">Categories</h6>
			<table>
				<thead>
					<th>Task</th>
					<th>Type</th>
					<th>Discpline</th>
					<th>Requirement</th>
				</thead>
				<tbody>
					<td tcg="card-category-task" task-id="<?= $Card->Categories->Task["ID"]; ?>"><?= "{$Card->Categories->Task["Label"]} [{$Card->Categories->Task["Short"]}]"; ?></td>
					<td tcg="card-category-cardtype" cardtype-id="<?= $Card->Categories->CardType["ID"]; ?>"><?= "{$Card->Categories->CardType["Label"]} [{$Card->Categories->CardType["Short"]}]"; ?></td>
					<td tcg="card-category-discipline" discipline-id="<?= $Card->Categories->Discipline["ID"]; ?>"><?= "{$Card->Categories->Discipline["Label"]} [{$Card->Categories->Discipline["Short"]}]"; ?></td>
					<?php if(isset($Card->Categories->RequirementCardType["ID"])): ?>
						<td tcg="card-category-requirement" requirement-id="<?= $Card->Categories->RequirementCardType["ID"]; ?>"><?= "{$Card->Categories->RequirementCardType["Label"]} [{$Card->Categories->RequirementCardType["Short"]}]"; ?></td>
					<?php else: ?>
						<td tcg="card-category-requirement">-</td>
					<?php endif; ?>
				</tbody>
			</table>
		</li>
		<li>
			<h6 tcg="card-name" class="tc">Stats</h6>
			<table tcg="card-stats">
				<thead>
					<th>STR</th>
					<th>TGH</th>
					<th>PWR</th>
					<th>RES</th>
					<th>HP</th>
					<th>MP</th>
					<th>DUR</th>
				</thead>
				<tbody>
					<tr>
						<td tcg="card-stat-strength"><?= $Card->Stats->Strength; ?></td>
						<td tcg="card-stat-toughness"><?= $Card->Stats->Toughness; ?></td>
						<td tcg="card-stat-power"><?= $Card->Stats->Power; ?></td>
						<td tcg="card-stat-resistance"><?= $Card->Stats->Resistance; ?></td>
						<td tcg="card-stat-health"><?= $Card->Stats->Health; ?></td>
						<td tcg="card-stat-mana"><?= $Card->Stats->Mana; ?></td>
						<td tcg="card-stat-durability"><?= $Card->Stats->Durability; ?></td>
					</tr>
				</tbody>
			</table>
		</li>
		<li>
			<h6 tcg="card-name" class="tc">Modifiers</h6>
			<ul class="collection ml3 mr3 br2 shadow-3">
				<?php foreach($Card->Modifiers as $i => $Modifier): ?>
					<li class="collection-item">
						<div class="row" tcg="card-modifier-stat" statid="<?= $Modifier["Stat"]["ID"]; ?>">
							<div class="col s6">
								<div>
									<i class="material-icons">insert_chart</i>
									<span><?= "{$Modifier["Stat"]["Label"]} [{$Modifier["Stat"]["Short"]}]"; ?></span>
								</div>
							</div>
							<div class="col s6">
								<div>
									<i class="material-icons">call_split</i>
									<span><?= "{$Modifier["Stat"]["Action"]["Label"]} [{$Modifier["Stat"]["Action"]["Short"]}]"; ?></span>
								</div>
							</div>
						</div>
							
						<div class="row black-text <?= $Modifier["Target"]["IsFriendly"] ? "green lighten-3" : "red lighten-3"; ?>" tcg="card-modifier-target" statid="<?= $Modifier["Target"]["ID"]; ?>">
							<div class="col s6">
								<div>
									<i class="material-icons">location_on</i>
									<span><?= "{$Modifier["Target"]["X"]}, {$Modifier["Target"]["Y"]}"; ?></span>
								</div>
							</div>
							<div class="col s6">
								<div>
									<i class="material-icons">perm_identity</i>
									<span><?= "{$Modifier["Target"]["Label"]} [{$Modifier["Target"]["Short"]}]"; ?></span>
								</div>
							</div>
						</div>
						
						<div class="row" tcg="card-modifier-values" statid="<?= $Modifier["Values"]["ID"]; ?>">
							<div class="col s4">
								<div>
									<i class="material-icons">update</i>
									<span><?= $Modifier["Values"]["Lifespan"] === -1 ? "<i class='material-icons'>all_inclusive</i>" : $Modifier["Values"]["Lifespan"]; ?></span>
								</div>
							</div>
							<div class="col s4">
								<div>
									<i class="material-icons">exposure</i>
									<span><?= $Modifier["Values"]["Number"] === 0 ? ($Modifier["Values"]["Bonus"] >= 0 ? "+{$Modifier["Values"]["Bonus"]}" : "{$Modifier["Values"]["Bonus"]}") : "{$Modifier["Values"]["Number"]}d{$Modifier["Values"]["Sided"]}+{$Modifier["Values"]["Bonus"]}"; ?></span>
								</div>
							</div>
							<div class="col s4">
								<div>
									<i class="material-icons">format_list_numbered</i>
									<span><?= "{$Modifier["Values"]["Stage"]}.{$Modifier["Values"]["Step"]}"; ?></span>
								</div>
							</div>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		</li>
	</ul>
</div>