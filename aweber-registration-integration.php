<?php
/*
Plugin Name: AWeber Registration Integration
Plugin URI: http://www.gkauten.com/aweber-registration-integration
Description: Integrates the AWeber contact registration script into your WordPress registration process. Users are seamlessly added to your AWeber account during registration on your site, either by request or silently. If you do not yet have an AWeber account, you will need to <a href='http://www.aweber.com/?323919'>go here</a> and sign up for one.
Version: 1.2.8
Author: GKauten
Author URI: http://www.gkauten.com

Special thanks to Guru Consulting Services, Inc. (http://www.gurucs.com) for their original platform on which some of these functions derive.

Copyright (c) 2009 - GKauten (www.GKauten.com)
  
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/************************************/
/* Plugin Activation                */
/************************************/

function aweber_registration_activate() {
  global $wpdb, $installed;
  $result = $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}aweber_registrations(`email` VARCHAR(255) NOT NULL, PRIMARY KEY (`email`))");
  if(!$result === false) {
    // Schema Created
	add_option("awr_installed", "true", "", true);
  } else {
	// Schema Failed
	add_option("awr_installed", "false", "", true);
  }
}
register_activation_hook(__FILE__, 'aweber_registration_activate');

/************************************/
/* Plugin Deactivation              */
/************************************/

function aweber_registration_deactivate() {
  global $wpdb;
  $result = $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}aweber_registrations");
  if($result) {
    // Schema Deleted
	delete_option("awr_installed");
	delete_option("awr_form_id");
	delete_option("awr_list_name");
	delete_option("awr_disabled");
	delete_option("awr_opt_in");
	
	// v1.2.6 - Check for old awr_unit_name and remove
	if(get_awr_option("awr_unit_name") !== false) {
	  delete_option("awr_unit_name");
	}

  }
}
register_deactivation_hook(__FILE__, 'aweber_registration_deactivate');

/************************************/
/* Plugin Init Load                 */
/************************************/

function aweber_registration_load() {
  global $optin;
  // Confirm Installation Before Load
  if(get_awr_option("awr_installed") !== false && get_awr_option("awr_installed") != "error" && get_awr_option("awr_installed") != "false") {
    // Install Options if not present
    if(get_awr_option("awr_form_id") === false) {
	  add_option("awr_form_id", "", "", true);
	}
	if(get_awr_option("awr_list_name") === false) {
	  add_option("awr_list_name", "", "", true);
	}
	if(get_awr_option("awr_disabled") === false) {
	  add_option("awr_disabled", "false", "", true);
	}
	if(get_awr_option("awr_opt_in") === false) {
	  add_option("awr_opt_in", "true", "", true);
	}
  }
  
  // v1.2.6 - Check for awr_unit_name and import to awr_list_name
  if(get_awr_option("awr_unit_name") != "" & get_awr_option("awr_list_name") == "") {
    update_option("awr_list_name", get_awr_option("awr_unit_name"));
  }
  
  // Define Optin
  $optin = false;
}
add_action("admin_init", "aweber_registration_load");

/************************************/
/* Administration Menu              */
/************************************/

function aweber_registration_admin_init() {
  add_submenu_page("options-general.php", "AWeber Registration Integration", "AWeber Integration", 8, __FILE__, "aweber_registration_admin_page");
}
add_action("admin_menu", "aweber_registration_admin_init");

function aweber_registration_admin_page() {
  global $wpdb;
   
  // Credentials Form Submit
  if(isset($_POST["AWeber_Credentials"])) {
    // Update Settings
	if(isset($_POST["awr_form_id"]) && $_POST["awr_form_id"] != "") {
      update_option("awr_form_id", $_POST["awr_form_id"]);
	} else {
	  update_option("awr_form_id", "");
	}
	  
	if(isset($_POST["awr_list_name"]) && $_POST["awr_list_name"] != "") {
      update_option("awr_list_name", $_POST["awr_list_name"]);
	} else {
	  update_option("awr_list_name", "");
	}
	  
	$message = "Your information has been saved!";
  }
	
  // Settings Form Submit
  if(isset($_POST["AWeber_Settings"])) {
    // Update Settings
	if(isset($_POST["awr_disabled"]) && $_POST["awr_disabled"] != "") {
      update_option("awr_disabled", "true");
	} else {
	  update_option("awr_disabled", "false");
	}
	  
	if(isset($_POST["awr_opt_in"]) && $_POST["awr_opt_in"] != "") {
      update_option("awr_opt_in", "true");
	} else {
	  update_option("awr_opt_in", "false");
	}
	  
	$message = "Your settings have been saved!";
  }
  ?>
    
  <?php if ($message) : ?>
    <div id='message' class='updated fade'><p><?php echo $message; ?></p></div>
  <?php endif; ?>
  
  <div id="dropmessage" class="updated" style="display:none;"></div>
  <div class="wrap">
    <h2><?php _e("AWeber Registration Integration", "aweber_registration"); ?></h2>
    <form method="post" name="aweber_registration_integration" target="_self">
      <p>In order to integrate the <a href="http://www.aweber.com/?323919">AWeber</a> registration form with the WordPress registration process you need to enter in the information below. This information can be found in the HTML form code provided to you in your <a href="http://www.aweber.com/?323919">AWeber</a> control panel. After logging in to your account, select Web Forms from the primary navigation followed by Get HTML for the form you intend to use. Select Raw HTML Version from the resulting popup window and retrieve the required values as shown below.</p>
      <table class="form-table" style="width: 400px; margin-left: 25px;">
        <tr>
          <td style="width: 100px;" valign="top"><?php _e("Form ID:", "aweber_registration"); ?></td>
          <td style="width: 300px;"><input type="text" name="awr_form_id" style="width: 250px;" value="<?php if (get_awr_option("awr_form_id")) echo get_awr_option("awr_form_id"); ?>" /><br /><i>(From HTML Code: name="meta_web_form_id")</i></td>
        </tr>
        <tr>
          <td valign="top"><?php _e("List Name:", "aweber_registration"); ?></td>
          <td><input type="text" name="awr_list_name" style="width: 250px;" value="<?php if (get_awr_option("awr_list_name")) echo get_awr_option("awr_list_name"); ?>" /><br /><i>(From HTML Code: name="listname")</i></td>
        </tr>
        <tr>
          <td colspan="2"><input type="submit" name="AWeber_Credentials" value="<?php _e("Save Information", "aweber_registration")?> &raquo;" /></td>
        </tr>
      </table>
    </form>
    <h2><?php _e("Settings", "aweber_registration"); ?></h2>
    <form method="post" name="aweber_registration_settings" target="_self">
      <p>Use the settings below to control additional aspects of how the <a href="http://www.aweber.com/?323919">AWeber</a> registration process is integrated with your WordPress installation.</p>
      <table class="form-table" style="width: 600px; margin-left: 25px;">
        <tr>
          <td style="width: 150px;" valign="top"><?php _e("Disable Integration?", "aweber_registration"); ?></td>
          <td style="width: 25px;" valign="top"><input type="checkbox" name="awr_disabled" <?php if (get_awr_option("awr_disabled") == "true") echo "checked=\"checked\" "; ?>/></td>
          <td style="width: 425px;"><i>Removes the integration from the WordPress registration process without having to disable the plugin. Useful in the event of cross-plugin complications.</i></td>
        </tr>
        <tr>
          <td valign="top"><?php _e("Display Opt-In?", "aweber_registration"); ?></td>
          <td valign="top"><input type="checkbox" name="awr_opt_in" <?php if (get_awr_option("awr_opt_in") == "true") echo "checked=\"checked\" "; ?>/></td>
          <td><i>Places a checkbox on the registration form allowing the user to decide whether or not to sign up.</i></td>
        </tr>
        <tr>
          <td colspan="2"><input type="submit" name="AWeber_Settings" value="<?php _e("Save Settings", "aweber_registration")?> &raquo;" /></td>
        </tr>
      </table>
    </form> 
    <h2><?php _e("Registered Users", "aweber_registration"); ?></h2>
    <p>This is a list of the registered users on your site who also requested to be signed up with AWeber. Please keep in mind that the list here may not reflect the list in your AWeber control panel since users must also confirm their intent to register in a separate email sent by AWeber.</p>
    <table class="widefat fixed" cellspacing="0">
      <thead>
        <tr class="thead">
	      <th scope="col" id="username" class="manage-column column-username" style="">Username</th>
	      <th scope="col" id="name" class="manage-column column-name" style="">Display Name</th>
	      <th scope="col" id="email" class="manage-column column-email" style="">E-mail</th>
          <th scope="col" id="registered" class="manage-column column-name" style="">Registered On</th>
        </tr>
      </thead>
      <tbody id="users" class="list:user user-list">
        <?php
		  $users = $wpdb->get_results("SELECT $wpdb->users.user_login, $wpdb->users.display_name, $wpdb->users.user_email, $wpdb->users.user_registered FROM $wpdb->users, {$wpdb->prefix}aweber_registrations WHERE $wpdb->users.user_email = {$wpdb->prefix}aweber_registrations.email");
		  if($users) :
		    foreach($users as $user) { ?>
			  <tr>
                <td class="username column-username"><strong><?php echo $user->user_login; ?></strong></td>
                <td class="name column-name"><?php echo $user->display_name; ?></td>
                <td class="email column-email"><a href='mailto:<?php echo $user->user_email; ?>' title='e-mail: <?php echo $user->user_email; ?>'><?php echo $user->user_email; ?></a></td>
                <td class="name column-name"><?php echo date("F jS, Y", strtotime($user->user_registered)); ?></td>
              </tr>
			<?php } ?>
		  <?php else: ?>
		    <tr class="alternate">
              <td class="username column-username" colspan="4"><i>There are not currently any registered users.</i></td>
            </tr>
		  <?php endif; ?>
      </tbody>
      <tfoot>
        <tr class="thead">
	      <th scope="col" id="username" class="manage-column column-username" style="">Username</th>
	      <th scope="col" id="name" class="manage-column column-name" style="">Display Name</th>
	      <th scope="col" id="email" class="manage-column column-email" style="">E-mail</th>
          <th scope="col" id="registered" class="manage-column column-name" style="">Registered On</th>
        </tr>
      </tfoot>
    </table>
  </div>
  
<?php } 

/************************************/
/* Safely Retrieve Options          */
/************************************/

function get_awr_option($option = "") {
  if($option) return str_replace("\"", "'", stripcslashes(get_option($option)));
}

/************************************/
/* Check Email Existence            */
/************************************/

function awr_email_exists($email = "") {
  if($email) {
    global $wpdb;
	$awr_email = $wpdb->escape(strtolower($email));
	$exist = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}aweber_registrations WHERE LOWER(email)='$awr_email'");
	if($exist) {
	  return true;
	} else {
	  return false;
	}
  }
}

/************************************/
/* Registration Process Hooks       */
/************************************/

function awr_register($id) {
  global $wpdb, $optin;
  
  // Exit if AWeber is Disabled
  if(get_awr_option("awr_disabled") == "true") return;
  
  // Exit if AWeber Info is Absent
  if(get_awr_option("awr_form_id") == "" || get_awr_option("awr_form_id") === false) return;
  if(get_awr_option("awr_list_name") == "" || get_awr_option("awr_list_name") === false) return;
  
  // Check Opt-In
  if(get_awr_option("awr_opt_in") == "true" && $optin == true) {
    // Opt-In Accepted - No Action Needed
  } elseif(get_awr_option("awr_opt_in") == "true" && $optin == false) {
    // Opt-In Declined
    return;
  }
  
  // Begin AWeber Registration
  $id = intval($id);
  $user = $wpdb->get_row("SELECT * FROM $wpdb->users WHERE ID=$id");
  if(!awr_email_exists($user->user_email)) {
	$params = array(
	  "meta_web_form_id" => get_awr_option("awr_form_id"),
	  "meta_split_id" => "",
	  "unit" => get_awr_option("awr_list_name"),
	  "redirect" => "http://www.aweber.com/form/thankyou_vo.html",
	  "meta_redirect_onlist" => "",
	  "meta_adtracking" => "",
	  "meta_message" => "1",
	  "meta_required" => "name,from",
	  "meta_forward_vars" => "0",
	  "name" => $user->user_nicename,
	  "from" => $user->user_email,
	  "submit" => "Submit"
	);
	$r = _post('http://www.aweber.com/scripts/addlead.pl', $params);
	$result = $wpdb->query("INSERT INTO {$wpdb->prefix}aweber_registrations(email) VALUES('".$user->user_email."')");
  }
}

add_action('user_register', 'awr_register');

function awr_opt_in() {
  if(get_awr_option("awr_opt_in") == "true" && (get_awr_option("awr_disabled") == "" || get_awr_option("awr_disabled") == "false")) { ?>
    <p><input type="checkbox" name="awr_opt_in" id="awr_opt_in" class="input" <?php if(isset($_POST["awr_opt_in"]) && $_POST["awr_opt_in"] != "") { echo("checked=\"checked\""); } ?> tabindex="99"/> 
	&nbsp;&nbsp;Sign me up to receive the newsletter!</p><br class="clear" /><?php 
  }
}

add_action('register_form', 'awr_opt_in', 10, 0);

function awr_check_opt_in($login, $email, $errors) {
  global $optin;
  if(get_awr_option("awr_opt_in") == "true") {
    if(isset($_POST["awr_opt_in"]) && $_POST["awr_opt_in"] != "") {
      // Opted In
	  $optin = true;
	} else {
	  // Opted Out
	  $optin = false;
	}
  }
}

add_action('register_post', 'awr_check_opt_in', 10, 3);
 
/************************************/
/* Post Registration Method         */
/************************************/

function _post($url, $fields) {
  return _request(true, $url, $fields);
}

/************************************/
/* Request Registration Method      */
/************************************/

function _request($post, $url, $fields) {
  $postfields = array();
  if(count($fields)) {
	foreach($fields as $i => $f) {
	  $postfields[] = urlencode($i) . '=' . urlencode($f);
    }
  }
  $fields = implode('&', $postfields);
  return _http($post ? 'POST' : 'GET', $url, $fields);
  $ch = curl_init($url);
  $ck = array();
  $headers = array("User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.11) Gecko/20071127 Firefox/2.0.0.11", "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5", "Accept-Language: en-us,en;q=0.5", "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7", "Keep-Alive: 300", "Connection: keep-alive");
  curl_setopt($ch, CURLOPT_TIMEOUT, 5);
  if($post) {
    curl_setopt($ch, CURLOPT_POST, true);
	$postfields = array();
	if(count($fields)) {
	  foreach($fields as $i => $f) {
		$postfields[] = urlencode($i) . '=' . urlencode($f);
	  }
	}
	$fields = implode('&', $postfields);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    $headers[] = "Content-Type: application/x-www-form-urlencoded";
    $headers[] = "Content-Length: " . strlen($fields);
  }
  curl_setopt($ch, CURLOPT_HEADER, $this->headers);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $res = curl_exec($ch);
  curl_close($ch);
  return $res;
}

/************************************/
/* Registration Process Hooks       */
/************************************/

function _http($method, $url, $data = null) {
  preg_match('~http://([^/]+)(/.*)~', $url, $subs);
  $host = $subs[1];
  $uri = $subs[2];
  $header .= "$method $uri HTTP/1.1\r\n";
  $header .= "Host: $host\r\n";
  $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
  $header .= "Content-Length: " . strlen($data) . "\r\n\r\n";
  $fp = fsockopen ($host, 80, $errno, $errstr, 30);
  if($fp) {
    fputs($fp, $header . $data);
	$result = '';
	while(!feof($fp)) $result .= fgets($fp, 4096);
  }
  fclose($fp);
  return $result;
}

?>