<?php

require_once("../../include/initialize.php");
global $mydb;

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action == 'save') {
	doSaveGrades();
} else if ($action == 'delete_grades') {
	doDeleteGradeHistory();
} else {
	redirect('index.php');
}

/* HIPANAO SOLUTIONS - Isang enrollment, maraming subject: sabay-sabay
   na naise-save ang grade/remarks ng bawat subject na kinuha, sa
   halip na isang record lang sa isang pagkakataon (dating generic
   CRUD para sa tblgrades). Nag-a-upsert - kung may record na sa
   tblgrades para sa (S_ID, SUBJECT_ID, ENROLLMENT_ID) na iyon,
   ina-update na lang; kung wala pa, gagawa ng bago. Subject na
   walang laman ang Grade at Remarks ay nilalaktawan (hindi pa
   kailangang i-grade). */
function doSaveGrades() {
	global $mydb;

	$eid = isset($_POST['GRADE_EID']) ? intval($_POST['GRADE_EID']) : 0;
	if ($eid <= 0) {
		message("No enrollment record selected.", "error");
		redirect('index.php');
		return;
	}

	$mydb->setQuery("SELECT S_ID, SY_ID, SEMESTER FROM `tblenrollment` WHERE ENROLLMENT_ID = '".$eid."' LIMIT 1");
	$rows = $mydb->loadResultList();
	if (count($rows) < 1) {
		message("That enrollment record no longer exists.", "error");
		redirect('index.php');
		return;
	}
	$enr = $rows[0];

	$subjectIds = isset($_POST['GRADE_SUBJECT_ID']) && is_array($_POST['GRADE_SUBJECT_ID']) ? $_POST['GRADE_SUBJECT_ID'] : array();
	$gradeValues = isset($_POST['GRADE_VALUE']) && is_array($_POST['GRADE_VALUE']) ? $_POST['GRADE_VALUE'] : array();
	$remarksValues = isset($_POST['GRADE_REMARKS']) && is_array($_POST['GRADE_REMARKS']) ? $_POST['GRADE_REMARKS'] : array();

	$savedCount = 0;

	foreach ($subjectIds as $sidRaw) {
		$subjectId = intval($sidRaw);
		if ($subjectId <= 0) { continue; }

		$gradeVal   = isset($gradeValues[$subjectId]) ? trim($gradeValues[$subjectId]) : '';
		$remarksVal = isset($remarksValues[$subjectId]) ? trim($remarksValues[$subjectId]) : '';

		if ($gradeVal === '' && $remarksVal === '') { continue; }

		$gradeSql   = ($gradeVal === '') ? 'NULL' : "'".floatval($gradeVal)."'";
		$remarksSql = ($remarksVal === '') ? 'NULL' : "'".$mydb->escape_value($remarksVal)."'";

		$mydb->setQuery("SELECT GRADE_ID FROM `tblgrades` 
			WHERE ENROLLMENT_ID = '".$eid."' AND SUBJECT_ID = '".$subjectId."' LIMIT 1");
		$existing = $mydb->loadResultList();

		if (count($existing) >= 1) {
			$mydb->InsertThis("UPDATE `tblgrades` 
				SET GRADE = ".$gradeSql.", REMARKS = ".$remarksSql.", DATE_ENCODED = CURDATE() 
				WHERE GRADE_ID = '".intval($existing[0]->GRADE_ID)."'");
		} else {
			$mydb->InsertThis("INSERT INTO `tblgrades` 
				(S_ID, SUBJECT_ID, ENROLLMENT_ID, SY_ID, SEMESTER, GRADE, REMARKS, DATE_ENCODED) 
				VALUES 
				('".intval($enr->S_ID)."', '".$subjectId."', '".$eid."', '".intval($enr->SY_ID)."', 
				 '".$mydb->escape_value($enr->SEMESTER)."', ".$gradeSql.", ".$remarksSql.", CURDATE())");
		}

		$savedCount++;
	}

	if ($savedCount > 0) {
		message($savedCount." grade(s) saved successfully.", "success");
	} else {
		message("No grades were entered.", "info");
	}
	redirect('index.php');
}

/* HIPANAO SOLUTIONS - "Delete" para sa History Grade. Tinatanggal
   lang ang mga naka-encode na grade (tblgrades) para sa isang
   naka-archive nang enrollment - hindi ang buong enrollment/history
   record mismo (iyon ay nasa module/hitoryenrollment na, may sarili
   nang Delete). Ginagamit dito ang ENROLLMENT_ID (parehong ginagamit
   ng tblhistoryenrollment at tblenrollment/tblgrades). */
function doDeleteGradeHistory() {

	global $mydb;

	$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

	if ($id <= 0) {
		message("No history record selected.", "error");
		redirect('index.php');
		return;
	}

	$mydb->setQuery("SELECT HISTORY_ID FROM `tblhistoryenrollment` WHERE ENROLLMENT_ID = '".$id."' LIMIT 1");
	if ($mydb->num_rows() < 1) {
		message("That history record no longer exists.", "error");
		redirect('index.php');
		return;
	}

	$mydb->setQuery("SELECT GRADE_ID FROM `tblgrades` WHERE ENROLLMENT_ID = '".$id."'");
	$gradeCount = $mydb->num_rows();

	if ($gradeCount < 1) {
		message("There are no grade records to delete for this enrollment.", "info");
		redirect('index.php');
		return;
	}

	if ($mydb->InsertThis("DELETE FROM `tblgrades` WHERE `ENROLLMENT_ID` = '".$id."'")) {
		message($gradeCount." grade record(s) deleted.", "info");
	} else {
		message("The grade record(s) could not be deleted.", "error");
	}
	redirect('index.php');
}
?>
