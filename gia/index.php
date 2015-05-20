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

global $DB, $USER, $CFG;

require_login (); // Requiere estar log in

$baseurl = new moodle_url ( '/local/gia/index.php' ); // importante para crear la clase pagina
$urltogo = $CFG->wwwroot.'/my/index.php';
$context = context_system::instance (); // context_system::instance();
$PAGE->set_context ( $context );
$PAGE->set_url ( $baseurl );
$PAGE->set_pagelayout ( 'standard' );

$title = 'FIFTH YEAR CIVIL ENGINEER 2015';

$PAGE->set_title($title);
$PAGE->set_heading($title);

$params = array('userid' => $USER->id);
echo $OUTPUT->header (); // Imprime el header
echo $OUTPUT->heading ($title);

$user_info = $DB->get_record_sql("SELECT u.lastname as 'lastname', u.firstname as 'name', ui.user_status as 'status',
		ui.adm_date as 'date', ui.study_plan as 'plan', ui.plan_version as 'version'
		FROM {user_info} ui
		JOIN {user} u 
		ON u.id = ui.user_id
		WHERE '$USER->id' = u.id");
//user info for table of data

$datetime = date_create($user_info->date)->format('d-m-Y');
$espacios = '<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>';

if($user_info->status == 1){
	$realstatus = 'Active';
}

else  if($user_info->status == 2) {
	$realstatus = "Not Active";
}

else {
	$realstatus = "Pending";
}
echo "<table><tr><td>Student Name:       </td>$espacios<td>$user_info->lastname, $user_info->name </td></tr>
<tr><td>State:              </td>$espacios<td>$realstatus                        </td></tr>
<tr><td>Admission Date:     </td>$espacios<td>$datetime                         </td></tr>
<tr><td>Study Plan:         </td>$espacios<td>$user_info->plan                        </td></tr>
<tr><td>Study Plan Version: </td>$espacios<td>$user_info->version                  </td></tr></table>  ";

echo "<br>";
echo "<br>";

$fifth_year_status = $DB->get_record_sql("SELECT *
		FROM {local_gia} lg
		WHERE (lg.user_id = '$USER->id') AND (lg.status = 1)"); //promoted

$fifth_year_status_not = $DB->get_record_sql("SELECT *
		FROM {local_gia} lg
		WHERE lg.user_id = '$USER->id' "); //anything

$fifth_year_status2 = $DB->get_record_sql("SELECT *
		FROM {local_gia} lg
		WHERE lg.user_id = '$USER->id' AND lg.status = 2");//sent application form

$main_courses_passed = $DB->get_record_sql("SELECT count(cs.status) as 'status'
		FROM {course_status} cs
		JOIN {course} c
		ON c.id = cs.course_id
		WHERE (c.shortname not LIKE 'BPL%') AND (c.shortname not LIKE 'GYM%') AND (cs.user_id = '$USER->id') AND (cs.status= '1')"); //cs.status == '1' means course passed
//count of all main curses passed
$main_courses_total = $DB->get_record_sql("SELECT count(cs.status) as 'status'
		FROM {course_status} cs
		JOIN {course} c
		ON c.id = cs.course_id
		WHERE (c.shortname not LIKE 'BPL%') AND (c.shortname not LIKE 'GYM%') AND (cs.user_id = '$USER->id')");
//count of all main courses
$main_courses_in_progress = $DB->get_record_sql("SELECT count(cs.status) as 'status'
		FROM {course_status} cs
		JOIN {course} c
		ON c.id = cs.course_id
		WHERE (c.shortname not LIKE 'BPL%') AND (c.shortname not LIKE 'GYM%') AND (cs.user_id = '$USER->id') AND (cs.status= '2')"); //cs.status == '2' means course in progress
//count of all main courses in progress
$gym_passed = $DB->get_record_sql("SELECT count(cs.status) as 'status'
		FROM {course_status} cs
		JOIN {course} c
		ON c.id = cs.course_id
		WHERE (c.shortname LIKE 'GYM%') AND (cs.user_id = '$USER->id' AND cs.status = '1')");
//count of all gym courses passed
$gym_total = $DB->get_record_sql("SELECT count(cs.status) as 'status'
		FROM {course_status} cs
		JOIN {course} c
		ON c.id = cs.course_id
		WHERE (c.shortname LIKE 'GYM%') AND (cs.user_id = '$USER->id')");
//count of all gym courses
$english_passed = $DB->get_record_sql("SELECT count(cs.status) as 'status'
		FROM {course_status} cs
		JOIN {course} c
		ON c.id = cs.course_id
		WHERE (c.shortname LIKE 'BPL%') AND (cs.user_id = '$USER->id' AND cs.status = '1')");
//count of all english courses passed
$english_total = $DB->get_record_sql("SELECT count(cs.status) as 'status'
		FROM {course_status} cs
		JOIN {course} c
		ON c.id = cs.course_id
		WHERE (c.shortname LIKE 'BPL%') AND (cs.user_id = '$USER->id')");
// count of all english courses

$pending_main = ($main_courses_total->status - $main_courses_passed->status) ;
$in_progress = $main_courses_in_progress->status;
$pending_gym = ($gym_total->status - $gym_passed->status);
$pending_english = ($english_total->status - $english_passed->status);

if($fifth_year_status){
	echo "You have already been promoted to 5th year.";
	echo $OUTPUT->single_button($urltogo,'Return');
}
//there is already a record that he's been promoted, so we decide it to be simpler.

if($fifth_year_status2){
	echo "You have already sent your application. Check again later. Your applications is been proccesed.";
	echo $OUTPUT->single_button($urltogo,'Return');
}
else {
		
if( ($pending_main) == 0 && ($pending_gym) == 0 && ($pending_english) == 0 ){
	echo "Congratulations! You are going to be automatically promoted to 5th year";
	echo $OUTPUT->single_button($urltogo,'Return');
	
if($fifth_year_status_not){
	$records->user_id = $USER->id;
	$records->status = 1;
	$DB->update_record('local_gia', $records);
}
else{
	$records->user_id = $USER->id;
	$records->status = 1;
	$DB->insert_record('local_gia', $records);
}
	
	
}
//automatically promoted

else if (($pending_main - $in_progress) <=3 && ($pending_main - $in_progress) >0  && ($pending_gym) == 0 && ($pending_english) == 0) {
	echo "Academic Advance";
	echo "<br>";
	
	echo "<table><tr><td>Asignatures pending*:</td>$espacios<td>$pending_main</td></tr>
	             <tr><td>Asignatures in progress*:</td>$espacios<td>$in_progress</td></tr></table>";
	echo "*not including English or Sports";
	echo "<br>";
	echo "<br>";     
	echo "<br>";
	
	echo "English and Sports";
	echo "<br>";
	
	echo "<table><tr><td>English Levels Pending:</td>$espacios<td>$pending_english</td></tr>
	             <tr><td>Sports Courses Pending:</td>$espacios<td>$pending_gym</td></tr></table>";

	echo "<br>";
	echo "<br>";
	echo "Warning: In order to be promoted to 5th year you need to pass at least 3 of the asignatures you are coursing and send the application form at the bottom of the page.";
	
	echo "<br>";
	echo "<br>";
	echo $OUTPUT->single_button('index2.php','Apply to 5th Year');
	echo $OUTPUT->single_button($urltogo,'Cancel');
	
	
}

else if(  ($pending_main - $in_progress) == 0 && ($pending_gym) <=2 && ($pending_english) <=2 ){
	
	echo "Academic Advance";
	echo "<br>";
	
	echo "<table><tr><td>Asignatures pending*:</td>$espacios<td>$pending_main</td></tr>
	<tr><td>Asignatures in progress*:</td>$espacios<td>$in_progress</td></tr></table>";
	echo "*not including English or Sports";
	echo "<br>";
		echo "<br>";
		echo "<br>";
	
		echo "English and Sports";
		echo "<br>";
	
	echo "<table><tr><td>English Levels Pending:</td>$espacios<td>$pending_english</td></tr>
		<tr><td>Sports Courses Pending:</td>$espacios<td>$pending_gym</td></tr></table>";
	
		echo "<br>";
		echo "<br>";
		echo "To be automatically promoted to 5th Year you need to complete all of you courses(including english and sports), otherwise apply.";
	echo "<br>";
	echo "<br>";
	echo $OUTPUT->single_button('index2.php','Apply to 5th Year');
		echo $OUTPUT->single_button($urltogo,'Cancel');
		
		if($fifth_year_status_not){
			$records->user_id = $USER->id;
			$records->status = 2;
			$DB->update_record('local_gia', $records);
		}
		else{
			$records->user_id = $USER->id;
			$records->status = 2;
			$DB->insert_record('local_gia', $records);
		}
}

else if ( ($pending_main - $in_progress) >5 OR ($pending_gym) >5 OR ($pending_english) >5 ){
	echo "Academic Advance";
	echo "<br>";
	
	echo "<table><tr><td>Asignatures pending*:</td>$espacios<td>$pending_main</td></tr>
	<tr><td>Asignatures in progress*:</td>$espacios<td>$in_progress</td></tr></table>";
	echo "*not including English or Sports";
			echo "<br>";
			echo "<br>";
			echo "<br>";
	
			echo "English and Sports";
					echo "<br>";
	
					echo "<table><tr><td>English Levels Pending:</td>$espacios<td>$pending_english</td></tr>
					<tr><td>Sports Courses Pending:</td>$espacios<td>$pending_gym</td></tr></table>";
	
					echo "<br>";
					echo "<br>";
					echo "DENIED due to your actual situation, you are not eligible to be promoted to 5th year.";
	echo "<br>";
	echo "<br>";
		
			echo $OUTPUT->single_button($urltogo,'Return');
			$records->user_id = $USER->id;
			$records->status = 0;
			$DB->insert_record('local_gia', $records);
}
}


echo $OUTPUT->footer (); // imprime el footer