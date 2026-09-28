<?php

// SANTE SOLUTIONS - Classrooms (DataTable + single-record fetch for Edit)
require_once("../../include/initialize.php");
global $mydb;

if (isset($_POST['CLASSROOM_ID']) && !isset($_POST['draw'])) {
	$output = array();
	$mydb->setQuery("SELECT * FROM `tblclassrooms` WHERE CLASSROOM_ID = '" . intval($_POST['CLASSROOM_ID']) . "' LIMIT 1");
	$result = $mydb->loadResultList();

	foreach ($result as $row) {
		$output["CLASSROOM_ID"]   = $row->CLASSROOM_ID;
		$output["CLASSROOM_NAME"] = $row->CLASSROOM_NAME;
		$output["DESCRIPTION"]    = $row->DESCRIPTION;
		$output["STATUS"]         = $row->STATUS;
	}
	echo json_encode($output);
} else {
	$from  = " FROM `tblclassrooms`";
	$where = '';
	if (isset($_POST["search"]["value"]) && $_POST["search"]["value"] !== '') {
		$term  = $mydb->escape_value($_POST["search"]["value"]);
		$where = " WHERE (`CLASSROOM_NAME` LIKE '%" . $term . "%' OR `DESCRIPTION` LIKE '%" . $term . "%')";
	}

	// Allowed sort columns only (DataTables column index => SQL column)
	$orderMap = array(1 => '`CLASSROOM_NAME`', 2 => '`DESCRIPTION`', 3 => '`STATUS`');
	$orderSql = ' ORDER BY `CLASSROOM_NAME` ASC ';
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
		$sub_array[] = htmlspecialchars($result->CLASSROOM_NAME);
		$sub_array[] = !empty($result->DESCRIPTION) ? nl2br(htmlspecialchars($result->DESCRIPTION)) : '<span class="text-muted">&mdash;</span>';
		$sub_array[] = hipanao_badge($result->STATUS);
		$sub_array[] = '<div class="text-nowrap">'
			. hipanao_action_btn('edit', 'CLASSROOM_ID', $result->CLASSROOM_ID, 'editEntry')
			. hipanao_action_btn('delete', 'CLASSROOM_ID', $result->CLASSROOM_ID, 'deleteEntry')
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
