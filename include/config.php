<?php
defined('DB_SERVER') ? null : define("DB_SERVER", "localhost");
defined('DB_USER')   ? null : define("DB_USER", "root");
defined('DB_PASS')   ? null : define("DB_PASS", "");
defined('DB_NAME')   ? null : define("DB_NAME", "alumni_db");

/* Added so the Enrollment / Payment modules (fee computation and the
   Print Enrollment Form / Official Receipt) have the values they need. */
defined('ENROLLMENT_FEE')   ? null : define("ENROLLMENT_FEE", 1000);
defined('MAJOR_UNIT_RATE')  ? null : define("MAJOR_UNIT_RATE", 850);
defined('MINOR_UNIT_RATE')  ? null : define("MINOR_UNIT_RATE", 450);
defined('SCHOOL_NAME')    ? null : define("SCHOOL_NAME", "Colegio de Santa Rita de San Carlos, Inc.");
defined('SCHOOL_ADDRESS') ? null : define("SCHOOL_ADDRESS", "San Carlos City");
defined('SCHOOL_CONTACT') ? null : define("SCHOOL_CONTACT", "0900-000-0000");
defined('SCHOOL_LOGO')    ? null : define("SCHOOL_LOGO", "csr.png");

defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);

$this_file = str_replace('\\', '/', __FILE__);
$doc_root  = $_SERVER['DOCUMENT_ROOT'];

defined('SITE_ROOT') ? null : define('SITE_ROOT', $_SERVER['DOCUMENT_ROOT']);
defined('LIB_PATH')  ? null : define('LIB_PATH', SITE_ROOT.DS.'include');

$webRoot = str_replace(array($doc_root, "include/config.php"), '', $this_file);
$srvRoot = str_replace('config/config.php', '', $this_file);

define('WEB_ROOT', $webRoot);
define('SRV_ROOT', $srvRoot);
?>