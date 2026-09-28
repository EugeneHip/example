<?php
/* SANTE SOLUTIONS - Section Schedule: pumili ng Section + Semester,
   tapos ilagay sa section ang mga subject (ng course ng section na
   iyon) na may Schedule na. Tingnan ang lib.php para sa buong paliwanag. */
global $mydb;

$ssSectionId = isset($_GET['section']) ? intval($_GET['section']) : 0;
$ssSem       = ss_clean_semester(isset($_GET['sem']) ? $_GET['sem'] : '');
$ssSection   = ss_load_section($ssSectionId);
$ssGroups    = ss_section_groups();
$ssLabel     = $ssSection ? ss_section_label($ssSection) : '';

$ssAvailable = array();
$ssAssigned  = array();
$ssTotal     = 0;
if ($ssSection) {
	$ssAvailable = ss_available($ssSection, $ssSem);
	$ssAssigned  = ss_assigned($ssSection, $ssSem);
	foreach ($ssAssigned as $a) { $ssTotal += intval($a->UNITS); }
}
?>
<section class="content">
  <div class="container-fluid">
    <?php check_message(); ?>

    <div class="card card-primary card-outline">
      <div class="card-header">
        <h3 class="card-title">Section Schedule</h3>
      </div>
      <div class="card-body">
        <form method="GET" action="index.php" class="form-row align-items-end">
          <div class="form-group col-md-5">
            <label for="ssSection" class="col-form-label-sm">Section</label>
            <select class="form-control form-control-sm" name="section" id="ssSection" onchange="this.form.submit()">
              <option value="">-- Select Section --</option>
              <?php foreach ($ssGroups as $ssGroupLabel => $ssGroupRows): ?>
                <?php if (count($ssGroupRows) == 0) { continue; } ?>
                <optgroup label="<?php echo htmlspecialchars($ssGroupLabel); ?>">
                <?php foreach ($ssGroupRows as $o): ?>
                  <option value="<?php echo $o->SECTION_ID; ?>" <?php echo ($o->SECTION_ID == $ssSectionId) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars(ss_section_label($o).($o->SCHOOL_YEAR ? ' (S.Y. '.$o->SCHOOL_YEAR.')' : '')); ?>
                  </option>
                <?php endforeach; ?>
                </optgroup>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group col-md-3">
            <label for="ssSem" class="col-form-label-sm">Semester</label>
            <select class="form-control form-control-sm" name="sem" id="ssSem" onchange="this.form.submit()">
              <?php foreach (ss_semesters() as $sm): ?>
                <option value="<?php echo $sm; ?>" <?php echo ($sm === $ssSem) ? 'selected' : ''; ?>><?php echo $sm; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <noscript><div class="form-group col-md-2"><button type="submit" class="btn btn-primary btn-sm">Load</button></div></noscript>
        </form>
      </div>
    </div>

    <?php if (!$ssSection): ?>
      <div class="alert alert-info">
        <i class="fa fa-info-circle mr-1"></i>
        Choose a section (for example <b>BSIT 1st Year - Section A</b>) to see the subjects of that course that already have a schedule.
        Schedules are created first in <b>Set Schedule</b>.
      </div>
    <?php else: ?>

    <div class="row">
      <!-- ================= Available (may schedule na, wala pa sa section) ================= -->
      <div class="col-lg-6">
        <div class="card card-info card-outline">
          <div class="card-header">
            <h3 class="card-title">Scheduled subjects for <?php echo htmlspecialchars($ssLabel); ?></h3>
            <div class="card-tools">
                <?php if (count($ssAvailable) > 0): ?>
                <form action="controller.php?action=addall" method="POST" class="d-inline">
                  <input type="hidden" name="SECTION_ID" value="<?php echo $ssSection->SECTION_ID; ?>">
                  <input type="hidden" name="SEM" value="<?php echo htmlspecialchars($ssSem); ?>">
                  <button type="submit" class="btn btn-info btn-sm"><i class="fa fa-plus-circle mr-1"></i>Add All</button>
                </form>
                <?php endif; ?>
              </div>
            <?php $ssAnyShared = false; foreach ($ssAvailable as $r) { if (!empty($r->SHARED_WITH)) { $ssAnyShared = true; break; } } ?>
            <?php if ($ssAnyShared): ?>
            <small class="text-muted d-block mt-2"><i class="fa fa-exclamation-triangle text-warning mr-1"></i>Rows marked <b>Also used by</b> are already taken by another section. "Add All" skips those - add them one by one and confirm if this section should really share that class session.</small>
            <?php endif; ?>
          </div>
          <div class="card-body table-responsive p-0">
            <table class="table table-sm table-striped mb-0">
              <thead>
                <tr><th>Code</th><th>Subject</th><th>Units</th><th>Day</th><th>Time</th><th>Room</th><th></th></tr>
              </thead>
              <tbody>
              <?php if (count($ssAvailable) == 0): ?>
                <tr><td colspan="7" class="text-center text-muted py-3">No more scheduled subjects to add for <?php echo htmlspecialchars($ssLabel.' / '.$ssSem); ?>. Set the schedule of a subject first in Set Schedule.</td></tr>
              <?php endif; ?>
              <?php foreach ($ssAvailable as $r): ?>
                <tr>
                  <td><?php echo htmlspecialchars($r->SUBJECT_CODE); ?></td>
                  <td><?php echo htmlspecialchars($r->SUBJECT_NAME); ?><?php echo ($r->SUBJECT_TYPE == 'Minor') ? ' <span class="badge badge-secondary">Minor</span>' : ''; ?>
                    <?php if (!empty($r->SHARED_WITH)): ?>
                      <br><span class="badge badge-warning" title="Already used by another section - adding it here will put this section in the same class session.">
                        <i class="fa fa-exclamation-triangle mr-1"></i>Also used by: <?php echo htmlspecialchars($r->SHARED_WITH); ?>
                      </span>
                    <?php endif; ?>
                  </td>
                  <td><?php echo intval($r->UNITS); ?></td>
                  <td><?php echo htmlspecialchars($r->DAY_NAME); ?></td>
                  <td class="text-nowrap"><?php echo htmlspecialchars(ss_time_range($r->TIME_START, $r->TIME_END)); ?></td>
                  <td><?php echo htmlspecialchars($r->CLASSROOM_NAME); ?></td>
                  <td>
                    <form action="controller.php?action=add" method="POST" class="d-inline ss-add-form"
                          <?php if (!empty($r->SHARED_WITH)): ?>
                          data-confirm="This Day/Time/Room (<?php echo htmlspecialchars($r->SUBJECT_CODE.' - '.$r->DAY_NAME.' '.ss_time_range($r->TIME_START, $r->TIME_END)); ?>) is already used by: <?php echo htmlspecialchars($r->SHARED_WITH); ?>.&#10;&#10;Adding it here too means this section will share/combine into the same class session. Continue?"
                          <?php endif; ?>>
                      <input type="hidden" name="SECTION_ID" value="<?php echo $ssSection->SECTION_ID; ?>">
                      <input type="hidden" name="SEM" value="<?php echo htmlspecialchars($ssSem); ?>">
                      <input type="hidden" name="SCHEDULE_ID" value="<?php echo $r->SCHEDULE_ID; ?>">
                      <button type="submit" class="btn <?php echo !empty($r->SHARED_WITH) ? 'btn-warning' : 'btn-success'; ?> btn-xs">
                        <i class="fa fa-plus"></i> Add<?php echo !empty($r->SHARED_WITH) ? ' (shared)' : ''; ?>
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ================= Naka-schedule na sa section ================= -->
      <div class="col-lg-6">
        <div class="card card-success card-outline">
          <div class="card-header">
            <h3 class="card-title">
                <?php echo htmlspecialchars($ssLabel); ?> schedule
              </h3>
            <div class="card-tools">
                <?php if (count($ssAssigned) > 0): ?>
                <a href="print.php?section=<?php echo $ssSection->SECTION_ID; ?>&sem=<?php echo urlencode($ssSem); ?>" target="_blank" class="btn btn-primary btn-sm"><i class="fa fa-print mr-1"></i>Print</a>
                <?php endif; ?>
              </div>
          </div>
          <div class="card-body table-responsive p-0">
            <table class="table table-sm table-striped mb-0">
              <thead>
                <tr><th>Time</th><th>Day</th><th>Code</th><th>Subject</th><th>Units</th><th>Room</th><th>Instructor</th><th></th></tr>
              </thead>
              <tbody>
              <?php if (count($ssAssigned) == 0): ?>
                <tr><td colspan="8" class="text-center text-muted py-3">Nothing added yet. Use the Add buttons on the left.</td></tr>
              <?php endif; ?>
              <?php foreach ($ssAssigned as $r): ?>
                <tr>
                  <td class="text-nowrap"><?php echo htmlspecialchars(ss_time_range($r->TIME_START, $r->TIME_END)); ?></td>
                  <td><?php echo htmlspecialchars($r->DAY_NAME); ?></td>
                  <td><?php echo htmlspecialchars($r->SUBJECT_CODE); ?></td>
                  <td><?php echo htmlspecialchars($r->SUBJECT_NAME); ?></td>
                  <td><?php echo intval($r->UNITS); ?></td>
                  <td><?php echo htmlspecialchars($r->CLASSROOM_NAME); ?></td>
                  <td><?php echo $r->INSTRUCTOR_NAME ? htmlspecialchars($r->INSTRUCTOR_NAME) : '<span class="text-muted">-</span>'; ?></td>
                  <td>
                    <form action="controller.php?action=remove" method="POST" class="d-inline" onsubmit="return confirm('Remove this subject from the section schedule?');">
                      <input type="hidden" name="SECTION_ID" value="<?php echo $ssSection->SECTION_ID; ?>">
                      <input type="hidden" name="SEM" value="<?php echo htmlspecialchars($ssSem); ?>">
                      <input type="hidden" name="SCHEDULE_ID" value="<?php echo $r->SCHEDULE_ID; ?>">
                      <button type="submit" class="btn btn-outline-danger btn-xs" title="Remove"><i class="fa fa-times"></i></button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
              </tbody>
              <?php if (count($ssAssigned) > 0): ?>
              <tfoot><tr><th colspan="4" class="text-right">Total Units</th><th><?php echo $ssTotal; ?></th><th colspan="3"></th></tr></tfoot>
              <?php endif; ?>
            </table>
          </div>
        </div>
      </div>
    </div>

    <?php endif; ?>
  </div>
</section>

<script type="text/javascript">
  // SANTE SOLUTIONS - kapag ang schedule na i-a-add ay ginagamit na
  // rin ng ibang section (may data-confirm), magpakita muna ng
  // babala bago talaga isubmit ang form.
  $(document).on('submit', '.ss-add-form', function(e){
    var msg = $(this).data('confirm');
    if (msg && !confirm(msg)) {
      e.preventDefault();
      return false;
    }
  });
</script>
