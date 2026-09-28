<?php

require_once("../../include/initialize.php");
global $mydb;

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action == 'subjects') {
	doAssignSubjects();
} else if ($action == 'requirements') {
	doRequirements();
} else if ($action == 'edit') {
	doEdit();
} else if ($action == 'drop') {
	doDrop();
} else if ($action == 'register') {
	doRegisterAgain();
} else {
	redirect('index.php');
}

/* HIPANAO SOLUTIONS - "Requirements / Documents" para sa mga estudyanteng
   "Enroll" na - baka may kulang pang naisumite noong Document stage
   (module/enrollment) na napansin lang ngayon. Iisang REQ_* fields sa
   tblstudent ang ginagamit dito (hindi na kailangang balik pa sa
   Enrollment module) - walang status transition dito dahil "Enroll"
   na talaga ang estudyante, save lang ng checklist. */
function doRequirements() {

	global $mydb;

	$EID = isset($_POST['REQ_EID']) ? intval($_POST['REQ_EID']) : 0;

	if ($EID <= 0) {
		message("No enrollment record selected.", "error");
		redirect('index.php');
		return;
	}

	$mydb->setQuery("SELECT S_ID FROM `tblenrollment` WHERE ENROLLMENT_ID = '".$EID."' LIMIT 1");
	$rows = $mydb->loadResultList();
	if (count($rows) < 1) {
		message("That enrollment record no longer exists.", "error");
		redirect('index.php');
		return;
	}
	$S_ID = intval($rows[0]->S_ID);

	$req138          = isset($_POST['REQ_FORM138'])          ? 1 : 0;
	$reqGoodmoral    = isset($_POST['REQ_GOODMORAL'])        ? 1 : 0;
	$reqBirthcert    = isset($_POST['REQ_BIRTHCERT'])        ? 1 : 0;
	$reqBaptismal    = isset($_POST['REQ_BAPTISMAL'])        ? 1 : 0;
	$reqAssessment   = isset($_POST['REQ_ASSESSMENT'])       ? 1 : 0;
	$reqTransfercred = isset($_POST['REQ_TRANSFERCRED'])     ? 1 : 0;
	$reqMarriage     = isset($_POST['REQ_MARRIAGECONTRACT']) ? 1 : 0;
	$req2x2          = isset($_POST['REQ_2X2PICTURE'])       ? 1 : 0;
	$others1 = isset($_POST['REQ_OTHERS1']) ? trim($_POST['REQ_OTHERS1']) : '';
	$others2 = isset($_POST['REQ_OTHERS2']) ? trim($_POST['REQ_OTHERS2']) : '';
	$others3 = isset($_POST['REQ_OTHERS3']) ? trim($_POST['REQ_OTHERS3']) : '';
	$notes   = isset($_POST['REQ_NOTES'])   ? trim($_POST['REQ_NOTES'])   : '';

	$mydb->InsertThis("UPDATE `tblstudent` SET
			REQ_FORM138          = '".$req138."',
			REQ_GOODMORAL        = '".$reqGoodmoral."',
			REQ_BIRTHCERT        = '".$reqBirthcert."',
			REQ_BAPTISMAL        = '".$reqBaptismal."',
			REQ_ASSESSMENT       = '".$reqAssessment."',
			REQ_TRANSFERCRED     = '".$reqTransfercred."',
			REQ_MARRIAGECONTRACT = '".$reqMarriage."',
			REQ_2X2PICTURE       = '".$req2x2."',
			REQ_OTHERS1          = '".$mydb->escape_value($others1)."',
			REQ_OTHERS2          = '".$mydb->escape_value($others2)."',
			REQ_OTHERS3          = '".$mydb->escape_value($others3)."',
			REQ_NOTES            = '".$mydb->escape_value($notes)."'
		WHERE S_ID = '".$S_ID."'");

	message("Requirements updated.", "success");
	redirect('index.php');
}

/* HIPANAO SOLUTIONS - Ginagamit ng doEdit() sa ibaba para i-validate
   ang Section kapag na-set ng staff sa Edit modal (kaparehong helper
   ng module/enrollment/controller.php). */
function sectionBelongsTo($sectionId, $courseId, $syId) {
	global $mydb;
	$mydb->setQuery("SELECT SECTION_ID FROM `tblsections`
		WHERE SECTION_ID = '".intval($sectionId)."'
		  AND COURSE_ID  = '".intval($courseId)."'
		  AND SY_ID      = '".intval($syId)."'
		LIMIT 1");
	return ($mydb->num_rows() >= 1);
}

/* HIPANAO SOLUTIONS - "Edit" - dito na-eedit ang Course, Section,
   Academic Year, Semester, Year Level, Category, Curriculum, at Status
   ng isang record dito mismo sa Enrollment Details, kaparehong
   validation ng module/enrollment/controller.php::doEdit(), pero
   pabalik dito na lang muli ang redirect. */
function doEdit() {

	global $mydb;

	$EID        = isset($_POST['ED_EID'])           ? intval($_POST['ED_EID'])          : 0;
	$SY_ID      = isset($_POST['ED_SY'])             ? intval($_POST['ED_SY'])           : 0;
	$COURSE_ID  = isset($_POST['ED_COURSE'])         ? intval($_POST['ED_COURSE'])       : 0;
	$SECTION_ID = isset($_POST['ED_SECTION'])        ? intval($_POST['ED_SECTION'])      : 0;
	$YEAR_LEVEL = isset($_POST['ED_YEARLEVEL'])      ? trim($_POST['ED_YEARLEVEL'])      : '';
	$SEMESTER   = isset($_POST['ED_SEMESTER'])       ? trim($_POST['ED_SEMESTER'])       : '';
	$CATEGORY   = isset($_POST['ED_CATEGORY'])       ? trim($_POST['ED_CATEGORY'])       : 'New';
	$CURRICULUM = isset($_POST['ED_CURRICULUM'])     ? trim($_POST['ED_CURRICULUM'])     : '';
	$STATUS     = isset($_POST['ED_STATUS'])         ? trim($_POST['ED_STATUS'])         : 'Enroll';
	$RESERVED   = isset($_POST['ED_DATE_RESERVED'])  ? trim($_POST['ED_DATE_RESERVED'])  : '';
	$ENROLLED   = isset($_POST['ED_DATE_ENROLLED'])  ? trim($_POST['ED_DATE_ENROLLED'])  : '';

	if ($EID <= 0 || $SY_ID <= 0 || $COURSE_ID <= 0 || $YEAR_LEVEL == '' || $SEMESTER == '') {
		message("Please complete all the required fields.", "error");
		redirect('index.php');
		return;
	}

	if ($SECTION_ID > 0 && !sectionBelongsTo($SECTION_ID, $COURSE_ID, $SY_ID)) {
		message("That section does not belong to the selected course and academic year.", "error");
		redirect('index.php');
		return;
	}

	$mydb->setQuery("SELECT S_ID FROM `tblenrollment` WHERE ENROLLMENT_ID = '".$EID."' LIMIT 1");
	$owner = $mydb->loadResultList();
	if (count($owner) < 1) {
		message("That enrollment record no longer exists.", "error");
		redirect('index.php');
		return;
	}
	$S_ID = $owner[0]->S_ID;

	$mydb->setQuery("SELECT ENROLLMENT_ID FROM `tblenrollment`
		WHERE S_ID     = '".$S_ID."'
		  AND SY_ID    = '".$SY_ID."'
		  AND SEMESTER = '".$mydb->escape_value($SEMESTER)."'
		  AND ENROLLMENT_ID <> '".$EID."'
		LIMIT 1");
	if ($mydb->num_rows() >= 1) {
		message("This student already has another record for that academic year and semester.", "error");
		redirect('index.php');
		return;
	}

	$sectionSql    = ($SECTION_ID > 0) ? "'".$SECTION_ID."'" : "NULL";
	$curriculumSql = ($CURRICULUM == '') ? "NULL" : "'".$mydb->escape_value($CURRICULUM)."'";
	$reservedSql   = ($RESERVED == '')   ? "NULL" : "'".$mydb->escape_value($RESERVED)."'";
	$enrolledSql   = ($ENROLLED == '')   ? "NULL" : "'".$mydb->escape_value($ENROLLED)."'";

	$sql = "UPDATE `tblenrollment` SET
			`COURSE_ID`     = '".$COURSE_ID."',
			`SECTION_ID`    = ".$sectionSql.",
			`SY_ID`         = '".$SY_ID."',
			`YEAR_LEVEL`    = '".$mydb->escape_value($YEAR_LEVEL)."',
			`SEMESTER`      = '".$mydb->escape_value($SEMESTER)."',
			`CATEGORY`      = '".$mydb->escape_value($CATEGORY)."',
			`CURRICULUM_YR` = ".$curriculumSql.",
			`DATE_RESERVED` = ".$reservedSql.",
			`DATE_ENROLLED` = ".$enrolledSql.",
			`STATUS`        = '".$mydb->escape_value($STATUS)."'
		WHERE `ENROLLMENT_ID` = '".$EID."'";

	if ($mydb->InsertThis($sql)) {
		message("Enrollment record updated.", "success");
	} else {
		message("The enrollment record could not be updated.", "error");
	}
	redirect('index.php');
}

/* HIPANAO SOLUTIONS - "Drop" - mabilis na aksyon (walang form, may
   confirm lang sa JS side) para markahan ang isang record bilang
   'Dropped'. Mawawala na ito sa listahan ng Enrollment Details dahil
   ang filter doon ay STATUS = 'Enroll' lang. */
function doDrop() {

	global $mydb;

	$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

	if ($id <= 0) {
		message("No enrollment record selected.", "error");
		redirect('index.php');
		return;
	}

	$mydb->setQuery("SELECT ENROLLMENT_ID FROM `tblenrollment` WHERE ENROLLMENT_ID = '".$id."' LIMIT 1");
	if ($mydb->num_rows() < 1) {
		message("That enrollment record no longer exists.", "error");
		redirect('index.php');
		return;
	}

	if ($mydb->InsertThis("UPDATE `tblenrollment` SET `STATUS` = 'Dropped' WHERE `ENROLLMENT_ID` = '".$id."'")) {
		/* HIPANAO SOLUTIONS - I-archive papunta sa tblhistoryenrollment
		   para dito na rin lumabas ang record na ito sa History
		   Enrollment module (module/hitoryenrollment). */
		archiveEnrollmentToHistory($id);
		message("Student has been dropped from this enrollment.", "info");
	} else {
		message("Could not drop this enrollment record.", "error");
	}
	redirect('index.php');
}

/* HIPANAO SOLUTIONS - "Register Again" (Stage 1 muli) - bagong
   tblenrollment row para sa PAREHONG estudyante (parehong S_ID), para
   sa susunod na semester o susunod na Year Level. Kaparehong daloy ng
   Student > Register Enrollment Slot (module/student/controller.php::
   doRegister()) - dinadala pabalik ang staff sa Enrollment module para
   doon ituloy ang Assign -> Payment -> Enroll. */
function doRegisterAgain() {

	global $mydb;

	$S_ID       = isset($_POST['RA_SID'])           ? intval($_POST['RA_SID'])         : 0;
	$SOURCE_EID = isset($_POST['RA_EID'])           ? intval($_POST['RA_EID'])         : 0;
	$SY_ID      = isset($_POST['RA_SY'])             ? intval($_POST['RA_SY'])          : 0;
	$COURSE_ID  = isset($_POST['RA_COURSE'])         ? intval($_POST['RA_COURSE'])      : 0;
	$YEAR_LEVEL = isset($_POST['RA_YEARLEVEL'])      ? trim($_POST['RA_YEARLEVEL'])     : '';
	$SEMESTER   = isset($_POST['RA_SEMESTER'])       ? trim($_POST['RA_SEMESTER'])      : '';
	$CATEGORY   = isset($_POST['RA_CATEGORY'])       ? trim($_POST['RA_CATEGORY'])      : 'Old';
	$CURRICULUM = isset($_POST['RA_CURRICULUM'])     ? trim($_POST['RA_CURRICULUM'])    : '';
	$RESERVED   = isset($_POST['RA_DATE_RESERVED'])  ? trim($_POST['RA_DATE_RESERVED']) : '';

	$enrollmentPage = WEB_ROOT.'module/enrollment/index.php';

	if ($S_ID <= 0 || $SY_ID <= 0 || $COURSE_ID <= 0 || $YEAR_LEVEL == '' || $SEMESTER == '') {
		message("Please complete all the registration fields.", "error");
		redirect('index.php');
		return;
	}

	$mydb->setQuery("SELECT S_ID FROM `tblstudent` WHERE S_ID = '".$S_ID."' LIMIT 1");
	if ($mydb->num_rows() < 1) {
		message("That student record no longer exists.", "error");
		redirect('index.php');
		return;
	}

	/* HIPANAO SOLUTIONS - Server-side na ulit na check (huwag lang
	   umasa sa JS/frontend na check sa module/enrollmentdetails/index.php)
	   - hindi dapat makapag-register ulit ang estudyante kung hindi pa
	   bayad nang buo ang tuition o wala pang kumpletong grade sa
	   kasalukuyan niyang enrollment - tingnan ang canRegisterAgain()
	   sa include/functions.php. */
	$eligibility = canRegisterAgain($SOURCE_EID);
	if (!$eligibility['ok']) {
		message($eligibility['reason'] ? $eligibility['reason'] : "This student is not yet eligible to register again for the next term.", "error");
		redirect('index.php');
		return;
	}

	if ($RESERVED == '') { $RESERVED = date('Y-m-d'); }

	$mydb->setQuery("SELECT ENROLLMENT_ID FROM `tblenrollment`
		WHERE S_ID     = '".$S_ID."'
		  AND SY_ID    = '".$SY_ID."'
		  AND SEMESTER = '".$mydb->escape_value($SEMESTER)."'
		LIMIT 1");
	if ($mydb->num_rows() >= 1) {
		message("This student already has a record for that academic year and semester.", "error");
		redirect('index.php');
		return;
	}

	$encodedBy = isset($_SESSION['UID']) ? intval($_SESSION['UID']) : 0;
	$encodedSql = ($encodedBy > 0) ? "'".$encodedBy."'" : "NULL";
	$curriculumSql = ($CURRICULUM == '') ? "NULL" : "'".$mydb->escape_value($CURRICULUM)."'";

	$sql = "INSERT INTO `tblenrollment`
		(`S_ID`, `COURSE_ID`, `SECTION_ID`, `SY_ID`, `YEAR_LEVEL`, `SEMESTER`,
		 `CATEGORY`, `CURRICULUM_YR`, `DATE_RESERVED`, `DATE_ENROLLED`, `STATUS`, `ENCODED_BY`)
		VALUES ('".$S_ID."', '".$COURSE_ID."', NULL, '".$SY_ID."',
		'".$mydb->escape_value($YEAR_LEVEL)."', '".$mydb->escape_value($SEMESTER)."',
		'".$mydb->escape_value($CATEGORY)."', ".$curriculumSql.",
		'".$mydb->escape_value($RESERVED)."', NULL, 'Register', ".$encodedSql.")";

	if ($mydb->InsertThis($sql)) {
		$mydb->InsertThis("UPDATE `tblstudent` SET `COURSE_ID` = '".$COURSE_ID."' WHERE `S_ID` = '".$S_ID."'");
		message("New enrollment slot registered for the next term. Go to Enrollment > Subjects to Assign this student's subjects next.", "success");
		redirect($enrollmentPage);
	} else {
		message("The registration could not be saved.", "error");
		redirect('index.php');
	}
}

/* HIPANAO SOLUTIONS - Same save path as Enrollment > Subjects
   (module/enrollment/controller.php::doAssignSubjects). Reusing
   saveEnrollmentSubjects() here means there is only ever one place
   that decides which subjects belong to an enrollment - no more
   free-form single-subject edits on this screen that could drift out
   of sync with what Enrollment > Subjects shows. */
function doAssignSubjects() {

	global $mydb;

	$EID = isset($_POST['MS_EID']) ? intval($_POST['MS_EID']) : 0;
	$posted = isset($_POST['MS_SUBJECTS']) && is_array($_POST['MS_SUBJECTS']) ? $_POST['MS_SUBJECTS'] : array();

	if ($EID <= 0) {
		message("No enrollment record selected.", "error");
		redirect('index.php');
		return;
	}

	$mydb->setQuery("SELECT ENROLLMENT_ID FROM `tblenrollment` WHERE ENROLLMENT_ID = '".$EID."' LIMIT 1");
	if ($mydb->num_rows() < 1) {
		message("That enrollment record no longer exists.", "error");
		redirect('index.php');
		return;
	}

	$kept = saveEnrollmentSubjects($EID, $posted);

	if ($kept > 0) {
		message("Subjects updated (".$kept." subject(s) kept for this enrollment).", "success");
	} else {
		message("All subjects were removed from this enrollment.", "info");
	}
	redirect('index.php');
}
?>
