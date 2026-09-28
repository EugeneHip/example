<?php
// SANTE SOLUTIONS - Classrooms module: listahan ng mga Classroom (Add/Edit/Delete).
// Ginagamit ang mga Classroom na ito sa Set Schedule (module/schedule).
?>
<section class="content">

   <!-- SANTE SOLUTIONS -->

      <div class="container-fluid">
         <?php check_message(); ?>
        <div class="row">
          <div class="col-12">

            <div class="card">
              <div class="card-header">
                <h3 class="card-title">List of Classrooms</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <?php if (!$mydb->tableExists('tblclassrooms')): ?>
                <div class="alert alert-warning mb-3">
                  <i class="fa fa-exclamation-triangle mr-1"></i>
                  Table <code>tblclassrooms</code> was not found in the database.
                  Please run <code>database/migration_add_scheduling.sql</code> in phpMyAdmin first.
                </div>
                <?php endif; ?>
                <table id="tblclassroom" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th width="5%">#</th>
                    <th>Classroom Name</th>
                    <th>Description</th>
                    <th width="10%">Status</th>
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
              <h4 class="modal-title">Add New Classroom</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="row">

                    <div class="col-sm-8">
                      <div class="form-group">
                       <label for="CLASSROOM_NAME" class="col-form-label col-form-label-sm">Classroom Name</label>
                        <input type="text" class="form-control form-control-sm" name="CLASSROOM_NAME"
                        id="CLASSROOM_NAME" placeholder="e.g. Room 101, Computer Laboratory 1" required>
                      </div>
                    </div>

                    <div class="col-sm-4">
                      <div class="form-group">
                       <label for="STATUS" class="col-form-label col-form-label-sm">Status</label>
                        <select class="form-control form-control-sm" name="STATUS" required>
                          <option value="Active">Active</option>
                          <option value="Inactive">Inactive</option>
                        </select>
                      </div>
                    </div>

                    <div class="col-sm-12">
                      <div class="form-group">
                       <label for="DESCRIPTION" class="col-form-label col-form-label-sm">Description</label>
                        <textarea class="form-control form-control-sm" name="DESCRIPTION"
                        id="DESCRIPTION" placeholder="e.g. 2nd floor, 40 seating capacity"></textarea>
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
              <h4 class="modal-title">Modify Classroom</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="row">
                    <input type="hidden" name="CLASSROOM_ID" id="CLASSROOM_ID">
                    <div class="col-sm-8">
                      <div class="form-group">
                       <label for="CLASSROOM_NAME1" class="col-form-label col-form-label-sm">Classroom Name</label>
                        <input type="text" class="form-control form-control-sm" name="CLASSROOM_NAME1"
                        id="CLASSROOM_NAME1" placeholder="Classroom Name">
                      </div>
                    </div>

                    <div class="col-sm-4">
                      <div class="form-group">
                       <label for="STATUS1" class="col-form-label col-form-label-sm">Status</label>
                        <select class="form-control form-control-sm" name="STATUS1" id="STATUS1">
                          <option value="Active">Active</option>
                          <option value="Inactive">Inactive</option>
                        </select>
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
