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

$courseid = optional_param('courseid','-1',PARAM_INT); //retrieving course id as a parameter for roles

global $DB, $USER, $CFG;

require_login (); // requires log in

$fifth_year_status_not = $DB->get_record_sql("SELECT lg.id as 'id'
		FROM {local_gia} lg
		WHERE lg.user_id = '$USER->id' "); //searching if user has records on the 5th year status
$status_id = $fifth_year_status_not->id; //retrieving the id of the record for future update record

$urltogo = ($CFG->wwwroot.'/my/index.php');// go home
if($courseid == '-1'){ //security! if someone tries to copy url, they will be sent home.
	redirect($urltogo);
}
$urltogo2 = new moodle_url ('/local/gia/index.php', array('courseid'=>$courseid));//url to go back to index with the course id as parameter
$baseurl = new moodle_url ( '/local/gia/success.php' ); // important to create page class

$context = context_course::instance ($courseid); // setting context with course id
//$context = context_system::instance();

$roles = get_user_roles($context, $USER->id, false); //getting roles from context
$role = key($roles);
$roleid = $roles[$role]->roleid; //getting role id's for each role

$PAGE->set_context ( $context );
$PAGE->set_url ( $baseurl );
$PAGE->set_pagelayout ( 'standard' );

$title = 'FIFTH YEAR CIVIL ENGINEER 2015';
$title2 = 'APPLY TO 5TH YEAR';
$PAGE->set_title($title);
$PAGE->set_heading($title);
echo $OUTPUT->header (); // prints header
echo $OUTPUT->heading ($title2);

if($roleid != 5){ //if user is not student, show error
	echo '<div class="alert alert-danger">You do not have permission to access this page</div';
}
else { //student view
echo "<br>";
$message4 = '<div class="alert alert-success"> Your file has been submitted succesfully.</div>'; //message to tell user the pdf was succesfully submited
echo $message4;
echo "<br>";
echo $OUTPUT->single_button($urltogo, 'Home'); //go back home
echo $OUTPUT->single_button($urltogo2, 'Back'); //go back to index with course id as parameter

if($fifth_year_status_not){
	$records->id = $status_id;
	$records->user_id = $USER->id;
	$records->status = 2;
	$DB->update_record('local_gia', $records); //if user already has records, will be updated, and the status will be set with a '2', meaning the user uploaded a pdf
}
else{
	$records->user_id = $USER->id;
	$records->status = 2;
	$DB->insert_record('local_gia', $records); //if user doesn't have records, will be inserted, and the status will be set with a '2', meaning the user uploaded a pdf
}
}
echo $OUTPUT->footer (); // prints footer