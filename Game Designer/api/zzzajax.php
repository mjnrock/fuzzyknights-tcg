<?php
	require_once "{$_SERVER["DOCUMENT_ROOT"]}/libs/index.php";
	require_once "{$_SERVER["DOCUMENT_ROOT"]}/models/index.php";

	if(isset($_GET["Action"]) && isset($_GET["Payload"])) {
		$Payload = json_decode($_GET["Payload"]);

		if($_GET["Action"] === "UpdateName") {
			Display::UpdateName($Payload);
		} else if($_GET["Action"] === "UpdateTask") {
			Display::UpdateTask($Payload);
		} else if($_GET["Action"] === "UpdateStat") {
			Display::UpdateStat($Payload);
		} else if($_GET["Action"] === "UpdateModifier") {
			Display::UpdateModifier($Payload);
		} else if($_GET["Action"] === "UpdateModifierState") {
			Display::UpdateModifierState($Payload);
		}

		if($_GET["Action"] === "CardUpdateState") {
			Display::CardUpdateState($Payload);
		}

		if($_GET["Action"] === "DeckUpdateState") {
			Display::DeckUpdateState($Payload);
		}
	}


	abstract class Display {
		public static function UpdateName($Payload) {
			$Payload->Name = str_replace("'", "''", $Payload->Name);

			$SQL = <<<SQL
			UPDATE TCG.Card
			SET
				Name = '{$Payload->Name}',
				ModifiedDateTime = GETDATE()
			OUTPUT
				Inserted.CardID,
				Inserted.Name
			WHERE
				CardID = {$Payload->CardID}
SQL;
			if(isset($Payload->CardID) && isset($Payload->Name)) {
				$result = API::query($SQL);

				echo json_encode($result);
			}
		}

		public static function UpdateTask($Payload) {			
			$SQL = <<<SQL
			UPDATE TCG.CardCategorization
			SET
				{$Payload->Column}ID = {$Payload->PKID},
				ModifiedDateTime = GETDATE()
			OUTPUT
				Inserted.CardID,
				'{$Payload->Table}' AS 'Table',
				'{$Payload->Column}' AS 'Column',
				Inserted.{$Payload->Column}ID AS PKID
			WHERE
				CardID = {$Payload->CardID}
SQL;
			if(isset($Payload->CardID) && isset($Payload->Table) && isset($Payload->Column) && isset($Payload->PKID)) {
				$result = API::query($SQL);
				$lookup = API::query("SELECT * FROM TCG.{$Payload->Table}");

				echo json_encode([
					"Result" => $result,
					"Lookup" => $lookup
				]);
			}
		}

		public static function UpdateStat($Payload) {
			$SQL = <<<SQL
			UPDATE TCG.CardStat
			SET
				Value = {$Payload->Value},
				ModifiedDateTime = GETDATE()
			OUTPUT
				Inserted.CardID,
				Inserted.StatID,
				Inserted.Value
			FROM
				TCG.CardStat cs WITH (NOLOCK)
				INNER JOIN TCG.[Stat] s WITH (NOLOCK)
					ON cs.StatID = s.StatID
			WHERE
				cs.CardID = {$Payload->CardID}
				AND s.Short = '{$Payload->Key}'
SQL;
			if(isset($Payload->CardID) && isset($Payload->Key) && isset($Payload->Value)) {
				$result = API::query($SQL);

				echo json_encode($result);
			}
		}

		public static function UpdateModifier($Payload) {
			if(isset($Payload->CardStatModifierID)) {
				if(isset($Payload->PKID) && isset($Payload->Table)) {
					$SQL = <<<SQL
					UPDATE TCG.CardStatModifier
					SET
						{$Payload->Table}ID = {$Payload->PKID},
						ModifiedDateTime = GETDATE()
					OUTPUT
						Inserted.CardStatModifierID,
						'{$Payload->Table}' AS 'Table',
						Inserted.{$Payload->Table}ID AS PKID
					WHERE
						CardStatModifierID = {$Payload->CardStatModifierID}
SQL;

					$result = API::query($SQL);
					$lookup = API::query("SELECT * FROM TCG.{$Payload->Table}");

					echo json_encode([
						"Result" => $result,
						"Lookup" => $lookup
					]);
				} else if(isset($Payload->Key) && isset($Payload->Value)) {
					$SQL = <<<SQL
					UPDATE TCG.CardStatModifier
					SET
						{$Payload->Key} = {$Payload->Value},
						ModifiedDateTime = GETDATE()
					OUTPUT
						Inserted.CardStatModifierID,
						'{$Payload->Key}' AS 'Key',
						Inserted.{$Payload->Key} AS 'Value'
					WHERE
						CardStatModifierID = {$Payload->CardStatModifierID}
SQL;
					$result = API::query($SQL);
					
					echo json_encode($result);
				}
			}
		}

		
		public static function UpdateModifierState($Payload) {
			if(isset($Payload->Action)) {
				if(isset($Payload->CardStatModifierID)) {
					if($Payload->Action === "DeActivate") {
						$SQL = <<<SQL
						UPDATE TCG.CardStatModifier
						SET
							DeactivatedDateTime = CASE
								WHEN DeactivatedDateTime IS NULL THEN GETDATE()
								ELSE NULL
							END,
							ModifiedDateTime = GETDATE()
						OUTPUT
							Inserted.CardStatModifierID,
							CASE
								WHEN Inserted.DeactivatedDateTime IS NULL THEN 1
								ELSE 0
							END AS ModifierIsActive
						WHERE
							CardStatModifierID = {$Payload->CardStatModifierID}
SQL;
					} else if($Payload->Action === "Delete") {
						$SQL = <<<SQL
						DELETE FROM TCG.CardStatModifier
						WHERE
							CardStatModifierID = {$Payload->CardStatModifierID};
SQL;
					}
				} else {
					if(isset($Payload->CardID) && $Payload->Action === "Add") {
						$SQL = <<<SQL
						INSERT INTO TCG.CardStatModifier (CardID, StatID, StatActionID, TargetID, Lifespan, Number, Sided, Bonus, Stage, Step)
						VALUES
							($Payload->CardID, 1, 1, 1, 0, 0, 0, 0, 99, 99);
SQL;
					} 
				}
				
				$result = API::query($SQL);
				
				echo json_encode($result);
			}
		}

		public static function CardUpdateState($Payload) {
			if(isset($Payload->Action)) {
				if(isset($Payload->CardID)) {
					if($Payload->Action === "DeActivate") {
						$SQL = <<<SQL
						UPDATE TCG.Card
						SET
							DeactivatedDateTime = CASE
								WHEN DeactivatedDateTime IS NULL THEN GETDATE()
								ELSE NULL
							END,
							ModifiedDateTime = GETDATE()
						OUTPUT
							Inserted.CardID,
							CASE
								WHEN Inserted.DeactivatedDateTime IS NULL THEN 1
								ELSE 0
							END AS IsActive
						WHERE
							CardID = {$Payload->CardID}
SQL;
					} else if($Payload->Action === "Delete") {
						$SQL = <<<SQL
						EXEC TCG.DeleteCard {$Payload->CardID};
SQL;
					}
				} else {
					if($Payload->Action === "Add") {						
						$SQL = <<<SQL
						EXEC TCG.QuickCreateCard
SQL;
					}
				}
				
				$result = API::query($SQL);
				echo json_encode($result);
			}
		}

		public static function DeckUpdateState($Payload) {
			if(isset($Payload->Action)) {
				if(isset($Payload->DeckID)) {
					if($Payload->Action === "DeActivate") {
						$SQL = <<<SQL
						UPDATE TCG.Deck
						SET
							DeactivatedDateTime = CASE
								WHEN DeactivatedDateTime IS NULL THEN GETDATE()
								ELSE NULL
							END,
							ModifiedDateTime = GETDATE()
						OUTPUT
							Inserted.DeckID,
							CASE
								WHEN Inserted.DeactivatedDateTime IS NULL THEN 1
								ELSE 0
							END AS IsActive
						WHERE
							DeckID = {$Payload->DeckID}
SQL;
					} else if($Payload->Action === "Delete") {
						$SQL = <<<SQL
						EXEC TCG.DeleteDeck {$Payload->DeckID};
SQL;
					}
				} else {
					if($Payload->Action === "Add") {						
						$SQL = <<<SQL
						EXEC TCG.QuickCreateDeck
SQL;
					}
				}
				
				$result = API::query($SQL);
				echo json_encode($result);
			}
		}
	}
?>