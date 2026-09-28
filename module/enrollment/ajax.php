<?php

require_once("../../include/initialize.php");
global $mydb;

$act = isset($_POST['act']) ? $_POST['act'] : '';

if ($act === 'row') {

	$id = intval($_POST['ENROLLMENT_ID']);
	$output = array();

	$mydb->setQuery("SELECT e.*, 
			s.IDNO, s.LNAME, s.FNAME, s.MNAME, s.COMPANYIDNO,
			c.COURSE_CODE, c.COURSE_NAME, 
			sy.SCHOOL_YEAR 
		FROM `tblenrollment` e 
		JOIN `tblstudent`    s  ON s.S_ID       = e.S_ID 
		JOIN `tblcourses`    c  ON c.COURSE_ID  = e.COURSE_ID 
		JOIN `tblschoolyear` sy ON sy.SY_ID     = e.SY_ID 
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

if ($act === 'sections') {

	$course = intval($_POST['COURSE_ID']);
	$sy     = intval($_POST['SY_ID']);
	$yearLevel = isset($_POST['YEAR_LEVEL']) ? trim($_POST['YEAR_LEVEL']) : '';
	$rows   = array();

	if ($course > 0 && $sy > 0) {
		$sql = "SELECT SECTION_ID, SECTION_NAME, YEAR_LEVEL 
			FROM `tblsections` 
			WHERE COURSE_ID = '".$course."' AND SY_ID = '".$sy."' ";
		/* Kapag may YEAR_LEVEL na ibinigay (galing sa enrollment record),
		   doon lang muna i-filter ang mga section - para hindi magkahalo
		   ang section ng ibang year level. */
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

if ($act === 'document_data') {

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

$base = "FROM `tblenrollment` e 
	JOIN `tblstudent`     s   ON s.S_ID      = e.S_ID 
	JOIN `tblcourses`     c   ON c.COURSE_ID = e.COURSE_ID 
	JOIN `tblschoolyear`  sy  ON sy.SY_ID    = e.SY_ID 
	LEFT JOIN `tblsections` sec ON sec.SECTION_ID = e.SECTION_ID ";

$where = " WHERE 1=1 ";

/* HIPANAO SOLUTIONS - Once a student's enrollment fee is fully paid
   (STATUS = 'Enroll'), the record is considered done with this
   queue - the student's name should move on and no longer clutter the
   working Enrollment list. It still lives in `tblenrollment` and shows
   up under Enrollment Details / Student > View / Payment (for any
   remaining tuition), and staff can still reach it here explicitly via
   the "Enroll" filter chip if they ever need to fix or drop it - it
   is only hidden from the default "All" view.

   BUG FIX: "Completed" (naunang term na naipalit dahil nag-Register
   Again + nag-Enroll ulit ang estudyante - tingnan ang
   markAsEnrolled() sa include/functions.php) at "Dropped" ay dapat
   ding hindi na lumabas dito sa "All" - nasa module/hitoryenrollment
   na sila makikita, hindi na dapat may Edit/Delete pa dito. */
$filter = isset($_POST['status_filter']) ? trim($_POST['status_filter']) : '';
if ($filter !== '') {
	$where .= " AND e.STATUS = '".$mydb->escape_value($filter)."' ";
} else {
	$where .= " AND e.STATUS NOT IN ('Enroll', 'Completed', 'Dropped') ";
}

if (isset($_POST["search"]["value"]) && $_POST["search"]["value"] != '') {
	$s = $mydb->escape_value($_POST["search"]["value"]);
	$where .= " AND (s.LNAME LIKE '%".$s."%' 
				 OR s.FNAME LIKE '%".$s."%' 
				 OR s.IDNO  LIKE '%".$s."%' 
				 OR c.COURSE_CODE LIKE '%".$s."%' 
				 OR e.STATUS LIKE '%".$s."%') ";
}

$orderCols = array(
	0 => 'e.ENROLLMENT_ID',
	1 => 's.IDNO',
	2 => 's.LNAME',
	3 => 'c.COURSE_CODE',
	4 => 'sy.SCHOOL_YEAR',
	5 => 'e.SEMESTER',
	6 => 'e.YEAR_LEVEL',
	7 => 'e.CATEGORY',
	8 => 'sec.SECTION_NAME',
	9 => 'e.DATE_RESERVED',
	10 => 'e.DATE_ENROLLED',
	11 => 'e.STATUS'
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

$select = "SELECT e.ENROLLMENT_ID, e.YEAR_LEVEL, e.SEMESTER, e.CATEGORY, e.CURRICULUM_YR, 
		e.DATE_RESERVED, e.DATE_ENROLLED, e.STATUS, 
		s.IDNO, s.LNAME, s.FNAME, s.MNAME, 
		c.COURSE_CODE, sy.SCHOOL_YEAR, sec.SECTION_NAME, 
		(SELECT COUNT(*) FROM `tblenrollmentdetails` ed WHERE ed.ENROLLMENT_ID = e.ENROLLMENT_ID) AS SUBJ_COUNT ";

$mydb->setQuery($select.$base.$where.$orderBy.$limit);
$rows = $mydb->loadResultList();

$mydb->setQuery("SELECT e.ENROLLMENT_ID ".$base.$where);
$filtered = $mydb->num_rows();

$mydb->setQuery("SELECT ENROLLMENT_ID FROM `tblenrollment`");
$total = $mydb->num_rows();

$data = array();
$i = isset($_POST['start']) ? intval($_POST['start']) + 1 : 1;

foreach ($rows as $r) {

	$statusClass = 'secondary';
	if ($r->STATUS == 'Enroll')   { $statusClass = 'success'; }
	if ($r->STATUS == 'Register') { $statusClass = 'warning'; }
	if ($r->STATUS == 'Assign')   { $statusClass = 'primary'; }
	if ($r->STATUS == 'Document') { $statusClass = 'dark'; }
	if ($r->STATUS == 'Paid')     { $statusClass = 'info'; }
	if ($r->STATUS == 'Dropped')  { $statusClass = 'danger'; }
	if ($r->STATUS == 'Completed'){ $statusClass = 'secondary'; }

	$section = ($r->SECTION_NAME === null || $r->SECTION_NAME == '')
		? '<span class="text-muted">Not sectioned</span>'
		: htmlspecialchars($r->SECTION_NAME);

	$dateEnrolled = ($r->DATE_ENROLLED === null || $r->DATE_ENROLLED == '')
		? '<span class="text-muted">-</span>'
		: $r->DATE_ENROLLED;

	$dateReserved = ($r->DATE_RESERVED === null || $r->DATE_RESERVED == '')
		? '<span class="text-muted">-</span>'
		: $r->DATE_RESERVED;

	$subjCount = intval($r->SUBJ_COUNT);

	/* HIPANAO SOLUTIONS - Subjects (Assign) ay para lang sa mga hindi pa
	   nagbabayad (Register/Assign). Kapag "Paid" o "Enroll" na, tapos na
	   ang hakbang na ito para sa Enrollment module - ang pag-aayos ng
	   subjects (add/drop) ng mga naka-Enroll na ay ginagawa na sa
	   Enrollment Details module sa halip. */
	$subjectsBtnClass = ($subjCount == 0 && $r->STATUS == 'Register') ? 'btn-primary' : 'btn-outline-secondary';
	$subjectsBtn = ($r->STATUS == 'Register' || $r->STATUS == 'Assign')
		? '<button type="button" EID="'.$r->ENROLLMENT_ID.'" class="btn '.$subjectsBtnClass.' btn-xs doSubjects" title="Subjects">
			<span class="fa fa-book fw-fa"></span> Subjects
		   </button>'
		: '';

	/* HIPANAO SOLUTIONS - "Document" na hakbang (Stage 3, pagkatapos ng
	   Assign Subjects). Lumalabas lang kapag may naka-assign nang
	   subject (subjCount > 0) at "Assign" o "Document" pa ang STATUS -
	   dito tine-tsek ng REGISTRAR STAFF (hindi ng applicant) kung anong
	   mga dokumento na ang naisumite. Pag-save nito habang "Assign" pa,
	   awtomatiko itong lilipat sa "Document" (tingnan ang doDocument()
	   sa controller.php). */
	$documentBtnClass = ($r->STATUS == 'Assign') ? 'btn-dark' : 'btn-outline-dark';
	$documentBtn = ($subjCount > 0 && ($r->STATUS == 'Assign' || $r->STATUS == 'Document'))
		? '<button type="button" EID="'.$r->ENROLLMENT_ID.'" class="btn '.$documentBtnClass.' btn-xs doDocument" title="Requirements / Documents">
			<span class="fa fa-clipboard-check fw-fa"></span> Document
		   </button>'
		: '';

	/* Sectioning is no longer a required step - kaya kahit walang
	   Section pa, puwede nang magbayad basta may subject na naka-
	   assign (STATUS = 'Assign' pataas). Ang Section field mismo ay
	   nasa Edit Enrollment modal pa rin kung kailangang i-set.
	   Ipinapakita pa rin ang Payment button kahit "Paid" na (para sa
	   sunod na tuition installment), pero hindi na kapag "Enroll" na.

	   HIPANAO SOLUTIONS - BUG FIX: dating "Assign" pataas na agad
	   pwedeng magbayad - ngayon kailangan munang dumaan sa "Document"
	   (naisumite na ang mga requirement) bago lumabas ang Payment
	   button, kaayon ng bagong 5-hakbang na daloy: Register -> Assign
	   -> Document -> Paid -> Enroll. */
	$payBtnClass = ($r->STATUS == 'Document') ? 'btn-info' : 'btn-outline-info';
	$payBtn = ($subjCount > 0 && ($r->STATUS == 'Document' || $r->STATUS == 'Paid'))
		? '<a href="'.WEB_ROOT.'module/payment/index.php?eid='.$r->ENROLLMENT_ID.'">
			<button type="button" class="btn '.$payBtnClass.' btn-xs" title="Payment">
				<span class="fa fa-money-check-alt fw-fa"></span> Payment
			</button>
		   </a>'
		: '';

	/* HIPANAO SOLUTIONS - "Enroll" action. Lumalabas lang kapag "Paid"
	   na ang STATUS (bayad na nang buo ang Enrollment Fee). Manual na
	   hakbang ito - saka pa lang opisyal na magiging "Enroll" ang
	   record (tingnan ang markAsEnrolled() sa include/functions.php).
	   Dito na rin dinadala ang staff diretso sa Print Enrollment Form
	   (module/enrollmentdetails/print.php) kaagad pagkatapos i-Enroll -
	   tingnan ang doMarkEnroll() sa controller.php. */
	/* HIPANAO SOLUTIONS - BUG FIX: dating plain browser confirm() ang
	   ginagamit dito ("localhost says..." na pop-up) - iba ang itsura
	   kumpara sa SweetAlert2 na ginagamit na ng ibang module (Student,
	   Course, Subject, atbp). Kaya lang, ginawang button na lang ito
	   (may EID na attribute, kaparehong porma ng ibang action button
	   dito rin) sa halip na <a> na may inline onclick - ang aktwal na
	   pag-navigate papunta sa controller.php ay ginagawa na lang sa JS
	   (doEnroll handler sa index.php) pagkatapos kumpirmahin sa Swal. */
	$enrollBtn = ($r->STATUS == 'Paid')
		? '<button type="button" EID="'.$r->ENROLLMENT_ID.'" class="btn btn-success btn-xs doEnroll" title="Enroll">
			<span class="fa fa-graduation-cap fw-fa"></span> Enroll
		   </button>'
		: '';

	$actions = '
		'.$subjectsBtn.'
		'.$documentBtn.'
		'.$payBtn.'
		'.$enrollBtn.'
		<button type="button" EID="'.$r->ENROLLMENT_ID.'" class="btn btn-warning btn-xs editEnrollment" title="Edit">
			<span class="fa fa-edit fw-fa"></span>
		</button>
		<button type="button" EID="'.$r->ENROLLMENT_ID.'" class="btn btn-danger btn-xs deleteEnrollment" title="Delete">
			<span class="fa fa-trash fw-fa"></span>
		</button>';

	$data[] = array(
		$i,
		htmlspecialchars($r->IDNO),
		htmlspecialchars(trim($r->LNAME.', '.$r->FNAME.' '.$r->MNAME)),
		htmlspecialchars($r->COURSE_CODE),
		htmlspecialchars($r->SCHOOL_YEAR),
		htmlspecialchars($r->SEMESTER),
		htmlspecialchars($r->YEAR_LEVEL),
		htmlspecialchars($r->CATEGORY),
		$section,
		$dateReserved,
		$dateEnrolled,
		'<span class="badge badge-'.$statusClass.'">'.htmlspecialchars($r->STATUS).'</span>',
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
