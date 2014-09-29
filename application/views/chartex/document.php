<?php
	$title = $entities[0]['document_name'].' <span>('.$entities[0]['collection_name'].')';
	echo heading($title, 2);
	echo heading('Document Text', 3);
	
	//this retrieve all document information and all entities for the document; each row is an entity in the document
	$newstring = $entities[0]['document_text'];
	for($i = 0; $i< count($entities); $i++)
	{
		$row = $entities[$i];
		$span_open = '<a href="entity/'.$row['entity_type_name'].'/'.$row['dm_entity_id'].'/'.$row['entity_id'].'" title="'.$row['entity_type_name'].' '.$row['dm_entity_id'].'" data-title="'.$row['entity_type_name'].' '.$row['dm_entity_id'].'" id="'.$row['entity_type_name'].'~'.$row['dm_entity_id'].'~'.$row['entity_id'].'" class="'.$row['entity_type_name'].'">';
		$span_close = '</a>';
		$newstring = substr_replace($newstring, $span_close, $row['text_end'], 0);
		if ($row['text_start'] > 0) {
			$newstring = substr_replace($newstring, $span_open, $row['text_start'], 0);
		} else {
			$newstring = $span_open . $newstring;
		}
	}
	echo '<p class="document_text">'.$newstring.'</p>';
		
	//form_open() - Creates opening form tag (with attributes)
	$attributes = array('id' => 'highlight_document_'.$entities[0]['document_id'], 'class' => 'highlight_form');
	echo form_open('document', $attributes);

	//form_fieldset() - Creates fieldset/legend fields (with attributes)
	$attributes = array('class' => 'highlight_fieldset');
	echo form_fieldset('Show markup:', $attributes);
	echo '<p>Highlight one or more of the following in the document text:</p>';
 
	//Calls a list of entity types from db and populates a checkbox (form_checkbox()) and label (form_label()) for each one
	foreach ($entity_types as $type_item):
	if($type_item['entity_type_name'] != "Document" && $type_item['entity_type_name'] != "PlaceRef" && $type_item['entity_type_name'] != "SiteRef") {
		echo '<span class="'.$type_item['entity_type_name'].' nobreak">';
		$data = array(
			'name'        => 'chk_entities[]',
			'id'          => 'chk_entities_'.$type_item['entity_type_name'],
			'value'       => $type_item['entity_type_name'],
			'checked'     => FALSE,
			);
		echo form_checkbox($data);
		
		$attributes = array(
			'class' => 'label_entities_'.$type_item['entity_type_name'],
			);
		echo form_label($type_item['entity_type_name'], 'chk_entities_'.$type_item['entity_type_name'], $attributes);
		echo '</span>';
	}
	endforeach;

	echo '<br />';
	$data = array(
		'name'        => 'checkall',
		'class'       => 'checkall',
		'checked'     => FALSE
		);
	
	echo form_checkbox($data);
	echo form_label('<strong>Select/Deselect all</strong>', 'checkall');
	//form_fieldset_close() - Generates a closing fieldset tag
	echo form_fieldset_close();
	//- Generates a closing form tag 
	echo form_close();
	
	//echo heading('documents Images', 2);
	//echo '<img src="'.asset_url().'images/'.$document['document_images'].'" alt="'.$document['document_images'].'">';
	//echo heading('Document Details', 3);
	//echo '<p><strong>Collection: </strong>'.$entities[0]['collection_name'].'</p>';
?>