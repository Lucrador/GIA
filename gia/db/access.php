<?php
$capabilities = array(

    'local/gia:viewstudent' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => array(
        	'student'=> CAP_ALLOW,
        	'teacher' => CAP_PROHIBIT,
            'editingteacher' => CAP_PROHIBIT,
            'manager' => CAP_PROHIBIT
            )),	
		
	'local/gia:viewadmin' => array(
			'captype' => 'read',
			'contextlevel' => CONTEXT_COURSE,
			'archetypes' => array(
					'teacher' => CAP_ALLOW,
					'editingteacher' => CAP_ALLOW,
					'manager' => CAP_ALLOW,
					'student'=> CAP_PROHIBIT
			))
);
?>	