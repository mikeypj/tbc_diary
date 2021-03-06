<?php
//////////////////////////////////////////////////////////////////////////////////////
//
//	Name:		admin_filters.php
//	Desc:		general filter and column sorting
//  Client:		LAD
//	Author:		Rob Curle
//	Date:		13 June 2005
//	Notes:
//
///////////////////////////////////////////////////////////////////////////////////////

	function build_where_clause($filters, $arr_fields) {

		$where_clause = "";

		if (is_array($filters)) {

			foreach ($filters as $field => $value) {

				if ($field == "current_event") {

					switch ($value) {

						case 1: $where_clause.= "event_date >= CURDATE() AND "; break;
						case 2: $where_clause.= "event_date < CURDATE() AND "; break; // past events
					}
				}	else if ($field == "current") {

					switch ($value) {

						case 1: $where_clause.= "(l.date_from >= CURDATE() OR l.date_to >= CURDATE()) AND "; break; //past events
						case 2: $where_clause.= "((l.date_from < CURDATE() AND l.date_to is null) OR (l.date_to < CURDATE() AND l.date_to is not null)) AND "; break; //past events
					}
				} else {

					if ($GLOBALS[$arr_fields][$field] == "info_screen") {

						$where_clause .= $GLOBALS[$arr_fields][$field] . " = " . ($value>0 ? "1":"0") . " AND ";
					} else {

						$where_clause .= $GLOBALS[$arr_fields][$field] . " LIKE '" . addslashes($value) . "%' AND ";
					}
				}
			}
		}

		if (!empty($where_clause)) {
			$where_clause = "WHERE " . substr($where_clause, 0, -5);	// remove last AND
		}

		return $where_clause;
	}

	function get_filter($type) {
		$db = new db_mysql;

		$db->query("SELECT name, value FROM filters WHERE type={$type} AND user_id={$_SESSION['admin_id']} ORDER BY name");
		while ($db->next_record()) {
			$filters[$db->f("name")] = $db->f("value");
		}

		return $filters;
	}

	function set_filter($type) {
		$db = new db_mysql;
		$db->query("DELETE FROM filters WHERE type={$type} AND user_id={$_SESSION['admin_id']}");

		foreach ($_POST as $field => $value) {
			if ($field=="a" || $field=="go_x" || $field=="go_y") continue;

			$trimmed_val = trim($value);
			if (!empty($trimmed_val)) {
				$db->query("INSERT INTO filters (user_id, type, name, value) VALUES ({$_SESSION['admin_id']}, {$type}, \"$field\", \"".addslashes($value)."\")");
			}
		}
	}


	function clear_filter($type) {
		$db = new db_mysql;

		$db->query("DELETE FROM filters WHERE type={$type} AND user_id={$_SESSION['admin_id']}");
	}

	function order_recordset($type, $arr_fields) {
		$db = new db_mysql;
		$direction = "";

		if (is_numeric($_GET['c'])) {
			$num = count($GLOBALS[$arr_fields]);

			if ($_GET['c']>=1 && $_GET['c']<=$num) {
				$db->query("SELECT sort_col, sort_dir FROM sort_columns WHERE type={$type} AND user_id={$_SESSION['admin_id']}");
				if ($db->num_rows() == 0) {		// add sorting data, if doesn't exist
					$db->query("INSERT INTO sort_columns (user_id, type, sort_col) VALUES ({$_SESSION['admin_id']}, {$type}, 1)");
					$db->query("SELECT sort_col, sort_dir FROM sort_columns WHERE type={$type} AND user_id={$_SESSION['admin_id']}");
				}
				$db->next_record();

				if ($db->f("sort_col") == $_GET['c']) {
					$direction = ($db->f("sort_dir")=="" ? "DESC":"");
				}
				$db->query("UPDATE sort_columns SET sort_col={$_GET['c']}, sort_dir='$direction' WHERE type={$type} AND user_id={$_SESSION['admin_id']}");
			}
		}
	}
?>