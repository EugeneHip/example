<?php
/* SANTE SOLUTIONS - Section Schedule module (helpers).

   Daloy: pumili ng Section (hal. BSIT - 1st Year - Section A) at Semester -> lalabas LANG
   ang mga subject ng course ng section na iyon (hal. BSIT) na may
   Schedule na (galing sa Set Schedule module: Day + Time + Room +
   Instructor). I-click ang "Add" para ilagay sa section. Ang "Print"
   ay naglalabas ng Schedule of Classes ng section.

   Kasama rin ang Minor/GE subjects (walang COURSE_ID, bukas sa lahat
   ng course) na para sa parehong Year Level - kagaya ng pagkuha ng
   subjects sa Enrollment. Ang mga na-add na sa section ay nasa
   tblsectionschedule (SECTION_ID + SCHEDULE_ID). */

function ss_semesters() {
	return array('1st Semester', '2nd Semester', 'Summer');
}

function ss_clean_semester($sem) {
	$sem = trim((string)$sem);
	return in_array($sem, ss_semesters(), true) ? $sem : '1st Semester';
}

function ss_time_range($start, $end) {
	return date('g:i A', strtotime($start)).' - '.date('g:i A', strtotime($end));
}

/* SANTE SOLUTIONS - Pangalan ng section na ipinapakita sa dropdown, sa
   mga pamagat at sa print. Halimbawa: "BSIT 1st Year - Section A". */
function ss_section_label($s) {
	return $s->COURSE_CODE . ' ' . $s->YEAR_LEVEL . ' - Section ' . $s->SECTION_NAME;
}

function ss_load_section($sectionId) {
	global $mydb;
	$sectionId = intval($sectionId);
	if ($sectionId <= 0) { return null; }
	$mydb->setQuery("SELECT sec.SECTION_ID, sec.SECTION_NAME, sec.YEAR_LEVEL, sec.COURSE_ID, sec.SY_ID,
			c.COURSE_CODE, c.COURSE_NAME, sy.SCHOOL_YEAR
		FROM `tblsections` sec
		JOIN `tblcourses` c ON c.COURSE_ID = sec.COURSE_ID
		LEFT JOIN `tblschoolyear` sy ON sy.SY_ID = sec.SY_ID
		WHERE sec.SECTION_ID = '".$sectionId."' LIMIT 1");
	$rows = $mydb->loadResultList();
	return (count($rows) >= 1) ? $rows[0] : null;
}

function ss_section_options() {
	global $mydb;
	$mydb->setQuery("SELECT sec.SECTION_ID, sec.SECTION_NAME, sec.YEAR_LEVEL, sec.COURSE_ID, c.COURSE_CODE, c.COURSE_NAME, sy.SCHOOL_YEAR
		FROM `tblsections` sec
		JOIN `tblcourses` c ON c.COURSE_ID = sec.COURSE_ID
		LEFT JOIN `tblschoolyear` sy ON sy.SY_ID = sec.SY_ID
		WHERE c.STATUS = 'Active'
		ORDER BY c.COURSE_CODE ASC, sec.YEAR_LEVEL ASC, sec.SECTION_NAME ASC, sy.SCHOOL_YEAR DESC");
	return $mydb->loadResultList();
}

/* SANTE SOLUTIONS - hinahati ang mga section sa dropdown ayon sa Course
   (BSIT, BSTM, BSED...). Ibinabalik: [course code => [section rows]]. */
function ss_section_groups() {
	$groups = array();
	foreach (ss_section_options() as $o) {
		if (!isset($groups[$o->COURSE_CODE])) { $groups[$o->COURSE_CODE] = array(); }
		$groups[$o->COURSE_CODE][] = $o;
	}
	return $groups;
}

/* Base SELECT + JOINs ng isang schedule row. */
function ss_base_select() {
	return "SELECT sch.SCHEDULE_ID, sch.SUBJECT_ID, sch.SCHEDULEDAY_ID,
			sub.SUBJECT_CODE, sub.SUBJECT_NAME, sub.UNITS, sub.SUBJECT_TYPE, sub.SEMESTER,
			cl.CLASSROOM_NAME, d.DAY_NAME, t.TIME_START, t.TIME_END,
			u.DISPLAYNAME AS INSTRUCTOR_NAME
		FROM `tblschedule` sch
		JOIN `tblsubjects` sub ON sub.SUBJECT_ID = sch.SUBJECT_ID
		JOIN `tblclassrooms` cl ON cl.CLASSROOM_ID = sch.CLASSROOM_ID
		JOIN `tblscheduledays` d ON d.SCHEDULEDAY_ID = sch.SCHEDULEDAY_ID
		JOIN `tblscheduletimes` t ON t.SCHEDULETIME_ID = sch.SCHEDULETIME_ID
		LEFT JOIN `tblusers` u ON u.UID = sch.INSTRUCTOR_ID ";
}

/* Kondisyon: subject ng course ng section (o Minor na COURSE_ID NULL),
   para sa Year Level ng section, at sa napiling Semester. */
function ss_eligible_where($section, $sem) {
	global $mydb;
	return " (sub.COURSE_ID = '".intval($section->COURSE_ID)."' OR sub.COURSE_ID IS NULL)
		AND (sub.YEAR_LEVEL = '".$mydb->escape_value($section->YEAR_LEVEL)."' OR sub.YEAR_LEVEL IS NULL OR sub.YEAR_LEVEL = '')
		AND (sub.SEMESTER = '".$mydb->escape_value($sem)."' OR sub.SEMESTER IS NULL OR sub.SEMESTER = '') ";
}

/* Para sa listahan ng "Available" - alamin kung aling IBANG section
   (maliban dito) ang gumagamit na rin ng bawat SCHEDULE_ID. Ginagamit
   para may makitang babala/paalala BAGO i-add: "ginagamit na rin ito
   ni BSIT 1st Year - Section A" - baka gusto pa ring i-share/combine ng user
   ang parehong Room/Day/Time sa dalawang section, pero dapat malaman
   niya muna bago niya i-confirm. Ibinabalik: [SCHEDULE_ID => "BSIT 1st Year -
   Section A, BSIT 1st Year - Section B"]. */
function ss_other_sections_map($excludeSectionId) {
	global $mydb;
	$mydb->setQuery("SELECT ss.SCHEDULE_ID,
			GROUP_CONCAT(DISTINCT CONCAT(c.COURSE_CODE, ' ', sec.YEAR_LEVEL, ' - Section ', sec.SECTION_NAME) ORDER BY c.COURSE_CODE, sec.YEAR_LEVEL, sec.SECTION_NAME SEPARATOR ', ') AS SECTION_LABELS
		FROM `tblsectionschedule` ss
		JOIN `tblsections` sec ON sec.SECTION_ID = ss.SECTION_ID
		JOIN `tblcourses` c ON c.COURSE_ID = sec.COURSE_ID
		WHERE ss.SECTION_ID != '".intval($excludeSectionId)."'
		GROUP BY ss.SCHEDULE_ID");
	$map = array();
	foreach ($mydb->loadResultList() as $r) { $map[intval($r->SCHEDULE_ID)] = $r->SECTION_LABELS; }
	return $map;
}

/* Mga subject na may Schedule na at PWEDENG idagdag sa section, pero
   wala pa dito. Hindi na dito tinatanggal ang IBANG schedule row ng
   isang subject na may isa NANG naka-add (hal. Lecture) - kailangan
   pa rin itong lumabas para sa mga subject na may hiwalay na Lab
   meeting (ibang Day/Time/Room, parehong subject). Ang tanging
   hindi na dapat lumabas ay ang eksaktong SCHEDULE_ID na naka-add na.
   Bawat row ay may dagdag na SHARED_WITH - kung hindi blangko, ibig
   sabihin ay ginagamit na rin ng ibang section ang schedule na ito
   (tingnan ss_other_sections_map). */
function ss_available($section, $sem) {
	global $mydb;
	$mydb->setQuery(ss_base_select()."
		WHERE ".ss_eligible_where($section, $sem)."
		AND sch.SCHEDULE_ID NOT IN (SELECT SCHEDULE_ID FROM `tblsectionschedule` WHERE SECTION_ID = '".intval($section->SECTION_ID)."')
		ORDER BY sub.SUBJECT_TYPE ASC, sub.SUBJECT_CODE ASC, t.TIME_START ASC");
	$rows = $mydb->loadResultList();
	$sharedMap = ss_other_sections_map($section->SECTION_ID);
	foreach ($rows as $r) {
		$r->SHARED_WITH = isset($sharedMap[intval($r->SCHEDULE_ID)]) ? $sharedMap[intval($r->SCHEDULE_ID)] : '';
	}
	return $rows;
}

/* Mga schedule na nakalagay na sa section (para sa napiling Semester). */
function ss_assigned($section, $sem) {
	global $mydb;
	$mydb->setQuery(ss_base_select()."
		JOIN `tblsectionschedule` ss ON ss.SCHEDULE_ID = sch.SCHEDULE_ID
		WHERE ss.SECTION_ID = '".intval($section->SECTION_ID)."'
		AND (sub.SEMESTER = '".$mydb->escape_value($sem)."' OR sub.SEMESTER IS NULL OR sub.SEMESTER = '')
		ORDER BY t.TIME_START ASC, sub.SUBJECT_CODE ASC");
	return $mydb->loadResultList();
}

/* Subukang idagdag ang isang schedule sa section. Ibinabalik ang
   array('ok' => bool, 'msg' => string). Bawal kapag: hindi pwede sa
   section na ito, eksaktong schedule row na iyon ay naka-add na, o
   magtatapat ang Day at oras sa isa pang schedule (kahit ibang
   subject o iyon ding subject) na nasa section na. Pwede nang
   magkaroon ng dalawang magkaibang schedule row (hal. Lecture +
   Lab) ang IISANG subject, basta't magkaiba ang Day/Time nila. */
function ss_try_add($section, $scheduleId, $sem) {
	global $mydb;
	$scheduleId = intval($scheduleId);

	$mydb->setQuery(ss_base_select()."
		WHERE sch.SCHEDULE_ID = '".$scheduleId."' AND ".ss_eligible_where($section, $sem)." LIMIT 1");
	$rows = $mydb->loadResultList();
	if (count($rows) < 1) {
		return array('ok' => false, 'msg' => 'That schedule is not available for this section and semester.');
	}
	$new = $rows[0];

	$mydb->setQuery(ss_base_select()."
		JOIN `tblsectionschedule` ss ON ss.SCHEDULE_ID = sch.SCHEDULE_ID
		WHERE ss.SECTION_ID = '".intval($section->SECTION_ID)."'");
	foreach ($mydb->loadResultList() as $ex) {
		if (intval($ex->SCHEDULE_ID) === $scheduleId) {
			return array('ok' => false, 'msg' => $new->SUBJECT_CODE.' already has that schedule in this section.');
		}
		if (intval($ex->SCHEDULEDAY_ID) === intval($new->SCHEDULEDAY_ID)
			&& $new->TIME_START < $ex->TIME_END && $ex->TIME_START < $new->TIME_END) {
			return array('ok' => false, 'msg' => $new->SUBJECT_CODE.' overlaps with '.$ex->SUBJECT_CODE.' ('.$ex->DAY_NAME.' '.ss_time_range($ex->TIME_START, $ex->TIME_END).').');
		}
	}

	$ok = $mydb->InsertThis("INSERT INTO `tblsectionschedule` (SECTION_ID, SCHEDULE_ID)
		VALUES ('".intval($section->SECTION_ID)."', '".$scheduleId."')");
	return $ok ? array('ok' => true, 'msg' => '') : array('ok' => false, 'msg' => 'The schedule could not be added.');
}
?>
