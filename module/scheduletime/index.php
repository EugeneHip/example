<?php

require_once("../../include/initialize.php");

$view = (isset($_GET['view']) && $_GET['view'] != '') ? $_GET['view'] : '';
$title="Schedule Time Module";
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
        var t = $('#tblscheduletime').DataTable( {
            "processing":true,
            "serverSide":true,
            "order":[],
            "ajax":{
              url:"<?php echo WEB_ROOT; ?>module/scheduletime/scheduletime_ajax.php",
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

        // SANTE SOLUTIONS - Edit Schedule Time
        $(document).on('click', '.editEntry', function(){
            var SCHEDULETIME_ID = $(this).attr("SCHEDULETIME_ID");
            $.ajax({
                url:"<?php echo WEB_ROOT; ?>module/scheduletime/scheduletime_ajax.php",
                method:"POST",
                data:{SCHEDULETIME_ID:SCHEDULETIME_ID},
                dataType:"json",
                success:function(data)
                {
                    $('#editEntry').modal('show');
                    $('#SCHEDULETIME_ID').val(data.SCHEDULETIME_ID);
                    $('#TIME_START1').val(data.TIME_START);
                    $('#TIME_END1').val(data.TIME_END);
                    $('#DESCRIPTION1').val(data.DESCRIPTION);
                }
            });
        });

        // SANTE SOLUTIONS - Delete Schedule Time
        $(document).on('click', '.deleteEntry', function(){
            var SCHEDULETIME_ID = $(this).attr("SCHEDULETIME_ID");
            Swal.fire({
                title: 'Delete this Schedule Time?',
                text: "This will also remove any Set Schedule entries that use this Time. This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.value) {
                    window.location.href = "<?php echo WEB_ROOT; ?>module/scheduletime/controller.php?action=delete&id=" + SCHEDULETIME_ID;
                }
            });
        });

    });
</script>
