<?php

$student = null;
$enroll  = null;

/* Subjects taken + payment status for the "about this student" summary
   the Action > View page now shows - HIPANAO SOLUTIONS. */
$viewSubjects      = array();
$viewTuition       = null;
$viewPaid          = null;
$viewFeeSettled    = false;
$viewTuitionSettled = true;
$viewFullyPaid     = false;

/* HIPANAO SOLUTIONS - Grades ng estudyante, ACROSS lahat ng enrollment
   record nya (hindi lang yung pinaka-huli), kasama ang course/SY/sem
   kung kailan kinuha, para makita kung meron na syang naka-grade na
   subject kahit anong term pa iyon. */
$viewGrades = array();

if (isset($_GET['id']) && $_GET['id'] != '') {
    
    $mydb->setQuery("SELECT * FROM `tblstudent` WHERE `S_ID`='".(int)$_GET['id']."' LIMIT 1");
    $student = $mydb->loadSingleResult();

    $mydb->setQuery("SELECT e.*, c.COURSE_CODE, c.COURSE_NAME, s.SECTION_NAME, sy.SCHOOL_YEAR
                      FROM `tblenrollment` e
                      LEFT JOIN `tblcourses` c ON c.COURSE_ID = e.COURSE_ID
                      LEFT JOIN `tblsections` s ON s.SECTION_ID = e.SECTION_ID
                      LEFT JOIN `tblschoolyear` sy ON sy.SY_ID = e.SY_ID
                      WHERE e.S_ID='".(int)$_GET['id']."'
                      ORDER BY e.ENROLLMENT_ID DESC LIMIT 1");
    $enroll = $mydb->loadSingleResult();

    if ($enroll) {
        $mydb->setQuery("SELECT s.SUBJECT_ID, s.SUBJECT_CODE, s.SUBJECT_NAME, s.UNITS, s.SUBJECT_TYPE
                          FROM `tblenrollmentdetails` d
                          JOIN `tblsubjects` s ON s.SUBJECT_ID = d.SUBJECT_ID
                          WHERE d.ENROLLMENT_ID = '".(int)$enroll->ENROLLMENT_ID."'
                          ORDER BY s.SUBJECT_TYPE ASC, s.SUBJECT_CODE ASC");
        $viewSubjects = $mydb->loadResultList();

        $viewTuition = tuitionBreakdownForEnrollment($enroll->ENROLLMENT_ID);
        $viewPaid    = paymentTotalsForEnrollment($enroll->ENROLLMENT_ID);

        $viewFeeSettled     = ($viewPaid['Enrollment Fee'] >= ENROLLMENT_FEE);
        $viewTuitionSettled = ($viewTuition['total_amount'] <= 0) || ($viewPaid['Tuition Fee'] >= $viewTuition['total_amount']);
        $viewFullyPaid      = $viewFeeSettled && $viewTuitionSettled;
    }

    $mydb->setQuery("SELECT g.GRADE, g.REMARKS, sub.SUBJECT_CODE, sub.SUBJECT_NAME,
                      sy.SCHOOL_YEAR, g.SEMESTER
                      FROM `tblgrades` g
                      JOIN `tblsubjects` sub ON sub.SUBJECT_ID = g.SUBJECT_ID
                      JOIN `tblschoolyear` sy ON sy.SY_ID = g.SY_ID
                      WHERE g.S_ID = '".(int)$_GET['id']."'
                      ORDER BY sy.SCHOOL_YEAR DESC, g.SEMESTER ASC, sub.SUBJECT_CODE ASC");
    $viewGrades = $mydb->loadResultList();
}
?>

<section class="content">
  <div class="container-fluid">

  <?php if (!$student): ?>
    <!-- STEP 2: Kung walang napiling estudyante, ipakita na lang ang warning -->
    <div class="alert alert-warning">
      No student was selected. Please go back to the <a href="<?php echo WEB_ROOT; ?>module/student/">student list</a> and click the view button of a student.
    </div>

  <?php else: ?>

    <div class="row">
      <div class="col-md-3">

        <!-- STEP 3: Litrato, batayang info, at buod ng enrollment (kaliwang column) -->
        <div class="card card-primary card-outline">
          <div class="card-body box-profile">

            <div class="text-center">
              <img class="profile-user-img img-fluid img-circle"
                   src="<?php echo (isset($student->COMPANYIDNO) && $student->COMPANYIDNO != '') ? WEB_ROOT.'module/student/image/'.$student->COMPANYIDNO : WEB_ROOT.'module/student/image/1.png'; ?>"
                   alt="Student photo">
            </div>

            <h3 class="profile-username text-center">
              <?php echo htmlspecialchars($student->FNAME.' '.$student->MNAME.' '.$student->LNAME); ?>
            </h3>

            <p class="text-muted text-center">
              Student ID: <?php echo htmlspecialchars($student->IDNO); ?>
            </p>

            <ul class="list-group list-group-unbordered mb-3">
              <li class="list-group-item">
                <b>Gender</b> <span class="float-right"><?php echo hipanao_badge($student->SEX); ?></span>
              </li>
              <li class="list-group-item">
                <b>Birthday</b> <span class="float-right"><?php echo htmlspecialchars($student->BDAY); ?></span>
              </li>
              <li class="list-group-item">
                <b>Status</b> <span class="float-right"><?php echo hipanao_badge($student->STATUS); ?></span>
              </li>
            </ul>

            <a href="<?php echo WEB_ROOT; ?>module/student/" class="btn btn-primary btn-block"><b>Back to List</b></a>
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->

        <!-- Buod ng kasalukuyang enrollment (Enrollment summary) -->
        <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Current Enrollment</h3>
          </div>
          <div class="card-body">
            <?php if ($enroll):  ?>
              <strong><i class="fas fa-book mr-1"></i> Course</strong>
              <p class="text-muted">
                <?php echo htmlspecialchars($enroll->COURSE_CODE.' - '.$enroll->COURSE_NAME); ?>
              </p>
              <hr>
              <strong><i class="fas fa-users mr-1"></i> Section</strong>
              <p class="text-muted"><?php echo htmlspecialchars($enroll->SECTION_NAME); ?></p>
              <hr>
              <strong><i class="fas fa-calendar mr-1"></i> School Year</strong>
              <p class="text-muted"><?php echo htmlspecialchars($enroll->SCHOOL_YEAR); ?></p>
              <hr>
              <strong><i class="fas fa-info-circle mr-1"></i> Enrollment Status</strong>
              <p class="text-muted"><?php echo hipanao_badge($enroll->STATUS); ?></p>
            <?php else: ?>
              <p class="text-muted">This student is not yet enrolled in any course/section.</p>
            <?php endif; ?>
          </div>
        </div>

        <!-- Payment Status - "bayad na ba sya sa lahat", at pindutan
             papuntang Payment module kung meron pang balanse. -->
        <?php if ($enroll): ?>
        <div class="card card-info">
          <div class="card-header">
            <h3 class="card-title">Payment Status</h3>
          </div>
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span>Enrollment Fee</span>
              <?php echo $viewFeeSettled ? hipanao_badge('Paid') : hipanao_badge('Unpaid'); ?>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span>Tuition</span>
              <?php
                if ($viewTuition['total_amount'] <= 0) {
                    echo '<span class="text-muted">No tuition due</span>';
                } else if ($viewTuitionSettled) {
                    echo hipanao_badge('Paid');
                } else if ($viewPaid['Tuition Fee'] > 0) {
                    echo '<span class="badge badge-warning">Partial (&#8369;'.number_format($viewPaid['Tuition Fee'], 2).' of &#8369;'.number_format($viewTuition['total_amount'], 2).')</span>';
                } else {
                    echo hipanao_badge('Unpaid');
                }
              ?>
            </div>
            <hr class="my-2">
            <?php if ($viewFullyPaid): ?>
              <p class="text-success mb-0"><i class="fa fa-check-circle mr-1"></i> Fully paid.</p>
            <?php else: ?>
              <p class="text-muted mb-2"><i class="fa fa-exclamation-circle mr-1"></i> This student still has a balance.</p>
              <a href="<?php echo WEB_ROOT; ?>module/payment/index.php?eid=<?php echo (int)$enroll->ENROLLMENT_ID; ?>" class="btn btn-info btn-block btn-sm">
                <i class="fa fa-money-check-alt mr-1"></i> Pay Now
              </a>
            <?php endif; ?>
          </div>
        </div>
        <?php endif; ?>

      </div>
      <!-- /.col -->
      <div class="col-md-9">
        <!-- STEP 4: Buong detalye - Profile Info / Contact Info tabs (kanang column) -->
        <div class="card">
          <div class="card-header p-2">
            <ul class="nav nav-pills">
              <li class="nav-item"><a class="nav-link active" href="#profile" data-toggle="tab">Profile Info</a></li>
              <li class="nav-item"><a class="nav-link" href="#contact" data-toggle="tab">Contact Info</a></li>
              <li class="nav-item"><a class="nav-link" href="#guardian" data-toggle="tab">Parents' / Guardian's Information</a></li>
              <?php if ($enroll): ?>
              <li class="nav-item"><a class="nav-link" href="#subjects" data-toggle="tab">Subjects Taken</a></li>
              <?php endif; ?>
              <?php if (count($viewGrades) > 0): ?>
              <li class="nav-item"><a class="nav-link" href="#grades" data-toggle="tab">Grades</a></li>
              <?php endif; ?>
            </ul>
          </div><!-- /.card-header -->
          <div class="card-body">
            <div class="tab-content">

              <!-- TAB 1: Profile info - pangunahing detalye ng estudyante -->
              <div class="active tab-pane" id="profile">
                <table class="table table-bordered">
                  <tr>
                    <th style="width:30%">Student ID Number</th>
                    <td><?php echo htmlspecialchars($student->IDNO); ?></td>
                  </tr>
                  <tr>
                    <th>Full Name</th>
                    <td><?php echo htmlspecialchars($student->FNAME.' '.$student->MNAME.' '.$student->LNAME); ?></td>
                  </tr>
                  <tr>
                    <th>Gender</th>
                    <td><?php echo hipanao_badge($student->SEX); ?></td>
                  </tr>
                  <tr>
                    <th>Birthday</th>
                    <td><?php echo htmlspecialchars($student->BDAY); ?></td>
                  </tr>
                  <tr>
                    <th>Birth Place</th>
                    <td><?php echo isset($student->BPLACE) ? htmlspecialchars($student->BPLACE) : ''; ?></td>
                  </tr>
                  <tr>
                    <th>Age</th>
                    <td><?php echo isset($student->AGE) ? htmlspecialchars($student->AGE) : ''; ?></td>
                  </tr>
                  <tr>
                    <th>Nationality</th>
                    <td><?php echo isset($student->NATIONALITY) ? htmlspecialchars($student->NATIONALITY) : ''; ?></td>
                  </tr>
                  <tr>
                    <th>Religion</th>
                    <td><?php echo isset($student->RELIGION) ? htmlspecialchars($student->RELIGION) : ''; ?></td>
                  </tr>
                  <tr>
                    <th>Status</th>
                    <td><?php echo hipanao_badge($student->STATUS); ?></td>
                  </tr>
                </table>
              </div>
              <!-- /.tab-pane -->

              <!-- TAB 2: Contact info - numero, email, at address -->
              <div class="tab-pane" id="contact">
                <table class="table table-bordered">
                  <tr>
                    <th style="width:30%">Contact Number</th>
                    <td><?php echo isset($student->CONTACT_NO) ? htmlspecialchars($student->CONTACT_NO) : ''; ?></td>
                  </tr>
                  <tr>
                    <th>Email</th>
                    <td><?php echo isset($student->EMAIL) ? htmlspecialchars($student->EMAIL) : ''; ?></td>
                  </tr>
                  <tr>
                    <th>Home Address</th>
                    <td><?php echo isset($student->HOME_ADD) ? htmlspecialchars($student->HOME_ADD) : ''; ?></td>
                  </tr>
                </table>
              </div>
              <!-- /.tab-pane -->

              <!-- TAB 2b: Parents' / Guardian's Information - HIPANAO SOLUTIONS -->
              <div class="tab-pane" id="guardian">
                <h6 class="text-muted"><i class="fa fa-male mr-1"></i> Father's Information</h6>
                <table class="table table-bordered">
                  <tr>
                    <th style="width:30%">Father's Name</th>
                    <td><?php echo isset($student->FATHER_NAME) ? htmlspecialchars($student->FATHER_NAME) : ''; ?></td>
                  </tr>
                  <tr>
                    <th>Contact No.</th>
                    <td><?php echo isset($student->FATHER_CONTACT) ? htmlspecialchars($student->FATHER_CONTACT) : ''; ?></td>
                  </tr>
                  <tr>
                    <th>Email</th>
                    <td><?php echo isset($student->FATHER_EMAIL) ? htmlspecialchars($student->FATHER_EMAIL) : ''; ?></td>
                  </tr>
                  <tr>
                    <th>Occupation</th>
                    <td><?php echo isset($student->FATHER_OCCUPATION) ? htmlspecialchars($student->FATHER_OCCUPATION) : ''; ?></td>
                  </tr>
                  <tr>
                    <th>Deceased?</th>
                    <td><?php echo isset($student->FATHER_DECEASED) ? htmlspecialchars($student->FATHER_DECEASED) : 'No'; ?></td>
                  </tr>
                </table>

                <h6 class="text-muted"><i class="fa fa-female mr-1"></i> Mother's Information</h6>
                <table class="table table-bordered">
                  <tr>
                    <th style="width:30%">Mother's Maiden Name</th>
                    <td><?php echo isset($student->MOTHER_NAME) ? htmlspecialchars($student->MOTHER_NAME) : ''; ?></td>
                  </tr>
                  <tr>
                    <th>Contact No.</th>
                    <td><?php echo isset($student->MOTHER_CONTACT) ? htmlspecialchars($student->MOTHER_CONTACT) : ''; ?></td>
                  </tr>
                  <tr>
                    <th>Email</th>
                    <td><?php echo isset($student->MOTHER_EMAIL) ? htmlspecialchars($student->MOTHER_EMAIL) : ''; ?></td>
                  </tr>
                  <tr>
                    <th>Occupation</th>
                    <td><?php echo isset($student->MOTHER_OCCUPATION) ? htmlspecialchars($student->MOTHER_OCCUPATION) : ''; ?></td>
                  </tr>
                  <tr>
                    <th>Deceased?</th>
                    <td><?php echo isset($student->MOTHER_DECEASED) ? htmlspecialchars($student->MOTHER_DECEASED) : 'No'; ?></td>
                  </tr>
                </table>

                <h6 class="text-muted"><i class="fa fa-users mr-1"></i> Guardian's Information</h6>
                <table class="table table-bordered">
                  <tr>
                    <th style="width:30%">Guardian's Name</th>
                    <td><?php echo isset($student->GUARDIAN_NAME) ? htmlspecialchars($student->GUARDIAN_NAME) : ''; ?></td>
                  </tr>
                  <tr>
                    <th>Relationship</th>
                    <td><?php echo isset($student->GUARDIAN_RELATIONSHIP) ? htmlspecialchars($student->GUARDIAN_RELATIONSHIP) : ''; ?></td>
                  </tr>
                  <tr>
                    <th>Contact No.</th>
                    <td><?php echo isset($student->GUARDIAN_CONTACT) ? htmlspecialchars($student->GUARDIAN_CONTACT) : ''; ?></td>
                  </tr>
                  <tr>
                    <th>Email</th>
                    <td><?php echo isset($student->GUARDIAN_EMAIL) ? htmlspecialchars($student->GUARDIAN_EMAIL) : ''; ?></td>
                  </tr>
                  <tr>
                    <th>Address</th>
                    <td><?php echo isset($student->GUARDIAN_ADDRESS) ? htmlspecialchars($student->GUARDIAN_ADDRESS) : ''; ?></td>
                  </tr>
                  <tr>
                    <th>Civil Status</th>
                    <td><?php echo isset($student->CIVIL_STATUS) ? htmlspecialchars($student->CIVIL_STATUS) : 'Single'; ?></td>
                  </tr>
                </table>
              </div>
              <!-- /.tab-pane -->

              <!-- TAB 3: Subjects taken + tuition breakdown, para agad
                   makita kung ano ang kinuha ng estudyante at bayad na ba
                   sya sa lahat. -->
              <?php if ($enroll): ?>
              <div class="tab-pane" id="subjects">
                <p class="text-muted">
                  <strong><?php echo htmlspecialchars($enroll->COURSE_CODE); ?></strong>
                  &middot; <?php echo htmlspecialchars($enroll->SCHOOL_YEAR.' / '.$enroll->SEMESTER); ?>
                  &middot; <?php echo hipanao_badge($enroll->STATUS); ?>
                </p>
                <table class="table table-bordered table-sm">
                  <thead class="thead-light">
                    <tr>
                      <th>Code</th>
                      <th>Subject</th>
                      <th>Type</th>
                      <th>Units</th>
                      <th class="text-right">Amount</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (count($viewSubjects) < 1): ?>
                      <tr><td colspan="5" class="text-muted text-center">No subjects taken yet for this enrollment.</td></tr>
                    <?php else: foreach ($viewSubjects as $vs):
                        $vsType = ($vs->SUBJECT_TYPE == 'Minor') ? 'Minor' : 'Major';
                        $vsRate = ($vsType == 'Minor') ? MINOR_UNIT_RATE : MAJOR_UNIT_RATE;
                    ?>
                      <tr>
                        <td><?php echo htmlspecialchars($vs->SUBJECT_CODE); ?></td>
                        <td><?php echo htmlspecialchars($vs->SUBJECT_NAME); ?></td>
                        <td><?php echo hipanao_badge($vsType); ?></td>
                        <td><?php echo (int)$vs->UNITS; ?></td>
                        <td class="text-right">&#8369;<?php echo number_format($vs->UNITS * $vsRate, 2); ?></td>
                      </tr>
                    <?php endforeach; endif; ?>
                  </tbody>
                  <?php if (count($viewSubjects) > 0): ?>
                  <tfoot>
                    <tr class="table-active">
                      <th colspan="4" class="text-right">Total Tuition</th>
                      <th class="text-right">&#8369;<?php echo number_format($viewTuition['total_amount'], 2); ?></th>
                    </tr>
                  </tfoot>
                  <?php endif; ?>
                </table>
              </div>
              <?php endif; ?>
              <!-- /.tab-pane -->

              <!-- TAB 4: Grades - lahat ng na-encode na grade ng
                   estudyanteng ito, kahit anong term pa - HIPANAO SOLUTIONS -->
              <?php if (count($viewGrades) > 0): ?>
              <div class="tab-pane" id="grades">
                <table class="table table-bordered table-sm">
                  <thead class="thead-light">
                    <tr>
                      <th>School Year</th>
                      <th>Semester</th>
                      <th>Code</th>
                      <th>Subject</th>
                      <th>Grade</th>
                      <th>Remarks</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($viewGrades as $vg): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($vg->SCHOOL_YEAR); ?></td>
                      <td><?php echo htmlspecialchars($vg->SEMESTER); ?></td>
                      <td><?php echo htmlspecialchars($vg->SUBJECT_CODE); ?></td>
                      <td><?php echo htmlspecialchars($vg->SUBJECT_NAME); ?></td>
                      <td><?php echo ($vg->GRADE === null) ? '<span class="text-muted">-</span>' : htmlspecialchars($vg->GRADE); ?></td>
                      <td><?php echo $vg->REMARKS ? hipanao_badge($vg->REMARKS) : '<span class="text-muted">-</span>'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
              <?php endif; ?>
              <!-- /.tab-pane -->

            </div>
            <!-- /.tab-content -->
          </div><!-- /.card-body -->
        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->

  <?php endif; ?>

  </div><!-- /.container-fluid -->
</section>
<!-- /.content -->