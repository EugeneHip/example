<?php

require_once("../../include/initialize.php");
global $mydb;

$act = isset($_POST['act']) ? $_POST['act'] : '';

/* HIPANAO SOLUTIONS - "Grade" button data: subjects the student
   actually took for this enrollment (tblenrollmentdetails), plus
   any grade already encoded for each (tblgrades), so the modal opens
   pre-filled when re-editing. */
if ($act === 'subjects') {

	$id = intval($_POST['ENROLLMENT_ID']);
	$output = array('subjects' => array());

	$mydb->setQuery("SELECT e.ENROLLMENT_ID, e.S_ID, e.SY_ID, e.SEMESTER,
			s.IDNO, s.LNAME, s.FNAME, s.MNAME, s.COMPANYIDNO,
			c.COURSE_CODE, c.COURSE_NAME, sy.SCHOOL_YEAR
		FROM `tblenrollment` e
		JOIN `tblstudent` s ON s.S_ID = e.S_ID
		JOIN `tblcourses` c ON c.COURSE_ID = e.COURSE_ID
		JOIN `tblschoolyear` sy ON sy.SY_ID = e.SY_ID
		WHERE e.ENROLLMENT_ID = '".$id."' LIMIT 1");
	$rows = $mydb->loadResultList();

	if (count($rows) >= 1) {
		$r = $rows[0];
		$output['ENROLLMENT_ID'] = $r->ENROLLMENT_ID;
		$output['IDNO']          = $r->IDNO;
		$output['FULLNAME']      = trim($r->LNAME.', '.$r->FNAME.' '.$r->MNAME);
		$output['COURSE_TEXT']   = $r->COURSE_CODE.' - '.$r->COURSE_NAME;
		$output['TERM_TEXT']     = $r->SCHOOL_YEAR.' / '.$r->SEMESTER;
		$output['PICTURE_URL']   = (isset($r->COMPANYIDNO) && $r->COMPANYIDNO != '')
			? WEB_ROOT.'module/student/image/'.$r->COMPANYIDNO
			: '';

		$mydb->setQuery("SELECT sub.SUBJECT_ID, sub.SUBJECT_CODE, sub.SUBJECT_NAME, g.GRADE, g.REMARKS
			FROM `tblenrollmentdetails` d
			JOIN `tblsubjects` sub ON sub.SUBJECT_ID = d.SUBJECT_ID
			LEFT JOIN `tblgrades` g ON g.ENROLLMENT_ID = d.ENROLLMENT_ID AND g.SUBJECT_ID = d.SUBJECT_ID
			WHERE d.ENROLLMENT_ID = '".$id."'
			ORDER BY sub.SUBJECT_CODE ASC");
		foreach ($mydb->loadResultList() as $sub) {
			$output['subjects'][] = array(
				'SUBJECT_ID'   => $sub->SUBJECT_ID,
				'SUBJECT_CODE' => $sub->SUBJECT_CODE,
				'SUBJECT_NAME' => $sub->SUBJECT_NAME,
				'GRADE'        => ($sub->GRADE === null) ? null : floatval($sub->GRADE),
				'REMARKS'      => $sub->REMARKS
			);
		}
	}

	echo json_encode($output);
	exit;
}

/* HIPANAO SOLUTIONS - Listahan lang ng estudyanteng may subject na
   kinuha (kung wala pang subject, wala pang igra-grade, kaya wala
   silang dahilan para lumabas dito), AT "Enroll" pa rin ang STATUS
   (kasalukuyang term) - hindi na kasama dito ang mga naka-"Completed"
   o "Dropped" na, dahil doon na sila lumalabas sa "History Grade"
   (tingnan ang act=history_list sa ibaba). Dati'y wala itong STATUS
   filter kaya magkahalo ang kasalukuyan at lumang record dito. */
if ($act !== 'list' && $act !== 'history_list') {
	echo json_encode(array('data' => array(), 'recordsTotal' => 0, 'recordsFiltered' => 0));
	exit;
}

if ($act === 'history_list') {

	/* HIPANAO SOLUTIONS - "History Grade": mga grade ng mga
	   enrollment na naka-archive na (tblhistoryenrollment - "Completed"
	   dahil nag-Register Again + na-Enroll ulit ang estudyante sa
	   bagong term, o "Dropped"). Kaparehong pattern ng
	   module/hitoryenrollment/ajax.php - hindi tinatanggal ang
	   tblenrollment row kapag na-archive kaya nandiyan pa rin ang mga
	   subject/grade nito, tingnan lang natin dito via ENROLLMENT_ID. */
	$base = "FROM `tblhistoryenrollment` h
		JOIN `tblstudent`    s  ON s.S_ID      = h.S_ID
		JOIN `tblcourses`    c  ON c.COURSE_ID = h.COURSE_ID
		JOIN `tblschoolyear` sy ON sy.SY_ID    = h.SY_ID
		WHERE EXISTS (SELECT 1 FROM `tblenrollmentdetails` ed WHERE ed.ENROLLMENT_ID = h.ENROLLMENT_ID) ";

	$where = "";

	if (isset($_POST["search"]["value"]) && $_POST["search"]["value"] != '') {
		$s = $mydb->escape_value($_POST["search"]["value"]);
		$where .= " AND (s.IDNO LIKE '%".$s."%'
					 OR s.LNAME LIKE '%".$s."%'
					 OR s.FNAME LIKE '%".$s."%'
					 OR c.COURSE_CODE LIKE '%".$s."%'
					 OR h.YEAR_LEVEL LIKE '%".$s."%'
					 OR sy.SCHOOL_YEAR LIKE '%".$s."%'
					 OR h.SEMESTER LIKE '%".$s."%'
					 OR h.STATUS LIKE '%".$s."%') ";
	}

	$orderCols = array(
		0 => 's.IDNO',
		1 => 's.LNAME',
		2 => 'c.COURSE_CODE',
		3 => 'h.YEAR_LEVEL',
		4 => 'sy.SCHOOL_YEAR'
	);

	$orderBy = " ORDER BY h.DATE_ARCHIVED DESC, h.HISTORY_ID DESC ";
	if (isset($_POST['order'][0]['column'])) {
		$ci = intval($_POST['order'][0]['column']) - 1;
		$dir = (isset($_POST['order'][0]['dir']) && strtolower($_POST['order'][0]['dir']) == 'asc') ? 'ASC' : 'DESC';
		if (isset($orderCols[$ci])) {
			$orderBy = " ORDER BY ".$orderCols[$ci]." ".$dir." ";
		}
	}

	$limit = "";
	if (isset($_POST['length']) && $_POST['length'] != -1) {
		$limit = " LIMIT ".intval($_POST['start']).", ".intval($_POST['length'])." ";
	}

	$select = "SELECT h.HISTORY_ID, h.ENROLLMENT_ID, s.IDNO, s.LNAME, s.FNAME, s.MNAME,
			c.COURSE_CODE, h.YEAR_LEVEL, sy.SCHOOL_YEAR, h.SEMESTER, h.STATUS ";

	$mydb->setQuery($select.$base.$where." GROUP BY h.HISTORY_ID ".$orderBy.$limit);
	$rows = $mydb->loadResultList();

	$mydb->setQuery("SELECT COUNT(DISTINCT h.HISTORY_ID) AS CNT ".$base.$where);
	$filteredRows = $mydb->loadResultList();
	$filtered = (count($filteredRows) >= 1) ? intval($filteredRows[0]->CNT) : 0;

	$mydb->setQuery("SELECT COUNT(DISTINCT h.HISTORY_ID) AS CNT ".$base);
	$totalRows = $mydb->loadResultList();
	$total = (count($totalRows) >= 1) ? intval($totalRows[0]->CNT) : 0;

	$data = array();
	$i = isset($_POST['start']) ? intval($_POST['start']) + 1 : 1;

	foreach ($rows as $r) {

		$mydb->setQuery("SELECT COUNT(*) AS CNT FROM `tblenrollmentdetails` WHERE ENROLLMENT_ID = '".$r->ENROLLMENT_ID."'");
		$subjRows = $mydb->loadResultList();
		$subjCount = (count($subjRows) >= 1) ? intval($subjRows[0]->CNT) : 0;

		$mydb->setQuery("SELECT COUNT(*) AS CNT FROM `tblgrades` WHERE ENROLLMENT_ID = '".$r->ENROLLMENT_ID."' AND GRADE IS NOT NULL");
		$gradedRows = $mydb->loadResultList();
		$gradedCount = (count($gradedRows) >= 1) ? intval($gradedRows[0]->CNT) : 0;

		$gradedCell = ($subjCount > 0 && $gradedCount >= $subjCount)
			? '<span class="badge badge-success">'.$gradedCount.' of '.$subjCount.' graded</span>'
			: '<span class="badge badge-secondary">'.$gradedCount.' of '.$subjCount.' graded</span>';

		$actions = '<div class="text-nowrap">
			<button type="button" EID="'.$r->ENROLLMENT_ID.'" class="btn btn-outline-info btn-sm action-btn doViewGrade" title="View"><i class="fa fa-eye"></i></button>
			<button type="button" data-eid="'.$r->ENROLLMENT_ID.'" data-name="'.htmlspecialchars(trim($r->LNAME.', '.$r->FNAME.' '.$r->MNAME), ENT_QUOTES).'" class="btn btn-outline-danger btn-sm action-btn doDeleteGradeHistory" title="Delete"><i class="fa fa-trash"></i></button>
		</div>';

		$data[] = array(
			$i,
			htmlspecialchars($r->IDNO),
			htmlspecialchars(trim($r->LNAME.', '.$r->FNAME.' '.$r->MNAME)),
			htmlspecialchars($r->COURSE_CODE),
			htmlspecialchars($r->YEAR_LEVEL),
			htmlspecialchars($r->SCHOOL_YEAR.' / '.$r->SEMESTER),
			hipanao_badge($r->STATUS),
			$gradedCell,
			$actions
		);
		$i++;
	}

	echo json_encode(array(
		'data'            => $data,
		'recordsTotal'    => $total,
		'recordsFiltered' => $filtered
	));
	exit;
}

/* Listahan lang ng estudyanteng may subject na kinuha AT "Enroll" pa
   rin ang STATUS (kasalukuyang term - tingnan ang paliwanag sa
   itaas). */
$base = "FROM `tblenrollment` e
	JOIN `tblstudent`    s  ON s.S_ID      = e.S_ID
	JOIN `tblcourses`    c  ON c.COURSE_ID = e.COURSE_ID
	JOIN `tblschoolyear` sy ON sy.SY_ID    = e.SY_ID
	WHERE e.STATUS = 'Enroll'
	  AND EXISTS (SELECT 1 FROM `tblenrollmentdetails` ed WHERE ed.ENROLLMENT_ID = e.ENROLLMENT_ID) ";

$where = "";

if (isset($_POST["search"]["value"]) && $_POST["search"]["value"] != '') {
	$s = $mydb->escape_value($_POST["search"]["value"]);
	$where .= " AND (s.IDNO LIKE '%".$s."%'
				 OR s.LNAME LIKE '%".$s."%'
				 OR s.FNAME LIKE '%".$s."%'
				 OR c.COURSE_CODE LIKE '%".$s."%'
				 OR e.YEAR_LEVEL LIKE '%".$s."%'
				 OR sy.SCHOOL_YEAR LIKE '%".$s."%'
				 OR e.SEMESTER LIKE '%".$s."%') ";
}

$orderCols = array(
	0 => 's.IDNO',
	1 => 's.LNAME',
	2 => 'c.COURSE_CODE',
	3 => 'e.YEAR_LEVEL',
	4 => 'sy.SCHOOL_YEAR'
);

$orderBy = " ORDER BY e.ENROLLMENT_ID DESC ";
if (isset($_POST['order'][0]['column'])) {
	$ci = intval($_POST['order'][0]['column']) - 1;
	$dir = (isset($_POST['order'][0]['dir']) && strtolower($_POST['order'][0]['dir']) == 'asc') ? 'ASC' : 'DESC';
	if (isset($orderCols[$ci])) {
		$orderBy = " ORDER BY ".$orderCols[$ci]." ".$dir." ";
	}
}

$limit = "";
if (isset($_POST['length']) && $_POST['length'] != -1) {
	$limit = " LIMIT ".intval($_POST['start']).", ".intval($_POST['length'])." ";
}

$select = "SELECT e.ENROLLMENT_ID, s.IDNO, s.LNAME, s.FNAME, s.MNAME,
		c.COURSE_CODE, e.YEAR_LEVEL, sy.SCHOOL_YEAR, e.SEMESTER ";

$mydb->setQuery($select.$base.$where.$orderBy.$limit);
$rows = $mydb->loadResultList();

$mydb->setQuery("SELECT e.ENROLLMENT_ID ".$base.$where);
$filtered = $mydb->num_rows();

$mydb->setQuery("SELECT e.ENROLLMENT_ID ".$base);
$total = $mydb->num_rows();

$data = array();
$i = isset($_POST['start']) ? intval($_POST['start']) + 1 : 1;

foreach ($rows as $r) {

	$actions = '<div class="text-nowrap">
		<button type="button" EID="'.$r->ENROLLMENT_ID.'" class="btn btn-info btn-xs doGrade" title="Grade">
			<span class="fa fa-graduation-cap fw-fa"></span> Grade
		</button>
		<button type="button" EID="'.$r->ENROLLMENT_ID.'" class="btn btn-outline-info btn-xs doViewGrade" title="View">
			<span class="fa fa-eye fw-fa"></span> View
		</button>
	</div>';

	$data[] = array(
		$i,
		htmlspecialchars($r->IDNO),
		htmlspecialchars(trim($r->LNAME.', '.$r->FNAME.' '.$r->MNAME)),
		htmlspecialchars($r->COURSE_CODE),
		htmlspecialchars($r->YEAR_LEVEL),
		htmlspecialchars($r->SCHOOL_YEAR.' / '.$r->SEMESTER),
		$actions
	);
	$i++;
}

echo json_encode(array(
	'data'            => $data,
	'recordsTotal'    => $total,
	'recordsFiltered' => $filtered
));
?>
