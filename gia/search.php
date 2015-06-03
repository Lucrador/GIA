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
require_once($CFG->dirroot.'/local/gia/tablas.php');

$courseid = optional_param('courseid','-1',PARAM_INT); //retrieving course id for roles

global $DB, $USER, $CFG, $COURSE;

require_login (); // requires log in

$baseurl = new moodle_url ( '/local/gia/search.php' ); // important to create page class
$baseurl2 = new moodle_url('search.php', array('courseid'=>$courseid)); //2nd base url to avoid losing parameter in form
$urltogo = new moodle_url('/my/index.php'); //url to go back gome
if($courseid == '-1'){ //security! if someone tries to copy url, they will be sent home.
	redirect($urltogo);
}
$urltogo2 = new moodle_url ('/local/gia/index.php', array('courseid'=>$courseid)); //url to go back to index with course id as paramter
$context = context_course::instance ($courseid); // context set by course id
//$context = context_system::instance();

 $roles = get_user_roles($context, $USER->id, false); //getting roles from context
 $role = key($roles);
 $roleid = $roles[$role]->roleid; //getting role id's for each role

$PAGE->set_context ( $context );
$PAGE->set_url ( $baseurl );
$PAGE->set_pagelayout ( 'standard' );

$title = 'FIFTH YEAR CIVIL ENGINEER 2015';
$title2 = 'SEARCH STUDENT';
$PAGE->set_title($title);
$PAGE->set_heading($title);
echo $OUTPUT->header (); // prints header
echo $OUTPUT->heading ($title2);

if($roleid != 3){ //if user is student, show error
	echo '<div class="alert alert-danger">You do not have permission to access this page</div';
}
else if($roleid == 3) { //admin view

$mform = new searchform($baseurl2); //new searchfrom with $baseurl as parameter to avoid losing courseid

if ($fromform = $mform->get_data()){

	$rut_search = $fromform->rut_search; //retrieving 'rut_search' from what user typed
	$user_info= $DB->get_records_sql("SELECT u.firstname as 'name', u.lastname as 'lastname', u.id as 'id',ui.user_rut as 'rut', lg.status  as 'status'   
			FROM {user_info} ui
			JOIN {user} u
			ON u.id = ui.user_id
			JOIN {local_gia} lg
			ON lg.user_id = u.id
			WHERE ui.user_rut = '$rut_search'");  //getting records for setting up the table
	
	$user_info2 = $DB->get_records_sql("SELECT u.firstname as 'name', u.lastname as 'lastname', u.id as 'id', ui.user_rut as 'rut'   
			FROM {user_info} ui
			JOIN {user} u
			ON u.id = ui.user_id
			WHERE ui.user_rut = '$rut_search'"); //if user in search doesn't have records on 5th year status, this will be in use

	if(empty($user_info)){ //if user in search doesn't have records on 5th year status, using $user_info2
		$user_info = $user_info2;
		$user_info->status = NULL; //setting status as NULL
		$table = tabla::getInfo($user_info, $courseid); //showing table
		echo html_writer::table($table);
		echo $OUTPUT->single_button($urltogo2, 'Back'); //go back to index
		echo $OUTPUT->single_button($urltogo, 'Home'); //go back home
		

	}
	else {
			$table = tabla::getInfo($user_info); //show table
			echo html_writer::table($table); 
			echo $OUTPUT->single_button($urltogo2, 'Back');	
			echo $OUTPUT->single_button($urltogo, 'Home');
	}
}		
else {
	$mform->set_data($toform);
	$mform->display();
	echo $OUTPUT->single_button($urltogo2, 'Back');
	echo $OUTPUT->single_button($urltogo, 'Home');
}
}
echo $OUTPUT->footer (); // prints footer