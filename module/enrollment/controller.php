<?php

require_once("../../include/initialize.php");
global $mydb;

$action = (isset($_GET['action']) && $_GET['action'] != '') ? $_GET['action'] : '';

switch ($action) {
	case 'subjects' :
		doAssignSubjects();
		break;

	case 'document' :
		doDocument();
		break;

	case 'enroll' :
		doMarkEnroll();
		break;

	case 'edit' :
		doEdit();
		break;

	case 'delete' :
		doDelete();
		break;
}

/* HIPANAO SOLUTIONS - Hindi na hiwalay na hakbang ang Sectioning
   (tinanggal ang action=section route at ang doSectioning()), pero
   naiiwan pa rin ang helper na ito - ginagamit pa rin ng doEdit()
   sa ibaba para i-validate ang Section kapag na-set ng staff sa Edit
   Enrollment modal. */
function sectionBelongsTo($sectionId, $courseId, $syId) {
	global $mydb;
	$mydb->setQuery("SELECT SECTION_ID FROM `tblsections` 
		WHERE SECTION_ID = '".intval($sectionId)."' 
		  AND COURSE_ID  = '".intval($courseId)."' 
		  AND SY_ID      = '".intval($syId)."' 
		LIMIT 1");
	return ($mydb->num_rows() >= 1);
}

function doAssignSubjects() {

	global $mydb;

	$EID = isset($_POST['SUB_EID']) ? intval($_POST['SUB_EID']) : 0;
	$posted = isset($_POST['SUB_SUBJECTS']) && is_array($_POST['SUB_SUBJECTS']) ? $_POST['SUB_SUBJECTS'] : array();

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

	$finalStatus = currentEnrollmentStatus($EID);
	$extra = '';
	if ($finalStatus == 'Assign') {
		$extra = " Head to the Payment module to collect the enrollment fee and finish enrolling this student.";
	}
	message("Subjects saved (".$kept."). The student is now ".$finalStatus.".".$extra, "success");
	redirect('index.php');
}

/* HIPANAO SOLUTIONS - Bagong Stage 3: "Document". Ito ang tumatanggap
   ng checklist mula sa "Document" modal (list.php) - ang mga checkbox
   ay naka-store sa tblstudent (REQ_* fields, tingnan ang
   database/migration_add_student_profile_fields.sql), hindi sa
   tblenrollment, dahil isa lang ang requirements checklist per
   estudyante kahit maraming beses siyang mag-enroll. Pag-save nito
   habang "Assign" pa ang STATUS ng enrollment record na ito,
   awtomatiko itong lilipat sa "Document" - katulad ng paglipat mula
   "Register" papuntang "Assign" pag may na-save nang subject
   (tingnan ang saveEnrollmentSubjects() sa include/functions.php). */
function doDocument() {

	global $mydb;

	$EID = isset($_POST['DOC_EID']) ? intval($_POST['DOC_EID']) : 0;

	if ($EID <= 0) {
		message("No enrollment record selected.", "error");
		redirect('index.php');
		return;
	}

	$mydb->setQuery("SELECT S_ID, STATUS FROM `tblenrollment` WHERE ENROLLMENT_ID = '".$EID."' LIMIT 1");
	$rows = $mydb->loadResultList();
	if (count($rows) < 1) {
		message("That enrollment record no longer exists.", "error");
		redirect('index.php');
		return;
	}
	$S_ID  = intval($rows[0]->S_ID);
	$status = $rows[0]->STATUS;

	$req138          = isset($_POST['DOC_FORM138'])          ? 1 : 0;
	$reqGoodmoral    = isset($_POST['DOC_GOODMORAL'])        ? 1 : 0;
	$reqBirthcert    = isset($_POST['DOC_BIRTHCERT'])        ? 1 : 0;
	$reqBaptismal    = isset($_POST['DOC_BAPTISMAL'])        ? 1 : 0;
	$reqAssessment   = isset($_POST['DOC_ASSESSMENT'])       ? 1 : 0;
	$reqTransfercred = isset($_POST['DOC_TRANSFERCRED'])     ? 1 : 0;
	$reqMarriage     = isset($_POST['DOC_MARRIAGECONTRACT']) ? 1 : 0;
	$req2x2          = isset($_POST['DOC_2X2PICTURE'])       ? 1 : 0;
	$others1 = isset($_POST['DOC_OTHERS1']) ? trim($_POST['DOC_OTHERS1']) : '';
	$others2 = isset($_POST['DOC_OTHERS2']) ? trim($_POST['DOC_OTHERS2']) : '';
	$others3 = isset($_POST['DOC_OTHERS3']) ? trim($_POST['DOC_OTHERS3']) : '';
	$notes   = isset($_POST['DOC_NOTES'])   ? trim($_POST['DOC_NOTES'])   : '';

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

	if ($status == 'Assign') {
		$mydb->InsertThis("UPDATE `tblenrollment` SET STATUS = 'Document' WHERE ENROLLMENT_ID = '".$EID."'");
		message("Requirements saved. The student is now Document. Head to the Payment module once ready.", "success");
	} else {
		message("Requirements updated.", "success");
	}
	redirect('index.php');
}

/* HIPANAO SOLUTIONS - Huling manual na hakbang: "Enroll". Kapag na-tap
   ng staff ang aksyong "Enroll" sa isang estudyanteng "Paid" na ang
   STATUS, dito na dinedeklarang opisyal na "Enroll" ang record
   (markAsEnrolled() sa include/functions.php), at saka agad dinadala
   ang staff sa Print Enrollment Form ng estudyanteng iyon
   (module/enrollmentdetails/print.php). Mula roon, may "Back" na button
   pabalik sa Enrollment Details module - doon na makikita ang
   estudyanteng ito bilang "Enroll", at doon na rin puwedeng mag-add o
   mag-drop pa ng subjects kung kailangan. */
function doMarkEnroll() {

	global $mydb;

	$EID = isset($_GET['id']) ? intval($_GET['id']) : 0;

	if ($EID <= 0) {
		message("No enrollment record selected.", "error");
		redirect('index.php');
		return;
	}

	$mydb->setQuery("SELECT STATUS FROM `tblenrollment` WHERE ENROLLMENT_ID = '".$EID."' LIMIT 1");
	$rows = $mydb->loadResultList();
	if (count($rows) < 1) {
		message("That enrollment record no longer exists.", "error");
		redirect('index.php');
		return;
	}

	if ($rows[0]->STATUS != 'Paid') {
		message("This student must be fully Paid before they can be Enrolled.", "error");
		redirect('index.php');
		return;
	}

	if (markAsEnrolled($EID)) {
		redirect(WEB_ROOT.'module/enrollmentdetails/print.php?id='.$EID);
	} else {
		message("This student could not be marked as Enrolled.", "error");
		redirect('index.php');
	}
}

function doEdit() {

	global $mydb;

	$EID        = isset($_POST['E_EID'])            ? intval($_POST['E_EID'])           : 0;
	$SY_ID      = isset($_POST['E_SY'])             ? intval($_POST['E_SY'])            : 0;
	$COURSE_ID  = isset($_POST['E_COURSE'])         ? intval($_POST['E_COURSE'])        : 0;
	$SECTION_ID = isset($_POST['E_SECTION'])        ? intval($_POST['E_SECTION'])       : 0;
	$YEAR_LEVEL = isset($_POST['E_YEARLEVEL'])      ? trim($_POST['E_YEARLEVEL'])       : '';
	$SEMESTER   = isset($_POST['E_SEMESTER'])       ? trim($_POST['E_SEMESTER'])        : '';
	$CATEGORY   = isset($_POST['E_CATEGORY'])       ? trim($_POST['E_CATEGORY'])        : 'New';
	$CURRICULUM = isset($_POST['E_CURRICULUM'])     ? trim($_POST['E_CURRICULUM'])      : '';
	$STATUS     = isset($_POST['E_STATUS'])         ? trim($_POST['E_STATUS'])          : 'Register';
	$RESERVED   = isset($_POST['E_DATE_RESERVED'])  ? trim($_POST['E_DATE_RESERVED'])   : '';
	$ENROLLED   = isset($_POST['E_DATE_ENROLLED'])  ? trim($_POST['E_DATE_ENROLLED'])   : '';

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

function doDelete() {

	global $mydb;

	$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

	if ($id <= 0) {
		message("No enrollment record selected.", "error");
		redirect('index.php');
		return;
	}

	$mydb->setQuery("SELECT DETAIL_ID FROM `tblenrollmentdetails` WHERE ENROLLMENT_ID = '".$id."'");
	$subjects = $mydb->num_rows();
	$mydb->setQuery("SELECT GRADE_ID FROM `tblgrades` WHERE ENROLLMENT_ID = '".$id."'");
	$grades = $mydb->num_rows();

	if ($mydb->InsertThis("DELETE FROM `tblenrollment` WHERE `ENROLLMENT_ID` = '".$id."'")) {
		$extra = '';
		if ($subjects > 0 || $grades > 0) {
			$extra = " ".$subjects." subject record(s) and ".$grades." grade record(s) were removed with it.";
		}
		message("Enrollment record deleted.".$extra, "info");
	} else {
		message("The enrollment record could not be deleted.", "error");
	}
	redirect('index.php');
}
?>
