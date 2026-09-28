<?php

global $mydb;

/* HIPANAO SOLUTIONS - "History Enrollment": mga enrollment record na
   TAPOS na ang saysay dito sa kasalukuyan -
     - "Completed": naunang term na naipalit dahil nag-"Register Again"
       + nag-Enroll ulit ang estudyante sa bagong term (tingnan ang
       markAsEnrolled() sa include/functions.php)
     - "Dropped": dinrop mula sa Enrollment Details (doDropEnrollment)
   Dito na sila makikita sa halip na sa Enrollment Details, na "Enroll"
   status lang ang ipinapakita. May filter ito "by year" (School Year)
   at "anong semester" (Semester), dahil isang estudyante ay maaaring
   magkaroon ng maraming history record sa iba't ibang taon/semester. */
$hySchoolYears = array();
$mydb->setQuery("SELECT SY_ID, SCHOOL_YEAR FROM `tblschoolyear` ORDER BY SCHOOL_YEAR DESC");
foreach ($mydb->loadResultList() as $r) { $hySchoolYears[] = $r; }

$hySemesters = array('1st Semester', '2nd Semester', 'Summer');
?>
<section class="content">
  <div class="container-fluid">
    <?php check_message(); ?>

    <div class="row">
      <div class="col-12">

        <div class="card card-secondary card-outline">
          <div class="card-header">
            <div class="hipanao-toolbar">
              <div>
                <h3 class="card-title mb-0"><i class="fa fa-history mr-1 text-muted"></i> History Enrollment</h3>
              </div>
              <div class="hipanao-toolbar-actions">
                <div class="form-inline">
                  <label class="mr-2 mb-0 small text-muted" for="HY_SY_FILTER">By Year</label>
                  <select class="form-control form-control-sm mr-3" id="HY_SY_FILTER" style="width:160px;">
                    <option value="">All</option>
                    <?php foreach ($hySchoolYears as $sy) { ?>
                    <option value="<?php echo $sy->SY_ID; ?>"><?php echo htmlspecialchars($sy->SCHOOL_YEAR); ?></option>
                    <?php } ?>
                  </select>

                  <label class="mr-2 mb-0 small text-muted" for="HY_SEM_FILTER">Semester</label>
                  <select class="form-control form-control-sm" id="HY_SEM_FILTER" style="width:160px;">
                    <option value="">All</option>
                    <?php foreach ($hySemesters as $sem) { ?>
                    <option value="<?php echo $sem; ?>"><?php echo $sem; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
            </div>
          </div>
          <div class="card-body">
            <?php if (!$mydb->tableExists('tblhistoryenrollment')): ?>
            <div class="alert alert-warning mb-3">
              <i class="fa fa-exclamation-triangle mr-1"></i>
              Table <code>tblhistoryenrollment</code> was not found in the database.
              The SQL file may not have been fully imported yet. The rest of the system will still work normally.
            </div>
            <?php endif; ?>
            <table id="tblhistoryenrollment" class="table table-bordered table-striped" style="width:100%">
              <thead>
                <tr>
                  <th>#</th>
                  <th>ID No.</th>
                  <th>Name</th>
                  <th>Course</th>
                  <th>Year Level</th>
                  <th>School Year</th>
                  <th>Semester</th>
                  <th>Status</th>
                  <th>Subjects</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>

<!-- =================================================================
     VIEW (HISTORY) - HIPANAO SOLUTIONS.
     Kaparehong porma ng View Profile sa Enrollment Details, may dagdag
     na Grade/Remarks column sa Subjects table dahil "history" na ang
     mga record dito (may resulta na dapat). Read-only lang - walang
     Edit/Drop/Register dito, dahil tapos na ang term na ito.
     ================================================================= -->
<div class="modal fade" id="viewHistoryModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><i class="fa fa-id-card text-secondary"></i> &nbsp;Enrollment History Profile</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

        <div class="row">
          <div class="col-md-3">
            <div class="card card-secondary card-outline mb-3">
              <div class="card-body box-profile text-center">
                <img id="HV_PICTURE"
                     class="profile-user-img img-fluid img-circle"
                     src="<?php echo WEB_ROOT; ?>module/student/image/1.png"
                     alt="Student photo">
                <h3 class="profile-username text-center mt-2 mb-0" id="HV_NAME_TEXT" style="font-size:1rem;">-</h3>
                <p class="text-muted text-center mb-0">ID No: <span id="HV_IDNO_TEXT">-</span></p>
                <p class="text-center mb-0"><span id="HV_STATUS_TEXT"></span></p>
              </div>
            </div>
            <div class="card card-secondary mb-0">
              <div class="card-header p-2">
                <h6 class="mb-0 pl-1">Enrollment Term</h6>
              </div>
              <div class="card-body p-2">
                <p class="mb-1"><i class="fas fa-book mr-1 text-muted"></i> <span id="HV_COURSE_TEXT">-</span></p>
                <p class="mb-1"><i class="fas fa-users mr-1 text-muted"></i> <span id="HV_SECTION_TEXT">-</span></p>
                <p class="mb-1"><i class="fas fa-layer-group mr-1 text-muted"></i> <span id="HV_YEAR_TEXT">-</span></p>
                <p class="mb-0"><i class="fas fa-calendar mr-1 text-muted"></i> <span id="HV_TERM_TEXT">-</span></p>
              </div>
            </div>
          </div>

          <div class="col-md-9">
            <div class="card card-outline mb-3">
              <div class="card-header p-2"><h6 class="mb-0 pl-1">Profile Info</h6></div>
              <div class="card-body p-2">
                <table class="table table-bordered table-sm mb-0">
                  <tr><th style="width:30%">Gender</th><td id="HV_SEX">-</td></tr>
                  <tr><th>Birthday</th><td id="HV_BDAY">-</td></tr>
                  <tr><th>Birth Place</th><td id="HV_BPLACE">-</td></tr>
                  <tr><th>Age</th><td id="HV_AGE">-</td></tr>
                  <tr><th>Nationality</th><td id="HV_NATIONALITY">-</td></tr>
                  <tr><th>Religion</th><td id="HV_RELIGION">-</td></tr>
                </table>
              </div>
            </div>

            <div class="card card-outline mb-3">
              <div class="card-header p-2"><h6 class="mb-0 pl-1">Contact Info</h6></div>
              <div class="card-body p-2">
                <table class="table table-bordered table-sm mb-0">
                  <tr><th style="width:30%">Contact Number</th><td id="HV_CONTACT">-</td></tr>
                  <tr><th>Email</th><td id="HV_EMAIL">-</td></tr>
                  <tr><th>Home Address</th><td id="HV_ADDRESS">-</td></tr>
                </table>
              </div>
            </div>

            <div class="card card-outline mb-0">
              <div class="card-header p-2"><h6 class="mb-0 pl-1">Subjects Taken &amp; Grades</h6></div>
              <div class="card-body p-2" style="max-height:260px; overflow-y:auto;">
                <table class="table table-bordered table-sm mb-0">
                  <thead class="thead-light">
                    <tr>
                      <th>Code</th>
                      <th>Subject</th>
                      <th>Type</th>
                      <th>Units</th>
                      <th>Grade</th>
                      <th>Remarks</th>
                    </tr>
                  </thead>
                  <tbody id="HV_SUBJECTS_TBODY">
                    <tr><td colspan="6" class="text-muted text-center">Loading...</td></tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
