<?php

require_once("../../include/initialize.php");
global $mydb;

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action == 'delete') {
	doDelete();
} else {
	redirect('index.php');
}

/* HIPANAO SOLUTIONS - "Delete" para sa History Enrollment. Wala pang
   paraan dati ang staff para tanggalin ang isang record dito (View at
   Print lang ang aksyon), kaya dagdag ito para sa mga talagang maling
   na-archive na record.

   Ang ENROLLMENT_ID na kinukuha dito ay parehong ginagamit ng
   `tblhistoryenrollment` at ng orihinal na `tblenrollment` row (hindi
   tinatanggal ang huli kapag na-archive - STATUS na lang ang
   pinapalitan, tingnan ang archiveEnrollmentToHistory() sa
   include/functions.php). Dahil dito, at dahil ang mga sumusunod na
   FK ay ON DELETE CASCADE papuntang tblenrollment (tblenrollmentdetails,
   tblgrades, tblhistoryenrollment, tblpayments), ang pagtanggal sa
   tblenrollment mismo ay siya ring buong-buong nagtatanggal ng record
   na ito - kasama na ang mga subject, grade, at ang history entry mismo -
   kaparehong daloy ng module/enrollment/controller.php::doDelete(). */
function doDelete() {

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

	$mydb->setQuery("SELECT DETAIL_ID FROM `tblenrollmentdetails` WHERE ENROLLMENT_ID = '".$id."'");
	$subjects = $mydb->num_rows();
	$mydb->setQuery("SELECT GRADE_ID FROM `tblgrades` WHERE ENROLLMENT_ID = '".$id."'");
	$grades = $mydb->num_rows();

	if ($mydb->InsertThis("DELETE FROM `tblenrollment` WHERE `ENROLLMENT_ID` = '".$id."'")) {
		$extra = '';
		if ($subjects > 0 || $grades > 0) {
			$extra = " ".$subjects." subject record(s) and ".$grades." grade record(s) were removed with it.";
		}
		message("History record deleted.".$extra, "info");
	} else {
		message("The history record could not be deleted.", "error");
	}
	redirect('index.php');
}
?>
