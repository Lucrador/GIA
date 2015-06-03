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
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
//defined('MOODLE_INTERNAL') || die();
//require_once('index.php');

class tabla{
	public function __construct(){
	}
	public static function getInfo($user_info, $courseid){ //using course id as parameter for roles
		global $DB, $OUTPUT, $rut_search, $courseid;
			
		$modifyicon = new pix_icon("t/preview"); //pix icon for the update link
		
		$table = new html_table();
		$table->head = array('Name','Lastname','RUT', 'Status'); //header of table
		foreach($user_info as $result){

			$modifyurl = new moodle_url ('/local/gia/update.php', array('courseid'=> $courseid,'id' => $result->id )); //link to update using course id and user's id as parameter
			
			$modifybutton = $OUTPUT->action_icon($modifyurl, $modifyicon); //converting pix icon in button
			
			if($result->status == '1'){ //if user status = 1 => is promoted, so show 'promoted' instead of 1
				$status = "PROMOTED";
			}
			else if($result->status == '2'){ //if user status = 2 => user sent from, so show 'sent form' instead of 2
				$status = "USER SENT FORM";
			}
			else if($result->status == '0'){ //if user status =  => is denied, so show 'denied' instead of 0
				$status = "DENIED";
			}
			else if($result->status == '3'){ //if user status = 3 => user stays in 4th year, so show 'stays in 4th year' instead of 3
				$status = 'STAYS IN 4TH YEAR';
			}
			else if(empty($result->status)){ //if user status = empty  => no records, so show 'null' instead of nothing
				$status = "NULL";
			}
		if($result->status == '1'){ //if user is promoted, don't show the update option
			$table->data[]= array($result->name,$result->lastname,$result->rut,$status, NULL);
		}	
	    else { //if user is not promoted, you can update his status
		$table->data[]= array($result->name,$result->lastname,$result->rut,$status, $modifybutton);
	    }
	}
	return $table;
	}
}
//table to show user data when searching by RUT

