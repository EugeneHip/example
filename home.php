<?php

//EJB SOLUTIONS - BATUTO 2025
//SANTE SOLUTIONS - dashboard boxes now show live counts and their
//"More info" links go to the matching module instead of "#".

global $mydb;

function home_safe_count($table) {
	global $mydb;
	if (!$mydb->tableExists($table)) { return 0; }
	$mydb->setQuery("SELECT * FROM `".$table."`");
	return $mydb->num_rows();
}

$totalAlumni  = home_safe_count('alumni_details');
$totalStudent = home_safe_count('tblstudent');
$totalCourse  = home_safe_count('tblcourses');
$totalSection = home_safe_count('tblsections');
?>

<section class="content">

      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3><?php echo $totalAlumni; ?></h3>

                <p>Alumni Count</p>
              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
              <a href="<?php echo WEB_ROOT; ?>module/generic/index.php?t=alumni_details" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3><?php echo $totalStudent; ?></h3>

                <p>Total Students</p>
              </div>
              <div class="icon">
                <i class="ion ion-stats-bars"></i>
              </div>
              <a href="<?php echo WEB_ROOT; ?>module/student/" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3><?php echo $totalCourse; ?></h3>

                <p>Total Courses</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
              <a href="<?php echo WEB_ROOT; ?>module/course/" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3><?php echo $totalSection; ?></h3>

                <p>Total Sections</p>
              </div>
              <div class="icon">
                <i class="ion ion-pie-graph"></i>
              </div>
              <a href="<?php echo WEB_ROOT; ?>module/generic/index.php?t=tblsections" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
        </div>
        <!-- /.row -->


    </section>
