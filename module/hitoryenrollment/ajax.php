<?php

require_once("../../include/initialize.php");
global $mydb;

$act = isset($_POST['act']) ? $_POST['act'] : '';

/* HIPANAO SOLUTIONS - Profile + Subjects (kasama na ang huling Grade
   at Remarks) para sa View modal - kaparehong porma ng
   module/enrollmentdetails/ajax.php act=profile, may dagdag na
   GRADE/REMARKS column dahil "history" na ang mga record dito. */
if ($act === 'profile') {

	$id = intval($_POST['ENROLLMENT_ID']);
	$output = array();

	/* HIPANAO SOLUTIONS - Dagdag na LEFT JOIN papunta sa tblsections para
	   makuha ang SECTION_NAME, kagaya ng ginagawa sa module/enrollmentdetails
	   act=profile. Hindi na-store ang SECTION_ID sa tblhistoryenrollment
	   mismo, pero dahil hindi tinatanggal ang tblenrollment row kapag
	   na-archive (STATUS na lang ang pinapalitan - Completed/Dropped),
	   nandiyan pa rin ang SECTION_ID sa e.SECTION_ID. */
	$mydb->setQuery("SELECT e.*,
			s.IDNO, s.LNAME, s.FNAME, s.MNAME, s.SEX, s.BDAY, s.BPLACE, s.AGE,
			s.NATIONALITY, s.RELIGION, s.CONTACT_NO, s.HOME_ADD, s.EMAIL, s.COMPANYIDNO,
			c.COURSE_CODE, c.COURSE_NAME, sy.SCHOOL_YEAR, sec.SECTION_NAME
		FROM `tblenrollment` e
		JOIN `tblstudent`    s  ON s.S_ID      = e.S_ID
		JOIN `tblcourses`    c  ON c.COURSE_ID = e.COURSE_ID
		JOIN `tblschoolyear` sy ON sy.SY_ID    = e.SY_ID
		LEFT JOIN `tblsections` sec ON sec.SECTION_ID = e.SECTION_ID
		WHERE e.ENROLLMENT_ID = '".$id."' LIMIT 1");
	$rows = $mydb->loadResultList();

	if (count($rows) >= 1) {
		$r = $rows[0];

		$output['ENROLLMENT_ID'] = $r->ENROLLMENT_ID;
		$output['IDNO']          = $r->IDNO;
		$output['FULLNAME']      = trim($r->LNAME.', '.$r->FNAME.' '.$r->MNAME);
		$output['STATUS_BADGE']  = hipanao_badge($r->STATUS);

		$output['PICTURE_URL']   = (isset($r->COMPANYIDNO) && $r->COMPANYIDNO != '')
			? WEB_ROOT.'module/student/image/'.$r->COMPANYIDNO
			: '';

		$output['COURSE_TEXT']   = $r->COURSE_CODE.' - '.$r->COURSE_NAME;
		$output['YEAR_LEVEL']    = $r->YEAR_LEVEL;
		$output['SCHOOL_YEAR']   = $r->SCHOOL_YEAR;
		$output['SEMESTER']      = $r->SEMESTER;
		$output['SECTION_NAME']  = isset($r->SECTION_NAME) ? $r->SECTION_NAME : '-';

		$output['SEX_BADGE']     = hipanao_badge($r->SEX);
		$output['BDAY']          = $r->BDAY;
		$output['BPLACE']        = $r->BPLACE;
		$output['AGE']           = $r->AGE;
		$output['NATIONALITY']   = $r->NATIONALITY;
		$output['RELIGION']      = $r->RELIGION;

		$output['CONTACT_NO']    = $r->CONTACT_NO;
		$output['EMAIL']         = $r->EMAIL;
		$output['HOME_ADD']      = $r->HOME_ADD;

		$output['subjects']      = array();
		$mydb->setQuery("SELECT sub.SUBJECT_CODE, sub.SUBJECT_NAME, sub.UNITS, sub.SUBJECT_TYPE, g.GRADE, g.REMARKS
			FROM `tblenrollmentdetails` d
			JOIN `tblsubjects` sub ON sub.SUBJECT_ID = d.SUBJECT_ID
			LEFT JOIN `tblgrades` g ON g.ENROLLMENT_ID = d.ENROLLMENT_ID AND g.SUBJECT_ID = d.SUBJECT_ID
			WHERE d.ENROLLMENT_ID = '".$id."'
			ORDER BY sub.SUBJECT_TYPE ASC, sub.SUBJECT_CODE ASC");
		foreach ($mydb->loadResultList() as $sub) {
			$output['subjects'][] = array(
				'SUBJECT_CODE' => $sub->SUBJECT_CODE,
				'SUBJECT_NAME' => $sub->SUBJECT_NAME,
				'UNITS'        => $sub->UNITS,
				'SUBJECT_TYPE' => isset($sub->SUBJECT_TYPE) ? $sub->SUBJECT_TYPE : 'Major',
				'GRADE'        => ($sub->GRADE === null) ? null : floatval($sub->GRADE),
				'REMARKS'      => $sub->REMARKS
			);
		}
	}

	echo json_encode($output);
	exit;
}

/* HIPANAO SOLUTIONS - Listahan: binabase na ngayon sa `tblhistoryenrollment`
   (hindi na live-filtered `tblenrollment` query) - isang tunay na
   archive table na dedikado para dito. Isang row dito bawat record na
   na-"Completed" (naipalit dahil nag-Register Again + nag-Enroll ulit
   ang estudyante sa bagong term) o "Dropped" (dinrop mula sa
   Enrollment Details) - tingnan ang archiveEnrollmentToHistory() sa
   include/functions.php, ang tumatawag dito ay markAsEnrolled() at
   module/enrollmentdetails/controller.php::doDrop(). Sinusuportahan
   pa rin ang filter "by year" (sy_filter) at "semester" (sem_filter)
   na galing sa mga dropdown sa itaas ng listahan. */
$base = "FROM `tblhistoryenrollment` h
	JOIN `tblstudent`    s   ON s.S_ID      = h.S_ID
	JOIN `tblcourses`    c   ON c.COURSE_ID = h.COURSE_ID
	JOIN `tblschoolyear` sy  ON sy.SY_ID    = h.SY_ID
	LEFT JOIN `tblenrollmentdetails` d ON d.ENROLLMENT_ID = h.ENROLLMENT_ID
	LEFT JOIN `tblsubjects`   sub ON sub.SUBJECT_ID = d.SUBJECT_ID ";

$where = " WHERE 1=1 ";

if (isset($_POST['sy_filter']) && $_POST['sy_filter'] != '') {
	$where .= " AND h.SY_ID = '".intval($_POST['sy_filter'])."' ";
}
if (isset($_POST['sem_filter']) && $_POST['sem_filter'] != '') {
	$where .= " AND h.SEMESTER = '".$mydb->escape_value($_POST['sem_filter'])."' ";
}

if (isset($_POST["search"]["value"]) && $_POST["search"]["value"] != '') {
	$s = $mydb->escape_value($_POST["search"]["value"]);
	$where .= " AND (s.IDNO LIKE '%".$s."%'
				 OR s.LNAME LIKE '%".$s."%'
				 OR s.FNAME LIKE '%".$s."%'
				 OR c.COURSE_CODE LIKE '%".$s."%'
				 OR c.COURSE_NAME LIKE '%".$s."%'
				 OR h.YEAR_LEVEL LIKE '%".$s."%'
				 OR h.STATUS LIKE '%".$s."%'
				 OR sy.SCHOOL_YEAR LIKE '%".$s."%'
				 OR h.SEMESTER LIKE '%".$s."%'
				 OR sub.SUBJECT_CODE LIKE '%".$s."%'
				 OR sub.SUBJECT_NAME LIKE '%".$s."%') ";
}

$orderCols = array(
	1 => 's.IDNO',
	2 => 's.LNAME',
	3 => 'c.COURSE_CODE',
	4 => 'h.YEAR_LEVEL',
	5 => 'sy.SCHOOL_YEAR',
	6 => 'h.SEMESTER',
	7 => 'h.STATUS'
);

$orderBy = " ORDER BY h.DATE_ARCHIVED DESC, h.HISTORY_ID DESC ";
if (isset($_POST['order'][0]['column'])) {
	$ci = intval($_POST['order'][0]['column']);
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
		c.COURSE_CODE, h.YEAR_LEVEL, sy.SCHOOL_YEAR, h.SEMESTER, h.STATUS,
		GROUP_CONCAT(DISTINCT sub.SUBJECT_CODE ORDER BY sub.SUBJECT_CODE SEPARATOR ', ') AS SUBJECT_CODES,
		COUNT(DISTINCT d.DETAIL_ID) AS SUBJ_COUNT ";

$mydb->setQuery($select.$base.$where." GROUP BY h.HISTORY_ID ".$orderBy.$limit);
$rows = $mydb->loadResultList();

$mydb->setQuery("SELECT COUNT(DISTINCT h.HISTORY_ID) AS CNT ".$base.$where);
$filteredRows = $mydb->loadResultList();
$filtered = (count($filteredRows) >= 1) ? intval($filteredRows[0]->CNT) : 0;

$mydb->setQuery("SELECT COUNT(*) AS CNT FROM `tblhistoryenrollment`");
$totalRows = $mydb->loadResultList();
$total = (count($totalRows) >= 1) ? intval($totalRows[0]->CNT) : 0;

$data = array();
$i = isset($_POST['start']) ? intval($_POST['start']) + 1 : 1;

foreach ($rows as $r) {

	$rowName = htmlspecialchars(trim($r->LNAME.', '.$r->FNAME.' '.$r->MNAME), ENT_QUOTES);

	/* HIPANAO SOLUTIONS - Dagdag na "Delete" action (wala pa dati dito -
	   walang paraan ang staff para tanggalin ang isang maling na-archive
	   na history record). Tinatanggal nito ang buong tblenrollment row
	   (kasama ang mga subject/grade/history record nito - CASCADE sa
	   database), kaya dapat lang gamitin para sa mga talagang maling
	   record - tingnan ang doDelete() sa controller.php. */
	$actions = '<div class="text-nowrap">
		<button type="button" data-eid="'.$r->ENROLLMENT_ID.'" class="btn btn-outline-info btn-sm action-btn doViewHistoryProfile" title="View"><i class="fa fa-eye"></i></button>
		<a href="'.WEB_ROOT.'module/enrollmentdetails/print.php?id='.$r->ENROLLMENT_ID.'" target="_blank"><button type="button" class="btn btn-outline-secondary btn-sm action-btn" title="Print Enrollment Form"><i class="fa fa-print"></i></button></a>
		<button type="button" data-eid="'.$r->ENROLLMENT_ID.'" data-name="'.$rowName.'" class="btn btn-outline-danger btn-sm action-btn doDeleteHistory" title="Delete"><i class="fa fa-trash"></i></button>
	</div>';

	$subjCount = intval($r->SUBJ_COUNT);
	$subjectsCell = ($subjCount > 0 && !empty($r->SUBJECT_CODES))
		? htmlspecialchars($r->SUBJECT_CODES).' <span class="badge badge-secondary">'.$subjCount.'</span>'
		: '<span class="text-muted font-italic">No subjects taken</span>';

	$data[] = array(
		$i,
		htmlspecialchars($r->IDNO),
		htmlspecialchars(trim($r->LNAME.', '.$r->FNAME.' '.$r->MNAME)),
		htmlspecialchars($r->COURSE_CODE),
		htmlspecialchars($r->YEAR_LEVEL),
		htmlspecialchars($r->SCHOOL_YEAR),
		htmlspecialchars($r->SEMESTER),
		hipanao_badge($r->STATUS),
		$subjectsCell,
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
