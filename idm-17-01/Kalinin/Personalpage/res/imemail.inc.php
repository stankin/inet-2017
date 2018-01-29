<?php
  	//Incomedia WebSite X5 EMail Class. All rights reserved.
  
	class imEMail {
		var $from;
		var $to;
		var $subject;
		var $charset;
		var $text;
		var $html;
		var $type;
		var $newline = "\r\n";
		var $exposeWsx5 = true;
		
		var $attachments;
		
		function imEMail($from,$to,$subject,$charset) {
			$this->from = $from;
			$this->to = $to;
			$this->charset = $charset;
			$this->subject = strlen($subject) ? "=?" . strtoupper($this->charset) . "?B?". base64_encode($subject) . "?=" : "";
		}
		
		function setExpose($expose) {
			$this->exposeWsx5 = $expose;
		}

		/**
		 * Set the type of email standard (HTML, HTML-X or Text-only)
		 * @param [type] $type [description]
		 */
		function setStandardType($type = "html") {
			$this->type = $type;
			$this->newline = (strtolower($type) == "html-x" ? "\n" : "\r\n");
		}
		
		function setFrom($from) {
			$this->from = $from;
		}
		
		function setTo($to) {
			$this->to = $to;
		}
		
		function setSubject($subject) {
			if (strlen($subject))
				$this->subject = "=?" . strtoupper($this->charset) . "?B?". base64_encode($subject) . "?=";
			else
				$this->subject = "";
		}
		
		function setCharset($charset) {
			$this->charset = $charset;
		}
		
		function setText($text) {
			$this->text = $text;
		}
		
		function setHTML($html) {
			$this->html = $html;
		}
		
		function attachFile($name,$content,$mime_type) {
			if (strlen($name) === 0)
				return false;
			$attachment['name'] = "=?" . strtoupper($this->charset) . "?B?". base64_encode($name) . "?=";
			$attachment['content'] = base64_encode($content);
			$attachment['mime_type'] = $mime_type;
			$this->attachments[] = $attachment;
		}
		
		function send() {
			$headers = "";
			$msg = "";

			if($this->from == "" || $this->to == "" || ($this->text == "" && $this->html == ""))
				return false;
			
			if ($this->type != "text") {

				/*
				|-------------------------------
				| HTML/HTML-X email
				|-------------------------------
				 */
				
				$boundary_file = md5(time() . "_attachment");
				$boundary_alt = md5(time() . "_alternative");			

				$headers .= "From: " . $this->from . $this->newline;
				$headers .= "Message-ID: <" . time() . rand(0,9) . rand(0,9) . "@" . ($this->exposeWsx5 ? "websitex5" : rand(100,200)) . ".users>" . $this->newline;
				$headers .= "X-Mailer: " . ($this->exposeWsx5 ? "WebSiteX5 Mailer" : "PHP") . $this->newline;
				
				$headers .= "MIME-Version: 1.0" . $this->newline;

				if(is_array($this->attachments)) {
					$headers .= "Content-Type: multipart/mixed; boundary=\"" . $boundary_file . "\"" . $this->newline;
					$headers .= "--" . $boundary_file . $this->newline;
				}
				
				if($this->html == "") {
					$headers .= "Content-Type: text/plain; charset=" . strtoupper($this->charset) . $this->newline;
					if (strtolower($this->charset) != "utf-8")
						$headers .= "Content-Transfer-Encoding: 7bit" . $this->newline;
					else
					  	$headers .= "Content-Transfer-Encoding: 8bit" . $this->newline;
					$msg .= $this->text . $this->newline . $this->newline;
				}
				else if($this->text == "") {
					$headers .= "Content-Type: text/html; charset=" . strtoupper($this->charset) . $this->newline;
					if (strtolower($this->charset) != "utf-8")
						$headers .= "Content-Transfer-Encoding: 7bit" . $this->newline;
	        		else
					  	$headers .= "Content-Transfer-Encoding: 8bit" . $this->newline;
					$msg .= $this->html . $this->newline . $this->newline;
				}
				else {
					$headers .= "Content-Type: multipart/alternative; boundary=\"" . $boundary_alt . "\"" . $this->newline;
					
					$msg .= "--" .$boundary_alt . $this->newline;
					$msg .= "Content-Type: text/plain; charset=" . strtoupper($this->charset) . $this->newline;
					if (strtolower($this->charset) != "utf-8")
	    				$msg .= "Content-Transfer-Encoding: 7bit" . $this->newline;
	        		else
				  		$msg .= "Content-Transfer-Encoding: 8bit" . $this->newline;
					$msg .= $this->newline;
					$msg .= $this->text . $this->newline . $this->newline;
					
					$msg .= "--" . $boundary_alt . $this->newline;
				  	$msg .= "Content-Type: text/html; charset=" . strtoupper($this->charset) . $this->newline;
				  	if (strtolower($this->charset) != "utf-8")
						$msg .= "Content-Transfer-Encoding: 7bit" . $this->newline;
	        		else
				  		$msg .= "Content-Transfer-Encoding: 8bit" . $this->newline;
					$msg .= $this->newline;
					$msg .= $this->html . $this->newline . $this->newline;
					
					$msg .= "--" . $boundary_alt . "--" . $this->newline . $this->newline;
				}
				
				if(is_array($this->attachments)) {
					foreach($this->attachments as $attachment) {
						$msg .= "--" . $boundary_file . $this->newline;
						$msg .= "Content-Type: " . $attachment["mime_type"] . "; name=\"" . $attachment["name"] . "\"" . $this->newline;
						$msg .= "Content-Transfer-Encoding: base64" . $this->newline;
						$msg .= "Content-Disposition: attachment; filename=\"" . $attachment["name"] . "\"" . $this->newline . $this->newline;
						$msg .= chunk_split($attachment["content"]) . $this->newline . $this->newline;
					}
					$msg .= "--" . $boundary_file . "--" . $this->newline . $this->newline;
				}
				
				if (function_exists('ini_set'))
					@ini_set("sendmail_from", $this->from);
				
				// First attempt: -f flag, no more headers
				if(@mail($this->to, $this->subject, $msg, $headers, "-f" . $this->from))
					return true;
				// Second attempt: no -f flag, no more headers
				if (@mail($this->to, $this->subject, $msg, $headers))
					return true;
				// Third attempt: no -f flag, one more To header
				$headers = "To: " . $this->to . $this->newline . $headers;
				return @mail($this->to, $this->subject, $msg, $headers);
			} else {

				/*
				|-------------------------------
				| Text-only email
				|-------------------------------
				 */

				$headers .= "From: " . $this->from . $this->newline;
				$headers .= "Content-Type: text/plain;charset=" . $this->charset . $this->newline;
				$msg .= $this->text . $this->newline . $this->newline;

				$r = @mail($this->to, $this->subject, $msg, $headers);
				return $r;
			}
		}
	}

// End of file imemail.inc.php

