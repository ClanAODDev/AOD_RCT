<?php
/**
 * vBulletin-PHP, An easy to use PHP class for providing vBulletin functions
 * Copyright (C) 2012 Nikki <nikki@nikkii.us>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

 /**
 * A module which contains methods for interacting with threads and posts
 * @author Nikki
 */
 class Module_posts extends vBulletinModule {


	/**
	 * Post a new thread
	 * @param forumid  The forum id
	 * @param title  The thread title
	 * @param message  The message
	 * @param tags  The thread tags
	 */
	public function postThread($forumid, $title, $message, $tags=array()) {
		$postfields = $this->getParams();
		$postfields['do'] = "postthread";
		$postfields['f'] = $forumid;
		$postfields['subject'] = $title;
		$postfields['message'] = $message;
		$postfields['vB_Editor_001_mode'] = 'wysiwyg';
		$postfields['taglist'] = implode(",", $tags);
		$resp = $this->request("newthread.php", $postfields, true);
		if(preg_match("#Location:\s*(.*)#", $resp['header'], $matches)) {
			return trim($matches[1]);
		}
		return false;
	}
	
	/**
	 * Post a reply on a thread
	 * @param thread  The thread id
	 * @param reply  The reply
	 * @param title  default false, can be set if you want a title
	 */
	public function postReply($thread, $reply, $title=false) {
		$postfields = $this->getParams();
		$postfields['do'] = "postreply";
		$postfields['t'] = $thread;
		if(!empty($title)) {
			$postfields['title'] = $title;
		}
		$postfields['message'] = $reply;
		$resp = $this->request("newreply.php", $postfields, true);
		if(preg_match("#Location:\s*(.*)#", $resp['header'], $matches)) {
			return trim($matches[1]);
		}
		return false;
	}

	/**
	 * Submit a new member request
	 * @param recruitId  The user's forum id
	 * @param newname  A new name, if the recruit needs a username change
	 */
	/*public function requestNewRecruit($recruitId, $newname = NULL) {
		$postfields = $this->getParams();
		
		$postfields['do'] = 'form'; 
		$postfields['fid'] = 39; 

		$postfields['q_468'] = $recruitId; // recruit user id
		$postfields['q_469'] = $newname; // new rct name (if needs to be changed)
		$postfields['a_470'] = 2; // division [battlefield 4] -- probably needs to be an argument in the function
		$postfields['a_471'] = 1; // rank [Recruit]
		
		$resp = $this->request("misc.php", $postfields, true);
		if(preg_match("#Location:\s*(.*)#", $resp['header'], $matches)) {
			return trim($matches[1]);
		}
		return false;

	}*/


}
?>