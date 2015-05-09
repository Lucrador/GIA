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
 * @subpackage ayudantia 
 * @copyright  2015-Tics331
 * 				Francisco García Ralph (francisco.garcia.ralph@gmail.com)			
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once (dirname ( __FILE__ ) . '/../../config.php');

global $DB, $USER, $CFG;

require_login (); // Requiere estar log in

$baseurl = new moodle_url ( '/local/ayudantia/index.php' ); // importante para crear la clase pagina
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

$apellido = $DB->get_record_sql("SELECT u.lastname as 'lastname'
		FROM {user} u 
		WHERE u.id = :userid",$params);
$nombre = $DB-> get_record_sql("SELECT u.firstname as 'name'
		FROM {user} u
		WHERE u.id = :userid", $params);
$status = $DB-> get_record_sql("SELECT u.user_status as 'status'
		FROM {user} u
		WHERE u.id = :userid", $params);
$date = $DB-> get_record_sql("SELECT  u.adm_date as 'date'
		FROM {user} u
		WHERE u.id = :userid",$params);
$plan = $DB-> get_record_sql("SELECT u.study_plan as 'plan'
		FROM {user} u
		WHERE u.id = :userid", $params);
$version = $DB->get_record_sql("SELECT u.plan_version as 'version'
		FROM {user} u
		WHERE u.id = :userid", $params);

$datetime = date_create($date->date)->format('d-m-Y');
$espacios = '<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>';
if($status->status == '1'){
	$realstatus = 'Active';
}

else {
	$realstatus = "Not Active";
}

echo "<table><tr><td>Student Name:       </td>$espacios<td>$apellido->lastname, $nombre->name </td></tr>
             <tr><td>State:              </td>$espacios<td>$realstatus                        </td></tr>
             <tr><td>Admission Date:     </td>$espacios<td>$datetime                          </td></tr>  
             <tr><td>Study Plan:         </td>$espacios<td>$plan->plan                        </td></tr>
             <tr><td>Study Plan Version: </td>$espacios<td>$version->version                  </td></tr></table>  "; 

echo "<br>";
echo "<br>";
echo "<br>";
echo "<br>";

if(isset($version)){
	echo "Congratulations! You are going to be automatically promoted to 5th year";
}

echo $OUTPUT->footer (); // imprime el footer