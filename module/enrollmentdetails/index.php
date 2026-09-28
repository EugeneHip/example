<?php

require_once("../../include/initialize.php");

$title   = "Enrollment Details";
$header  = "";
$content = 'list.php';

require_once("../../theme/template.php");
?>

<!-- =================================================================
     ENROLLMENT DETAILS - HIPANAO SOLUTIONS.
     Isang row na lang bawat estudyante/enrollment (kagaya ng Student
     at Enrollment modules) - hindi na paulit-ulit ang pangalan niya
     kada subject. Ang View at ang Manage Subjects ay pareho nang
     nangyayari dito mismo sa page (modal) - hindi na sya papunta pa
     sa Student module.
     ================================================================= -->
<script type="text/javascript">
var edTable;

$(document).ready(function() {
    edTable = $('#tblenrollmentdetails').DataTable({
        "processing": true,
        "serverSide": true,
        "scrollX": true,
        "order": [],
        "ajax": {
            url: "<?php echo WEB_ROOT; ?>module/enrollmentdetails/ajax.php",
            type: "POST",
            data: function (d) { d.act = 'list'; }
        },
        "columnDefs": [
            { "orderable": false, "targets": [0, 8, 9] }
        ]
    });
});

/* ---------------- VIEW PROFILE (in-page, hindi na pupunta sa Student module) ---------------- */
$(document).on('click', '.doViewProfile', function() {
    var eid = $(this).attr('data-eid');

    $('#VP_SUBJECTS_TBODY').html('<tr><td colspan="5" class="text-muted text-center">Loading...</td></tr>');

    $.ajax({
        url: "<?php echo WEB_ROOT; ?>module/enrollmentdetails/ajax.php",
        method: "POST",
        data: { act: 'profile', ENROLLMENT_ID: eid },
        dataType: "json",
        success: function(d) {
            if (!d.ENROLLMENT_ID) {
                alert('Could not load that record.');
                return;
            }

            $('#VP_PICTURE').attr('src', d.PICTURE_URL ? d.PICTURE_URL : '<?php echo WEB_ROOT; ?>module/student/image/1.png');
            $('#VP_NAME_TEXT').text(d.FULLNAME || '-');
            $('#VP_IDNO_TEXT').text(d.IDNO || '-');
            $('#VP_STATUS_TEXT').html(d.STATUS_BADGE || '');

            $('#VP_COURSE_TEXT').text(d.COURSE_TEXT || '-');
            $('#VP_SECTION_TEXT').text(d.SECTION_NAME || '-');
            $('#VP_YEAR_TEXT').text(d.YEAR_LEVEL || '-');
            $('#VP_TERM_TEXT').text((d.SCHOOL_YEAR || '-') + ' / ' + (d.SEMESTER || '-'));

            $('#VP_SEX').html(d.SEX_BADGE || '-');
            $('#VP_BDAY').text(d.BDAY || '-');
            $('#VP_BPLACE').text(d.BPLACE || '-');
            $('#VP_AGE').text(d.AGE || '-');
            $('#VP_NATIONALITY').text(d.NATIONALITY || '-');
            $('#VP_RELIGION').text(d.RELIGION || '-');

            $('#VP_CONTACT').text(d.CONTACT_NO || '-');
            $('#VP_EMAIL').text(d.EMAIL || '-');
            $('#VP_ADDRESS').text(d.HOME_ADD || '-');

            var rows = '';
            if (!d.subjects || d.subjects.length === 0) {
                rows = '<tr><td colspan="5" class="text-muted text-center">No subjects taken yet for this enrollment.</td></tr>';
            } else {
                $.each(d.subjects, function(i, s) {
                    rows += '<tr>' +
                        '<td>' + s.SUBJECT_CODE + '</td>' +
                        '<td>' + s.SUBJECT_NAME + '</td>' +
                        '<td>' + (s.SUBJECT_TYPE || 'Major') + '</td>' +
                        '<td>' + s.UNITS + '</td>' +
                        '<td>' + (s.GRADE || '-') + '</td>' +
                        '</tr>';
                });
            }
            $('#VP_SUBJECTS_TBODY').html(rows);

            $('#viewProfileModal').modal('show');
        },
        error: function() { alert('Could not load that record.'); }
    });
});

/* ---------------- MANAGE SUBJECTS (checklist, kaparehong Enrollment > Subjects) ---------------- */
$(document).on('click', '.doManageSubjects', function() {
    var eid = $(this).attr('data-eid');

    $('#MS_TBODY').html('<tr><td colspan="5" class="text-muted text-center">Loading...</td></tr>');

    $.ajax({
        url: "<?php echo WEB_ROOT; ?>module/enrollmentdetails/ajax.php",
        method: "POST",
        data: { act: 'subjects_data', ENROLLMENT_ID: eid },
        dataType: "json",
        success: function(d) {
            $('#MS_EID').val(d.ENROLLMENT_ID);
            $('#MS_IDNO_TEXT').text(d.IDNO || '-');
            $('#MS_NAME_TEXT').text(d.FULLNAME || '-');
            $('#MS_COURSE_TEXT').text(d.COURSE_TEXT || '-');
            $('#MS_STATUS_TEXT').text(d.STATUS || '-');
            $('#MS_PICTURE').attr('src', d.PICTURE_URL ? d.PICTURE_URL : '<?php echo WEB_ROOT; ?>module/student/image/1.png');

            var checked = d.checked || [];
            var rows = '';
            if (!d.subjects || d.subjects.length === 0) {
                rows = '<tr><td colspan="5" class="text-muted text-center">No subjects set up yet for this course. Add some under Subjects first.</td></tr>';
            } else {
                $.each(d.subjects, function(i, s) {
                    var isChecked = checked.indexOf(s.SUBJECT_ID) !== -1;
                    rows += '<tr>' +
                        '<td><input type="checkbox" name="MS_SUBJECTS[]" value="' + s.SUBJECT_ID + '"' + (isChecked ? ' checked' : '') + '></td>' +
                        '<td>' + s.SUBJECT_CODE + '</td>' +
                        '<td>' + s.SUBJECT_NAME + '</td>' +
                        '<td>' + s.UNITS + '</td>' +
                        '<td>' + (s.YEAR_LEVEL || '-') + (s.SEMESTER ? ' / ' + s.SEMESTER : '') + '</td>' +
                        '</tr>';
                });
            }
            $('#MS_TBODY').html(rows);

            $('#manageSubjectsModal').modal('show');
        },
        error: function() { alert('Could not load subjects for that enrollment record.'); }
    });
});

/* ---------------- SELECT ALL / CLEAR ALL (Manage Subjects checklist) ---------------- */
$(document).on('click', '#MS_SELECT_ALL', function() {
    $('#MS_TBODY input[type="checkbox"]').prop('checked', true);
});
$(document).on('click', '#MS_CLEAR_ALL', function() {
    $('#MS_TBODY input[type="checkbox"]').prop('checked', false);
});

/* ---------------- LOAD SECTIONS (para sa Edit Enrollment) ---------------- */
function edLoadSections(targetId, courseId, syId, preselect, allowBlank, yearLevel) {
    var $sel = $('#' + targetId);

    if (!courseId || !syId) {
        $sel.html('<option value="">Select course and academic year first</option>');
        return;
    }

    $sel.html('<option value="">Loading...</option>');

    $.ajax({
        url: "<?php echo WEB_ROOT; ?>module/enrollmentdetails/ajax.php",
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
            $.each(rows, function(i, r) {
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

/* ---------------- REQUIREMENTS / DOCUMENTS (baka may kulang) ---------------- */
$(document).on('click', '.doRequirements', function() {
    var eid = $(this).attr('data-eid');

    $.ajax({
        url: "<?php echo WEB_ROOT; ?>module/enrollmentdetails/ajax.php",
        method: "POST",
        data: { act: 'requirements_data', ENROLLMENT_ID: eid },
        dataType: "json",
        success: function(d) {
            if (!d.ENROLLMENT_ID) { alert('Could not load requirements for that record.'); return; }

            $('#REQ_EID').val(d.ENROLLMENT_ID);
            $('#REQ_IDNO_TEXT').text(d.IDNO || '-');
            $('#REQ_NAME_TEXT').text(d.FULLNAME || '-');
            $('#REQ_STATUS_TEXT').text(d.STATUS || '-');
            $('#REQ_PICTURE').attr('src', d.PICTURE_URL ? d.PICTURE_URL : '<?php echo WEB_ROOT; ?>module/student/image/1.png');

            $('#REQD_FORM138').prop('checked', !!parseInt(d.REQ_FORM138));
            $('#REQD_GOODMORAL').prop('checked', !!parseInt(d.REQ_GOODMORAL));
            $('#REQD_BIRTHCERT').prop('checked', !!parseInt(d.REQ_BIRTHCERT));
            $('#REQD_BAPTISMAL').prop('checked', !!parseInt(d.REQ_BAPTISMAL));
            $('#REQD_ASSESSMENT').prop('checked', !!parseInt(d.REQ_ASSESSMENT));
            $('#REQD_TRANSFERCRED').prop('checked', !!parseInt(d.REQ_TRANSFERCRED));
            $('#REQD_MARRIAGECONTRACT').prop('checked', !!parseInt(d.REQ_MARRIAGECONTRACT));
            $('#REQD_2X2PICTURE').prop('checked', !!parseInt(d.REQ_2X2PICTURE));
            $('#REQD_OTHERS1').val(d.REQ_OTHERS1 || '');
            $('#REQD_OTHERS2').val(d.REQ_OTHERS2 || '');
            $('#REQD_OTHERS3').val(d.REQ_OTHERS3 || '');
            $('#REQD_NOTES').val(d.REQ_NOTES || '');

            $('#requirementsModal').modal('show');
        },
        error: function() { alert('Could not load requirements for that record.'); }
    });
});

/* ---------------- EDIT ENROLLMENT ---------------- */
$(document).on('click', '.doEditEnrollment', function() {
    var eid = $(this).attr('data-eid');

    $.ajax({
        url: "<?php echo WEB_ROOT; ?>module/enrollmentdetails/ajax.php",
        method: "POST",
        data: { act: 'row', ENROLLMENT_ID: eid },
        dataType: "json",
        success: function(d) {
            if (!d.ENROLLMENT_ID) { alert('Could not load that record.'); return; }

            $('#ED_EID').val(d.ENROLLMENT_ID);
            $('#ED_IDNO_TEXT').text(d.IDNO || '-');
            $('#ED_NAME_TEXT').text(d.FULLNAME || '-');
            $('#ED_PICTURE').attr('src', d.PICTURE_URL ? d.PICTURE_URL : '<?php echo WEB_ROOT; ?>module/student/image/1.png');

            $('#ED_SY').val(d.SY_ID);
            $('#ED_SEMESTER').val(d.SEMESTER);
            $('#ED_COURSE').val(d.COURSE_ID);
            $('#ED_YEARLEVEL').val(d.YEAR_LEVEL);
            $('#ED_CURRICULUM').val(d.CURRICULUM_YR || '');
            $('#ED_CATEGORY').val(d.CATEGORY);
            $('#ED_STATUS').val(d.STATUS);
            $('#ED_DATE_RESERVED').val(d.DATE_RESERVED || '');
            $('#ED_DATE_ENROLLED').val(d.DATE_ENROLLED || '');

            edLoadSections('ED_SECTION', d.COURSE_ID, d.SY_ID, d.SECTION_ID, true, d.YEAR_LEVEL);

            $('#editEnrollmentDetailsModal').modal('show');
        },
        error: function() { alert('Could not load that enrollment record.'); }
    });
});

/* Changing course, academic year, or year level sa Edit ay
   nagpapawalang-bisa sa napiling section - kailangan ulit i-filter. */
$(document).on('change', '#ED_COURSE, #ED_SY, #ED_YEARLEVEL', function() {
    edLoadSections('ED_SECTION', $('#ED_COURSE').val(), $('#ED_SY').val(), '', true, $('#ED_YEARLEVEL').val());
});

/* ---------------- DROP ---------------- */
$(document).on('click', '.doDropEnrollment', function() {
    var eid = $(this).attr('data-eid');
    var name = $(this).attr('data-name');

    Swal.fire({
        title: 'Drop this student?',
        text: "This will mark " + (name || 'this enrollment') + " as Dropped for the current term.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, drop',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.value) {
            window.location.href = "<?php echo WEB_ROOT; ?>module/enrollmentdetails/controller.php?action=drop&id=" + eid;
        }
    });
});

/* ---------------- REGISTER AGAIN (susunod na semester / susunod na Year Level) ---------------- */
$(document).on('click', '.doRegisterAgain', function() {
    var eid = $(this).attr('data-eid');

    $.ajax({
        url: "<?php echo WEB_ROOT; ?>module/enrollmentdetails/ajax.php",
        method: "POST",
        data: { act: 'register_info', ENROLLMENT_ID: eid },
        dataType: "json",
        success: function(d) {
            if (!d.S_ID) { alert('Could not load that record.'); return; }

            if (!d.CAN_REGISTER) {
                Swal.fire({
                    title: 'Cannot register again yet',
                    text: d.BLOCK_REASON || 'This student is not yet eligible to register again for the next term.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }

            $('#RA_SID').val(d.S_ID);
            $('#RA_EID').val(d.ENROLLMENT_ID);
            $('#RA_IDNO_TEXT').text(d.IDNO || '-');
            $('#RA_NAME_TEXT').text(d.FULLNAME || '-');
            $('#RA_PICTURE').attr('src', d.PICTURE_URL ? d.PICTURE_URL : '<?php echo WEB_ROOT; ?>module/student/image/1.png');

            $('#RA_SY').val(d.ACTIVE_SY ? d.ACTIVE_SY : '');
            $('#RA_COURSE').val(d.COURSE_ID ? d.COURSE_ID : '');
            $('#RA_CURRICULUM').val(d.ACTIVE_AY ? d.ACTIVE_AY : '');
            $('#RA_CATEGORY').val('Old');
            $('#RA_YEARLEVEL').val(d.NEXT_YEARLEVEL || '');
            $('#RA_SEMESTER').val(d.NEXT_SEMESTER || '');
            $('#RA_DATE_RESERVED').val('<?php echo date("Y-m-d"); ?>');

            $('#registerAgainModal').modal('show');
        },
        error: function() { alert('Could not load that record.'); }
    });
});
</script>
