<?php

global $mydb;
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
                <h3 class="card-title mb-0"><i class="fa fa-graduation-cap mr-1 text-muted"></i> Grades</h3>
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
            <table id="tblgradestudents" class="table table-bordered table-striped" style="width:100%">
              <thead>
                <tr>
                  <th>#</th>
                  <th>ID No.</th>
                  <th>Student Name</th>
                  <th>Course</th>
                  <th>Year Level</th>
                  <th>AY / Semester</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>

      </div>
    </div>

    <!-- =================================================================
         HISTORY GRADE - HIPANAO SOLUTIONS.
         Mga grade ng mga enrollment na naka-archive na (naka-"Register
         Again" ang estudyante at na-Enroll na sa bagong term - o
         "Dropped"). Dito na lilipat ang lumang record kapag na-enroll
         ulit ang estudyante (tingnan ang markAsEnrolled() sa
         include/functions.php). Read-only lang ang View - "Grade"
         (encode) button ay wala na dito, dahil tapos na ang term na
         ito - View at Delete (delete ng grade record) na lang.
         ================================================================= -->
    <div class="row">
      <div class="col-12">

        <div class="card card-secondary card-outline">
          <div class="card-header">
            <div class="hipanao-toolbar">
              <div>
                <h3 class="card-title mb-0"><i class="fa fa-history mr-1 text-muted"></i> History Grade</h3>
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
            <table id="tblgradehistory" class="table table-bordered table-striped" style="width:100%">
              <thead>
                <tr>
                  <th>#</th>
                  <th>ID No.</th>
                  <th>Student Name</th>
                  <th>Course</th>
                  <th>Year Level</th>
                  <th>AY / Semester</th>
                  <th>Status</th>
                  <th>Grades</th>
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
     ENCODE GRADES - HIPANAO SOLUTIONS.
     Kaparehong disenyo ng Collect Payment modal - litrato/pangalan sa
     kaliwa, tapos lahat ng subject na kinuha para dito mismong
     enrollment, bawat isa may sariling Grade at Remarks - sabay-sabay
     na naise-save.
     ================================================================= -->
<div class="modal fade" id="gradeModal">
  <div class="modal-dialog modal-lg">
    <form action="controller.php?action=save" method="POST">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"><i class="fa fa-graduation-cap text-info"></i> &nbsp;Encode Grades</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">

          <input type="hidden" name="GRADE_EID" id="GRADE_EID" value="">

          <div class="row">
            <div class="col-md-3">
              <div class="card card-info card-outline mb-0">
                <div class="card-body box-profile text-center">
                  <img id="GRADE_PICTURE"
                       class="profile-user-img img-fluid img-circle"
                       src="<?php echo WEB_ROOT; ?>module/student/image/1.png"
                       alt="Student photo">
                  <h3 class="profile-username text-center mt-2 mb-0" id="GRADE_IDNO_TEXT" style="font-size:1rem;">-</h3>
                  <p class="text-muted text-center mb-0" id="GRADE_NAME_TEXT">-</p>
                </div>
                <ul class="list-group list-group-flush">
                  <li class="list-group-item p-2">
                    <small class="text-muted d-block">Course</small>
                    <span id="GRADE_COURSE_TEXT">-</span>
                  </li>
                  <li class="list-group-item p-2">
                    <small class="text-muted d-block">AY / Semester</small>
                    <span id="GRADE_TERM_TEXT">-</span>
                  </li>
                </ul>
              </div>
            </div>

            <div class="col-md-9">
              <table class="table table-sm table-hover mb-0">
                <thead class="thead-light">
                  <tr>
                    <th>Subject</th>
                    <th width="25%">Grade</th>
                    <th width="30%">Remarks</th>
                  </tr>
                </thead>
                <tbody id="GRADE_SUBJECTS_TBODY">
                  <tr><td colspan="4" class="text-muted text-center">Loading...</td></tr>
                </tbody>
              </table>
            </div>
          </div>

        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Save Grades</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- =================================================================
     VIEW GRADES (read-only) - HIPANAO SOLUTIONS.
     Kaparehong laman ng Encode Grades modal sa itaas, pero paningin
     lang - walang input/select, walang Save, para sa mabilisang
     pagtingin ng grades na naka-encode na.
     ================================================================= -->
<div class="modal fade" id="viewGradeModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><i class="fa fa-eye text-info"></i> &nbsp;View Grades</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

        <div class="row">
          <div class="col-md-3">
            <div class="card card-info card-outline mb-0">
              <div class="card-body box-profile text-center">
                <img id="VG_PICTURE"
                     class="profile-user-img img-fluid img-circle"
                     src="<?php echo WEB_ROOT; ?>module/student/image/1.png"
                     alt="Student photo">
                <h3 class="profile-username text-center mt-2 mb-0" id="VG_IDNO_TEXT" style="font-size:1rem;">-</h3>
                <p class="text-muted text-center mb-0" id="VG_NAME_TEXT">-</p>
              </div>
              <ul class="list-group list-group-flush">
                <li class="list-group-item p-2">
                  <small class="text-muted d-block">Course</small>
                  <span id="VG_COURSE_TEXT">-</span>
                </li>
                <li class="list-group-item p-2">
                  <small class="text-muted d-block">AY / Semester</small>
                  <span id="VG_TERM_TEXT">-</span>
                </li>
              </ul>
            </div>
          </div>

          <div class="col-md-9">
            <table class="table table-sm table-hover mb-0">
              <thead class="thead-light">
                <tr>
                  <th>Subject</th>
                  <th width="20%">Grade</th>
                  <th width="30%">Remarks</th>
                </tr>
              </thead>
              <tbody id="VG_SUBJECTS_TBODY">
                <tr><td colspan="3" class="text-muted text-center">Loading...</td></tr>
              </tbody>
            </table>
          </div>
        </div>

      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
