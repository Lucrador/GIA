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

$userid = required_param('id', PARAM_INT);

global $DB, $USER, $CFG;

$fifth_year_status_not = $DB->get_record_sql("SELECT lg.id as 'id'
		FROM {local_gia} lg
		WHERE lg.user_id = '$userid' "); //searching if user has records on 5th year status
$status_id = $fifth_year_status_not->id; //getting id from table to update records

require_login (); // requires log in

$baseurl = new moodle_url ( '/local/gia/update.php' ); // important to create page class
$baseurl2 = new moodle_url ( 'update.php',array('courseid'=> $courseid,'id' => $userid)); //2nd baseurl to avoid losing course id parameter in form
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
else if($roleid == 3) { //admin view
	
	$mform = new update_state($baseurl2); //new update form with $baseurl2 as parameter
	
	if ($fromform = $mform->get_data()){
	  
		$status = $fromform->status; //getting the status choosen by user in form
		
		if($status == '0'){ //if user set 'Approved' == 0
			$realstatus = '1'; //then the real status to insert in db is '1'
		}
		else if($status == '1'){ //if user set "Deny' == 1
			$realstatus = '0'; //then real status to insert in db is '0'
		}
		if($fifth_year_status_not){
			$records->id = $status_id;
			$records->user_id = $userid;
			$records->status = $realstatus;
			$DB->update_record('local_gia', $records); //if user already has records, update record with the status chosen 
		}
		else if(!$fifth_year_status_not){
			$records->user_id = $userid;
			$records->status = $realstatus;
			$DB->insert_record('local_gia', $records); //if user doesn't have records, insert record with the status chosen
		}
		$message4 = '<div class="alert alert-success">Data has been changed succesfully.</div>'; //show message that records has been updated
		echo $message4;
		echo "<br>";
		echo $OUTPUT->single_button($urltogo, 'Home'); //go back home
		echo $OUTPUT->single_button($urltogo2, 'Back'); //go back to index
		
		
	
	}
	else {
		$mform->set_data($toform);
		$mform->display();
		echo $OUTPUT->single_button($urltogo2, 'Back');
		echo $OUTPUT->single_button($urltogo, 'Home');
	}
}
echo $OUTPUT->footer (); // prints footer