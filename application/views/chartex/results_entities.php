<?php
if (!$results) {
    echo '<p class="results_statement">Your search for "<strong>'.$search_form_query.'</strong>" returned no ' .$search_type.'.</p>'; 
} else {
	echo '<p class="results_statement">Your search for "<strong>'.$search_form_query.'</strong>" in these collections:';
	echo '<ul class="collections_list"><li>';
	if (!$chk_collections){
		echo 'No collections selected (returned all instead)</li>';
	} else {
		$i = 1;
		foreach ($chk_collections as $collection) {
    		if ( $i != count($chk_collections) ) {
    			echo $collection.', ';
 			} else {
    			echo $collection.'</li>';
  			}
  			$i++;
		}
	}
	echo '</ul>';
	echo '<p class="results_statement">returned the following entities:</p>';
	 
	$this->load->library('table');
	
	$tmpl = array (
						'table_open'          => '<table summary="Search results" id="entity_search_results" class="tablesorter">',
	
						'heading_row_start'   => '<tr>',
						'heading_row_end'     => '</tr>',
						'heading_cell_start'  => '<th>',
						'heading_cell_end'    => '</th>',
	
						'row_start'           => '<tr class="odd">',
						'row_end'             => '</tr>',
						'cell_start'          => '<td>',
						'cell_end'            => '</td>',
	
						'row_alt_start'       => '<tr class="even">',
						'row_alt_end'         => '</tr>',
						'cell_alt_start'      => '<td>',
						'cell_alt_end'        => '</td>',
	
						'table_close'         => '</table>'
				  );
	
	$this->table->set_template($tmpl); 
	
	$this->table->set_heading('Entity Extract', 'Entity Type', 'Document Name', 'Collection');
	
	foreach($results as $row) {			
		$start_padding = 80;
		$end_padding = 80;
		$snip_start = true;
		$snip_end = true;
		
		//If the entity starts at less than 40 characters from the beginning of the document...
		if ($row['text_start'] < $start_padding) {
			//...make the start padding equal to the number of characters between the entity and the start.
			$start_padding = $row['text_start'];
			$snip_start = false;
		}
		//If the entity ends at less than 40 characters from the end of the document...
		if (($row['document_char_count'] - $row['text_end']) < $end_padding) {
			//...make the end padding equal to the number of characters between the entity and the end.
			$end_padding = ($row['document_char_count'] - $row['text_end']);
			$snip_end = false;
		}	
		//Add in the strong tags
		$document_text = substr_replace($row['document_text'], '</strong>', $row['text_end'], 0);
		$document_text = substr_replace($document_text, '<strong>', $row['text_start'], 0);
		
		//The start position is equal to the text_start minus the start_padding
		$text_start = $row['text_start'] - $start_padding;
		//The string length is equal to the text_length plus the start and end padding, plus 17 for the STRONG tags
		$text_length = $row['text_length'] + $start_padding + $end_padding + 17;
		
		//Snip the document text to the requisite length			
		$snippet = substr($document_text,$text_start,$text_length);	
		
		//If the entity was not at the very start or end of the document, snip off any words that have been split
		if ($snip_end == true) {		
			$snippet = preg_replace('/ [^ ]*$/', ' ...', $snippet);
		}
		if ($snip_start == true) {	
			$snippet = preg_replace('/^[^ ]*/', '...', $snippet);
		}
		
		$snippet_data = '<a href="entity/'.$row['entity_type_name'].'/'.$row['dm_entity_id'].'/'.$row['entity_id'].'" title="'.$row['entity_type_name'].' '.$row['dm_entity_id'].'" id="'.$row['entity_type_name'].'~'.$row['dm_entity_id'].'~'.$row['entity_id'].'" class="entity_link '.$row['entity_type_name'].'">'.$snippet.'</a>';
		$snippet_cell = array('data' => $snippet_data, 'class' => 'snippet_text');
		
		$this->table->add_row(
			$snippet_cell,
			$row['entity_type_name'],
			'<a href="document/'.$row['document_id'].'" title="Document '.$row['document_id'].'" id="'.$row['document_id'].'~'.$row['document_name'].'" class="document_link">'.$row['document_name'].'</a>',
			$row['collection_name']
		);
	}
	//Calls the contents of the results db and populates a table
	echo $this->table->generate(); 
}
    echo '<div id="entity_pager" class="pager">
			<form>
  			<img src="'.asset_url().'images/structural/begin.png" class="first"/>
  			<img src="'.asset_url().'images/structural/rw.png" class="prev"/>
  			<input type="text" class="pagedisplay"/>
  			<img src="'.asset_url().'images/structural/ff.png" class="next"/>
  			<img src="'.asset_url().'images/structural/end.png" class="last"/>
  			<select class="pagesize">
    			<option value="5">5</option>
    			<option selected="selected" value="10">10</option>
    			<option value="20">20</option>
  			</select>
			</form>
			</div>';
?>