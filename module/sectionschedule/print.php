<?php
require_once("../../include/initialize.php");
require_once("lib.php");
global $mydb;

if (!isset($_SESSION['UID'])) {
	redirect(WEB_ROOT."login.php");
	exit;
}

/* SANTE SOLUTIONS - Print "Schedule of Classes" ng isang Section.
   Kaparehong disenyo ng sample na binigay ng client: letterhead, SCHEDULE
   OF CLASSES, "FIRST SEMESTER A.Y. 2026-2027", "2ND YEAR-SET C" (asul),
   pulang banner ng program, at ang table: TIME / DAY / CODE / SUBJECT
   DESCRIPTION / UNITS / ROOM / INSTRUCTOR, may Total Units sa dulo. */

$section = ss_load_section(isset($_GET['section']) ? $_GET['section'] : 0);
$sem     = ss_clean_semester(isset($_GET['sem']) ? $_GET['sem'] : '');
$rows    = $section ? ss_assigned($section, $sem) : array();

$programCode = $section ? $section->COURSE_CODE : '';
$programName = $section ? $section->COURSE_NAME : '';

// "SET A" (asul na linya) at ang pulang banner ng program
$setName = $section ? strtoupper(trim($section->SECTION_NAME)) : '';
if ($setName !== '' && strpos($setName, 'SET ') !== 0) { $setName = 'SET '.$setName; }
$bannerText = $programName.' ('.$programCode.')';
$titleLine  = $section ? strtoupper($section->YEAR_LEVEL).'-'.$setName : '';

$totalUnits = 0;
foreach ($rows as $r) { $totalUnits += intval($r->UNITS); }
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Schedule of Classes<?php echo $section ? ' - '.htmlspecialchars(ss_section_label($section)) : ''; ?></title>
<style>
	body { font-family: "Cambria", "Georgia", serif; font-weight: bold; color: #222; margin: 0; background: #e9e9e9; }
	.sheet { width: 8.5in; min-height: 11in; box-sizing: border-box; margin: 12px auto; background: #fff; padding: 0.5in 0.55in; }
	.letterhead { display: flex; align-items: center; justify-content: center; gap: 14px; text-align: center; }
	.letterhead img { width: 70px; height: 70px; object-fit: contain; }
	.letterhead h2 { margin: 0; font-size: 20px; letter-spacing: 1px; font-style: italic; }
	.letterhead small { display: block; font-size: 12px; font-weight: normal; }
	.title { text-align: center; margin-top: 26px; }
	.title .t1 { font-size: 26px; text-decoration: underline; letter-spacing: 1px; }
	.title .t2 { font-size: 17px; margin-top: 4px; }
	.title .t3 { font-size: 26px; color: #1f6fb5; text-decoration: underline; margin-top: 4px; }
	.banner { background: #d9363e; color: #3a0d10; text-align: center; font-size: 17px; padding: 3px 6px; margin-top: 6px; text-transform: uppercase; }
	table.sched { width: 100%; border-collapse: collapse; font-size: 13px; }
	table.sched th { color: #c0392b; text-align: center; font-size: 13px; text-transform: uppercase; border: 1px solid #555; padding: 6px 4px; }
	table.sched td { border: 1px solid #555; padding: 6px 6px; text-align: center; vertical-align: top; text-transform: uppercase; }
	table.sched tr.bar td { background: #2b9be0; height: 14px; padding: 0; border-color: #2b9be0; }
	table.sched td.total { border: none; }
	table.sched td.units-total { border: 1px solid #555; }
	.empty { text-align: center; padding: 30px; font-weight: normal; }
	.actions { text-align: center; margin: 14px 0; }
	.btn-print { background: #1f6fb5; color: #fff; border: 0; padding: 9px 20px; font-size: 14px; border-radius: 4px; cursor: pointer; }
	.btn-close { background: #6c757d; color: #fff; border: 0; padding: 9px 20px; font-size: 14px; border-radius: 4px; cursor: pointer; margin-left: 6px; }
	@media print {
		body { background: #fff; }
		.sheet { margin: 0; width: auto; min-height: 0; padding: 0.3in 0.4in; }
		.actions { display: none; }
		table.sched th, table.sched td, .banner, table.sched tr.bar td { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
	}
</style>
</head>
<body>
<div class="actions">
	<button class="btn-print" onclick="window.print();">Print Schedule</button>
	<button class="btn-close" onclick="closeSchedule();">Close</button>
</div>
<script>
// Isara ang tab ng print. Kung hindi maisara ng browser, bumalik sa Section Schedule.
function closeSchedule() {
	window.close();
	setTimeout(function () {
		if (window.history.length > 1) { window.history.back(); }
		else { window.location.href = 'index.php'; }
	}, 200);
}
</script>
<div class="sheet">
<?php if (!$section): ?>
	<div class="empty">Section not found.</div>
<?php else: ?>
	<div class="letterhead">
		<img src="<?php echo WEB_ROOT.SCHOOL_LOGO; ?>" onerror="this.style.display='none';">
		<div>
			<h2><?php echo htmlspecialchars(SCHOOL_NAME); ?></h2>
			<small><?php echo htmlspecialchars(SCHOOL_ADDRESS); ?></small>
			<small>Tel. No.: <?php echo htmlspecialchars(SCHOOL_CONTACT); ?></small>
		</div>
	</div>

	<div class="title">
		<div class="t1">SCHEDULE OF CLASSES</div>
		<div class="t2"><?php echo htmlspecialchars(strtoupper($sem)); ?> A.Y. <?php echo htmlspecialchars((string)$section->SCHOOL_YEAR); ?></div>
		<div class="t3"><?php echo htmlspecialchars($titleLine); ?></div>
	</div>

	<div class="banner"><?php echo htmlspecialchars($bannerText); ?></div>
	<table class="sched">
		<thead>
			<tr>
				<th style="width:17%">Time</th><th style="width:9%">Day</th><th style="width:12%">Code</th>
				<th>Subject Description</th><th style="width:7%">Units</th><th style="width:12%">Room</th><th style="width:16%">Instructor</th>
			</tr>
		</thead>
		<tbody>
		<?php if (count($rows) == 0): ?>
			<tr><td colspan="7" class="empty">No schedule has been added to this section yet.</td></tr>
		<?php endif; ?>
		<?php $i = 0; foreach ($rows as $r): $i++; ?>
			<?php if ($i > 1): ?><tr class="bar"><td colspan="7"></td></tr><?php endif; ?>
			<tr>
				<td><?php echo htmlspecialchars(ss_time_range($r->TIME_START, $r->TIME_END)); ?></td>
				<td><?php echo htmlspecialchars($r->DAY_NAME); ?></td>
				<td><?php echo htmlspecialchars($r->SUBJECT_CODE); ?></td>
				<td><?php echo htmlspecialchars($r->SUBJECT_NAME); ?></td>
				<td><?php echo intval($r->UNITS); ?></td>
				<td><?php echo htmlspecialchars($r->CLASSROOM_NAME); ?></td>
				<td><?php echo $r->INSTRUCTOR_NAME ? htmlspecialchars($r->INSTRUCTOR_NAME) : ''; ?></td>
			</tr>
		<?php endforeach; ?>
		<?php if (count($rows) > 0): ?>
			<tr>
				<td colspan="4" class="total"></td>
				<td class="units-total"><?php echo $totalUnits; ?></td>
				<td colspan="2" class="total"></td>
			</tr>
		<?php endif; ?>
		</tbody>
	</table>
<?php endif; ?>
</div>
</body>
</html>
