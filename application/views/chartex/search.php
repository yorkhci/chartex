<div id="search_container">
<?php
////////////////////////////////////////////////////////
//SEARCH
//RECEIVES THE DATA ABOUT COLLECTIONS AND ENTITIES
//SETS UP THE VARIOUS SEARCH INPUTS
////////////////////////////////////////////////////////

//form_open() - Creates opening form tag (with attributes)
$attributes = array('id' => 'search_form');
echo form_open('chartex/results/documents', $attributes);

//form_fieldset() - Creates fieldset/legend fields (with attributes)
$attributes = array('id' => 'search_fieldset');
echo form_fieldset('Search:  <a title="Help" class="help_tooltip_1">What\'s this?</a>', $attributes);

//form_input() - Generates a standard text input field (with attributes)
$data = array('name' => 'search_form_query', 'id' => 'search_form_query');
echo form_input($data);

//form_submit() - Generates a standard submit button (with attributes)
$attributes = 'id = "search_form_query_submit"';
echo form_submit('search_form_query_submit', 'Search', $attributes);

//form_fieldset_close() - Generates a closing fieldset tag
echo form_fieldset_close();


//form_fieldset() - Creates fieldset/legend fields (with attributes)
$attributes = array('id' => 'collections_fieldset');
echo form_fieldset('Collections to search:', $attributes);
echo "<p>Select one or more of the following collections to include them in your search:</p>\n";
 
//Calls a list of collections from db and populates a checkbox (form_checkbox()) and label (form_label()) for each one
foreach ($collections as $collections_item):
	echo '<span class="nobreak">';
	$data = array(
		'name'        => 'chk_collections[]',
		'id'          => 'chk_collections'.$collections_item['collection_id'],
		'value'       => $collections_item['collection_name'],
		'checked'     => TRUE,
		);
	echo form_checkbox($data);
	
	$attributes = array(
    	'id' => 'label_collection_'.$collections_item['collection_id'],
		);
	echo form_label($collections_item['collection_name'], 'chk_collections'.$collections_item['collection_id'], $attributes);
	echo '</span>';
endforeach;

	echo '<br />';
	$data = array(
		'name'        => 'checkall_collections',
		'id'          => 'checkall_collections',
		'class'       => 'checkall',
		'checked'     => TRUE
		);
	
	echo form_checkbox($data);
	echo form_label('<strong>Select / Deselect all</strong>', 'checkall_collections');

//form_fieldset_close() - Generates a closing fieldset tag
echo form_fieldset_close();


//form_fieldset() - Creates fieldset/legend fields (with attributes)
$attributes = array('id' => 'advanced_fieldset');
echo form_fieldset('Entities to search for:', $attributes);
echo "<p>Select one or more of the following entity types to include them in your search:</p>";
 
//Calls a list of collections from db and populates a checkbox (form_checkbox()) and label (form_label()) for each one
foreach ($entities as $entities_item):
	if($entities_item['entity_type_id'] != 3 && $entities_item['entity_type_id'] != 9 && $entities_item['entity_type_id'] != 11) {
		echo '<span class="'.$entities_item['entity_type_name'].' nobreak">';
		$data = array(
			'name'        => 'chk_entities[]',
			'id'          => 'chk_entities'.$entities_item['entity_type_id'],
			'value'       => $entities_item['entity_type_name'],
			'checked'     => TRUE,
			);
		echo form_checkbox($data);
		
		$attributes = array(
			'id' => 'label_entity_'.$entities_item['entity_type_id'],
			);
		echo form_label($entities_item['entity_type_name'], 'chk_entities'.$entities_item['entity_type_id'], $attributes);
		echo '</span>';
	}
endforeach;

echo '<br />';
	$data = array(
		'name'        => 'checkall_entities',
		'id'          => 'checkall_entities',
		'class'       => 'checkall',
		'checked'     => TRUE
		);
	
	echo form_checkbox($data);
	
	echo form_label('<strong>Select / Deselect all</strong>', 'checkall_entities');

//form_fieldset_close() - Generates a closing fieldset tag
echo form_fieldset_close();
//- Generates a closing form tag 
echo form_close();
?>
<!--Placeholder for ajax search results-->
<div id="search_results_container">
	<div id="tabs">
<!--The placeholder for the tab menu-->
		<ul><li><a href="#documents">Document Results</a></li><li><a href="#entities">Entity Results</a></li></ul>
<!--The placeholder for the tab content-->
		<div id="documents">
        	<div class="ajaxloader">
    			<img src='<?=asset_url()?>images/structural/ajax-loader.gif' width="16" height="16" alt="" />
			</div> 
        </div>
		<div id="entities">
         	<div class="ajaxloader">
            	<img src='<?=asset_url()?>images/structural/ajax-loader.gif' width="16" height="16" alt="" />
            </div>
        </div>      
	</div>
</div>
</div>