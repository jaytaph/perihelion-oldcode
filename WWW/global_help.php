<?php

// ============================================================================================
// Help_Link ();
//
// Description:
//   Returns a link to a help-screen for a particulair topic.
//
// Parameters:
//   Help Topic (should be available in the mysql database perihelion.help)
//
// Returns:
//   HTML-string with the help topic
//
function help_link ($help_topic) {
	assert (is_string ($help_topic));
 
  return "[ <a href='help.php?hid=".encrypt_get_vars ($help_topic)."' onclick='targetBlank(this.href);return false;'><b>?</b></a> ]";
}

// ============================================================================================
// Help_Set_Template_Vars
//
// Description:
//   Creates the helplink_* vars from all the help-topics from the database perihelion.help. Usefull so
//   webdesigners can use them at will in the templates.
//
// Parameters:
//   $template   handle to a Yapter-template object
//
// Returns:
//   nothing (but sets the helplink_* vars in the object)
//
function help_set_template_vars (&$template, $prefix = "%") {
	// NOOOTIENOOOT: You see this is a REFERENCE to the object, not the object itself, since we still use PHP4.0 :(
	assert (is_object ($template));
	
	$result = sql_query ("SELECT * FROM perihelion.help WHERE id like '".$prefix."_%'");
	while ($row = sql_fetchrow ($result)) {
    $template->assign ("help_".strtolower($row['id']), help_link ($row['id']));
  }
}

?>