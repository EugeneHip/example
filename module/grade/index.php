<?php

require_once("../../include/initialize.php");

$title   = "Grades";
$header  = "";
$content = 'list.php';

require_once("../../theme/template.php");
?>

<!-- =================================================================
     GRADES - HIPANAO SOLUTIONS.
     Hindi na generic "isang record sa isang pagkakataon" na CRUD ito.
     Ang listahan dito ay estudyante (bawat enrollment), may "Grade"
     button; pag-tap, lalabas LAHAT ng subject na kinuha niya sa
     enrollment na iyon, tapos lagyan ng grade bawat isa nang sabay.
     ================================================================= -->
<script type="text/javascript">
var gradeTable;

var gradeHistoryTable;

$(document).ready(function() {
    gradeTable = $('#tblgradestudents').DataTable({
        "processing": true,
        "serverSide": true,
        "scrollX": true,
        "order": [],
        "ajax": {
            url: "<?php echo WEB_ROOT; ?>module/grade/ajax.php",
            type: "POST",
            data: function (d) { d.act = 'list'; }
        },
        "columnDefs": [
            { "orderable": false, "targets": [6] }
        ]
    });

    gradeHistoryTable = $('#tblgradehistory').DataTable({
        "processing": true,
        "serverSide": true,
        "scrollX": true,
        "order": [],
        "ajax": {
            url: "<?php echo WEB_ROOT; ?>module/grade/ajax.php",
            type: "POST",
            data: function (d) { d.act = 'history_list'; }
        },
        "columnDefs": [
            { "orderable": false, "targets": [0, 6, 7, 8] }
        ]
    });
});

function openGradeModal(eid) {
    $('#GRADE_SUBJECTS_TBODY').html('<tr><td colspan="4" class="text-muted text-center">Loading...</td></tr>');

    $.ajax({
        url: "<?php echo WEB_ROOT; ?>module/grade/ajax.php",
        method: "POST",
        data: { act: 'subjects', ENROLLMENT_ID: eid },
        dataType: "json",
        success: function(d) {
            if (!d.ENROLLMENT_ID) {
                alert('Could not load that enrollment record.');
                return;
            }

            $('#GRADE_EID').val(d.ENROLLMENT_ID);
            $('#GRADE_IDNO_TEXT').text(d.IDNO || '-');
            $('#GRADE_NAME_TEXT').text(d.FULLNAME || '-');
            $('#GRADE_COURSE_TEXT').text(d.COURSE_TEXT || '-');
            $('#GRADE_TERM_TEXT').text(d.TERM_TEXT || '-');
            $('#GRADE_PICTURE').attr('src', d.PICTURE_URL ? d.PICTURE_URL : '<?php echo WEB_ROOT; ?>module/student/image/1.png');

            var rows = '';
            if (!d.subjects || d.subjects.length === 0) {
                rows = '<tr><td colspan="4" class="text-muted text-center">No subjects taken yet for this enrollment.</td></tr>';
            } else {
                $.each(d.subjects, function(i, s) {
                    var remarksOptions = '<option value="">-- Remarks --</option>';
                    $.each(['Passed', 'Failed', 'Incomplete', 'Dropped'], function(j, r) {
                        remarksOptions += '<option value="' + r + '"' + (s.REMARKS === r ? ' selected' : '') + '>' + r + '</option>';
                    });
                    rows += '<tr>' +
                        '<td>' + s.SUBJECT_CODE + '<div class="text-muted small">' + s.SUBJECT_NAME + '</div></td>' +
                        '<td>' +
                            '<input type="hidden" name="GRADE_SUBJECT_ID[]" value="' + s.SUBJECT_ID + '">' +
                            '<input type="number" step="0.01" min="1" max="5" class="form-control form-control-sm" name="GRADE_VALUE[' + s.SUBJECT_ID + ']" value="' + (s.GRADE !== null ? s.GRADE : '') + '">' +
                        '</td>' +
                        '<td>' +
                            '<select class="form-control form-control-sm" name="GRADE_REMARKS[' + s.SUBJECT_ID + ']">' + remarksOptions + '</select>' +
                        '</td>' +
                        '</tr>';
                });
            }
            $('#GRADE_SUBJECTS_TBODY').html(rows);

            $('#gradeModal').modal('show');
        },
        error: function() { alert('Could not load that enrollment record.'); }
    });
}

$(document).on('click', '.doGrade', function() {
    openGradeModal($(this).attr('EID'));
});

/* ---------------- VIEW GRADES (read-only) ---------------- */
$(document).on('click', '.doViewGrade', function() {
    var eid = $(this).attr('EID');

    $('#VG_SUBJECTS_TBODY').html('<tr><td colspan="3" class="text-muted text-center">Loading...</td></tr>');

    $.ajax({
        url: "<?php echo WEB_ROOT; ?>module/grade/ajax.php",
        method: "POST",
        data: { act: 'subjects', ENROLLMENT_ID: eid },
        dataType: "json",
        success: function(d) {
            if (!d.ENROLLMENT_ID) {
                alert('Could not load that enrollment record.');
                return;
            }

            $('#VG_IDNO_TEXT').text(d.IDNO || '-');
            $('#VG_NAME_TEXT').text(d.FULLNAME || '-');
            $('#VG_COURSE_TEXT').text(d.COURSE_TEXT || '-');
            $('#VG_TERM_TEXT').text(d.TERM_TEXT || '-');
            $('#VG_PICTURE').attr('src', d.PICTURE_URL ? d.PICTURE_URL : '<?php echo WEB_ROOT; ?>module/student/image/1.png');

            var rows = '';
            if (!d.subjects || d.subjects.length === 0) {
                rows = '<tr><td colspan="3" class="text-muted text-center">No subjects taken yet for this enrollment.</td></tr>';
            } else {
                $.each(d.subjects, function(i, s) {
                    var gradeText = (s.GRADE !== null && s.GRADE !== undefined) ? s.GRADE : '<span class="text-muted">Not yet graded</span>';
                    var remarksText = s.REMARKS ? s.REMARKS : '<span class="text-muted">-</span>';
                    rows += '<tr>' +
                        '<td>' + s.SUBJECT_CODE + '<div class="text-muted small">' + s.SUBJECT_NAME + '</div></td>' +
                        '<td>' + gradeText + '</td>' +
                        '<td>' + remarksText + '</td>' +
                        '</tr>';
                });
            }
            $('#VG_SUBJECTS_TBODY').html(rows);

            $('#viewGradeModal').modal('show');
        },
        error: function() { alert('Could not load that enrollment record.'); }
    });
});

/* ---------------- HISTORY GRADE: DELETE (grade records lang, hindi ang enrollment/history entry mismo) ---------------- */
$(document).on('click', '.doDeleteGradeHistory', function() {
    var eid = $(this).attr('data-eid');
    var name = $(this).attr('data-name');

    Swal.fire({
        title: 'Delete grade record?',
        text: "This will remove the encoded grade(s) for " + (name || 'this student') + " on this past enrollment. This cannot be undone.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.value) {
            window.location.href = "<?php echo WEB_ROOT; ?>module/grade/controller.php?action=delete_grades&id=" + eid;
        }
    });
});
</script>
