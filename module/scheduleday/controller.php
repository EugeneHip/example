<?php

require_once("../../include/initialize.php");
global $mydb;

$action = (isset($_GET['action']) && $_GET['action'] != '') ? $_GET['action'] : '';

switch ($action) {
	case 'add' :
		doInsert();
		break;

	case 'edit' :
		doEdit();
		break;

	case 'delete' :
		doDelete();
		break;
}

function doInsert(){
	global $mydb;
	$DAY_NAME    = trim($_POST['DAY_NAME']);
	$DESCRIPTION = isset($_POST['DESCRIPTION']) ? trim($_POST['DESCRIPTION']) : '';

	if ($DAY_NAME === '') {
		message("Day name is required.", "error");
		redirect('index.php');
		return;
	}

	$mydb->setQuery("SELECT SCHEDULEDAY_ID FROM `tblscheduledays` WHERE DAY_NAME = '" . $mydb->escape_value($DAY_NAME) . "' LIMIT 1");
	if ($mydb->num_rows() >= 1) {
		message("A schedule day with that name already exists!", "error");
		redirect('index.php');
		return;
	}

	$sql = "INSERT INTO `tblscheduledays` (DAY_NAME, DESCRIPTION) VALUES (
			'" . $mydb->escape_value($DAY_NAME) . "',
			'" . $mydb->escape_value($DESCRIPTION) . "'
		)";
	$istrue = $mydb->InsertThis($sql);

	if ($istrue) {
		message("New schedule day [" . $DAY_NAME . "] has been added successfully!", "success");
	} else {
		message("No schedule day has been added.", "error");
	}
	redirect('index.php');
}

function doEdit(){
	global $mydb;
	if (isset($_POST['edit'])) {
		$SCHEDULEDAY_ID = intval($_POST['SCHEDULEDAY_ID']);
		$DAY_NAME       = trim($_POST['DAY_NAME1']);
		$DESCRIPTION    = isset($_POST['DESCRIPTION1']) ? trim($_POST['DESCRIPTION1']) : '';

		if ($DAY_NAME === '') {
			message("Day name is required.", "error");
			redirect('index.php');
			return;
		}

		$sql = "UPDATE `tblscheduledays` SET
				DAY_NAME    = '" . $mydb->escape_value($DAY_NAME) . "',
				DESCRIPTION = '" . $mydb->escape_value($DESCRIPTION) . "'
			WHERE SCHEDULEDAY_ID = " . $SCHEDULEDAY_ID;
		$istrue = $mydb->InsertThis($sql);

		if ($istrue) {
			message("Schedule day [" . $DAY_NAME . "] has been updated successfully!", "success");
		} else {
			message("No schedule day has been updated.", "error");
		}
		redirect('index.php');
	}
}

function doDelete(){
	global $mydb;
	$id = intval($_GET['id']);

	// SANTE SOLUTIONS - tanggalin muna ang mga naka-link na Schedule
	// entries (module/schedule) na gumagamit ng Day na ito, bago ito
	// buo tanggalin, para walang naiiwang "orphan" na schedule record.
	$mydb->InsertThis("DELETE FROM `tblschedule` WHERE SCHEDULEDAY_ID = " . $id);
	$mydb->InsertThis("DELETE FROM `tblscheduledays` WHERE SCHEDULEDAY_ID = " . $id);

	message("Schedule day has been deleted!", "success");
	redirect('index.php');
}
?>
