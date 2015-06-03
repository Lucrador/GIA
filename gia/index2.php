<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 *
*
* @package    local
* @subpackage gia
* @copyright  2015
* 				
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

require_once (dirname ( __FILE__ ) . '/../../config.php');
require_once($CFG->dirroot.'/local/gia/form.php');

$courseid = optional_param('courseid','-1',PARAM_INT); //retrieving course id for roles

global $DB, $USER, $CFG;

require_login (); // requires log in

$baseurl = new moodle_url ( '/local/gia/index2.php' ); // important to create page class
$baseurl2 = new moodle_url('index2.php', array('courseid'=>$courseid)); //creating a 2nd baseurl for the form, to avoid losing the parameter
$urltogo = ($CFG->wwwroot.'/my/index.php'); //url to go home
if($courseid == '-1'){ //security! if someone tries to copy url, they will be sent home.
	redirect($urltogo);
}
$urltogo2 = new moodle_url ('/local/gia/index.php', array('courseid'=>$courseid)); //url to go back to index, wiht the param

$context = context_course::instance ($courseid); // getting context from course id from the previous course
//$context = context_system::instance();

$roles = get_user_roles($context, $USER->id, false); //getting roles from context
$role = key($roles);
$roleid = $roles[$role]->roleid; //getting role id from each role

$PAGE->set_context ( $context );
$PAGE->set_url ( $baseurl );
$PAGE->set_pagelayout ( 'standard' );

$title = 'FIFTH YEAR CIVIL ENGINEER 2015';
$title2 = 'APPLY TO 5TH YEAR';
$PAGE->set_title($title);
$PAGE->set_heading($title);
echo $OUTPUT->header (); // prints header
echo $OUTPUT->heading ($title2);

$fifth_year_status_not = $DB->get_record_sql("SELECT *
		FROM {local_gia} lg
		WHERE lg.user_id = '$USER->id' "); //searching in database if user has record for 5th year status

$user_rut = $DB->get_record_sql("SELECT ui.user_rut as 'rut' 
		FROM {user_info} ui
		WHERE ui.user_id = '$USER->id'"); //getting the RUT of user, to save the pdf with the RUT
$rut = $user_rut->rut; //shorter variable

if($roleid != 5){ //if user is not student, show error
	echo '<div class="alert alert-danger">You do not have permission to access this page</div';
}
else { //student view
$message1 = "Read, print and sign the '2015 5th year admission form.pdf' located in the course page."; 
$message2 = "You can upload a scan of the signed document or deliver it to the secretary in D buliding.";
//messages to tell user what to do
echo $message1;
echo "<br>";
echo "<br>";
echo $message2;

echo "<br>";
echo "<br>";

$mform = new filepicker($baseurl2); //new form! filepicker to upload the pdf....$baseurl2 to avoid losing parameter when sending the data

if ($fromform = $mform->get_data()){
		
$to_success_page = new moodle_url ('/local/gia/success.php', array('courseid'=>$courseid)); //to success page, carrying course id parameter
$success = $mform->save_file('userfile', "C:/xampp/moodledata/filedir/$rut.pdf", $override); //saving file named, with user's rut. $override means it will override if thre is a file named exactly the same
redirect($to_success_page); //redirecting to the success page

}
else {
	$mform->set_data($toform); 
	$mform->display();
	echo $OUTPUT->single_button($urltogo2, 'Back'); //go back to index
	echo $OUTPUT->single_button($urltogo, 'Home'); //go back home
}
}
echo $OUTPUT->footer (); //print footer