<?php

	global $recordings;

	$recordings = array(
		"greetings" => array(
			"3_1" => "Very friendly, low agitation. Examples: What's up? ... Hi, how are you? ... Hello!",
			"3_2" => "Very friendly, medium agitation. Examples: I'm sorry I am really busy ... Yes, what do you need?",
			"3_3" => "Very friendly, high agitation. Examples: Please leave me alone ... Don't make me call the police! ... What's going on?",
			"3_4" => "Very friendly, maximum agitation. Examples: Get away from me! ... I'm calling the police!",
			"2_1" => "Medium friendlyness, low agitation. Examples: Is there something I can help you with? ... What do you want?",
			"2_2" => "Medium friendlyness, medium agitation. Examples: Leave me alone ... I've got better shit to do ... You again?",
			"2_3" => "Medium friendlyness, high agitation. Examples: Fuck off ... Get a life ... Screw off",
			"2_4" => "Medium friendlyness, maximum agitation. Examples: I'm gonna hurt you if you don't fuck off ... Piss off you fucker",
			"1_1" => "Low happyness, low agitation. Examples: What? ... Do you have anything better to do? ... Fuck off.",
			"1_2" => "Low happyness, medium agitation. Examples: Can't you see I don't want to talk to you? ... You better get outta here",
			"1_3" => "Low happyness, high agitation. Examples: Fuck you ... Eat a fat one",
			"1_4" => "Low happyness, maximum agitation. Examples: I'm going to kill your family ... I'll slit your throat",		
		),
		
		"drugs" => array(
			"yes_sell" => "Drug dealer agrees to sell drugs/seeds to the player. Examples: Ya man, whatcha want?",
			"yes_buy" => "Drug dealer agrees to buy drugs from the player. ",
			"no" => "NPC says no to sell/buy drugs/seeds to/from the player. Examples: You got the wrong guy man ... I quit that shit years ago",
		),
	);

	function numPhrases() {
		global $recordings;
		$cnt = 0;
		
		foreach( $recordings as $cat ) {
			$cnt += count( $cat );
		}
		
		return $cnt;
	}
	
?>