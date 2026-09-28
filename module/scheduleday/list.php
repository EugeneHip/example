<?php
// SANTE SOLUTIONS - "Schedule Day" Module (module/scheduleday):
// listahan ng reusable na Day patterns (hal. MWF, TTH, Saturday)
// na ginagamit ng Set Schedule module (module/schedule) sa
// pagpili ng araw ng isang klase.
?>
<section class="content">

   <!-- SANTE SOLUTIONS -->

      <div class="container-fluid">
         <?php check_message(); ?>
        <div class="row">
          <div class="col-12">

            <div class="card">
              <div class="card-header">
                <h3 class="card-title">List of Schedule Days</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <?php if (!$mydb->tableExists('tblscheduledays')): ?>
                <div class="alert alert-warning mb-3">
                  <i class="fa fa-exclamation-triangle mr-1"></i>
                  Table <code>tblscheduledays</code> was not found in the database.
                  Please run <code>database/migration_add_scheduling.sql</code> in phpMyAdmin first.
                </div>
                <?php endif; ?>
                <table id="tblscheduleday" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th width="5%">#</th>
                    <th>Day Name</th>
                    <th>Description</th>
                    <th width="14%">Action</th>
                  </tr>
                  </thead>
                  <tbody>

                  </tbody>
                  <tfoot>

                  </tfoot>
                </table>
                  <div class="btn-group">

                  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#AddNewEntry">Add New</button>

                </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>

<!-----START of Add Form---->
     <div class="modal fade" id="AddNewEntry">
        <div class="modal-dialog">
        <form action="controller.php?action=add" method="POST">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Add New Schedule Day</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="row">

                    <div class="col-sm-12">
                      <div class="form-group">
                       <label for="DAY_NAME" class="col-form-label col-form-label-sm">Day Name</label>
                        <input type="text" class="form-control form-control-sm" name="DAY_NAME"
                        id="DAY_NAME" placeholder="e.g. MWF, TTH, Saturday" required>
                      </div>
                    </div>

                    <div class="col-sm-12">
                      <div class="form-group">
                       <label for="DESCRIPTION" class="col-form-label col-form-label-sm">Description</label>
                        <textarea class="form-control form-control-sm" name="DESCRIPTION"
                        id="DESCRIPTION" placeholder="e.g. Monday, Wednesday, Friday"></textarea>
                      </div>
                    </div>

                  </div>
            </div>
            <div class="modal-footer justify-content-between">
             <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                 <button type="submit" class="btn btn-primary" name="save" type="submit">Save changes</button>
            </div>
          </div>
          </form>
        </div>
      </div>
<!-----End of Add Form---->

<!-----Start of Edit Form---->
<div class="modal fade" id="editEntry">
        <div class="modal-dialog">
        <form action="controller.php?action=edit" method="POST">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Modify Schedule Day</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="row">
                    <input type="hidden" name="SCHEDULEDAY_ID" id="SCHEDULEDAY_ID">
                    <div class="col-sm-12">
                      <div class="form-group">
                       <label for="DAY_NAME1" class="col-form-label col-form-label-sm">Day Name</label>
                        <input type="text" class="form-control form-control-sm" name="DAY_NAME1"
                        id="DAY_NAME1" placeholder="Day Name">
                      </div>
                    </div>

                    <div class="col-sm-12">
                      <div class="form-group">
                       <label for="DESCRIPTION1" class="col-form-label col-form-label-sm">Description</label>
                        <textarea class="form-control form-control-sm" name="DESCRIPTION1"
                        id="DESCRIPTION1" placeholder="Description"></textarea>
                      </div>
                    </div>

                  </div>
            </div>
            <div class="modal-footer justify-content-between">
             <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                 <button type="submit" class="btn btn-primary" name="edit" type="submit">Save changes</button>
            </div>
          </div>
          </form>
        </div>
      </div>
<!-----End of Edit Form---->
