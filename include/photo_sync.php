<?php

/* HIPANAO SOLUTIONS - Photo Sync (Student <-> User Account).

   Layunin: iisa lang dapat na "profile picture" ang makikita ng isang
   estudyante kahit saan ito i-edit - module/student (Admin), module/user
   (Admin), o module/studentmodule (ang estudyante mismo, sa kanyang
   sariling dashboard). Kaya sa tuwing may bagong na-upload na photo sa
   kahit alin sa mga iyon, dito na tinatawag para awtomatikong ma-copy
   ang file papunta sa KABILANG folder at ma-update ang KABILANG record
   - hindi na kailangang mag-alala ang bawat module tungkol sa isa't isa.

     tblstudent.COMPANYIDNO  (file lang, hal. "student_20260901..._1234.jpg")
       -> nasa module/student/image/<file>

     tblusers.PICTURE        (may kasamang "images/" prefix, hal. "images/user_..._5678.jpg")
       -> nasa module/user/<PICTURE>  (ibig sabihin module/user/images/<file>)

   Ang dalawang function dito ay basta na lang nag-COPY ng file (hindi
   move) - iniiwan pa rin ang orihinal kung saan ito unang na-upload,
   bago gumawa ng sarili niyang bagong pangalan ng file sa kabilang
   folder (parehong convention ng random suffix na gaya ng ginagawa na
   ng handleStudentPhotoUpload()/handleUserPhotoUpload()). */

function hipanao_student_image_dir() {
	return SITE_ROOT . DS . 'module' . DS . 'student' . DS . 'image' . DS;
}

function hipanao_user_image_dir() {
	return SITE_ROOT . DS . 'module' . DS . 'user' . DS . 'images' . DS;
}

/* Kinukuha lang ang basename ng isang stored path (COMPANYIDNO ay
   "file.jpg" na lang mismo, PICTURE naman ay "images/file.jpg") -
   pinoprotektahan din laban sa "../" (path traversal) bago gamitin
   sa pag-open/copy ng file mula sa disk. */
function hipanao_safe_basename($path) {
	$path = str_replace('\\', '/', (string) $path);
	return basename($path);
}

/* Ise-sync ang larawan ng isang estudyante (tblstudent.COMPANYIDNO)
   PAPUNTA sa naka-link na login account niya (tblusers.PICTURE), kung
   meron. Tinatawag ito pagkatapos mag-Add/Edit ng photo sa
   module/student, pagkatapos ng "Create Login Account" (Approve) sa
   module/student, AT pagkatapos mag-upload ang estudyante mismo ng
   sariling photo sa module/studentmodule.

   Kung wala pang photo ang estudyante ("kong may pic o wala"), o kung
   wala pang naka-link na account, tahimik lang itong hindi gagawa ng
   anuman - hindi ito error, normal lang na kaso. */
function hipanao_sync_student_photo_to_user($S_ID) {
	global $mydb;

	$S_ID = intval($S_ID);
	if ($S_ID <= 0) { return false; }

	$mydb->setQuery("SELECT COMPANYIDNO, ACCOUNT_UID FROM `tblstudent` WHERE S_ID = '".$S_ID."' LIMIT 1");
	$rows = $mydb->loadResultList();
	if (count($rows) < 1) { return false; }

	$studentRow = $rows[0];
	$accountUid = isset($studentRow->ACCOUNT_UID) ? intval($studentRow->ACCOUNT_UID) : 0;
	$photoFile  = isset($studentRow->COMPANYIDNO) ? trim((string) $studentRow->COMPANYIDNO) : '';

	if ($accountUid <= 0 || $photoFile === '') { return false; }

	$photoFile  = hipanao_safe_basename($photoFile);
	$sourcePath = hipanao_student_image_dir() . $photoFile;

	if (!is_file($sourcePath)) { return false; }

	$ext = strtolower(pathinfo($photoFile, PATHINFO_EXTENSION));
	if ($ext === '') { $ext = 'jpg'; }

	$destDir = hipanao_user_image_dir();
	if (!is_dir($destDir)) { @mkdir($destDir, 0755, true); }

	$newFileName = 'user_' . date('YmdHis') . '_' . mt_rand(1000, 9999) . '.' . $ext;
	$destPath    = $destDir . $newFileName;

	if (!@copy($sourcePath, $destPath)) { return false; }

	$newPicture = 'images/' . $newFileName;
	$mydb->InsertThis("UPDATE `tblusers` SET `PICTURE` = '".$mydb->escape_value($newPicture)."' WHERE `UID` = '".$accountUid."'");

	/* Kung ang kasalukuyang naka-login ay ang mismong account na ito
	   (halimbawa: kaka-edit lang ng estudyante ng sarili niyang photo
	   sa module/studentmodule), i-update din agad ang SESSION para
	   sumalamin kaagad sa header (theme/header.php gumagamit ng
	   $_SESSION['PICTURE']) nang hindi na kailangang mag-logout/login. */
	if (isset($_SESSION['UID']) && intval($_SESSION['UID']) === $accountUid) {
		$_SESSION['PICTURE'] = $newPicture;
	}

	return true;
}

/* Kabaligtaran naman nito: ise-sync ang larawan ng isang User account
   (tblusers.PICTURE) PABALIK sa naka-link na tblstudent.COMPANYIDNO
   niya, kung meron. Tinatawag pagkatapos mag-Edit ng photo sa
   module/user (Manage User Accounts) - para kung Admin ang nag-edit
   dito sa halip na sa module/student, sumalamin pa rin ito pabalik sa
   tblstudent record (gaya ng makikita sa Admin's tblstudent listing,
   view profile, atbp). */
function hipanao_sync_user_photo_to_student($UID) {
	global $mydb;

	$UID = intval($UID);
	if ($UID <= 0) { return false; }

	$mydb->setQuery("SELECT PICTURE FROM `tblusers` WHERE UID = '".$UID."' LIMIT 1");
	$rows = $mydb->loadResultList();
	if (count($rows) < 1) { return false; }

	$photoFile = isset($rows[0]->PICTURE) ? trim((string) $rows[0]->PICTURE) : '';
	if ($photoFile === '') { return false; }

	$mydb->setQuery("SELECT S_ID FROM `tblstudent` WHERE ACCOUNT_UID = '".$UID."' LIMIT 1");
	$studentRows = $mydb->loadResultList();
	if (count($studentRows) < 1) { return false; }

	$S_ID = intval($studentRows[0]->S_ID);
	if ($S_ID <= 0) { return false; }

	$photoFile  = hipanao_safe_basename($photoFile);
	$sourcePath = hipanao_user_image_dir() . $photoFile;

	if (!is_file($sourcePath)) { return false; }

	$ext = strtolower(pathinfo($photoFile, PATHINFO_EXTENSION));
	if ($ext === '') { $ext = 'jpg'; }

	$destDir = hipanao_student_image_dir();
	if (!is_dir($destDir)) { @mkdir($destDir, 0755, true); }

	$newFileName = 'student_' . date('YmdHis') . '_' . mt_rand(1000, 9999) . '.' . $ext;
	$destPath    = $destDir . $newFileName;

	if (!@copy($sourcePath, $destPath)) { return false; }

	$mydb->InsertThis("UPDATE `tblstudent` SET `COMPANYIDNO` = '".$mydb->escape_value($newFileName)."' WHERE `S_ID` = '".$S_ID."'");

	return true;
}

?>
