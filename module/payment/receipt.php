<?php
require_once("../../include/initialize.php");
global $mydb;

if (!isset($_SESSION['UID'])) {
	redirect(WEB_ROOT."login.php");
	exit;
}

$orNo = isset($_GET['or']) ? trim($_GET['or']) : '';

$payments = array();
$student  = null;
$course   = null;
$section  = null;
$receivedByName = '-';
$datePaid = '';

if ($orNo != '') {
	$mydb->setQuery("SELECT p.*, e.S_ID AS E_S_ID, e.COURSE_ID, e.SECTION_ID, e.SY_ID, e.SEMESTER 
		FROM `tblpayments` p 
		LEFT JOIN `tblenrollment` e ON e.ENROLLMENT_ID = p.ENROLLMENT_ID 
		WHERE p.OR_NO = '".$mydb->escape_value($orNo)."' 
		ORDER BY p.PAYMENT_ID ASC");
	$payments = $mydb->loadResultList();

	if (count($payments) >= 1) {
		$first = $payments[0];
		$datePaid = $first->DATE_PAID;

		$mydb->setQuery("SELECT * FROM `tblstudent` WHERE S_ID = '".intval($first->S_ID)."' LIMIT 1");
		$srows = $mydb->loadResultList();
		$student = (count($srows) >= 1) ? $srows[0] : null;

		if (!empty($first->COURSE_ID)) {
			$mydb->setQuery("SELECT * FROM `tblcourses` WHERE COURSE_ID = '".intval($first->COURSE_ID)."' LIMIT 1");
			$crows = $mydb->loadResultList();
			$course = (count($crows) >= 1) ? $crows[0] : null;
		}

		if (!empty($first->SECTION_ID)) {
			$mydb->setQuery("SELECT * FROM `tblsections` WHERE SECTION_ID = '".intval($first->SECTION_ID)."' LIMIT 1");
			$secrows = $mydb->loadResultList();
			$section = (count($secrows) >= 1) ? $secrows[0] : null;
		}

		if (!empty($first->RECEIVED_BY)) {
			$mydb->setQuery("SELECT DISPLAYNAME FROM `tblusers` WHERE UID = '".intval($first->RECEIVED_BY)."' LIMIT 1");
			$urows = $mydb->loadResultList();
			if (count($urows) >= 1) { $receivedByName = $urows[0]->DISPLAYNAME; }
		}
	}
}

$total = 0;
foreach ($payments as $p) { $total += floatval($p->AMOUNT); }

/* Tuition total and remaining balance for this enrollment, so the
   receipt shows the student/cashier how much of the tuition is left
   to pay, not just the amount collected in this transaction. */
$tuitionTotal   = 0;
$tuitionBalance = 0;
if (count($payments) >= 1 && !empty($payments[0]->ENROLLMENT_ID)) {
	$tuitionBreakdown = tuitionBreakdownForEnrollment($payments[0]->ENROLLMENT_ID);
	$paidTotals       = paymentTotalsForEnrollment($payments[0]->ENROLLMENT_ID);
	$tuitionTotal     = $tuitionBreakdown['total_amount'];
	$tuitionBalance   = max(0, $tuitionTotal - $paidTotals['Tuition Fee']);
}

/* Iisang Cash Received / Change lang bawat OR# (parehong value sa bawat
   row ng OR# na iyon - tingnan ang controller.php::doPay()), kaya sa
   unang row na lang kukunin. */
$cashReceived = null;
$changeDue    = null;
if (count($payments) >= 1 && $payments[0]->CASH_RECEIVED !== null) {
	$cashReceived = floatval($payments[0]->CASH_RECEIVED);
	$changeDue    = floatval($payments[0]->CHANGE_DUE);
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Official Receipt <?php echo htmlspecialchars($orNo); ?></title>
<style>
	/* HIPANAO SOLUTIONS - Black & white lang ang disenyo dito (para
	   makatipid sa colored ink pag-print). */
	body { font-family: Arial, Helvetica, sans-serif; color: #000; margin: 0; padding: 0; background: #fff; }
	.sheet { max-width: 620px; margin: 20px auto; background: #fff; padding: 30px 35px; }
	.letterhead { text-align: center; border-bottom: 2px solid #000; padding-bottom: 12px; margin-bottom: 18px; }
	.letterhead img { height: 55px; margin-bottom: 4px; filter: grayscale(100%); }
	.letterhead h2 { margin: 4px 0 0; color: #000; }
	.letterhead small { color: #333; }
	.receipt-title { text-align: center; margin: 10px 0 20px; }
	.receipt-title h3 { margin: 0; letter-spacing: 1px; }
	.receipt-title .orno { font-size: 1.1em; font-weight: bold; color: #000; }
	table.info td { padding: 3px 6px; font-size: 0.95em; vertical-align: top; }
	table.info td.label { color: #333; width: 130px; }
	table.items { width: 100%; border-collapse: collapse; margin-top: 16px; }
	table.items th, table.items td { border: 1px solid #000; padding: 6px 8px; font-size: 0.95em; }
	table.items th { background: #e8e8e8; text-align: left; }
	table.items td.amt, table.items th.amt { text-align: right; }
	table.items tfoot td { font-weight: bold; background: #f5f5f5; }
	.signatures { margin-top: 45px; display: flex; justify-content: space-between; }
	.signatures div { width: 45%; text-align: center; }
	.signatures .line { border-top: 1px solid #000; margin-top: 40px; padding-top: 4px; font-size: 0.85em; color: #000; }
	.no-print { text-align: center; margin: 18px 0; }
	.no-print button { padding: 8px 22px; margin: 0 5px; border: 1px solid #000; border-radius: 4px; cursor: pointer; font-size: 0.95em; }
	.no-print .btn-print { background: #000; color: #fff; }
	.no-print .btn-close { background: #fff; color: #000; }
	.not-found { text-align: center; color: #000; padding: 40px 0; }
	@media print {
		body { background: #fff; }
		.sheet { margin: 0; max-width: 100%; }
		.no-print { display: none; }
	}
</style>
</head>
<body>

<?php if ($orNo == '' || count($payments) < 1) { ?>
	<div class="sheet">
		<p class="not-found">Walang nahanap na resibo para sa OR# "<?php echo htmlspecialchars($orNo); ?>".</p>
		<div class="no-print"><button class="btn-close" onclick="window.close();">Close</button></div>
	</div>
<?php } else { ?>

	<div class="sheet">
		<div class="letterhead">
			<img src="<?php echo WEB_ROOT.SCHOOL_LOGO; ?>" onerror="this.style.display='none';">
			<h2><?php echo htmlspecialchars(SCHOOL_NAME); ?></h2>
			<small>Official Receipt of Payment</small>
		</div>

		<div class="receipt-title">
			<h3>OFFICIAL RECEIPT</h3>
			<div class="orno">OR# <?php echo htmlspecialchars($orNo); ?></div>
		</div>

		<table class="info">
			<tr>
				<td class="label">Student Name</td>
				<td><strong><?php echo $student ? htmlspecialchars(trim($student->LNAME.', '.$student->FNAME.' '.$student->MNAME)) : '-'; ?></strong></td>
				<td class="label">Date Paid</td>
				<td><?php echo htmlspecialchars(date_toText($datePaid)); ?></td>
			</tr>
			<tr>
				<td class="label">ID No.</td>
				<td><?php echo $student ? htmlspecialchars($student->IDNO) : '-'; ?></td>
				<td class="label">Course</td>
				<td><?php echo $course ? htmlspecialchars($course->COURSE_CODE.' - '.$course->COURSE_NAME) : '-'; ?></td>
			</tr>
			<tr>
				<td class="label">Section</td>
				<td><?php echo $section ? htmlspecialchars($section->SECTION_NAME) : '-'; ?></td>
				<td class="label">Received By</td>
				<td><?php echo htmlspecialchars($receivedByName); ?></td>
			</tr>
		</table>

		<table class="items">
			<thead>
				<tr>
					<th>Particulars</th>
					<th class="amt">Amount</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($payments as $p) { ?>
				<tr>
					<td><?php echo htmlspecialchars($p->PAYMENT_TYPE); ?><?php echo ($p->REMARKS != '') ? ' - '.htmlspecialchars($p->REMARKS) : ''; ?></td>
					<td class="amt">&#8369;<?php echo number_format($p->AMOUNT, 2); ?></td>
				</tr>
				<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td>TOTAL</td>
					<td class="amt">&#8369;<?php echo number_format($total, 2); ?></td>
				</tr>
				<?php if ($cashReceived !== null) { ?>
				<tr>
					<td>Cash Received</td>
					<td class="amt">&#8369;<?php echo number_format($cashReceived, 2); ?></td>
				</tr>
				<tr>
					<td>Change</td>
					<td class="amt">&#8369;<?php echo number_format($changeDue, 2); ?></td>
				</tr>
				<?php } ?>
			</tfoot>
		</table>

		<table class="info" style="margin-top:14px;">
			<tr>
				<td class="label">Tuition Total</td>
				<td><strong>&#8369;<?php echo number_format($tuitionTotal, 2); ?></strong></td>
				<td class="label">Tuition Balance</td>
				<td><strong>&#8369;<?php echo number_format($tuitionBalance, 2); ?></strong></td>
			</tr>
		</table>

		<div class="signatures">
			<div>
				<div class="line">Cashier / Received By</div>
			</div>
			<div>
				<div class="line">Student Signature</div>
			</div>
		</div>

		<div class="no-print">
			<button class="btn-print" onclick="window.print();">🖨️ Print Receipt</button>
			<button class="btn-close" onclick="window.close();">Close</button>
		</div>
	</div>

	<script>
		window.onload = function () { window.print(); };
	</script>

<?php } ?>

</body>
</html>
