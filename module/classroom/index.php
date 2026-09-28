<?php

require_once("../../include/initialize.php");

$view = (isset($_GET['view']) && $_GET['view'] != '') ? $_GET['view'] : '';
$title="Classroom Module";
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
        var t = $('#tblclassroom').DataTable( {
            "processing":true,
            "serverSide":true,
            "order":[],
            "ajax":{
              url:"<?php echo WEB_ROOT; ?>module/classroom/classroom_ajax.php",
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

        // SANTE SOLUTIONS - Edit Classroom
        $(document).on('click', '.editEntry', function(){
            var CLASSROOM_ID = $(this).attr("CLASSROOM_ID");
            $.ajax({
                url:"<?php echo WEB_ROOT; ?>module/classroom/classroom_ajax.php",
                method:"POST",
                data:{CLASSROOM_ID:CLASSROOM_ID},
                dataType:"json",
                success:function(data)
                {
                    $('#editEntry').modal('show');
                    $('#CLASSROOM_ID').val(data.CLASSROOM_ID);
                    $('#CLASSROOM_NAME1').val(data.CLASSROOM_NAME);
                    $('#DESCRIPTION1').val(data.DESCRIPTION);
                    $('#STATUS1').val(data.STATUS);
                }
            });
        });

        // SANTE SOLUTIONS - Delete Classroom
        $(document).on('click', '.deleteEntry', function(){
            var CLASSROOM_ID = $(this).attr("CLASSROOM_ID");
            Swal.fire({
                title: 'Delete Classroom?',
                text: "This will also remove its saved schedules. This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.value) {
                    window.location.href = "<?php echo WEB_ROOT; ?>module/classroom/controller.php?action=delete&id=" + CLASSROOM_ID;
                }
            });
        });

    });
</script>
