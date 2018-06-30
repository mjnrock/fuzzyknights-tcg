<?php	
	$temps = API::vwCardStatModifier(NULL, NULL, NULL, "Name, Stage, Step");
	$Cards = [];
	foreach($temps as $temp) {
		if(!isset($Cards[$temp["Name"]])) {
			$Cards[$temp["Name"]] = [];
		}
		$Cards[$temp["Name"]][] =  $temp;
	}
	foreach($Cards as $i => $Card) {
		$Cards[$i] = new Card($Card);
	}
?>

<table class="table centered">
	<thead>
		<th>Name</th>

		<th>Type</th>
		<th>Discipline</th>

		<th>Task</th>
		<th>Requirement</th>

		<th>Actions</th>
	</thead>
	<tbody>
		<?php foreach($Cards as $key => $Card): ?>
			<tr class="pointer" card-id="<?= $Card->ID; ?>">
				<td><?= $Card->Name; ?></td>

				<td card-type-id="<?= $Card->Categories->CardType["ID"]; ?>">
					<?= $Card->Categories->CardType["Label"]; ?>
				</td>
				<td discipline-id="<?= $Card->Categories->Discipline["ID"]; ?>">
					<?= $Card->Categories->Discipline["Label"]; ?>
				</td>

				<td task-id="<?= $Card->Categories->Task["ID"]; ?>">
					<?= $Card->Categories->Task["Label"]; ?>
				</td>
				<td requirement-id="<?= $Card->Categories->RequirementCardType["ID"]; ?>">
					<?= $Card->Categories->RequirementCardType["Label"]; ?>
				</td>

				<td class="table-actions">
					<div class="flex">
						<div class="btn mr1 white ba cyan-text text-darken-4 cyan lighten-3" action="Edit">
							<i class="material-icons">edit</i>
						</div>
						<div class="btn mr1 white ba <?= $Card->IsActive === 1 ? "green lighten-3 green-text text-darken-2" : "grey lighten-3 grey-text text-darken-2"; ?>" action="DeActivate">
							<i class="material-icons"><?= $Card->IsActive === 1 ? "visibility_on" : "visibility_off"; ?></i>
						</div>
						<div class="btn white ba red-text text-darken-4 red lighten-3" action="Delete">
							<i class="material-icons">delete_forever</i>
						</div>
					</div>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<script>
	let table = $("table").DataTable({
		pageLength: 100
	});
		
	$(document).ready(function() {
		$("tr").on("dblclick", function () {
			window.location.href = `/card/${$(this).attr("card-id")}`;
		});

		$("td.table-actions div.btn").on("click", function() {
			if($(this).attr("action") === "Edit") {
				location.href = `/card/${$(this).parents("tr[card-id]").attr("card-id")}`;
			} else if($(this).attr("action") === "DeActivate") {
				AJAX("CardUpdateState", {
					CardID: $(this).parents("tr[card-id]").attr("card-id"),
					Action: "DeActivate"
				}, (e) => {
					if(e !== null && e !== void 0) {
						let response = JSON.parse(e)[0];

						// console.log(response);
						$(this).attr("class", `btn mr1 white ba ${+response.IsActive === 1 ? "green lighten-3 green-text text-darken-2" : "grey lighten-3 grey-text text-darken-2"}`);
						$(this).find("i").text("edit");

						// 	text = null,
						// 	css = null;

						// if(+response.ModifierIsActive === 1) {
						// 	text = "<i class='material-icons'>visibility_on</i>";
						// 	css = "green lighten-3 green-text text-darken-2";
						// } else {
						// 	text = "<i class='material-icons'>visibility_off</i>";
						// 	css = "grey lighten-3 grey-text text-darken-2";
						// }
						// $(`li[csm-id=${+response.CardStatModifierID}] a[action=DeActivate]`)
						// 	.html(text)
						// 	.attr("class", `w-80 waves-effect waves-dark btn b ba ${css}`);
					}
				});
			} else if($(this).attr("action") === "Delete") {

			}
		});
	});
</script>