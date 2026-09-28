<?php

require_once("../../include/initialize.php");

$view = (isset($_GET['view']) && $_GET['view'] != '') ? $_GET['view'] : '';
$title="Schedule Day Module";
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
        var t = $('#tblscheduleday').DataTable( {
            "processing":true,
            "serverSide":true,
            "order":[],
            "ajax":{
              url:"<?php echo WEB_ROOT; ?>module/scheduleday/scheduleday_ajax.php",
              type:"POST"
            },
            "columnDefs": [ {
                "searchable": true,
                "orderable": true,
                "targets": 1
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

        // SANTE SOLUTIONS - Edit Schedule Day
        $(document).on('click', '.editEntry', function(){
            var SCHEDULEDAY_ID = $(this).attr("SCHEDULEDAY_ID");
            $.ajax({
                url:"<?php echo WEB_ROOT; ?>module/scheduleday/scheduleday_ajax.php",
                method:"POST",
                data:{SCHEDULEDAY_ID:SCHEDULEDAY_ID},
                dataType:"json",
                success:function(data)
                {
                    $('#editEntry').modal('show');
                    $('#SCHEDULEDAY_ID').val(data.SCHEDULEDAY_ID);
                    $('#DAY_NAME1').val(data.DAY_NAME);
                    $('#DESCRIPTION1').val(data.DESCRIPTION);
                }
            });
        });

        // SANTE SOLUTIONS - Delete Schedule Day
        $(document).on('click', '.deleteEntry', function(){
            var SCHEDULEDAY_ID = $(this).attr("SCHEDULEDAY_ID");
            Swal.fire({
                title: 'Delete this Schedule Day?',
                text: "This will also remove any Set Schedule entries that use this Day. This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.value) {
                    window.location.href = "<?php echo WEB_ROOT; ?>module/scheduleday/controller.php?action=delete&id=" + SCHEDULEDAY_ID;
                }
            });
        });

    });
</script>
