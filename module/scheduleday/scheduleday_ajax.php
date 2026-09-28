<?php

// SANTE SOLUTIONS - Schedule Days (DataTable + single-record fetch for Edit)
require_once("../../include/initialize.php");
global $mydb;

if (isset($_POST['SCHEDULEDAY_ID']) && !isset($_POST['draw'])) {
	$output = array();
	$mydb->setQuery("SELECT * FROM `tblscheduledays` WHERE SCHEDULEDAY_ID = '" . intval($_POST['SCHEDULEDAY_ID']) . "' LIMIT 1");
	$result = $mydb->loadResultList();

	foreach ($result as $row) {
		$output["SCHEDULEDAY_ID"] = $row->SCHEDULEDAY_ID;
		$output["DAY_NAME"]       = $row->DAY_NAME;
		$output["DESCRIPTION"]    = $row->DESCRIPTION;
	}
	echo json_encode($output);
} else {
	$from  = " FROM `tblscheduledays`";
	$where = '';
	if (isset($_POST["search"]["value"]) && $_POST["search"]["value"] !== '') {
		$term  = $mydb->escape_value($_POST["search"]["value"]);
		$where = " WHERE (`DAY_NAME` LIKE '%" . $term . "%' OR `DESCRIPTION` LIKE '%" . $term . "%')";
	}

	// Allowed sort columns only (DataTables column index => SQL column)
	$orderMap = array(1 => '`DAY_NAME`', 2 => '`DESCRIPTION`');
	$orderSql = ' ORDER BY `DAY_NAME` ASC ';
	if (isset($_POST["order"][0]["column"]) && isset($orderMap[intval($_POST["order"][0]["column"])])) {
		$dir = (isset($_POST["order"][0]["dir"]) && strtolower($_POST["order"][0]["dir"]) === 'desc') ? 'DESC' : 'ASC';
		$orderSql = ' ORDER BY ' . $orderMap[intval($_POST["order"][0]["column"])] . ' ' . $dir . ' ';
	}

	$limitSql = '';
	if (isset($_POST["length"]) && intval($_POST["length"]) != -1) {
		$limitSql = " LIMIT " . intval(isset($_POST['start']) ? $_POST['start'] : 0) . ", " . intval($_POST['length']);
	}

	$mydb->setQuery("SELECT *" . $from . $where . $orderSql . $limitSql);
	$cur  = $mydb->loadResultList();
	$data = array();
	$i = 1;
	foreach ($cur as $result) {
		$sub_array = array();
		$sub_array[] = $i;
		$sub_array[] = htmlspecialchars($result->DAY_NAME);
		$sub_array[] = !empty($result->DESCRIPTION) ? nl2br(htmlspecialchars($result->DESCRIPTION)) : '<span class="text-muted">&mdash;</span>';
		$sub_array[] = '<div class="text-nowrap">'
			. hipanao_action_btn('edit', 'SCHEDULEDAY_ID', $result->SCHEDULEDAY_ID, 'editEntry')
			. hipanao_action_btn('delete', 'SCHEDULEDAY_ID', $result->SCHEDULEDAY_ID, 'deleteEntry')
			. '</div>';
		$data[] = $sub_array;
		$i++;
	}

	$mydb->setQuery("SELECT COUNT(*) AS n" . $from);
	$tot = $mydb->loadResultList();
	$mydb->setQuery("SELECT COUNT(*) AS n" . $from . $where);
	$flt = $mydb->loadResultList();

	$output = array(
		'draw'            => isset($_POST['draw']) ? intval($_POST['draw']) : 0,
		'data'            => $data,
		"recordsTotal"    => count($tot) ? intval($tot[0]->n) : 0,
		"recordsFiltered" => count($flt) ? intval($flt[0]->n) : 0
	);
	echo json_encode($output);
}
?>
