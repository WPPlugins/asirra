<?php
/*
Plugin Name: Asirra
Plugin URI: http://research.microsoft.com/asirra/
Description: Uses the Asirra web service (<a href="http://research.microsoft.com/asirra/">http://research.microsoft.com/asirra/</a>) to add a pleasant image-based HIP for comments
Author: Jon Howell
Version: 1.0
Author URI: http://research.microsoft.com/~howell/
*/
/*  Copyright 2007  Jon Howell  (contact email : asirra@microsoft.com)
**
**  This program is in the public domain.
*/
require_once(dirname(__FILE__).'/../../../wp-config.php');

class AsirraValidator
{
	var $inResult = 0;
	var $passed = 0;
	
	function AsirraValidator($ticket)
	{
		global $g_this;	// Yuck. Is there a way to have callback methods see my $this without using a global in PHP?
		$g_this = $this;
		$g_this->dbg = "";

		$g_this->dbg .= "<br>ticket = ".$ticket;

		$AsirraServiceUrl = "http://challenge.asirra.com/cgi/Asirra";
	
		$url = $AsirraServiceUrl."?action=ValidateTicket&ticket=".$ticket;
	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		$resultXml = curl_exec($ch);
		curl_close($ch);
	
		$xml_parser = xml_parser_create();

		function startElement($parser, $name, $attrs)
		{
			global $g_this;
			$g_this->inResult = ($name=="RESULT");
			$g_this->dbg .= "<br>start ".$name." ir=".$g_this->inResult;
		}

		function endElement($name)
		{
			global $g_this;
			$g_this->inResult = 0;
			$g_this->dbg .= "<br>end ".$name;
		}

		function characterData($parer, $data)
		{
			global $g_this;
			$g_this->dbg .= "<br>cd ir ".$g_this->inResult." data=".$data;
			if ($g_this->inResult && $data == "Pass")
			{
				$g_this->dbg .= "<br>setting PASS";
				$g_this->passed = 1;
			}
		}

		xml_set_element_handler($xml_parser, startElement, endElement);
		xml_set_character_data_handler($xml_parser, characterData);
		xml_parse($xml_parser, $resultXml, 1);
		xml_parser_free($xml_parser);

		$g_this->dbg .= "<p><pre>XML: ".$resultXml."</pre>";
	
		if (!$g_this->passed)
		{
			// This can be ugly, because only cheaters should
			// see it. Real users that 'fail' the HIP get decent
			// feedback at the client
			// before they ever get back here to the server.
			die("<html>Asirra validation failed!<pre>".$g_this->dbg);
		}
	}

	

}

class AsirraPlugin
{
	var $settings = array();
	
	function AsirraPlugin()
	{
		if (isset($this))
		{
			$this->settings = get_settings('asirra');
			add_action('admin_menu', array(&$this, 'admin_menu'));
			add_filter( 'preprocess_comment', array( &$this, 'comment_post') );    // add post comment post security code check
		}
	}
	
	// TODO: provide a way to push translated text through to the
	// AsirraDiv.

	function admin_menu()
	{
		if (function_exists('add_options_page')) {
			add_options_page('Asirra', 'Asirra', 8, "options-general.php?page=asirra/admin.php");
		}
	}
	
	function update_settings($settings)
	{
		foreach($settings as $key => $val)
		{
			$this->settings[$key] = $val;
		}
		
		update_option('asirra', $this->settings);
	}
	
	function annotate_comment_form()
	{
		echo '<script type="text/javascript" src="http://challenge.asirra.com/js/AsirraClientSide.js"></script>';
		echo '<script type="text/javascript" src="wp-content/plugins/asirra/form-annotate.js"></script>';
	}

	function comment_post($incoming_comment)
	{
		global $_POST;
		
		//require_once(dirname(__FILE__).'/asirra/asirra.cfg.php');

		new AsirraValidator($_POST['Asirra_Ticket']);	// die()s if ticket bogus.

		return $incoming_comment;
	}
}

$asirraPlugin = new AsirraPlugin();

function display_asirra()
{
	AsirraPlugin::annotate_comment_form();
}

//require_once(dirname(__FILE__).'/asirra/asirra.cfg.php');

?>
