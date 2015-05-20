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

$PAGE->set_heading($title);

echo $OUTPUT->header (); // Imprime el header
echo $OUTPUT->heading ($title);

$message4 = "Your file has been submitted succesfully. ";
echo $message4;
echo "<br>";
echo "<br>";
echo $OUTPUT->single_button($urltogo, 'Return');

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

echo $OUTPUT->footer (); // imprime el footer