<?php
require_once("../../include/initialize.php");
global $mydb;

if (!isset($_SESSION['UID'])) {
	redirect(WEB_ROOT."login.php");
	exit;
}

/* HIPANAO SOLUTIONS - Print Enrollment Form (Admission Form). Kaparehong
   disenyo ng sample admission form na binigay ng client: letterhead,
   Student's Information, Courses Enrolled (may
   Units), at ang mga signature/approval line sa ibaba. Buong data ay
   kinukuha direkta dito (parang receipt.php) - walang kailangang extra
   ajax load. */

$EID = isset($_GET['id']) ? intval($_GET['id']) : 0;

$enroll  = null;
$student = null;
$course  = null;
$sy      = null;
$subjects = array();
$totalUnits = 0;

if ($EID > 0) {
	$mydb->setQuery("SELECT e.*, c.COURSE_CODE, c.COURSE_NAME, sy.SCHOOL_YEAR
		FROM `tblenrollment` e
		JOIN `tblcourses` c ON c.COURSE_ID = e.COURSE_ID
		JOIN `tblschoolyear` sy ON sy.SY_ID = e.SY_ID
		WHERE e.ENROLLMENT_ID = '".$EID."' LIMIT 1");
	$rows = $mydb->loadResultList();
	$enroll = (count($rows) >= 1) ? $rows[0] : null;

	if ($enroll) {
		$mydb->setQuery("SELECT * FROM `tblstudent` WHERE S_ID = '".intval($enroll->S_ID)."' LIMIT 1");
		$srows = $mydb->loadResultList();
		$student = (count($srows) >= 1) ? $srows[0] : null;

		$mydb->setQuery("SELECT sub.SUBJECT_CODE, sub.SUBJECT_NAME, sub.UNITS
			FROM `tblenrollmentdetails` d
			JOIN `tblsubjects` sub ON sub.SUBJECT_ID = d.SUBJECT_ID
			WHERE d.ENROLLMENT_ID = '".$EID."'
			ORDER BY sub.SUBJECT_TYPE ASC, sub.SUBJECT_CODE ASC");
		$subjects = $mydb->loadResultList();
		foreach ($subjects as $sub) { $totalUnits += intval($sub->UNITS); }
	}
}

function pv($val) {
	return ($val !== null && trim((string)$val) !== '') ? htmlspecialchars($val) : '-';
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Admission Form<?php echo $student ? ' - '.htmlspecialchars($student->LNAME.', '.$student->FNAME) : ''; ?></title>
<style>
	/* HIPANAO SOLUTIONS - Black & white lang ang disenyo dito (para
	   makatipid sa colored ink pag-print) - walang ibang kulay, itim,
	   puti, at abo-abo (gray) na lang, at ang mga dating may kulay na
	   "status stamp" ay itim na outline box na lang, may text label
	   mismo bilang paraan ng pagkilala (hindi na umaasa sa kulay). */
	body { font-family: Arial, Helvetica, sans-serif; color: #000; margin: 0; padding: 0; background: #fff; }
	.sheet { max-width: 820px; margin: 20px auto; background: #fff; padding: 30px 40px; }
	.letterhead { text-align: center; border-bottom: 3px solid #000; padding-bottom: 12px; margin-bottom: 6px; }
	.letterhead img { height: 60px; margin-bottom: 6px; filter: grayscale(100%); }
	.letterhead h2 { margin: 4px 0 0; color: #000; letter-spacing: .5px; }
	.letterhead small { color: #333; display: block; }
	.form-title { text-align: center; margin: 14px 0 18px; }
	.form-title h3 { margin: 0; letter-spacing: 2px; text-decoration: underline; }
	.top-meta { width: 100%; border-collapse: collapse; margin-bottom: 14px; font-size: 0.9em; }
	.top-meta td { padding: 2px 4px; }
	.top-meta td.label { color: #333; }
	.section-title { background: #000; color: #fff; padding: 5px 10px; font-weight: bold; font-size: 0.95em; margin-top: 14px; }
	table.info-grid { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
	table.info-grid td { border: 1px solid #000; padding: 5px 8px; font-size: 0.9em; vertical-align: top; }
	table.info-grid td.k { background: #f0f0f0; color: #000; width: 20%; font-weight: bold; }
	table.info-grid td.v { width: 30%; }
	table.courses { width: 100%; border-collapse: collapse; margin-top: 4px; }
	table.courses th, table.courses td { border: 1px solid #000; padding: 6px 8px; font-size: 0.9em; }
	table.courses th { background: #e8e8e8; text-align: left; }
	table.courses td.units, table.courses th.units { text-align: center; width: 10%; }
	table.courses tfoot td { font-weight: bold; background: #f5f5f5; }
	.signatures { width: 100%; border-collapse: collapse; margin-top: 40px; }
	.signatures td { width: 50%; text-align: center; padding: 0 15px; vertical-align: bottom; }
	.signatures .sig-line { border-top: 1px solid #000; margin-top: 45px; padding-top: 4px; font-size: 0.85em; }
	.signatures .sig-role { color: #333; font-size: 0.8em; }
	.status-stamp { display: inline-block; border: 3px solid #000; color: #000; font-weight: bold; letter-spacing: 2px; padding: 6px 16px; transform: rotate(-6deg); font-size: 1.1em; margin-top: 10px; }
	.status-stamp.not-enrolled { border-style: dashed; }
	.no-print { text-align: center; margin: 18px 0; }
	.no-print button, .no-print a.btn-close { display: inline-block; padding: 8px 22px; margin: 0 5px; border: 1px solid #000; border-radius: 4px; cursor: pointer; font-size: 0.95em; text-decoration: none; }
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

<?php if (!$enroll || !$student) { ?>
	<div class="sheet">
		<p class="not-found">Walang nahanap na enrollment record.</p>
		<div class="no-print"><button class="btn-close" onclick="window.close();">Close</button></div>
	</div>
<?php } else { ?>

	<div class="sheet">

		<div class="letterhead">
			<img src="<?php echo WEB_ROOT.SCHOOL_LOGO; ?>" onerror="this.style.display='none';">
			<h2><?php echo htmlspecialchars(SCHOOL_NAME); ?></h2>
			<small><?php echo htmlspecialchars(SCHOOL_ADDRESS); ?></small>
		</div>

		<div class="form-title">
			<h3>ADMISSION FORM</h3>
		</div>

		<table class="top-meta">
			<tr>
				<td class="label">Student ID:</td>
				<td><strong><?php echo pv($student->IDNO); ?></strong></td>
				<td class="label" style="text-align:right;">Date Enrolled:</td>
				<td style="text-align:right;"><strong><?php echo $enroll->DATE_ENROLLED ? pv(date('m/d/Y', strtotime($enroll->DATE_ENROLLED))) : '-'; ?></strong></td>
			</tr>
			<tr>
				<td class="label">School Year:</td>
				<td><?php echo pv($enroll->SCHOOL_YEAR); ?> (<?php echo pv($enroll->SEMESTER); ?>)</td>
				<td class="label" style="text-align:right;">Enrollment Status:</td>
				<td style="text-align:right;"><?php echo pv($enroll->STATUS); ?></td>
			</tr>
		</table>

		<div class="section-title">Student's Information</div>
		<table class="info-grid">
			<tr>
				<td class="k">Last Name</td><td class="v"><?php echo pv($student->LNAME); ?></td>
				<td class="k">First Name</td><td class="v"><?php echo pv($student->FNAME); ?></td>
			</tr>
			<tr>
				<td class="k">Middle Name</td><td class="v"><?php echo pv($student->MNAME); ?></td>
				<td class="k">Course / Year Level</td><td class="v"><?php echo pv($enroll->COURSE_CODE.' - '.$enroll->YEAR_LEVEL); ?></td>
			</tr>
			<tr>
				<td class="k">LRN</td><td class="v"><?php echo pv(isset($student->LRNNO) ? $student->LRNNO : ''); ?></td>
				<td class="k">Civil Status</td><td class="v"><?php echo pv(isset($student->CIVIL_STATUS) ? $student->CIVIL_STATUS : ''); ?></td>
			</tr>
			<tr>
				<td class="k">Sex</td><td class="v"><?php echo pv($student->SEX); ?></td>
				<td class="k">Birthday</td><td class="v"><?php echo $student->BDAY ? pv(date('Y-m-d', strtotime($student->BDAY))) : '-'; ?></td>
			</tr>
			<tr>
				<td class="k">Birthplace</td><td class="v"><?php echo pv(isset($student->BPLACE) ? $student->BPLACE : ''); ?></td>
				<td class="k">Nationality</td><td class="v"><?php echo pv(isset($student->NATIONALITY) ? $student->NATIONALITY : ''); ?></td>
			</tr>
			<tr>
				<td class="k">Religion</td><td class="v"><?php echo pv(isset($student->RELIGION) ? $student->RELIGION : ''); ?></td>
				<td class="k">Mobile Number</td><td class="v"><?php echo pv(isset($student->CONTACT_NO) ? $student->CONTACT_NO : ''); ?></td>
			</tr>
			<tr>
				<td class="k">Email Address</td><td class="v" colspan="3"><?php echo pv(isset($student->EMAIL) ? $student->EMAIL : ''); ?></td>
			</tr>
			<tr>
				<td class="k">Address</td><td class="v" colspan="3"><?php echo pv(isset($student->HOME_ADD) ? $student->HOME_ADD : ''); ?></td>
			</tr>
		</table>

		<div class="section-title">Parents' / Guardian's Information</div>
		<table class="info-grid">
			<tr>
				<td class="k">Father's Name</td><td class="v"><?php echo pv(isset($student->FATHER_NAME) ? $student->FATHER_NAME : ''); ?></td>
				<td class="k">Father's Contact No.</td><td class="v"><?php echo pv(isset($student->FATHER_CONTACT) ? $student->FATHER_CONTACT : ''); ?></td>
			</tr>
			<tr>
				<td class="k">Father's Occupation</td><td class="v"><?php echo pv(isset($student->FATHER_OCCUPATION) ? $student->FATHER_OCCUPATION : ''); ?></td>
				<td class="k">Father Deceased?</td><td class="v"><?php echo pv(isset($student->FATHER_DECEASED) ? $student->FATHER_DECEASED : 'No'); ?></td>
			</tr>
			<tr>
				<td class="k">Mother's Maiden Name</td><td class="v"><?php echo pv(isset($student->MOTHER_NAME) ? $student->MOTHER_NAME : ''); ?></td>
				<td class="k">Mother's Contact No.</td><td class="v"><?php echo pv(isset($student->MOTHER_CONTACT) ? $student->MOTHER_CONTACT : ''); ?></td>
			</tr>
			<tr>
				<td class="k">Mother's Occupation</td><td class="v"><?php echo pv(isset($student->MOTHER_OCCUPATION) ? $student->MOTHER_OCCUPATION : ''); ?></td>
				<td class="k">Mother Deceased?</td><td class="v"><?php echo pv(isset($student->MOTHER_DECEASED) ? $student->MOTHER_DECEASED : 'No'); ?></td>
			</tr>
			<tr>
				<td class="k">Guardian's Name</td><td class="v"><?php echo pv(isset($student->GUARDIAN_NAME) ? $student->GUARDIAN_NAME : ''); ?></td>
				<td class="k">Relationship</td><td class="v"><?php echo pv(isset($student->GUARDIAN_RELATIONSHIP) ? $student->GUARDIAN_RELATIONSHIP : ''); ?></td>
			</tr>
			<tr>
				<td class="k">Guardian's Contact No.</td><td class="v"><?php echo pv(isset($student->GUARDIAN_CONTACT) ? $student->GUARDIAN_CONTACT : ''); ?></td>
				<td class="k">Guardian's Email</td><td class="v"><?php echo pv(isset($student->GUARDIAN_EMAIL) ? $student->GUARDIAN_EMAIL : ''); ?></td>
			</tr>
			<tr>
				<td class="k">Guardian's Address</td><td class="v" colspan="3"><?php echo pv(isset($student->GUARDIAN_ADDRESS) ? $student->GUARDIAN_ADDRESS : ''); ?></td>
			</tr>
		</table>

		<div class="section-title">Courses Enrolled</div>
		<table class="courses">
			<thead>
				<tr>
					<th style="width:15%">Course Code</th>
					<th>Course Description</th>
					<th class="units">Units</th>
				</tr>
			</thead>
			<tbody>
				<?php if (count($subjects) < 1) { ?>
				<tr><td colspan="3" style="text-align:center;color:#888;">No subjects taken yet for this enrollment.</td></tr>
				<?php } else { foreach ($subjects as $sub) { ?>
				<tr>
					<td><?php echo pv($sub->SUBJECT_CODE); ?></td>
					<td><?php echo pv($sub->SUBJECT_NAME); ?></td>
					<td class="units"><?php echo (int)$sub->UNITS; ?></td>
				</tr>
				<?php } } ?>
			</tbody>
			<?php if (count($subjects) > 0) { ?>
			<tfoot>
				<tr>
					<td colspan="2" style="text-align:right;">Total Units</td>
					<td class="units"><?php echo (int)$totalUnits; ?></td>
				</tr>
			</tfoot>
			<?php } ?>
		</table>

		<div style="text-align:center;">
			<?php if (strtolower($enroll->STATUS) == 'enroll') { ?>
				<span class="status-stamp">ENROLLED</span>
			<?php } else { ?>
				<span class="status-stamp not-enrolled"><?php echo htmlspecialchars(strtoupper($enroll->STATUS)); ?></span>
			<?php } ?>
		</div>

		<table class="signatures">
			<tr>
				<td>
					<div class="sig-line">Program Head</div>
					<div class="sig-role">Approved By</div>
				</td>
				<td>
					<div class="sig-line">Registrar</div>
					<div class="sig-role">Verified By</div>
				</td>
			</tr>
			<tr>
				<td>
					<div class="sig-line">Guidance Counselor</div>
					<div class="sig-role">Noted By</div>
				</td>
				<td>
					<div class="sig-line">Treasurer</div>
					<div class="sig-role">Certified By</div>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<div class="sig-line" style="width:60%; margin-left:auto; margin-right:auto;">
						<?php echo pv(trim($student->LNAME.', '.$student->FNAME.' '.$student->MNAME)); ?>
					</div>
					<div class="sig-role">Student</div>
				</td>
			</tr>
		</table>

		<div class="no-print">
			<button class="btn-print" onclick="window.print();">🖨️ Print Form</button>
			<a class="btn-close" href="<?php echo WEB_ROOT; ?>module/enrollmentdetails/index.php">✔ Close</a>
		</div>

	</div>

<?php } ?>

</body>
</html>
