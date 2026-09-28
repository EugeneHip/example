   <nav class="mt-2">

 <!-- EJB SOLUTIONS - BATUTO 2025  -->

        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
         
           <li class="nav-header"> 
            <a href='<?php echo WEB_ROOT; ?>' class="nav-link <?php  echo ($title=='Home') ? "active" : 'na' ;?>">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
                   </p>
            </a>
          </li>

  <li class="nav-header">RECORDS</li>

 <li class="nav-item"> 
  <a href='<?php echo WEB_ROOT; ?>module/generic/index.php?t=alumni_details' class="nav-link <?php  echo ($title=='Alumni Details') ? "active" : 'na' ;?>">
  <i class="nav-icon fa fa-id-card"></i>
  <p style="font-weight: normal">
                Alumni Details
  </p>
  </a>
  </li>

  <li class="nav-item"> 
  <a href='<?php echo WEB_ROOT; ?>module/student' class="nav-link <?php  echo ($title=='Student Module') ? "active" : 'na' ;?>">
  <i class="nav-icon fa fa-user-graduate"></i>
  <p style="font-weight: normal">
                Student
  </p>
  </a>
  </li>

  <li class="nav-item"> 
  <a href='<?php echo WEB_ROOT; ?>module/course' class="nav-link <?php  echo ($title=='Course Module') ? "active" : 'na' ;?>">
  <i class="nav-icon fa fa-book"></i>
  <p style="font-weight: normal">
                Course
  </p>
  </a>
  </li>

  <li class="nav-item"> 
  <a href='<?php echo WEB_ROOT; ?>module/subject' class="nav-link <?php  echo ($title=='Subject Module') ? "active" : 'na' ;?>">
  <i class="nav-icon fa fa-book-open"></i>
  <p style="font-weight: normal">
                Subject
  </p>
  </a>
  </li>

  <li class="nav-header">SCHEDULING</li>

  <li class="nav-item"> 
  <a href='<?php echo WEB_ROOT; ?>module/generic/index.php?t=tblschoolyear' class="nav-link <?php  echo ($title=='School Year') ? "active" : 'na' ;?>">
  <i class="nav-icon fa fa-calendar-alt"></i>
  <p style="font-weight: normal">
                School Year
  </p>
  </a>
  </li>

  <li class="nav-item"> 
  <a href='<?php echo WEB_ROOT; ?>module/generic/index.php?t=tblsections' class="nav-link <?php  echo ($title=='Sections') ? "active" : 'na' ;?>">
  <i class="nav-icon fa fa-chalkboard"></i>
  <p style="font-weight: normal">
                Sections
  </p>
  </a>
  </li>

  <li class="nav-item"> 
  <a href='<?php echo WEB_ROOT; ?>module/classroom' class="nav-link <?php  echo ($title=='Classroom Module') ? "active" : 'na' ;?>">
  <i class="nav-icon fa fa-door-open"></i>
  <p style="font-weight: normal">
                Classrooms
  </p>
  </a>
  </li>

  <li class="nav-item"> 
  <a href='<?php echo WEB_ROOT; ?>module/scheduletime' class="nav-link <?php  echo ($title=='Schedule Time Module') ? "active" : 'na' ;?>">
  <i class="nav-icon fa fa-clock"></i>
  <p style="font-weight: normal">
                Schedule Time
  </p>
  </a>
  </li>

  <li class="nav-item"> 
  <a href='<?php echo WEB_ROOT; ?>module/scheduleday' class="nav-link <?php  echo ($title=='Schedule Day Module') ? "active" : 'na' ;?>">
  <i class="nav-icon fa fa-calendar-day"></i>
  <p style="font-weight: normal">
                Schedule Day
  </p>
  </a>
  </li>

  <li class="nav-item"> 
  <a href='<?php echo WEB_ROOT; ?>module/schedule' class="nav-link <?php  echo ($title=='Set Schedule Module') ? "active" : 'na' ;?>">
  <i class="nav-icon fa fa-calendar-check"></i>
  <p style="font-weight: normal">
                Set Schedule
  </p>
  </a>
  </li>

  <li class="nav-item"> 
  <a href='<?php echo WEB_ROOT; ?>module/sectionschedule/index.php' class="nav-link <?php  echo ($title=='Section Schedule Module') ? "active" : 'na' ;?>">
  <i class="nav-icon fa fa-table"></i>
  <p style="font-weight: normal">
                Section Schedule
  </p>
  </a>
  </li>

  <li class="nav-header">ENROLLMENT</li>

  <li class="nav-item"> 
  <a href='<?php echo WEB_ROOT; ?>module/enrollment/index.php' class="nav-link <?php  echo ($title=='Enrollment') ? "active" : 'na' ;?>">
  <i class="nav-icon fa fa-clipboard-list"></i>
  <p style="font-weight: normal">
                Enrollment
  </p>
  </a>
  </li>

  <li class="nav-item"> 
  <a href='<?php echo WEB_ROOT; ?>module/payment/index.php' class="nav-link <?php  echo ($title=='Payment') ? "active" : 'na' ;?>">
  <i class="nav-icon fa fa-money-check-alt"></i>
  <p style="font-weight: normal">
                Payment
  </p>
  </a>
  </li>

  <li class="nav-item"> 
  <a href='<?php echo WEB_ROOT; ?>module/enrollmentdetails/index.php' class="nav-link <?php  echo ($title=='Enrollment Details') ? "active" : 'na' ;?>">
  <i class="nav-icon fa fa-list-alt"></i>
  <p style="font-weight: normal">
                Enrollment Details
  </p>
  </a>
  </li>

  <li class="nav-item"> 
  <a href='<?php echo WEB_ROOT; ?>module/hitoryenrollment/index.php' class="nav-link <?php  echo ($title=='History Enrollment') ? "active" : 'na' ;?>">
  <i class="nav-icon fa fa-history"></i>
  <p style="font-weight: normal">
                History Enrollment
  </p>
  </a>
  </li>

  <li class="nav-header">OPERATIONS</li>

  <li class="nav-item"> 
  <a href='<?php echo WEB_ROOT; ?>module/grade/index.php' class="nav-link <?php  echo ($title=='Grades') ? "active" : 'na' ;?>">
  <i class="nav-icon fa fa-graduation-cap"></i>
  <p style="font-weight: normal">
                Grades
  </p>
  </a>
  </li>

  <li class="nav-item"> 
  <a href='<?php echo WEB_ROOT; ?>module/about/index.php' class="nav-link <?php  echo ($title=='About') ? "active" : 'na' ;?>">
  <i class="nav-icon fa fa-info-circle"></i>
  <p style="font-weight: normal">
                About
  </p>
  </a>
  </li>

          <li class="nav-item has-treeview">
           
            <a href="#" class="nav-link <?php  echo ($title=='User Module' || $title=='User Type' ) ? "active" : 'na' ;?>">
              <i class="fas fa-cog"></i>
              <p>
                Account Settings
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href='<?php echo WEB_ROOT; ?>module/user/' class="nav-link">
                  <i class="nav-icon fa fa-users"></i>
                  <p>Manage User Accounts</p>
                </a>
              </li>
              <li class="nav-item">
                <a href='<?php echo WEB_ROOT; ?>module/usertype/' class="nav-link">
                  <i class="nav-icon fa fa-key"></i>
                  <p>Manage User Type</p>
                </a>
              </li>    
            </ul>
          </li>
          
              
              
            </ul>
              
            
             

        </ul>
      </nav>