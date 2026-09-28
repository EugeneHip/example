<?php

require_once("../../include/initialize.php");

$title   = "History Enrollment";
$header  = "";
$content = 'list.php';

require_once("../../theme/template.php");
?>

<!-- =================================================================
     HISTORY ENROLLMENT - HIPANAO SOLUTIONS.
     Dito napupunta ang mga enrollment record na "Completed" (naipalit
     dahil nag-Register Again + nag-Enroll ulit ang estudyante sa
     bagong term) o "Dropped". Filterable "by year" (School Year) at
     "semester", may View na kasama ang huling grade ng bawat subject.
     ================================================================= -->
<script type="text/javascript">
var hyTable;
var hySyFilter  = '';
var hySemFilter = '';

$(document).ready(function() {
    hyTable = $('#tblhistoryenrollment').DataTable({
        "processing": true,
        "serverSide": true,
        "order": [],
        "ajax": {
            url: "<?php echo WEB_ROOT; ?>module/hitoryenrollment/ajax.php",
            type: "POST",
            data: function (d) {
                d.sy_filter  = hySyFilter;
                d.sem_filter = hySemFilter;
            }
        },
        "columnDefs": [
            { "orderable": false, "targets": [0, 8, 9] }
        ],
        "scrollX": true
    });

    $('#HY_SY_FILTER').on('change', function() {
        hySyFilter = $(this).val();
        hyTable.ajax.reload();
    });
    $('#HY_SEM_FILTER').on('change', function() {
        hySemFilter = $(this).val();
        hyTable.ajax.reload();
    });
});

/* ---------------- VIEW (kasama na ang huling Grade) ---------------- */
$(document).on('click', '.doViewHistoryProfile', function() {
    var eid = $(this).attr('data-eid');

    $('#HV_SUBJECTS_TBODY').html('<tr><td colspan="6" class="text-muted text-center">Loading...</td></tr>');

    $.ajax({
        url: "<?php echo WEB_ROOT; ?>module/hitoryenrollment/ajax.php",
        method: "POST",
        data: { act: 'profile', ENROLLMENT_ID: eid },
        dataType: "json",
        success: function(d) {
            if (!d.ENROLLMENT_ID) { alert('Could not load that record.'); return; }

            $('#HV_PICTURE').attr('src', d.PICTURE_URL ? d.PICTURE_URL : '<?php echo WEB_ROOT; ?>module/student/image/1.png');
            $('#HV_NAME_TEXT').text(d.FULLNAME || '-');
            $('#HV_IDNO_TEXT').text(d.IDNO || '-');
            $('#HV_STATUS_TEXT').html(d.STATUS_BADGE || '');

            $('#HV_COURSE_TEXT').text(d.COURSE_TEXT || '-');
            $('#HV_SECTION_TEXT').text(d.SECTION_NAME || '-');
            $('#HV_YEAR_TEXT').text(d.YEAR_LEVEL || '-');
            $('#HV_TERM_TEXT').text((d.SCHOOL_YEAR || '-') + ' / ' + (d.SEMESTER || '-'));

            $('#HV_SEX').html(d.SEX_BADGE || '-');
            $('#HV_BDAY').text(d.BDAY || '-');
            $('#HV_BPLACE').text(d.BPLACE || '-');
            $('#HV_AGE').text(d.AGE || '-');
            $('#HV_NATIONALITY').text(d.NATIONALITY || '-');
            $('#HV_RELIGION').text(d.RELIGION || '-');

            $('#HV_CONTACT').text(d.CONTACT_NO || '-');
            $('#HV_EMAIL').text(d.EMAIL || '-');
            $('#HV_ADDRESS').text(d.HOME_ADD || '-');

            var rows = '';
            if (!d.subjects || d.subjects.length === 0) {
                rows = '<tr><td colspan="6" class="text-muted text-center">No subjects taken for this enrollment.</td></tr>';
            } else {
                $.each(d.subjects, function(i, s) {
                    var gradeText   = (s.GRADE === null || s.GRADE === undefined) ? '<span class="text-muted">-</span>' : s.GRADE;
                    var remarksText = s.REMARKS ? s.REMARKS : '<span class="text-muted">-</span>';
                    rows += '<tr>' +
                        '<td>' + s.SUBJECT_CODE + '</td>' +
                        '<td>' + s.SUBJECT_NAME + '</td>' +
                        '<td>' + (s.SUBJECT_TYPE || 'Major') + '</td>' +
                        '<td>' + s.UNITS + '</td>' +
                        '<td>' + gradeText + '</td>' +
                        '<td>' + remarksText + '</td>' +
                        '</tr>';
                });
            }
            $('#HV_SUBJECTS_TBODY').html(rows);

            $('#viewHistoryModal').modal('show');
        },
        error: function() { alert('Could not load that record.'); }
    });
});

/* ---------------- DELETE ---------------- */
$(document).on('click', '.doDeleteHistory', function() {
    var eid = $(this).attr('data-eid');
    var name = $(this).attr('data-name');

    Swal.fire({
        title: 'Delete this history record?',
        text: "This will permanently delete " + (name || 'this record') + " together with its subjects and grades. This cannot be undone.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.value) {
            window.location.href = "<?php echo WEB_ROOT; ?>module/hitoryenrollment/controller.php?action=delete&id=" + eid;
        }
    });
});
</script>
