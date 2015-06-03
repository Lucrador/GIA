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

$courseid = optional_param('courseid','-1',PARAM_INT);//retrieving course id as parameter for roles

global $DB, $USER, $CFG;

require_login (); // requires log in

$baseurl = new moodle_url ( '/local/gia/stay.php' ); // important to create page class
$urltogo = $CFG->wwwroot.'/my/index.php'; //url to go back home
if($courseid == '-1'){ //security! if someone tries to copy url, they will be sent home.
	redirect($urltogo);
}
$urltogo2  = new moodle_url ( '/local/gia/index.php', array('courseid'=>$courseid)); //url to go back to index, with course id as parameter

$context = context_course::instance ($courseid); // context set with course id
//$context = context_system::instance();

$roles = get_user_roles($context, $USER->id, false); //getting roles from context
$role = key($roles);
$roleid = $roles[$role]->roleid; //getting role id's for each role

$PAGE->set_context ( $context );
$PAGE->set_url ( $baseurl );
$PAGE->set_pagelayout ( 'standard' );

$title = 'FIFTH YEAR CIVIL ENGINEER 2015';

$PAGE->set_heading($title);

echo $OUTPUT->header (); // Imprime el header
echo $OUTPUT->heading ($title);

if($roleid != 5){ //if user is not student show error
	echo '<div class="alert alert-danger">You do not have permission to access this page</div';
}
else { //student view

	$records->user_id = $USER->id;
	$records->status = 3;
	$DB->insert_record('local_gia', $records); //user decided to stay, so inserting records in table, with 3, meaning he stays in 4th year

echo "<br>";
$message = '<div class="alert alert-info">You are staying in 4th year.</div>'; //message to tell he stays in 4th year
echo $message;
echo "<br>";
echo $OUTPUT->single_button($urltogo2, 'Back'); //go back to index
echo $OUTPUT->single_button($urltogo, 'Home'); //go back home
}
echo $OUTPUT->footer (); // prints footer