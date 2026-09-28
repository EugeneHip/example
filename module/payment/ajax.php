<?php

require_once("../../include/initialize.php");
global $mydb;

$act = isset($_POST['act']) ? $_POST['act'] : '';

if ($act === 'row') {

	$id = intval($_POST['ENROLLMENT_ID']);
	$output = array('subjects' => array(), 'checked' => array());

	$mydb->setQuery("SELECT e.*, 
			s.IDNO, s.LNAME, s.FNAME, s.MNAME, s.COMPANYIDNO,
			c.COURSE_CODE, c.COURSE_NAME, 
			sy.SCHOOL_YEAR, sec.SECTION_NAME 
		FROM `tblenrollment` e 
		JOIN `tblstudent`    s   ON s.S_ID      = e.S_ID 
		JOIN `tblcourses`    c   ON c.COURSE_ID = e.COURSE_ID 
		JOIN `tblschoolyear` sy  ON sy.SY_ID    = e.SY_ID 
		LEFT JOIN `tblsections` sec ON sec.SECTION_ID = e.SECTION_ID 
		WHERE e.ENROLLMENT_ID = '".$id."' LIMIT 1");
	$rows = $mydb->loadResultList();

	if (count($rows) >= 1) {
		$r = $rows[0];

		$output['ENROLLMENT_ID'] = $r->ENROLLMENT_ID;
		$output['S_ID']          = $r->S_ID;
		$output['IDNO']          = $r->IDNO;
		$output['FULLNAME']      = trim($r->LNAME.', '.$r->FNAME.' '.$r->MNAME);
		$output['PICTURE_URL']   = (isset($r->COMPANYIDNO) && $r->COMPANYIDNO != '')
			? WEB_ROOT.'module/student/image/'.$r->COMPANYIDNO
			: '';
		$output['COURSE_TEXT']   = $r->COURSE_CODE.' - '.$r->COURSE_NAME;
		$output['TERM_TEXT']     = $r->SCHOOL_YEAR.' / '.$r->SEMESTER;
		$output['SECTION_TEXT']  = ($r->SECTION_NAME === null || $r->SECTION_NAME == '') ? 'Not sectioned' : $r->SECTION_NAME;
		$output['STATUS']        = $r->STATUS;
		$output['HAS_SECTION']   = !empty($r->SECTION_ID);

		/* Major subjects ng course na ito + lahat ng Minor subjects
		   (COURSE_ID IS NULL sa mga iyon - bukas sa lahat ng course).
		   Ipinapakita lang ang mga subject na para sa YEAR_LEVEL at
		   SEMESTER ng enrollment na ito (o walang partikular na
		   year level/semester), para hindi magkahalo-halo ang subjects
		   ng ibang year o ibang semester. */
		$mydb->setQuery("SELECT SUBJECT_ID, SUBJECT_CODE, SUBJECT_NAME, UNITS, YEAR_LEVEL, SEMESTER, SUBJECT_TYPE 
			FROM `tblsubjects` 
			WHERE (COURSE_ID = '".intval($r->COURSE_ID)."' OR COURSE_ID IS NULL) 
			AND (YEAR_LEVEL = '".$mydb->escape_value($r->YEAR_LEVEL)."' OR YEAR_LEVEL IS NULL OR YEAR_LEVEL = '') 
			AND (SEMESTER = '".$mydb->escape_value($r->SEMESTER)."' OR SEMESTER IS NULL OR SEMESTER = '') 
			ORDER BY SUBJECT_TYPE ASC, YEAR_LEVEL ASC, SEMESTER ASC, SUBJECT_CODE ASC");
		foreach ($mydb->loadResultList() as $sub) {
			$type = ($sub->SUBJECT_TYPE == 'Minor') ? 'Minor' : 'Major';
			$rate = ($type == 'Minor') ? MINOR_UNIT_RATE : MAJOR_UNIT_RATE;
			$output['subjects'][] = array(
				'SUBJECT_ID'   => $sub->SUBJECT_ID,
				'SUBJECT_CODE' => $sub->SUBJECT_CODE,
				'SUBJECT_NAME' => $sub->SUBJECT_NAME,
				'UNITS'        => $sub->UNITS,
				'YEAR_LEVEL'   => $sub->YEAR_LEVEL,
				'SEMESTER'     => $sub->SEMESTER,
				'SUBJECT_TYPE' => $type,
				'RATE'         => $rate,
				'AMOUNT'       => $sub->UNITS * $rate
			);
		}

		$mydb->setQuery("SELECT SUBJECT_ID FROM `tblenrollmentdetails` WHERE ENROLLMENT_ID = '".$id."'");
		foreach ($mydb->loadResultList() as $d) {
			$output['checked'][] = intval($d->SUBJECT_ID);
		}

		$breakdown = tuitionBreakdownForEnrollment($id);
		$output['tuition'] = array(
			'major_units'  => $breakdown['major_units'],
			'minor_units'  => $breakdown['minor_units'],
			'major_amount' => $breakdown['major_amount'],
			'minor_amount' => $breakdown['minor_amount'],
			'total_amount' => $breakdown['total_amount']
		);

		$paid = paymentTotalsForEnrollment($id);
		$output['enrollment_fee_amount'] = ENROLLMENT_FEE;
		$output['enrollment_fee_paid']   = $paid['Enrollment Fee'];
		$output['tuition_paid']          = $paid['Tuition Fee'];
		$output['tuition_balance']       = max(0, $breakdown['total_amount'] - $paid['Tuition Fee']);
		$output['or_no_suggestion']      = generateORNumber();
	}

	echo json_encode($output);
	exit;
}

if ($act === 'queue_list') {

	/* IMPORTANT: dapat direktang correlated subquery ito - HINDI puwede
	   i-wrap sa isang derived table (a "FROM (SELECT ...) alias") dahil
	   hindi pinapayagan ng MySQL na tumingin ang isang derived table sa
	   labas ng sarili niyang subquery (sa `e.ENROLLMENT_ID` dito). Iyon
	   ang dating sanhi ng "Invalid JSON response" error sa Payment page -
	   nag-eerror ang SQL, kaya plain text error ang nailalabas sa halip
	   na JSON. */
	/* HIPANAO SOLUTIONS - Every student who has reached Assign, Paid, or
	   Enroll stays listed here permanently, whether or not they have
	   paid in full - the cashier can still open "Collect Payment" any
	   time (e.g. a later tuition installment, or just to review/print
	   a past receipt). Names are no longer removed from this queue
	   once fully paid. Section is no longer required to reach this
	   queue - only having at least one subject assigned. */
	$base = "FROM `tblenrollment` e 
		JOIN `tblstudent`    s   ON s.S_ID      = e.S_ID 
		JOIN `tblcourses`    c   ON c.COURSE_ID = e.COURSE_ID 
		LEFT JOIN `tblsections` sec ON sec.SECTION_ID = e.SECTION_ID 
		WHERE e.STATUS IN ('Assign', 'Paid', 'Enroll') ";

	if (isset($_POST["search"]["value"]) && $_POST["search"]["value"] != '') {
		$s = $mydb->escape_value($_POST["search"]["value"]);
		$base .= " AND (s.LNAME LIKE '%".$s."%' OR s.FNAME LIKE '%".$s."%' OR s.IDNO LIKE '%".$s."%' OR c.COURSE_CODE LIKE '%".$s."%') ";
	}

	$orderBy = " ORDER BY e.STATUS DESC, e.ENROLLMENT_ID DESC ";
	$limit = "";
	if (isset($_POST['length']) && $_POST['length'] != -1) {
		$limit = " LIMIT ".intval($_POST['start']).", ".intval($_POST['length'])." ";
	}

	$mydb->setQuery("SELECT e.ENROLLMENT_ID, e.STATUS, s.IDNO, s.LNAME, s.FNAME, s.MNAME, 
			c.COURSE_CODE, sec.SECTION_NAME ".$base.$orderBy.$limit);
	$rows = $mydb->loadResultList();

	$mydb->setQuery("SELECT e.ENROLLMENT_ID ".$base);
	$filtered = $mydb->num_rows();

	$mydb->setQuery("SELECT e.ENROLLMENT_ID FROM `tblenrollment` e WHERE e.STATUS IN ('Assign', 'Paid', 'Enroll')");
	$total = $mydb->num_rows();

	$data = array();
	$i = isset($_POST['start']) ? intval($_POST['start']) + 1 : 1;

	foreach ($rows as $r) {
		$breakdown = tuitionBreakdownForEnrollment($r->ENROLLMENT_ID);
		$paid = paymentTotalsForEnrollment($r->ENROLLMENT_ID);

		$statusClass = 'primary';
		if ($r->STATUS == 'Paid')   { $statusClass = 'info'; }
		if ($r->STATUS == 'Enroll') { $statusClass = 'success'; }
		$feeBadge = ($paid['Enrollment Fee'] >= ENROLLMENT_FEE)
			? '<span class="badge badge-status-active">Paid</span>'
			: '<span class="badge badge-status-forpayment">Unpaid</span>';

		$subjCount = count($breakdown['subjects']);
		$subjText = ($subjCount > 0)
			? $subjCount.' subject(s)'
			: '<span class="text-muted">No subjects yet</span>';

		/* Tuition Status - clear paid / partial / unpaid indicator per
		   student, separate from the Enrollment Fee badge, so cashiers
		   can see at a glance whether the tuition itself is settled. */
		if ($breakdown['total_amount'] <= 0) {
			$tuitionBadge = '<span class="text-muted">No tuition due</span>';
		} else if ($paid['Tuition Fee'] >= $breakdown['total_amount']) {
			$tuitionBadge = '<span class="badge badge-status-active">Paid</span>';
		} else if ($paid['Tuition Fee'] > 0) {
			$tuitionBadge = '<span class="badge badge-warning">Partial (&#8369;'.number_format($paid['Tuition Fee'], 2).' of &#8369;'.number_format($breakdown['total_amount'], 2).')</span>';
		} else {
			$tuitionBadge = '<span class="badge badge-status-forpayment">Unpaid</span>';
		}

		$actions = '<button type="button" EID="'.$r->ENROLLMENT_ID.'" class="btn btn-info btn-xs doCollectPayment" title="Collect Payment">
				<span class="fa fa-money-check-alt fw-fa"></span> Collect Payment
			</button>';

		$data[] = array(
			$i,
			htmlspecialchars($r->IDNO),
			htmlspecialchars(trim($r->LNAME.', '.$r->FNAME.' '.$r->MNAME)),
			htmlspecialchars($r->COURSE_CODE),
			htmlspecialchars($r->SECTION_NAME ?: '-'),
			'<span class="badge badge-'.$statusClass.'">'.htmlspecialchars($r->STATUS).'</span>',
			$subjText,
			'&#8369;'.number_format($breakdown['total_amount'], 2),
			$tuitionBadge,
			$feeBadge,
			$actions
		);
		$i++;
	}

	echo json_encode(array('data' => $data, 'recordsTotal' => $total, 'recordsFiltered' => $filtered));
	exit;
}

if ($act === 'history_list') {

	$base = "FROM `tblpayments` p 
		LEFT JOIN `tblusers` u ON u.UID = p.RECEIVED_BY 
		WHERE 1=1 ";

	if (isset($_POST["search"]["value"]) && $_POST["search"]["value"] != '') {
		$s = $mydb->escape_value($_POST["search"]["value"]);
		$base .= " AND (p.STUDENT_NAME LIKE '%".$s."%' OR p.OR_NO LIKE '%".$s."%' OR p.PAYMENT_TYPE LIKE '%".$s."%') ";
	}

	$orderBy = " ORDER BY p.PAYMENT_ID DESC ";
	$limit = "";
	if (isset($_POST['length']) && $_POST['length'] != -1) {
		$limit = " LIMIT ".intval($_POST['start']).", ".intval($_POST['length'])." ";
	}

	$mydb->setQuery("SELECT p.*, u.DISPLAYNAME ".$base.$orderBy.$limit);
	$rows = $mydb->loadResultList();

	$mydb->setQuery("SELECT PAYMENT_ID ".$base);
	$filtered = $mydb->num_rows();

	$mydb->setQuery("SELECT PAYMENT_ID FROM `tblpayments`");
	$total = $mydb->num_rows();

	$data = array();
	$i = isset($_POST['start']) ? intval($_POST['start']) + 1 : 1;

	foreach ($rows as $r) {
		$typeBadge = ($r->PAYMENT_TYPE == 'Tuition Fee')
			? '<span class="badge badge-subject-major">Tuition Fee</span>'
			: '<span class="badge badge-status-active">Enrollment Fee</span>';

		$printBtn = ($r->OR_NO != '')
			? '<a href="'.WEB_ROOT.'module/payment/receipt.php?or='.urlencode($r->OR_NO).'" target="_blank" class="btn btn-outline-secondary btn-xs" title="Print Receipt"><span class="fa fa-print"></span> Print</a>'
			: '-';

		$data[] = array(
			$i,
			htmlspecialchars($r->OR_NO ?: '-'),
			htmlspecialchars($r->STUDENT_NAME),
			$typeBadge,
			'&#8369;'.number_format($r->AMOUNT, 2),
			htmlspecialchars($r->DATE_PAID),
			htmlspecialchars($r->DISPLAYNAME ?: '-'),
			$printBtn
		);
		$i++;
	}

	echo json_encode(array('data' => $data, 'recordsTotal' => $total, 'recordsFiltered' => $filtered));
	exit;
}
