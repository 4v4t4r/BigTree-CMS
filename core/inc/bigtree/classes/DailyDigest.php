<?php
	/*
		Class: BigTree\DailyDigest
			Provides an interface for handling BigTree daily digest emails.
	*/

	namespace BigTree;

	use BigTree;
	use BigTreeCMS;

	class DailyDigest extends BaseObject {

		/*
			Function: getAlerts
				Generates markup for daily digest alerts for a given user.

			Parameters:
				user - A user entry

			Returns:
				HTML markup for daily digest email
		*/

		static function getAlerts($user) {
			$alerts = BigTree\Page::getAlertsForUser($user);
			$alerts_markup = "";
			$wrapper = '<div style="margin: 20px 0 30px;">
							<h3 style="color: #333; font-size: 18px; font-weight: normal; margin: 0 0 10px; padding: 0;">Content Age Alerts</h3>
							<table cellspacing="0" cellpadding="0" style="border: 1px solid #eee; border-width: 1px 1px 0; width: 100%;">
								<thead style="background: #ccc; color: #fff; font-size: 10px; text-align: left; text-transform: uppercase;">
									<tr>
										<th style="font-weight: normal; padding: 4px 0 3px 15px;" align="left">Page</th>
										<th style="font-weight: normal; padding: 4px 20px 3px 15px; text-align: right; width: 50px;" align="left">Age</th>
										<th style="font-weight: normal; padding: 4px 0 3px; text-align: center; width: 50px;" align="left">View</th>
										<th style="font-weight: normal; padding: 4px 0 3px; text-align: center; width: 50px;" align="left">Edit</th>
									</tr>
								</thead>
								<tbody style="color: #333; font-size: 13px;">
									{content_alerts}
								</tbody>
							</table>
						</div>';

			// Alerts
			if (is_array($alerts) && count($alerts)) {
				foreach ($alerts as $alert) {
					$alerts_markup .= '<tr>
										<td style="border-bottom: 1px solid #eee; padding: 10px 0 10px 15px;">'.$alert["nav_title"].'</td>
										<td style="border-bottom: 1px solid #eee; padding: 10px 20px 10px 15px; text-align: right;">'.$alert["current_age"].' Days</td>
										<td style="border-bottom: 1px solid #eee; padding: 10px 0; text-align: center;"><a href="'.WWW_ROOT.$alert["path"].'/"><img src="'.ADMIN_ROOT.'images/email/launch.gif" alt="Launch" /></a></td>
										<td style="border-bottom: 1px solid #eee; padding: 10px 0; text-align: center;"><a href="'.ADMIN_ROOT."pages/edit/".$alert["id"].'/"><img src="'.ADMIN_ROOT.'images/email/edit.gif" alt="Edit" /></a></td>
									 </tr>';
				}
			}

			if ($alerts_markup) {
				return str_replace("{content_alerts}",$alerts_markup,$wrapper);
			}
			return "";
		}

		/*
			Function: getChanges
				Generates markup for daily digest pending changes for a given user.

			Parameters:
				user - A user entry

			Returns:
				HTML markup for daily digest email
		*/

		static function getChanges($user) {
			$changes = static::getPublishableChanges($user["id"]);
			$changes_markup = "";
			$wrapper = '<div style="margin: 20px 0 30px;">
							<h3 style="color: #333; font-size: 18px; font-weight: normal; margin: 0 0 10px; padding: 0;">Pending Changes</h3>
							<table cellspacing="0" cellpadding="0" style="border: 1px solid #eee; border-width: 1px 1px 0; width: 100%;">
								<thead style="background: #ccc; color: #fff; font-size: 10px; text-align: left; text-transform: uppercase;">
									<tr>
										<th style="font-weight: normal; padding: 4px 0 3px 15px; width: 150px;" align="left">Author</th>
										<th style="font-weight: normal; padding: 4px 0 3px 15px; width: 180px;" align="left">Module</th>
										<th style="font-weight: normal; padding: 4px 0 3px 15px;" align="left">Type</th>
										<th style="font-weight: normal; padding: 4px 0 3px; text-align: center; width: 50px;" align="left">View</th>
									</tr>
								</thead>
								<tbody style="color: #333; font-size: 13px;">
									{pending_changes}
								</tbody>
							</table>
						</div>';

			if (count($changes)) {
				foreach ($changes as $change) {
					$changes_markup .= '<tr>';
					$changes_markup .= '<td style="border-bottom: 1px solid #eee; padding: 10px 0 10px 15px;">'.$change->User->Name.'</td>';
					if ($change["title"]) {
						$changes_markup .= '<td style="border-bottom: 1px solid #eee; padding: 10px 0 10px 15px;">Pages</td>';
					} else {
						$changes_markup .= '<td style="border-bottom: 1px solid #eee; padding: 10px 0 10px 15px;">'.$change->Module->Name.'</td>';
					}
					if (is_null($change["item_id"])) {
						$changes_markup .= '<td style="border-bottom: 1px solid #eee; padding: 10px 0 10px 15px;">Addition</td>';
					} else {
						$changes_markup .= '<td style="border-bottom: 1px solid #eee; padding: 10px 0 10px 15px;">Edit</td>';
					}
					$changes_markup .= '<td style="border-bottom: 1px solid #eee; padding: 10px 0; text-align: center;"><a href="'.$change->EditLink.'"><img src="'.ADMIN_ROOT.'images/email/launch.gif" alt="Launch" /></a></td>' . "\r\n";
					$changes_markup .= '</tr>';
				}

				return str_replace("{pending_changes}",$changes_markup,$wrapper);
			} else {
				return "";
			}
		}

		/*
			Function: getMessages
				Generates markup for daily digest messages for a given user.

			Parameters:
				user - A user entry

			Returns:
				HTML markup for daily digest email
		*/

		static function getMessages($user) {
			$messages = BigTree\Message::allByUser($user["id"],true);
			$messages_markup = "";
			$wrapper = '<div style="margin: 20px 0 30px;">
							<h3 style="color: #333; font-size: 18px; font-weight: normal; margin: 0 0 10px; padding: 0;">Unread Messages</h3>
							<table cellspacing="0" cellpadding="0" style="border: 1px solid #eee; border-width: 1px 1px 0; width: 100%;">
								<thead style="background: #ccc; color: #fff; font-size: 10px; text-align: left; text-transform: uppercase;">
									<tr>
										<th style="font-weight: normal; padding: 4px 0 3px 15px; width: 150px;" align="left">Sender</th>
										<th style="font-weight: normal; padding: 4px 0 3px 15px; width: 180px;" align="left">Subject</th>
										<th style="font-weight: normal; padding: 4px 0 3px 15px;" align="left">Date</th>
									</tr>
								</thead>
								<tbody style="color: #333; font-size: 13px;">
									{unread_messages}
								</tbody>
							</table>
						</div>';

			if (count($messages["unread"])) {
				foreach ($messages["unread"] as $message) {
					$messages_markup .= '<tr>
											<td style="border-bottom: 1px solid #eee; padding: 10px 0 10px 15px;">'.$message["sender_name"].'</td>
											<td style="border-bottom: 1px solid #eee; padding: 10px 0 10px 15px;">'.$message["subject"].'</td>
											<td style="border-bottom: 1px solid #eee; padding: 10px 0 10px 15px;">'.date("n/j/y g:ia",strtotime($message["date"])).'</td>
										</tr>';
				}

				return str_replace("{unread_messages}",$messages_markup,$wrapper);
			} else {
				return "";
			}
		}

		/*
			Function: send
				Sends out a daily digest email to all who have subscribed.
		*/

		static function send() {
			global $bigtree;

			// We're going to show the site's title in the email
			$site_title = BigTreeCMS::$DB->fetchSingle("SELECT `nav_title` FROM `bigtree_pages` WHERE id = '0'");

			// Find out what blocks are on
			$extension_settings = Setting::value("bigtree-internal-extension-settings");
			$digest_settings = $extension_settings["digest"];

			// Get a list of blocks we'll draw in emails
			$blocks = array();
			$positions = array();

			// Start email service
			$email_service = new BigTreeEmailService;
		
			// We're going to get the position setups and the multi-sort the list to get it in order
			foreach (BigTreeAdmin::$DailyDigestPlugins["core"] as $id => $details) {
				if (empty($digest_settings[$id]["disabled"])) {
					$blocks[] = $details["function"];
					$positions[] = isset($digest_settings[$id]["position"]) ? $digest_settings[$id]["position"] : 0;
				}
			}
			foreach (BigTreeAdmin::$DailyDigestPlugins["extension"] as $extension => $set) {
				foreach ($set as $id => $details) {
					$id = $extension."*".$id;
					if (empty($digest_settings[$id]["disabled"])) {
						$blocks[] = $details["function"];
						$positions[] = isset($digest_settings[$id]["position"]) ? $digest_settings[$id]["position"] : 0;
					}
				}
			}
			array_multisort($positions,SORT_DESC,$blocks);

			// Loop through each user who has opted in to emails
			$daily_digest_users = BigTreeCMS::$DB->fetchAll("SELECT * FROM bigtree_users WHERE daily_digest = 'on'");
			foreach ($daily_digest_users as $user) {
				$block_markup = "";

				foreach ($blocks as $function) {
					$block_markup .= call_user_func($function,$user);
				}

				// Send it
				if (trim($block_markup)) {
					$body = file_get_contents(BigTree::path("admin/email/daily-digest.html"));
					$body = str_ireplace("{www_root}", $bigtree["config"]["www_root"], $body);
					$body = str_ireplace("{admin_root}", $bigtree["config"]["admin_root"], $body);
					$body = str_ireplace("{site_title}", $site_title, $body);
					$body = str_ireplace("{date}", date("F j, Y",time()), $body);
					$body = str_ireplace("{blocks}", $block_markup, $body);

					// If we don't have a from email set, third parties most likely will fail so we're going to use local sending
					if ($email_service->Settings["bigtree_from"]) {
						$reply_to = "no-reply@".(isset($_SERVER["HTTP_HOST"]) ? str_replace("www.","",$_SERVER["HTTP_HOST"]) : str_replace(array("http://www.","https://www.","http://","https://"),"",DOMAIN));
						$email_service->sendEmail("$site_title Daily Digest",$body,$user["email"],$email_service->Settings["bigtree_from"],"BigTree CMS",$reply_to);
					} else {
						BigTree::sendEmail($user["email"],"$site_title Daily Digest",$body);
					}
				}
			}
		}

	}
