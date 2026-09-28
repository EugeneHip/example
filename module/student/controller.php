<?php

require_once ("../../include/initialize.php");
	  if (!isset($_SESSION['ACCOUNT_ID'])){
     
     }

$action = (isset($_GET['action']) && $_GET['action'] != '') ? $_GET['action'] : '';

switch ($action) {
	case 'add' :
	doInsert();
	break;
	
	case 'edit' :
	doEdit();
	break;
	
	case 'delete' :
	doDelete();
	break;

	case 'register' :
	doRegister();
	break;

	case 'confirmaccount' :
	doConfirmAccount();
	break;
	 
	}

	function handleStudentPhotoUpload(){

		if (!isset($_FILES['PICTURE']) || $_FILES['PICTURE']['error'] === UPLOAD_ERR_NO_FILE) {
			return '';
		}
		if ($_FILES['PICTURE']['error'] !== UPLOAD_ERR_OK) {
			return false;
		}

		$tmpName   = $_FILES['PICTURE']['tmp_name'];
		$imageInfo = @getimagesize($tmpName);
		if ($imageInfo === false) {
			return false;
		}

		$allowedExt = array('jpg' => true, 'jpeg' => true, 'png' => true, 'gif' => true, 'webp' => true);
		$ext = strtolower(pathinfo($_FILES['PICTURE']['name'], PATHINFO_EXTENSION));
		if (!isset($allowedExt[$ext])) {
			return false;
		}

		$uploadDir = __DIR__ . DS . 'image' . DS;
		if (!is_dir($uploadDir)) {
			@mkdir($uploadDir, 0755, true);
		}

		$fileName = 'student_' . date('YmdHis') . '_' . mt_rand(1000, 9999) . '.' . $ext;

		if (move_uploaded_file($tmpName, $uploadDir . $fileName)) {
			return $fileName;
		}
		return false;
	}

	function doInsert(){
  
		$student = new Student();

		$IDNO   = $_POST['IDNO'];
		$FNAME	= $_POST['FNAME'];
		$LNAME 	= $_POST['LNAME'];
		$MNAME 		= $_POST['MNAME'];
		$SEX 		= $_POST['SEX'];
		$BDAY  = $_POST['BDAY'];
		
		$res = $student->find_all_student($IDNO);
		
			if ($res >=1) {
				message("Student IDNO already exist!", "error");
				redirect('index.php');
			}else{

				$photo = handleStudentPhotoUpload();
				if ($photo === false) {
					message("Uploaded photo is not a valid image!", "error");
					redirect('index.php');
					return;
				}

				$student->IDNO = $IDNO;
				$student->FNAME = $FNAME;
				$student->LNAME = $LNAME;
				$student->MNAME 	= $MNAME;
				$student->SEX 	= $SEX;
				$student->BDAY 	= $BDAY;

				$student->BPLACE        = isset($_POST['BPLACE'])        ? $_POST['BPLACE']        : '';
				$student->STATUS        = isset($_POST['STATUS'])        ? $_POST['STATUS']        : 'Active';
				$student->NATIONALITY   = isset($_POST['NATIONALITY'])   ? $_POST['NATIONALITY']   : '';
				$student->RELIGION      = isset($_POST['RELIGION'])      ? $_POST['RELIGION']      : '';
				$student->CONTACT_NO    = isset($_POST['CONTACT_NO'])    ? $_POST['CONTACT_NO']    : '';
				$student->HOME_ADD      = isset($_POST['HOME_ADD'])      ? $_POST['HOME_ADD']      : '';
				$student->EMAIL         = isset($_POST['EMAIL'])         ? $_POST['EMAIL']         : '';
				$student->LRNNO         = isset($_POST['LRNNO'])         ? $_POST['LRNNO']         : '';
				$student->CONTACTPERSON = isset($_POST['CONTACTPERSON']) ? $_POST['CONTACTPERSON'] : '';

				/* Parents' / Guardian's Information - HIPANAO SOLUTIONS,
				   ginagamit sa Print Enrollment Form. */
				$student->CIVIL_STATUS          = isset($_POST['CIVIL_STATUS'])          ? $_POST['CIVIL_STATUS']          : 'Single';
				$student->FATHER_NAME           = isset($_POST['FATHER_NAME'])           ? $_POST['FATHER_NAME']           : '';
				$student->FATHER_CONTACT        = isset($_POST['FATHER_CONTACT'])        ? $_POST['FATHER_CONTACT']        : '';
				$student->FATHER_EMAIL          = isset($_POST['FATHER_EMAIL'])          ? $_POST['FATHER_EMAIL']          : '';
				$student->FATHER_OCCUPATION     = isset($_POST['FATHER_OCCUPATION'])     ? $_POST['FATHER_OCCUPATION']     : '';
				$student->FATHER_DECEASED       = isset($_POST['FATHER_DECEASED'])       ? $_POST['FATHER_DECEASED']       : 'No';
				$student->MOTHER_NAME           = isset($_POST['MOTHER_NAME'])           ? $_POST['MOTHER_NAME']           : '';
				$student->MOTHER_CONTACT        = isset($_POST['MOTHER_CONTACT'])        ? $_POST['MOTHER_CONTACT']        : '';
				$student->MOTHER_EMAIL          = isset($_POST['MOTHER_EMAIL'])          ? $_POST['MOTHER_EMAIL']          : '';
				$student->MOTHER_OCCUPATION     = isset($_POST['MOTHER_OCCUPATION'])     ? $_POST['MOTHER_OCCUPATION']     : '';
				$student->MOTHER_DECEASED       = isset($_POST['MOTHER_DECEASED'])       ? $_POST['MOTHER_DECEASED']       : 'No';
				$student->GUARDIAN_NAME         = isset($_POST['GUARDIAN_NAME'])         ? $_POST['GUARDIAN_NAME']         : '';
				$student->GUARDIAN_RELATIONSHIP = isset($_POST['GUARDIAN_RELATIONSHIP']) ? $_POST['GUARDIAN_RELATIONSHIP'] : '';
				$student->GUARDIAN_CONTACT      = isset($_POST['GUARDIAN_CONTACT'])      ? $_POST['GUARDIAN_CONTACT']      : '';
				$student->GUARDIAN_EMAIL        = isset($_POST['GUARDIAN_EMAIL'])        ? $_POST['GUARDIAN_EMAIL']        : '';
				$student->GUARDIAN_ADDRESS      = isset($_POST['GUARDIAN_ADDRESS'])      ? $_POST['GUARDIAN_ADDRESS']      : '';

				/* Other Person Supporting / Boarding - HIPANAO SOLUTIONS. */
				$student->OTHER_PERSON_SUPPORTING = isset($_POST['OTHER_PERSON_SUPPORTING']) ? $_POST['OTHER_PERSON_SUPPORTING'] : '';
				$student->IS_BOARDING             = isset($_POST['IS_BOARDING'])             ? $_POST['IS_BOARDING']             : 'No';
				$student->WITH_FAMILY             = isset($_POST['WITH_FAMILY'])             ? $_POST['WITH_FAMILY']             : 'Yes';
				$student->BOARDING_ADDRESS        = isset($_POST['BOARDING_ADDRESS'])        ? $_POST['BOARDING_ADDRESS']        : '';

				/* Education - HIPANAO SOLUTIONS. */
				$student->ELEM_SCHOOL      = isset($_POST['ELEM_SCHOOL'])      ? $_POST['ELEM_SCHOOL']      : '';
				$student->ELEM_ADDRESS     = isset($_POST['ELEM_ADDRESS'])     ? $_POST['ELEM_ADDRESS']     : '';
				$student->ELEM_YEAR        = isset($_POST['ELEM_YEAR'])        ? $_POST['ELEM_YEAR']        : '';
				$student->SEC_SCHOOL       = isset($_POST['SEC_SCHOOL'])       ? $_POST['SEC_SCHOOL']       : '';
				$student->SEC_ADDRESS      = isset($_POST['SEC_ADDRESS'])      ? $_POST['SEC_ADDRESS']      : '';
				$student->SEC_YEAR         = isset($_POST['SEC_YEAR'])         ? $_POST['SEC_YEAR']         : '';
				$student->COLLEGE_SCHOOL   = isset($_POST['COLLEGE_SCHOOL'])   ? $_POST['COLLEGE_SCHOOL']   : '';
				$student->COLLEGE_ADDRESS  = isset($_POST['COLLEGE_ADDRESS'])  ? $_POST['COLLEGE_ADDRESS']  : '';
				$student->COLLEGE_YEAR     = isset($_POST['COLLEGE_YEAR'])     ? $_POST['COLLEGE_YEAR']     : '';
				$student->VOC_SCHOOL       = isset($_POST['VOC_SCHOOL'])       ? $_POST['VOC_SCHOOL']       : '';
				$student->VOC_ADDRESS      = isset($_POST['VOC_ADDRESS'])      ? $_POST['VOC_ADDRESS']      : '';
				$student->VOC_YEAR         = isset($_POST['VOC_YEAR'])         ? $_POST['VOC_YEAR']         : '';
				$student->OTHERS_SCHOOL    = isset($_POST['OTHERS_SCHOOL'])    ? $_POST['OTHERS_SCHOOL']    : '';

				/* HIPANAO SOLUTIONS - Ang Requirements checklist ay hindi na
				   dito ina-edit (module/student) - sa module/enrollment na
				   lang ito ("Document" stage) - kaya inalis na dito ang
				   REQ_* assignment para hindi ma-overwrite ng blangko/
				   default value ang datos na ise-save doon. */

				if (isset($_POST['AGE']) && $_POST['AGE'] !== '') {
					$student->AGE = intval($_POST['AGE']);
				}
				if (isset($_POST['COURSE_ID']) && $_POST['COURSE_ID'] !== '') {
					$student->COURSE_ID = intval($_POST['COURSE_ID']);
				}

				if (isset($_POST['ACC_PASSWORD']) && $_POST['ACC_PASSWORD'] !== '') {
					$student->ACC_PASSWORD = sha1($_POST['ACC_PASSWORD']);
				}

				if ($photo !== '') {
					$student->COMPANYIDNO = $photo;
				}

				if (isset($_SESSION['UID'])) {
					$student->AddedBy = intval($_SESSION['UID']);
				}

				 $istrue = $student->create(); 
				 
				 		if ($istrue == true) {
					 		message("New Student [". $IDNO ."] has been created successfully!", "success");
					 		redirect('index.php');
					 	}else{
					 		message("No user has been created successfully!", "error");
					 		redirect('index.php');
					 	}
			}	 

	}
	
	function doEdit(){

			$student = new Student();

			$UID   = $_POST['UID'];

			$IDNO   = $_POST['IDNO1'];
			$FNAME	= $_POST['FNAME1'];
			$MNAME	= $_POST['MNAME1'];
			$LNAME	= $_POST['LNAME1'];
			$SEX	= isset($_POST['SEX1'])  ? $_POST['SEX1']  : '';
			$BDAY	= isset($_POST['BDAY1']) ? $_POST['BDAY1'] : '';

			$photo = handleStudentPhotoUpload();
			if ($photo === false) {
				message("Uploaded photo is not a valid image!", "error");
				redirect('index.php');
				return;
			}
					
				$student->IDNO = $IDNO;
				$student->FNAME = $FNAME;
				$student->MNAME = $MNAME;
				$student->LNAME = $LNAME;

				if ($SEX == 'Male' || $SEX == 'Female') {
					$student->SEX = $SEX;
				}

				if ($BDAY != '') {
					$student->BDAY = $BDAY;
				}

				$student->BPLACE        = isset($_POST['BPLACE1'])        ? $_POST['BPLACE1']        : '';
				$student->STATUS        = isset($_POST['STATUS1'])        ? $_POST['STATUS1']        : 'Active';
				$student->NATIONALITY   = isset($_POST['NATIONALITY1'])   ? $_POST['NATIONALITY1']   : '';
				$student->RELIGION      = isset($_POST['RELIGION1'])      ? $_POST['RELIGION1']      : '';
				$student->CONTACT_NO    = isset($_POST['CONTACT_NO1'])    ? $_POST['CONTACT_NO1']    : '';
				$student->HOME_ADD      = isset($_POST['HOME_ADD1'])      ? $_POST['HOME_ADD1']      : '';
				$student->EMAIL         = isset($_POST['EMAIL1'])         ? $_POST['EMAIL1']         : '';
				$student->LRNNO         = isset($_POST['LRNNO1'])         ? $_POST['LRNNO1']         : '';
				$student->CONTACTPERSON = isset($_POST['CONTACTPERSON1']) ? $_POST['CONTACTPERSON1'] : '';

				/* Parents' / Guardian's Information - HIPANAO SOLUTIONS,
				   ginagamit sa Print Enrollment Form. */
				$student->CIVIL_STATUS          = isset($_POST['CIVIL_STATUS1'])          ? $_POST['CIVIL_STATUS1']          : 'Single';
				$student->FATHER_NAME           = isset($_POST['FATHER_NAME1'])           ? $_POST['FATHER_NAME1']           : '';
				$student->FATHER_CONTACT        = isset($_POST['FATHER_CONTACT1'])        ? $_POST['FATHER_CONTACT1']        : '';
				$student->FATHER_EMAIL          = isset($_POST['FATHER_EMAIL1'])          ? $_POST['FATHER_EMAIL1']          : '';
				$student->FATHER_OCCUPATION     = isset($_POST['FATHER_OCCUPATION1'])     ? $_POST['FATHER_OCCUPATION1']     : '';
				$student->FATHER_DECEASED       = isset($_POST['FATHER_DECEASED1'])       ? $_POST['FATHER_DECEASED1']       : 'No';
				$student->MOTHER_NAME           = isset($_POST['MOTHER_NAME1'])           ? $_POST['MOTHER_NAME1']           : '';
				$student->MOTHER_CONTACT        = isset($_POST['MOTHER_CONTACT1'])        ? $_POST['MOTHER_CONTACT1']        : '';
				$student->MOTHER_EMAIL          = isset($_POST['MOTHER_EMAIL1'])          ? $_POST['MOTHER_EMAIL1']          : '';
				$student->MOTHER_OCCUPATION     = isset($_POST['MOTHER_OCCUPATION1'])     ? $_POST['MOTHER_OCCUPATION1']     : '';
				$student->MOTHER_DECEASED       = isset($_POST['MOTHER_DECEASED1'])       ? $_POST['MOTHER_DECEASED1']       : 'No';
				$student->GUARDIAN_NAME         = isset($_POST['GUARDIAN_NAME1'])         ? $_POST['GUARDIAN_NAME1']         : '';
				$student->GUARDIAN_RELATIONSHIP = isset($_POST['GUARDIAN_RELATIONSHIP1']) ? $_POST['GUARDIAN_RELATIONSHIP1'] : '';
				$student->GUARDIAN_CONTACT      = isset($_POST['GUARDIAN_CONTACT1'])      ? $_POST['GUARDIAN_CONTACT1']      : '';
				$student->GUARDIAN_EMAIL        = isset($_POST['GUARDIAN_EMAIL1'])        ? $_POST['GUARDIAN_EMAIL1']        : '';
				$student->GUARDIAN_ADDRESS      = isset($_POST['GUARDIAN_ADDRESS1'])      ? $_POST['GUARDIAN_ADDRESS1']      : '';

				/* Other Person Supporting / Boarding - HIPANAO SOLUTIONS. */
				$student->OTHER_PERSON_SUPPORTING = isset($_POST['OTHER_PERSON_SUPPORTING1']) ? $_POST['OTHER_PERSON_SUPPORTING1'] : '';
				$student->IS_BOARDING             = isset($_POST['IS_BOARDING1'])             ? $_POST['IS_BOARDING1']             : 'No';
				$student->WITH_FAMILY             = isset($_POST['WITH_FAMILY1'])             ? $_POST['WITH_FAMILY1']             : 'Yes';
				$student->BOARDING_ADDRESS        = isset($_POST['BOARDING_ADDRESS1'])        ? $_POST['BOARDING_ADDRESS1']        : '';

				/* Education - HIPANAO SOLUTIONS. */
				$student->ELEM_SCHOOL      = isset($_POST['ELEM_SCHOOL1'])      ? $_POST['ELEM_SCHOOL1']      : '';
				$student->ELEM_ADDRESS     = isset($_POST['ELEM_ADDRESS1'])     ? $_POST['ELEM_ADDRESS1']     : '';
				$student->ELEM_YEAR        = isset($_POST['ELEM_YEAR1'])        ? $_POST['ELEM_YEAR1']        : '';
				$student->SEC_SCHOOL       = isset($_POST['SEC_SCHOOL1'])       ? $_POST['SEC_SCHOOL1']       : '';
				$student->SEC_ADDRESS      = isset($_POST['SEC_ADDRESS1'])      ? $_POST['SEC_ADDRESS1']      : '';
				$student->SEC_YEAR         = isset($_POST['SEC_YEAR1'])         ? $_POST['SEC_YEAR1']         : '';
				$student->COLLEGE_SCHOOL   = isset($_POST['COLLEGE_SCHOOL1'])   ? $_POST['COLLEGE_SCHOOL1']   : '';
				$student->COLLEGE_ADDRESS  = isset($_POST['COLLEGE_ADDRESS1'])  ? $_POST['COLLEGE_ADDRESS1']  : '';
				$student->COLLEGE_YEAR     = isset($_POST['COLLEGE_YEAR1'])     ? $_POST['COLLEGE_YEAR1']     : '';
				$student->VOC_SCHOOL       = isset($_POST['VOC_SCHOOL1'])       ? $_POST['VOC_SCHOOL1']       : '';
				$student->VOC_ADDRESS      = isset($_POST['VOC_ADDRESS1'])      ? $_POST['VOC_ADDRESS1']      : '';
				$student->VOC_YEAR         = isset($_POST['VOC_YEAR1'])         ? $_POST['VOC_YEAR1']         : '';
				$student->OTHERS_SCHOOL    = isset($_POST['OTHERS_SCHOOL1'])    ? $_POST['OTHERS_SCHOOL1']    : '';

				/* HIPANAO SOLUTIONS - Requirements checklist: sa
				   module/enrollment na lang ito ("Document" stage). */

				if (isset($_POST['AGE1']) && $_POST['AGE1'] !== '') {
					$student->AGE = intval($_POST['AGE1']);
				}
				if (isset($_POST['COURSE_ID1']) && $_POST['COURSE_ID1'] !== '') {
					$student->COURSE_ID = intval($_POST['COURSE_ID1']);
				}

				if (isset($_POST['ACC_PASSWORD1']) && $_POST['ACC_PASSWORD1'] !== '') {
					$student->ACC_PASSWORD = sha1($_POST['ACC_PASSWORD1']);
				}

				if ($photo !== '') {
					$student->COMPANYIDNO = $photo;
				}
				
				 $istrue = $student->update($UID); 
				 if ($istrue == true){

				 	/* Kung may bagong na-upload na photo dito, i-sync din ito
				 	   papunta sa naka-link na login account (module/user),
				 	   kung meron - HIPANAO SOLUTIONS. */
				 	if ($photo !== '') {
				 		hipanao_sync_student_photo_to_user($UID);
				 	}

				 	message("Details has been Updated successfully!", "success");
				 	redirect('index.php');
				 	
				 }else{
				 	message("No user account has been updated successfully!", "error");
				 	redirect('index.php');
				 }
		
	}

	function doRegister(){

		global $mydb;

		$S_ID       = isset($_POST['R_SID'])           ? intval($_POST['R_SID'])          : 0;
		$SY_ID      = isset($_POST['R_SY'])            ? intval($_POST['R_SY'])           : 0;
		$COURSE_ID  = isset($_POST['R_COURSE'])        ? intval($_POST['R_COURSE'])       : 0;
		$YEAR_LEVEL = isset($_POST['R_YEARLEVEL'])     ? trim($_POST['R_YEARLEVEL'])      : '';
		$SEMESTER   = isset($_POST['R_SEMESTER'])      ? trim($_POST['R_SEMESTER'])       : '';
		$CATEGORY   = isset($_POST['R_CATEGORY'])      ? trim($_POST['R_CATEGORY'])       : 'New';
		$CURRICULUM = isset($_POST['R_CURRICULUM'])    ? trim($_POST['R_CURRICULUM'])     : '';
		$RESERVED   = isset($_POST['R_DATE_RESERVED']) ? trim($_POST['R_DATE_RESERVED'])  : '';

		$enrollmentPage = WEB_ROOT.'module/enrollment/index.php';

		if ($S_ID <= 0 || $SY_ID <= 0 || $COURSE_ID <= 0 || $YEAR_LEVEL == '' || $SEMESTER == '') {
			message("Please complete all the registration fields.", "error");
			redirect('index.php');
			return;
		}

		if ($RESERVED == '') { $RESERVED = date('Y-m-d'); }

		$mydb->setQuery("SELECT ENROLLMENT_ID FROM `tblenrollment` 
			WHERE S_ID     = '".$S_ID."' 
			  AND SY_ID    = '".$SY_ID."' 
			  AND SEMESTER = '".$mydb->escape_value($SEMESTER)."' 
			LIMIT 1");
		if ($mydb->num_rows() >= 1) {
			message("This student already has a record for that academic year and semester.", "error");
			redirect('index.php');
			return;
		}

		$encodedBy = isset($_SESSION['UID']) ? intval($_SESSION['UID']) : 0;
		$encodedSql = ($encodedBy > 0) ? "'".$encodedBy."'" : "NULL";

		$curriculumSql = ($CURRICULUM == '') ? "NULL" : "'".$mydb->escape_value($CURRICULUM)."'";

		$sql = "INSERT INTO `tblenrollment` 
			(`S_ID`, `COURSE_ID`, `SECTION_ID`, `SY_ID`, `YEAR_LEVEL`, `SEMESTER`, 
			 `CATEGORY`, `CURRICULUM_YR`, `DATE_RESERVED`, `DATE_ENROLLED`, `STATUS`, `ENCODED_BY`) 
			VALUES ('".$S_ID."', '".$COURSE_ID."', NULL, '".$SY_ID."', 
			'".$mydb->escape_value($YEAR_LEVEL)."', '".$mydb->escape_value($SEMESTER)."', 
			'".$mydb->escape_value($CATEGORY)."', ".$curriculumSql.", 
			'".$mydb->escape_value($RESERVED)."', NULL, 'Register', ".$encodedSql.")";

		$istrue = $mydb->InsertThis($sql);

		if ($istrue) {

			$mydb->InsertThis("UPDATE `tblstudent` SET `COURSE_ID` = '".$COURSE_ID."' WHERE `S_ID` = '".$S_ID."'");

			message("Enrollment slot registered. Go to Enrollment > Subjects to Assign this student's subjects next.", "success");
			redirect($enrollmentPage);
		} else {
			message("The registration could not be saved.", "error");
			redirect('index.php');
		}
	}

	function doDelete(){
		
				$id = 	$_GET['id'];

				$student = New Student();
	 		 	$student->delete($id);
			 
			message("Student already Deleted!","info");
			redirect('index.php');
		
	}

	/* HIPANAO SOLUTIONS - Student Login Accounts.
	   Ito ang "Confirm" na hinihiling: sinuman ang pinagmulan ng student
	   record (self-register sa register.php o dinagdag mismo ng
	   staff/admin dito sa module/student), pagka-tap ng Administrator sa
	   "Create Login Account" button sa listahan, gagawa ito ng account sa
	   tblusers (TYPE = 'Student') at ma-i-link ito pabalik sa tblstudent
	   sa pamamagitan ng ACCOUNT_UID - saka na lang makakapag-login ang
	   estudyante. Ang bagong account ay lalabas din sa module/user
	   (Manage User Accounts).

	   Password: kung nagbigay ang estudyante ng ACC_PASSWORD noong
	   pag-register/pagkadagdag (naka-sha1 na iyon, parehong paraan ng
	   pag-hash ng tblusers.PASSWORD), gagamitin ito - hindi na
	   kailangang malaman pa ng Administrator ang plaintext niyon. Kung
	   wala, ang IDNO mismo ng estudyante ang gagawing default password
	   (sasabihin ito sa Administrator sa success message, para maipasa
	   sa estudyante). */
	function doConfirmAccount(){

		global $mydb;

		$S_ID = isset($_GET['id']) ? intval($_GET['id']) : 0;

		if ($S_ID <= 0) {
			message("Invalid student record.", "error");
			redirect('index.php');
			return;
		}

		$mydb->setQuery("SELECT * FROM `tblstudent` WHERE S_ID = '".$S_ID."' LIMIT 1");
		$rows = $mydb->loadResultList();

		if (count($rows) < 1) {
			message("Student record not found.", "error");
			redirect('index.php');
			return;
		}

		$student = $rows[0];

		/* Huwag nang gumawa ulit ng bagong account kung may na-link na. */
		if (isset($student->ACCOUNT_UID) && intval($student->ACCOUNT_UID) > 0) {
			message("This student already has a login account.", "info");
			redirect(WEB_ROOT.'module/user/index.php?view=list');
			return;
		}

		if (trim((string)$student->IDNO) === '') {
			message("This student needs an ID No. before a login account can be created.", "error");
			redirect('index.php');
			return;
		}

		/* Username: IDNO. Kung sakaling nagbanggaan (halimbawa, may
		   na-delete nang student record noon pero naiwan ang username
		   niya sa tblusers), maglagay na lang ng maiksing random suffix
		   para hindi mabara ang Administrator. */
		$username = trim($student->IDNO);
		$mydb->setQuery("SELECT UID FROM `tblusers` WHERE USERNAME = '".$mydb->escape_value($username)."' LIMIT 1");
		if ($mydb->num_rows() > 0) {
			$username = $username.'-'.mt_rand(100, 999);
		}

		$generatedPassword = '';

		if (isset($student->ACC_PASSWORD) && trim((string)$student->ACC_PASSWORD) !== '') {
			/* Naka-sha1 na ito mula noong pag-register/pagkadagdag. */
			$passwordHash = $student->ACC_PASSWORD;
		} else {
			/* Walang naibigay na password - ang IDNO ang gagawing default,
			   ipapaalam sa Administrator sa success message. */
			$generatedPassword = trim($student->IDNO);
			$passwordHash = sha1($generatedPassword);
		}

		$displayName = trim($student->FNAME.' '.$student->LNAME);
		if ($displayName === '') { $displayName = $username; }
		if (strlen($displayName) > 30) { $displayName = substr($displayName, 0, 30); }

		/* tblusers.ADDEDBY/MODIFIEDBY ay NOT NULL (int(3)) - '0' na lang
		   kung walang session UID (hindi dapat mangyari - naka-login na
		   dapat ang Administrator bago pa marating ang action na ito). */
		$addedBy = isset($_SESSION['UID']) ? intval($_SESSION['UID']) : 0;
		$addedBySql = "'".$addedBy."'";

		$sql = "INSERT INTO `tblusers`
				(`DISPLAYNAME`, `USERNAME`, `PASSWORD`, `TYPE`, `PICTURE`, `ADDEDBY`, `DATEADDED`, `MODIFIEDBY`, `DATEMODIFIED`, `STATUSACTIVE`)
				VALUES
				('".$mydb->escape_value($displayName)."',
				 '".$mydb->escape_value($username)."',
				 '".$mydb->escape_value($passwordHash)."',
				 'Student',
				 NULL,
				 ".$addedBySql.",
				 CURDATE(),
				 ".$addedBySql.",
				 CURDATE(),
				 1)";

		$istrue = $mydb->InsertThis($sql);

		if (!$istrue) {
			message("The login account could not be created.", "error");
			redirect('index.php');
			return;
		}

		$newUid = $mydb->insert_id();

		$mydb->InsertThis("UPDATE `tblstudent` SET `ACCOUNT_UID` = '".intval($newUid)."' WHERE `S_ID` = '".$S_ID."'");

		/* Kung may litrato na ang estudyante mula pa noong pagkadagdag/
		   pagre-register (tblstudent.COMPANYIDNO), ikopya na rin agad ito
		   papunta sa bagong login account na ito - HIPANAO SOLUTIONS.
		   Kung wala pang litrato, tahimik lang itong hindi gagawa ng
		   anuman (hindi ito error). */
		hipanao_sync_student_photo_to_user($S_ID);

		if ($generatedPassword !== '') {
			message("Login account created! Username: [".$username."], Default Password: [".$generatedPassword."]. Please give this to the student and ask them to change it after logging in.", "success");
		} else {
			message("Login account created! Username: [".$username."]. The student can log in using the password they set during registration.", "success");
		}

		redirect(WEB_ROOT.'module/user/index.php?view=list');
	}

?>