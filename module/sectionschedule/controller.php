<?php
require_once("../../include/initialize.php");
require_once("lib.php");
global $mydb;

if (!isset($_SESSION['UID'])) {
	redirect(WEB_ROOT."login.php");
	exit;
}

$action    = isset($_GET['action']) ? $_GET['action'] : '';
$sectionId = isset($_POST['SECTION_ID']) ? intval($_POST['SECTION_ID']) : 0;
$sem       = ss_clean_semester(isset($_POST['SEM']) ? $_POST['SEM'] : '');
$back      = 'index.php?section='.$sectionId.'&sem='.urlencode($sem);

$section = ss_load_section($sectionId);
if (!$section) {
	message("Please select a section first.", "error");
	redirect('index.php');
	exit;
}

switch ($action) {
	case 'add':
		$r = ss_try_add($section, isset($_POST['SCHEDULE_ID']) ? $_POST['SCHEDULE_ID'] : 0, $sem);
		if ($r['ok']) { message("Schedule added to the section.", "success"); }
		else          { message($r['msg'], "error"); }
		break;

	case 'addall':
		$added = 0;
		$skipped = array();
		$skippedShared = 0;
		foreach (ss_available($section, $sem) as $row) {
			if (!empty($row->SHARED_WITH)) {
				// SANTE SOLUTIONS - huwag basta-basta isama ng "Add All"
				// ang isang schedule na ginagamit na ng ibang section -
				// dapat sinasadya ito (Add + confirm), hindi bulk.
				$skippedShared++;
				continue;
			}
			$r = ss_try_add($section, $row->SCHEDULE_ID, $sem);
			if ($r['ok']) { $added++; } else { $skipped[] = $r['msg']; }
		}
		if ($skippedShared > 0) {
			$skipped[] = $skippedShared.' schedule(s) already used by another section were skipped - add those individually and confirm.';
		}
		if ($added > 0 && count($skipped) == 0) {
			message($added." schedule(s) added to the section.", "success");
		} else if ($added > 0) {
			message($added." schedule(s) added. Skipped: ".implode(' ', $skipped), "info");
		} else if (count($skipped) > 0) {
			message("Nothing added. ".implode(' ', $skipped), "error");
		} else {
			message("There is no schedule to add for this section yet.", "info");
		}
		break;

	case 'remove':
		$scheduleId = isset($_POST['SCHEDULE_ID']) ? intval($_POST['SCHEDULE_ID']) : 0;
		$mydb->InsertThis("DELETE FROM `tblsectionschedule`
			WHERE SECTION_ID = '".intval($sectionId)."' AND SCHEDULE_ID = '".$scheduleId."'");
		message("Schedule removed from the section.", "success");
		break;
}

redirect($back);
?>
