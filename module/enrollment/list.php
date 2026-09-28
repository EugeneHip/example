<?php

global $mydb;

$enSchoolYears = array();
$mydb->setQuery("SELECT SY_ID, SCHOOL_YEAR, STATUS FROM `tblschoolyear` ORDER BY SCHOOL_YEAR DESC");
foreach ($mydb->loadResultList() as $r) { $enSchoolYears[] = $r; }

$enCourses = array();
$mydb->setQuery("SELECT COURSE_ID, COURSE_CODE, COURSE_NAME FROM `tblcourses` ORDER BY COURSE_CODE ASC");
foreach ($mydb->loadResultList() as $r) { $enCourses[] = $r; }

$enYearLevels = array('1st Year', '2nd Year', '3rd Year', '4th Year');
$enSemesters  = array('1st Semester', '2nd Semester', 'Summer');
$enCategories = array('New', 'Old', 'Transferee', 'Returnee', 'Shiftee');
/* HIPANAO SOLUTIONS - Bagong 5-hakbang na daloy (wala nang hiwalay na
   Sectioning): Register -> Assign -> Payment -> Paid -> Enroll (manual).
   Ang Section ay hindi na kailangan bago makapag-Assign/makapagbayad -
   naroon pa rin ang field sa Edit Enrollment kung kailangang i-set ng
   staff. Ang "Paid" ay bagong status sa gitna ng Assign at Enroll -
   dito na dumadaan ang isang estudyante pagkatapos mabayaran nang buo
   ang Enrollment Fee, bago pa man opisyal na maging "Enroll" (na
   sadyang manual/aksyon pa rin - tingnan ang "Enroll" na button sa
   Action column sa ibaba). */
/* HIPANAO SOLUTIONS - Bagong 6-hakbang na daloy (may Document na
   ngayon sa pagitan ng Assign at Paid): Register -> Assign -> Document
   -> Paid -> Enroll (manual). Ang Document ay kung saan tine-tsek ng
   REGISTRAR STAFF (hindi ng applicant) kung anong mga dokumento na ang
   naisumite - tingnan ang "Document" na modal at doDocument() sa
   controller.php sa ibaba. */
$enStatuses   = array('Register', 'Assign', 'Document', 'Paid', 'Enroll', 'Dropped', 'Completed');

$mydb->setQuery("SELECT ENROLLMENT_ID FROM `tblenrollment` WHERE STATUS = 'Register'");
$cntRegister = $mydb->num_rows();
$mydb->setQuery("SELECT ENROLLMENT_ID FROM `tblenrollment` WHERE STATUS = 'Assign'");
$cntAssign = $mydb->num_rows();
$mydb->setQuery("SELECT ENROLLMENT_ID FROM `tblenrollment` WHERE STATUS = 'Document'");
$cntDocument = $mydb->num_rows();
$mydb->setQuery("SELECT ENROLLMENT_ID FROM `tblenrollment` WHERE STATUS = 'Paid'");
$cntPaid = $mydb->num_rows();
$mydb->setQuery("SELECT ENROLLMENT_ID FROM `tblenrollment` WHERE STATUS = 'Enroll'");
$cntEnroll = $mydb->num_rows();
/* "All" here means "still in the enrollment queue" - once fully
   Enroll (enrollment fee paid), a student is done with this list
   (see module/enrollment/ajax.php), so the All count matches what
   actually shows on that tab. Click the Enroll chip to still find them.

   HIPANAO SOLUTIONS - BUG FIX: "Completed" (naunang term na naipalit
   dahil nag-Register Again + nag-Enroll ulit ang estudyante - tingnan
   ang markAsEnrolled() sa include/functions.php) at "Dropped" ay hindi
   dapat sumali dito sa "All" - tapos na ang saysay ng mga record na
   iyon dito, nasa module/hitoryenrollment na sila makikita. Dating
   "STATUS <> 'Enroll'" lang ang tsek kaya lumalabas pa rin dito ang
   mga "Completed" record, may Edit/Delete pa, kahit dapat wala nang
   silbi rito. */
$mydb->setQuery("SELECT ENROLLMENT_ID FROM `tblenrollment` WHERE STATUS NOT IN ('Enroll', 'Completed', 'Dropped')");
$cntAll = $mydb->num_rows();
?>
<section class="content">
  <div class="container-fluid">
    <?php check_message(); ?>

    <div class="row">
      <div class="col-12">

        <div class="card card-primary card-outline">
          <div class="card-header">
            <div class="hipanao-toolbar">
              <div>
                <h3 class="card-title mb-0"><i class="fa fa-clipboard-list mr-1 text-muted"></i> Enrollment Records</h3>
              </div>
              <div class="hipanao-toolbar-actions">
                <div class="btn-group btn-group-sm btn-pill-group" id="statusFilters">
                  <button type="button" class="btn btn-secondary btn-pill active" data-filter="">All (<?php echo $cntAll; ?>)</button>
                  <button type="button" class="btn btn-outline-warning btn-pill" data-filter="Register">Register (<?php echo $cntRegister; ?>)</button>
                  <button type="button" class="btn btn-outline-primary btn-pill" data-filter="Assign">Assign (<?php echo $cntAssign; ?>)</button>
                  <button type="button" class="btn btn-outline-dark btn-pill" data-filter="Document">Document (<?php echo $cntDocument; ?>)</button>
                  <button type="button" class="btn btn-outline-info btn-pill" data-filter="Paid">Paid (<?php echo $cntPaid; ?>)</button>
                  <button type="button" class="btn btn-outline-success btn-pill" data-filter="Enroll">Enroll (<?php echo $cntEnroll; ?>)</button>
                </div>
              </div>
            </div>
          </div>

          <div class="card-body">
            <?php if (!$mydb->tableExists('tblenrollment')): ?>
            <div class="alert alert-warning mb-3">
              <i class="fa fa-exclamation-triangle mr-1"></i>
              Table <code>tblenrollment</code> was not found in the database.
              The SQL file may not have been fully imported yet. The rest of the system will still work normally.
            </div>
            <?php endif; ?>
            <table id="tblenrollmentlist" class="table table-bordered table-striped" style="width:100%">
              <thead>
                <tr>
                  <th>#</th>
                  <th>ID No.</th>
                  <th>Student Name</th>
                  <th>Course</th>
                  <th>Academic Year</th>
                  <th>Semester</th>
                  <th>Year Level</th>
                  <th>Category</th>
                  <th>Section</th>
                  <th>Date Reserved</th>
                  <th>Date Enrolled</th>
                  <th>Status</th>
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
     SUBJECTS (Stage 2, pagkatapos ng Register)
     Dito pumipili ng subjects ang isang estudyante - puwede na agad
     kahit "Register" pa lang ang status, wala nang hiwalay na
     Sectioning na hihintayin. Nagiging "Assign" ang record pag may
     kahit isang subject na naka-check (tingnan ang
     syncEnrollmentStatusFromSubjects() sa include/functions.php).
     Ang Section ay hindi na kailangan dito - puwede pa ring i-set ng
     staff sa Edit Enrollment modal kahit kailan.
     ================================================================= -->
<div class="modal fade" id="subjectsModal">
  <div class="modal-dialog modal-lg">
    <form action="controller.php?action=subjects" method="POST" id="subjectsForm">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"><i class="fa fa-book text-primary"></i> &nbsp;Subjects</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">

          <input type="hidden" name="SUB_EID" id="SUB_EID" value="">

          <div class="row">

            <div class="col-md-3">
              <div class="card card-primary card-outline mb-0">
                <div class="card-body box-profile text-center">
                  <img id="SUB_PICTURE"
                       class="profile-user-img img-fluid img-circle"
                       src="<?php echo WEB_ROOT; ?>module/student/image/1.png"
                       alt="Student photo">
                  <h3 class="profile-username text-center mt-2 mb-0" id="SUB_IDNO_TEXT" style="font-size:1rem;">-</h3>
                  <p class="text-muted text-center mb-0" id="SUB_NAME_TEXT">-</p>
                  <p class="text-muted text-center mb-0"><small id="SUB_COURSE_TEXT">-</small></p>
                  <p class="text-center mb-0"><span class="badge badge-secondary" id="SUB_STATUS_TEXT">-</span></p>
                </div>
              </div>
            </div>

            <div class="col-md-9">
              <div class="card mb-0">
                <div class="card-header p-2">
                  <h6 class="mb-0 pl-1">Check the subjects to enroll for this term</h6>
                </div>
                <div class="card-body" style="max-height:360px; overflow-y:auto;">
                  <table class="table table-sm table-hover mb-0" id="SUB_TABLE">
                    <thead>
                      <tr>
                        <th style="width:40px;"></th>
                        <th>Code</th>
                        <th>Subject</th>
                        <th>Units</th>
                        <th>Year / Sem</th>
                      </tr>
                    </thead>
                    <tbody id="SUB_TBODY">
                      <tr><td colspan="5" class="text-muted text-center">Loading...</td></tr>
                    </tbody>
                  </table>
                </div>
                <!-- HIPANAO SOLUTIONS - Select All / Clear All, sa baba
                     ng listahan ng subjects para mabilis na ma-check
                     lahat (o ma-clear lahat) kaysa isa-isahin. -->
                <div class="card-footer p-2 text-right">
                  <button type="button" id="SUB_SELECT_ALL" class="btn btn-outline-primary btn-sm"><i class="fa fa-check-square"></i> Select All</button>
                  <button type="button" id="SUB_CLEAR_ALL" class="btn btn-outline-secondary btn-sm"><i class="fa fa-square"></i> Clear All</button>
                </div>
              </div>
            </div>

          </div>

        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Subjects</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- =================================================================
     DOCUMENT (Stage 3, pagkatapos ng Assign Subjects)
     Dito ina-alalayan/tine-tsek ng REGISTRAR STAFF (hindi ng applicant)
     kung anong mga physical na dokumento na ang naisumite ng estudyante.
     Pag-save nito habang "Assign" pa ang STATUS, awtomatiko itong
     lilipat sa "Document" - tingnan ang doDocument() sa controller.php.
     ================================================================= -->
<div class="modal fade" id="documentModal">
  <div class="modal-dialog modal-lg">
    <form action="controller.php?action=document" method="POST" id="documentForm">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"><i class="fa fa-clipboard-check text-dark"></i> &nbsp;Requirements / Documents</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">

          <input type="hidden" name="DOC_EID" id="DOC_EID" value="">

          <div class="row">

            <div class="col-md-3">
              <div class="card card-dark card-outline mb-0">
                <div class="card-body box-profile text-center">
                  <img id="DOC_PICTURE"
                       class="profile-user-img img-fluid img-circle"
                       src="<?php echo WEB_ROOT; ?>module/student/image/1.png"
                       alt="Student photo">
                  <h3 class="profile-username text-center mt-2 mb-0" id="DOC_IDNO_TEXT" style="font-size:1rem;">-</h3>
                  <p class="text-muted text-center mb-0" id="DOC_NAME_TEXT">-</p>
                  <p class="text-center mb-0"><span class="badge badge-secondary" id="DOC_STATUS_TEXT">-</span></p>
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
                    <input class="form-check-input" type="checkbox" name="DOC_FORM138" id="DOC_FORM138" value="1">
                    <label class="form-check-label" for="DOC_FORM138">Form 138/SF9 (High School Report Card)</label>
                  </div>
                  <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="DOC_GOODMORAL" id="DOC_GOODMORAL" value="1">
                    <label class="form-check-label" for="DOC_GOODMORAL">Certificate of Good Moral Character</label>
                  </div>
                  <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="DOC_BIRTHCERT" id="DOC_BIRTHCERT" value="1">
                    <label class="form-check-label" for="DOC_BIRTHCERT">Certificate of Live Birth - NSO/PSA (photocopy)</label>
                  </div>
                  <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="DOC_BAPTISMAL" id="DOC_BAPTISMAL" value="1">
                    <label class="form-check-label" for="DOC_BAPTISMAL">Baptismal/Dedication Certificate (photocopy)</label>
                  </div>
                  <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="DOC_ASSESSMENT" id="DOC_ASSESSMENT" value="1">
                    <label class="form-check-label" for="DOC_ASSESSMENT">Result of Assessment Test</label>
                  </div>
                  <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="DOC_TRANSFERCRED" id="DOC_TRANSFERCRED" value="1">
                    <label class="form-check-label" for="DOC_TRANSFERCRED">For transferees: Certificate of Transfer Credential and O.T.R.</label>
                  </div>
                  <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="DOC_MARRIAGECONTRACT" id="DOC_MARRIAGECONTRACT" value="1">
                    <label class="form-check-label" for="DOC_MARRIAGECONTRACT">For married students (Female): Marriage Contract - NSO (photocopy)</label>
                  </div>
                  <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="DOC_2X2PICTURE" id="DOC_2X2PICTURE" value="1">
                    <label class="form-check-label" for="DOC_2X2PICTURE">1 pc. 2x2 Picture</label>
                  </div>
                  <div class="row">
                    <div class="col-sm-4">
                      <div class="form-group">
                        <label for="DOC_OTHERS1" class="col-form-label col-form-label-sm">Others 1</label>
                        <input type="text" class="form-control form-control-sm" name="DOC_OTHERS1" id="DOC_OTHERS1">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <div class="form-group">
                        <label for="DOC_OTHERS2" class="col-form-label col-form-label-sm">Others 2</label>
                        <input type="text" class="form-control form-control-sm" name="DOC_OTHERS2" id="DOC_OTHERS2">
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <div class="form-group">
                        <label for="DOC_OTHERS3" class="col-form-label col-form-label-sm">Others 3</label>
                        <input type="text" class="form-control form-control-sm" name="DOC_OTHERS3" id="DOC_OTHERS3">
                      </div>
                    </div>
                    <div class="col-sm-12">
                      <div class="form-group">
                        <label for="DOC_NOTES" class="col-form-label col-form-label-sm">Notes</label>
                        <textarea class="form-control form-control-sm" name="DOC_NOTES" id="DOC_NOTES" rows="2"></textarea>
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
     EDIT an existing enrollment record (Pag-edit ng existing record)

     TAMA NA DISENYO: Plain white ang header na ngayon (hindi na
     malaking bg-warning), at may litrato ng estudyante sa kaliwa,
     kaparehong disenyo ng Register modal at View page ng Student.
     ================================================================= -->
<div class="modal fade" id="editEnrollmentModal">
  <div class="modal-dialog modal-lg">
    <form action="controller.php?action=edit" method="POST">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"><i class="fa fa-edit text-warning"></i> &nbsp;Edit Enrollment</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">

          <input type="hidden" name="E_EID" id="E_EID" value="">

          <div class="row">

            <!-- Litrato ng estudyante - kaparehong disenyo ng profile
                 card sa Register modal at View page ng Student. -->
            <div class="col-md-3">
              <div class="card card-warning card-outline mb-0">
                <div class="card-body box-profile text-center">
                  <img id="E_PICTURE"
                       class="profile-user-img img-fluid img-circle"
                       src="<?php echo WEB_ROOT; ?>module/student/image/1.png"
                       alt="Student photo">
                  <h3 class="profile-username text-center mt-2 mb-0" id="E_IDNO_TEXT" style="font-size:1rem;">-</h3>
                  <p class="text-muted text-center mb-0" id="E_NAME_TEXT">-</p>
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
                        <label for="E_SY" class="col-form-label col-form-label-sm">Academic Year</label>
                        <select class="form-control form-control-sm" name="E_SY" id="E_SY" required>
                          <?php foreach ($enSchoolYears as $sy) { ?>
                          <option value="<?php echo $sy->SY_ID; ?>"><?php echo htmlspecialchars($sy->SCHOOL_YEAR); ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="E_SEMESTER" class="col-form-label col-form-label-sm">Semester</label>
                        <select class="form-control form-control-sm" name="E_SEMESTER" id="E_SEMESTER" required>
                          <?php foreach ($enSemesters as $sem) { ?>
                          <option value="<?php echo $sem; ?>"><?php echo $sem; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-sm-12">
                      <div class="form-group">
                        <label for="E_COURSE" class="col-form-label col-form-label-sm">Course</label>
                        <select class="form-control form-control-sm" name="E_COURSE" id="E_COURSE" required>
                          <?php foreach ($enCourses as $c) { ?>
                          <option value="<?php echo $c->COURSE_ID; ?>"><?php echo htmlspecialchars($c->COURSE_CODE.' - '.$c->COURSE_NAME); ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-sm-12">
                      <div class="form-group">
                        <label for="E_SECTION" class="col-form-label col-form-label-sm">Section</label>
                        <select class="form-control form-control-sm" name="E_SECTION" id="E_SECTION">
                          <option value="">Not sectioned yet</option>
                        </select>
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="E_YEARLEVEL" class="col-form-label col-form-label-sm">Year Level</label>
                        <select class="form-control form-control-sm" name="E_YEARLEVEL" id="E_YEARLEVEL" required>
                          <?php foreach ($enYearLevels as $yl) { ?>
                          <option value="<?php echo $yl; ?>"><?php echo $yl; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="E_CURRICULUM" class="col-form-label col-form-label-sm">Curriculum Yr</label>
                        <input type="text" class="form-control form-control-sm" name="E_CURRICULUM" id="E_CURRICULUM" placeholder="e.g. 2023-2024">
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="E_CATEGORY" class="col-form-label col-form-label-sm">Category</label>
                        <select class="form-control form-control-sm" name="E_CATEGORY" id="E_CATEGORY" required>
                          <?php foreach ($enCategories as $cat) { ?>
                          <option value="<?php echo $cat; ?>"><?php echo $cat; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="E_STATUS" class="col-form-label col-form-label-sm">Status</label>
                        <select class="form-control form-control-sm" name="E_STATUS" id="E_STATUS" required>
                          <?php foreach ($enStatuses as $st) { ?>
                          <option value="<?php echo $st; ?>"><?php echo $st; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="E_DATE_RESERVED" class="col-form-label col-form-label-sm">Date Reserved</label>
                        <input type="date" class="form-control form-control-sm" name="E_DATE_RESERVED" id="E_DATE_RESERVED">
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="E_DATE_ENROLLED" class="col-form-label col-form-label-sm">Date Enrolled</label>
                        <input type="date" class="form-control form-control-sm" name="E_DATE_ENROLLED" id="E_DATE_ENROLLED">
                      </div>
                    </div>

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
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-warning" name="edit"><i class="fa fa-save"></i> Save changes</button>
        </div>
      </div>
    </form>
  </div>
</div>
