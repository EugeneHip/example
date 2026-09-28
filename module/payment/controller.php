<?php

require_once("../../include/initialize.php");
global $mydb;

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action == 'pay') {
	doPay();
} else if ($action == 'delete') {
	doDeletePayment();
} else {
	redirect('index.php');
}

function doPay() {

	global $mydb;

	$EID = isset($_POST['PAY_EID']) ? intval($_POST['PAY_EID']) : 0;
	$posted = isset($_POST['PAY_SUBJECTS']) && is_array($_POST['PAY_SUBJECTS']) ? $_POST['PAY_SUBJECTS'] : array();

	if ($EID <= 0) {
		message("No enrollment record selected.", "error");
		redirect('index.php');
		return;
	}

	$mydb->setQuery("SELECT e.S_ID, e.SECTION_ID, e.STATUS, s.LNAME, s.FNAME, s.MNAME 
		FROM `tblenrollment` e 
		JOIN `tblstudent` s ON s.S_ID = e.S_ID 
		WHERE e.ENROLLMENT_ID = '".$EID."' LIMIT 1");
	$rows = $mydb->loadResultList();

	if (count($rows) < 1) {
		message("That enrollment record no longer exists.", "error");
		redirect('index.php');
		return;
	}
	$rec = $rows[0];

	/* HIPANAO SOLUTIONS - Wala nang hiwalay na Sectioning na hakbang
	   bago makapagbayad - basta may subject na naka-Assign, puwede nang
	   magbayad kahit wala pang Section. (Ang Section ay opsyonal na
	   lang na field, naaayos pa rin sa Edit Enrollment modal.) */

	$studentName = trim($rec->LNAME.', '.$rec->FNAME.' '.$rec->MNAME);
	$receivedBy = isset($_SESSION['UID']) ? intval($_SESSION['UID']) : null;
	$datePaid = (isset($_POST['PAY_DATE']) && $_POST['PAY_DATE'] != '') ? $_POST['PAY_DATE'] : date('Y-m-d');
	$orNo = isset($_POST['PAY_OR_NO']) ? trim($_POST['PAY_OR_NO']) : '';
	if ($orNo == '') { $orNo = generateORNumber(); }

	/* HIPANAO SOLUTIONS - BUG FIX: dating tinatawag dito ang
	   saveEnrollmentSubjects($EID, $posted), kaya kung anuman ang
	   naka-check (o HINDI naka-check) sa checklist ng Payment form sa
	   oras na iyon ang siyang nagiging BAGONG buong listahan ng
	   subjects ng estudyante - kahit kumpleto na ang Section at
	   Subjects na na-set up sa Enrollment module bago pa dumating dito.
	   Kaya paulit-ulit na "nawawala"/"nare-reset" ang section at
	   subjects pagkatapos magbayad ng enrollment fee.

	   Ang Payment module ay dapat MAGBAYAD LANG - ang Section at
	   Subjects ay pinamamahalaan na ng Enrollment / Enrollment Details
	   modules. Kaya inalis na ang pag-save ng subjects dito; ang
	   listahan ng subjects na ginagamit sa pagkuwenta ng tuition
	   (tuitionBreakdownForEnrollment) ay direkta na lang sa DB kinukuha -
	   hindi na apektado ng mga checkbox dito sa Payment form. Ang mga
	   checkbox na ito ay para lang sa live na "amount due" preview. */

	$breakdown = tuitionBreakdownForEnrollment($EID);
	$paidBefore = paymentTotalsForEnrollment($EID);

	/* HIPANAO SOLUTIONS - Sukli (change). Kinukuha muna kung magkano
	   talaga ang makokolekta ngayon (Enrollment Fee kung babayaran +
	   Tuition amount) BAGO mag-insert, para malaman kung magkano dapat
	   ang minimum na Cash Received - kagaya rin ito ng ginagawa ng JS
	   sa index.php, pero dito ulit ino-validate server-side para hindi
	   ito ma-bypass. */
	$payEnrollmentFeeRequested = isset($_POST['PAY_ENROLLMENT_FEE']) && $_POST['PAY_ENROLLMENT_FEE'] == '1';
	$enrollmentFeeAlreadyPaid  = $paidBefore['Enrollment Fee'] >= ENROLLMENT_FEE;
	$tuitionAmountRequested    = isset($_POST['PAY_TUITION_AMOUNT']) ? floatval($_POST['PAY_TUITION_AMOUNT']) : 0;
	$tuitionBalance            = max(0, $breakdown['total_amount'] - $paidBefore['Tuition Fee']);
	$tuitionAmountToCollect    = min(max(0, $tuitionAmountRequested), $tuitionBalance);

	$amountDueNow = 0;
	if ($payEnrollmentFeeRequested && !$enrollmentFeeAlreadyPaid) { $amountDueNow += floatval(ENROLLMENT_FEE); }
	$amountDueNow += $tuitionAmountToCollect;

	$cashReceived = null;
	$changeDue    = null;
	if ($amountDueNow > 0) {
		$cashReceived = isset($_POST['PAY_CASH_RECEIVED']) && $_POST['PAY_CASH_RECEIVED'] !== '' ? floatval($_POST['PAY_CASH_RECEIVED']) : null;

		if ($cashReceived === null) {
			message("Please enter the cash received.", "error");
			redirect('index.php?eid='.$EID);
			return;
		}
		if ($cashReceived < $amountDueNow) {
			message("Insufficient cash received. Amount Due Now is ₱".number_format($amountDueNow, 2).", but only ₱".number_format($cashReceived, 2)." was entered.", "error");
			redirect('index.php?eid='.$EID);
			return;
		}
		$changeDue = $cashReceived - $amountDueNow;
	}

	$cashSql   = ($cashReceived !== null) ? "'".$cashReceived."'" : "NULL";
	$changeSql = ($changeDue !== null) ? "'".$changeDue."'" : "NULL";

	$collected = array();

	$payEnrollmentFee = $payEnrollmentFeeRequested;
	if ($payEnrollmentFee) {
		if ($enrollmentFeeAlreadyPaid) {
			message("The enrollment fee for this student was already paid.", "error");
			redirect('index.php');
			return;
		}
		$mydb->InsertThis("INSERT INTO `tblpayments` 
			(ENROLLMENT_ID, S_ID, STUDENT_NAME, OR_NO, PAYMENT_TYPE, AMOUNT, CASH_RECEIVED, CHANGE_DUE, DATE_PAID, RECEIVED_BY, REMARKS) 
			VALUES 
			('".$EID."', '".intval($rec->S_ID)."', '".$mydb->escape_value($studentName)."', 
			 '".$mydb->escape_value($orNo)."', 'Enrollment Fee', '".floatval(ENROLLMENT_FEE)."', ".$cashSql.", ".$changeSql.", 
			 '".$mydb->escape_value($datePaid)."', ".($receivedBy ? "'".$receivedBy."'" : "NULL").", 'Enrollment fee')");
		$collected[] = 'Enrollment Fee ₱'.number_format(ENROLLMENT_FEE, 2);
	}

	$tuitionAmount = $tuitionAmountToCollect;
	if ($tuitionAmount > 0) {
		$mydb->InsertThis("INSERT INTO `tblpayments` 
			(ENROLLMENT_ID, S_ID, STUDENT_NAME, OR_NO, PAYMENT_TYPE, AMOUNT, CASH_RECEIVED, CHANGE_DUE, DATE_PAID, RECEIVED_BY, REMARKS) 
			VALUES 
			('".$EID."', '".intval($rec->S_ID)."', '".$mydb->escape_value($studentName)."', 
			 '".$mydb->escape_value($orNo)."', 'Tuition Fee', '".$tuitionAmount."', ".$cashSql.", ".$changeSql.", 
			 '".$mydb->escape_value($datePaid)."', ".($receivedBy ? "'".$receivedBy."'" : "NULL").", 'Tuition payment')");
		$collected[] = 'Tuition Fee ₱'.number_format($tuitionAmount, 2);
	}

	$nowPaid = maybeMarkPaid($EID);

	if (count($collected) == 0) {
		message("Subjects saved, but no payment amount was collected.", "info");
		redirect('index.php');
		return;
	}

	$msg = "Payment recorded (".implode(' + ', $collected)."), OR# ".$orNo.".";
	if ($changeDue !== null && $changeDue > 0) { $msg .= " Change: ₱".number_format($changeDue, 2)."."; }
	if ($nowPaid) { $msg .= " The student is now Paid. Go to Enrollment and click Enroll to finish enrolling this student and print the Enrollment Form."; }
	message($msg, "success");

	/* Dalhin agad sa Payment page, saka doon awtomatikong bubukas (bagong
	   tab) ang printable na resibo para dito mismong OR# - tingnan ang
	   receipt.php at ang script sa index.php na tumitingin sa ?receipt=. */
	redirect('index.php?receipt='.urlencode($orNo));
}

function doDeletePayment() {
	global $mydb;
	$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

	if ($id > 0) {
		$mydb->InsertThis("DELETE FROM `tblpayments` WHERE PAYMENT_ID = '".$id."'");
		message("Payment record deleted.", "success");
	}
	redirect('index.php');
}
