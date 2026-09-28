<?php

global $mydb;
 
$regSchoolYears = array();
$mydb->setQuery("SELECT SY_ID, SCHOOL_YEAR, STATUS FROM `tblschoolyear` ORDER BY SCHOOL_YEAR DESC");
foreach ($mydb->loadResultList() as $r) { $regSchoolYears[] = $r; }
 
$regCourses = array();
$mydb->setQuery("SELECT COURSE_ID, COURSE_CODE, COURSE_NAME FROM `tblcourses` WHERE STATUS = 'Active' ORDER BY COURSE_CODE ASC");
foreach ($mydb->loadResultList() as $r) { $regCourses[] = $r; }
 
$regYearLevels = array('1st Year', '2nd Year', '3rd Year', '4th Year');
$regSemesters  = array('1st Semester', '2nd Semester', 'Summer');
$regCategories = array('New', 'Old', 'Transferee', 'Returnee', 'Shiftee');
?>

<!-- STEP 2: Listahan ng mga estudyante - laman galing sa ajax.php (DataTables) -->
 <section class="content">
      <div class="container-fluid">
         <?php check_message();  ?>
        <div class="row">
          <div class="col-12">
          
            <div class="card card-primary card-outline">
              <div class="card-header">
                <div class="hipanao-toolbar">
                  <div>
                    <h3 class="card-title mb-0"><i class="fa fa-user-graduate mr-1 text-muted"></i> List of Students</h3>
                  </div>
                  <div class="hipanao-toolbar-actions">
                    <button type="button" class="btn btn-primary btn-add-new" data-toggle="modal" data-target="#AddNewEntry"><i class="fa fa-user-plus mr-1"></i>Add New</button>
                  </div>
                </div>
              </div>
 
              <!-- /.card-header -->
              <div class="card-body">
                <?php if (!$mydb->tableExists('tblstudent')): ?>
                <div class="alert alert-warning mb-3">
                  <i class="fa fa-exclamation-triangle mr-1"></i>
                  Table <code>tblstudent</code> was not found in the database.
                  The SQL file may not have been fully imported yet. The rest of the system will still work normally.
                </div>
                <?php endif; ?>
                <table id="tblstudent" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>#</th>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Gender</th>
                    <th>Birthday</th>
                    <th>Status</th>
                    <th width="16%">Action</th>
                  </tr>
                  </thead>
                  <tbody>
                  
                  </tbody>
                  <tfoot>
                  
                  </tfoot>
                </table>
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
<!-- =================================================================
     ADD NEW STUDENT
     ================================================================= -->
<div class="modal fade" id="AddNewEntry">
  <div class="modal-dialog modal-lg">
    <form action="controller.php?action=add" method="POST" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"><i class="fa fa-user-plus"></i> &nbsp;Add New Student</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <!-- Tabs para hindi sobrang haba ang modal - HIPANAO SOLUTIONS -->
          <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item"><a class="nav-link active" href="#add-tab-basic" data-toggle="tab">Basic Info</a></li>
            <li class="nav-item"><a class="nav-link" href="#add-tab-contact" data-toggle="tab">Contact Information</a></li>
            <li class="nav-item"><a class="nav-link" href="#add-tab-guardian" data-toggle="tab">Parents' / Guardian's Information</a></li>
            <li class="nav-item"><a class="nav-link" href="#add-tab-education" data-toggle="tab">Education</a></li>
          </ul>
          <div class="tab-content">
          <div class="tab-pane active" id="add-tab-basic">
          <div class="row">

            <!-- Photo upload -->
            <div class="col-md-3">
              <div class="hipanao-photo-upload">
                <img id="img-upload-add" src="<?php echo WEB_ROOT; ?>module/student/image/1.png" alt="Student photo">
                <div class="custom-file">
                  <input type="file" name="PICTURE" id="PICTURE" class="custom-file-input student-photo-input" data-preview="#img-upload-add" accept="image/*">
                  <label class="custom-file-label" for="PICTURE">Choose photo...</label>
                </div>
              </div>
            </div>

            <!-- Basic info -->
            <div class="col-md-9">
              <div class="row">
                <div class="col-sm-4">
                  <div class="form-group">
                    <label for="IDNO" class="col-form-label col-form-label-sm">ID No.</label>
                    <input type="text" class="form-control form-control-sm" name="IDNO" id="IDNO" placeholder="Student ID No." required>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label for="LRNNO" class="col-form-label col-form-label-sm">LRN No.</label>
                    <input type="text" class="form-control form-control-sm" name="LRNNO" id="LRNNO" placeholder="LRN No.">
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label for="STATUS" class="col-form-label col-form-label-sm">Status</label>
                    <select class="form-control form-control-sm" name="STATUS" id="STATUS">
                      <option value="Active">Active</option>
                      <option value="Inactive">Inactive</option>
                      <option value="Graduated">Graduated</option>
                    </select>
                  </div>
                </div>

                <div class="col-sm-4">
                  <div class="form-group">
                    <label for="LNAME" class="col-form-label col-form-label-sm">Last Name</label>
                    <input type="text" class="form-control form-control-sm" name="LNAME" id="LNAME" placeholder="Last Name" required>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label for="FNAME" class="col-form-label col-form-label-sm">First Name</label>
                    <input type="text" class="form-control form-control-sm" name="FNAME" id="FNAME" placeholder="First Name" required>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label for="MNAME" class="col-form-label col-form-label-sm">Middle Name</label>
                    <input type="text" class="form-control form-control-sm" name="MNAME" id="MNAME" placeholder="Middle Name">
                  </div>
                </div>

                <div class="col-sm-4">
                  <div class="form-group">
                    <label for="SEX" class="col-form-label col-form-label-sm">Gender</label>
                    <select class="form-control form-control-sm" name="SEX" id="SEX" required>
                      <option value="">-- Select --</option>
                      <option value="Male">Male</option>
                      <option value="Female">Female</option>
                    </select>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label for="BDAY" class="col-form-label col-form-label-sm">Birthday</label>
                    <input type="date" class="form-control form-control-sm" name="BDAY" id="BDAY" required>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label for="AGE" class="col-form-label col-form-label-sm">Age</label>
                    <input type="number" min="1" class="form-control form-control-sm" name="AGE" id="AGE" placeholder="Age" readonly>
                  </div>
                </div>

                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="BPLACE" class="col-form-label col-form-label-sm">Birth Place</label>
                    <input type="text" class="form-control form-control-sm" name="BPLACE" id="BPLACE" placeholder="Birth Place">
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="NATIONALITY" class="col-form-label col-form-label-sm">Nationality</label>
                    <input type="text" class="form-control form-control-sm" name="NATIONALITY" id="NATIONALITY" placeholder="Nationality" value="Filipino">
                  </div>
                </div>

                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="RELIGION" class="col-form-label col-form-label-sm">Religion</label>
                    <input type="text" class="form-control form-control-sm" name="RELIGION" id="RELIGION" placeholder="Religion">
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="COURSE_ID" class="col-form-label col-form-label-sm">Course</label>
                    <select class="form-control form-control-sm" name="COURSE_ID" id="COURSE_ID">
                      <option value="">-- Select Course --</option>
                      <?php foreach ($regCourses as $c): ?>
                      <option value="<?php echo $c->COURSE_ID; ?>"><?php echo htmlspecialchars($c->COURSE_CODE.' - '.$c->COURSE_NAME); ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="CIVIL_STATUS" class="col-form-label col-form-label-sm">Civil Status</label>
                    <select class="form-control form-control-sm" name="CIVIL_STATUS" id="CIVIL_STATUS">
                      <option value="Single">Single</option>
                      <option value="Married">Married</option>
                      <option value="Widowed">Widowed</option>
                      <option value="Separated">Separated</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>
          </div>
          <!-- /#add-tab-basic -->

          <div class="tab-pane" id="add-tab-contact">
          <div class="row">
            <div class="col-sm-4">
              <div class="form-group">
                <label for="CONTACT_NO" class="col-form-label col-form-label-sm">Contact No.</label>
                <input type="text" class="form-control form-control-sm" name="CONTACT_NO" id="CONTACT_NO" placeholder="Contact No.">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label for="EMAIL" class="col-form-label col-form-label-sm">Email</label>
                <input type="email" class="form-control form-control-sm" name="EMAIL" id="EMAIL" placeholder="Email Address">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label for="CONTACTPERSON" class="col-form-label col-form-label-sm">Contact Person</label>
                <input type="text" class="form-control form-control-sm" name="CONTACTPERSON" id="CONTACTPERSON" placeholder="Guardian / Contact Person">
              </div>
            </div>
            <div class="col-sm-12">
              <div class="form-group">
                <label for="HOME_ADD" class="col-form-label col-form-label-sm">Home Address</label>
                <textarea class="form-control form-control-sm" name="HOME_ADD" id="HOME_ADD" placeholder="Complete Home Address"></textarea>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label for="ACC_PASSWORD" class="col-form-label col-form-label-sm">Portal Password <small class="text-muted">(optional)</small></label>
                <input type="password" class="form-control form-control-sm" name="ACC_PASSWORD" id="ACC_PASSWORD" placeholder="Leave blank if not needed">
              </div>
            </div>
          </div>
          </div>
          <!-- /#add-tab-contact -->

          <!-- =============================================================
               PARENTS' / GUARDIAN'S INFORMATION - HIPANAO SOLUTIONS.
               Kailangan ito para makumpleto ang Print Enrollment/Admission
               Form sa module/enrollmentdetails (kaparehong layout ng
               sample admission form).
               ============================================================= -->
          <div class="tab-pane" id="add-tab-guardian">
          <div class="row">
            <div class="col-sm-4">
              <div class="form-group">
                <label for="FATHER_NAME" class="col-form-label col-form-label-sm">Father's Name</label>
                <input type="text" class="form-control form-control-sm" name="FATHER_NAME" id="FATHER_NAME" placeholder="Father's Full Name">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label for="FATHER_CONTACT" class="col-form-label col-form-label-sm">Father's Contact No.</label>
                <input type="text" class="form-control form-control-sm" name="FATHER_CONTACT" id="FATHER_CONTACT" placeholder="Contact No.">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label for="FATHER_EMAIL" class="col-form-label col-form-label-sm">Father's Email</label>
                <input type="email" class="form-control form-control-sm" name="FATHER_EMAIL" id="FATHER_EMAIL" placeholder="Email Address">
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label for="FATHER_OCCUPATION" class="col-form-label col-form-label-sm">Father's Occupation</label>
                <input type="text" class="form-control form-control-sm" name="FATHER_OCCUPATION" id="FATHER_OCCUPATION" placeholder="Occupation">
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label for="FATHER_DECEASED" class="col-form-label col-form-label-sm">Father Deceased?</label>
                <select class="form-control form-control-sm" name="FATHER_DECEASED" id="FATHER_DECEASED">
                  <option value="No">No</option>
                  <option value="Yes">Yes</option>
                </select>
              </div>
            </div>

            <div class="col-sm-4">
              <div class="form-group">
                <label for="MOTHER_NAME" class="col-form-label col-form-label-sm">Mother's Maiden Name</label>
                <input type="text" class="form-control form-control-sm" name="MOTHER_NAME" id="MOTHER_NAME" placeholder="Mother's Maiden Name">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label for="MOTHER_CONTACT" class="col-form-label col-form-label-sm">Mother's Contact No.</label>
                <input type="text" class="form-control form-control-sm" name="MOTHER_CONTACT" id="MOTHER_CONTACT" placeholder="Contact No.">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label for="MOTHER_EMAIL" class="col-form-label col-form-label-sm">Mother's Email</label>
                <input type="email" class="form-control form-control-sm" name="MOTHER_EMAIL" id="MOTHER_EMAIL" placeholder="Email Address">
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label for="MOTHER_OCCUPATION" class="col-form-label col-form-label-sm">Mother's Occupation</label>
                <input type="text" class="form-control form-control-sm" name="MOTHER_OCCUPATION" id="MOTHER_OCCUPATION" placeholder="Occupation">
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label for="MOTHER_DECEASED" class="col-form-label col-form-label-sm">Mother Deceased?</label>
                <select class="form-control form-control-sm" name="MOTHER_DECEASED" id="MOTHER_DECEASED">
                  <option value="No">No</option>
                  <option value="Yes">Yes</option>
                </select>
              </div>
            </div>

            <div class="col-sm-4">
              <div class="form-group">
                <label for="GUARDIAN_NAME" class="col-form-label col-form-label-sm">Guardian's Name</label>
                <input type="text" class="form-control form-control-sm" name="GUARDIAN_NAME" id="GUARDIAN_NAME" placeholder="Guardian's Full Name">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label for="GUARDIAN_RELATIONSHIP" class="col-form-label col-form-label-sm">Relationship</label>
                <input type="text" class="form-control form-control-sm" name="GUARDIAN_RELATIONSHIP" id="GUARDIAN_RELATIONSHIP" placeholder="e.g. Brother, Aunt">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label for="GUARDIAN_CONTACT" class="col-form-label col-form-label-sm">Guardian's Contact No.</label>
                <input type="text" class="form-control form-control-sm" name="GUARDIAN_CONTACT" id="GUARDIAN_CONTACT" placeholder="Contact No.">
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label for="GUARDIAN_EMAIL" class="col-form-label col-form-label-sm">Guardian's Email</label>
                <input type="email" class="form-control form-control-sm" name="GUARDIAN_EMAIL" id="GUARDIAN_EMAIL" placeholder="Email Address">
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label for="GUARDIAN_ADDRESS" class="col-form-label col-form-label-sm">Guardian's Address</label>
                <input type="text" class="form-control form-control-sm" name="GUARDIAN_ADDRESS" id="GUARDIAN_ADDRESS" placeholder="Guardian's Address">
              </div>
            </div>

            <!-- Other Person Supporting / Boarding - HIPANAO SOLUTIONS -->
            <div class="col-sm-12"><hr></div>
            <div class="col-sm-6">
              <div class="form-group">
                <label for="OTHER_PERSON_SUPPORTING" class="col-form-label col-form-label-sm">Other Person Supporting</label>
                <input type="text" class="form-control form-control-sm" name="OTHER_PERSON_SUPPORTING" id="OTHER_PERSON_SUPPORTING" placeholder="Other Person Supporting the Student">
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="col-form-label col-form-label-sm d-block">Are you Boarding?</label>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="IS_BOARDING" id="IS_BOARDING_YES" value="Yes">
                  <label class="form-check-label" for="IS_BOARDING_YES">Yes</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="IS_BOARDING" id="IS_BOARDING_NO" value="No" checked>
                  <label class="form-check-label" for="IS_BOARDING_NO">No</label>
                </div>
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="col-form-label col-form-label-sm d-block">With Family?</label>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="WITH_FAMILY" id="WITH_FAMILY_YES" value="Yes" checked>
                  <label class="form-check-label" for="WITH_FAMILY_YES">Yes</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="WITH_FAMILY" id="WITH_FAMILY_NO" value="No">
                  <label class="form-check-label" for="WITH_FAMILY_NO">No</label>
                </div>
              </div>
            </div>
            <div class="col-sm-12">
              <div class="form-group">
                <label for="BOARDING_ADDRESS" class="col-form-label col-form-label-sm">Boarding Address <small class="text-muted">(kung "Yes" sa Boarding)</small></label>
                <input type="text" class="form-control form-control-sm" name="BOARDING_ADDRESS" id="BOARDING_ADDRESS" placeholder="Boarding House Address">
              </div>
            </div>
          </div>
          </div>
          <!-- /#add-tab-guardian -->

          <!-- =============================================================
               EDUCATION (Elementary / Secondary / College / Vocational)
               - HIPANAO SOLUTIONS.
               ============================================================= -->
          <div class="tab-pane" id="add-tab-education">
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label for="ELEM_SCHOOL" class="col-form-label col-form-label-sm">Elementary</label>
                <input type="text" class="form-control form-control-sm" name="ELEM_SCHOOL" id="ELEM_SCHOOL" placeholder="Elementary School Name">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label for="ELEM_ADDRESS" class="col-form-label col-form-label-sm">Address</label>
                <input type="text" class="form-control form-control-sm" name="ELEM_ADDRESS" id="ELEM_ADDRESS" placeholder="School Address">
              </div>
            </div>
            <div class="col-sm-2">
              <div class="form-group">
                <label for="ELEM_YEAR" class="col-form-label col-form-label-sm">Academic Year</label>
                <input type="text" class="form-control form-control-sm" name="ELEM_YEAR" id="ELEM_YEAR" placeholder="e.g. 2015-2016">
              </div>
            </div>

            <div class="col-sm-6">
              <div class="form-group">
                <label for="SEC_SCHOOL" class="col-form-label col-form-label-sm">Secondary</label>
                <input type="text" class="form-control form-control-sm" name="SEC_SCHOOL" id="SEC_SCHOOL" placeholder="Secondary/High School Name">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label for="SEC_ADDRESS" class="col-form-label col-form-label-sm">Address</label>
                <input type="text" class="form-control form-control-sm" name="SEC_ADDRESS" id="SEC_ADDRESS" placeholder="School Address">
              </div>
            </div>
            <div class="col-sm-2">
              <div class="form-group">
                <label for="SEC_YEAR" class="col-form-label col-form-label-sm">Academic Year</label>
                <input type="text" class="form-control form-control-sm" name="SEC_YEAR" id="SEC_YEAR" placeholder="e.g. 2021-2022">
              </div>
            </div>

            <div class="col-sm-6">
              <div class="form-group">
                <label for="COLLEGE_SCHOOL" class="col-form-label col-form-label-sm">College</label>
                <input type="text" class="form-control form-control-sm" name="COLLEGE_SCHOOL" id="COLLEGE_SCHOOL" placeholder="College Name">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label for="COLLEGE_ADDRESS" class="col-form-label col-form-label-sm">Address</label>
                <input type="text" class="form-control form-control-sm" name="COLLEGE_ADDRESS" id="COLLEGE_ADDRESS" placeholder="School Address">
              </div>
            </div>
            <div class="col-sm-2">
              <div class="form-group">
                <label for="COLLEGE_YEAR" class="col-form-label col-form-label-sm">Academic Year</label>
                <input type="text" class="form-control form-control-sm" name="COLLEGE_YEAR" id="COLLEGE_YEAR" placeholder="e.g. 2023-2024">
              </div>
            </div>

            <div class="col-sm-6">
              <div class="form-group">
                <label for="VOC_SCHOOL" class="col-form-label col-form-label-sm">Vocational</label>
                <input type="text" class="form-control form-control-sm" name="VOC_SCHOOL" id="VOC_SCHOOL" placeholder="Vocational School Name">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label for="VOC_ADDRESS" class="col-form-label col-form-label-sm">Address</label>
                <input type="text" class="form-control form-control-sm" name="VOC_ADDRESS" id="VOC_ADDRESS" placeholder="School Address">
              </div>
            </div>
            <div class="col-sm-2">
              <div class="form-group">
                <label for="VOC_YEAR" class="col-form-label col-form-label-sm">Academic Year</label>
                <input type="text" class="form-control form-control-sm" name="VOC_YEAR" id="VOC_YEAR" placeholder="e.g. 2024-2025">
              </div>
            </div>

            <div class="col-sm-6">
              <div class="form-group">
                <label for="OTHERS_SCHOOL" class="col-form-label col-form-label-sm">Others</label>
                <input type="text" class="form-control form-control-sm" name="OTHERS_SCHOOL" id="OTHERS_SCHOOL" placeholder="Other School/Training Attended">
              </div>
            </div>
          </div>
          </div>
          <!-- /#add-tab-education -->
          </div>
          <!-- /.tab-content -->

        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" name="save"><i class="fa fa-save"></i> Save changes</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- =================================================================
     EDIT STUDENT
     ================================================================= -->
<div class="modal fade" id="editEntry">
  <div class="modal-dialog modal-lg">
    <form action="controller.php?action=edit" method="POST" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"><i class="fa fa-user-edit"></i> &nbsp;Edit Student</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="UID" id="UID" value="">
          <!-- Tabs para hindi sobrang haba ang modal - HIPANAO SOLUTIONS -->
          <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item"><a class="nav-link active" href="#edit-tab-basic" data-toggle="tab">Basic Info</a></li>
            <li class="nav-item"><a class="nav-link" href="#edit-tab-contact" data-toggle="tab">Contact Information</a></li>
            <li class="nav-item"><a class="nav-link" href="#edit-tab-guardian" data-toggle="tab">Parents' / Guardian's Information</a></li>
            <li class="nav-item"><a class="nav-link" href="#edit-tab-education" data-toggle="tab">Education</a></li>
          </ul>
          <div class="tab-content">
          <div class="tab-pane active" id="edit-tab-basic">
          <div class="row">

            <!-- Photo upload -->
            <div class="col-md-3">
              <div class="hipanao-photo-upload">
                <img id="img-upload-edit" src="<?php echo WEB_ROOT; ?>module/student/image/1.png" alt="Student photo">
                <div class="custom-file">
                  <input type="file" name="PICTURE" id="PICTURE1" class="custom-file-input student-photo-input" data-preview="#img-upload-edit" accept="image/*">
                  <label class="custom-file-label" for="PICTURE1">Choose new photo...</label>
                </div>
              </div>
            </div>

            <div class="col-md-9">
              <div class="row">
                <div class="col-sm-4">
                  <div class="form-group">
                    <label for="IDNO1" class="col-form-label col-form-label-sm">ID No.</label>
                    <input type="text" class="form-control form-control-sm" name="IDNO1" id="IDNO1" placeholder="Student ID No.">
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label for="LRNNO1" class="col-form-label col-form-label-sm">LRN No.</label>
                    <input type="text" class="form-control form-control-sm" name="LRNNO1" id="LRNNO1" placeholder="LRN No.">
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label for="STATUS1" class="col-form-label col-form-label-sm">Status</label>
                    <select class="form-control form-control-sm" name="STATUS1" id="STATUS1">
                      <option value="Active">Active</option>
                      <option value="Inactive">Inactive</option>
                      <option value="Graduated">Graduated</option>
                    </select>
                  </div>
                </div>

                <div class="col-sm-4">
                  <div class="form-group">
                    <label for="LNAME1" class="col-form-label col-form-label-sm">Last Name</label>
                    <input type="text" class="form-control form-control-sm" name="LNAME1" id="LNAME1" placeholder="Last Name">
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label for="FNAME1" class="col-form-label col-form-label-sm">First Name</label>
                    <input type="text" class="form-control form-control-sm" name="FNAME1" id="FNAME1" placeholder="First Name">
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label for="MNAME1" class="col-form-label col-form-label-sm">Middle Name</label>
                    <input type="text" class="form-control form-control-sm" name="MNAME1" id="MNAME1" placeholder="Middle Name">
                  </div>
                </div>

                <div class="col-sm-4">
                  <div class="form-group">
                    <label for="SEX1" class="col-form-label col-form-label-sm">Gender</label>
                    <select class="form-control form-control-sm" name="SEX1" id="SEX1">
                      <option value="">-- Select --</option>
                      <option value="Male">Male</option>
                      <option value="Female">Female</option>
                    </select>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label for="BDAY1" class="col-form-label col-form-label-sm">Birthday</label>
                    <input type="date" class="form-control form-control-sm" name="BDAY1" id="BDAY1">
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label for="AGE1" class="col-form-label col-form-label-sm">Age</label>
                    <input type="number" min="1" class="form-control form-control-sm" name="AGE1" id="AGE1" placeholder="Age" readonly>
                  </div>
                </div>

                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="BPLACE1" class="col-form-label col-form-label-sm">Birth Place</label>
                    <input type="text" class="form-control form-control-sm" name="BPLACE1" id="BPLACE1" placeholder="Birth Place">
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="NATIONALITY1" class="col-form-label col-form-label-sm">Nationality</label>
                    <input type="text" class="form-control form-control-sm" name="NATIONALITY1" id="NATIONALITY1" placeholder="Nationality">
                  </div>
                </div>

                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="RELIGION1" class="col-form-label col-form-label-sm">Religion</label>
                    <input type="text" class="form-control form-control-sm" name="RELIGION1" id="RELIGION1" placeholder="Religion">
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="COURSE_ID1" class="col-form-label col-form-label-sm">Course</label>
                    <select class="form-control form-control-sm" name="COURSE_ID1" id="COURSE_ID1">
                      <option value="">-- Select Course --</option>
                      <?php foreach ($regCourses as $c): ?>
                      <option value="<?php echo $c->COURSE_ID; ?>"><?php echo htmlspecialchars($c->COURSE_CODE.' - '.$c->COURSE_NAME); ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="CIVIL_STATUS1" class="col-form-label col-form-label-sm">Civil Status</label>
                    <select class="form-control form-control-sm" name="CIVIL_STATUS1" id="CIVIL_STATUS1">
                      <option value="Single">Single</option>
                      <option value="Married">Married</option>
                      <option value="Widowed">Widowed</option>
                      <option value="Separated">Separated</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>
          </div>
          <!-- /#edit-tab-basic -->

          <div class="tab-pane" id="edit-tab-contact">
          <div class="row">
            <div class="col-sm-4">
              <div class="form-group">
                <label for="CONTACT_NO1" class="col-form-label col-form-label-sm">Contact No.</label>
                <input type="text" class="form-control form-control-sm" name="CONTACT_NO1" id="CONTACT_NO1" placeholder="Contact No.">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label for="EMAIL1" class="col-form-label col-form-label-sm">Email</label>
                <input type="email" class="form-control form-control-sm" name="EMAIL1" id="EMAIL1" placeholder="Email Address">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label for="CONTACTPERSON1" class="col-form-label col-form-label-sm">Contact Person</label>
                <input type="text" class="form-control form-control-sm" name="CONTACTPERSON1" id="CONTACTPERSON1" placeholder="Guardian / Contact Person">
              </div>
            </div>
            <div class="col-sm-12">
              <div class="form-group">
                <label for="HOME_ADD1" class="col-form-label col-form-label-sm">Home Address</label>
                <textarea class="form-control form-control-sm" name="HOME_ADD1" id="HOME_ADD1" placeholder="Complete Home Address"></textarea>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label for="ACC_PASSWORD1" class="col-form-label col-form-label-sm">Portal Password <small class="text-muted">(leave blank to keep current)</small></label>
                <input type="password" class="form-control form-control-sm" name="ACC_PASSWORD1" id="ACC_PASSWORD1" placeholder="Leave blank to keep current password">
              </div>
            </div>
          </div>
          </div>
          <!-- /#edit-tab-contact -->

          <!-- =============================================================
               PARENTS' / GUARDIAN'S INFORMATION - HIPANAO SOLUTIONS.
               ============================================================= -->
          <div class="tab-pane" id="edit-tab-guardian">
          <div class="row">
            <div class="col-sm-4">
              <div class="form-group">
                <label for="FATHER_NAME1" class="col-form-label col-form-label-sm">Father's Name</label>
                <input type="text" class="form-control form-control-sm" name="FATHER_NAME1" id="FATHER_NAME1" placeholder="Father's Full Name">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label for="FATHER_CONTACT1" class="col-form-label col-form-label-sm">Father's Contact No.</label>
                <input type="text" class="form-control form-control-sm" name="FATHER_CONTACT1" id="FATHER_CONTACT1" placeholder="Contact No.">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label for="FATHER_EMAIL1" class="col-form-label col-form-label-sm">Father's Email</label>
                <input type="email" class="form-control form-control-sm" name="FATHER_EMAIL1" id="FATHER_EMAIL1" placeholder="Email Address">
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label for="FATHER_OCCUPATION1" class="col-form-label col-form-label-sm">Father's Occupation</label>
                <input type="text" class="form-control form-control-sm" name="FATHER_OCCUPATION1" id="FATHER_OCCUPATION1" placeholder="Occupation">
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label for="FATHER_DECEASED1" class="col-form-label col-form-label-sm">Father Deceased?</label>
                <select class="form-control form-control-sm" name="FATHER_DECEASED1" id="FATHER_DECEASED1">
                  <option value="No">No</option>
                  <option value="Yes">Yes</option>
                </select>
              </div>
            </div>

            <div class="col-sm-4">
              <div class="form-group">
                <label for="MOTHER_NAME1" class="col-form-label col-form-label-sm">Mother's Maiden Name</label>
                <input type="text" class="form-control form-control-sm" name="MOTHER_NAME1" id="MOTHER_NAME1" placeholder="Mother's Maiden Name">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label for="MOTHER_CONTACT1" class="col-form-label col-form-label-sm">Mother's Contact No.</label>
                <input type="text" class="form-control form-control-sm" name="MOTHER_CONTACT1" id="MOTHER_CONTACT1" placeholder="Contact No.">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label for="MOTHER_EMAIL1" class="col-form-label col-form-label-sm">Mother's Email</label>
                <input type="email" class="form-control form-control-sm" name="MOTHER_EMAIL1" id="MOTHER_EMAIL1" placeholder="Email Address">
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label for="MOTHER_OCCUPATION1" class="col-form-label col-form-label-sm">Mother's Occupation</label>
                <input type="text" class="form-control form-control-sm" name="MOTHER_OCCUPATION1" id="MOTHER_OCCUPATION1" placeholder="Occupation">
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label for="MOTHER_DECEASED1" class="col-form-label col-form-label-sm">Mother Deceased?</label>
                <select class="form-control form-control-sm" name="MOTHER_DECEASED1" id="MOTHER_DECEASED1">
                  <option value="No">No</option>
                  <option value="Yes">Yes</option>
                </select>
              </div>
            </div>

            <div class="col-sm-4">
              <div class="form-group">
                <label for="GUARDIAN_NAME1" class="col-form-label col-form-label-sm">Guardian's Name</label>
                <input type="text" class="form-control form-control-sm" name="GUARDIAN_NAME1" id="GUARDIAN_NAME1" placeholder="Guardian's Full Name">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label for="GUARDIAN_RELATIONSHIP1" class="col-form-label col-form-label-sm">Relationship</label>
                <input type="text" class="form-control form-control-sm" name="GUARDIAN_RELATIONSHIP1" id="GUARDIAN_RELATIONSHIP1" placeholder="e.g. Brother, Aunt">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label for="GUARDIAN_CONTACT1" class="col-form-label col-form-label-sm">Guardian's Contact No.</label>
                <input type="text" class="form-control form-control-sm" name="GUARDIAN_CONTACT1" id="GUARDIAN_CONTACT1" placeholder="Contact No.">
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label for="GUARDIAN_EMAIL1" class="col-form-label col-form-label-sm">Guardian's Email</label>
                <input type="email" class="form-control form-control-sm" name="GUARDIAN_EMAIL1" id="GUARDIAN_EMAIL1" placeholder="Email Address">
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label for="GUARDIAN_ADDRESS1" class="col-form-label col-form-label-sm">Guardian's Address</label>
                <input type="text" class="form-control form-control-sm" name="GUARDIAN_ADDRESS1" id="GUARDIAN_ADDRESS1" placeholder="Guardian's Address">
              </div>
            </div>

            <!-- Other Person Supporting / Boarding - HIPANAO SOLUTIONS -->
            <div class="col-sm-12"><hr></div>
            <div class="col-sm-6">
              <div class="form-group">
                <label for="OTHER_PERSON_SUPPORTING1" class="col-form-label col-form-label-sm">Other Person Supporting</label>
                <input type="text" class="form-control form-control-sm" name="OTHER_PERSON_SUPPORTING1" id="OTHER_PERSON_SUPPORTING1" placeholder="Other Person Supporting the Student">
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="col-form-label col-form-label-sm d-block">Are you Boarding?</label>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="IS_BOARDING1" id="IS_BOARDING1_YES" value="Yes">
                  <label class="form-check-label" for="IS_BOARDING1_YES">Yes</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="IS_BOARDING1" id="IS_BOARDING1_NO" value="No" checked>
                  <label class="form-check-label" for="IS_BOARDING1_NO">No</label>
                </div>
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="col-form-label col-form-label-sm d-block">With Family?</label>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="WITH_FAMILY1" id="WITH_FAMILY1_YES" value="Yes" checked>
                  <label class="form-check-label" for="WITH_FAMILY1_YES">Yes</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="WITH_FAMILY1" id="WITH_FAMILY1_NO" value="No">
                  <label class="form-check-label" for="WITH_FAMILY1_NO">No</label>
                </div>
              </div>
            </div>
            <div class="col-sm-12">
              <div class="form-group">
                <label for="BOARDING_ADDRESS1" class="col-form-label col-form-label-sm">Boarding Address <small class="text-muted">(kung "Yes" sa Boarding)</small></label>
                <input type="text" class="form-control form-control-sm" name="BOARDING_ADDRESS1" id="BOARDING_ADDRESS1" placeholder="Boarding House Address">
              </div>
            </div>
          </div>
          </div>
          <!-- /#edit-tab-guardian -->

          <!-- =============================================================
               EDUCATION (Elementary / Secondary / College / Vocational)
               - HIPANAO SOLUTIONS.
               ============================================================= -->
          <div class="tab-pane" id="edit-tab-education">
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label for="ELEM_SCHOOL1" class="col-form-label col-form-label-sm">Elementary</label>
                <input type="text" class="form-control form-control-sm" name="ELEM_SCHOOL1" id="ELEM_SCHOOL1" placeholder="Elementary School Name">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label for="ELEM_ADDRESS1" class="col-form-label col-form-label-sm">Address</label>
                <input type="text" class="form-control form-control-sm" name="ELEM_ADDRESS1" id="ELEM_ADDRESS1" placeholder="School Address">
              </div>
            </div>
            <div class="col-sm-2">
              <div class="form-group">
                <label for="ELEM_YEAR1" class="col-form-label col-form-label-sm">Academic Year</label>
                <input type="text" class="form-control form-control-sm" name="ELEM_YEAR1" id="ELEM_YEAR1" placeholder="e.g. 2015-2016">
              </div>
            </div>

            <div class="col-sm-6">
              <div class="form-group">
                <label for="SEC_SCHOOL1" class="col-form-label col-form-label-sm">Secondary</label>
                <input type="text" class="form-control form-control-sm" name="SEC_SCHOOL1" id="SEC_SCHOOL1" placeholder="Secondary/High School Name">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label for="SEC_ADDRESS1" class="col-form-label col-form-label-sm">Address</label>
                <input type="text" class="form-control form-control-sm" name="SEC_ADDRESS1" id="SEC_ADDRESS1" placeholder="School Address">
              </div>
            </div>
            <div class="col-sm-2">
              <div class="form-group">
                <label for="SEC_YEAR1" class="col-form-label col-form-label-sm">Academic Year</label>
                <input type="text" class="form-control form-control-sm" name="SEC_YEAR1" id="SEC_YEAR1" placeholder="e.g. 2021-2022">
              </div>
            </div>

            <div class="col-sm-6">
              <div class="form-group">
                <label for="COLLEGE_SCHOOL1" class="col-form-label col-form-label-sm">College</label>
                <input type="text" class="form-control form-control-sm" name="COLLEGE_SCHOOL1" id="COLLEGE_SCHOOL1" placeholder="College Name">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label for="COLLEGE_ADDRESS1" class="col-form-label col-form-label-sm">Address</label>
                <input type="text" class="form-control form-control-sm" name="COLLEGE_ADDRESS1" id="COLLEGE_ADDRESS1" placeholder="School Address">
              </div>
            </div>
            <div class="col-sm-2">
              <div class="form-group">
                <label for="COLLEGE_YEAR1" class="col-form-label col-form-label-sm">Academic Year</label>
                <input type="text" class="form-control form-control-sm" name="COLLEGE_YEAR1" id="COLLEGE_YEAR1" placeholder="e.g. 2023-2024">
              </div>
            </div>

            <div class="col-sm-6">
              <div class="form-group">
                <label for="VOC_SCHOOL1" class="col-form-label col-form-label-sm">Vocational</label>
                <input type="text" class="form-control form-control-sm" name="VOC_SCHOOL1" id="VOC_SCHOOL1" placeholder="Vocational School Name">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label for="VOC_ADDRESS1" class="col-form-label col-form-label-sm">Address</label>
                <input type="text" class="form-control form-control-sm" name="VOC_ADDRESS1" id="VOC_ADDRESS1" placeholder="School Address">
              </div>
            </div>
            <div class="col-sm-2">
              <div class="form-group">
                <label for="VOC_YEAR1" class="col-form-label col-form-label-sm">Academic Year</label>
                <input type="text" class="form-control form-control-sm" name="VOC_YEAR1" id="VOC_YEAR1" placeholder="e.g. 2024-2025">
              </div>
            </div>

            <div class="col-sm-6">
              <div class="form-group">
                <label for="OTHERS_SCHOOL1" class="col-form-label col-form-label-sm">Others</label>
                <input type="text" class="form-control form-control-sm" name="OTHERS_SCHOOL1" id="OTHERS_SCHOOL1" placeholder="Other School/Training Attended">
              </div>
            </div>
          </div>
          </div>
          <!-- /#edit-tab-education -->
          </div>
          <!-- /.tab-content -->

        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" name="edit"><i class="fa fa-save"></i> Save changes</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- =================================================================
     REGISTER ENROLLMENT SLOT (Stage 1) - photo card left, form right,
     same look as the Edit Enrollment modal.
     ================================================================= -->
<div class="modal fade" id="registerEntry">
  <div class="modal-dialog modal-lg">
    <form action="controller.php?action=register" method="POST">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"><i class="fa fa-clipboard-check text-success"></i> &nbsp;Register Enrollment Slot</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">

          <input type="hidden" name="R_SID" id="R_SID" value="">

          <div class="row">
            <div class="col-md-3">
              <div class="card card-success card-outline mb-0">
                <div class="card-body box-profile text-center">
                  <img id="R_PICTURE" class="profile-user-img img-fluid img-circle"
                       src="<?php echo WEB_ROOT; ?>module/student/image/1.png" alt="Student photo">
                  <h3 class="profile-username text-center mt-2 mb-0" id="R_IDNO_TEXT" style="font-size:1rem;">-</h3>
                  <p class="text-muted text-center mb-0" id="R_NAME_TEXT">-</p>
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
                        <label for="R_SY" class="col-form-label col-form-label-sm">School Year</label>
                        <select class="form-control form-control-sm" name="R_SY" id="R_SY" required>
                          <?php foreach ($regSchoolYears as $sy) { ?>
                          <option value="<?php echo $sy->SY_ID; ?>"><?php echo htmlspecialchars($sy->SCHOOL_YEAR); ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="R_COURSE" class="col-form-label col-form-label-sm">Course</label>
                        <select class="form-control form-control-sm" name="R_COURSE" id="R_COURSE" required>
                          <?php foreach ($regCourses as $c) { ?>
                          <option value="<?php echo $c->COURSE_ID; ?>"><?php echo htmlspecialchars($c->COURSE_CODE.' - '.$c->COURSE_NAME); ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="R_YEARLEVEL" class="col-form-label col-form-label-sm">Year Level</label>
                        <select class="form-control form-control-sm" name="R_YEARLEVEL" id="R_YEARLEVEL" required>
                          <option value="">-- Select --</option>
                          <?php foreach ($regYearLevels as $yl) { ?>
                          <option value="<?php echo $yl; ?>"><?php echo $yl; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="R_SEMESTER" class="col-form-label col-form-label-sm">Semester</label>
                        <select class="form-control form-control-sm" name="R_SEMESTER" id="R_SEMESTER" required>
                          <option value="">-- Select --</option>
                          <?php foreach ($regSemesters as $sem) { ?>
                          <option value="<?php echo $sem; ?>"><?php echo $sem; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="R_CATEGORY" class="col-form-label col-form-label-sm">Category</label>
                        <select class="form-control form-control-sm" name="R_CATEGORY" id="R_CATEGORY" required>
                          <?php foreach ($regCategories as $cat) { ?>
                          <option value="<?php echo $cat; ?>"><?php echo $cat; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="R_CURRICULUM" class="col-form-label col-form-label-sm">Curriculum Yr</label>
                        <input type="text" class="form-control form-control-sm" name="R_CURRICULUM" id="R_CURRICULUM" placeholder="e.g. 2025-2026">
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="R_DATE_RESERVED" class="col-form-label col-form-label-sm">Date Reserved</label>
                        <input type="date" class="form-control form-control-sm" name="R_DATE_RESERVED" id="R_DATE_RESERVED" value="<?php echo date('Y-m-d'); ?>">
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
          <button type="submit" class="btn btn-success" name="register"><i class="fa fa-check"></i> Register Slot</button>
        </div>
      </div>
    </form>
  </div>
</div>
