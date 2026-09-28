-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 21, 2026 at 12:07 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `alumni_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `alumni_details`
--

CREATE TABLE `alumni_details` (
  `AlumniID` int(11) NOT NULL,
  `InstitutionName` varchar(255) NOT NULL,
  `Degree` varchar(100) NOT NULL,
  `FieldOfStudy` varchar(100) DEFAULT NULL,
  `StartDate` date DEFAULT NULL,
  `EndDate` date DEFAULT NULL,
  `logo` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `S_ID` int(11) DEFAULT NULL,
  `AddedBy` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `alumni_details`
--

INSERT INTO `alumni_details` (`AlumniID`, `InstitutionName`, `Degree`, `FieldOfStudy`, `StartDate`, `EndDate`, `logo`, `description`, `S_ID`, `AddedBy`) VALUES
(6, 'PIES DIGITAL', 'MIT', 'SYSTEM DEVELOPMENT', '2024-06-26', '2024-06-25', 'image/csr-scc.png', 'Avtech Solution', NULL, 1),
(7, 'HIPANAO SOLUTIONS', 'BSED-MATH', 'Research', '1988-09-23', '2024-06-25', 'image/ejb.png', 'Research is defined as the creation of new knowledge and/or the use of existing knowledge in a new and creative way so as to generate new concepts, methodologies and understandings. This could include synthesis and analysis of previous research to the extent that it leads to new and creative outcomes.', NULL, 1),
(8, 'Diocese of San Carlos', 'DOSC', 'Church System', '2022-01-01', '2024-06-25', 'image/1.png', 'Smart Diocese App', NULL, 1),
(10, 'Tanon State', 'MIT', 'Church System', '2025-01-30', '2025-01-16', 'image/Teacher-male512_44209.png', 'pataka', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tblannouncements`
--

CREATE TABLE `tblannouncements` (
  `ANNOUNCEMENT_ID` int(11) NOT NULL,
  `TITLE` varchar(200) NOT NULL,
  `CONTENT` text NOT NULL,
  `PICTURE` varchar(255) DEFAULT NULL COMMENT 'relative path, e.g. images/announcement_20260101_1234.jpg',
  `VIDEO` varchar(255) DEFAULT NULL COMMENT 'relative path, e.g. videos/announcement_20260101_1234.mp4',
  `DATE_POSTED` date NOT NULL,
  `STATUS` varchar(20) NOT NULL DEFAULT 'Published' COMMENT 'Published or Draft',
  `DATEADDED` datetime DEFAULT NULL,
  `ADDEDBY` int(11) DEFAULT NULL,
  `DATEMODIFIED` datetime DEFAULT NULL,
  `MODIFIEDBY` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tblannouncements`
--

INSERT INTO `tblannouncements` (`ANNOUNCEMENT_ID`, `TITLE`, `CONTENT`, `PICTURE`, `VIDEO`, `DATE_POSTED`, `STATUS`, `DATEADDED`, `ADDEDBY`, `DATEMODIFIED`, `MODIFIEDBY`) VALUES
(4, 'TAÑON COLLEGE INC. 74TH FOUNDING ANNIVERSARY-CELEBRATING 74 YEARS OF EXCELLENCE, RESILIENCE,AND SERVICE.', 'September 1, 2026—Tañon College Inc.(TCI)\r\n‎held it 74th Founding Anniversary and launched Intramurals 2026 with a lively band parade on the school grounds.\r\n‎The parade started with the colors bearing the flags, followed by the first set of the band, then The  PTA  (Parents Teachers Association), together with the faculty and  staff.\r\n‎\r\n‎Leading the parade were the colors, followed by Tañon College Drum and Lyre Corps (TCDLC) set A and next were the Grade 7 Green Stallions first in line to lead all the grade levels. Coming after in order are Grade 8 Golden Tigers, Shepherd Learning Center, continued with the Grade 9 Red Phoenix, Grade 10 Blue Dragons, Grade 11 Purple Griffins, at the end of the line Grade 12 Pink Fox. \r\n‎\r\n‎After the parade ended, students and faculty staff gathered at the Tañon College School Quadrangle to witness the band exhibition of  Tañon College Drum and Lyre Corps (TCDLC) Drum Beaters then showcased their opening salvo. Next, the School Principal Mrs. Jane S. De Guzman, delivered her opening remarks for this school year\'s Founder\'s Day celebration and Intramurals. Then called on Dra. Elizabeth E. Carbajosa, School OIC (Officer in-charge) Administrator, gave her welcome address for this year\'s Founder\'s Day Celebration.Every grade level\'s emblems were then opened, symbolizing the strong animals of their respective grade levels. \r\n‎\r\n‎The event continues on, loud cheers, and supports as  The 74th Founding Anniversary, Intramurals 2026 officially began.\r\n‎', 'images/announcement_20260904155735_2923.jpg', NULL, '2026-09-04', 'Published', '2026-09-04 15:57:35', 82, NULL, NULL),
(5, '𝐀 𝐌𝐨𝐫𝐧𝐢𝐧𝐠 𝐨𝐟 𝐅𝐮𝐧, 𝐅𝐫𝐢𝐞𝐧𝐝𝐬𝐡𝐢𝐩 & 𝐀𝐥𝐮𝐦𝐧𝐢 𝐂𝐚𝐦𝐚𝐫𝐚𝐝𝐞𝐫𝐢𝐞!', 'This morning was filled with smiles, laughter, meaningful reunions, and wonderful memories as we gathered with alumni from different batches! \r\n\r\nA big thank you to our Host Batch 2001, in cooperation with the Tañon College Alumni Association, for planning and making this special activity possible. \r\n\r\nOur heartfelt appreciation also goes to Batch 2001 for generously sponsoring the FREE snacks for all participating alumni batches. \r\n\r\nAnd, of course, the fun continued with an exciting Bingo Game! 🎱 The cheers, laughter, and friendly competition made the morning even more memorable. \r\n\r\nThank you to all the alumni batches who came, participated, and shared in this wonderful morning. Together, we continue to celebrate the friendship and memories that connect us through the years. \r\n\r\nCheers to Batch 2001 and to our ever-growing Tañon College Alumni family!', 'images/announcement_20260904160536_6123.jpg', NULL, '2026-09-04', 'Published', '2026-09-04 16:05:36', 82, '2026-09-05 20:32:01', 82);

-- --------------------------------------------------------

--
-- Table structure for table `tblclassrooms`
--

CREATE TABLE `tblclassrooms` (
  `CLASSROOM_ID` int(11) NOT NULL,
  `CLASSROOM_NAME` varchar(100) NOT NULL,
  `DESCRIPTION` text DEFAULT NULL,
  `STATUS` enum('Active','Inactive') NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tblclassrooms`
--

INSERT INTO `tblclassrooms` (`CLASSROOM_ID`, `CLASSROOM_NAME`, `DESCRIPTION`, `STATUS`) VALUES
(1, 'Room 101', 'Ground floor lecture room', 'Active'),
(2, 'Room 102', 'Ground floor lecture room', 'Active'),
(3, 'Room 201', '2nd floor lecture room', 'Active'),
(4, 'Computer Laboratory 1', '2nd floor - 40 computer units', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `tblcourses`
--

CREATE TABLE `tblcourses` (
  `COURSE_ID` int(11) NOT NULL,
  `COURSE_CODE` varchar(20) NOT NULL,
  `COURSE_NAME` varchar(150) NOT NULL,
  `COURSE_DESC` text DEFAULT NULL,
  `STATUS` varchar(20) NOT NULL DEFAULT 'Active',
  `PROGRAM_HEAD_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tblcourses`
--

INSERT INTO `tblcourses` (`COURSE_ID`, `COURSE_CODE`, `COURSE_NAME`, `COURSE_DESC`, `STATUS`, `PROGRAM_HEAD_ID`) VALUES
(1, 'BSIT', 'Bachelor of Science in Information Technology', 'Information Technology program', 'Active', 84),
(2, 'BSTM', 'Bachelor of Science in Tourism Management', 'Tourism Management program', 'Active', 85),
(3, 'BSED', 'Bachelor of Secondary Education', 'Secondary Education program', 'Active', 86);

-- --------------------------------------------------------

--
-- Table structure for table `tblenrollment`
--

CREATE TABLE `tblenrollment` (
  `ENROLLMENT_ID` int(11) NOT NULL,
  `S_ID` int(11) NOT NULL,
  `COURSE_ID` int(11) NOT NULL,
  `SECTION_ID` int(11) DEFAULT NULL,
  `SY_ID` int(11) NOT NULL,
  `YEAR_LEVEL` varchar(20) NOT NULL,
  `SEMESTER` varchar(20) NOT NULL,
  `CATEGORY` varchar(30) NOT NULL DEFAULT 'New',
  `CURRICULUM_YR` varchar(20) DEFAULT NULL,
  `DATE_RESERVED` date DEFAULT NULL,
  `DATE_ENROLLED` date DEFAULT NULL,
  `STATUS` varchar(30) NOT NULL DEFAULT 'Reserved',
  `ENCODED_BY` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `tblenrollment`
--
DELIMITER $$
CREATE TRIGGER `trg_enrollment_status_sync` AFTER UPDATE ON `tblenrollment` FOR EACH ROW BEGIN
	IF NEW.STATUS <> OLD.STATUS THEN
		UPDATE `tblenrollmentdetails`
		SET STATUS = NEW.STATUS
		WHERE ENROLLMENT_ID = NEW.ENROLLMENT_ID;
	END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tblenrollmentdetails`
--

CREATE TABLE `tblenrollmentdetails` (
  `DETAIL_ID` int(11) NOT NULL,
  `ENROLLMENT_ID` int(11) NOT NULL,
  `SUBJECT_ID` int(11) NOT NULL,
  `IDNO` varchar(20) DEFAULT NULL,
  `STUDENT_NAME` varchar(150) DEFAULT NULL,
  `COURSE_CODE` varchar(20) DEFAULT NULL,
  `YEAR_LEVEL` varchar(20) DEFAULT NULL,
  `SEMESTER` varchar(20) DEFAULT NULL,
  `SCHOOL_YEAR` varchar(20) DEFAULT NULL,
  `STATUS` varchar(30) DEFAULT NULL,
  `SUBJECT_CODE` varchar(20) DEFAULT NULL,
  `SUBJECT_NAME` varchar(150) DEFAULT NULL,
  `UNITS` int(11) DEFAULT NULL,
  `SUBJECT_TYPE` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `tblenrollmentdetails`
--
DELIMITER $$
CREATE TRIGGER `trg_enrollmentdetails_fill` BEFORE INSERT ON `tblenrollmentdetails` FOR EACH ROW BEGIN
	DECLARE v_idno VARCHAR(20);
	DECLARE v_name VARCHAR(150);
	DECLARE v_course VARCHAR(20);
	DECLARE v_yl VARCHAR(20);
	DECLARE v_sem VARCHAR(20);
	DECLARE v_sy VARCHAR(20);
	DECLARE v_status VARCHAR(30);
	DECLARE v_scode VARCHAR(20);
	DECLARE v_sname VARCHAR(150);
	DECLARE v_units INT;
	DECLARE v_stype VARCHAR(20);

	SELECT s.IDNO, CONCAT(s.LNAME, ', ', s.FNAME, ' ', s.MNAME), c.COURSE_CODE,
		e.YEAR_LEVEL, e.SEMESTER, sy.SCHOOL_YEAR, e.STATUS
	INTO v_idno, v_name, v_course, v_yl, v_sem, v_sy, v_status
	FROM `tblenrollment` e
	JOIN `tblstudent`    s  ON s.S_ID      = e.S_ID
	JOIN `tblcourses`    c  ON c.COURSE_ID = e.COURSE_ID
	JOIN `tblschoolyear` sy ON sy.SY_ID    = e.SY_ID
	WHERE e.ENROLLMENT_ID = NEW.ENROLLMENT_ID
	LIMIT 1;

	SELECT SUBJECT_CODE, SUBJECT_NAME, UNITS, SUBJECT_TYPE
	INTO v_scode, v_sname, v_units, v_stype
	FROM `tblsubjects`
	WHERE SUBJECT_ID = NEW.SUBJECT_ID
	LIMIT 1;

	SET NEW.IDNO         = v_idno;
	SET NEW.STUDENT_NAME = v_name;
	SET NEW.COURSE_CODE  = v_course;
	SET NEW.YEAR_LEVEL   = v_yl;
	SET NEW.SEMESTER     = v_sem;
	SET NEW.SCHOOL_YEAR  = v_sy;
	SET NEW.STATUS       = v_status;
	SET NEW.SUBJECT_CODE = v_scode;
	SET NEW.SUBJECT_NAME = v_sname;
	SET NEW.UNITS        = v_units;
	SET NEW.SUBJECT_TYPE = v_stype;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tblgrades`
--

CREATE TABLE `tblgrades` (
  `GRADE_ID` int(11) NOT NULL,
  `S_ID` int(11) NOT NULL,
  `SUBJECT_ID` int(11) NOT NULL,
  `ENROLLMENT_ID` int(11) NOT NULL,
  `SY_ID` int(11) NOT NULL,
  `SEMESTER` varchar(20) DEFAULT NULL,
  `GRADE` decimal(5,2) DEFAULT NULL,
  `REMARKS` varchar(20) DEFAULT NULL,
  `DATE_ENCODED` date NOT NULL DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblhistoryenrollment`
--

CREATE TABLE `tblhistoryenrollment` (
  `HISTORY_ID` int(11) NOT NULL,
  `ENROLLMENT_ID` int(11) NOT NULL,
  `S_ID` int(11) NOT NULL,
  `COURSE_ID` int(11) NOT NULL,
  `SY_ID` int(11) NOT NULL,
  `YEAR_LEVEL` varchar(20) DEFAULT NULL,
  `SEMESTER` varchar(20) DEFAULT NULL,
  `CATEGORY` varchar(30) DEFAULT NULL,
  `STATUS` varchar(30) NOT NULL,
  `DATE_RESERVED` date DEFAULT NULL,
  `DATE_ENROLLED` date DEFAULT NULL,
  `DATE_ARCHIVED` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblpayments`
--

CREATE TABLE `tblpayments` (
  `PAYMENT_ID` int(11) NOT NULL,
  `ENROLLMENT_ID` int(11) NOT NULL,
  `S_ID` int(11) NOT NULL,
  `STUDENT_NAME` varchar(150) NOT NULL,
  `OR_NO` varchar(30) DEFAULT NULL,
  `PAYMENT_TYPE` varchar(20) NOT NULL DEFAULT 'Enrollment Fee',
  `AMOUNT` decimal(10,2) NOT NULL DEFAULT 0.00,
  `CASH_RECEIVED` decimal(10,2) DEFAULT NULL,
  `CHANGE_DUE` decimal(10,2) DEFAULT NULL,
  `DATE_PAID` date NOT NULL DEFAULT curdate(),
  `RECEIVED_BY` int(11) DEFAULT NULL,
  `REMARKS` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblschedule`
--

CREATE TABLE `tblschedule` (
  `SCHEDULE_ID` int(11) NOT NULL,
  `SUBJECT_ID` int(11) NOT NULL,
  `CLASSROOM_ID` int(11) NOT NULL,
  `SCHEDULEDAY_ID` int(11) NOT NULL,
  `SCHEDULETIME_ID` int(11) NOT NULL,
  `INSTRUCTOR_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tblschedule`
--

INSERT INTO `tblschedule` (`SCHEDULE_ID`, `SUBJECT_ID`, `CLASSROOM_ID`, `SCHEDULEDAY_ID`, `SCHEDULETIME_ID`, `INSTRUCTOR_ID`) VALUES
(1, 1, 4, 1, 1, 87),
(2, 2, 4, 1, 2, 87),
(3, 3, 1, 2, 1, 88),
(4, 11, 1, 2, 2, 88),
(5, 12, 2, 3, 1, 89),
(6, 13, 2, 3, 2, 89),
(7, 5, 2, 1, 3, 89),
(8, 6, 2, 2, 3, 89),
(9, 8, 3, 1, 4, 88),
(10, 9, 3, 2, 4, 88);

-- --------------------------------------------------------

--
-- Table structure for table `tblscheduledays`
--

CREATE TABLE `tblscheduledays` (
  `SCHEDULEDAY_ID` int(11) NOT NULL,
  `DAY_NAME` varchar(50) NOT NULL,
  `DESCRIPTION` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tblscheduledays`
--

INSERT INTO `tblscheduledays` (`SCHEDULEDAY_ID`, `DAY_NAME`, `DESCRIPTION`) VALUES
(1, 'MWF', 'Monday, Wednesday, Friday'),
(2, 'TTH', 'Tuesday, Thursday'),
(3, 'Saturday', 'Saturday only'),
(4, 'Daily', 'Monday to Friday');

-- --------------------------------------------------------

--
-- Table structure for table `tblscheduletimes`
--

CREATE TABLE `tblscheduletimes` (
  `SCHEDULETIME_ID` int(11) NOT NULL,
  `TIME_START` time NOT NULL,
  `TIME_END` time NOT NULL,
  `DESCRIPTION` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tblscheduletimes`
--

INSERT INTO `tblscheduletimes` (`SCHEDULETIME_ID`, `TIME_START`, `TIME_END`, `DESCRIPTION`) VALUES
(1, '07:30:00', '09:00:00', NULL),
(2, '09:00:00', '10:30:00', NULL),
(3, '10:30:00', '12:00:00', NULL),
(4, '13:00:00', '14:30:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblschoolyear`
--

CREATE TABLE `tblschoolyear` (
  `SY_ID` int(11) NOT NULL,
  `SCHOOL_YEAR` varchar(20) NOT NULL,
  `STATUS` varchar(20) NOT NULL DEFAULT 'Inactive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tblschoolyear`
--

INSERT INTO `tblschoolyear` (`SY_ID`, `SCHOOL_YEAR`, `STATUS`) VALUES
(1, '2025-2026', 'Active'),
(4, '2024-2025', 'Inactive');

-- --------------------------------------------------------

--
-- Table structure for table `tblsections`
--

CREATE TABLE `tblsections` (
  `SECTION_ID` int(11) NOT NULL,
  `SECTION_NAME` varchar(50) NOT NULL,
  `COURSE_ID` int(11) NOT NULL,
  `SY_ID` int(11) NOT NULL,
  `YEAR_LEVEL` varchar(20) NOT NULL,
  `PROGRAM_HEAD` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tblsections`
--

INSERT INTO `tblsections` (`SECTION_ID`, `SECTION_NAME`, `COURSE_ID`, `SY_ID`, `YEAR_LEVEL`, `PROGRAM_HEAD`) VALUES
(1, 'A', 1, 1, '1st Year', NULL),
(2, 'B', 1, 1, '1st Year', NULL),
(3, 'C', 1, 1, '1st Year', NULL),
(4, 'D', 1, 1, '1st Year', NULL),
(5, 'A', 2, 1, '1st Year', NULL),
(6, 'B', 2, 1, '1st Year', NULL),
(7, 'C', 2, 1, '1st Year', NULL),
(8, 'D', 2, 1, '1st Year', NULL),
(9, 'A', 3, 1, '1st Year', NULL),
(10, 'B', 3, 1, '1st Year', NULL),
(11, 'C', 3, 1, '1st Year', NULL),
(12, 'D', 3, 1, '1st Year', NULL),
(13, 'A', 1, 1, '2nd Year', NULL),
(14, 'B', 1, 1, '2nd Year', NULL),
(15, 'A', 1, 1, '3rd Year', NULL),
(16, 'B', 1, 1, '3rd Year', NULL),
(17, 'A', 1, 1, '4th Year', NULL),
(18, 'B', 1, 1, '4th Year', NULL),
(19, 'A', 2, 1, '2nd Year', NULL),
(20, 'B', 2, 1, '2nd Year', NULL),
(21, 'A', 2, 1, '3rd Year', NULL),
(22, 'B', 2, 1, '3rd Year', NULL),
(23, 'A', 2, 1, '4th Year', NULL),
(24, 'B', 2, 1, '4th Year', NULL),
(25, 'A', 3, 1, '2nd Year', NULL),
(26, 'B', 3, 1, '2nd Year', NULL),
(27, 'A', 3, 1, '3rd Year', NULL),
(28, 'B', 3, 1, '3rd Year', NULL),
(29, 'A', 3, 1, '4th Year', NULL),
(30, 'B', 3, 1, '4th Year', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblsectionschedule`
--

CREATE TABLE `tblsectionschedule` (
  `SECTIONSCHEDULE_ID` int(11) NOT NULL,
  `SECTION_ID` int(11) NOT NULL,
  `SCHEDULE_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tblsectionschedule`
--

INSERT INTO `tblsectionschedule` (`SECTIONSCHEDULE_ID`, `SECTION_ID`, `SCHEDULE_ID`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 1, 4),
(5, 1, 5),
(6, 1, 6),
(7, 5, 7),
(8, 5, 8),
(9, 9, 9),
(10, 9, 10);

-- --------------------------------------------------------

--
-- Table structure for table `tblstudent`
--

CREATE TABLE `tblstudent` (
  `S_ID` int(11) NOT NULL,
  `IDNO` varchar(20) NOT NULL,
  `FNAME` varchar(40) NOT NULL,
  `LNAME` varchar(40) NOT NULL,
  `MNAME` varchar(40) NOT NULL,
  `SEX` varchar(10) NOT NULL DEFAULT 'Male',
  `BDAY` date DEFAULT NULL,
  `BPLACE` text DEFAULT NULL,
  `STATUS` varchar(30) NOT NULL DEFAULT 'Active',
  `AGE` int(11) DEFAULT NULL,
  `NATIONALITY` varchar(40) DEFAULT NULL,
  `RELIGION` varchar(255) DEFAULT NULL,
  `CONTACT_NO` varchar(40) DEFAULT NULL,
  `HOME_ADD` text DEFAULT NULL,
  `EMAIL` varchar(150) DEFAULT NULL,
  `ACC_PASSWORD` text DEFAULT NULL,
  `LRNNO` varchar(15) DEFAULT NULL,
  `CONTACTPERSON` varchar(150) DEFAULT NULL,
  `COMPANYIDNO` varchar(255) DEFAULT NULL,
  `COURSE_ID` int(11) DEFAULT NULL,
  `AddedBy` int(11) DEFAULT NULL,
  `ACCOUNT_UID` int(11) DEFAULT NULL,
  `CIVIL_STATUS` varchar(20) DEFAULT 'Single',
  `FATHER_NAME` varchar(150) DEFAULT NULL,
  `FATHER_CONTACT` varchar(40) DEFAULT NULL,
  `FATHER_EMAIL` varchar(150) DEFAULT NULL,
  `FATHER_OCCUPATION` varchar(100) DEFAULT NULL,
  `FATHER_DECEASED` varchar(5) DEFAULT 'No',
  `MOTHER_NAME` varchar(150) DEFAULT NULL,
  `MOTHER_CONTACT` varchar(40) DEFAULT NULL,
  `MOTHER_EMAIL` varchar(150) DEFAULT NULL,
  `MOTHER_OCCUPATION` varchar(100) DEFAULT NULL,
  `MOTHER_DECEASED` varchar(5) DEFAULT 'No',
  `GUARDIAN_NAME` varchar(150) DEFAULT NULL,
  `GUARDIAN_RELATIONSHIP` varchar(50) DEFAULT NULL,
  `GUARDIAN_CONTACT` varchar(40) DEFAULT NULL,
  `GUARDIAN_EMAIL` varchar(150) DEFAULT NULL,
  `GUARDIAN_ADDRESS` text DEFAULT NULL,
  `OTHER_PERSON_SUPPORTING` varchar(150) DEFAULT NULL,
  `IS_BOARDING` varchar(5) DEFAULT 'No',
  `WITH_FAMILY` varchar(5) DEFAULT 'Yes',
  `BOARDING_ADDRESS` text DEFAULT NULL,
  `ELEM_SCHOOL` varchar(150) DEFAULT NULL,
  `ELEM_ADDRESS` varchar(255) DEFAULT NULL,
  `ELEM_YEAR` varchar(20) DEFAULT NULL,
  `SEC_SCHOOL` varchar(150) DEFAULT NULL,
  `SEC_ADDRESS` varchar(255) DEFAULT NULL,
  `SEC_YEAR` varchar(20) DEFAULT NULL,
  `COLLEGE_SCHOOL` varchar(150) DEFAULT NULL,
  `COLLEGE_ADDRESS` varchar(255) DEFAULT NULL,
  `COLLEGE_YEAR` varchar(20) DEFAULT NULL,
  `VOC_SCHOOL` varchar(150) DEFAULT NULL,
  `VOC_ADDRESS` varchar(255) DEFAULT NULL,
  `VOC_YEAR` varchar(20) DEFAULT NULL,
  `OTHERS_SCHOOL` varchar(150) DEFAULT NULL,
  `REQ_FORM138` tinyint(1) NOT NULL DEFAULT 0,
  `REQ_GOODMORAL` tinyint(1) NOT NULL DEFAULT 0,
  `REQ_BIRTHCERT` tinyint(1) NOT NULL DEFAULT 0,
  `REQ_BAPTISMAL` tinyint(1) NOT NULL DEFAULT 0,
  `REQ_ASSESSMENT` tinyint(1) NOT NULL DEFAULT 0,
  `REQ_TRANSFERCRED` tinyint(1) NOT NULL DEFAULT 0,
  `REQ_MARRIAGECONTRACT` tinyint(1) NOT NULL DEFAULT 0,
  `REQ_2X2PICTURE` tinyint(1) NOT NULL DEFAULT 0,
  `REQ_OTHERS1` varchar(150) DEFAULT NULL,
  `REQ_OTHERS2` varchar(150) DEFAULT NULL,
  `REQ_OTHERS3` varchar(150) DEFAULT NULL,
  `REQ_NOTES` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `tblstudent`
--
DELIMITER $$
CREATE TRIGGER `trg_student_sync_enrollmentdetails` AFTER UPDATE ON `tblstudent` FOR EACH ROW BEGIN
	IF (NEW.IDNO <> OLD.IDNO OR NEW.FNAME <> OLD.FNAME OR NEW.LNAME <> OLD.LNAME OR NEW.MNAME <> OLD.MNAME) THEN

		UPDATE `tblenrollmentdetails` d
		JOIN `tblenrollment` e ON e.ENROLLMENT_ID = d.ENROLLMENT_ID
		SET d.IDNO = NEW.IDNO,
			d.STUDENT_NAME = CONCAT(NEW.LNAME, ', ', NEW.FNAME, ' ', NEW.MNAME)
		WHERE e.S_ID = NEW.S_ID;

		UPDATE `tblpayments`
		SET STUDENT_NAME = CONCAT(NEW.LNAME, ', ', NEW.FNAME, ' ', NEW.MNAME)
		WHERE S_ID = NEW.S_ID;

	END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tblsubjects`
--

CREATE TABLE `tblsubjects` (
  `SUBJECT_ID` int(11) NOT NULL,
  `SUBJECT_CODE` varchar(20) NOT NULL,
  `SUBJECT_NAME` varchar(150) NOT NULL,
  `UNITS` int(11) NOT NULL DEFAULT 3,
  `COURSE_ID` int(11) DEFAULT NULL,
  `YEAR_LEVEL` varchar(20) DEFAULT NULL,
  `SEMESTER` varchar(20) DEFAULT NULL,
  `SUBJECT_TYPE` varchar(10) NOT NULL DEFAULT 'Major'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tblsubjects`
--

INSERT INTO `tblsubjects` (`SUBJECT_ID`, `SUBJECT_CODE`, `SUBJECT_NAME`, `UNITS`, `COURSE_ID`, `YEAR_LEVEL`, `SEMESTER`, `SUBJECT_TYPE`) VALUES
(1, 'IT101', 'Introduction to Computing', 3, 1, '1st Year', '1st Semester', 'Major'),
(2, 'IT102', 'Computer Programming 1', 3, 1, '1st Year', '1st Semester', 'Major'),
(3, 'IT103', 'Discrete Mathematics', 3, 1, '1st Year', '1st Semester', 'Major'),
(4, 'IT104', 'Web Systems and Technologies', 3, 1, '1st Year', '2nd Semester', 'Major'),
(5, 'TM101', 'Introduction to Tourism', 3, 2, '1st Year', '1st Semester', 'Major'),
(6, 'TM102', 'Philippine Culture and Tourism Geography', 3, 2, '1st Year', '1st Semester', 'Major'),
(7, 'TM103', 'Micro Perspective of Tourism', 3, 2, '1st Year', '2nd Semester', 'Major'),
(8, 'ED101', 'The Teaching Profession', 3, 3, '1st Year', '1st Semester', 'Major'),
(9, 'ED102', 'Child and Adolescent Development', 3, 3, '1st Year', '1st Semester', 'Major'),
(10, 'ED103', 'Facilitating Learner-Centered Teaching', 3, 3, '1st Year', '2nd Semester', 'Major'),
(11, 'GE01', 'Purposive Communication', 3, NULL, '1st Year', '1st Semester', 'Minor'),
(12, 'PE101', 'Physical Education 1', 2, NULL, '1st Year', '1st Semester', 'Minor'),
(13, 'NSTP1', 'National Service Training Program 1', 3, NULL, '1st Year', '1st Semester', 'Minor'),
(14, 'IT105', 'Computer Programming 2', 3, 1, '1st Year', '2nd Semester', 'Major'),
(15, 'IT201', 'Object-Oriented Programming', 3, 1, '2nd Year', '1st Semester', 'Major'),
(16, 'IT202', 'Data Structures and Algorithms', 3, 1, '2nd Year', '1st Semester', 'Major'),
(17, 'IT203', 'Information Management', 3, 1, '2nd Year', '2nd Semester', 'Major'),
(18, 'IT204', 'Networking 1', 3, 1, '2nd Year', '2nd Semester', 'Major'),
(19, 'IT301', 'Systems Analysis and Design', 3, 1, '3rd Year', '1st Semester', 'Major'),
(20, 'IT302', 'Systems Integration and Architecture', 3, 1, '3rd Year', '1st Semester', 'Major'),
(21, 'IT303', 'Web Systems Development 2', 3, 1, '3rd Year', '2nd Semester', 'Major'),
(22, 'IT304', 'Mobile Application Development', 3, 1, '3rd Year', '2nd Semester', 'Major'),
(23, 'IT401', 'Capstone Project 1', 3, 1, '4th Year', '1st Semester', 'Major'),
(24, 'IT402', 'Information Assurance and Security', 3, 1, '4th Year', '1st Semester', 'Major'),
(25, 'IT403', 'Capstone Project 2', 3, 1, '4th Year', '2nd Semester', 'Major'),
(26, 'IT404', 'Practicum/OJT', 6, 1, '4th Year', '2nd Semester', 'Major'),
(27, 'TM104', 'Tourism Marketing', 3, 2, '1st Year', '2nd Semester', 'Major'),
(28, 'TM201', 'Tourism Planning and Development', 3, 2, '2nd Year', '1st Semester', 'Major'),
(29, 'TM202', 'Front Office Operations', 3, 2, '2nd Year', '1st Semester', 'Major'),
(30, 'TM203', 'Housekeeping Operations', 3, 2, '2nd Year', '2nd Semester', 'Major'),
(31, 'TM204', 'Food and Beverage Services', 3, 2, '2nd Year', '2nd Semester', 'Major'),
(32, 'TM301', 'Tour Guiding and Escorting', 3, 2, '3rd Year', '1st Semester', 'Major'),
(33, 'TM302', 'Events Management', 3, 2, '3rd Year', '1st Semester', 'Major'),
(34, 'TM303', 'International Tourism', 3, 2, '3rd Year', '2nd Semester', 'Major'),
(35, 'TM304', 'Sustainable Tourism', 3, 2, '3rd Year', '2nd Semester', 'Major'),
(36, 'TM401', 'Tourism Research', 3, 2, '4th Year', '1st Semester', 'Major'),
(37, 'TM402', 'Practicum/OJT 1', 6, 2, '4th Year', '1st Semester', 'Major'),
(38, 'TM403', 'Tourism Capstone', 3, 2, '4th Year', '2nd Semester', 'Major'),
(39, 'TM404', 'Practicum/OJT 2', 6, 2, '4th Year', '2nd Semester', 'Major'),
(40, 'ED104', 'Foundations of Special and Inclusive Education', 3, 3, '1st Year', '2nd Semester', 'Major'),
(41, 'ED201', 'Assessment in Learning 1', 3, 3, '2nd Year', '1st Semester', 'Major'),
(42, 'ED202', 'Technology for Teaching and Learning 1', 3, 3, '2nd Year', '1st Semester', 'Major'),
(43, 'ED203', 'Assessment in Learning 2', 3, 3, '2nd Year', '2nd Semester', 'Major'),
(44, 'ED204', 'Technology for Teaching and Learning 2', 3, 3, '2nd Year', '2nd Semester', 'Major'),
(45, 'ED301', 'Field Study 1', 3, 3, '3rd Year', '1st Semester', 'Major'),
(46, 'ED302', 'Curriculum Development', 3, 3, '3rd Year', '1st Semester', 'Major'),
(47, 'ED303', 'Field Study 2', 3, 3, '3rd Year', '2nd Semester', 'Major'),
(48, 'ED304', 'Teaching Internship Preparation', 3, 3, '3rd Year', '2nd Semester', 'Major'),
(49, 'ED401', 'Practice Teaching 1', 6, 3, '4th Year', '1st Semester', 'Major'),
(50, 'ED402', 'Action Research', 3, 3, '4th Year', '1st Semester', 'Major'),
(51, 'ED403', 'Practice Teaching 2', 6, 3, '4th Year', '2nd Semester', 'Major'),
(52, 'ED404', 'Seminar in Education', 3, 3, '4th Year', '2nd Semester', 'Major'),
(53, 'GE02', 'Mathematics in the Modern World', 3, NULL, '1st Year', '2nd Semester', 'Minor'),
(54, 'PE102', 'Physical Education 2', 2, NULL, '1st Year', '2nd Semester', 'Minor'),
(55, 'NSTP2', 'National Service Training Program 2', 3, NULL, '1st Year', '2nd Semester', 'Minor'),
(56, 'GE03', 'Understanding the Self', 3, NULL, '2nd Year', '1st Semester', 'Minor'),
(57, 'PE201', 'Physical Education 3', 2, NULL, '2nd Year', '1st Semester', 'Minor'),
(58, 'GE04', 'Art Appreciation', 3, NULL, '2nd Year', '2nd Semester', 'Minor'),
(59, 'PE202', 'Physical Education 4', 2, NULL, '2nd Year', '2nd Semester', 'Minor'),
(60, 'GE05', 'Ethics', 3, NULL, '3rd Year', '1st Semester', 'Minor'),
(61, 'GE06', 'Rizal\'s Life and Works', 3, NULL, '3rd Year', '2nd Semester', 'Minor'),
(62, 'GE07', 'Environmental Science', 3, NULL, '4th Year', '1st Semester', 'Minor'),
(63, 'GE08', 'Gender and Society', 3, NULL, '4th Year', '2nd Semester', 'Minor');

-- --------------------------------------------------------

--
-- Table structure for table `tblusers`
--

CREATE TABLE `tblusers` (
  `UID` int(11) NOT NULL,
  `DISPLAYNAME` varchar(30) NOT NULL,
  `USERNAME` varchar(50) NOT NULL,
  `PASSWORD` text NOT NULL,
  `TYPE` varchar(15) NOT NULL,
  `TYPEID` int(11) DEFAULT NULL,
  `PICTURE` varchar(255) DEFAULT NULL,
  `ADDEDBY` int(3) NOT NULL,
  `DATEADDED` date NOT NULL,
  `MODIFIEDBY` int(3) NOT NULL,
  `DATEMODIFIED` date NOT NULL,
  `STATUSACTIVE` int(2) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblusers`
--

INSERT INTO `tblusers` (`UID`, `DISPLAYNAME`, `USERNAME`, `PASSWORD`, `TYPE`, `TYPEID`, `PICTURE`, `ADDEDBY`, `DATEADDED`, `MODIFIEDBY`, `DATEMODIFIED`, `STATUSACTIVE`) VALUES
(1, 'Jason', 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', 'Administrator', 1, NULL, 1, '2020-08-27', 1, '2021-07-05', 1),
(77, 'dfd', 'dfd', '6bb65257fcab4e2975cd96b0f7fc4b53d97c10b6', 'Staff', NULL, NULL, 1, '2025-01-16', 1, '2026-08-02', 1),
(78, 'dfd', 'dfdf', '6bb65257fcab4e2975cd96b0f7fc4b53d97c10b6', 'Staff', NULL, NULL, 1, '2025-01-16', 1, '2025-01-16', 1),
(82, 'Eugene', 'eugene', '8cb2237d0679ca88db6464eac60da96345513964', 'Administrator', NULL, 'images/user_20260827094602_7226.jpg', 1, '2026-08-24', 82, '2026-08-27', 1),
(84, 'Juan Dela Cruz', 'Juan', '8cb2237d0679ca88db6464eac60da96345513964', 'Program Head', NULL, NULL, 82, '2026-09-06', 82, '2026-09-06', 1),
(85, 'Maria Santos', 'Maria', '8cb2237d0679ca88db6464eac60da96345513964', 'Program Head', NULL, NULL, 82, '2026-09-06', 82, '2026-09-06', 1),
(86, 'John Reyes', 'John', '8cb2237d0679ca88db6464eac60da96345513964', 'Program Head', NULL, NULL, 82, '2026-09-06', 82, '2026-09-06', 1),
(87, 'Pedro Garcia', 'pedro', '8cb2237d0679ca88db6464eac60da96345513964', 'Instructor', 17, NULL, 1, '2026-09-20', 1, '2026-09-20', 1),
(88, 'Ana Lopez', 'ana', '8cb2237d0679ca88db6464eac60da96345513964', 'Instructor', 17, NULL, 1, '2026-09-20', 1, '2026-09-20', 1),
(89, 'Mark Cruz', 'mark', '8cb2237d0679ca88db6464eac60da96345513964', 'Instructor', 17, NULL, 1, '2026-09-20', 1, '2026-09-20', 1),
(90, 'Sante', 'sante', '8cb2237d0679ca88db6464eac60da96345513964', 'Administrator', NULL, NULL, 82, '2026-09-20', 82, '2026-09-20', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tblusertype`
--

CREATE TABLE `tblusertype` (
  `TYPEID` int(11) NOT NULL,
  `USERTYPE` varchar(30) NOT NULL,
  `STATUS` varchar(20) NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblusertype`
--

INSERT INTO `tblusertype` (`TYPEID`, `USERTYPE`, `STATUS`) VALUES
(1, 'Administrator', 'Active'),
(2, 'Doctor', 'Active'),
(7, 'Nurse', 'Active'),
(13, 'cashier', 'Active'),
(15, 'Student', 'Active'),
(16, 'Program Head', 'Active'),
(17, 'Instructor', 'Active');

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_enrollmentdetails`
-- (See below for the actual view)
--
CREATE TABLE `vw_enrollmentdetails` (
`DETAIL_ID` int(11)
,`ENROLLMENT_ID` int(11)
,`IDNO` varchar(20)
,`STUDENT_NAME` varchar(123)
,`COURSE_CODE` varchar(20)
,`COURSE_NAME` varchar(150)
,`YEAR_LEVEL` varchar(20)
,`SEMESTER` varchar(20)
,`SCHOOL_YEAR` varchar(20)
,`STATUS` varchar(30)
,`SUBJECT_CODE` varchar(20)
,`SUBJECT_NAME` varchar(150)
,`UNITS` int(11)
,`SUBJECT_TYPE` varchar(10)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_enrollmentdetails_summary`
-- (See below for the actual view)
--
CREATE TABLE `vw_enrollmentdetails_summary` (
`ENROLLMENT_ID` int(11)
,`IDNO` varchar(20)
,`STUDENT_NAME` varchar(123)
,`COURSE_CODE` varchar(20)
,`YEAR_LEVEL` varchar(20)
,`SEMESTER` varchar(20)
,`SCHOOL_YEAR` varchar(20)
,`STATUS` varchar(30)
,`SUBJECTS` mediumtext
,`TOTAL_UNITS` decimal(32,0)
,`SUBJECT_COUNT` bigint(21)
);

-- --------------------------------------------------------

--
-- Structure for view `vw_enrollmentdetails`
--
DROP TABLE IF EXISTS `vw_enrollmentdetails`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_enrollmentdetails`  AS SELECT `d`.`DETAIL_ID` AS `DETAIL_ID`, `e`.`ENROLLMENT_ID` AS `ENROLLMENT_ID`, `s`.`IDNO` AS `IDNO`, concat(`s`.`LNAME`,', ',`s`.`FNAME`,' ',`s`.`MNAME`) AS `STUDENT_NAME`, `c`.`COURSE_CODE` AS `COURSE_CODE`, `c`.`COURSE_NAME` AS `COURSE_NAME`, `e`.`YEAR_LEVEL` AS `YEAR_LEVEL`, `e`.`SEMESTER` AS `SEMESTER`, `sy`.`SCHOOL_YEAR` AS `SCHOOL_YEAR`, `e`.`STATUS` AS `STATUS`, `sub`.`SUBJECT_CODE` AS `SUBJECT_CODE`, `sub`.`SUBJECT_NAME` AS `SUBJECT_NAME`, `sub`.`UNITS` AS `UNITS`, `sub`.`SUBJECT_TYPE` AS `SUBJECT_TYPE` FROM (((((`tblenrollmentdetails` `d` join `tblenrollment` `e` on(`e`.`ENROLLMENT_ID` = `d`.`ENROLLMENT_ID`)) join `tblstudent` `s` on(`s`.`S_ID` = `e`.`S_ID`)) join `tblcourses` `c` on(`c`.`COURSE_ID` = `e`.`COURSE_ID`)) join `tblschoolyear` `sy` on(`sy`.`SY_ID` = `e`.`SY_ID`)) join `tblsubjects` `sub` on(`sub`.`SUBJECT_ID` = `d`.`SUBJECT_ID`)) ORDER BY `e`.`ENROLLMENT_ID` ASC, `sub`.`SUBJECT_CODE` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `vw_enrollmentdetails_summary`
--
DROP TABLE IF EXISTS `vw_enrollmentdetails_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_enrollmentdetails_summary`  AS SELECT `e`.`ENROLLMENT_ID` AS `ENROLLMENT_ID`, `s`.`IDNO` AS `IDNO`, concat(`s`.`LNAME`,', ',`s`.`FNAME`,' ',`s`.`MNAME`) AS `STUDENT_NAME`, `c`.`COURSE_CODE` AS `COURSE_CODE`, `e`.`YEAR_LEVEL` AS `YEAR_LEVEL`, `e`.`SEMESTER` AS `SEMESTER`, `sy`.`SCHOOL_YEAR` AS `SCHOOL_YEAR`, `e`.`STATUS` AS `STATUS`, group_concat(distinct concat(`sub`.`SUBJECT_CODE`,' - ',`sub`.`SUBJECT_NAME`) order by `sub`.`SUBJECT_CODE` ASC separator '; ') AS `SUBJECTS`, sum(`sub`.`UNITS`) AS `TOTAL_UNITS`, count(`d`.`DETAIL_ID`) AS `SUBJECT_COUNT` FROM (((((`tblenrollment` `e` join `tblstudent` `s` on(`s`.`S_ID` = `e`.`S_ID`)) join `tblcourses` `c` on(`c`.`COURSE_ID` = `e`.`COURSE_ID`)) join `tblschoolyear` `sy` on(`sy`.`SY_ID` = `e`.`SY_ID`)) left join `tblenrollmentdetails` `d` on(`d`.`ENROLLMENT_ID` = `e`.`ENROLLMENT_ID`)) left join `tblsubjects` `sub` on(`sub`.`SUBJECT_ID` = `d`.`SUBJECT_ID`)) GROUP BY `e`.`ENROLLMENT_ID` ORDER BY `e`.`ENROLLMENT_ID` ASC ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alumni_details`
--
ALTER TABLE `alumni_details`
  ADD PRIMARY KEY (`AlumniID`),
  ADD KEY `fk_alumni_student` (`S_ID`),
  ADD KEY `fk_alumni_addedby` (`AddedBy`);

--
-- Indexes for table `tblannouncements`
--
ALTER TABLE `tblannouncements`
  ADD PRIMARY KEY (`ANNOUNCEMENT_ID`),
  ADD KEY `fk_announcement_addedby` (`ADDEDBY`);

--
-- Indexes for table `tblclassrooms`
--
ALTER TABLE `tblclassrooms`
  ADD PRIMARY KEY (`CLASSROOM_ID`);

--
-- Indexes for table `tblcourses`
--
ALTER TABLE `tblcourses`
  ADD PRIMARY KEY (`COURSE_ID`),
  ADD UNIQUE KEY `COURSE_CODE` (`COURSE_CODE`),
  ADD KEY `fk_course_programhead` (`PROGRAM_HEAD_ID`);

--
-- Indexes for table `tblenrollment`
--
ALTER TABLE `tblenrollment`
  ADD PRIMARY KEY (`ENROLLMENT_ID`),
  ADD UNIQUE KEY `uq_enrollment_term` (`S_ID`,`SY_ID`,`SEMESTER`),
  ADD KEY `fk_enrollment_student` (`S_ID`),
  ADD KEY `fk_enrollment_course` (`COURSE_ID`),
  ADD KEY `fk_enrollment_section` (`SECTION_ID`),
  ADD KEY `fk_enrollment_sy` (`SY_ID`),
  ADD KEY `fk_enrollment_encodedby` (`ENCODED_BY`);

--
-- Indexes for table `tblenrollmentdetails`
--
ALTER TABLE `tblenrollmentdetails`
  ADD PRIMARY KEY (`DETAIL_ID`),
  ADD UNIQUE KEY `uq_enrollment_subject` (`ENROLLMENT_ID`,`SUBJECT_ID`),
  ADD KEY `fk_endetails_enrollment` (`ENROLLMENT_ID`),
  ADD KEY `fk_endetails_subject` (`SUBJECT_ID`);

--
-- Indexes for table `tblgrades`
--
ALTER TABLE `tblgrades`
  ADD PRIMARY KEY (`GRADE_ID`),
  ADD KEY `fk_grades_student` (`S_ID`),
  ADD KEY `fk_grades_subject` (`SUBJECT_ID`),
  ADD KEY `fk_grades_enrollment` (`ENROLLMENT_ID`),
  ADD KEY `fk_grades_sy` (`SY_ID`);

--
-- Indexes for table `tblhistoryenrollment`
--
ALTER TABLE `tblhistoryenrollment`
  ADD PRIMARY KEY (`HISTORY_ID`),
  ADD UNIQUE KEY `uq_history_enrollment` (`ENROLLMENT_ID`),
  ADD KEY `fk_history_student` (`S_ID`),
  ADD KEY `fk_history_course` (`COURSE_ID`),
  ADD KEY `fk_history_sy` (`SY_ID`);

--
-- Indexes for table `tblpayments`
--
ALTER TABLE `tblpayments`
  ADD PRIMARY KEY (`PAYMENT_ID`),
  ADD KEY `fk_payments_enrollment` (`ENROLLMENT_ID`),
  ADD KEY `fk_payments_student` (`S_ID`),
  ADD KEY `fk_payments_receivedby` (`RECEIVED_BY`);

--
-- Indexes for table `tblschedule`
--
ALTER TABLE `tblschedule`
  ADD PRIMARY KEY (`SCHEDULE_ID`),
  ADD KEY `idx_schedule_subject` (`SUBJECT_ID`),
  ADD KEY `idx_schedule_classroom` (`CLASSROOM_ID`),
  ADD KEY `idx_schedule_day` (`SCHEDULEDAY_ID`),
  ADD KEY `idx_schedule_time` (`SCHEDULETIME_ID`),
  ADD KEY `idx_schedule_instructor` (`INSTRUCTOR_ID`);

--
-- Indexes for table `tblscheduledays`
--
ALTER TABLE `tblscheduledays`
  ADD PRIMARY KEY (`SCHEDULEDAY_ID`);

--
-- Indexes for table `tblscheduletimes`
--
ALTER TABLE `tblscheduletimes`
  ADD PRIMARY KEY (`SCHEDULETIME_ID`);

--
-- Indexes for table `tblschoolyear`
--
ALTER TABLE `tblschoolyear`
  ADD PRIMARY KEY (`SY_ID`),
  ADD UNIQUE KEY `SCHOOL_YEAR` (`SCHOOL_YEAR`);

--
-- Indexes for table `tblsections`
--
ALTER TABLE `tblsections`
  ADD PRIMARY KEY (`SECTION_ID`),
  ADD KEY `fk_sections_course` (`COURSE_ID`),
  ADD KEY `fk_sections_sy` (`SY_ID`);

--
-- Indexes for table `tblsectionschedule`
--
ALTER TABLE `tblsectionschedule`
  ADD PRIMARY KEY (`SECTIONSCHEDULE_ID`),
  ADD UNIQUE KEY `uq_section_schedule` (`SECTION_ID`,`SCHEDULE_ID`),
  ADD KEY `idx_sectionschedule_schedule` (`SCHEDULE_ID`);

--
-- Indexes for table `tblstudent`
--
ALTER TABLE `tblstudent`
  ADD PRIMARY KEY (`S_ID`),
  ADD UNIQUE KEY `IDNO` (`IDNO`),
  ADD KEY `fk_student_course` (`COURSE_ID`),
  ADD KEY `fk_student_addedby` (`AddedBy`),
  ADD KEY `fk_student_account` (`ACCOUNT_UID`);

--
-- Indexes for table `tblsubjects`
--
ALTER TABLE `tblsubjects`
  ADD PRIMARY KEY (`SUBJECT_ID`),
  ADD UNIQUE KEY `uq_subject_code` (`SUBJECT_CODE`),
  ADD KEY `fk_subjects_course` (`COURSE_ID`);

--
-- Indexes for table `tblusers`
--
ALTER TABLE `tblusers`
  ADD PRIMARY KEY (`UID`),
  ADD UNIQUE KEY `uq_username` (`USERNAME`),
  ADD KEY `fk_users_usertype` (`TYPEID`);

--
-- Indexes for table `tblusertype`
--
ALTER TABLE `tblusertype`
  ADD PRIMARY KEY (`TYPEID`),
  ADD UNIQUE KEY `uq_usertype` (`USERTYPE`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alumni_details`
--
ALTER TABLE `alumni_details`
  MODIFY `AlumniID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tblannouncements`
--
ALTER TABLE `tblannouncements`
  MODIFY `ANNOUNCEMENT_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tblclassrooms`
--
ALTER TABLE `tblclassrooms`
  MODIFY `CLASSROOM_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tblcourses`
--
ALTER TABLE `tblcourses`
  MODIFY `COURSE_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tblenrollment`
--
ALTER TABLE `tblenrollment`
  MODIFY `ENROLLMENT_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tblenrollmentdetails`
--
ALTER TABLE `tblenrollmentdetails`
  MODIFY `DETAIL_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `tblgrades`
--
ALTER TABLE `tblgrades`
  MODIFY `GRADE_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tblhistoryenrollment`
--
ALTER TABLE `tblhistoryenrollment`
  MODIFY `HISTORY_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblpayments`
--
ALTER TABLE `tblpayments`
  MODIFY `PAYMENT_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tblschedule`
--
ALTER TABLE `tblschedule`
  MODIFY `SCHEDULE_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tblscheduledays`
--
ALTER TABLE `tblscheduledays`
  MODIFY `SCHEDULEDAY_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tblscheduletimes`
--
ALTER TABLE `tblscheduletimes`
  MODIFY `SCHEDULETIME_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tblschoolyear`
--
ALTER TABLE `tblschoolyear`
  MODIFY `SY_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tblsections`
--
ALTER TABLE `tblsections`
  MODIFY `SECTION_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `tblsectionschedule`
--
ALTER TABLE `tblsectionschedule`
  MODIFY `SECTIONSCHEDULE_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tblstudent`
--
ALTER TABLE `tblstudent`
  MODIFY `S_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tblsubjects`
--
ALTER TABLE `tblsubjects`
  MODIFY `SUBJECT_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `tblusers`
--
ALTER TABLE `tblusers`
  MODIFY `UID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `tblusertype`
--
ALTER TABLE `tblusertype`
  MODIFY `TYPEID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `alumni_details`
--
ALTER TABLE `alumni_details`
  ADD CONSTRAINT `fk_alumni_addedby` FOREIGN KEY (`AddedBy`) REFERENCES `tblusers` (`UID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_alumni_student` FOREIGN KEY (`S_ID`) REFERENCES `tblstudent` (`S_ID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `tblcourses`
--
ALTER TABLE `tblcourses`
  ADD CONSTRAINT `fk_course_programhead` FOREIGN KEY (`PROGRAM_HEAD_ID`) REFERENCES `tblusers` (`UID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `tblenrollment`
--
ALTER TABLE `tblenrollment`
  ADD CONSTRAINT `fk_enrollment_course` FOREIGN KEY (`COURSE_ID`) REFERENCES `tblcourses` (`COURSE_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_enrollment_encodedby` FOREIGN KEY (`ENCODED_BY`) REFERENCES `tblusers` (`UID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_enrollment_section` FOREIGN KEY (`SECTION_ID`) REFERENCES `tblsections` (`SECTION_ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_enrollment_student` FOREIGN KEY (`S_ID`) REFERENCES `tblstudent` (`S_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_enrollment_sy` FOREIGN KEY (`SY_ID`) REFERENCES `tblschoolyear` (`SY_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tblenrollmentdetails`
--
ALTER TABLE `tblenrollmentdetails`
  ADD CONSTRAINT `fk_endetails_enrollment` FOREIGN KEY (`ENROLLMENT_ID`) REFERENCES `tblenrollment` (`ENROLLMENT_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_endetails_subject` FOREIGN KEY (`SUBJECT_ID`) REFERENCES `tblsubjects` (`SUBJECT_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tblgrades`
--
ALTER TABLE `tblgrades`
  ADD CONSTRAINT `fk_grades_enrollment` FOREIGN KEY (`ENROLLMENT_ID`) REFERENCES `tblenrollment` (`ENROLLMENT_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_grades_student` FOREIGN KEY (`S_ID`) REFERENCES `tblstudent` (`S_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_grades_subject` FOREIGN KEY (`SUBJECT_ID`) REFERENCES `tblsubjects` (`SUBJECT_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_grades_sy` FOREIGN KEY (`SY_ID`) REFERENCES `tblschoolyear` (`SY_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tblhistoryenrollment`
--
ALTER TABLE `tblhistoryenrollment`
  ADD CONSTRAINT `fk_history_course` FOREIGN KEY (`COURSE_ID`) REFERENCES `tblcourses` (`COURSE_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_history_enrollment` FOREIGN KEY (`ENROLLMENT_ID`) REFERENCES `tblenrollment` (`ENROLLMENT_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_history_student` FOREIGN KEY (`S_ID`) REFERENCES `tblstudent` (`S_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_history_sy` FOREIGN KEY (`SY_ID`) REFERENCES `tblschoolyear` (`SY_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tblpayments`
--
ALTER TABLE `tblpayments`
  ADD CONSTRAINT `fk_payments_enrollment` FOREIGN KEY (`ENROLLMENT_ID`) REFERENCES `tblenrollment` (`ENROLLMENT_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_payments_receivedby` FOREIGN KEY (`RECEIVED_BY`) REFERENCES `tblusers` (`UID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_payments_student` FOREIGN KEY (`S_ID`) REFERENCES `tblstudent` (`S_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tblschedule`
--
ALTER TABLE `tblschedule`
  ADD CONSTRAINT `fk_schedule_classroom` FOREIGN KEY (`CLASSROOM_ID`) REFERENCES `tblclassrooms` (`CLASSROOM_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_schedule_day` FOREIGN KEY (`SCHEDULEDAY_ID`) REFERENCES `tblscheduledays` (`SCHEDULEDAY_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_schedule_instructor` FOREIGN KEY (`INSTRUCTOR_ID`) REFERENCES `tblusers` (`UID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_schedule_subject` FOREIGN KEY (`SUBJECT_ID`) REFERENCES `tblsubjects` (`SUBJECT_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_schedule_time` FOREIGN KEY (`SCHEDULETIME_ID`) REFERENCES `tblscheduletimes` (`SCHEDULETIME_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tblsections`
--
ALTER TABLE `tblsections`
  ADD CONSTRAINT `fk_sections_course` FOREIGN KEY (`COURSE_ID`) REFERENCES `tblcourses` (`COURSE_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sections_sy` FOREIGN KEY (`SY_ID`) REFERENCES `tblschoolyear` (`SY_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tblsectionschedule`
--
ALTER TABLE `tblsectionschedule`
  ADD CONSTRAINT `fk_sectionschedule_schedule` FOREIGN KEY (`SCHEDULE_ID`) REFERENCES `tblschedule` (`SCHEDULE_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sectionschedule_section` FOREIGN KEY (`SECTION_ID`) REFERENCES `tblsections` (`SECTION_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tblstudent`
--
ALTER TABLE `tblstudent`
  ADD CONSTRAINT `fk_student_account` FOREIGN KEY (`ACCOUNT_UID`) REFERENCES `tblusers` (`UID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_student_addedby` FOREIGN KEY (`AddedBy`) REFERENCES `tblusers` (`UID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `tblsubjects`
--
ALTER TABLE `tblsubjects`
  ADD CONSTRAINT `fk_subjects_course` FOREIGN KEY (`COURSE_ID`) REFERENCES `tblcourses` (`COURSE_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tblusers`
--
ALTER TABLE `tblusers`
  ADD CONSTRAINT `fk_users_usertype` FOREIGN KEY (`TYPEID`) REFERENCES `tblusertype` (`TYPEID`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
