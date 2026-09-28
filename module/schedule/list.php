<?php
// SANTE SOLUTIONS - "Set Schedule" Module (module/schedule): dito
// pinagsasama ang Subject + Room (Classroom) + Day (module/scheduleday)
// + Time (module/scheduletime) + Instructor. Kunin muna ang mga
// listahan na gagamitin sa mga dropdown ng Add/Edit form.
$schedSubjects = array();
$mydb->setQuery("SELECT sub.SUBJECT_ID, sub.SUBJECT_CODE, sub.SUBJECT_NAME, sub.UNITS, sub.YEAR_LEVEL, sub.SEMESTER, sub.COURSE_ID, c.COURSE_CODE
	FROM `tblsubjects` sub LEFT JOIN `tblcourses` c ON c.COURSE_ID = sub.COURSE_ID
	ORDER BY c.COURSE_CODE ASC, sub.YEAR_LEVEL ASC, sub.SEMESTER ASC, sub.SUBJECT_CODE ASC");
foreach ($mydb->loadResultList() as $r) { $schedSubjects[] = $r; }

/* SANTE SOLUTIONS - hinahati ang Subject dropdown ayon sa Course (BSIT,
   BSTM, BSED...). Ang mga Minor/GE subject (walang Course) ay nasa
   "General Education / Minor". */
$schedSubjectGroups = array();
foreach ($schedSubjects as $r) {
	$key = !empty($r->COURSE_CODE) ? $r->COURSE_CODE : 'General Education / Minor';
	if (!isset($schedSubjectGroups[$key])) { $schedSubjectGroups[$key] = array(); }
	$schedSubjectGroups[$key][] = $r;
}
if (isset($schedSubjectGroups['General Education / Minor'])) {
	$ge = $schedSubjectGroups['General Education / Minor'];
	unset($schedSubjectGroups['General Education / Minor']);
	$schedSubjectGroups['General Education / Minor'] = $ge;
}
if (!function_exists('sched_subject_options')) {
	function sched_subject_options($groups) {
		$html = '';
		foreach ($groups as $label => $list) {
			if (count($list) === 0) { continue; }
			$html .= '<optgroup label="' . htmlspecialchars($label) . '">';
			foreach ($list as $sub) {
				$extra = ' (' . $sub->UNITS . ' units' . ($sub->YEAR_LEVEL ? ', ' . $sub->YEAR_LEVEL : '') . ($sub->SEMESTER ? ', ' . $sub->SEMESTER : '') . ')';
				$html .= '<option value="' . intval($sub->SUBJECT_ID) . '">'
					. htmlspecialchars($sub->SUBJECT_CODE . ' - ' . $sub->SUBJECT_NAME . $extra)
					. '</option>';
			}
			$html .= '</optgroup>';
		}
		return $html;
	}
}

$schedClassrooms = array();
$mydb->setQuery("SELECT CLASSROOM_ID, CLASSROOM_NAME FROM `tblclassrooms` WHERE STATUS = 'Active' ORDER BY CLASSROOM_NAME ASC");
foreach ($mydb->loadResultList() as $r) { $schedClassrooms[] = $r; }

$schedDays = array();
$mydb->setQuery("SELECT SCHEDULEDAY_ID, DAY_NAME FROM `tblscheduledays` ORDER BY DAY_NAME ASC");
foreach ($mydb->loadResultList() as $r) { $schedDays[] = $r; }

$schedTimes = array();
$mydb->setQuery("SELECT SCHEDULETIME_ID, TIME_START, TIME_END FROM `tblscheduletimes` ORDER BY TIME_START ASC");
foreach ($mydb->loadResultList() as $r) { $schedTimes[] = $r; }

// SANTE SOLUTIONS - listahan ng mga account na ang usertype (TYPE)
// ay "Instructor" - gagamitin bilang pagpipilian sa Instructor ng
// isang Schedule. Optional lang ito ("Not Assigned" kung wala pa).
$user = new User();
$schedInstructors = $user->listInstructors();
?>
<section class="content">

   <!-- SANTE SOLUTIONS -->

      <div class="container-fluid">
         <?php check_message(); ?>
        <div class="row">
          <div class="col-12">

            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Set Schedule List</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <?php if (!$mydb->tableExists('tblschedule') || !$mydb->tableExists('tblscheduledays') || !$mydb->tableExists('tblscheduletimes')): ?>
                <div class="alert alert-warning mb-3">
                  <i class="fa fa-exclamation-triangle mr-1"></i>
                  The Schedule tables were not found in the database.
                  Please run <code>database/migration_add_scheduling.sql</code> in phpMyAdmin first.
                </div>
                <?php endif; ?>
                <?php if (empty($schedDays)): ?>
                <div class="alert alert-info mb-3">
                  No Schedule Days yet. Please add one in the <a href="<?php echo WEB_ROOT; ?>module/scheduleday">Schedule Day</a> module first.
                </div>
                <?php endif; ?>
                <?php if (empty($schedTimes)): ?>
                <div class="alert alert-info mb-3">
                  No Schedule Times yet. Please add one in the <a href="<?php echo WEB_ROOT; ?>module/scheduletime">Schedule Time</a> module first.
                </div>
                <?php endif; ?>
                <?php if (empty($schedClassrooms)): ?>
                <div class="alert alert-info mb-3">
                  No Active Classrooms yet. Please add one in the <a href="<?php echo WEB_ROOT; ?>module/classroom">Classroom</a> module first.
                </div>
                <?php endif; ?>
                <table id="tblschedule" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th width="4%">#</th>
                    <th>Time</th>
                    <th>Day</th>
                    <th>Code</th>
                    <th>Subject Description</th>
                    <th>Course</th>
                    <th width="6%">Unit</th>
                    <th>Room</th>
                    <th>Instructor</th>
                    <th width="12%">Action</th>
                  </tr>
                  </thead>
                  <tbody>

                  </tbody>
                  <tfoot>

                  </tfoot>
                </table>
                  <div class="btn-group">

                  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#AddNewEntry">Add New</button>

                </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>

<!-----START of Add Form---->
     <div class="modal fade" id="AddNewEntry">
        <div class="modal-dialog modal-lg">
        <form action="controller.php?action=add" method="POST">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Add New Schedule</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="row">

                    <div class="col-sm-12">
                      <div class="form-group">
                       <label for="SUBJECT_ID" class="col-form-label col-form-label-sm">Subject</label>
                        <select class="form-control form-control-sm" name="SUBJECT_ID" id="SUBJECT_ID" required>
                          <option value="">-- Select Subject --</option>
                          <?php echo sched_subject_options($schedSubjectGroups); ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="form-group">
                       <label for="SCHEDULEDAY_ID" class="col-form-label col-form-label-sm">Day</label>
                        <select class="form-control form-control-sm" name="SCHEDULEDAY_ID" id="SCHEDULEDAY_ID" required>
                          <option value="">-- Select Day --</option>
                          <?php foreach ($schedDays as $d): ?>
                          <option value="<?php echo $d->SCHEDULEDAY_ID; ?>"><?php echo htmlspecialchars($d->DAY_NAME); ?></option>
                          <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">Manage Day options in the <a href="<?php echo WEB_ROOT; ?>module/scheduleday">Schedule Day</a> module.</small>
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="form-group">
                       <label for="SCHEDULETIME_ID" class="col-form-label col-form-label-sm">Time</label>
                        <select class="form-control form-control-sm" name="SCHEDULETIME_ID" id="SCHEDULETIME_ID" required>
                          <option value="">-- Select Time --</option>
                          <?php foreach ($schedTimes as $t): ?>
                          <option value="<?php echo $t->SCHEDULETIME_ID; ?>"><?php echo date('g:i A', strtotime($t->TIME_START)) . ' - ' . date('g:i A', strtotime($t->TIME_END)); ?></option>
                          <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">Manage Time options in the <a href="<?php echo WEB_ROOT; ?>module/scheduletime">Schedule Time</a> module.</small>
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="form-group">
                       <label for="CLASSROOM_ID" class="col-form-label col-form-label-sm">Room</label>
                        <select class="form-control form-control-sm" name="CLASSROOM_ID" id="CLASSROOM_ID" required>
                          <option value="">-- Select Room --</option>
                          <?php foreach ($schedClassrooms as $c): ?>
                          <option value="<?php echo $c->CLASSROOM_ID; ?>"><?php echo htmlspecialchars($c->CLASSROOM_NAME); ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="form-group">
                       <label for="INSTRUCTOR_ID" class="col-form-label col-form-label-sm">Instructor <span class="text-muted">(optional)</span></label>
                        <select class="form-control form-control-sm" name="INSTRUCTOR_ID" id="INSTRUCTOR_ID">
                          <option value="">-- Not Assigned --</option>
                          <?php foreach ($schedInstructors as $ins): ?>
                          <option value="<?php echo $ins->UID; ?>"><?php echo htmlspecialchars($ins->DISPLAYNAME); ?></option>
                          <?php endforeach; ?>
                        </select>
                        <?php if (empty($schedInstructors)): ?>
                        <small class="form-text text-muted">No accounts with the "Instructor" user type yet. Add one in the Users module first.</small>
                        <?php endif; ?>
                      </div>
                    </div>

                  </div>
            </div>
            <div class="modal-footer justify-content-between">
             <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                 <button type="submit" class="btn btn-primary" name="save" type="submit">Save changes</button>
            </div>
          </div>
          </form>
        </div>
      </div>
<!-----End of Add Form---->

<!-----Start of Edit Form---->
<div class="modal fade" id="editEntry">
        <div class="modal-dialog modal-lg">
        <form action="controller.php?action=edit" method="POST">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Modify Schedule</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="row">
                    <input type="hidden" name="SCHEDULE_ID" id="SCHEDULE_ID">
                    <div class="col-sm-12">
                      <div class="form-group">
                       <label for="SUBJECT_ID1" class="col-form-label col-form-label-sm">Subject</label>
                        <select class="form-control form-control-sm" name="SUBJECT_ID1" id="SUBJECT_ID1">
                          <option value="">-- Select Subject --</option>
                          <?php echo sched_subject_options($schedSubjectGroups); ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="form-group">
                       <label for="SCHEDULEDAY_ID1" class="col-form-label col-form-label-sm">Day</label>
                        <select class="form-control form-control-sm" name="SCHEDULEDAY_ID1" id="SCHEDULEDAY_ID1">
                          <option value="">-- Select Day --</option>
                          <?php foreach ($schedDays as $d): ?>
                          <option value="<?php echo $d->SCHEDULEDAY_ID; ?>"><?php echo htmlspecialchars($d->DAY_NAME); ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="form-group">
                       <label for="SCHEDULETIME_ID1" class="col-form-label col-form-label-sm">Time</label>
                        <select class="form-control form-control-sm" name="SCHEDULETIME_ID1" id="SCHEDULETIME_ID1">
                          <option value="">-- Select Time --</option>
                          <?php foreach ($schedTimes as $t): ?>
                          <option value="<?php echo $t->SCHEDULETIME_ID; ?>"><?php echo date('g:i A', strtotime($t->TIME_START)) . ' - ' . date('g:i A', strtotime($t->TIME_END)); ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="form-group">
                       <label for="CLASSROOM_ID1" class="col-form-label col-form-label-sm">Room</label>
                        <select class="form-control form-control-sm" name="CLASSROOM_ID1" id="CLASSROOM_ID1">
                          <option value="">-- Select Room --</option>
                          <?php foreach ($schedClassrooms as $c): ?>
                          <option value="<?php echo $c->CLASSROOM_ID; ?>"><?php echo htmlspecialchars($c->CLASSROOM_NAME); ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="form-group">
                       <label for="INSTRUCTOR_ID1" class="col-form-label col-form-label-sm">Instructor <span class="text-muted">(optional)</span></label>
                        <select class="form-control form-control-sm" name="INSTRUCTOR_ID1" id="INSTRUCTOR_ID1">
                          <option value="">-- Not Assigned --</option>
                          <?php foreach ($schedInstructors as $ins): ?>
                          <option value="<?php echo $ins->UID; ?>"><?php echo htmlspecialchars($ins->DISPLAYNAME); ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>

                  </div>
            </div>
            <div class="modal-footer justify-content-between">
             <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                 <button type="submit" class="btn btn-primary" name="edit" type="submit">Save changes</button>
            </div>
          </div>
          </form>
        </div>
      </div>
<!-----End of Edit Form---->
