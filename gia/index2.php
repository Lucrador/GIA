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

global $DB, $USER, $CFG;

require_login (); // Requiere estar log in

$urltogo = $CFG->wwwroot.'/my/index.php';
$baseurl = new moodle_url ( '/local/gia/index.php' ); // importante para crear la clase pagina
$urltogo = $CFG->wwwroot.'/my/index.php';
$context = context_system::instance (); // context_system::instance();
$PAGE->set_context ( $context );
$PAGE->set_url ( $baseurl );
$PAGE->set_pagelayout ( 'standard' );

$title = 'FIFTH YEAR CIVIL ENGINEER 2015';

//$PAGE->set_title($title);
$PAGE->set_heading($title);

$fifth_year_status_not = $DB->get_record_sql("SELECT *
		FROM {local_gia} lg
		WHERE lg.user_id = '$USER->id' ");


echo $OUTPUT->header (); // Imprime el header
echo $OUTPUT->heading ($title);
$message1 = "Read, print and sign the '2015 5th year admission form.pdf' located in the course page.";
$message2 = "Please make sure to upload the file with these format: Lastname, Firstname.pdf. Otherwise, it won't be revised.";
$message3 = "You can upload a scan of the signed document or deliver it to the secretary in D buliding.";
echo $message1;
echo "<br>";
echo "<br>";
echo $message2;
echo "<br>";
echo "<br>";
echo $message3;

echo "<br>";
echo "<br>";

$mform = new filepicker();

if($mform->is_cancelled()){
	redirect($urltogo);
}
else if ($fromform = $mform->get_data()){
$to_success_page = $CFG->wwwroot.'/local/gia/success.php'; 
$name = $mform->get_new_filename('userfile');
$firstname = $USER->firstname;
$lastname = $USER->lastname;
$success = $mform->save_file('userfile', "C:/xampp/moodledata/1/$lastname,$firstname.pdf", $override);
redirect($to_success_page);

}

else {
	$mform->set_data($toform);
	$mform->display();
}
echo $OUTPUT->footer (); // imprime el footer