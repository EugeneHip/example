<?php

require_once("../../include/initialize.php");

$view = (isset($_GET['view']) && $_GET['view'] != '') ? $_GET['view'] : '';
$title="Set Schedule Module";
$header=$view;
switch ($view) {
	case 'list' :
		$content    = 'list.php';
		break;

	default :
		$content    = 'list.php';
}
require_once ("../../theme/template.php");

?>

<script type="text/javascript">
    $(document).ready(function() {
        var t = $('#tblschedule').DataTable( {
            "processing":true,
            "serverSide":true,
            "order":[],
            "ajax":{
              url:"<?php echo WEB_ROOT; ?>module/schedule/schedule_ajax.php",
              type:"POST"
            },
            "columnDefs": [ {
                "searchable": true,
                "orderable": true,
                "targets": 1
            }, {
                "orderable": false,
                "targets": 9
            } ],
            "scrollY": "400px",
            "scrollCollapse": true,
            "order": [[ 1, 'asc' ]]
        } );

        t.on( 'order.dt search.dt', function () {
            t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                cell.innerHTML = i+1;
            } );
        } ).draw();

        // SANTE SOLUTIONS - Edit Schedule
        $(document).on('click', '.editEntry', function(){
            var SCHEDULE_ID = $(this).attr("SCHEDULE_ID");
            $.ajax({
                url:"<?php echo WEB_ROOT; ?>module/schedule/schedule_ajax.php",
                method:"POST",
                data:{SCHEDULE_ID:SCHEDULE_ID},
                dataType:"json",
                success:function(data)
                {
                    $('#editEntry').modal('show');
                    $('#SCHEDULE_ID').val(data.SCHEDULE_ID);
                    $('#SUBJECT_ID1').val(data.SUBJECT_ID);
                    $('#SCHEDULEDAY_ID1').val(data.SCHEDULEDAY_ID);
                    $('#SCHEDULETIME_ID1').val(data.SCHEDULETIME_ID);
                    $('#CLASSROOM_ID1').val(data.CLASSROOM_ID);
                    $('#INSTRUCTOR_ID1').val(data.INSTRUCTOR_ID ? data.INSTRUCTOR_ID : '');
                }
            });
        });

        // SANTE SOLUTIONS - Delete Schedule
        $(document).on('click', '.deleteEntry', function(){
            var SCHEDULE_ID = $(this).attr("SCHEDULE_ID");
            Swal.fire({
                title: 'Delete this Schedule?',
                text: "This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.value) {
                    window.location.href = "<?php echo WEB_ROOT; ?>module/schedule/controller.php?action=delete&id=" + SCHEDULE_ID;
                }
            });
        });

    });
</script>
