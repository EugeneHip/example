<?php

require_once("../../include/initialize.php");
global $mydb;

$act = isset($_POST['act']) ? $_POST['act'] : '';

/* HIPANAO SOLUTIONS - Full student + enrollment profile for the View
   modal. Everything the Student > View page shows, just fetched here
   so the admin never has to leave Enrollment Details to see it. */
if ($act === 'profile') {

	$id = intval($_POST['ENROLLMENT_ID']);
	$output = array();

	$mydb->setQuery("SELECT e.*,
			s.IDNO, s.LNAME, s.FNAME, s.MNAME, s.SEX, s.BDAY, s.BPLACE, s.AGE,
			s.NATIONALITY, s.RELIGION, s.CONTACT_NO, s.HOME_ADD, s.EMAIL, s.STATUS AS S_STATUS, s.COMPANYIDNO,
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
		/* BUG FIX: dating iniikot ang tblgrades gamit ang g.S_ID = IDNO
		   (mali - ang IDNO ay ang ID Number/string ng estudyante, hindi
		   ang tblgrades.S_ID na numeric FK papuntang tblstudent.S_ID),
		   kaya kahit may nailagay nang grade ay hindi ito nahahanap at
		   laging "-" ang lumalabas dito. Ang tama, kagaya ng ginagamit
		   sa module/grade/ajax.php at module/hitoryenrollment/ajax.php,
		   ay itugma ang ENROLLMENT_ID + SUBJECT_ID. */
		$mydb->setQuery("SELECT sub.SUBJECT_CODE, sub.SUBJECT_NAME, sub.UNITS, sub.SUBJECT_TYPE,
				g.GRADE, g.REMARKS
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
				'GRADE'        => isset($sub->GRADE) ? $sub->GRADE : '-',
				'REMARKS'      => isset($sub->REMARKS) ? $sub->REMARKS : '-'
			);
		}
	}

	echo json_encode($output);
	exit;
}

/* HIPANAO SOLUTIONS - Same checklist data shape as Enrollment >
   Subjects (module/enrollment/ajax.php act=subjects_data), so Manage
   Subjects here saves through the exact same saveEnrollmentSubjects()
   helper and never gets out of sync with that module. */
if ($act === 'subjects_data') {

	$id = intval($_POST['ENROLLMENT_ID']);
	$output = array('subjects' => array(), 'checked' => array());

	$mydb->setQuery("SELECT e.*,
			s.IDNO, s.LNAME, s.FNAME, s.MNAME, s.COMPANYIDNO,
			c.COURSE_CODE, c.COURSE_NAME
		FROM `tblenrollment` e
		JOIN `tblstudent` s ON s.S_ID = e.S_ID
		JOIN `tblcourses` c ON c.COURSE_ID = e.COURSE_ID
		WHERE e.ENROLLMENT_ID = '".$id."' LIMIT 1");
	$rows = $mydb->loadResultList();

	if (count($rows) >= 1) {
		$r = $rows[0];
		$output['ENROLLMENT_ID'] = $r->ENROLLMENT_ID;
		$output['IDNO']          = $r->IDNO;
		$output['FULLNAME']      = trim($r->LNAME.', '.$r->FNAME.' '.$r->MNAME);
		$output['COURSE_TEXT']   = $r->COURSE_CODE.' - '.$r->COURSE_NAME;
		$output['STATUS']        = $r->STATUS;
		$output['PICTURE_URL']   = (isset($r->COMPANYIDNO) && $r->COMPANYIDNO != '')
			? WEB_ROOT.'module/student/image/'.$r->COMPANYIDNO
			: '';

		/* Major subjects ng course na ito + lahat ng Minor subjects
		   (COURSE_ID IS NULL sa mga iyon - bukas sa lahat ng course).
		   Ipinapakita lang ang mga subject na para sa YEAR_LEVEL at
		   SEMESTER ng enrollment na ito (o walang partikular na
		   year level/semester), para hindi magkahalo-halo ang subjects
		   ng ibang year o ibang semester.

		   HIPANAO SOLUTIONS - Kung "Summer" ang SEMESTER ng enrollment
		   na ito, lahat ng subjects ng year level na iyon (1st Semester
		   man o 2nd Semester) ay dapat magamit/makuha - dito karaniwang
		   naglo-load ang mga estudyante ng subjects mula sa parehong
		   semester para makabawi/makaunlad. */
		$semesterFilter = "SEMESTER IS NULL OR SEMESTER = ''";
		if (strtolower(trim($r->SEMESTER)) == 'summer') {
			$semesterFilter .= " OR SEMESTER = '1st Semester' OR SEMESTER = '2nd Semester' OR SEMESTER = 'Summer'";
		} else {
			$semesterFilter .= " OR SEMESTER = '".$mydb->escape_value($r->SEMESTER)."'";
		}

		$mydb->setQuery("SELECT SUBJECT_ID, SUBJECT_CODE, SUBJECT_NAME, UNITS, YEAR_LEVEL, SEMESTER, SUBJECT_TYPE
			FROM `tblsubjects`
			WHERE (COURSE_ID = '".intval($r->COURSE_ID)."' OR COURSE_ID IS NULL)
			AND (YEAR_LEVEL = '".$mydb->escape_value($r->YEAR_LEVEL)."' OR YEAR_LEVEL IS NULL OR YEAR_LEVEL = '')
			AND (".$semesterFilter.")
			ORDER BY SUBJECT_TYPE ASC, YEAR_LEVEL ASC, SEMESTER ASC, SUBJECT_CODE ASC");
		foreach ($mydb->loadResultList() as $sub) {
			$output['subjects'][] = array(
				'SUBJECT_ID'   => $sub->SUBJECT_ID,
				'SUBJECT_CODE' => $sub->SUBJECT_CODE,
				'SUBJECT_NAME' => $sub->SUBJECT_NAME,
				'UNITS'        => $sub->UNITS,
				'YEAR_LEVEL'   => $sub->YEAR_LEVEL,
				'SEMESTER'     => $sub->SEMESTER,
				'SUBJECT_TYPE' => isset($sub->SUBJECT_TYPE) ? $sub->SUBJECT_TYPE : 'Major'
			);
		}

		$mydb->setQuery("SELECT SUBJECT_ID FROM `tblenrollmentdetails` WHERE ENROLLMENT_ID = '".$id."'");
		foreach ($mydb->loadResultList() as $d) {
			$output['checked'][] = intval($d->SUBJECT_ID);
		}
	}

	echo json_encode($output);
	exit;
}

/* HIPANAO SOLUTIONS - Requirements checklist para sa mga estudyanteng
   "Enroll" na - baka may kulang pang naisumite noong Document stage
   (module/enrollment) na napansin lang ngayon, kaya dito rin ma-tsek/
   ma-edit. Iisa lang ang checklist na ito per estudyante (nasa
   tblstudent, hindi tblenrollment) - tingnan din ang parehong
   REQ_* fields sa module/enrollment (Document stage). */
if ($act === 'requirements_data') {

	$id = intval($_POST['ENROLLMENT_ID']);
	$output = array();

	$mydb->setQuery("SELECT e.ENROLLMENT_ID, e.STATUS,
			s.IDNO, s.LNAME, s.FNAME, s.MNAME, s.COMPANYIDNO,
			s.REQ_FORM138, s.REQ_GOODMORAL, s.REQ_BIRTHCERT, s.REQ_BAPTISMAL, s.REQ_ASSESSMENT,
			s.REQ_TRANSFERCRED, s.REQ_MARRIAGECONTRACT, s.REQ_2X2PICTURE,
			s.REQ_OTHERS1, s.REQ_OTHERS2, s.REQ_OTHERS3, s.REQ_NOTES
		FROM `tblenrollment` e
		JOIN `tblstudent` s ON s.S_ID = e.S_ID
		WHERE e.ENROLLMENT_ID = '".$id."' LIMIT 1");
	$rows = $mydb->loadResultList();

	if (count($rows) >= 1) {
		$r = $rows[0];
		$output['ENROLLMENT_ID']       = $r->ENROLLMENT_ID;
		$output['IDNO']                = $r->IDNO;
		$output['FULLNAME']            = trim($r->LNAME.', '.$r->FNAME.' '.$r->MNAME);
		$output['STATUS']              = $r->STATUS;
		$output['PICTURE_URL']         = (isset($r->COMPANYIDNO) && $r->COMPANYIDNO != '')
			? WEB_ROOT.'module/student/image/'.$r->COMPANYIDNO
			: '';
		$output['REQ_FORM138']          = isset($r->REQ_FORM138) ? intval($r->REQ_FORM138) : 0;
		$output['REQ_GOODMORAL']        = isset($r->REQ_GOODMORAL) ? intval($r->REQ_GOODMORAL) : 0;
		$output['REQ_BIRTHCERT']        = isset($r->REQ_BIRTHCERT) ? intval($r->REQ_BIRTHCERT) : 0;
		$output['REQ_BAPTISMAL']        = isset($r->REQ_BAPTISMAL) ? intval($r->REQ_BAPTISMAL) : 0;
		$output['REQ_ASSESSMENT']       = isset($r->REQ_ASSESSMENT) ? intval($r->REQ_ASSESSMENT) : 0;
		$output['REQ_TRANSFERCRED']     = isset($r->REQ_TRANSFERCRED) ? intval($r->REQ_TRANSFERCRED) : 0;
		$output['REQ_MARRIAGECONTRACT'] = isset($r->REQ_MARRIAGECONTRACT) ? intval($r->REQ_MARRIAGECONTRACT) : 0;
		$output['REQ_2X2PICTURE']       = isset($r->REQ_2X2PICTURE) ? intval($r->REQ_2X2PICTURE) : 0;
		$output['REQ_OTHERS1']          = isset($r->REQ_OTHERS1) ? $r->REQ_OTHERS1 : '';
		$output['REQ_OTHERS2']          = isset($r->REQ_OTHERS2) ? $r->REQ_OTHERS2 : '';
		$output['REQ_OTHERS3']          = isset($r->REQ_OTHERS3) ? $r->REQ_OTHERS3 : '';
		$output['REQ_NOTES']            = isset($r->REQ_NOTES) ? $r->REQ_NOTES : '';
	}

	echo json_encode($output);
	exit;
}

/* HIPANAO SOLUTIONS - Full enrollment row for the Edit modal (Course,
   Section, Academic Year, Semester, Year Level, Category, Curriculum,
   Status, dates) - kaparehong shape ng module/enrollment/ajax.php
   act=row, dinala na lang dito para hindi na kailangang lumabas ng
   Enrollment Details module para lang mag-edit ng record. */
if ($act === 'row') {

	$id = intval($_POST['ENROLLMENT_ID']);
	$output = array();

	$mydb->setQuery("SELECT e.*,
			s.IDNO, s.LNAME, s.FNAME, s.MNAME, s.COMPANYIDNO,
			c.COURSE_CODE, c.COURSE_NAME,
			sy.SCHOOL_YEAR
		FROM `tblenrollment` e
		JOIN `tblstudent`    s  ON s.S_ID      = e.S_ID
		JOIN `tblcourses`    c  ON c.COURSE_ID = e.COURSE_ID
		JOIN `tblschoolyear` sy ON sy.SY_ID    = e.SY_ID
		WHERE e.ENROLLMENT_ID = '".$id."' LIMIT 1");

	foreach ($mydb->loadResultList() as $r) {
		$output['ENROLLMENT_ID'] = $r->ENROLLMENT_ID;
		$output['S_ID']          = $r->S_ID;
		$output['IDNO']          = $r->IDNO;
		$output['FULLNAME']      = trim($r->LNAME.', '.$r->FNAME.' '.$r->MNAME);

		$output['PICTURE_URL']   = (isset($r->COMPANYIDNO) && $r->COMPANYIDNO != '')
			? WEB_ROOT.'module/student/image/'.$r->COMPANYIDNO
			: '';

		$output['COURSE_ID']     = $r->COURSE_ID;
		$output['COURSE_TEXT']   = $r->COURSE_CODE.' - '.$r->COURSE_NAME;
		$output['SY_ID']         = $r->SY_ID;
		$output['SCHOOL_YEAR']   = $r->SCHOOL_YEAR;
		$output['SEMESTER']      = $r->SEMESTER;
		$output['YEAR_LEVEL']    = $r->YEAR_LEVEL;
		$output['CATEGORY']      = $r->CATEGORY;
		$output['CURRICULUM_YR'] = ($r->CURRICULUM_YR === null) ? '' : $r->CURRICULUM_YR;
		$output['SECTION_ID']    = ($r->SECTION_ID === null) ? '' : $r->SECTION_ID;
		$output['STATUS']        = $r->STATUS;

		$output['DATE_RESERVED'] = ($r->DATE_RESERVED === null || $r->DATE_RESERVED == '0000-00-00') ? '' : substr($r->DATE_RESERVED, 0, 10);
		$output['DATE_ENROLLED'] = ($r->DATE_ENROLLED === null || $r->DATE_ENROLLED == '0000-00-00') ? '' : substr($r->DATE_ENROLLED, 0, 10);
	}

	echo json_encode($output);
	exit;
}

/* HIPANAO SOLUTIONS - Section choices para sa Edit modal, i-filter base
   sa Course + Academic Year + Year Level (kaparehong helper ng
   module/enrollment/ajax.php act=sections). */
if ($act === 'sections') {

	$course = intval($_POST['COURSE_ID']);
	$sy     = intval($_POST['SY_ID']);
	$yearLevel = isset($_POST['YEAR_LEVEL']) ? trim($_POST['YEAR_LEVEL']) : '';
	$rows   = array();

	if ($course > 0 && $sy > 0) {
		$sql = "SELECT SECTION_ID, SECTION_NAME, YEAR_LEVEL
			FROM `tblsections`
			WHERE COURSE_ID = '".$course."' AND SY_ID = '".$sy."' ";
		if ($yearLevel != '') {
			$sql .= "AND YEAR_LEVEL = '".$mydb->escape_value($yearLevel)."' ";
		}
		$sql .= "ORDER BY YEAR_LEVEL ASC, SECTION_NAME ASC";

		$mydb->setQuery($sql);
		foreach ($mydb->loadResultList() as $row) {
			$rows[] = array(
				'SECTION_ID'   => $row->SECTION_ID,
				'SECTION_NAME' => $row->SECTION_NAME,
				'YEAR_LEVEL'   => $row->YEAR_LEVEL
			);
		}
	}

	echo json_encode($rows);
	exit;
}

/* HIPANAO SOLUTIONS - "Register Again" (Stage 1, kaparehong daloy ng
   Student > Register Enrollment Slot, pero dito na direkta mula sa
   Enrollment Details, dahil dito na natin makikita ang mga estudyanteng
   tapos na ang kasalukuyang semester/taon). Nagbibigay ng suhestiyon
   kung ano ang susunod na Year Level / Semester base sa kasalukuyang
   record ng estudyante - editable pa rin ito sa modal kung mali. */
if ($act === 'register_info') {

	$id = intval($_POST['ENROLLMENT_ID']);
	$output = array();

	$mydb->setQuery("SELECT e.*, s.IDNO, s.LNAME, s.FNAME, s.MNAME, s.COMPANYIDNO
		FROM `tblenrollment` e
		JOIN `tblstudent` s ON s.S_ID = e.S_ID
		WHERE e.ENROLLMENT_ID = '".$id."' LIMIT 1");
	$rows = $mydb->loadResultList();

	if (count($rows) < 1) {
		echo json_encode($output);
		exit;
	}
	$r = $rows[0];

	$output['ENROLLMENT_ID'] = $r->ENROLLMENT_ID;
	$output['S_ID']        = $r->S_ID;
	$output['IDNO']        = $r->IDNO;
	$output['FULLNAME']    = trim($r->LNAME.', '.$r->FNAME.' '.$r->MNAME);
	$output['COURSE_ID']   = $r->COURSE_ID;
	$output['PICTURE_URL'] = (isset($r->COMPANYIDNO) && $r->COMPANYIDNO != '')
		? WEB_ROOT.'module/student/image/'.$r->COMPANYIDNO
		: '';

	/* HIPANAO SOLUTIONS - Hindi dapat makapag-"Register Again" ang
	   estudyante kung hindi pa bayad nang buo ang tuition niya at wala
	   pa siyang kumpletong grade para sa KASALUKUYANG enrollment na ito
	   - tingnan ang canRegisterAgain() sa include/functions.php. Ang
	   frontend (module/enrollmentdetails/index.php) ang gagamit nito
	   para hindi na lang basta bubukas ang Register Again modal. */
	$eligibility = canRegisterAgain($r->ENROLLMENT_ID);
	$output['CAN_REGISTER']  = $eligibility['ok'];
	$output['BLOCK_REASON']  = $eligibility['reason'];

	$output['ACTIVE_SY'] = '';
	$output['ACTIVE_AY'] = '';
	$mydb->setQuery("SELECT SY_ID, SCHOOL_YEAR FROM `tblschoolyear` WHERE STATUS = 'Active' ORDER BY SY_ID DESC LIMIT 1");
	foreach ($mydb->loadResultList() as $row) {
		$output['ACTIVE_SY'] = $row->SY_ID;
		$output['ACTIVE_AY'] = $row->SCHOOL_YEAR;
	}

	/* Kung tapos na ang 1st Semester, i-suggest ang 2nd Semester (same
	   Year Level pa rin). Kung tapos na ang 2nd Semester o Summer,
	   i-suggest ang 1st Semester ng SUSUNOD na Year Level (dito
	   "nagiging 2nd Year" atbp. ang estudyante). */
	$yearLevels = array('1st Year', '2nd Year', '3rd Year', '4th Year');
	$curYear = $r->YEAR_LEVEL;
	$curSem  = $r->SEMESTER;
	$idx = array_search($curYear, $yearLevels);

	if ($curSem == '1st Semester') {
		$output['NEXT_SEMESTER']  = '2nd Semester';
		$output['NEXT_YEARLEVEL'] = $curYear;
	} else {
		$output['NEXT_SEMESTER'] = '1st Semester';
		$output['NEXT_YEARLEVEL'] = ($idx !== false && $idx < count($yearLevels) - 1)
			? $yearLevels[$idx + 1]
			: $curYear;
	}

	echo json_encode($output);
	exit;
}

/* HIPANAO SOLUTIONS - List: ONE row per student/enrollment (kagaya ng
   Student at Enrollment modules), hindi na isang row bawat subject.
   Ang mga subject ay pinagsasama-sama (GROUP_CONCAT) sa isang column.

   BUG FIX: dating INNER JOIN ang tblenrollmentdetails/tblsubjects,
   kaya ang enrollment na WALA pang subject na naka-assign ay
   TULUYANG NAWAWALA sa listahang ito - hindi na makikita, hindi na
   ma-cclick ang "Manage Subjects" para dagdagan ng subject (chicken-
   and-egg: kailangan munang may subject bago lumabas ang row, pero
   kailangan munang lumabas ang row bago maka-Manage Subjects).
   Ito ang dahilan kung bakit "hindi mapupunta sa enrollment details"
   kahit bayad na ang enrollment fee - LEFT JOIN na ngayon para lumabas
   pa rin ang lahat ng enrollment kahit wala pang subjects. */
$base = "FROM `tblenrollment` e
	JOIN `tblstudent`    s   ON s.S_ID      = e.S_ID
	JOIN `tblcourses`    c   ON c.COURSE_ID = e.COURSE_ID
	JOIN `tblschoolyear` sy  ON sy.SY_ID    = e.SY_ID
	LEFT JOIN `tblenrollmentdetails` d ON d.ENROLLMENT_ID = e.ENROLLMENT_ID
	LEFT JOIN `tblsubjects`   sub ON sub.SUBJECT_ID = d.SUBJECT_ID ";

/* HIPANAO SOLUTIONS - Ang Enrollment Details module ay para lang sa mga
   estudyanteng OPISYAL nang "Enroll" (nag-Enroll na sa Enrollment
   module pagkatapos mabayaran nang buo). Kaya lang sila lumalabas dito -
   nawawala sila sa Enrollment module (tingnan ang filter doon sa
   "e.STATUS <> 'Enroll'") at doon na sila makikita dito, saka na pwede
   dito mag-add/mag-drop pa ng subjects gamit ang Manage Subjects. */
$where = " WHERE e.STATUS = 'Enroll' ";

if (isset($_POST["search"]["value"]) && $_POST["search"]["value"] != '') {
	$s = $mydb->escape_value($_POST["search"]["value"]);
	$where .= " AND (s.IDNO LIKE '%".$s."%'
				 OR s.LNAME LIKE '%".$s."%'
				 OR s.FNAME LIKE '%".$s."%'
				 OR c.COURSE_CODE LIKE '%".$s."%'
				 OR c.COURSE_NAME LIKE '%".$s."%'
				 OR e.YEAR_LEVEL LIKE '%".$s."%'
				 OR sy.SCHOOL_YEAR LIKE '%".$s."%'
				 OR e.SEMESTER LIKE '%".$s."%'
				 OR e.STATUS LIKE '%".$s."%'
				 OR sub.SUBJECT_CODE LIKE '%".$s."%'
				 OR sub.SUBJECT_NAME LIKE '%".$s."%') ";
}

$orderCols = array(
	1 => 's.IDNO',
	2 => 's.LNAME',
	3 => 'c.COURSE_CODE',
	4 => 'e.YEAR_LEVEL',
	5 => 'sy.SCHOOL_YEAR',
	6 => 'e.SEMESTER',
	7 => 'e.STATUS'
);

$orderBy = " ORDER BY e.ENROLLMENT_ID DESC ";
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

$select = "SELECT e.ENROLLMENT_ID, s.IDNO, s.LNAME, s.FNAME, s.MNAME,
		c.COURSE_CODE, e.YEAR_LEVEL, sy.SCHOOL_YEAR, e.SEMESTER, e.STATUS,
		GROUP_CONCAT(DISTINCT sub.SUBJECT_CODE ORDER BY sub.SUBJECT_CODE SEPARATOR ', ') AS SUBJECT_CODES,
		COUNT(DISTINCT d.DETAIL_ID) AS SUBJ_COUNT ";

$mydb->setQuery($select.$base.$where." GROUP BY e.ENROLLMENT_ID ".$orderBy.$limit);
$rows = $mydb->loadResultList();

$mydb->setQuery("SELECT COUNT(DISTINCT e.ENROLLMENT_ID) AS CNT ".$base.$where);
$filteredRows = $mydb->loadResultList();
$filtered = (count($filteredRows) >= 1) ? intval($filteredRows[0]->CNT) : 0;

/* BUG FIX: dating binibilang lang ang mga ENROLLMENT_ID na may subject
   (`FROM tblenrollmentdetails`), kaya mali/kulang ang "recordsTotal"
   na ipinapakita sa DataTables paging info - dapat lahat ng enrollment
   record ang binibilang dito, base sa parehong table na ginagamit ng
   listahan mismo. */
$mydb->setQuery("SELECT COUNT(*) AS CNT FROM `tblenrollment` WHERE STATUS = 'Enroll'");
$totalRows = $mydb->loadResultList();
$total = (count($totalRows) >= 1) ? intval($totalRows[0]->CNT) : 0;

$data = array();
$i = isset($_POST['start']) ? intval($_POST['start']) + 1 : 1;

foreach ($rows as $r) {

	$rowName = htmlspecialchars(trim($r->LNAME.', '.$r->FNAME.' '.$r->MNAME), ENT_QUOTES);

	$actions = '<div class="text-nowrap">
		<button type="button" data-eid="'.$r->ENROLLMENT_ID.'" class="btn btn-outline-info btn-sm action-btn doViewProfile" title="View"><i class="fa fa-eye"></i></button>
		<button type="button" data-eid="'.$r->ENROLLMENT_ID.'" class="btn btn-outline-warning btn-sm action-btn doManageSubjects" title="Add Subject / Manage Subjects"><i class="fa fa-book"></i></button>
		<button type="button" data-eid="'.$r->ENROLLMENT_ID.'" class="btn btn-outline-dark btn-sm action-btn doRequirements" title="Requirements / Documents"><i class="fa fa-clipboard-check"></i></button>
		<button type="button" data-eid="'.$r->ENROLLMENT_ID.'" class="btn btn-outline-primary btn-sm action-btn doEditEnrollment" title="Edit"><i class="fa fa-edit"></i></button>
		<button type="button" data-eid="'.$r->ENROLLMENT_ID.'" data-name="'.$rowName.'" class="btn btn-outline-danger btn-sm action-btn doDropEnrollment" title="Drop"><i class="fa fa-user-times"></i></button>
		<button type="button" data-eid="'.$r->ENROLLMENT_ID.'" class="btn btn-outline-success btn-sm action-btn doRegisterAgain" title="Register Again (new semester / next year level)"><i class="fa fa-redo"></i></button>
		<a href="print.php?id='.$r->ENROLLMENT_ID.'" target="_blank"><button type="button" class="btn btn-outline-secondary btn-sm action-btn" title="Print Enrollment Form"><i class="fa fa-print"></i></button></a>
	</div>';

	$subjCount = intval($r->SUBJ_COUNT);
	$subjectsCell = ($subjCount > 0 && !empty($r->SUBJECT_CODES))
		? htmlspecialchars($r->SUBJECT_CODES).' <span class="badge badge-secondary">'.$subjCount.'</span>'
		: '<span class="text-muted font-italic">No subjects yet</span>';

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
