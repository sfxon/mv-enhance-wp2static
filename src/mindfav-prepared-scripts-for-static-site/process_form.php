<?php

//Check Google Re-Captcha
if(!isset($_POST['mvtok'])) {
		// Output to show success message, no matter if it was successful or not..
		end_processing();
		die;
}

$mvtok = $_POST['mvtok'];

$secret = '';	//Google - Private Secret!!!
$url = 'https:/www.google.com/recaptcha/api/siteverify?secret=' . $secret . '&response=' . $mvtok;

$request = verifyReCaptcha($mvtok, $secret);

if(!isset($request->success)) {
		end_processing();
		die;
}

$mverr = true;		//Wir gehen zunächst davon aus, dass die Anfrage Fehler enthält. Nur wenn sie korrekt war, wird das auf true gesetzt..

if($request->success == true) {
		if($request->score >= 0.6) {
				$mverr = false;
		}
}

//echo '1';

if($mverr == true) {
		// Output to show success message, no matter if it was successful or not..
		end_processing();
		die;
}

$form_id = $_POST['_wpcf7'];

//Check form in database, get all form fields..
define( 'DB_NAME', '' );
define( 'DB_USER', '' );
define( 'DB_PASSWORD', '' );
define( 'DB_HOST', '' );

//echo '2';

$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$expected_form_data = mv_get_expected_form_data($link, $form_id);

//echo '3';

$is_valid = mv_check_posted_form_data($expected_form_data, $_POST);

//echo '4';

if($is_valid) {
		mv_send_mail($expected_form_data, $link, $form_id);	
}

//echo '5';

// Output to show success message, no matter if it was successful or not..
end_processing();
die;

//////////////////////////////////////////////////////////////////////////////////////////////
// Google Recaptcha
//////////////////////////////////////////////////////////////////////////////////////////////
function verifyReCaptcha($recaptchaCode, $secret){
    $curl = curl_init("https://www.google.com/recaptcha/api/siteverify");
    $postdata = http_build_query(array("secret"=>$secret,"response"=>$recaptchaCode));

		$verify = curl_init();
		curl_setopt($verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
		curl_setopt($verify, CURLOPT_POST, true);
		curl_setopt($verify, CURLOPT_POSTFIELDS, $postdata);
		curl_setopt($verify, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($verify);
		
    curl_close($curl);
		
    $check = json_decode($response);
    return $check;
}

//////////////////////////////////////////////////////////////////////////////////////////////
// Output to show success message, no matter if it was successful or not..
//////////////////////////////////////////////////////////////////////////////////////////////
function end_processing() {
		$result = array(
				'into' => '#' . $_POST['_wpcf7_unit_tag'],
				'status' => 'mail_sent',
				'message' => 'Vielen Dank für Ihre Anfrage. Wir werden uns in Kürze mit Ihnen in Verbindung setzen.'
		);
		$result = json_encode($result);
		
		echo $result;
}

//////////////////////////////////////////////////////////////////////////////////////////////
// Formular Felder von Datenbank abrufen und zurückgeben.
//////////////////////////////////////////////////////////////////////////////////////////////
function mv_send_mail($expected_form_data, $link, $form_id) {
		$mail_text = mv_get_mail_data($link, $form_id);
		
		$mail = unserialize($mail_text['meta_value']);
		
		$subject = $mail['subject'];
		$mail_text = utf8_decode($mail['body']);
		$mail_to = '';		//for example: steve@mindfav.com

		foreach($expected_form_data as $field) {
				$fieldname = $field['field_name'];
				
				$mail_text = str_replace('[' . $fieldname . ']', $_POST[$fieldname], $mail_text);
		}
		
		mail($mail_to, $subject, $mail_text);
}

//////////////////////////////////////////////////////////////////////////////////////////////
// Formular Felder von Datenbank abrufen und zurückgeben.
//////////////////////////////////////////////////////////////////////////////////////////////
function mv_check_posted_form_data($expected_form_data, $post) {
		foreach($expected_form_data as $field) {
				$fieldname = $field['field_name'];
				
				if(!isset($post[$fieldname])) {
						return false;
				}
		}
		
		return true;
}

//////////////////////////////////////////////////////////////////////////////////////////////
// Formular Felder von Datenbank abrufen und zurückgeben.
//////////////////////////////////////////////////////////////////////////////////////////////
function mv_get_expected_form_data($link, $form_id) {
		$query = 'SELECT meta_value FROM wp_postmeta WHERE post_id = ' . (int)$form_id  . ' AND meta_key = \'_form\'';
		$result = mysqli_query($link, $query);
		
		while ($row = mysqli_fetch_assoc($result)) {
				if(isset($row['meta_value'])) {
						$fields = mv_parse_fields_from_post_content($row['meta_value']);
						
						//Prüfen, ob wir die Formularfelder erfolgreich geparst haben..
						if(isset($fields[0]) && is_array($fields[0]) && count($fields[0]) > 0) {
								$field_names_array = mv_get_fieldnames_from_strings($fields[0]);
								return $field_names_array;
						}
				}
		}
		
		return false;
}

//////////////////////////////////////////////////////////////////////////////////////////////
// Formular Felder von Datenbank abrufen und zurückgeben.
//////////////////////////////////////////////////////////////////////////////////////////////
function mv_get_mail_data($link, $form_id) {
		$query = 'SELECT meta_value FROM wp_postmeta WHERE post_id = ' . (int)$form_id  . ' AND meta_key = \'_mail\'';
		$result = mysqli_query($link, $query);
		
		while ($row = mysqli_fetch_assoc($result)) {
				if(isset($row['meta_value'])) {
						return $row;
				}
		}
		
		return false;
}

//////////////////////////////////////////////////////////////////////////////////////////////
// Formularfelder aus String heraus parsen..
//////////////////////////////////////////////////////////////////////////////////////////////
function mv_parse_fields_from_post_content($post_content) {
		$output_array = array();
		$fields = preg_match_all('/\[(.*?)\]/', $post_content, $output_array);
		
		return $output_array;
}

//////////////////////////////////////////////////////////////////////////////////////////////
// Formularfelder aus String heraus parsen..
//////////////////////////////////////////////////////////////////////////////////////////////
function mv_get_fieldnames_from_strings($fields) {
		$output_array = array();
		
		foreach($fields as $field) {
				$field = str_replace('[', '', $field);
				$field = str_replace(']', '', $field);
				
				$field = explode(' ', $field);
				
				if(isset($field[1])) {
						//Here you could also add more fields to be parsed!
						$output_array[] = array(
								'field_name' => $field[1]
						);
				}
		}
		
		return $output_array;
}

echo 'nonono';
die;