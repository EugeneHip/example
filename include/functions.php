<?php
	function strip_zeros_from_date($marked_string="") {
		//first remove the marked zeros
		$no_zeros = str_replace('*0','',$marked_string);
		$cleaned_string = str_replace('*0','',$no_zeros);
		return $cleaned_string;
	}
	function redirect_to($location = NULL) {
		if($location != NULL){
			header("Location: {$location}");
			exit;
		}
	}
	function redirect($location=Null){
		if($location!=Null){
			echo "<script>
					window.location='{$location}'
				</script>";	
		}else{
			echo 'error location';
		}
		 
	}
	function output_message($message="") {
	
		if(!empty($message)){
		return "<p class=\"message\">{$message}</p>";
		}else{
			return "";
		}
	}
	function AllowAccessss($FormID=0)
    {
      global $mydb;
      $mydb->setQuery("SELECT * FROM user_permission Where UserType= '{$_SESSION['TYPE']}' AND FormID ={$FormID} AND AllowOpen=1");
       $row_count = $mydb->num_rows();
         
       if ($row_count > 0 ){
        return true;
       }else{
         return false;
       } 
        
    }	
	function date_toText($datetime=""){
		$nicetime = strtotime($datetime);
		return strftime("%B %d, %Y at %I:%M %p", $nicetime);	
					
	}
	function autoload($class_name) {
		$class_name = strtolower($class_name);
		$path = LIB_PATH.DS."{$class_name}.php";
		if(file_exists($path)){
			require_once($path);
		}else{
			die("The file {$class_name}.php could not be found.");
		}
					
	}

	function currentpage_public(){
		$this_page = $_SERVER['SCRIPT_NAME']; // will return /path/to/file.php
	    $bits = explode('/',$this_page);
	    $this_page = $bits[count($bits)-1]; // will return file.php, with parameters if case, like file.php?id=2
	    $this_script = $bits[0]; // will return file.php, no parameters*/
		 return $bits[2];
	  
	}

	function currentpage_admin(){
		$this_page = $_SERVER['SCRIPT_NAME']; // will return /path/to/file.php
	    $bits = explode('/',$this_page);
	    $this_page = $bits[count($bits)-1]; // will return file.php, with parameters if case, like file.php?id=2
	    $this_script = $bits[0]; // will return file.php, no parameters*/
		 return $bits[4];
	  
	}
  // echo "string " .currentpage_admin()."<br/>";

	function curPageName() {
 return substr($_SERVER['REQUEST_URI'], 21, strrpos($_SERVER['REQUEST_URI'], '/')-24);
}

  // echo "The current page name is ".curPageName();

function currentpage(){
		$this_page = $_SERVER['SCRIPT_NAME']; // will return /path/to/file.php
	    $bits = explode('/',$this_page);
	    $this_page = $bits[count($bits)-1]; // will return file.php, with parameters if case, like file.php?id=2
	    $this_script = $bits[0]; // will return file.php, no parameters*/
		 return $bits[3];
}
	 

	/* --- Enrollment workflow helpers (ported for the Enrollment / Enrollment Details /
	   Grades / Payment / History Enrollment modules) --- */
	function syncEnrollmentStatusFromSubjects($EID) {
		global $mydb;
		$EID = intval($EID);
		if ($EID <= 0) { return; }

		$mydb->setQuery("SELECT STATUS FROM `tblenrollment` WHERE ENROLLMENT_ID = '".$EID."' LIMIT 1");
		$rows = $mydb->loadResultList();
		if (count($rows) < 1) { return; }
		$enr = $rows[0];

		$mydb->setQuery("SELECT DETAIL_ID FROM `tblenrollmentdetails` WHERE ENROLLMENT_ID = '".$EID."'");
		$subjectCount = $mydb->num_rows();

		if ($subjectCount > 0 && $enr->STATUS == 'Register') {
			$mydb->InsertThis("UPDATE `tblenrollment` 
				SET STATUS = 'Assign' 
				WHERE ENROLLMENT_ID = '".$EID."'");
		}

		/* Kung tinanggal lahat ng subject, ibalik sa Register - kahit na
		   Document, Paid, o Enroll na - dahil wala nang laman ang
		   enrollment na ito. */
		if ($subjectCount == 0 && in_array($enr->STATUS, array('Assign', 'Document', 'Paid', 'Enroll'))) {
			$mydb->InsertThis("UPDATE `tblenrollment` 
				SET STATUS = 'Register', DATE_ENROLLED = NULL 
				WHERE ENROLLMENT_ID = '".$EID."'");
		}

		if ($subjectCount > 0) {
			maybeMarkPaid($EID);
		}
	}

	function currentEnrollmentStatus($EID) {
		global $mydb;
		$mydb->setQuery("SELECT STATUS FROM `tblenrollment` WHERE ENROLLMENT_ID = '".intval($EID)."' LIMIT 1");
		$rows = $mydb->loadResultList();
		return (count($rows) >= 1) ? $rows[0]->STATUS : '';
	}

	function saveEnrollmentSubjects($EID, $postedSubjectIds) {
		global $mydb;
		$EID = intval($EID);
		if ($EID <= 0) { return 0; }

		$mydb->setQuery("SELECT COURSE_ID FROM `tblenrollment` WHERE ENROLLMENT_ID = '".$EID."' LIMIT 1");
		$rows = $mydb->loadResultList();
		if (count($rows) < 1) { return 0; }
		$courseId = intval($rows[0]->COURSE_ID);

		$validIds = array();
		if (is_array($postedSubjectIds)) {
			foreach ($postedSubjectIds as $sid) {
				$sid = intval($sid);
				if ($sid > 0) { $validIds[] = $sid; }
			}
		}

		/* HIPANAO SOLUTIONS - Major subjects belong to one specific
		   course (COURSE_ID set), pero ang Minor subjects (GE, PE,
		   NSTP, atbp.) ay bukas sa LAHAT ng course - naka-store na
		   COURSE_ID IS NULL ang mga iyon sa tblsubjects. Kaya dapat
		   parehong kasama dito sa "allowed" whitelist, kung hindi
		   ma-filter out ang mga minor subject pag nag-save ng
		   subjects (tingnan din: module/enrollment/ajax.php,
		   module/enrollmentdetails/ajax.php, module/payment/ajax.php -
		   parehong pattern ang ginamit doon). */
		$mydb->setQuery("SELECT SUBJECT_ID FROM `tblsubjects` WHERE COURSE_ID = '".$courseId."' OR COURSE_ID IS NULL");
		$allowed = array();
		foreach ($mydb->loadResultList() as $s) { $allowed[] = intval($s->SUBJECT_ID); }

		$toKeep = array_values(array_intersect($validIds, $allowed));

		$keepSql = count($toKeep) > 0 ? implode(",", $toKeep) : "0";
		$mydb->InsertThis("DELETE FROM `tblenrollmentdetails` 
			WHERE ENROLLMENT_ID = '".$EID."' AND SUBJECT_ID NOT IN (".$keepSql.")");

		$mydb->setQuery("SELECT SUBJECT_ID FROM `tblenrollmentdetails` WHERE ENROLLMENT_ID = '".$EID."'");
		$existing = array();
		foreach ($mydb->loadResultList() as $e) { $existing[] = intval($e->SUBJECT_ID); }

		foreach ($toKeep as $sid) {
			if (!in_array($sid, $existing)) {
				$mydb->InsertThis("INSERT INTO `tblenrollmentdetails` (ENROLLMENT_ID, SUBJECT_ID) VALUES ('".$EID."', '".$sid."')");
			}
		}

		syncEnrollmentStatusFromSubjects($EID);

		return count($toKeep);
	}

	function tuitionBreakdownForEnrollment($EID) {
		global $mydb;
		$EID = intval($EID);

		$breakdown = array(
			'subjects'      => array(),
			'major_units'   => 0,
			'minor_units'   => 0,
			'major_amount'  => 0.0,
			'minor_amount'  => 0.0,
			'total_amount'  => 0.0,
		);

		$mydb->setQuery("SELECT s.SUBJECT_ID, s.SUBJECT_CODE, s.SUBJECT_NAME, s.UNITS, s.SUBJECT_TYPE 
			FROM `tblenrollmentdetails` d 
			JOIN `tblsubjects` s ON s.SUBJECT_ID = d.SUBJECT_ID 
			WHERE d.ENROLLMENT_ID = '".$EID."' 
			ORDER BY s.SUBJECT_TYPE ASC, s.SUBJECT_CODE ASC");

		foreach ($mydb->loadResultList() as $s) {
			$type  = ($s->SUBJECT_TYPE == 'Minor') ? 'Minor' : 'Major';
			$units = intval($s->UNITS);
			$rate  = ($type == 'Minor') ? MINOR_UNIT_RATE : MAJOR_UNIT_RATE;
			$amount = $units * $rate;

			if ($type == 'Minor') {
				$breakdown['minor_units']  += $units;
				$breakdown['minor_amount'] += $amount;
			} else {
				$breakdown['major_units']  += $units;
				$breakdown['major_amount'] += $amount;
			}

			$breakdown['subjects'][] = array(
				'SUBJECT_ID'   => $s->SUBJECT_ID,
				'SUBJECT_CODE' => $s->SUBJECT_CODE,
				'SUBJECT_NAME' => $s->SUBJECT_NAME,
				'UNITS'        => $units,
				'SUBJECT_TYPE' => $type,
				'RATE'         => $rate,
				'AMOUNT'       => $amount,
			);
		}

		$breakdown['total_amount'] = $breakdown['major_amount'] + $breakdown['minor_amount'];
		return $breakdown;
	}

	function paymentTotalsForEnrollment($EID) {
		global $mydb;
		$EID = intval($EID);

		$totals = array('Enrollment Fee' => 0.0, 'Tuition Fee' => 0.0);

		$mydb->setQuery("SELECT PAYMENT_TYPE, SUM(AMOUNT) AS TOTAL 
			FROM `tblpayments` 
			WHERE ENROLLMENT_ID = '".$EID."' 
			GROUP BY PAYMENT_TYPE");
		foreach ($mydb->loadResultList() as $row) {
			$totals[$row->PAYMENT_TYPE] = floatval($row->TOTAL);
		}

		return $totals;
	}

	/* Tinatawag pagkatapos mag-save ng subjects at pagkatapos magbayad -
	   kapag bayad na nang buo ang Enrollment Fee (at may subject na),
	   awtomatiko nang nagiging "Paid" ang status. HINDI pa ito "Enroll" -
	   sadyang manual na hakbang pa ang pag-Enroll (tingnan ang
	   markAsEnrolled() sa ibaba) para doon lang lumabas ang Print
	   Enrollment Form kapag talagang tiniyak ng staff na i-enroll na
	   ang estudyante. */
	function maybeMarkPaid($EID) {
		global $mydb;
		$EID = intval($EID);
		if ($EID <= 0) { return false; }

		$mydb->setQuery("SELECT STATUS FROM `tblenrollment` WHERE ENROLLMENT_ID = '".$EID."' LIMIT 1");
		$rows = $mydb->loadResultList();
		if (count($rows) < 1) { return false; }
		$status = $rows[0]->STATUS;

		/* HIPANAO SOLUTIONS - BUG FIX: dating "Assign" ang required status
	   bago maging "Paid" - ngayon "Document" na (kailangan munang
	   masuri ng REGISTRAR STAFF ang mga naisumiteng dokumento bago
	   tanggapin ang bayad), kaayon ng bagong 5-hakbang na daloy:
	   Register -> Assign -> Document -> Paid -> Enroll. */
	if ($status != 'Document') { return false; }

		$mydb->setQuery("SELECT DETAIL_ID FROM `tblenrollmentdetails` WHERE ENROLLMENT_ID = '".$EID."'");
		if ($mydb->num_rows() < 1) { return false; }

		$totals = paymentTotalsForEnrollment($EID);
		if ($totals['Enrollment Fee'] < ENROLLMENT_FEE) { return false; }

		$mydb->InsertThis("UPDATE `tblenrollment` 
			SET STATUS = 'Paid' 
			WHERE ENROLLMENT_ID = '".$EID."'");
		return true;
	}

	/* HIPANAO SOLUTIONS - Manual na hakbang na "Enroll". Tinatawag mula sa
	   Enrollment module (action=enroll) kapag na-tap ng staff ang aksyong
	   "Enroll" sa isang estudyanteng "Paid" na ang STATUS. Dito lang
	   opisyal na nagiging "Enroll" ang record, saka na dito bubukas ang
	   Print Enrollment Form (module/enrollmentdetails/print.php) at
	   lilipat ang estudyante papunta sa Enrollment Details module. */
	function markAsEnrolled($EID) {
		global $mydb;
		$EID = intval($EID);
		if ($EID <= 0) { return false; }

		$mydb->setQuery("SELECT S_ID, STATUS FROM `tblenrollment` WHERE ENROLLMENT_ID = '".$EID."' LIMIT 1");
		$rows = $mydb->loadResultList();
		if (count($rows) < 1) { return false; }
		$status = $rows[0]->STATUS;
		$S_ID   = $rows[0]->S_ID;

		if ($status != 'Paid') { return false; }

		$mydb->InsertThis("UPDATE `tblenrollment` 
			SET STATUS = 'Enroll', DATE_ENROLLED = IFNULL(DATE_ENROLLED, CURDATE()) 
			WHERE ENROLLMENT_ID = '".$EID."'");

		/* HIPANAO SOLUTIONS - Kung "Register Again" ang dinaanan ng
		   estudyanteng ito (may naunang record na "Enroll" pa rin para
		   sa ibang semester/taon), ilipat na ang naunang record sa
		   "Completed" ngayong opisyal na naka-Enroll na sya sa BAGONG
		   term. Dito na sya mawawala sa Enrollment Details (STATUS =
		   'Enroll' lang ang ipinapakita doon) at lilipat na sa History
		   Enrollment (module/hitoryenrollment) - iisang "Enroll" record
		   lang dapat ang makikita kada estudyante kahit kailan. */
		$mydb->setQuery("SELECT ENROLLMENT_ID FROM `tblenrollment` 
			WHERE S_ID = '".intval($S_ID)."' 
			  AND ENROLLMENT_ID <> '".$EID."' 
			  AND STATUS = 'Enroll'");
		$superseded = array();
		foreach ($mydb->loadResultList() as $row) { $superseded[] = intval($row->ENROLLMENT_ID); }

		$mydb->InsertThis("UPDATE `tblenrollment` 
			SET STATUS = 'Completed' 
			WHERE S_ID = '".intval($S_ID)."' 
			  AND ENROLLMENT_ID <> '".$EID."' 
			  AND STATUS = 'Enroll'");

		/* HIPANAO SOLUTIONS - Dito na-a-archive papunta sa
		   tblhistoryenrollment ang naunang record(s) na naging
		   "Completed" - dito na sila makikita sa History Enrollment
		   module (module/hitoryenrollment). */
		foreach ($superseded as $sid) { archiveEnrollmentToHistory($sid); }

		return true;
	}

	/* HIPANAO SOLUTIONS - Nagsa-save ng snapshot ng isang `tblenrollment`
	   record papunta sa `tblhistoryenrollment` (History Enrollment
	   module) sa mismong sandali na naging "Completed" ito (Register
	   Again + nag-Enroll ulit ang estudyante sa bagong term - tingnan
	   ang markAsEnrolled() sa ibaba) o "Dropped" (module/enrollmentdetails
	   ::doDrop()). UNIQUE ang ENROLLMENT_ID sa tblhistoryenrollment kaya
	   INSERT ... ON DUPLICATE KEY UPDATE - kung sakaling ma-archive ulit
	   ang parehong record (halimbawa: naibalik at na-drop ulit sa
	   ibang pagkakataon), ina-update na lang ang row na ito sa halip
	   na gumawa ng duplicate. */
	function archiveEnrollmentToHistory($EID) {
		global $mydb;
		$EID = intval($EID);
		if ($EID <= 0) { return false; }

		$mydb->setQuery("SELECT S_ID, COURSE_ID, SY_ID, YEAR_LEVEL, SEMESTER, CATEGORY, STATUS, DATE_RESERVED, DATE_ENROLLED 
			FROM `tblenrollment` WHERE ENROLLMENT_ID = '".$EID."' LIMIT 1");
		$rows = $mydb->loadResultList();
		if (count($rows) < 1) { return false; }
		$r = $rows[0];

		if (!in_array($r->STATUS, array('Completed', 'Dropped'))) { return false; }

		$reservedSql = ($r->DATE_RESERVED === null || $r->DATE_RESERVED == '0000-00-00') ? "NULL" : "'".$mydb->escape_value($r->DATE_RESERVED)."'";
		$enrolledSql = ($r->DATE_ENROLLED === null || $r->DATE_ENROLLED == '0000-00-00') ? "NULL" : "'".$mydb->escape_value($r->DATE_ENROLLED)."'";

		return $mydb->InsertThis("INSERT INTO `tblhistoryenrollment` 
				(ENROLLMENT_ID, S_ID, COURSE_ID, SY_ID, YEAR_LEVEL, SEMESTER, CATEGORY, STATUS, DATE_RESERVED, DATE_ENROLLED, DATE_ARCHIVED) 
			VALUES 
				('".$EID."', '".intval($r->S_ID)."', '".intval($r->COURSE_ID)."', '".intval($r->SY_ID)."', 
				 '".$mydb->escape_value($r->YEAR_LEVEL)."', '".$mydb->escape_value($r->SEMESTER)."', 
				 '".$mydb->escape_value($r->CATEGORY)."', '".$mydb->escape_value($r->STATUS)."', 
				 ".$reservedSql.", ".$enrolledSql.", NOW()) 
			ON DUPLICATE KEY UPDATE 
				STATUS = VALUES(STATUS), YEAR_LEVEL = VALUES(YEAR_LEVEL), SEMESTER = VALUES(SEMESTER), 
				CATEGORY = VALUES(CATEGORY), DATE_ENROLLED = VALUES(DATE_ENROLLED), DATE_ARCHIVED = NOW()");
	}

	/* HIPANAO SOLUTIONS - Bantay para sa "Register Again"
	   (module/enrollmentdetails). Hindi dapat makapag-register ulit
	   ang isang estudyante (bagong slot para sa susunod na term) kung
	   ang KASALUKUYAN niyang enrollment ay: (1) hindi pa bayad nang
	   buo ang Tuition Fee, o (2) wala pa siyang kumpletong grade sa
	   lahat ng subject na kinuha dito. Ginagamit ito PAREHO sa
	   module/enrollmentdetails/ajax.php (act=register_info, para hindi
	   na lang basta bubukas ang Register Again modal) AT sa
	   module/enrollmentdetails/controller.php::doRegisterAgain() (para
	   hindi din ito ma-bypass sa pamamagitan ng direktang POST). */
	function canRegisterAgain($EID) {
		global $mydb;
		$EID = intval($EID);

		$result = array('ok' => false, 'reason' => '', 'tuition_ok' => false, 'grades_ok' => false);

		if ($EID <= 0) {
			$result['reason'] = "That enrollment record no longer exists.";
			return $result;
		}

		$mydb->setQuery("SELECT ENROLLMENT_ID FROM `tblenrollment` WHERE ENROLLMENT_ID = '".$EID."' LIMIT 1");
		if ($mydb->num_rows() < 1) {
			$result['reason'] = "That enrollment record no longer exists.";
			return $result;
		}

		$breakdown = tuitionBreakdownForEnrollment($EID);
		$totals    = paymentTotalsForEnrollment($EID);
		$tuitionOk = ($totals['Tuition Fee'] >= $breakdown['total_amount']);

		$mydb->setQuery("SELECT COUNT(*) AS CNT FROM `tblenrollmentdetails` WHERE ENROLLMENT_ID = '".$EID."'");
		$subjRows  = $mydb->loadResultList();
		$subjCount = (count($subjRows) >= 1) ? intval($subjRows[0]->CNT) : 0;

		$mydb->setQuery("SELECT COUNT(*) AS CNT FROM `tblgrades` WHERE ENROLLMENT_ID = '".$EID."' AND GRADE IS NOT NULL");
		$gradedRows  = $mydb->loadResultList();
		$gradedCount = (count($gradedRows) >= 1) ? intval($gradedRows[0]->CNT) : 0;

		$gradesOk = ($subjCount > 0 && $gradedCount >= $subjCount);

		$result['tuition_ok'] = $tuitionOk;
		$result['grades_ok']  = $gradesOk;
		$result['ok']         = ($tuitionOk && $gradesOk);

		if (!$tuitionOk && !$gradesOk) {
			$result['reason'] = "This student still has an unpaid tuition balance and does not have complete grades yet for the current enrollment. Please settle payment and complete grade encoding first.";
		} else if (!$tuitionOk) {
			$result['reason'] = "This student still has an unpaid tuition balance for the current enrollment. Please settle payment first.";
		} else if (!$gradesOk) {
			$result['reason'] = "This student does not have complete grades yet for the current enrollment. Please encode grades first.";
		}

		return $result;
	}

	function generateORNumber() {
		return 'OR-'.date('Ymd').'-'.mt_rand(1000, 9999);
	}

	function msgBox($msg=""){
		?>
		<script type="text/javascript">
			 alert(<?php echo $msg; ?>)
		</script>
		<?php
	}
		
?>