<?php
echo '<p class="results_statement">Your search for "<strong>'.$search_form_query.'</strong>" returned the following '.$search_type.':</p>'; 
$this->load->library('table');

$tmpl = array (
                    'table_open'          => '<table summary="Search results" id="search_results">',

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

$this->table->set_heading('documents Text', 'documents Name', 'Collection', 'View');

foreach($results as $row) { //set your rows here

    //$links  = form_checkbox('id', 'accept');
	
	//$button = '<button class="add_tab" value="'.$row['collection'].' '.$row['document_name'].'">View this resource</button>';
    
	$button = '<form class="document_form" name="form_'.$row['document_id'].'" method="post" action="document/'.$row['document_id'].'"><br />
				<input type="hidden" value="'.$row['document_id'].'" name="document_id">
				<input type="hidden" value="'.$row['document_name'].'" name="document_name">
				<input type="image" src="'.asset_url().'images/icons/document.png" value="submit" alt="View this document">
				</form>';
	
	//Added in to truncate the output until we can retrieve actual document snippets
	$truncated_text = character_limiter($row['document_text'], 100); 
	
	$this->table->add_row(
		$truncated_text,
        $row['document_name'],
        $row['collection_name'],
		$button
    );
}

//Calls the contents of the results db and populates a table
echo $this->table->generate(); 
?>
<!--Script to initialise the Table Sorter functionality-->
<script src="<?=asset_url()?>js/tablesorter/jquery.tablesorter.js" type="text/javascript"></script> 
<!--Script to call the addTab function from each "View this document" button-->
<script src="<?=asset_url()?>js/tabs_caller.js" type="text/javascript"></script>

