<?php 

require_once("../../include/initialize.php");
global $mydb;

$act = isset($_POST['act']) ? $_POST['act'] : '';

if ($act === 'register_info') {

	$sid = intval($_POST['UID']);
	$output = array();

	$mydb->setQuery("SELECT * FROM `tblstudent` WHERE S_ID = '".$sid."' LIMIT 1");
	foreach ($mydb->loadResultList() as $row) {
		$output['S_ID']      = $row->S_ID;
		$output['IDNO']      = $row->IDNO;
		$output['FULLNAME']  = trim($row->LNAME.', '.$row->FNAME.' '.$row->MNAME);
		$output['COURSE_ID'] = $row->COURSE_ID;

		$output['PICTURE_URL'] = (isset($row->COMPANYIDNO) && $row->COMPANYIDNO != '')
			? WEB_ROOT.'module/student/image/'.$row->COMPANYIDNO
			: '';
	}

	$output['ACTIVE_SY']  = '';
	$output['ACTIVE_AY']  = '';
	$mydb->setQuery("SELECT SY_ID, SCHOOL_YEAR FROM `tblschoolyear` WHERE STATUS = 'Active' ORDER BY SY_ID DESC LIMIT 1");
	foreach ($mydb->loadResultList() as $row) {
		$output['ACTIVE_SY'] = $row->SY_ID;
		$output['ACTIVE_AY'] = $row->SCHOOL_YEAR;
	}

	$output['SUGGEST_CATEGORY'] = 'New';
	$mydb->setQuery("SELECT ENROLLMENT_ID FROM `tblenrollment` WHERE S_ID = '".$sid."' LIMIT 1");
	if ($mydb->num_rows() > 0) {
		$output['SUGGEST_CATEGORY'] = 'Old';
	}

	echo json_encode($output);
	exit;
}

if (isset($_POST['UID'])) {
	$output = array();
	$sid = intval($_POST["UID"]);
	$query =	"SELECT * FROM `tblstudent` 
		WHERE S_ID = '".$sid."' 
		LIMIT 1";
	$mydb->setQuery($query);
	$result = $mydb->loadResultList();

	foreach($result as $row)
	{ 
		$output["UID"]   = $row->S_ID;
		$output["IDNO"]  = $row->IDNO;
		$output["FNAME"] = $row->FNAME;
		$output["MNAME"] = $row->MNAME;
		$output["LNAME"] = $row->LNAME;
		$output["SEX"]   = $row->SEX;

		$bday = $row->BDAY;
		if ($bday === null || $bday == '0000-00-00' || $bday == '') {
			$output["BDAY"] = '';
		} else {
			$output["BDAY"] = substr($bday, 0, 10);
		}

		$output["BPLACE"]       = isset($row->BPLACE) ? $row->BPLACE : '';
		$output["STATUS"]       = isset($row->STATUS) ? $row->STATUS : '';
		$output["AGE"]          = isset($row->AGE) ? $row->AGE : '';
		$output["NATIONALITY"]  = isset($row->NATIONALITY) ? $row->NATIONALITY : '';
		$output["RELIGION"]     = isset($row->RELIGION) ? $row->RELIGION : '';
		$output["CONTACT_NO"]   = isset($row->CONTACT_NO) ? $row->CONTACT_NO : '';
		$output["HOME_ADD"]     = isset($row->HOME_ADD) ? $row->HOME_ADD : '';
		$output["EMAIL"]        = isset($row->EMAIL) ? $row->EMAIL : '';
		$output["LRNNO"]        = isset($row->LRNNO) ? $row->LRNNO : '';
		$output["CONTACTPERSON"]= isset($row->CONTACTPERSON) ? $row->CONTACTPERSON : '';
		$output["COURSE_ID"]    = isset($row->COURSE_ID) ? $row->COURSE_ID : '';

		/* Parents' / Guardian's Information - HIPANAO SOLUTIONS. */
		$output["CIVIL_STATUS"]          = isset($row->CIVIL_STATUS) ? $row->CIVIL_STATUS : 'Single';
		$output["FATHER_NAME"]           = isset($row->FATHER_NAME) ? $row->FATHER_NAME : '';
		$output["FATHER_CONTACT"]        = isset($row->FATHER_CONTACT) ? $row->FATHER_CONTACT : '';
		$output["FATHER_EMAIL"]          = isset($row->FATHER_EMAIL) ? $row->FATHER_EMAIL : '';
		$output["FATHER_OCCUPATION"]     = isset($row->FATHER_OCCUPATION) ? $row->FATHER_OCCUPATION : '';
		$output["FATHER_DECEASED"]       = isset($row->FATHER_DECEASED) ? $row->FATHER_DECEASED : 'No';
		$output["MOTHER_NAME"]           = isset($row->MOTHER_NAME) ? $row->MOTHER_NAME : '';
		$output["MOTHER_CONTACT"]        = isset($row->MOTHER_CONTACT) ? $row->MOTHER_CONTACT : '';
		$output["MOTHER_EMAIL"]          = isset($row->MOTHER_EMAIL) ? $row->MOTHER_EMAIL : '';
		$output["MOTHER_OCCUPATION"]     = isset($row->MOTHER_OCCUPATION) ? $row->MOTHER_OCCUPATION : '';
		$output["MOTHER_DECEASED"]       = isset($row->MOTHER_DECEASED) ? $row->MOTHER_DECEASED : 'No';
		$output["GUARDIAN_NAME"]         = isset($row->GUARDIAN_NAME) ? $row->GUARDIAN_NAME : '';
		$output["GUARDIAN_RELATIONSHIP"] = isset($row->GUARDIAN_RELATIONSHIP) ? $row->GUARDIAN_RELATIONSHIP : '';
		$output["GUARDIAN_CONTACT"]      = isset($row->GUARDIAN_CONTACT) ? $row->GUARDIAN_CONTACT : '';
		$output["GUARDIAN_EMAIL"]        = isset($row->GUARDIAN_EMAIL) ? $row->GUARDIAN_EMAIL : '';
		$output["GUARDIAN_ADDRESS"]      = isset($row->GUARDIAN_ADDRESS) ? $row->GUARDIAN_ADDRESS : '';

		/* Other Person Supporting / Boarding - HIPANAO SOLUTIONS. */
		$output["OTHER_PERSON_SUPPORTING"] = isset($row->OTHER_PERSON_SUPPORTING) ? $row->OTHER_PERSON_SUPPORTING : '';
		$output["IS_BOARDING"]             = isset($row->IS_BOARDING) ? $row->IS_BOARDING : 'No';
		$output["WITH_FAMILY"]             = isset($row->WITH_FAMILY) ? $row->WITH_FAMILY : 'Yes';
		$output["BOARDING_ADDRESS"]        = isset($row->BOARDING_ADDRESS) ? $row->BOARDING_ADDRESS : '';

		/* Education - HIPANAO SOLUTIONS. */
		$output["ELEM_SCHOOL"]     = isset($row->ELEM_SCHOOL) ? $row->ELEM_SCHOOL : '';
		$output["ELEM_ADDRESS"]    = isset($row->ELEM_ADDRESS) ? $row->ELEM_ADDRESS : '';
		$output["ELEM_YEAR"]       = isset($row->ELEM_YEAR) ? $row->ELEM_YEAR : '';
		$output["SEC_SCHOOL"]      = isset($row->SEC_SCHOOL) ? $row->SEC_SCHOOL : '';
		$output["SEC_ADDRESS"]     = isset($row->SEC_ADDRESS) ? $row->SEC_ADDRESS : '';
		$output["SEC_YEAR"]        = isset($row->SEC_YEAR) ? $row->SEC_YEAR : '';
		$output["COLLEGE_SCHOOL"]  = isset($row->COLLEGE_SCHOOL) ? $row->COLLEGE_SCHOOL : '';
		$output["COLLEGE_ADDRESS"] = isset($row->COLLEGE_ADDRESS) ? $row->COLLEGE_ADDRESS : '';
		$output["COLLEGE_YEAR"]    = isset($row->COLLEGE_YEAR) ? $row->COLLEGE_YEAR : '';
		$output["VOC_SCHOOL"]      = isset($row->VOC_SCHOOL) ? $row->VOC_SCHOOL : '';
		$output["VOC_ADDRESS"]     = isset($row->VOC_ADDRESS) ? $row->VOC_ADDRESS : '';
		$output["VOC_YEAR"]        = isset($row->VOC_YEAR) ? $row->VOC_YEAR : '';
		$output["OTHERS_SCHOOL"]   = isset($row->OTHERS_SCHOOL) ? $row->OTHERS_SCHOOL : '';

		/* HIPANAO SOLUTIONS - Requirements checklist ay hindi na kasama
		   dito - sa module/enrollment na lang ito ("Document" stage). */

		$output["PICTURE_URL"] = (isset($row->COMPANYIDNO) && $row->COMPANYIDNO != '')
			? WEB_ROOT.'module/student/image/'.$row->COMPANYIDNO
			: '';
	}
	echo json_encode($output);
}else{
	
	$output = array();
	$query = "SELECT `S_ID`, `LNAME`, `FNAME`, `MNAME`, `SEX`, `BDAY`, `COMPANYIDNO`, `STATUS`, `ACCOUNT_UID` FROM `tblstudent`";

	if(isset($_POST["search"]["value"]))
	{
	$query .= " where `LNAME` LIKE '%".$_POST["search"]["value"]."%' ";
	}
	if(isset($_POST["order"]))
	{
		$query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
	}
	else
	{
		$query .= 'ORDER BY `S_ID` DESC ';
	}
	if($_POST["length"] != -1)
	{
		$query .= " LIMIT " . $_POST['start'] . ", " . $_POST['length'] . "";
	}
	$mydb->setQuery($query);
	$cur = $mydb->loadResultList();
	$data = array();
	$filtered_rows = $mydb->num_rows();
	$i = 1;	
	foreach ($cur as $result) {
	$sub_array = array();

		$photoFile = (isset($result->COMPANYIDNO) && $result->COMPANYIDNO != '')
			? WEB_ROOT.'module/student/image/'.$result->COMPANYIDNO
			: WEB_ROOT.'module/student/image/1.png';

		$sub_array[] =$i;

		$sub_array[] = '<div class="hipanao-cell-user"><img src="'.$photoFile.'" class="hipanao-avatar" onerror="this.src=\''.WEB_ROOT.'module/student/image/1.png\'"><span>'.htmlspecialchars($result->LNAME).'</span></div>';
		$sub_array[] = htmlspecialchars($result->FNAME);
		$sub_array[] = htmlspecialchars($result->MNAME);
		$sub_array[] = hipanao_badge($result->SEX);
		$sub_array[] = $result->BDAY;
		$sub_array[] = hipanao_badge($result->STATUS);

		$hasAccount = isset($result->ACCOUNT_UID) && intval($result->ACCOUNT_UID) > 0;

		$sub_array[] = '<div class="text-nowrap">'
			.hipanao_action_btn('edit', 'UID', $result->S_ID, 'editEntry')
			.'<a href="index.php?view=view&id='.$result->S_ID.'">'.hipanao_action_btn('view', 'data-noop', '1', '') .'</a>'
			.hipanao_action_btn('approve', 'UID', $result->S_ID, 'registerEntry', 'Register Enrollment Slot')
			.($hasAccount
				? hipanao_action_btn('hasaccount', 'data-noop', '1', '')
				: hipanao_action_btn('account', 'UID', $result->S_ID, 'createAccountEntry'))
			.hipanao_action_btn('delete', 'UID', $result->S_ID, 'deleteEntry')
			.'</div>';
		$data[] = $sub_array;
	$i = $i + 1;		
	}
	function get_total_all_records()
	{
		global $mydb;
		$statement = "SELECT `S_ID`, `LNAME`, `FNAME`, `MNAME`, `SEX`, `BDAY` FROM `tblstudent`";
		$mydb->setQuery($statement);
		return $mydb->num_rows();
	}

	$output = array('data' 			   => $data, 
					"recordsTotal"	   => $filtered_rows,
					"recordsFiltered"	=>	get_total_all_records() );
	echo json_encode($output);
}
?>