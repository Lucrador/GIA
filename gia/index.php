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

$courseid = optional_param('courseid','-1',PARAM_INT); //retrieving the course id to get roles

global $DB, $USER, $CFG, $COURSE;

require_login (); // requires log in

$baseurl = new moodle_url ( '/local/gia/index.php'); // important to create page class
$urltogo = $CFG->wwwroot.'/my/index.php';  //url to return to home
if($courseid == '-1'){ //security! if someone tries to copy url, they will be sent home.
  redirect($urltogo);
}
$context = context_course::instance ($courseid); // getting context from the course id of the course the link was in
$roles = get_user_roles($context, $USER->id, false); //getting roles from the context
$role = key($roles);
$roleid = $roles[$role]->roleid; //getting the roleid from each role
$PAGE->set_context ( $context ); //setting context for the hole page
$PAGE->set_url ( $baseurl ); //setting url
$PAGE->set_pagelayout ( 'standard' ); 

$url1 = new moodle_url ('/local/gia/insert.php',array('courseid'=>$courseid)); //url to insert.php, carrying courseid as parameter
$url2 = new moodle_url ('/local/gia/search.php',array('courseid'=>$courseid)); //url to serch.php, carrying courseid as parameter
$url3 = new moodle_url ('/local/gia/index2.php',array('courseid'=>$courseid)); //url to index2.php, carrying courseid as parameter
$url4 = new moodle_url ('/local/gia/stay.php',array('courseid'=>$courseid)); //url to stay.php, carrying courseid as parameter

$title = 'FIFTH YEAR CIVIL ENGINEER 2015';

$PAGE->set_title($title);
$PAGE->set_heading($title);
echo $OUTPUT->header (); // prints header
echo $OUTPUT->heading ($title);

if($roleid == 3){ //if role is not from student => view for admin

	$total = $DB->count_records('local_gia'); //counting all the records in the table that saves the records of situations for 5th year
	$approved = $DB->count_records('local_gia', array('status'=>1)); //counting records for every person that is promoted to 5th year
	$sent_form = $DB->count_records('local_gia', array('status'=>2)); //counting every person that sent form to apply to 5th year
	$denied = $DB->count_records('local_gia', array('status'=> 0)); //counting every person that can't be promoted to 5th year
	$stayed = $DB->count_records('local_gia',array('status'=>3)); //counting every person that decided to stay in 4th year
	
	
	$new_approved = ($approved * 100) / $total; //getting percentage for promoted ones
	$new_sentform = ($sent_form * 100) / $total; //getting percentage from sent forms
	$new_denied = ($denied * 100) / $total; //getting percentage for denied ones
	$new_stayed = ($stayed * 100) / $total; //getting percentage for ones who stayed
	//$new_missed = ($missed * 200) / $total;
	
	$bootstrap = '<meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>'; //bootstrap needed for the stacked bar to work
	
	$stacked_bar = ' <div class="progress">
                       <div class="progress-bar progress-bar-info" role="progressbar" style="width:'.$new_sentform.'%">
                         Received Form('.$sent_form.')
                       </div>
                       <div class="progress-bar progress-bar-success" role="progressbar" style="width:'.$new_approved.'%">
                         Approved('.$approved.')
                       </div>
                       <div class="progress-bar progress-bar-danger" role="progressbar" style="width:'.$new_denied.'%">
                         Denied('.$denied.')
                       </div>
                       <div class="progress-bar progress-bar-warning" role="progressbar" style="width:'.$new_stayed.'%">
                         Stayed('.$stayed.')
                       </div>  		
                     </div>'; //setting up stack bar with colors, representing each category and showing the number on the bar.
	
	echo "<br>";
	echo "<br>";
	echo $bootstrap.$stacked_bar; //showing bar 
	echo "<br>";
	echo "Total candidates: $total"; //showing all candidates
	echo "<br>";
	echo "<br>";
	echo $OUTPUT->single_button($url1, 'Insert form for student'); //button to go to insert.php
	echo $OUTPUT->single_button($url2, 'Search Student by RUT'); //button to go to search.php
	
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
else if($roleid != 3){ //roleid != 4 => student view

$user_info = $DB->get_record_sql("SELECT u.lastname as 'lastname', u.firstname as 'name', ui.user_status as 'status',
		ui.adm_date as 'date', ui.study_plan as 'plan', ui.plan_version as 'version'
		FROM {user_info} ui
		JOIN {user} u 
		ON u.id = ui.user_id
		WHERE '$USER->id' = u.id");
//getting data for the user that enters the page

$datetime = date_create($user_info->date)->format('d-m-Y');//formatting date to d-m-Y
$espacios = '<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>';

if($user_info->status == 1){ 
	$realstatus = 'ACTIVE'; //user status => 1 means it's active
}

else  if($user_info->status == 2) {
	$realstatus = "NOT ACTIVE"; //user status => 2 means not active
}

else {
	$realstatus = "PENDING"; //user status => 3 just in case there is another state
}
echo "<table><tr><td>Student Name:       </td>$espacios<td>$user_info->lastname, $user_info->name </td></tr>
<tr><td>State:              </td>$espacios<td>$realstatus                        </td></tr>
<tr><td>Admission Date:     </td>$espacios<td>$datetime                         </td></tr>
<tr><td>Study Plan:         </td>$espacios<td>$user_info->plan                        </td></tr>
<tr><td>Study Plan Version: </td>$espacios<td>$user_info->version                  </td></tr></table>  ";
//setting data table with the data
echo "<br>";
echo "<br>";

$fifth_year_status = $DB->get_record_sql("SELECT *
		FROM {local_gia} lg
		WHERE (lg.user_id = '$USER->id') AND (lg.status = 1)"); //searching in the database if user has already been promoted => 1 means promoted

$fifth_year_status_not = $DB->get_record_sql("SELECT lg.id as 'id'
		FROM {local_gia} lg
		WHERE lg.user_id = '$USER->id' "); //searching in database if user already has records
$status_id = $fifth_year_status_not->id; //getting the id to update records if neccesary
$fifth_year_status2 = $DB->get_record_sql("SELECT * 
		FROM {local_gia} lg
		WHERE lg.user_id = '$USER->id' AND lg.status = 2");//searching in database if user has already sent form => 2 means user sent form
$fifth_year_status3 = $DB->get_record_sql("SELECT *
		FROM {local_gia} lg
		WHERE lg.user_id = '$USER->id' AND lg.status = 3");//searching in database if user decided to stay in 4th year => 3 means he stayed in 4th year
//if lg.status == 0 it means he got 5th year denied

$main_courses_passed = $DB->get_record_sql("SELECT count(cs.status) as 'status'
		FROM {course_status} cs
		WHERE (cs.course_short not LIKE 'BPL%') AND (cs.course_short not LIKE 'GYM%') AND (cs.user_id = '$USER->id') AND (cs.status= '1')"); //getting count of main courses that user approved => cs.status == '1' means course passed
//count of all main curses passed
$main_courses_total = $DB->get_record_sql("SELECT count(cs.status) as 'status'
		FROM {course_status} cs
		WHERE (cs.course_short not LIKE 'BPL%') AND (cs.course_short not LIKE 'GYM%') AND (cs.user_id = '$USER->id')"); //getting count of main courses total of users carrer
//count of all main courses
$main_courses_in_progress = $DB->get_record_sql("SELECT count(cs.status) as 'status'
		FROM {course_status} cs
		WHERE (cs.course_short not LIKE 'BPL%') AND (cs.course_short not LIKE 'GYM%') AND (cs.user_id = '$USER->id') AND (cs.status= '2')"); //getting count of main courses user has in progress => cs.status == '2' means course in progress
//count of all main courses in progress
$gym_passed = $DB->get_record_sql("SELECT count(cs.status) as 'status'
		FROM {course_status} cs
		WHERE (cs.course_short LIKE 'GYM%') AND (cs.user_id = '$USER->id' AND cs.status = '1')"); //getting count of gym classes user has approved
//count of all gym courses passed
$gym_total = $DB->get_record_sql("SELECT count(cs.status) as 'status'
		FROM {course_status} cs
		WHERE (cs.course_short LIKE 'GYM%') AND (cs.user_id = '$USER->id')"); //getting count of gym classes total for users carrer
//count of all gym courses
$english_passed = $DB->get_record_sql("SELECT count(cs.status) as 'status'
		FROM {course_status} cs
		WHERE (cs.course_short LIKE 'BPL%') AND (cs.user_id = '$USER->id' AND cs.status = '1')"); //getting count of english classes has approved
//count of all english courses passed
$english_total = $DB->get_record_sql("SELECT count(cs.status) as 'status'
		FROM {course_status} cs
		WHERE (cs.course_short LIKE 'BPL%') AND (cs.user_id = '$USER->id')"); //getting count english classes total for users carrer
// count of all english courses

$pending_main = ($main_courses_total->status - $main_courses_passed->status); //getting pending main courses user has
$in_progress = $main_courses_in_progress->status; //getting courses in progress (shorter variable)
$pending_gym = ($gym_total->status - $gym_passed->status); //getting pending gym classes
$pending_english = ($english_total->status - $english_passed->status); //getting pending english classes

if($fifth_year_status){
	echo '<div class="alert alert-success">You have already been promoted to 5th year.</div>';
	echo $OUTPUT->single_button($urltogo,'Return');
}
//there is already a record that he's been promoted, so we decide it to be simpler.
else if($fifth_year_status2){
	echo '<div class="alert alert-info">You have already sent your application. Check again later. Your applications is been proccesed.</div>';
	echo $OUTPUT->single_button($urltogo,'Home');
}//if application already sent, and admin hasn't changed state, he shouldn't see anything different
else if( ($main_courses_total->status) == 0 OR ($gym_total->status) == 0 OR ($english_total->status) == 0 ) {
	echo '<div class="alert alert-danger">Sorry. There was a problem with you data. Please try again later.</div>';
    echo $OUTPUT->single_button($urltogo, 'Home'); 
}
//user has no data on {course_status}, means show error
else if($fifth_year_status3){
	echo '<div class="alert alert-info">You already decided to stay in 4th year.</div>';
	echo $OUTPUT->single_button($urltogo,'Home');	
}
//user already decided to stay in 4th year
else  {
		
if( (($pending_main) == 0 && ($pending_gym) == 0 && ($pending_english) == 0) && (($main_courses_total) > 0 && ($english_total)>0 && ($gym_total)>0)){
	echo '<div class="alert alert-success">Congratulations! You are going to be automatically promoted to 5th year.</div>';
	echo $OUTPUT->single_button($urltogo,'Home');
	
if($fifth_year_status_not){
	$records->id = $status_id;
	$records->user_id = $USER->id;
	$records->status = 1;
	$DB->update_record('local_gia', $records); //updating records and setting a 1 on status! (promoted)
}
else{
	$records->user_id = $USER->id;
	$records->status = 1;
	$DB->insert_record('local_gia', $records); //inserting records and setting a 1 on status! (promoted)
}
}
//automatically promoted becuase no courses pending

 if (($pending_main - $in_progress) <= 3 && ($pending_main - $in_progress) > 0  && ($pending_gym) == 0 && ($pending_english) == 0) {
	
 	echo "Academic Advance";
 	echo "<br>";
 	echo "<table><tr><td>Asignatures pending*:</td>$espacios<td>$pending_main</td></tr>
 	<tr><td>Asignatures in progress*:</td>$espacios<td>$in_progress</td></tr></table>";
 	echo "*not including English or Sports";
	echo "<br>";
 		echo "<br>";
 		echo "English and Sports";
	echo "<br>";
	echo "<table><tr><td>English Levels Pending:</td>$espacios<td>$pending_english</td></tr>
 		<tr><td>Sports Courses Pending:</td>$espacios<td>$pending_gym</td></tr></table>";
 		echo "<br>";
 		echo "<br>";

 	$message = '<div class="alert alert-info">Warning: In order to be promoted to 5th year you need to pass at least 3 of the asignatures you are coursing and send the application form at the bottom of the page.</div>';
	echo $message;
	echo $OUTPUT->single_button($url3,'Apply to 5th Year'); //going to index2
	echo $OUTPUT->single_button($url4,'Stay in 4th year'); //going to stay
	echo $OUTPUT->single_button($urltogo,'Cancel'); //going home
}//user is not automatically promoted, but can choose to apply to 5th year or stay in 4th year
else  if (($pending_main - $in_progress) <= 3 && ($pending_main - $in_progress) > 0  && ($pending_gym) <= 3 && ($pending_english) <= 3) {

	echo "Academic Advance";
	echo "<br>";
	echo "<table><tr><td>Asignatures pending*:</td>$espacios<td>$pending_main</td></tr>
	<tr><td>Asignatures in progress*:</td>$espacios<td>$in_progress</td></tr></table>";
	echo "*not including English or Sports";
	echo "<br>";
	echo "<br>";
	echo "English and Sports";
	echo "<br>";
	echo "<table><tr><td>English Levels Pending:</td>$espacios<td>$pending_english</td></tr>
	<tr><td>Sports Courses Pending:</td>$espacios<td>$pending_gym</td></tr></table>";
	echo "<br>";
	echo "<br>";

	$message = '<div class="alert alert-info">To be automatically promoted to 5th Year you need to complete all of you courses(including english and sports), otherwise apply.</div>';
	echo $message;
	echo $OUTPUT->single_button($url3,'Apply to 5th Year'); //going to index2
	echo $OUTPUT->single_button($url4,'Stay in 4th year'); //going to stay
	echo $OUTPUT->single_button($urltogo,'Cancel'); //going home
}//user is not automatically promoted, but can choose to apply to 5th year or stay in 4th year
else if(  ($pending_main - $in_progress) == 0 &&  ($pending_main)>0  &&($pending_gym) <=2 && ($pending_english) <=2 ){
	
	echo "Academic Advance";
	echo "<br>";
	echo "<table><tr><td>Asignatures pending*:</td>$espacios<td>$pending_main</td></tr>
	<tr><td>Asignatures in progress*:</td>$espacios<td>$in_progress</td></tr></table>";
	echo "*not including English or Sports";
	echo "<br>";
		echo "<br>";
		echo "English and Sports";
	echo "<br>";
	echo "<table><tr><td>English Levels Pending:</td>$espacios<td>$pending_english</td></tr>
		<tr><td>Sports Courses Pending:</td>$espacios<td>$pending_gym</td></tr></table>";
		echo "<br>";
		echo "<br>";
	
	$message = '<div class="alert alert-info">To be automatically promoted to 5th Year you need to complete all of you courses(including english and sports), otherwise apply.</div>';
	echo $message;
	echo $OUTPUT->single_button($url3,'Apply to 5th Year');
	echo $OUTPUT->single_button($url4,'Stay in 4th year');
	echo $OUTPUT->single_button($urltogo,'Cancel');
}//user is not automatically promoted, and he needs to pass al the courses to be promoted,  but can choose to apply to 5th year or stay in 4th year

else if ( ($pending_main - $in_progress) >3 OR ($pending_gym) >3 OR ($pending_english) >3 ){

	echo "Academic Advance";
	echo "<br>";
	echo "<table><tr><td>Asignatures pending*:</td>$espacios<td>$pending_main</td></tr>
	<tr><td>Asignatures in progress*:</td>$espacios<td>$in_progress</td></tr></table>";
	echo "*not including English or Sports";
	echo "<br>";
		echo "<br>";
		echo "English and Sports";
	echo "<br>";
	echo "<table><tr><td>English Levels Pending:</td>$espacios<td>$pending_english</td></tr>
		<tr><td>Sports Courses Pending:</td>$espacios<td>$pending_gym</td></tr></table>";
		echo "<br>";
		echo "<br>";
	
	$message ='<div class="alert alert-danger">DENIED due to your actual situation, you are not eligible to be promoted to 5th year.</div>';
	echo $message;
	echo $OUTPUT->single_button($urltogo,'Cancel');
	$records->user_id = $USER->id;
	$records->status = 0;
	$DB->insert_record('local_gia', $records); //inserting in database that the user can't apply to 5th year (denied)
}//user denied becuase to many courses pending 

}
}
echo $OUTPUT->footer (); // print footer