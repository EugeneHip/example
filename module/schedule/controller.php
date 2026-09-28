<?php

require_once("../../include/initialize.php");
global $mydb;

/* SANTE SOLUTIONS - "Set Schedule" Module (module/schedule).
   Dito pinagsasama ang Subject + Room (Classroom) + Day
   (tblscheduledays) + Time (tblscheduletimes) + Instructor
   (tblusers, TYPE = 'Instructor'). May proteksyon laban sa
   double-booking: hindi pwedeng magka-tumpang (overlap) ang oras ng
   dalawang schedule sa iisang Room, sa parehong Day. Kung may
   Instructor na napili, hindi rin pwedeng magka-tumpang ang oras ng
   dalawang schedule ng iisang Instructor (kahit magkaiba ang Room). */

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

// Kunin ang TIME_START/TIME_END (actual na oras) ng isang
// SCHEDULETIME_ID, gagamitin sa pag-check ng overlap.
function getScheduleTimeRange($scheduleTimeId) {
	global $mydb;
	$mydb->setQuery("SELECT TIME_START, TIME_END FROM `tblscheduletimes` WHERE SCHEDULETIME_ID = " . intval($scheduleTimeId) . " LIMIT 1");
	$rows = $mydb->loadResultList();
	if (count($rows) < 1) {
		return null;
	}
	return $rows[0];
}

// Tingnan kung may existing schedule na nagta-tumpang (overlap) sa
// parehong Room (CLASSROOM_ID) at Day (SCHEDULEDAY_ID), sa
// magkatumbas na oras. $excludeId ang SCHEDULE_ID na dapat huwag
// isama sa check (ginagamit sa Edit, para hindi laban sa sarili
// niyang record).
function roomHasConflict($classroomId, $scheduledayId, $timeStart, $timeEnd, $excludeId = 0) {
	global $mydb;
	$sql = "SELECT s.SCHEDULE_ID FROM `tblschedule` s
			INNER JOIN `tblscheduletimes` t ON t.SCHEDULETIME_ID = s.SCHEDULETIME_ID
			WHERE s.CLASSROOM_ID = " . intval($classroomId) . "
			AND s.SCHEDULEDAY_ID = " . intval($scheduledayId) . "
			AND t.TIME_START < '" . $mydb->escape_value($timeEnd) . "'
			AND t.TIME_END > '" . $mydb->escape_value($timeStart) . "'";
	if ($excludeId > 0) {
		$sql .= " AND s.SCHEDULE_ID != " . intval($excludeId);
	}
	$mydb->setQuery($sql);
	return $mydb->num_rows() >= 1;
}

// Tingnan kung may existing schedule na nagta-tumpang (overlap) sa
// parehong Instructor, sa magkatumbas na Day at oras (kahit
// magkaiba ang Room) - para hindi ma-double-book ang isang guro.
function instructorHasConflict($instructorId, $scheduledayId, $timeStart, $timeEnd, $excludeId = 0) {
	global $mydb;
	if (empty($instructorId)) {
		return false;
	}
	$sql = "SELECT s.SCHEDULE_ID FROM `tblschedule` s
			INNER JOIN `tblscheduletimes` t ON t.SCHEDULETIME_ID = s.SCHEDULETIME_ID
			WHERE s.INSTRUCTOR_ID = " . intval($instructorId) . "
			AND s.SCHEDULEDAY_ID = " . intval($scheduledayId) . "
			AND t.TIME_START < '" . $mydb->escape_value($timeEnd) . "'
			AND t.TIME_END > '" . $mydb->escape_value($timeStart) . "'";
	if ($excludeId > 0) {
		$sql .= " AND s.SCHEDULE_ID != " . intval($excludeId);
	}
	$mydb->setQuery($sql);
	return $mydb->num_rows() >= 1;
}

function doInsert(){
	global $mydb;
	$SUBJECT_ID      = isset($_POST['SUBJECT_ID']) ? intval($_POST['SUBJECT_ID']) : 0;
	$CLASSROOM_ID    = isset($_POST['CLASSROOM_ID']) ? intval($_POST['CLASSROOM_ID']) : 0;
	$SCHEDULEDAY_ID  = isset($_POST['SCHEDULEDAY_ID']) ? intval($_POST['SCHEDULEDAY_ID']) : 0;
	$SCHEDULETIME_ID = isset($_POST['SCHEDULETIME_ID']) ? intval($_POST['SCHEDULETIME_ID']) : 0;
	$INSTRUCTOR_ID   = (!empty($_POST['INSTRUCTOR_ID'])) ? intval($_POST['INSTRUCTOR_ID']) : null;

	if ($SUBJECT_ID <= 0 || $CLASSROOM_ID <= 0 || $SCHEDULEDAY_ID <= 0 || $SCHEDULETIME_ID <= 0) {
		message("Subject, Room, Day, and Time are all required.", "error");
		redirect('index.php');
		return;
	}

	$range = getScheduleTimeRange($SCHEDULETIME_ID);
	if ($range === null) {
		message("The selected Time was not found. Please try again.", "error");
		redirect('index.php');
		return;
	}

	// SANTE SOLUTIONS - hindi gagana / hindi papayagan kung may
	// kumuha na ng Room na iyon sa parehong Day/Time.
	if (roomHasConflict($CLASSROOM_ID, $SCHEDULEDAY_ID, $range->TIME_START, $range->TIME_END)) {
		message("This room is already booked at that Day/Time. Please choose a different Room, Day, or Time.", "error");
		redirect('index.php');
		return;
	}

	if ($INSTRUCTOR_ID !== null && instructorHasConflict($INSTRUCTOR_ID, $SCHEDULEDAY_ID, $range->TIME_START, $range->TIME_END)) {
		message("This instructor is already scheduled at that Day/Time in another room. Please choose a different Instructor, Day, or Time.", "error");
		redirect('index.php');
		return;
	}

	$sql = "INSERT INTO `tblschedule`
			(SUBJECT_ID, CLASSROOM_ID, SCHEDULEDAY_ID, SCHEDULETIME_ID, INSTRUCTOR_ID)
			VALUES (
				" . $SUBJECT_ID . ",
				" . $CLASSROOM_ID . ",
				" . $SCHEDULEDAY_ID . ",
				" . $SCHEDULETIME_ID . ",
				" . ($INSTRUCTOR_ID !== null ? $INSTRUCTOR_ID : "NULL") . "
			)";
	$istrue = $mydb->InsertThis($sql);

	if ($istrue) {
		message("New schedule has been added successfully!", "success");
	} else {
		message("No schedule has been added.", "error");
	}
	redirect('index.php');
}

function doEdit(){
	global $mydb;
	if (isset($_POST['edit'])) {
		$SCHEDULE_ID     = intval($_POST['SCHEDULE_ID']);
		$SUBJECT_ID      = isset($_POST['SUBJECT_ID1']) ? intval($_POST['SUBJECT_ID1']) : 0;
		$CLASSROOM_ID    = isset($_POST['CLASSROOM_ID1']) ? intval($_POST['CLASSROOM_ID1']) : 0;
		$SCHEDULEDAY_ID  = isset($_POST['SCHEDULEDAY_ID1']) ? intval($_POST['SCHEDULEDAY_ID1']) : 0;
		$SCHEDULETIME_ID = isset($_POST['SCHEDULETIME_ID1']) ? intval($_POST['SCHEDULETIME_ID1']) : 0;
		$INSTRUCTOR_ID   = (!empty($_POST['INSTRUCTOR_ID1'])) ? intval($_POST['INSTRUCTOR_ID1']) : null;

		if ($SUBJECT_ID <= 0 || $CLASSROOM_ID <= 0 || $SCHEDULEDAY_ID <= 0 || $SCHEDULETIME_ID <= 0) {
			message("Subject, Room, Day, and Time are all required.", "error");
			redirect('index.php');
			return;
		}

		$range = getScheduleTimeRange($SCHEDULETIME_ID);
		if ($range === null) {
			message("The selected Time was not found. Please try again.", "error");
			redirect('index.php');
			return;
		}

		if (roomHasConflict($CLASSROOM_ID, $SCHEDULEDAY_ID, $range->TIME_START, $range->TIME_END, $SCHEDULE_ID)) {
			message("This room is already booked at that Day/Time. Please choose a different Room, Day, or Time.", "error");
			redirect('index.php');
			return;
		}

		if ($INSTRUCTOR_ID !== null && instructorHasConflict($INSTRUCTOR_ID, $SCHEDULEDAY_ID, $range->TIME_START, $range->TIME_END, $SCHEDULE_ID)) {
			message("This instructor is already scheduled at that Day/Time in another room. Please choose a different Instructor, Day, or Time.", "error");
			redirect('index.php');
			return;
		}

		$sql = "UPDATE `tblschedule` SET
				SUBJECT_ID      = " . $SUBJECT_ID . ",
				CLASSROOM_ID    = " . $CLASSROOM_ID . ",
				SCHEDULEDAY_ID  = " . $SCHEDULEDAY_ID . ",
				SCHEDULETIME_ID = " . $SCHEDULETIME_ID . ",
				INSTRUCTOR_ID   = " . ($INSTRUCTOR_ID !== null ? $INSTRUCTOR_ID : "NULL") . "
			WHERE SCHEDULE_ID = " . $SCHEDULE_ID;
		$istrue = $mydb->InsertThis($sql);

		if ($istrue) {
			message("Schedule has been updated successfully!", "success");
		} else {
			message("No schedule has been updated.", "error");
		}
		redirect('index.php');
	}
}

function doDelete(){
	global $mydb;
	$id = intval($_GET['id']);
	$mydb->InsertThis("DELETE FROM `tblschedule` WHERE SCHEDULE_ID = " . $id);

	message("Schedule has been deleted!", "success");
	redirect('index.php');
}
?>
