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

$courseid = optional_param('courseid','-1',PARAM_INT); //retrieving course id as parameter for roles

global $DB, $USER, $CFG;

require_login (); // requires log in

$baseurl = new moodle_url ( '/local/gia/insert.php'); // important to create page class
$baseurl2 = new moodle_url('insert.php', array('courseid'=>$courseid)); // 2nd base url to avoid losing course id parameter
$urltogo = $CFG->wwwroot.'/my/index.php'; //url to go back home
if($courseid == '-1'){ //security! if someone tries to copy url, they will be sent home.
	redirect($urltogo);
}
$urltogo2 = new moodle_url ('/local/gia/index.php', array('courseid'=>$courseid)); //url to go back to index with course id as parameter

$context = context_course::instance ($courseid); // context set by course id
//$context = context_system::instance();

$roles = get_user_roles($context, $USER->id, false); //getting roles from context
$role = key($roles);
$roleid = $roles[$role]->roleid; //getting role id's for each role

$PAGE->set_context ( $context );
$PAGE->set_url ( $baseurl );
$PAGE->set_pagelayout ( 'standard' );

$title = 'FIFTH YEAR CIVIL ENGINEER 2015';
$title2 = 'UPLOAD FORM FOR STUDENT';
$PAGE->set_title($title);
$PAGE->set_heading($title);
echo $OUTPUT->header (); // prints header
echo $OUTPUT->heading ($title2);

if($roleid != 3){ //if user is student, show error
	echo '<div class="alert alert-danger">You do not have permission to access this page</div';
}
else if ($roleid == 3){ //admin view

	$mform = new insert($baseurl2); //new insert form with $baseurl2 as parameter to avoid losing course id

if ($fromform = $mform->get_data()){
	
	$user_rut = $fromform->user_rut; //setting user rut, from what user typed in the form
	$success = $mform->save_file('users_file', "C:/xampp/moodledata/filedir/$user_rut.pdf", $override);	//saving file named, with user's rut. $override means it will override if thre is a file named exactly the same
	$user_id = $DB->get_record_sql("SELECT ui.user_id as 'id'
			FROM {user_info} ui
			WHERE ui.user_rut = '$user_rut'"); //getting user searched id
	$id = $user_id->id; //shorter variabler
	
	$fifth_year_status_not = $DB->get_record_sql("SELECT lg.id as 'id'
			FROM {local_gia} lg
			WHERE lg.user_id = '$id' "); //searching if user searched has records in 5th years status
	$status_id = $fifth_year_status_not->id;//getting id from table to update records
	
	$message4 = '<div class="alert alert-success">Your file for RUT:'.$user_rut.' has been submitted succesfully.</div>'; //message to show that the file for the user has been submitted
	echo "<br>";
	echo "<br>";
	echo $message4;

	if($fifth_year_status_not){
		$records->id = $status_id;
		$records->user_id = $id;
		$records->status = 2;
		$DB->update_record('local_gia', $records); //if user searched has records, update it, and set status with a 2, meaning user sent form
	}
	else if (!$fifth_year_status_not){
		$records->user_id = $id;
		$records->status = 2;
		$DB->insert_record('local_gia', $records); //if user doesn't have records, insert record, and set status with a 2, meaning user sent form
	}	
	echo $OUTPUT->single_button($urltogo2, 'Back'); //go back to index
	echo $OUTPUT->single_button($urltogo, 'Home'); //go back home
}
else {
	$mform->set_data($toform);
	$mform->display();
	echo $OUTPUT->single_button($urltogo2, 'Back');
	echo $OUTPUT->single_button($urltogo, 'Home');
}
}
echo $OUTPUT->footer (); // prints footer