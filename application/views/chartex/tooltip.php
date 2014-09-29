<?php
////////////////////////////////////////////////////////
//TOOLTIP
//RECEIVES THE DATA ABOUT A DOCUMENT
//CREATES A TOOLTIP
//INCLUDES A TRUNCATE FUNCTION TO SNIP THE TEXT 
////////////////////////////////////////////////////////

	//A handy little function to truncate text to a desired number of characters but not cut a word in half.
	function truncate($text, $chars = 25) {
		$text = $text." ";
		$text = substr($text,0,$chars);
		$text = substr($text,0,strrpos($text,' '));
		$text = $text."...";
		return $text;
	}
	
	echo '<div class="tooltip">';
	echo '<p><strong>Collection:</strong>';
	echo '<br />';
	echo $document['collection_name'];
	echo '<br />';
	echo '<strong>Document Text:</strong>';
	echo '<br />';
	//echo substr($document['document_text'], 0, 500);
	echo truncate($document['document_text'], 500);
	echo '<em>(continued)</em></p></div>';
?>