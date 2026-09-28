

  <nav class="main-header navbar navbar-expand navbar-dark" style="background-color: #8B0000">
    
 <!-- EJB SOLUTIONS - BATUTO 2025  -->

    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" data-controlsidebar-side="false" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="index.php" class="nav-link">Home</a>
      </li>
    </ul>

    <!-- SEARCH FORM -->

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Messages Dropdown Menu -->

      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
         Hello <i class="far fa-user"></i>
          <span class="badge badge-warning navbar-badge"></span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
       
          <div class="card card-widget widget-user">
             <div class="widget-user-header bg-info">
                <h4 class="widget-user-username"><?php  if (isset($_SESSION['DISPLAYNAME'])) {
                            echo $_SESSION['DISPLAYNAME'];
                          }else{
                            echo "User";
                          } ?></h3>
                <h5 class="widget-user-desc"><?php  if (isset($_SESSION['TYPE'])) {
                            echo $_SESSION['TYPE'];
                          }else{
                            echo "Guest";
                          } ?></h5>
              </div>
              <div class="widget-user-image">
                <?php
               echo '<img class="img-circle elevation-6" src="'. WEB_ROOT . 'module/user/images/default.png" alt="User Avatar">';
  
                ?>
              </div>
              <div class="card-footer">
                   <a href="<?php echo  WEB_ROOT;?>logout.php" class="small-box-footer">Logout <i class="fas fa-arrow-circle-right"></i></a>
                </div>
         
          </div> 
        </div>
            
                     
        </div>
      </li>
   
    </ul>
  </nav>