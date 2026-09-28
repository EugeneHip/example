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
	$CLASSROOM_NAME = trim($_POST['CLASSROOM_NAME']);
	$DESCRIPTION    = isset($_POST['DESCRIPTION']) ? trim($_POST['DESCRIPTION']) : '';
	$STATUS         = (isset($_POST['STATUS']) && $_POST['STATUS'] == 'Inactive') ? 'Inactive' : 'Active';

	if ($CLASSROOM_NAME === '') {
		message("Classroom name is required.", "error");
		redirect('index.php');
		return;
	}

	$mydb->setQuery("SELECT CLASSROOM_ID FROM `tblclassrooms` WHERE CLASSROOM_NAME = '" . $mydb->escape_value($CLASSROOM_NAME) . "' LIMIT 1");
	if ($mydb->num_rows() >= 1) {
		message("A classroom with that name already exists!", "error");
		redirect('index.php');
		return;
	}

	$sql = "INSERT INTO `tblclassrooms` (CLASSROOM_NAME, DESCRIPTION, STATUS) VALUES (
			'" . $mydb->escape_value($CLASSROOM_NAME) . "',
			'" . $mydb->escape_value($DESCRIPTION) . "',
			'" . $STATUS . "'
		)";
	$istrue = $mydb->InsertThis($sql);

	if ($istrue) {
		message("New classroom [" . $CLASSROOM_NAME . "] has been added successfully!", "success");
	} else {
		message("No classroom has been added.", "error");
	}
	redirect('index.php');
}

function doEdit(){
	global $mydb;
	if (isset($_POST['edit'])) {
		$CLASSROOM_ID   = intval($_POST['CLASSROOM_ID']);
		$CLASSROOM_NAME = trim($_POST['CLASSROOM_NAME1']);
		$DESCRIPTION    = isset($_POST['DESCRIPTION1']) ? trim($_POST['DESCRIPTION1']) : '';
		$STATUS         = (isset($_POST['STATUS1']) && $_POST['STATUS1'] == 'Inactive') ? 'Inactive' : 'Active';

		if ($CLASSROOM_NAME === '') {
			message("Classroom name is required.", "error");
			redirect('index.php');
			return;
		}

		$sql = "UPDATE `tblclassrooms` SET
				CLASSROOM_NAME = '" . $mydb->escape_value($CLASSROOM_NAME) . "',
				DESCRIPTION    = '" . $mydb->escape_value($DESCRIPTION) . "',
				STATUS         = '" . $STATUS . "'
			WHERE CLASSROOM_ID = " . $CLASSROOM_ID;
		$istrue = $mydb->InsertThis($sql);

		if ($istrue) {
			message("Classroom [" . $CLASSROOM_NAME . "] has been updated successfully!", "success");
		} else {
			message("No classroom has been updated.", "error");
		}
		redirect('index.php');
	}
}

function doDelete(){
	global $mydb;
	$id = intval($_GET['id']);

	// SANTE SOLUTIONS - tanggalin muna ang mga naka-link na Schedule
	// entries (module/schedule) ng classroom na ito, bago ito buo
	// tanggalin, para walang naiiwang "orphan" na schedule record.
	$mydb->InsertThis("DELETE FROM `tblschedule` WHERE CLASSROOM_ID = " . $id);
	$mydb->InsertThis("DELETE FROM `tblclassrooms` WHERE CLASSROOM_ID = " . $id);

	message("Classroom has been deleted!", "success");
	redirect('index.php');
}
?>
