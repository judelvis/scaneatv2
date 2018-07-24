<?php

class Email {
	var $from;
	var $password;
	var $to;
	var $subject;
	var $message;

	static function SendValidateMail($db, $user_mail) {
		$to  = utf8_decode($user_mail);
		$user = User::GetResetPassData($db, $user_mail);
		$userId = $user["id"];
		$userName = $user["nombre"];
		$valLink = $user["val_link"];
		
		$valLink = LINK_VALIDATE_USER_EMAIL."/".$userId."/".$valLink;
		// subject
		$subject = SUBJECT_MESSAGE_SIGNIN_EMAIL;

		// message
		$message = "
		<html>
								<head>
				<meta https-equiv='Content-Type' content='text/html; charset=utf-8' />
				<meta name='viewport' content='width=device-width, initial-scale=1.0'/>
				<title>Tempo-Responsive Email Template</title>
				
				<style type='text/css'>
					
					div, p, a, li, td { -webkit-text-size-adjust:none; }
					#outlook a {padding:0;} 
					html{width: 100%; }
					body{width:100% !important; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; margin:0; padding:0;}
				
					.ExternalClass {width:100%;} 
					.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%;} 
					#backgroundTable {margin:0; padding:0; width:100% !important; line-height: 100% !important;}
					img {outline:none; text-decoration:none;border:none; -ms-interpolation-mode: bicubic;}
					a img {border:none;}
					.image_fix {display:block;}
					p {margin: 0px 0px !important;}
					table td {border-collapse: collapse;}
					table { border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; }
					a {color: #33b9ff;text-decoration: none;text-decoration:none!important;}
					/*STYLES*/
					table[class=full] { width: 100%; clear: both; }
					.imgpop{color:#FFFFFF;}
					/*IPAD STYLES*/
					@media only screen and (max-width: 640px) {
					a[href^='tel'], a[href^='sms'] {
					text-decoration: none;
					color: #33b9ff; /* or whatever your want */
					pointer-events: none;
					cursor: default;
					}
					.mobile_link a[href^='tel'], .mobile_link a[href^='sms'] {
					text-decoration: default;
					color: #33b9ff !important;
					pointer-events: auto;
					cursor: default;
					}
					table[class=devicewidth] {width: 440px!important;text-align:center!important;}
					table[class=devicewidthinner] {width: 420px!important;text-align:center!important;}
					img[class=banner] {width: 440px!important;height:220px!important;}
					img[class=col2img] {width: 440px!important;height:220px!important;}
					
					
					}
					/*IPHONE STYLES*/
					@media only screen and (max-width: 480px) {
					a[href^='tel'], a[href^='sms'] {
					text-decoration: none;
					color: #33b9ff; /* or whatever your want */
					pointer-events: none;
					cursor: default;
					}
					.mobile_link a[href^='tel'], .mobile_link a[href^='sms'] {
					text-decoration: default;
					color: #33b9ff !important; 
					pointer-events: auto;
					cursor: default;
					}
					table[class=devicewidth] {width: 280px!important;text-align:center!important;}
					table[class=devicewidthinner] {width: 260px!important;text-align:center!important;}
					img[class=banner] {width: 280px!important;height:140px!important;}
					img[class=col2img] {width: 280px!important;height:140px!important;}
					
					
					}
				</style>
			</head>
			<body>
			<!-- Start of preheader -->
			<table width='100%' bgcolor='#fcfcfc' cellpadding='0' cellspacing='0' border='0' id='backgroundTable' st-sortable='preheader' >
			<tbody>
				<tr>
					<td>
						<table width='600' cellpadding='0' cellspacing='0' border='0' align='center' class='devicewidth'>
						<tbody>
							<tr>
								<td width='100%'>
									<table width='600' cellpadding='0' cellspacing='0' border='0' align='center' class='devicewidth'>
									<tbody>
										<!-- Spacing -->
										<tr>
											<td width='100%' height='20'></td>
										</tr>
										<!-- Spacing -->
										<tr>
											<td width='100%' align='left' valign='middle' style='font-family: Helvetica, arial, sans-serif; font-size: 13px;color: #282828' st-content='preheader'>
											".WELCOME_WORD_EMAIL." ".$userName.". ".VALIDATE_TEXT_EMAIL."
											</td>
										</tr>
										<!-- Spacing -->
										<tr>
											<td width='100%' height='20'></td>
										</tr>
										<!-- Spacing -->
									</tbody>
									</table>
								</td>
							</tr>
						</tbody>
						</table>
					</td>
				</tr>
			</tbody>
			</table>
			<!-- End of preheader -->       
			<!-- Start of header -->
			<table width='100%' bgcolor='#fcfcfc' cellpadding='0' cellspacing='0' border='0' id='backgroundTable' st-sortable='header'>
							<tbody>
							<tr>
								<td width='100%'>
									<table width='600' bgcolor='#2eab4a' color='#FFFFFF' cellpadding='0' cellspacing='0' border='0' align='center' class='devicewidth'>
									<tbody>
										
													<tr>
														<td width='140' height='50' align='center'>
															<div class='imgpop'>
															<h1 style='font-family: Helvetica, arial, sans-serif;'><img src='".LOGO_IMG_EMAIL."'/></h1>
															</div>
														</td>
													</tr>
												
									</tbody>
									</table>
								</td>
							</tr>
						</tbody>
					
			</table>
			<!-- End of Header -->


			<!-- start of Full text -->
			<table width='100%' bgcolor='#fcfcfc' cellpadding='0' cellspacing='0' border='0' id='backgroundTable' st-sortable='full-text'>
			<tbody>
				<tr>
					<td>
						<table width='600' cellpadding='0' cellspacing='0' border='0' align='center' class='devicewidth'>
						<tbody>
							<tr>
								<td width='100%'>
									<table bgcolor='#ffffff' width='600' cellpadding='0' cellspacing='0' border='0' align='center' class='devicewidth'>
									<tbody>
										<!-- Spacing -->
										<tr>
											<td height='20' style='font-size:1px; line-height:1px; mso-line-height-rule: exactly;'>&nbsp;</td>
										</tr>
										<!-- Spacing -->
										<tr>
											<td>
												<table width='560' align='center' cellpadding='0' cellspacing='0' border='0' class='devicewidthinner'>
												<tbody>
													<!-- Title -->
													<tr>
														<td style='font-family: Helvetica, arial, sans-serif; font-size: 18px; color: #282828; text-align:center; line-height: 24px;'>
															".THANK_YOU_TRUSTING_TEXT_EMAIL."
														</td>
													</tr>
													<!-- End of Title -->
													<!-- spacing -->
													<tr>
														<td width='100%' height='15' style='font-size:1px; line-height:1px; mso-line-height-rule: exactly;'>&nbsp;</td>
													</tr>
													<!-- End of spacing -->
													<!-- content -->
													<tr>
														<td style='font-family: Helvetica, arial, sans-serif; font-size: 14px; color: #889098; text-align:center; line-height: 24px;'>
															".VALIDATE_MESSAGE_TEXT_EMAIL."
														
														</td>
													</tr>
													<!-- End of content -->
													<!-- Spacing -->
													<tr>
														<td width='100%' height='15' style='font-size:1px; line-height:1px; mso-line-height-rule: exactly;'>&nbsp;</td>
													</tr>
													<!-- Spacing -->
												</tbody>
												</table>
												<table align='center' width='120' height='32' bgcolor='#2eab4a' align='left' valign='middle' border='0' cellpadding='0' cellspacing='0' style='border-radius:3px;' st-button='learnmore'>
																		<tbody>
																			<tr>
																				<td height='9' align='center' style='font-size:1px; line-height:1px;'>&nbsp;</td>
																			</tr>
																			<tr>
																				<td height='14' align='center' valign='middle' style='font-family: Helvetica, Arial, sans-serif; font-size: 13px; font-weight:bold;color: #ffffff; text-align:center; line-height: 14px; ; -webkit-text-size-adjust:none;' st-title='fulltext-btn'>
																					<a style='text-decoration: none;color: #ffffff; text-align:center;' href='".$valLink."'>".VALIDATE_EMAIL_WORD_EMAIL."</a> 
																				</td>
																			</tr>
																			<tr>
																				<td height='9' align='center' style='font-size:1px; line-height:1px;'>&nbsp;</td>
																			</tr>
																		</tbody>
																		</table>
											</td>
										</tr>
										<!-- Spacing -->
										<tr>
											<td height='20' style='font-size:1px; line-height:1px; mso-line-height-rule: exactly;'>&nbsp;</td>
										</tr>
										<!-- Spacing -->
									</tbody>
									</table>
								</td>
							</tr>
						</tbody>
						</table>
					</td>
				</tr>
			</tbody>
			</table>
			<!-- End of Full Text -->

			<table width='600' bgcolor='#fcfcfc' cellpadding='0' cellspacing='0' border='0' id='backgroundTable' st-sortable='header'>
							<tbody>
							<tr>
								<td width='600' bgcolor='#2eab4a' height='5' color='#FFFFFF' cellpadding='0' cellspacing='0' border='0' align='center' class='devicewidth'>
									
								</td>
							</tr>
						</tbody>
					
			</table>

			<!-- Start of Postfooter -->
			<table width='100%' bgcolor='#fcfcfc' cellpadding='0' cellspacing='0' border='0' id='backgroundTable' st-sortable='postfooter' >
			<tbody>
				<tr>
					<td>
						<table width='600' cellpadding='0' cellspacing='0' border='0' align='center' class='devicewidth'>
						<tbody>
							<!-- Spacing -->
							<tr>
								<td width='100%' height='20'></td>
							</tr>
							<!-- Spacing -->
							<tr>
								<td align='center' valign='middle' style='font-family: Helvetica, arial, sans-serif; font-size: 13px; st-content='preheader'>
								".HAVE_ANY_PROBLEM_WORD_EMAIL." <a href='".$link_duda_contacto."' style='text-decoration:none;'>".HERE_WORD_EMAIL."</a> 
								</td>
							</tr>
							<!-- Spacing -->
							<tr>
								<td width='100%' height='20'></td>
							</tr>
							<!-- Spacing -->
						</tbody>
						</table>
					</td>
				</tr>
			</tbody>
			</table>
			<!-- End of postfooter -->      
			</body>
		</html>";

		// To send HTML mail, the Content-type header must be set
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

		// Additional headers
		$headers .= 'From: ScanEat <scaneat@scaneat.com>' . "\r\n";

		// Mail it
		$result = new Ingredient();
		$result->setStatus(OK);
		$result->setMessage("Correo enviado");
		mail($to, $subject, $message, $headers);
		return $result;
	}

	static function SendResetPassMail($db, $user_mail) {
		$user = User::GetResetPassData($db, $user_mail);
		$userId = $user["id"];
		$userName = $user["nombre"];
		$valLink = $user["val_link"];
		$to  = utf8_decode($user_mail);
		// subject
		$passLink = LINK_RESET_PASS_USER_EMAIL."/".$userId."/".$valLink;
		$subject = SUBJECT_MESSAGE_SIGNIN_EMAIL;

		// message
		$message = "
		<html>
				<head>
				<meta https-equiv='Content-Type' content='text/html; charset=utf-8' />
				<meta name='viewport' content='width=device-width, initial-scale=1.0'/>
				<title>Tempo-Responsive Email Template</title>
				
				<style type='text/css'>
				
				div, p, a, li, td { -webkit-text-size-adjust:none; }
				#outlook a {padding:0;} 
				html{width: 100%; }
				body{width:100% !important; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; margin:0; padding:0;}
				
				.ExternalClass {width:100%;} 
				.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%;} 
				#backgroundTable {margin:0; padding:0; width:100% !important; line-height: 100% !important;}
				img {outline:none; text-decoration:none;border:none; -ms-interpolation-mode: bicubic;}
				a img {border:none;}
				.image_fix {display:block;}
				p {margin: 0px 0px !important;}
				table td {border-collapse: collapse;}
				table { border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; }
				a {color: #33b9ff;text-decoration: none;text-decoration:none!important;}
				/*STYLES*/
				table[class=full] { width: 100%; clear: both; }
				.imgpop{color:#FFFFFF;}
				/*IPAD STYLES*/
				@media only screen and (max-width: 640px) {
				a[href^='tel'], a[href^='sms'] {
				text-decoration: none;
				color: #33b9ff; /* or whatever your want */
				pointer-events: none;
				cursor: default;
				}
				.mobile_link a[href^='tel'], .mobile_link a[href^='sms'] {
				text-decoration: default;
				color: #33b9ff !important;
				pointer-events: auto;
				cursor: default;
				}
				table[class=devicewidth] {width: 440px!important;text-align:center!important;}
				table[class=devicewidthinner] {width: 420px!important;text-align:center!important;}
				img[class=banner] {width: 440px!important;height:220px!important;}
				img[class=col2img] {width: 440px!important;height:220px!important;}
				
				
				}
				/*IPHONE STYLES*/
				@media only screen and (max-width: 480px) {
				a[href^='tel'], a[href^='sms'] {
				text-decoration: none;
				color: #33b9ff; /* or whatever your want */
				pointer-events: none;
				cursor: default;
				}
				.mobile_link a[href^='tel'], .mobile_link a[href^='sms'] {
				text-decoration: default;
				color: #33b9ff !important; 
				pointer-events: auto;
				cursor: default;
				}
				table[class=devicewidth] {width: 280px!important;text-align:center!important;}
				table[class=devicewidthinner] {width: 260px!important;text-align:center!important;}
				img[class=banner] {width: 280px!important;height:140px!important;}
				img[class=col2img] {width: 280px!important;height:140px!important;}
				
				
				}
				</style>
			</head>
			<body>
				<!-- Start of preheader -->
				<table width='100%' bgcolor='#fcfcfc' cellpadding='0' cellspacing='0' border='0' id='backgroundTable' st-sortable='preheader' >
					<tbody>
						<tr>
						<td>
							<table width='600' cellpadding='0' cellspacing='0' border='0' align='center' class='devicewidth'>
								<tbody>
									<tr>
									<td width='100%'>
										<table width='600' cellpadding='0' cellspacing='0' border='0' align='center' class='devicewidth'>
											<tbody>
												<!-- Spacing -->
												<tr>
												<td width='100%' height='20'></td>
												</tr>
												<!-- Spacing -->
												<tr>
												<td width='100%' align='left' valign='middle' style='font-family: Helvetica, arial, sans-serif; font-size: 13px;color: #282828' st-content='preheader'>
													".HI_WORD_EMAIL." ".$userName.". 
												</td>
												</tr>
												<!-- Spacing -->
												<tr>
												<td width='100%' height='20'></td>
												</tr>
												<!-- Spacing -->
											</tbody>
										</table>
									</td>
									</tr>
								</tbody>
							</table>
						</td>
						</tr>
					</tbody>
				</table>
				<!-- End of preheader -->       
				<!-- Start of header -->
				<table width='100%' bgcolor='#fcfcfc' cellpadding='0' cellspacing='0' border='0' id='backgroundTable' st-sortable='header'>
								<tbody>
									<tr>
									<td width='100%'>
										<table width='600' bgcolor='#2eab4a' color='#FFFFFF' cellpadding='0' cellspacing='0' border='0' align='center' class='devicewidth'>
											<tbody>
											
															<tr>
															<td width='140' height='50' align='center'>
																<div class='imgpop'>
																	<h1 style='font-family: Helvetica, arial, sans-serif;'><img src='https://scaneat.com/img/mail/logo.png'/></h1>
																</div>
															</td>
															</tr>
														
											</tbody>
										</table>
									</td>
									</tr>
								</tbody>
							
				</table>
				<!-- End of Header -->
				
				
				<!-- start of Full text -->
				<table width='100%' bgcolor='#fcfcfc' cellpadding='0' cellspacing='0' border='0' id='backgroundTable' st-sortable='full-text'>
					<tbody>
						<tr>
						<td>
							<table width='600' cellpadding='0' cellspacing='0' border='0' align='center' class='devicewidth'>
								<tbody>
									<tr>
									<td width='100%'>
										<table bgcolor='#ffffff' width='600' cellpadding='0' cellspacing='0' border='0' align='center' class='devicewidth'>
											<tbody>
												<!-- Spacing -->
												<tr>
												<td height='20' style='font-size:1px; line-height:1px; mso-line-height-rule: exactly;'>&nbsp;</td>
												</tr>
												<!-- Spacing -->
												<tr>
												<td>
													<table width='560' align='center' cellpadding='0' cellspacing='0' border='0' class='devicewidthinner'>
														<tbody>
															<!-- Title -->
															
															<!-- End of Title -->
															<!-- spacing -->
															<tr>
															<td width='100%' height='15' style='font-size:1px; line-height:1px; mso-line-height-rule: exactly;'>&nbsp;</td>
															</tr>
															<!-- End of spacing -->
															<!-- content -->
															<tr>
															<td style='font-family: Helvetica, arial, sans-serif; font-size: 14px; color: #889098; text-align:center; line-height: 24px;'>
																".CHANGE_PASS_TEXT_EMAIL."
															</td>
															</tr>
															<!-- End of content -->
															<!-- Spacing -->
															<tr>
															<td width='100%' height='15' style='font-size:1px; line-height:1px; mso-line-height-rule: exactly;'>&nbsp;</td>
															</tr>
															<!-- Spacing -->
														</tbody>
													</table>
													<table align='center' width='120' height='32' bgcolor='#2eab4a' align='left' valign='middle' border='0' cellpadding='0' cellspacing='0' style='border-radius:3px;' st-button='learnmore'>
																				<tbody>
																					<tr>
																					<td height='9' align='center' style='font-size:1px; line-height:1px;'>&nbsp;</td>
																					</tr>
																					<tr>
																					<td height='14' align='center' valign='middle' style='font-family: Helvetica, Arial, sans-serif; font-size: 13px; font-weight:bold;color: #ffffff; text-align:center; line-height: 14px; ; -webkit-text-size-adjust:none;' st-title='fulltext-btn'>
																						<a style='text-decoration: none;color: #ffffff; text-align:center;' href='".$passLink."'>".CHANGE_PASS_BUTTON_EMAIL."</a> 
																					</td>
																					</tr>
																					<tr>
																					<td height='9' align='center' style='font-size:1px; line-height:1px;'>&nbsp;</td>
																					</tr>
																				</tbody>
																			</table>
												</td>
												</tr>
												<!-- Spacing -->
												<tr>
												<td height='20' style='font-size:1px; line-height:1px; mso-line-height-rule: exactly;'>&nbsp;</td>
												</tr>
												<!-- Spacing -->
											</tbody>
										</table>
									</td>
									</tr>
								</tbody>
							</table>
						</td>
						</tr>
					</tbody>
				</table>
				<!-- End of Full Text -->
				
				<table width='600' bgcolor='#fcfcfc' cellpadding='0' cellspacing='0' border='0' id='backgroundTable' st-sortable='header'>
								<tbody>
									<tr>
									<td width='600' bgcolor='#2eab4a' height='5' color='#FFFFFF' cellpadding='0' cellspacing='0' border='0' align='center' class='devicewidth'>
										
									</td>
									</tr>
								</tbody>
							
				</table>
				
				<!-- Start of Postfooter -->
				<table width='100%' bgcolor='#fcfcfc' cellpadding='0' cellspacing='0' border='0' id='backgroundTable' st-sortable='postfooter' >
					<tbody>
						<tr>
						<td>
							<table width='600' cellpadding='0' cellspacing='0' border='0' align='center' class='devicewidth'>
								<tbody>
									<!-- Spacing -->
									<tr>
									<td width='100%' height='20'></td>
									</tr>
									<!-- Spacing -->
									<tr>
									<td align='center' valign='middle' style='font-family: Helvetica, arial, sans-serif; font-size: 13px; st-content='preheader'>
										".HAVE_ANY_PROBLEM_WORD_EMAIL." <a href='".LINK_CONTACT_MAIL."' style='text-decoration:none;'>".HERE_WORD_EMAIL."</a>
									</td>
									</tr>
									<!-- Spacing -->
									<tr>
									<td width='100%' height='20'></td>
									</tr>
									<!-- Spacing -->
								</tbody>
							</table>
						</td>
						</tr>
					</tbody>
				</table>
				<!-- End of postfooter -->      
					</body>
		</html>";

		// To send HTML mail, the Content-type header must be set
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

		// Additional headers
		$headers .= 'From: ScanEat <scaneat@scaneat.com>' . "\r\n";

		// Mail it
		$result = new Ingredient();
		$result->setStatus(OK);
		$result->setMessage("Correo enviado");
		mail($to, $subject, $message, $headers);
		return $result;
	}
}

?>