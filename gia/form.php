
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
 //a

/**
 * 
 * @package    local
 * @subpackage gia
 * @copyright  2015
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__) . '/../../config.php');
require_once("$CFG->libdir/formslib.php");

class filepicker extends moodleform {
	function definition(){
		global $CFG, $DB, $USER, $PAGE;
		
		$mform = $this->_form;
		$mform->addElement('filepicker', 'userfile', 'File Submissions', null, //creating filepicker named 'userfile'
				array('maxbytes' => $maxbytes, 'accepted_types' => '.pdf')); //accepts only pdf's
		$mform->addRule('userfile','You need to upload a document.','required'); //required => not null
		$this->add_action_buttons($cancel = false, $submit = 'Save changes and apply to 5th year'); //Action buttons
	}
}
//form to upload form being student. only accepts pdf's and it's required

class insert extends moodleform {
	function definition() {
		global $CFG, $DB, $USER, $PAGE;
		
		$mform = $this->_form;
		$mform->addElement('text', 'user_rut', "Student's RUT"); //creating text box named 'user_rut'
		$mform->addRule('user_rut','Insert data','required'); //required => not null
		$mform->addRule('user_rut', 'The RUT must be at least 6 characters long.', 'minlength', 6); //rut minimum length is 6 characters
		$mform->setType('user_rut', PARAM_INT); //must be int = security
		echo "The RUT must be without points, and without codific number";
		echo "<br>";
		echo "<br>";
		$mform->addElement('filepicker', 'users_file', 'Submit Form', null, //creating filepicker name 'users_file'
				array('maxbytes' => $maxbytes, 'accepted_types' => '.pdf')); //only accepting pdf's
		$mform->addRule('users_file','You need to upload a document.','required'); //required => not null
		$this->add_action_buttons($cancel = false, $submit = 'Save changes'); //acion buttons
		}
//form to insert form for a student. only accepts pdf and the text and the filepicker are required		
		function validation($data, $files) {
			global $DB; //validation
				
			$request = $DB->get_records_sql("SELECT ui.user_id
					FROM {user_info} ui
					JOIN {local_gia} lg
					ON lg.user_id = ui.user_id
					WHERE (lg.status not LIKE '1') AND (ui.user_rut = $data[user_rut])"); //searching id from rut where user isn't promoted 
			
			$request2 = $DB->get_records_sql("SELECT ui.user_id
					FROM {user_info} ui 
					WHERE ui.user_rut = $data[user_rut]"); //searching id from rut
			
			$errors=array();
			
			if($request == false){ //if false, means user doesn't have records on 5th year status, meaning we use $request2
				if($request2 == false && !empty($data['user_rut'])){ //if $request2 doesn't exist, or rut typed is 0
					$errors["user_rut"]= "The RUT that you inserted does not exist, or user has already been promoted. Please try again.";
					return $errors;
				}
			}
			else { 
			if($request == false && !empty($data['user_rut'])){ //if $request doesn't exist, or rut typed is 0
				$errors["user_rut"]= "The RUT that you inserted does not exist, or user has already been promoted. Please try again.";
				return $errors;
			}
			}
			return array();
		}
}

class searchform extends moodleform { 
	function definition(){
		global $CFG, $DB, $USER, $PAGE;
		
		$mform = $this->_form;
		$mform->addElement('text', 'rut_search', "Student's RUT"); //creating text box
		$mform->addRule('rut_search', 'Insert data', 'required'); //required => not null
		$mform->addRule('rut_search', 'The RUT must be at least 6 characters longo.', 'minlength', 6); //rut minglength is 6 characters
		$mform->addRule('rut_search', PARAM_INT); //setting int for security
		$this->add_action_buttons($cancel = false , $submit = 'Search'); //action buttons	
	}
	
	function validation($data, $files) {
		global $DB; //validation
			
		$search = $DB->get_record('user_info',array('user_rut'=>$data['rut_search'])); //searching if rut exists
		$errors=array();
		if($search == false && !empty($data['rut_search'])){
			$errors["rut_search"]= "The RUT that you inserted does not exist. Please try again."; //if rut doesn't exist, or if its 0 => error
			return $errors;
		}
		return array();
	}
}
//form to search user 

class update_state extends moodleform{
	function definition(){
		global $CFG, $DB, $USER, $PAGE;
		
		$mform = $this->_form;
		$mform->addElement('select', 'status', 'Change user status', array('Approve','Deny')); //creating a select 
		$mform->addRule('status', 'Choose an option please.', 'required'); //required => obvious
		$this->add_action_buttons($cancel = false, $submit = 'Update'); //action buttons
	}
}
//form to update status of user