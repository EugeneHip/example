<?php

require_once("../../include/initialize.php");

$view = (isset($_GET['view']) && $_GET['view'] != '') ? $_GET['view'] : '';
 $title="Student Module"; 
 $header=$view; 

switch ($view) {
    case 'list' :
        $content    = 'list.php';       
        break;
 
    case 'add' :
        $content    = 'add.php';        
        break;
 
    case 'edit' :
        $content    = 'edit.php';       
        break;
    case 'view' :
        
        $content    = 'view.php';       
        break;
 
    default :
        
        $content    = 'list.php';       
}

require_once ("../../theme/template.php");
 
?>

<!-- STEP 2: DataTable setup - para gumana ang listahan (search/sort/paging galing ajax.php) -->
 <script type="text/javascript">
        $(document).ready(function() {
            var t = $('#tblstudent').DataTable( {
            "processing":true,
            "serverSide":true,
            "order":[],
            "ajax":{
              url:"<?php echo WEB_ROOT; ?>module/student/ajax.php",
              type:"POST"
            },
                "columnDefs": [ {
                    "searchable": true,
                    "orderable": true,
                    "targets": 1
                } ],
                //vertical scroll
                 "scrollY":        "400px",
                "scrollCollapse": true,
                //ordering start at column 2
               "order": [[ 2, 'asc' ]]
            } );
 
                t.on( 'order.dt search.dt', function () {
                t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                } );
            } ).draw();
         
        });
    </script>

<!-- STEP 3: Datetimepicker - para sa mga date field -->
           <script type="text/javascript">
            $(function () {
                $('#reservationdate').datetimepicker({
                    format: 'L'
                });
            });
        </script>
        <script type="text/javascript">
          // STEP 4: Photo preview - ipinapakita agad ang napiling litrato bago pa i-submit
          $(document).ready( function() {
            $(document).on('change', '.student-photo-input', function() {
                var input = this;
                var $input = $(this);
                var fileName = $input.val() ? $input.val().replace(/\\/g, '/').replace(/.*\//, '') : '';
 
                $input.next('.custom-file-label').text(fileName ? fileName : 'Choose photo...');
 
                var preview = $input.data('preview');
                if (preview && input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $(preview).attr('src', e.target.result);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            });
          });
        </script>
        <script type="text/javascript">
          // STEP 4b: Auto-compute Age from Birthday - awtomatikong nalalagay
          // ang Age (read-only) pag pinili ang Birthday, both sa Add at
          // Edit form. Hindi na kailangang i-type pa manually ang Age.
          function computeAgeFromBday(bday) {
              if (!bday) { return ''; }
              var parts = bday.split('-');
              if (parts.length !== 3) { return ''; }

              var birth = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
              var today = new Date();
              if (isNaN(birth.getTime()) || birth > today) { return ''; }

              var age = today.getFullYear() - birth.getFullYear();
              var m = today.getMonth() - birth.getMonth();
              if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) { age--; }

              return age >= 0 ? age : '';
          }

          $(document).on('change', '#BDAY', function() {
              $('#AGE').val(computeAgeFromBday($(this).val()));
          });
          $(document).on('change', '#BDAY1', function() {
              $('#AGE1').val(computeAgeFromBday($(this).val()));
          });
        </script>
        <script type="text/javascript">
          // STEP 5: Reset Add modal - laging blangko ang laman pag bubukas
          $('#AddNewEntry').on('show.bs.modal', function () {
              var $form = $(this).find('form')[0];
              if ($form) { $form.reset(); }
              $('#PICTURE').next('.custom-file-label').text('Choose photo...');
              $('#img-upload-add').attr('src', '<?php echo WEB_ROOT; ?>module/student/image/1.png');
          });
        </script>
<!-- STEP 6: Populate Edit modal - kinukuha via AJAX ang datos ng estudyanteng ie-edit -->
<script type="text/javascript">
  $(document).on('click', '.editEntry', function(){
    var uid = $(this).attr("UID");
    $.ajax({
      url:"<?php echo WEB_ROOT; ?>module/student/ajax.php",
      method:"POST",
      data:{UID:uid},
      dataType:"json",
      success:function(data)
      {
       $('#UID').val(data.UID);
       $('#IDNO1').val(data.IDNO);
       $('#LRNNO1').val(data.LRNNO ? data.LRNNO : '');
       $('#FNAME1').val(data.FNAME);
       $('#MNAME1').val(data.MNAME);
       $('#LNAME1').val(data.LNAME);
 
       /* Gender: the stored value has to match one of the <option value>
          entries exactly, otherwise the select stays on the placeholder.
          Old rows may hold junk like "Select Gen", so fall back to blank. */
       var sex = data.SEX ? $.trim(data.SEX) : '';
       if (sex !== 'Male' && sex !== 'Female') { sex = ''; }
       $('#SEX1').val(sex);
 
       /* Date input needs YYYY-MM-DD; ajax.php already normalizes it. */
       $('#BDAY1').val(data.BDAY ? data.BDAY : '');
 
       $('#AGE1').val(data.AGE ? data.AGE : '');
       $('#BPLACE1').val(data.BPLACE ? data.BPLACE : '');
       $('#NATIONALITY1').val(data.NATIONALITY ? data.NATIONALITY : '');
       $('#RELIGION1').val(data.RELIGION ? data.RELIGION : '');
       $('#COURSE_ID1').val(data.COURSE_ID ? data.COURSE_ID : '');
       $('#STATUS1').val(data.STATUS ? data.STATUS : 'Active');
       $('#CONTACT_NO1').val(data.CONTACT_NO ? data.CONTACT_NO : '');
       $('#EMAIL1').val(data.EMAIL ? data.EMAIL : '');
       $('#HOME_ADD1').val(data.HOME_ADD ? data.HOME_ADD : '');
       $('#CONTACTPERSON1').val(data.CONTACTPERSON ? data.CONTACTPERSON : '');
       $('#ACC_PASSWORD1').val('');

       /* Parents' / Guardian's Information - HIPANAO SOLUTIONS. */
       $('#CIVIL_STATUS1').val(data.CIVIL_STATUS ? data.CIVIL_STATUS : 'Single');
       $('#FATHER_NAME1').val(data.FATHER_NAME ? data.FATHER_NAME : '');
       $('#FATHER_CONTACT1').val(data.FATHER_CONTACT ? data.FATHER_CONTACT : '');
       $('#FATHER_EMAIL1').val(data.FATHER_EMAIL ? data.FATHER_EMAIL : '');
       $('#FATHER_OCCUPATION1').val(data.FATHER_OCCUPATION ? data.FATHER_OCCUPATION : '');
       $('#FATHER_DECEASED1').val(data.FATHER_DECEASED ? data.FATHER_DECEASED : 'No');
       $('#MOTHER_NAME1').val(data.MOTHER_NAME ? data.MOTHER_NAME : '');
       $('#MOTHER_CONTACT1').val(data.MOTHER_CONTACT ? data.MOTHER_CONTACT : '');
       $('#MOTHER_EMAIL1').val(data.MOTHER_EMAIL ? data.MOTHER_EMAIL : '');
       $('#MOTHER_OCCUPATION1').val(data.MOTHER_OCCUPATION ? data.MOTHER_OCCUPATION : '');
       $('#MOTHER_DECEASED1').val(data.MOTHER_DECEASED ? data.MOTHER_DECEASED : 'No');
       $('#GUARDIAN_NAME1').val(data.GUARDIAN_NAME ? data.GUARDIAN_NAME : '');
       $('#GUARDIAN_RELATIONSHIP1').val(data.GUARDIAN_RELATIONSHIP ? data.GUARDIAN_RELATIONSHIP : '');
       $('#GUARDIAN_CONTACT1').val(data.GUARDIAN_CONTACT ? data.GUARDIAN_CONTACT : '');
       $('#GUARDIAN_EMAIL1').val(data.GUARDIAN_EMAIL ? data.GUARDIAN_EMAIL : '');
       $('#GUARDIAN_ADDRESS1').val(data.GUARDIAN_ADDRESS ? data.GUARDIAN_ADDRESS : '');

       /* Other Person Supporting / Boarding - HIPANAO SOLUTIONS. */
       $('#OTHER_PERSON_SUPPORTING1').val(data.OTHER_PERSON_SUPPORTING ? data.OTHER_PERSON_SUPPORTING : '');
       $('input[name="IS_BOARDING1"][value="' + (data.IS_BOARDING === 'Yes' ? 'Yes' : 'No') + '"]').prop('checked', true);
       $('input[name="WITH_FAMILY1"][value="' + (data.WITH_FAMILY === 'No' ? 'No' : 'Yes') + '"]').prop('checked', true);
       $('#BOARDING_ADDRESS1').val(data.BOARDING_ADDRESS ? data.BOARDING_ADDRESS : '');

       /* Education - HIPANAO SOLUTIONS. */
       $('#ELEM_SCHOOL1').val(data.ELEM_SCHOOL ? data.ELEM_SCHOOL : '');
       $('#ELEM_ADDRESS1').val(data.ELEM_ADDRESS ? data.ELEM_ADDRESS : '');
       $('#ELEM_YEAR1').val(data.ELEM_YEAR ? data.ELEM_YEAR : '');
       $('#SEC_SCHOOL1').val(data.SEC_SCHOOL ? data.SEC_SCHOOL : '');
       $('#SEC_ADDRESS1').val(data.SEC_ADDRESS ? data.SEC_ADDRESS : '');
       $('#SEC_YEAR1').val(data.SEC_YEAR ? data.SEC_YEAR : '');
       $('#COLLEGE_SCHOOL1').val(data.COLLEGE_SCHOOL ? data.COLLEGE_SCHOOL : '');
       $('#COLLEGE_ADDRESS1').val(data.COLLEGE_ADDRESS ? data.COLLEGE_ADDRESS : '');
       $('#COLLEGE_YEAR1').val(data.COLLEGE_YEAR ? data.COLLEGE_YEAR : '');
       $('#VOC_SCHOOL1').val(data.VOC_SCHOOL ? data.VOC_SCHOOL : '');
       $('#VOC_ADDRESS1').val(data.VOC_ADDRESS ? data.VOC_ADDRESS : '');
       $('#VOC_YEAR1').val(data.VOC_YEAR ? data.VOC_YEAR : '');
       $('#OTHERS_SCHOOL1').val(data.OTHERS_SCHOOL ? data.OTHERS_SCHOOL : '');

       /* HIPANAO SOLUTIONS - Requirements checklist ay hindi na dito
          pina-populate - sa module/enrollment na lang ito. */

       /* The file input can't be pre-filled (browsers won't allow it), so
          just reset its label and show the student's current photo, or the
          placeholder if they don't have one yet. */
       $('#PICTURE1').val('');
       $('#PICTURE1').next('.custom-file-label').text('Choose new photo...');
       $('#img-upload-edit').attr('src', data.PICTURE_URL ? data.PICTURE_URL : '<?php echo WEB_ROOT; ?>module/student/image/1.png');
 
       $('#editEntry').modal('show');
      }
    })
  });
</script>
 
<script type="text/javascript">
// STEP 7: Populate Register modal - kinukuha ang datos para sa pag-register ng slot (Stage 1)
$(document).on('click', '.registerEntry', function(){
  var uid = $(this).attr("UID");
 
  $.ajax({
    url:"<?php echo WEB_ROOT; ?>module/student/ajax.php",
    method:"POST",
    data:{act:'register_info', UID:uid},
    dataType:"json",
    success:function(data)
    {
      $('#R_SID').val(data.S_ID);
      $('#R_IDNO_TEXT').text(data.IDNO ? data.IDNO : '-');
      $('#R_NAME_TEXT').text(data.FULLNAME ? data.FULLNAME : '-');
      $('#R_PICTURE').attr('src', data.PICTURE_URL ? data.PICTURE_URL : '<?php echo WEB_ROOT; ?>module/student/image/1.png');
 
      /* Reset first so a previous student's picks never carry over. */
      $('#R_SEMESTER').val('');
      $('#R_YEARLEVEL').val('');
 
      /* Sensible defaults: active school year, the course already on the
         student record, and the AY label as the curriculum year. */
      $('#R_SY').val(data.ACTIVE_SY ? data.ACTIVE_SY : '');
      $('#R_COURSE').val(data.COURSE_ID ? data.COURSE_ID : '');
      $('#R_CURRICULUM').val(data.ACTIVE_AY ? data.ACTIVE_AY : '');
      $('#R_CATEGORY').val(data.SUGGEST_CATEGORY ? data.SUGGEST_CATEGORY : 'New');
 
      $('#registerEntry').modal('show');
    },
    error:function()
    {
      alert('Could not load the student record.');
    }
  });
});
</script>

<script type="text/javascript">
// STEP 7a-2: Create Login Account (SweetAlert2) - ginagawa ng
// Administrator ang tblusers account (TYPE = 'Student') ng estudyanteng
// ito para makapag-login na siya. Gumagana ito kahit anong pinagmulan
// ng student record - self-register (register.php) man o dinagdag
// mismo ng staff/admin dito sa module/student.
$(document).on('click', '.createAccountEntry', function(){
  var uid = $(this).attr("UID");
  Swal.fire({
    title: 'Create Login Account?',
    text: "Gagawa ito ng account (module/user - Manage User Accounts) para makapag-login na ang estudyanteng ito. Kung wala pang password na na-set noong pag-register, ID No. niya ang gagawing default password.",
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes, create account',
    cancelButtonText: 'Cancel'
  }).then((result) => {
    if (result.value) {
      window.location.href = "<?php echo WEB_ROOT; ?>module/student/controller.php?action=confirmaccount&id=" + uid;
    }
  });
});
</script>

<script type="text/javascript">
// STEP 7b: Delete confirmation (SweetAlert2) - kaparehong disenyo ng
// ibang module (course, subject) para consistent ang confirmation dialog.
$(document).on('click', '.deleteEntry', function(){
  var uid = $(this).attr("UID");
  Swal.fire({
    title: 'Delete Student Record?',
    text: "This action cannot be undone. Are you sure you want to delete this student record?",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, delete it',
    cancelButtonText: 'Cancel'
  }).then((result) => {
    if (result.value) {
      window.location.href = "<?php echo WEB_ROOT; ?>module/student/controller.php?action=delete&id=" + uid;
    }
  });
});
</script>
