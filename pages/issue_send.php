<?php
/*
require_once( 'core.php' );
require_once( 'bug_api.php' );
require_once( 'email_api.php' );
*/
require_once( config_get( 'plugin_path' ) . 'Forward' . DIRECTORY_SEPARATOR . 'pages' .DIRECTORY_SEPARATOR .'api.php' );

form_security_validate( 'forward_issue' );
$f_bug_id	= gpc_get_int( 'bug_id' );
$f_to		= gpc_get_string( 'forward_address' );
$f_body		= gpc_get_string( 'body' );
$f_body 	.= "\n\n";

// Split for multiple adresses. 
$eaddress = explode(";",$f_to);

foreach ($eaddress as $adres){
	
$f_body_note  = 'Forwarded issue to:'. $f_to . "\r\n";
$f_body_note .= gpc_get_string( 'body' );

bug_ensure_exists( $f_bug_id );
$bug = bug_get( $f_bug_id, true );

// Add summary
$f_body		.= bug_format_summary( $f_bug_id, SUMMARY_CAPTION );
$f_body 	.= "\n\n";

// Add description
$tpl_description =  $bug->description ;
$f_body		.= lang_get( 'description' ) ;
$f_body		.= " : ";
$f_body		.= $tpl_description ;
$f_body 	.= "\n\n";

// Add due date
$tpl_due_date = date( config_get( 'normal_date_format' ), $bug->due_date );
$f_body		.= lang_get( 'due_date' );
$f_body		.= " : ";
$f_body		.= $tpl_due_date ;
$f_body 	.= "\n\n";

// Steps to reproduce
$tpl_steps_to_reproduce = $bug->steps_to_reproduce;
$f_body		.=  lang_get( 'steps_to_reproduce' );
$f_body		.= " : ";
$f_body		.= $tpl_steps_to_reproduce ;
$f_body 	.= "\n\n";

// additional indo
$tpl_additional_information = $bug->additional_information ;
$f_body		.=  lang_get( 'additional_information' );
$f_body		.= " : ";
$f_body		.= $tpl_additional_information ;
$f_body 	.= "\n\n";

$t_subject = email_build_subject( $f_bug_id );
$t_date = date( config_get( 'normal_date_format' ) );
$t_sender_id = auth_get_current_user_id();
$t_sender = user_get_name( $t_sender_id );

$t_header = "\n" . lang_get( 'on_date' ) . " $t_date, $t_sender $t_sender_email " . lang_get( 'plugin_forward_issue' ) . ": \n\n";
$t_contents = $t_header .  " \n\n$f_body";
$t_ok = email_store( $adres, $t_subject, $t_contents );
}

// Saving the forwarded issue as note
$savenote 	= savenote_query( $f_bug_id, $t_sender_id, $f_body_note);
print_header_redirect( 'view.php?id='.$f_bug_id.'' );