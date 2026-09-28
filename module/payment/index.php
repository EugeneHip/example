<?php

require_once("../../include/initialize.php");

$title  = "Payment";
$header = "";
$content = 'list.php';

$openEid = isset($_GET['eid']) ? intval($_GET['eid']) : 0;
$receiptOrNo = isset($_GET['receipt']) ? trim($_GET['receipt']) : '';

require_once("../../theme/template.php");
?>

<script type="text/javascript">
var paymentQueueTable;
var paymentHistoryTable;
var currentTuitionBalance = 0;
var currentEnrollmentFeePaid = false;

$(document).ready(function() {

  paymentQueueTable = $('#tblpaymentqueue').DataTable({
    "processing": true,
    "serverSide": true,
    "scrollX": true,
    "order": [],
    "ajax": {
      url: "<?php echo WEB_ROOT; ?>module/payment/ajax.php",
      type: "POST",
      data: function (d) { d.act = 'queue_list'; }
    },
    "columnDefs": [
      { "orderable": false, "targets": [10] }
    ]
  });

  paymentHistoryTable = $('#tblpaymenthistory').DataTable({
    "processing": true,
    "serverSide": true,
    "scrollX": true,
    "order": [],
    "ajax": {
      url: "<?php echo WEB_ROOT; ?>module/payment/ajax.php",
      type: "POST",
      data: function (d) { d.act = 'history_list'; }
    },
    "columnDefs": [
      { "orderable": false, "targets": [7] }
    ]
  });

  <?php if ($openEid > 0) { ?>
  openCollectPayment(<?php echo $openEid; ?>);
  <?php } ?>

  <?php if ($receiptOrNo != '') { ?>
  /* Kababayad lang - bubukas agad ang printable receipt sa bagong tab
     para dito mismong OR#. */
  window.open("<?php echo WEB_ROOT; ?>module/payment/receipt.php?or=<?php echo rawurlencode($receiptOrNo); ?>", "_blank");
  <?php } ?>
});

function pesoFmt(n) {
  n = parseFloat(n) || 0;
  return '\u20b1' + n.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

/* Recomputes the live tuition preview from whichever subject checkboxes
   are currently ticked in the modal - lets the cashier see the amount
   change as they check/uncheck subjects, before saving. */
function recalcTuitionPreview() {
  var majorAmt = 0, minorAmt = 0;
  $('#PAY_SUBJECTS_TBODY input[type=checkbox]:checked').each(function(){
    var amt = parseFloat($(this).data('amount')) || 0;
    if ($(this).data('type') === 'Minor') { minorAmt += amt; } else { majorAmt += amt; }
  });
  var total = majorAmt + minorAmt;
  $('#PAY_MAJOR_AMT').text(pesoFmt(majorAmt));
  $('#PAY_MINOR_AMT').text(pesoFmt(minorAmt));
  $('#PAY_TOTAL_AMT').text(pesoFmt(total));

  var balance = Math.max(0, total - (parseFloat($('#PAY_TUITION_PAID_HIDDEN').val()) || 0));
  currentTuitionBalance = balance;
  $('#PAY_TUITION_BALANCE_TEXT').text(pesoFmt(balance));
  $('#PAY_TUITION_AMOUNT').attr('max', balance.toFixed(2));

  recalcAmountDue();
}

/* HIPANAO SOLUTIONS - Sukli (change) calculator. "Amount Due Now" ay
   ang kabuuan ng aktwal na kokolektahin sa transaction na ito: ang
   Enrollment Fee (kung naka-check) + ang Tuition amount na ilalagay.
   Kada pagbabago dito o sa Cash Received, awtomatikong nire-recompute
   ang sukli - at kulay pula/warning kung kulang pa ang ibinigay na
   pera, para makita agad ng cashier bago pa mag-Save Payment. */
function recalcAmountDue() {
  var dueNow = 0;

  if ($('#PAY_ENROLLMENT_FEE_CHECK').is(':checked') && !$('#PAY_ENROLLMENT_FEE_CHECK').prop('disabled')) {
    dueNow += parseFloat($('#PAY_ENROLLMENT_FEE_AMOUNT_HIDDEN').val()) || 0;
  }
  dueNow += parseFloat($('#PAY_TUITION_AMOUNT').val()) || 0;

  $('#PAY_AMOUNT_DUE_TEXT').text(pesoFmt(dueNow));

  var cashReceived = parseFloat($('#PAY_CASH_RECEIVED').val());
  var hasCash = $('#PAY_CASH_RECEIVED').val() !== '' && !isNaN(cashReceived);
  var change = hasCash ? (cashReceived - dueNow) : 0;

  if (dueNow <= 0) {
    $('#PAY_CHANGE_TEXT').text(pesoFmt(0)).removeClass('text-danger').addClass('text-success');
    $('#PAY_CASH_NOTE').text('');
  } else if (!hasCash) {
    $('#PAY_CHANGE_TEXT').text(pesoFmt(0)).removeClass('text-danger').addClass('text-success');
    $('#PAY_CASH_NOTE').text('');
  } else if (change < 0) {
    $('#PAY_CHANGE_TEXT').text(pesoFmt(0)).removeClass('text-success').addClass('text-danger');
    $('#PAY_CASH_NOTE').html('<span class="text-danger"><i class="fa fa-exclamation-triangle"></i> Kulang ng ' + pesoFmt(Math.abs(change)) + ' ang ibinigay na pera.</span>');
  } else {
    $('#PAY_CHANGE_TEXT').text(pesoFmt(change)).removeClass('text-danger').addClass('text-success');
    $('#PAY_CASH_NOTE').text('');
  }
}

$(document).on('change', '#PAY_SUBJECTS_TBODY input[type=checkbox]', recalcTuitionPreview);
$(document).on('change', '#PAY_ENROLLMENT_FEE_CHECK', recalcAmountDue);
$(document).on('input change', '#PAY_TUITION_AMOUNT, #PAY_CASH_RECEIVED', recalcAmountDue);

/* Huling hakbang bago mag-submit - hindi papayagan mag-Save Payment
   kung walang laman ang Amount Due Now (walang kokolektahin) o kulang
   ang Cash Received sa Amount Due Now. */
$(document).on('submit', '#collectPaymentModal form', function(e){
  var dueNow = (parseFloat($('#PAY_ENROLLMENT_FEE_CHECK').is(':checked') && !$('#PAY_ENROLLMENT_FEE_CHECK').prop('disabled') ? $('#PAY_ENROLLMENT_FEE_AMOUNT_HIDDEN').val() : 0) || 0)
             + (parseFloat($('#PAY_TUITION_AMOUNT').val()) || 0);

  if (dueNow <= 0) { return true; } // subjects-only save, walang koleksyon - payagan pa rin (existing behavior)

  var cashReceived = parseFloat($('#PAY_CASH_RECEIVED').val());
  if ($('#PAY_CASH_RECEIVED').val() === '' || isNaN(cashReceived)) {
    e.preventDefault();
    alert('Please enter the cash received.');
    $('#PAY_CASH_RECEIVED').focus();
    return false;
  }
  if (cashReceived < dueNow) {
    e.preventDefault();
    alert('Kulang ang ibinigay na pera para sa Amount Due Now (' + pesoFmt(dueNow) + ').');
    $('#PAY_CASH_RECEIVED').focus();
    return false;
  }
});

function openCollectPayment(eid) {
  $('#PAY_SUBJECTS_TBODY').html('<tr><td colspan="6" class="text-muted text-center">Loading...</td></tr>');

  $.ajax({
    url: "<?php echo WEB_ROOT; ?>module/payment/ajax.php",
    method: "POST",
    data: { act: 'row', ENROLLMENT_ID: eid },
    dataType: "json",
    success: function(d) {
      if (!d.ENROLLMENT_ID) {
        alert('Could not load that enrollment record.');
        return;
      }

      $('#PAY_EID').val(d.ENROLLMENT_ID);
      $('#PAY_IDNO_TEXT').text(d.IDNO || '-');
      $('#PAY_NAME_TEXT').text(d.FULLNAME || '-');
      $('#PAY_COURSE_TEXT').text(d.COURSE_TEXT || '-');
      $('#PAY_TERM_TEXT').text(d.TERM_TEXT || '-');
      $('#PAY_SECTION_TEXT').text(d.SECTION_TEXT || '-');
      $('#PAY_STATUS_TEXT').html('<span class="badge badge-info">' + (d.STATUS || '-') + '</span>');
      $('#PAY_PICTURE').attr('src', d.PICTURE_URL ? d.PICTURE_URL : '<?php echo WEB_ROOT; ?>module/student/image/1.png');
      $('#PAY_OR_NO').val(d.or_no_suggestion || '');
      $('#PAY_DATE').val('<?php echo date('Y-m-d'); ?>');
      $('#PAY_TUITION_AMOUNT').val('');

      // Subject checklist
      var checked = d.checked || [];
      var rows = '';
      if (!d.subjects || d.subjects.length === 0) {
        rows = '<tr><td colspan="6" class="text-muted text-center">No subjects set up yet for this course.</td></tr>';
      } else {
        $.each(d.subjects, function(i, s){
          var isChecked = checked.indexOf(s.SUBJECT_ID) !== -1;
          var typeBadge = s.SUBJECT_TYPE === 'Minor'
            ? '<span class="badge badge-subject-minor">Minor</span>'
            : '<span class="badge badge-subject-major">Major</span>';
          rows += '<tr>' +
            '<td><input type="checkbox" name="PAY_SUBJECTS[]" value="' + s.SUBJECT_ID + '" data-amount="' + s.AMOUNT + '" data-type="' + s.SUBJECT_TYPE + '"' + (isChecked ? ' checked' : '') + '></td>' +
            '<td>' + s.SUBJECT_CODE + '</td>' +
            '<td>' + s.SUBJECT_NAME + '</td>' +
            '<td>' + typeBadge + '</td>' +
            '<td>' + s.UNITS + '</td>' +
            '<td class="text-right">' + pesoFmt(s.AMOUNT) + '</td>' +
            '</tr>';
        });
      }
      $('#PAY_SUBJECTS_TBODY').html(rows);

      // Enrollment fee block
      currentEnrollmentFeePaid = (d.enrollment_fee_paid >= d.enrollment_fee_amount);
      $('#PAY_ENROLLMENT_FEE_AMOUNT_HIDDEN').val(d.enrollment_fee_amount || 0);
      $('#PAY_ENROLLMENT_FEE_AMT').text(pesoFmt(d.enrollment_fee_amount));
      if (currentEnrollmentFeePaid) {
        $('#PAY_ENROLLMENT_FEE_STATUS').html('<span class="badge badge-status-active">Already Paid</span>');
        $('#PAY_ENROLLMENT_FEE_CHECK').prop('checked', false).prop('disabled', true);
      } else {
        $('#PAY_ENROLLMENT_FEE_STATUS').html('<span class="badge badge-status-forpayment">Unpaid - required to become Paid</span>');
        $('#PAY_ENROLLMENT_FEE_CHECK').prop('disabled', false).prop('checked', true);
      }

      // Tuition summary
      $('#PAY_TUITION_PAID_HIDDEN').val(d.tuition_paid || 0);
      $('#PAY_TUITION_PAID_TEXT').text(pesoFmt(d.tuition_paid || 0));
      $('#PAY_CASH_RECEIVED').val('');
      recalcTuitionPreview();

      $('#collectPaymentModal').modal('show');
    },
    error: function() { alert('Could not load that enrollment record.'); }
  });
}

$(document).on('click', '.doCollectPayment', function(){
  openCollectPayment($(this).attr('EID'));
});
</script>
