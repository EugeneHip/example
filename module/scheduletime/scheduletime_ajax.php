<?php

// SANTE SOLUTIONS - Schedule Times (DataTable + single-record fetch for Edit)
require_once("../../include/initialize.php");
global $mydb;

if (isset($_POST['SCHEDULETIME_ID']) && !isset($_POST['draw'])) {
	$output = array();
	$mydb->setQuery("SELECT * FROM `tblscheduletimes` WHERE SCHEDULETIME_ID = '" . intval($_POST['SCHEDULETIME_ID']) . "' LIMIT 1");
	$result = $mydb->loadResultList();

	foreach ($result as $row) {
		$output["SCHEDULETIME_ID"] = $row->SCHEDULETIME_ID;
		$output["TIME_START"]      = date('H:i', strtotime($row->TIME_START));
		$output["TIME_END"]        = date('H:i', strtotime($row->TIME_END));
		$output["DESCRIPTION"]     = $row->DESCRIPTION;
	}
	echo json_encode($output);
} else {
	$from  = " FROM `tblscheduletimes`";
	$where = '';
	if (isset($_POST["search"]["value"]) && $_POST["search"]["value"] !== '') {
		$term  = $mydb->escape_value($_POST["search"]["value"]);
		$where = " WHERE (`DESCRIPTION` LIKE '%" . $term . "%' OR TIME_FORMAT(`TIME_START`, '%l:%i %p') LIKE '%" . $term . "%' OR TIME_FORMAT(`TIME_END`, '%l:%i %p') LIKE '%" . $term . "%')";
	}

	// Allowed sort columns only (DataTables column index => SQL column)
	$orderMap = array(1 => '`TIME_START`', 2 => '`TIME_END`', 3 => '`DESCRIPTION`');
	$orderSql = ' ORDER BY `TIME_START` ASC ';
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
		$sub_array[] = date('g:i A', strtotime($result->TIME_START));
		$sub_array[] = date('g:i A', strtotime($result->TIME_END));
		$sub_array[] = !empty($result->DESCRIPTION) ? nl2br(htmlspecialchars($result->DESCRIPTION)) : '<span class="text-muted">&mdash;</span>';
		$sub_array[] = '<div class="text-nowrap">'
			. hipanao_action_btn('edit', 'SCHEDULETIME_ID', $result->SCHEDULETIME_ID, 'editEntry')
			. hipanao_action_btn('delete', 'SCHEDULETIME_ID', $result->SCHEDULETIME_ID, 'deleteEntry')
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
