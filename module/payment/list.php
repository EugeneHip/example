<?php

global $mydb;
?>
<section class="content">
  <div class="container-fluid">
    <?php check_message(); ?>

    <div class="row">
      <div class="col-12">

        <div class="card card-info card-outline">
          <div class="card-header">
            <div class="hipanao-toolbar">
              <div>
                <h3 class="card-title mb-0"><i class="fa fa-money-check-alt mr-1 text-muted"></i> Payment</h3>
              </div>
            </div>
          </div>
          <div class="card-body">
            <?php if (!$mydb->tableExists('tblenrollment')): ?>
            <div class="alert alert-warning mb-3">
              <i class="fa fa-exclamation-triangle mr-1"></i>
              Table <code>tblenrollment</code> was not found in the database.
              The SQL file may not have been fully imported yet. The rest of the system will still work normally.
            </div>
            <?php endif; ?>
            <table id="tblpaymentqueue" class="table table-bordered table-striped" style="width:100%">
              <thead>
                <tr>
                  <th>#</th>
                  <th>ID No.</th>
                  <th>Student Name</th>
                  <th>Course</th>
                  <th>Section</th>
                  <th>Status</th>
                  <th>Subjects</th>
                  <th>Tuition Total</th>
                  <th>Tuition Status</th>
                  <th>Enrollment Fee</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>

        <div class="card card-secondary card-outline">
          <div class="card-header">
            <div class="hipanao-toolbar">
              <div>
                <h3 class="card-title mb-0"><i class="fa fa-receipt mr-1 text-muted"></i> Payment History</h3>
              </div>
            </div>
          </div>
          <div class="card-body">
            <?php if (!$mydb->tableExists('tblpayments')): ?>
            <div class="alert alert-warning mb-3">
              <i class="fa fa-exclamation-triangle mr-1"></i>
              Table <code>tblpayments</code> was not found in the database.
              The SQL file may not have been fully imported yet. The rest of the system will still work normally.
            </div>
            <?php endif; ?>
            <table id="tblpaymenthistory" class="table table-bordered table-striped" style="width:100%">
              <thead>
                <tr>
                  <th>#</th>
                  <th>OR #</th>
                  <th>Student Name</th>
                  <th>Type</th>
                  <th>Amount</th>
                  <th>Date Paid</th>
                  <th>Received By</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>

<!-- =================================================================
     COLLECT PAYMENT
     Kaparehong disenyo ng ibang modal sa system - plain white header,
     litrato ng estudyante sa kaliwa (col-md-3).
     ================================================================= -->
<div class="modal fade" id="collectPaymentModal">
  <div class="modal-dialog modal-xl">
    <form action="controller.php?action=pay" method="POST">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"><i class="fa fa-money-check-alt text-info"></i> &nbsp;Collect Payment</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">

          <input type="hidden" name="PAY_EID" id="PAY_EID" value="">
          <input type="hidden" id="PAY_TUITION_PAID_HIDDEN" value="0">
          <input type="hidden" id="PAY_ENROLLMENT_FEE_AMOUNT_HIDDEN" value="0">

          <div class="row">

            <!-- Litrato at pangalan ng estudyante -->
            <div class="col-md-3">
              <div class="card card-info card-outline mb-0">
                <div class="card-body box-profile text-center">
                  <img id="PAY_PICTURE"
                       class="profile-user-img img-fluid img-circle"
                       src="<?php echo WEB_ROOT; ?>module/student/image/1.png"
                       alt="Student photo">
                  <h3 class="profile-username text-center mt-2 mb-0" id="PAY_IDNO_TEXT" style="font-size:1rem;">-</h3>
                  <p class="text-muted text-center mb-1" id="PAY_NAME_TEXT">-</p>
                  <p class="text-center mb-0" id="PAY_STATUS_TEXT">-</p>
                </div>
                <ul class="list-group list-group-flush">
                  <li class="list-group-item p-2">
                    <small class="text-muted d-block">Course</small>
                    <span id="PAY_COURSE_TEXT">-</span>
                  </li>
                  <li class="list-group-item p-2">
                    <small class="text-muted d-block">AY / Semester</small>
                    <span id="PAY_TERM_TEXT">-</span>
                  </li>
                  <li class="list-group-item p-2">
                    <small class="text-muted d-block">Section</small>
                    <span id="PAY_SECTION_TEXT">-</span>
                  </li>
                </ul>
              </div>
            </div>

            <div class="col-md-9">

              <!-- Subjects taken (editable) -->
              <div class="card mb-3">
                <div class="card-header p-2">
                  <h6 class="mb-0 pl-1">Subjects Taken</h6>
                </div>
                <div class="card-body p-0">
                  <table class="table table-sm table-hover mb-0">
                    <thead class="thead-light">
                      <tr>
                        <th width="5%"></th>
                        <th>Code</th>
                        <th>Subject</th>
                        <th width="10%">Type</th>
                        <th width="8%">Units</th>
                        <th width="15%" class="text-right">Amount</th>
                      </tr>
                    </thead>
                    <tbody id="PAY_SUBJECTS_TBODY">
                      <tr><td colspan="6" class="text-muted text-center">Loading...</td></tr>
                    </tbody>
                    <tfoot>
                      <tr>
                        <th colspan="5" class="text-right">Major subtotal</th>
                        <th class="text-right" id="PAY_MAJOR_AMT">&#8369;0.00</th>
                      </tr>
                      <tr>
                        <th colspan="5" class="text-right">Minor subtotal</th>
                        <th class="text-right" id="PAY_MINOR_AMT">&#8369;0.00</th>
                      </tr>
                      <tr class="table-active">
                        <th colspan="5" class="text-right">Total Tuition</th>
                        <th class="text-right" id="PAY_TOTAL_AMT">&#8369;0.00</th>
                      </tr>
                    </tfoot>
                  </table>
                </div>
              </div>

              <div class="row">
                <!-- Enrollment Fee -->
                <div class="col-md-6">
                  <div class="card card-outline card-info mb-3 mb-md-0">
                    <div class="card-header p-2">
                      <h6 class="mb-0 pl-1">Enrollment Fee</h6>
                    </div>
                    <div class="card-body">
                      <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Required amount</span>
                        <strong id="PAY_ENROLLMENT_FEE_AMT">&#8369;0.00</strong>
                      </div>
                      <div class="mb-2" id="PAY_ENROLLMENT_FEE_STATUS">-</div>
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="PAY_ENROLLMENT_FEE" id="PAY_ENROLLMENT_FEE_CHECK" value="1">
                        <label class="form-check-label" for="PAY_ENROLLMENT_FEE_CHECK">
                          Collect the enrollment fee now
                        </label>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Tuition payment -->
                <div class="col-md-6">
                  <div class="card card-outline card-secondary mb-0">
                    <div class="card-header p-2">
                      <h6 class="mb-0 pl-1">Tuition Payment (optional)</h6>
                    </div>
                    <div class="card-body">
                      <div class="d-flex justify-content-between align-items-center mb-1">
                        <span>Already paid</span>
                        <strong id="PAY_TUITION_PAID_TEXT">&#8369;0.00</strong>
                      </div>
                      <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Remaining balance</span>
                        <strong id="PAY_TUITION_BALANCE_TEXT">&#8369;0.00</strong>
                      </div>
                      <div class="form-group mb-0">
                        <label for="PAY_TUITION_AMOUNT" class="col-form-label col-form-label-sm">Amount to collect now</label>
                        <input type="number" step="0.01" min="0" class="form-control form-control-sm" name="PAY_TUITION_AMOUNT" id="PAY_TUITION_AMOUNT" placeholder="0.00">
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Cash Tendered / Sukli -->
              <div class="row mt-3">
                <div class="col-12">
                  <div class="card card-outline card-success mb-0">
                    <div class="card-header p-2">
                      <h6 class="mb-0 pl-1"><i class="fa fa-coins mr-1"></i> Cash Payment</h6>
                    </div>
                    <div class="card-body">
                      <div class="row">
                        <div class="col-sm-4 d-flex justify-content-between align-items-center mb-2 mb-sm-0">
                          <span>Amount Due Now</span>
                          <strong id="PAY_AMOUNT_DUE_TEXT">&#8369;0.00</strong>
                        </div>
                        <div class="col-sm-4">
                          <div class="form-group mb-0">
                            <label for="PAY_CASH_RECEIVED" class="col-form-label col-form-label-sm">Cash Received</label>
                            <input type="number" step="0.01" min="0" class="form-control form-control-sm" name="PAY_CASH_RECEIVED" id="PAY_CASH_RECEIVED" placeholder="0.00">
                          </div>
                        </div>
                        <div class="col-sm-4 d-flex justify-content-between align-items-center mt-2 mt-sm-0">
                          <span>Change</span>
                          <strong id="PAY_CHANGE_TEXT" class="text-success">&#8369;0.00</strong>
                        </div>
                      </div>
                      <small class="text-muted d-block mt-2" id="PAY_CASH_NOTE"></small>
                    </div>
                  </div>
                </div>
              </div>

              <!-- OR / Date -->
              <div class="row mt-3">
                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="PAY_OR_NO" class="col-form-label col-form-label-sm">OR Number</label>
                    <input type="text" class="form-control form-control-sm" name="PAY_OR_NO" id="PAY_OR_NO">
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="PAY_DATE" class="col-form-label col-form-label-sm">Date Paid</label>
                    <input type="date" class="form-control form-control-sm" name="PAY_DATE" id="PAY_DATE" value="<?php echo date('Y-m-d'); ?>">
                  </div>
                </div>
              </div>

            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->

        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Save Payment</button>
        </div>
      </div>
    </form>
  </div>
</div>
