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
	$TIME_START  = trim($_POST['TIME_START']);
	$TIME_END    = trim($_POST['TIME_END']);
	$DESCRIPTION = isset($_POST['DESCRIPTION']) ? trim($_POST['DESCRIPTION']) : '';

	if ($TIME_START === '' || $TIME_END === '') {
		message("Time Start and Time End are required.", "error");
		redirect('index.php');
		return;
	}

	if (strtotime($TIME_END) <= strtotime($TIME_START)) {
		message("Time End must be later than Time Start.", "error");
		redirect('index.php');
		return;
	}

	$sql = "INSERT INTO `tblscheduletimes` (TIME_START, TIME_END, DESCRIPTION) VALUES (
			'" . $mydb->escape_value($TIME_START) . "',
			'" . $mydb->escape_value($TIME_END) . "',
			'" . $mydb->escape_value($DESCRIPTION) . "'
		)";
	$istrue = $mydb->InsertThis($sql);

	if ($istrue) {
		message("New schedule time has been added successfully!", "success");
	} else {
		message("No schedule time has been added.", "error");
	}
	redirect('index.php');
}

function doEdit(){
	global $mydb;
	if (isset($_POST['edit'])) {
		$SCHEDULETIME_ID = intval($_POST['SCHEDULETIME_ID']);
		$TIME_START      = trim($_POST['TIME_START1']);
		$TIME_END        = trim($_POST['TIME_END1']);
		$DESCRIPTION     = isset($_POST['DESCRIPTION1']) ? trim($_POST['DESCRIPTION1']) : '';

		if ($TIME_START === '' || $TIME_END === '') {
			message("Time Start and Time End are required.", "error");
			redirect('index.php');
			return;
		}

		if (strtotime($TIME_END) <= strtotime($TIME_START)) {
			message("Time End must be later than Time Start.", "error");
			redirect('index.php');
			return;
		}

		$sql = "UPDATE `tblscheduletimes` SET
				TIME_START  = '" . $mydb->escape_value($TIME_START) . "',
				TIME_END    = '" . $mydb->escape_value($TIME_END) . "',
				DESCRIPTION = '" . $mydb->escape_value($DESCRIPTION) . "'
			WHERE SCHEDULETIME_ID = " . $SCHEDULETIME_ID;
		$istrue = $mydb->InsertThis($sql);

		if ($istrue) {
			message("Schedule time has been updated successfully!", "success");
		} else {
			message("No schedule time has been updated.", "error");
		}
		redirect('index.php');
	}
}

function doDelete(){
	global $mydb;
	$id = intval($_GET['id']);

	// SANTE SOLUTIONS - tanggalin muna ang mga naka-link na Schedule
	// entries (module/schedule) na gumagamit ng Time na ito, bago ito
	// buo tanggalin, para walang naiiwang "orphan" na schedule record.
	$mydb->InsertThis("DELETE FROM `tblschedule` WHERE SCHEDULETIME_ID = " . $id);
	$mydb->InsertThis("DELETE FROM `tblscheduletimes` WHERE SCHEDULETIME_ID = " . $id);

	message("Schedule time has been deleted!", "success");
	redirect('index.php');
}
?>
