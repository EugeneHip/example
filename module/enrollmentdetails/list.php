<?php

global $mydb;

/* HIPANAO SOLUTIONS - Dropdown data para sa bagong Edit at Register
   Again modals dito sa Enrollment Details - kaparehong listahan ng
   module/enrollment/list.php para consistent ang mga choices. */
$edSchoolYears = array();
$mydb->setQuery("SELECT SY_ID, SCHOOL_YEAR, STATUS FROM `tblschoolyear` ORDER BY SCHOOL_YEAR DESC");
foreach ($mydb->loadResultList() as $r) { $edSchoolYears[] = $r; }

$edCourses = array();
$mydb->setQuery("SELECT COURSE_ID, COURSE_CODE, COURSE_NAME FROM `tblcourses` ORDER BY COURSE_CODE ASC");
foreach ($mydb->loadResultList() as $r) { $edCourses[] = $r; }

$edYearLevels = array('1st Year', '2nd Year', '3rd Year', '4th Year');
$edSemesters  = array('1st Semester', '2nd Semester', 'Summer');
$edCategories = array('New', 'Old', 'Transferee', 'Returnee', 'Shiftee');

/* Dito lang naman gumagalaw ang mga estudyanteng "Enroll" na, kaya ang
   Status sa Edit modal ay limitado na lang sa mga katayuang may
   saysay sa hakbang na ito - manatiling Enroll, ma-Drop, o maging
   Completed ang term. */
$edStatuses = array('Enroll', 'Dropped', 'Completed');
?>
<section class="content">
  <div class="container-fluid">
    <?php check_message(); ?>

    <div class="row">
      <div class="col-12">

        <div class="card card-info card-outline">
          <div class="card-header">
            <div class="hipanao-toolbar">
              <div>
                <h3 class="card-title mb-0"><i class="fa fa-list-alt mr-1 text-muted"></i> List of Enrollment Details</h3>
              </div>
            </div>
          </div>
          <div class="card-body">
            <?php if (!$mydb->tableExists('tblenrollmentdetails')): ?>
            <div class="alert alert-warning mb-3">
              <i class="fa fa-exclamation-triangle mr-1"></i>
              Table <code>tblenrollmentdetails</code> was not found in the database.
              The SQL file may not have been fully imported yet. The rest of the system will still work normally.
            </div>
            <?php endif; ?>
            <table id="tblenrollmentdetails" class="table table-bordered table-striped" style="width:100%">
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
     VIEW PROFILE - HIPANAO SOLUTIONS.
     Buong detalye ng estudyante at ng enrollment na ito, dito na lang
     sa loob ng page (modal) - hindi na papunta pa sa Student module.
     ================================================================= -->
<div class="modal fade" id="viewProfileModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><i class="fa fa-id-card text-info"></i> &nbsp;Student &amp; Enrollment Profile</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

        <div class="row">
          <div class="col-md-3">
            <div class="card card-primary card-outline mb-3">
              <div class="card-body box-profile text-center">
                <img id="VP_PICTURE"
                     class="profile-user-img img-fluid img-circle"
                     src="<?php echo WEB_ROOT; ?>module/student/image/1.png"
                     alt="Student photo">
                <h3 class="profile-username text-center mt-2 mb-0" id="VP_NAME_TEXT" style="font-size:1rem;">-</h3>
                <p class="text-muted text-center mb-0">ID No: <span id="VP_IDNO_TEXT">-</span></p>
                <p class="text-center mb-0"><span id="VP_STATUS_TEXT"></span></p>
              </div>
            </div>
            <div class="card card-primary mb-0">
              <div class="card-header p-2">
                <h6 class="mb-0 pl-1">Current Enrollment</h6>
              </div>
              <div class="card-body p-2">
                <p class="mb-1"><i class="fas fa-book mr-1 text-muted"></i> <span id="VP_COURSE_TEXT">-</span></p>
                <p class="mb-1"><i class="fas fa-users mr-1 text-muted"></i> <span id="VP_SECTION_TEXT">-</span></p>
                <p class="mb-1"><i class="fas fa-layer-group mr-1 text-muted"></i> <span id="VP_YEAR_TEXT">-</span></p>
                <p class="mb-0"><i class="fas fa-calendar mr-1 text-muted"></i> <span id="VP_TERM_TEXT">-</span></p>
              </div>
            </div>
          </div>

          <div class="col-md-9">
            <div class="card card-outline mb-3">
              <div class="card-header p-2"><h6 class="mb-0 pl-1">Profile Info</h6></div>
              <div class="card-body p-2">
                <table class="table table-bordered table-sm mb-0">
                  <tr><th style="width:30%">Gender</th><td id="VP_SEX">-</td></tr>
                  <tr><th>Birthday</th><td id="VP_BDAY">-</td></tr>
                  <tr><th>Birth Place</th><td id="VP_BPLACE">-</td></tr>
                  <tr><th>Age</th><td id="VP_AGE">-</td></tr>
                  <tr><th>Nationality</th><td id="VP_NATIONALITY">-</td></tr>
                  <tr><th>Religion</th><td id="VP_RELIGION">-</td></tr>
                </table>
              </div>
            </div>

            <div class="card card-outline mb-3">
              <div class="card-header p-2"><h6 class="mb-0 pl-1">Contact Info</h6></div>
              <div class="card-body p-2">
                <table class="table table-bordered table-sm mb-0">
                  <tr><th style="width:30%">Contact Number</th><td id="VP_CONTACT">-</td></tr>
                  <tr><th>Email</th><td id="VP_EMAIL">-</td></tr>
                  <tr><th>Home Address</th><td id="VP_ADDRESS">-</td></tr>
                </table>
              </div>
            </div>

            <div class="card card-outline mb-0">
              <div class="card-header p-2"><h6 class="mb-0 pl-1">Subjects Taken</h6></div>
              <div class="card-body p-2" style="max-height:260px; overflow-y:auto;">
                <table class="table table-bordered table-sm mb-0">
                  <thead class="thead-light">
                    <tr><th>Code</th><th>Subject</th><th>Type</th><th>Units</th><th>Grade</th></tr>
                  </thead>
                  <tbody id="VP_SUBJECTS_TBODY">
                    <tr><td colspan="5" class="text-muted text-center">Loading...</td></tr>
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

<!-- =================================================================
     MANAGE SUBJECTS - HIPANAO SOLUTIONS.
     Kaparehong checklist mekanismo ng Enrollment > Subjects (iisang
     saveEnrollmentSubjects() ang ginagamit) - dito na lang direkta
     magagawa, hindi na kailangang pumunta pa sa Enrollment module.
     ================================================================= -->
<div class="modal fade" id="manageSubjectsModal">
  <div class="modal-dialog modal-lg">
    <form action="controller.php?action=subjects" method="POST" id="manageSubjectsForm">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"><i class="fa fa-book text-warning"></i> &nbsp;Manage Subjects</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">

          <input type="hidden" name="MS_EID" id="MS_EID" value="">

          <div class="row">
            <div class="col-md-3">
              <div class="card card-primary card-outline mb-0">
                <div class="card-body box-profile text-center">
                  <img id="MS_PICTURE"
                       class="profile-user-img img-fluid img-circle"
                       src="<?php echo WEB_ROOT; ?>module/student/image/1.png"
                       alt="Student photo">
                  <h3 class="profile-username text-center mt-2 mb-0" id="MS_IDNO_TEXT" style="font-size:1rem;">-</h3>
                  <p class="text-muted text-center mb-0" id="MS_NAME_TEXT">-</p>
                  <p class="text-muted text-center mb-0"><small id="MS_COURSE_TEXT">-</small></p>
                  <p class="text-center mb-0"><span class="badge badge-secondary" id="MS_STATUS_TEXT">-</span></p>
                </div>
              </div>
            </div>

            <div class="col-md-9">
              <div class="card mb-0">
                <div class="card-header p-2">
                  <h6 class="mb-0 pl-1">Check the subjects for this enrollment</h6>
                </div>
                <div class="card-body" style="max-height:360px; overflow-y:auto;">
                  <table class="table table-sm table-hover mb-0" id="MS_TABLE">
                    <thead>
                      <tr>
                        <th style="width:40px;"></th>
                        <th>Code</th>
                        <th>Subject</th>
                        <th>Units</th>
                        <th>Year / Sem</th>
                      </tr>
                    </thead>
                    <tbody id="MS_TBODY">
                      <tr><td colspan="5" class="text-muted text-center">Loading...</td></tr>
                    </tbody>
                  </table>
                </div>
                <!-- HIPANAO SOLUTIONS - Select All / Clear All, sa baba
                     ng listahan ng subjects para mabilis na ma-check
                     lahat (o ma-clear lahat) kaysa isa-isahin. -->
                <div class="card-footer p-2 text-right">
                  <button type="button" id="MS_SELECT_ALL" class="btn btn-outline-primary btn-sm"><i class="fa fa-check-square"></i> Select All</button>
                  <button type="button" id="MS_CLEAR_ALL" class="btn btn-outline-secondary btn-sm"><i class="fa fa-square"></i> Clear All</button>
                </div>
              </div>
            </div>
          </div>

        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-warning"><i class="fa fa-save"></i> Save Subjects</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- =================================================================
     REQUIREMENTS / DOCUMENTS - HIPANAO SOLUTIONS.
     Parehong checklist ng Enrollment > Document (iisang REQ_* fields
     sa tblstudent) - dito rin ma-tsek/ma-edit para sa mga estudyanteng
     "Enroll" na, kung sakaling may kulang pang naisumite noon.
     ================================================================= -->
<div class="modal fade" id="requirementsModal">
  <div class="modal-dialog modal-lg">
    <form action="controller.php?action=requirements" method="POST">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"><i class="fa fa-clipboard-check text-dark"></i> &nbsp;Requirements / Documents</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">

          <input type="hidden" name="REQ_EID" id="REQ_EID" value="">

          <div class="row">

            <div class="col-md-3">
              <div class="card card-dark card-outline mb-0">
                <div class="card-body box-profile text-center">
                  <img id="REQ_PICTURE"
                       class="profile-user-img img-fluid img-circle"
                       src="<?php echo WEB_ROOT; ?>module/student/image/1.png"
                       alt="Student photo">
                  <h3 class="profile-username text-center mt-2 mb-0" id="REQ_IDNO_TEXT" style="font-size:1rem;">-</h3>
                  <p class="text-muted text-center mb-0" id="REQ_NAME_TEXT">-</p>
                  <p class="text-center mb-0"><span class="badge badge-secondary" id="REQ_STATUS_TEXT">-</span></p>
                </div>
              </div>
            </div>

            <div class="col-md-9">
              <div class="card mb-0">
                <div class="card-header p-2">
                  <h6 class="mb-0 pl-1">Check off the documents already submitted</h6>
                </div>
                <div class="card-body" style="max-height:360px; overflow-y:auto;">
                  <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="REQ_FORM138" id="REQD_FORM138" value="1">
                    <label class="form-check-label" for="REQD_FORM138">Form 138/SF9 (High School Report Card)</label>
                  </div>
                  <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="REQ_GOODMORAL" id="REQD_GOODMORAL" value="1">
                    <label class="form-check-label" for="REQD_GOODMORAL">Certificate of Good Moral Character</label>
                  </div>
                  <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="REQ_BIRTHCERT" id="REQD_BIRTHCERT" value="1">
                    <label class="form-check-label" for="REQD_BIRTHCERT">Certificate of Live Birth - NSO/PSA (photocopy)</label>
                  </div>
                  <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="REQ_BAPTISMAL" id="REQD_BAPTISMAL" value="1">
                    <label class="form-check-label" for="REQD_BAPTISMAL">Baptismal/Dedication Certificate (photocopy)</label>
                  </div>
                  <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="REQ_ASSESSMENT" id="REQD_ASSESSMENT" value="1">
                    <label class="form-check-label" for="REQD_ASSESSMENT">Result of Assessment Test</label>
                  </div>
                  <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="REQ_TRANSFERCRED" id="REQD_TRANSFERCRED" value="1">
                    <label class="form-check-label" for="REQD_TRANSFERCRED">For transferees: Certificate of Transfer Credential and O.T.R.</label>
                  </div>
                  <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="REQ_MARRIAGECONTRACT" id="REQD_MARRIAGECONTRACT" value="1">
                    <label class="form-check-label" for="REQD_MARRIAGECONTRACT">For married students (Female): Marriage Contract - NSO (photocopy)</label>
                  </div>
                  <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="REQ_2X2PICTURE" id="REQD_2X2PICTURE" value="1">
                    <label class="form-check-label" for="REQD_2X2PICTURE">1 pc. 2x2 Picture</label>
                  </div>
                  <div class="row">
                    <div class="col-sm-4">
                      <div class="form-group">
                        <label for="REQD_OTHERS1" class="col-form-label col-form-label-sm">Others 1</label>
                        <input type="text" class="form-control form-control-sm" name="REQ_OTHERS1" id="REQD_OTHERS1">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <div class="form-group">
                        <label for="REQD_OTHERS2" class="col-form-label col-form-label-sm">Others 2</label>
                        <input type="text" class="form-control form-control-sm" name="REQ_OTHERS2" id="REQD_OTHERS2">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <div class="form-group">
                        <label for="REQD_OTHERS3" class="col-form-label col-form-label-sm">Others 3</label>
                        <input type="text" class="form-control form-control-sm" name="REQ_OTHERS3" id="REQD_OTHERS3">
                      </div>
                    </div>
                    <div class="col-sm-12">
                      <div class="form-group">
                        <label for="REQD_NOTES" class="col-form-label col-form-label-sm">Notes</label>
                        <textarea class="form-control form-control-sm" name="REQ_NOTES" id="REQD_NOTES" rows="2"></textarea>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>

        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-dark"><i class="fa fa-save"></i> Save Requirements</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- =================================================================
     EDIT ENROLLMENT - HIPANAO SOLUTIONS.
     I-edit ang Course, Section, Academic Year, Semester, Year Level,
     Category, Curriculum, at Status ng record dito na rin, hindi na
     kailangang pumunta pa sa Enrollment module.
     ================================================================= -->
<div class="modal fade" id="editEnrollmentDetailsModal">
  <div class="modal-dialog modal-lg">
    <form action="controller.php?action=edit" method="POST">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"><i class="fa fa-edit text-primary"></i> &nbsp;Edit Enrollment</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">

          <input type="hidden" name="ED_EID" id="ED_EID" value="">

          <div class="row">

            <div class="col-md-3">
              <div class="card card-primary card-outline mb-0">
                <div class="card-body box-profile text-center">
                  <img id="ED_PICTURE"
                       class="profile-user-img img-fluid img-circle"
                       src="<?php echo WEB_ROOT; ?>module/student/image/1.png"
                       alt="Student photo">
                  <h3 class="profile-username text-center mt-2 mb-0" id="ED_IDNO_TEXT" style="font-size:1rem;">-</h3>
                  <p class="text-muted text-center mb-0" id="ED_NAME_TEXT">-</p>
                </div>
              </div>
            </div>

            <div class="col-md-9">
              <div class="card mb-0">
                <div class="card-header p-2">
                  <h6 class="mb-0 pl-1">Enrollment Details</h6>
                </div>
                <div class="card-body">
                  <div class="row">

                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="ED_SY" class="col-form-label col-form-label-sm">Academic Year</label>
                        <select class="form-control form-control-sm" name="ED_SY" id="ED_SY" required>
                          <?php foreach ($edSchoolYears as $sy) { ?>
                          <option value="<?php echo $sy->SY_ID; ?>"><?php echo htmlspecialchars($sy->SCHOOL_YEAR); ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="ED_SEMESTER" class="col-form-label col-form-label-sm">Semester</label>
                        <select class="form-control form-control-sm" name="ED_SEMESTER" id="ED_SEMESTER" required>
                          <?php foreach ($edSemesters as $sem) { ?>
                          <option value="<?php echo $sem; ?>"><?php echo $sem; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-sm-12">
                      <div class="form-group">
                        <label for="ED_COURSE" class="col-form-label col-form-label-sm">Course</label>
                        <select class="form-control form-control-sm" name="ED_COURSE" id="ED_COURSE" required>
                          <?php foreach ($edCourses as $c) { ?>
                          <option value="<?php echo $c->COURSE_ID; ?>"><?php echo htmlspecialchars($c->COURSE_CODE.' - '.$c->COURSE_NAME); ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-sm-12">
                      <div class="form-group">
                        <label for="ED_SECTION" class="col-form-label col-form-label-sm">Section</label>
                        <select class="form-control form-control-sm" name="ED_SECTION" id="ED_SECTION">
                          <option value="">Not sectioned yet</option>
                        </select>
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="ED_YEARLEVEL" class="col-form-label col-form-label-sm">Year Level</label>
                        <select class="form-control form-control-sm" name="ED_YEARLEVEL" id="ED_YEARLEVEL" required>
                          <?php foreach ($edYearLevels as $yl) { ?>
                          <option value="<?php echo $yl; ?>"><?php echo $yl; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="ED_CURRICULUM" class="col-form-label col-form-label-sm">Curriculum Yr</label>
                        <input type="text" class="form-control form-control-sm" name="ED_CURRICULUM" id="ED_CURRICULUM" placeholder="e.g. 2023-2024">
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="ED_CATEGORY" class="col-form-label col-form-label-sm">Category</label>
                        <select class="form-control form-control-sm" name="ED_CATEGORY" id="ED_CATEGORY" required>
                          <?php foreach ($edCategories as $cat) { ?>
                          <option value="<?php echo $cat; ?>"><?php echo $cat; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="ED_STATUS" class="col-form-label col-form-label-sm">Status</label>
                        <select class="form-control form-control-sm" name="ED_STATUS" id="ED_STATUS" required>
                          <?php foreach ($edStatuses as $st) { ?>
                          <option value="<?php echo $st; ?>"><?php echo $st; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="ED_DATE_RESERVED" class="col-form-label col-form-label-sm">Date Reserved</label>
                        <input type="date" class="form-control form-control-sm" name="ED_DATE_RESERVED" id="ED_DATE_RESERVED">
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="ED_DATE_ENROLLED" class="col-form-label col-form-label-sm">Date Enrolled</label>
                        <input type="date" class="form-control form-control-sm" name="ED_DATE_ENROLLED" id="ED_DATE_ENROLLED">
                      </div>
                    </div>

                  </div>
                </div>
              </div>
            </div>

          </div>

        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save changes</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- =================================================================
     REGISTER AGAIN (Stage 1 muli) - HIPANAO SOLUTIONS.
     Para sa estudyanteng tapos na ang kasalukuyang semester (o
     papasok na sa susunod na Year Level) - bagong enrollment slot
     para sa PAREHONG estudyante, pupunta pagkatapos sa Enrollment
     module para doon ituloy ang Assign -> Payment -> Enroll.
     ================================================================= -->
<div class="modal fade" id="registerAgainModal">
  <div class="modal-dialog modal-lg">
    <form action="controller.php?action=register" method="POST">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"><i class="fa fa-redo text-success"></i> &nbsp;Register Again (Next Term)</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">

          <input type="hidden" name="RA_SID" id="RA_SID" value="">
          <input type="hidden" name="RA_EID" id="RA_EID" value="">

          <p class="text-muted small mb-3">
            <i class="fa fa-info-circle"></i>
            Suggested Year Level / Semester are auto-filled based on this student's current record - review and adjust before saving.
          </p>

          <div class="row">
            <div class="col-md-3">
              <div class="card card-success card-outline mb-0">
                <div class="card-body box-profile text-center">
                  <img id="RA_PICTURE" class="profile-user-img img-fluid img-circle"
                       src="<?php echo WEB_ROOT; ?>module/student/image/1.png" alt="Student photo">
                  <h3 class="profile-username text-center mt-2 mb-0" id="RA_IDNO_TEXT" style="font-size:1rem;">-</h3>
                  <p class="text-muted text-center mb-0" id="RA_NAME_TEXT">-</p>
                </div>
              </div>
            </div>

            <div class="col-md-9">
              <div class="card mb-0">
                <div class="card-header p-2">
                  <h6 class="mb-0 pl-1">Registration Details</h6>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="RA_SY" class="col-form-label col-form-label-sm">School Year</label>
                        <select class="form-control form-control-sm" name="RA_SY" id="RA_SY" required>
                          <?php foreach ($edSchoolYears as $sy) { ?>
                          <option value="<?php echo $sy->SY_ID; ?>"><?php echo htmlspecialchars($sy->SCHOOL_YEAR); ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="RA_COURSE" class="col-form-label col-form-label-sm">Course</label>
                        <select class="form-control form-control-sm" name="RA_COURSE" id="RA_COURSE" required>
                          <?php foreach ($edCourses as $c) { ?>
                          <option value="<?php echo $c->COURSE_ID; ?>"><?php echo htmlspecialchars($c->COURSE_CODE.' - '.$c->COURSE_NAME); ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="RA_YEARLEVEL" class="col-form-label col-form-label-sm">Year Level</label>
                        <select class="form-control form-control-sm" name="RA_YEARLEVEL" id="RA_YEARLEVEL" required>
                          <option value="">-- Select --</option>
                          <?php foreach ($edYearLevels as $yl) { ?>
                          <option value="<?php echo $yl; ?>"><?php echo $yl; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="RA_SEMESTER" class="col-form-label col-form-label-sm">Semester</label>
                        <select class="form-control form-control-sm" name="RA_SEMESTER" id="RA_SEMESTER" required>
                          <option value="">-- Select --</option>
                          <?php foreach ($edSemesters as $sem) { ?>
                          <option value="<?php echo $sem; ?>"><?php echo $sem; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="RA_CATEGORY" class="col-form-label col-form-label-sm">Category</label>
                        <select class="form-control form-control-sm" name="RA_CATEGORY" id="RA_CATEGORY" required>
                          <?php foreach ($edCategories as $cat) { ?>
                          <option value="<?php echo $cat; ?>" <?php echo ($cat == 'Old') ? 'selected' : ''; ?>><?php echo $cat; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="RA_CURRICULUM" class="col-form-label col-form-label-sm">Curriculum Yr</label>
                        <input type="text" class="form-control form-control-sm" name="RA_CURRICULUM" id="RA_CURRICULUM" placeholder="e.g. 2025-2026">
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="RA_DATE_RESERVED" class="col-form-label col-form-label-sm">Date Reserved</label>
                        <input type="date" class="form-control form-control-sm" name="RA_DATE_RESERVED" id="RA_DATE_RESERVED" value="<?php echo date('Y-m-d'); ?>">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Register Slot</button>
        </div>
      </div>
    </form>
  </div>
</div>
