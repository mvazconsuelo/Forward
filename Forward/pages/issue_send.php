<?php
require_once( 'core.php' );
require_once( 'bug_api.php' );
require_once( 'email_api.php' );
form_security_validate( 'forward_issue' );
$f_bug_id	= gpc_get_int( 'bug_id' );
$f_to		= gpc_get_string( 'forward_address' );
$f_body		= gpc_get_string( 'body' );
$f_body 	.= "\n\n";

bug_ensure_exists( $f_bug_id );
$bug = bug_get( $f_bug_id, true ); 

// Add summary
$f_body		.= bug_format_summary( $f_bug_id, SUMMARY_CAPTION );
$f_body 	.= "\n\n";

// Add description
$tpl_description = string_display_links( $bug->description );
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
$tpl_steps_to_reproduce = string_display_links( $bug->steps_to_reproduce );
$f_body		.=  lang_get( 'steps_to_reproduce' );
$f_body		.= " : ";
$f_body		.= $tpl_steps_to_reproduce ;
$f_body 	.= "\n\n";

// additional indo		
$tpl_additional_information = string_display_links( $bug->additional_information ); 
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
$t_ok = email_store( $f_to, $t_subject, $t_contents );

form_security_purge( 'forward_issue' );
html_page_top( null, string_get_bug_view_url( $f_bug_id ) );
?>
<br />
<div align="center">
<?php
echo lang_get( 'operation_successful' ).'<br />';
print_bracket_link( string_get_bug_view_url( $f_bug_id ), lang_get( 'proceed' ) );
?>
</div>
<?php
html_page_bottom();


