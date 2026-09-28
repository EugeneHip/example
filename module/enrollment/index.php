<?php

require_once("../../include/initialize.php");

$view = (isset($_GET['view']) && $_GET['view'] != '') ? $_GET['view'] : '';
$title  = "Enrollment";
$header = $view;
$content = 'list.php';

require_once("../../theme/template.php");
?>

<script type="text/javascript">
var enrollTable;
var currentStatusFilter = '';

$(document).ready(function() {

  enrollTable = $('#tblenrollmentlist').DataTable({
    "processing": true,
    "serverSide": true,
    "scrollX": true,
    "order": [],
    "ajax": {
      url: "<?php echo WEB_ROOT; ?>module/enrollment/ajax.php",
      type: "POST",
      data: function (d) {
        /* Sent with every draw so the filter chips survive paging,
           sorting and searching. */
        d.status_filter = currentStatusFilter;
      }
    },
    "columnDefs": [
      { "orderable": false, "targets": [12] }
    ]
  });

  $('#statusFilters button').on('click', function(){
    $('#statusFilters button').removeClass('active');
    $(this).addClass('active');
    currentStatusFilter = $(this).data('filter');
    enrollTable.ajax.reload();
  });

});

/* Fills a section dropdown for a given course + academic year + year
   level. Used by the Edit modal - kapag may year level, doon lang
   muna i-filter ang mga section (1st Year lang para sa 1st Year na
   estudyante, 2nd Year lang sa 2nd Year, atbp). Walang hiwalay nang
   Sectioning modal - ang Section ay opsyonal na lang na field dito
   sa Edit Enrollment. */
function loadSections(targetId, courseId, syId, preselect, allowBlank, yearLevel) {
  var $sel = $('#' + targetId);

  if (!courseId || !syId) {
    $sel.html('<option value="">Select course and academic year first</option>');
    return;
  }

  $sel.html('<option value="">Loading...</option>');

  $.ajax({
    url: "<?php echo WEB_ROOT; ?>module/enrollment/ajax.php",
    method: "POST",
    data: { act: 'sections', COURSE_ID: courseId, SY_ID: syId, YEAR_LEVEL: yearLevel || '' },
    dataType: "json",
    success: function(rows) {
      var html = allowBlank
        ? '<option value="">Not sectioned yet</option>'
        : '<option value="">Select Section</option>';

      if (!rows || rows.length === 0) {
        $sel.html('<option value="">No section for this course, academic year, and year level</option>');
        return;
      }
      $.each(rows, function(i, r){
        html += '<option value="' + r.SECTION_ID + '">' + r.YEAR_LEVEL + ' - ' + r.SECTION_NAME + '</option>';
      });
      $sel.html(html);
      if (preselect) { $sel.val(preselect); }
    },
    error: function() {
      $sel.html('<option value="">Could not load sections</option>');
    }
  });
}

/* ---------------- STAGE 2: SUBJECTS (Assign) ---------------- */
$(document).on('click', '.doSubjects', function(){
  var eid = $(this).attr('EID');

  $('#SUB_TBODY').html('<tr><td colspan="5" class="text-muted text-center">Loading...</td></tr>');

  $.ajax({
    url: "<?php echo WEB_ROOT; ?>module/enrollment/ajax.php",
    method: "POST",
    data: { act: 'subjects_data', ENROLLMENT_ID: eid },
    dataType: "json",
    success: function(d) {
      $('#SUB_EID').val(d.ENROLLMENT_ID);
      $('#SUB_IDNO_TEXT').text(d.IDNO || '-');
      $('#SUB_NAME_TEXT').text(d.FULLNAME || '-');
      $('#SUB_COURSE_TEXT').text(d.COURSE_TEXT || '-');
      $('#SUB_STATUS_TEXT').text(d.STATUS || '-');
      $('#SUB_PICTURE').attr('src', d.PICTURE_URL ? d.PICTURE_URL : '<?php echo WEB_ROOT; ?>module/student/image/1.png');

      var checked = d.checked || [];
      var rows = '';
      if (!d.subjects || d.subjects.length === 0) {
        rows = '<tr><td colspan="5" class="text-muted text-center">No subjects set up yet for this course. Add some under Subjects first.</td></tr>';
      } else {
        $.each(d.subjects, function(i, s){
          var isChecked = checked.indexOf(s.SUBJECT_ID) !== -1;
          rows += '<tr>' +
            '<td><input type="checkbox" name="SUB_SUBJECTS[]" value="' + s.SUBJECT_ID + '"' + (isChecked ? ' checked' : '') + '></td>' +
            '<td>' + s.SUBJECT_CODE + '</td>' +
            '<td>' + s.SUBJECT_NAME + '</td>' +
            '<td>' + s.UNITS + '</td>' +
            '<td>' + (s.YEAR_LEVEL || '-') + (s.SEMESTER ? ' / ' + s.SEMESTER : '') + '</td>' +
            '</tr>';
        });
      }
      $('#SUB_TBODY').html(rows);

      $('#subjectsModal').modal('show');
    },
    error: function() { alert('Could not load subjects for that enrollment record.'); }
  });
});

/* ---------------- SELECT ALL / CLEAR ALL (Subjects checklist) ---------------- */
$(document).on('click', '#SUB_SELECT_ALL', function() {
  $('#SUB_TBODY input[type="checkbox"]').prop('checked', true);
});
$(document).on('click', '#SUB_CLEAR_ALL', function() {
  $('#SUB_TBODY input[type="checkbox"]').prop('checked', false);
});

/* ---------------- STAGE 3: DOCUMENT (Requirements) ---------------- */
$(document).on('click', '.doDocument', function(){
  var eid = $(this).attr('EID');

  $.ajax({
    url: "<?php echo WEB_ROOT; ?>module/enrollment/ajax.php",
    method: "POST",
    data: { act: 'document_data', ENROLLMENT_ID: eid },
    dataType: "json",
    success: function(d) {
      $('#DOC_EID').val(d.ENROLLMENT_ID);
      $('#DOC_IDNO_TEXT').text(d.IDNO || '-');
      $('#DOC_NAME_TEXT').text(d.FULLNAME || '-');
      $('#DOC_STATUS_TEXT').text(d.STATUS || '-');
      $('#DOC_PICTURE').attr('src', d.PICTURE_URL ? d.PICTURE_URL : '<?php echo WEB_ROOT; ?>module/student/image/1.png');

      $('#DOC_FORM138').prop('checked', !!parseInt(d.REQ_FORM138));
      $('#DOC_GOODMORAL').prop('checked', !!parseInt(d.REQ_GOODMORAL));
      $('#DOC_BIRTHCERT').prop('checked', !!parseInt(d.REQ_BIRTHCERT));
      $('#DOC_BAPTISMAL').prop('checked', !!parseInt(d.REQ_BAPTISMAL));
      $('#DOC_ASSESSMENT').prop('checked', !!parseInt(d.REQ_ASSESSMENT));
      $('#DOC_TRANSFERCRED').prop('checked', !!parseInt(d.REQ_TRANSFERCRED));
      $('#DOC_MARRIAGECONTRACT').prop('checked', !!parseInt(d.REQ_MARRIAGECONTRACT));
      $('#DOC_2X2PICTURE').prop('checked', !!parseInt(d.REQ_2X2PICTURE));
      $('#DOC_OTHERS1').val(d.REQ_OTHERS1 || '');
      $('#DOC_OTHERS2').val(d.REQ_OTHERS2 || '');
      $('#DOC_OTHERS3').val(d.REQ_OTHERS3 || '');
      $('#DOC_NOTES').val(d.REQ_NOTES || '');

      $('#documentModal').modal('show');
    },
    error: function() { alert('Could not load requirements for that enrollment record.'); }
  });
});

/* ---------------- EDIT ---------------- */
$(document).on('click', '.editEnrollment', function(){
  var eid = $(this).attr('EID');

  $.ajax({
    url: "<?php echo WEB_ROOT; ?>module/enrollment/ajax.php",
    method: "POST",
    data: { act: 'row', ENROLLMENT_ID: eid },
    dataType: "json",
    success: function(d) {
      $('#E_EID').val(d.ENROLLMENT_ID);
      $('#E_IDNO_TEXT').text(d.IDNO || '-');
      $('#E_NAME_TEXT').text(d.FULLNAME || '-');

      // Litrato ng estudyante - kaparehong logic ng Register modal sa Student module
      $('#E_PICTURE').attr('src', d.PICTURE_URL ? d.PICTURE_URL : '<?php echo WEB_ROOT; ?>module/student/image/1.png');

      $('#E_SY').val(d.SY_ID);
      $('#E_SEMESTER').val(d.SEMESTER);
      $('#E_COURSE').val(d.COURSE_ID);
      $('#E_YEARLEVEL').val(d.YEAR_LEVEL);
      $('#E_CURRICULUM').val(d.CURRICULUM_YR || '');
      $('#E_CATEGORY').val(d.CATEGORY);
      $('#E_STATUS').val(d.STATUS);
      $('#E_DATE_RESERVED').val(d.DATE_RESERVED || '');
      $('#E_DATE_ENROLLED').val(d.DATE_ENROLLED || '');

      loadSections('E_SECTION', d.COURSE_ID, d.SY_ID, d.SECTION_ID, true, d.YEAR_LEVEL);

      $('#editEnrollmentModal').modal('show');
    },
    error: function() { alert('Could not load that enrollment record.'); }
  });
});

/* Changing course, academic year, or year level in Edit invalidates
   the section list - kailangan ulit i-filter para tama ang section
   choices sa napiling year level. */
$(document).on('change', '#E_COURSE, #E_SY, #E_YEARLEVEL', function(){
  loadSections('E_SECTION', $('#E_COURSE').val(), $('#E_SY').val(), '', true, $('#E_YEARLEVEL').val());
});

/* ---------------- ENROLL (SweetAlert2 confirm) ----------------
   HIPANAO SOLUTIONS - BUG FIX: dating plain browser confirm() ang
   ginagamit dito ("localhost says..." na hindi tugma sa disenyo ng
   ibang module), ginawa na itong SweetAlert2 kaparehong porma ng
   confirmation dialog sa Student, Course, at Subject module. */
$(document).on('click', '.doEnroll', function(){
  var eid = $(this).attr('EID');
  Swal.fire({
    title: 'Enroll this student now?',
    text: "The Enrollment Form will open for printing.",
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes, enroll',
    cancelButtonText: 'Cancel'
  }).then((result) => {
    if (result.value) {
      window.location.href = "<?php echo WEB_ROOT; ?>module/enrollment/controller.php?action=enroll&id=" + eid;
    }
  });
});

/* ---------------- DELETE (SweetAlert2 confirm) ---------------- */
$(document).on('click', '.deleteEnrollment', function(){
  var eid = $(this).attr('EID');
  Swal.fire({
    title: 'Delete this enrollment record?',
    text: "This action cannot be undone.",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, delete it',
    cancelButtonText: 'Cancel'
  }).then((result) => {
    if (result.value) {
      window.location.href = "<?php echo WEB_ROOT; ?>module/enrollment/controller.php?action=delete&id=" + eid;
    }
  });
});
</script>
