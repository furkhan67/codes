<?php
/**
 *    This file is part of the Zamp PHP Framework.
 *    (c) Mathan kumar R <phpmathan@gmail.com>
 *
 *    For the full copyright and license information, please view the LICENSE
 *    file that was distributed with this source code.
 *
 * @package Zamp_Mailer
 */

/**
 *    This package using phpmailer to send email messages.
 *
 * @category Zamp
 * @package Zamp_Mailer
 * @author Mathan kumar R <phpmathan@gmail.com>
 * @version SVN: $Id: Mailer.php 377 2018-03-09 06:17:39Z phpmathan $
 */
class Zamp_Mailer {
	/**
	 *    Method used for sending emails using phpmailer class
	 *
	 * @access public
	 * @param object|array $settings
	 *        <ul>
	 *            <li>bool use_smtp - if you want to send mail through SMTP then set to true</li>
	 *            <li>string smtp_host - SMTP hostname</li>
	 *            <li>string smtp_user - SMTP username</li>
	 *            <li>string smtp_pass - SMTP password</li>
	 *            <li>integer smtp_port - SMTP port</li>
	 *            <li>string smtp_ssl - ssl or tls or empty string</li>
	 *            <li>string report_id - string to insert in headers for reporting
	 *            <li>array from_address - From email address</li>
	 *            <li>array to_address - To email address</li>
	 *            <li>array cc_email - CC email address</li>
	 *            <li>array bcc_email - BCC email address</li>
	 *            <li>array reply_to_email - Reply to email address</li>
	 *            <li>array attach - file attachements</li>
	 *            <li>string subject - Subject of the message</li>
	 *            <li>string message - Mail message</li>
	 *            <li>array functions - list of functions with its arguments to call</li>
	 *        </ul>
	 *
	 *    Example use:
	 *
	 *    <code>
	 *    $mailSettings = new stdClass;
	 *    $mailSettings->from_address = [
	 *        'name' => 'Mathan',
	 *        'email' => 'phpmathan@gmail.com',
	 *    ];
	 *    $mailSettings->to_address = [
	 *        'toperson@somedomain.com' => 'Person Name',
	 *    ];
	 *    $mailSettings->subject = 'mail subject';
	 *    $mailSettings->message = 'Hi, This is message body';
	 *    $mailSettings->attach = [
	 *        0 => [
	 *            0 => 'myphoto.jpeg', // filename
	 *            1 => 'My Photo' // title for the file
	 *        ],
	 *        1 => [
	 *            0 => 'somefile.zip',
	 *            1 => 'Zip File'
	 *        ]
	 *     ];
	 *     $mailSettings->functions = [
	 *        [
	 *            'addCustomHeader' => ['Precedence: list'],
	 *        ],
	 *        [
	 *            'addCustomHeader' => ['Errors-To: <bounces@mydomain.com>'],
	 *        ],
	 *     ];
	 *
	 *     $mail = Zamp::getInstance('Zamp_Mailer');
	 *     $response = $mail->sendMail($mailSettings);
	 *    </code>
	 * @return array
	 */
	public function sendMail($settings, $convertToArray = true) {
		// converting $settings into array
		if((array) $settings === $settings || (object) $settings === $settings) {
			if($convertToArray) {
				$settings = Zamp_General::array2Object($settings);
				$settings = Zamp_General::object2Array($settings);
			}
		}
		else
			throw new Zamp_Exception("sendMail Settings Should be Object or Array!", 23);
		
		$vendors = Zamp::getVendorsPath();
		
		if(!isset($vendors['PHPMailer']))
			Zamp::setVendorPaths(_PROJECT_PATH_.'Zamp/3rdparty/', 'PHPMailer');
		
		$mail = new PHPMailer\PHPMailer\PHPMailer(true);
		
		try {
			if(!empty($settings['use_smtp']) && !empty($settings['smtp_host'])) {
				$mail->isSMTP();
				
				if(!empty($settings['smtp_user'])) {
					$mail->SMTPAuth = true;
					$mail->Username = $settings['smtp_user'];
					$mail->Password = $settings['smtp_pass'];
				}
				
				$mail->Host = $settings['smtp_host'];
				$mail->Port = $settings['smtp_port'];
				
				if(isset($settings['smtp_ssl']))
					$mail->SMTPSecure = $settings['smtp_ssl'];
				
				$mailed_by = $settings['smtp_host'].'( '.$settings['smtp_user'].' )';
			}
			else
				$mailed_by = 'PHP mail() function';
			
			// Applying custom properties to phpmailer
			if(!empty($settings['custom'])) {
				foreach($settings['custom'] as $k => $v)
					$mail->$k = $v;
			}
			
			$mail->setFrom($settings['from_address']['email'], $settings['from_address']['name']);
			
			$total_emails = 0;

			// Adding To address
			if(!empty($settings['to_address'])) {
				foreach($settings['to_address'] as $email => $name) {
					$mail->addAddress($email, $name);
					$total_emails++;
				}
			}
			else
				throw new Zamp_Exception("Enter Email <font color=red>To Address</font> !");

			// Adding CC
			if(!empty($settings['cc_email'])) {
				foreach($settings['cc_email'] as $email => $name) {
					$mail->addCC($email, $name);
					$total_emails++;
				}
			}
			elseif(isset($settings['cc_email']))
				throw new Zamp_Exception("Email cc_email must be array or object.");
			
			// Adding BCC
			if(!empty($settings['bcc_email'])) {
				foreach($settings['bcc_email'] as $email => $name) {
					$mail->addBCC($email, $name);
					$total_emails++;
				}
			}
			elseif(isset($settings['bcc_email']))
				throw new Zamp_Exception("Email bcc_email must be array or object.");
			
			// Adding replay to email
			if(!empty($settings['reply_to_email'])) {
				$mail->clearReplyTos();
				
				foreach($settings['reply_to_email'] as $email => $name)
					$mail->addReplyTo($email, $name);
			}
			elseif(isset($settings['reply_to_email']))
				throw new Zamp_Exception("Email reply_to_email must be array or object.");
			
			// Adding Attachements
			if(!empty($settings['attach']) && (array) $settings['attach'] === $settings['attach']) {
				foreach($settings['attach'] as $fileInfo) {
					if(is_file($fileInfo[0]))
						$mail->addAttachment($fileInfo[0], $fileInfo[1]);
					else
						throw new Zamp_Exception("Email Attachement file (<font color=red>{$fileInfo[0]}</font>) not found!");
				}
			}
			
			$mail->Subject = (!empty($settings['subject'])) ?$settings['subject'] :'(No Subject)';
			$mail->AltBody = (!empty($settings['AltBody'])) ?$settings['AltBody'] :'To view the message, please use an HTML compatible email viewer!';
			
			if($settings['message'])
				$mail->MsgHTML($settings['message']);
			else
				throw new Zamp_Exception("Email <font color=red>Message</font> is empty!");

			// Applying custom function calls
			if(!empty($settings['functions']) && (array) $settings['functions'] === $settings['functions']) {
				foreach($settings['functions'] as $funcInfo) {
					foreach($funcInfo as $method => $params)
						call_user_func_array([
							$mail,
							$method
						], $params);
				}
			}
			
			if(!empty($settings['report_id']))
				$mail->addCustomHeader('X-Report-Id', $settings['report_id']);

			if($mail->Send()) {
				$mailStatus = 'S';
				$mailMessage = 'ok';
			}
			else {
				$mailStatus = 'F';
				$mailMessage = 'Unknown Error Occured!';
			}

			$mail->clearAllRecipients();
			$mail->clearAttachments();
			$mail->smtpClose();
		}
		catch(phpmailerException $e) {
			$mailStatus = 'F';
			$mailMessage = $e->errorMessage();
		}

		unset($mail, $settings);

		return [
			'return_msg' => $mailMessage,
			'status' => $mailStatus,
			'total_emails' => $total_emails
		];
	}
}
/** End of File **/
