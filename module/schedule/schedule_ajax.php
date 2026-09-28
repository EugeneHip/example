<?php

// SANTE SOLUTIONS - Set Schedule: DataTable + single-record fetch for Edit
require_once("../../include/initialize.php");
global $mydb;

if (isset($_POST['SCHEDULE_ID']) && !isset($_POST['draw'])) {
	// Raw values ng isang schedule record (para sa Edit form)
	$output = array();
	$mydb->setQuery("SELECT * FROM `tblschedule` WHERE SCHEDULE_ID = '" . intval($_POST['SCHEDULE_ID']) . "' LIMIT 1");
	$result = $mydb->loadResultList();

	foreach ($result as $row) {
		$output["SCHEDULE_ID"]     = $row->SCHEDULE_ID;
		$output["SUBJECT_ID"]      = $row->SUBJECT_ID;
		$output["CLASSROOM_ID"]    = $row->CLASSROOM_ID;
		$output["SCHEDULEDAY_ID"]  = $row->SCHEDULEDAY_ID;
		$output["SCHEDULETIME_ID"] = $row->SCHEDULETIME_ID;
		$output["INSTRUCTOR_ID"]   = $row->INSTRUCTOR_ID;
	}
	echo json_encode($output);
} else {
	// Listahan ng Set Schedule (naka-join na sa Subject, Course, Room, Day, Time at Instructor)
	$select = "SELECT sch.SCHEDULE_ID,
			sub.SUBJECT_CODE, sub.SUBJECT_NAME, sub.UNITS, sub.YEAR_LEVEL, sub.SEMESTER,
			co.COURSE_CODE,
			c.CLASSROOM_NAME,
			d.DAY_NAME,
			t.TIME_START, t.TIME_END,
			u.DISPLAYNAME AS INSTRUCTOR_NAME";
	$from = " FROM `tblschedule` sch
			INNER JOIN `tblsubjects` sub ON sub.SUBJECT_ID = sch.SUBJECT_ID
			LEFT JOIN `tblcourses` co ON co.COURSE_ID = sub.COURSE_ID
			INNER JOIN `tblclassrooms` c ON c.CLASSROOM_ID = sch.CLASSROOM_ID
			INNER JOIN `tblscheduledays` d ON d.SCHEDULEDAY_ID = sch.SCHEDULEDAY_ID
			INNER JOIN `tblscheduletimes` t ON t.SCHEDULETIME_ID = sch.SCHEDULETIME_ID
			LEFT JOIN `tblusers` u ON u.UID = sch.INSTRUCTOR_ID";

	$where = '';
	if (isset($_POST["search"]["value"]) && $_POST["search"]["value"] !== '') {
		$term = $mydb->escape_value($_POST["search"]["value"]);
		$where = " WHERE (sub.SUBJECT_CODE LIKE '%" . $term . "%'
				OR sub.SUBJECT_NAME LIKE '%" . $term . "%'
				OR co.COURSE_CODE LIKE '%" . $term . "%'
				OR sub.YEAR_LEVEL LIKE '%" . $term . "%'
				OR sub.SEMESTER LIKE '%" . $term . "%'
				OR c.CLASSROOM_NAME LIKE '%" . $term . "%'
				OR d.DAY_NAME LIKE '%" . $term . "%'
				OR u.DISPLAYNAME LIKE '%" . $term . "%')";
	}

	// Allowed sort columns only (DataTables column index => SQL column)
	$orderMap = array(
		1 => 't.TIME_START',
		2 => 'd.DAY_NAME',
		3 => 'sub.SUBJECT_CODE',
		4 => 'sub.SUBJECT_NAME',
		5 => 'co.COURSE_CODE, sub.YEAR_LEVEL',
		6 => 'sub.UNITS',
		7 => 'c.CLASSROOM_NAME',
		8 => 'u.DISPLAYNAME',
	);
	$orderSql = ' ORDER BY t.TIME_START ASC, sch.SCHEDULE_ID ASC ';
	if (isset($_POST["order"][0]["column"]) && isset($orderMap[intval($_POST["order"][0]["column"])])) {
		$dir = (isset($_POST["order"][0]["dir"]) && strtolower($_POST["order"][0]["dir"]) === 'desc') ? 'DESC' : 'ASC';
		$parts = array();
		foreach (explode(',', $orderMap[intval($_POST["order"][0]["column"])]) as $col) {
			$parts[] = trim($col) . ' ' . $dir;
		}
		$orderSql = ' ORDER BY ' . implode(', ', $parts) . ', sch.SCHEDULE_ID ASC ';
	}

	$limitSql = '';
	if (isset($_POST["length"]) && intval($_POST["length"]) != -1) {
		$limitSql = " LIMIT " . intval(isset($_POST['start']) ? $_POST['start'] : 0) . ", " . intval($_POST['length']);
	}

	$mydb->setQuery($select . $from . $where . $orderSql . $limitSql);
	$cur  = $mydb->loadResultList();
	$data = array();
	$i = 1;
	foreach ($cur as $result) {
		// Course cell: BSIT + (1st Year / 1st Semester); Minor/GE = "General Education"
		if (!empty($result->COURSE_CODE)) {
			$courseCell = '<span class="badge badge-primary">' . htmlspecialchars($result->COURSE_CODE) . '</span>';
		} else {
			$courseCell = '<span class="badge badge-secondary">General Education</span>';
		}
		$courseSub = trim(($result->YEAR_LEVEL ? $result->YEAR_LEVEL : '') . ($result->SEMESTER ? ' - ' . $result->SEMESTER : ''), ' -');
		if ($courseSub !== '') {
			$courseCell .= '<br><small class="text-muted">' . htmlspecialchars($courseSub) . '</small>';
		}

		$sub_array = array();
		$sub_array[] = $i;
		$sub_array[] = date('g:i A', strtotime($result->TIME_START)) . ' - ' . date('g:i A', strtotime($result->TIME_END));
		$sub_array[] = htmlspecialchars($result->DAY_NAME);
		$sub_array[] = htmlspecialchars($result->SUBJECT_CODE);
		$sub_array[] = htmlspecialchars($result->SUBJECT_NAME);
		$sub_array[] = $courseCell;
		$sub_array[] = intval($result->UNITS);
		$sub_array[] = htmlspecialchars($result->CLASSROOM_NAME);
		$sub_array[] = $result->INSTRUCTOR_NAME ? htmlspecialchars($result->INSTRUCTOR_NAME) : '<span class="text-muted">Not Assigned</span>';
		$sub_array[] = '<div class="text-nowrap">'
			. hipanao_action_btn('edit', 'SCHEDULE_ID', $result->SCHEDULE_ID, 'editEntry')
			. hipanao_action_btn('delete', 'SCHEDULE_ID', $result->SCHEDULE_ID, 'deleteEntry')
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
