<?php
	if(isset($sameas_entities[0])){
		$title = $sameas_entities[0]['entity_type_name'].' '.$dm_entity_id;
	}
	else if(isset($possibly_entities[0])){
		$title = $possibly_entities[0]['entity_type_name'].' '.$dm_entity_id;
	}
	else {
		$title = "";
	}
	
	
	
	
	echo heading($title, 2);
		
	$current_doc = '';
	$current_subject = '';
	$current_related = '';
		
	foreach($sameas_entities as $row) {
		//If it is a new document...
		if ($row['document_id'] != $current_doc) {
			//Set the current document variable to the current document.
			$current_doc = $row['document_id'];
			//Set the current subject back to null.
			$current_subject = '';
			
			//JSON array
			$top_array['label'] = $row['document_name'].' <span class="entity_type">('.$row['collection_name'].')</span>';					
			$top_array['href'] = 'document/'.$row['document_id'];
			$top_array['title'] = 'Document '.$row['document_id'];
			$top_array['data-tabinfo'] = $row['document_id'].'~'.$row['document_name'];
			$top_array['id'] = 'document~'.$row['document_id'];
			$top_array['class'] = 'document_link';
			
			$top_array['children'] = array();
			$definitely_array[] = $top_array;		
		} 
		//If it is a new subject...
		if ($row['entity_id'] != $current_subject) {
			//Set the current subect variable to the current subject.
			$current_subject = $row['entity_id'];
			//Set the current relation back to null.
			$current_related = '';
			
			//JSON array
			$middle_array['label'] = '<span class="entity_name '.$row['entity_type_name'].'">'.$row['entity_name'].'</span> <span class="entity_type">('.$row['entity_type_name'].')</span>';	
			$middle_array['href'] = 'document/'.$row['document_id'];
			$middle_array['title'] = 'Document '.$row['document_id'];
			$middle_array['data-tabinfo'] = $row['document_id'].'~'.$row['document_name'].'~'.$row['entity_id'];
			$middle_array['id'] = 'entity~'.$row['entity_id'];
			$middle_array['class'] = 'entity_link';
			
			$middle_array['children'] = array();
			end($definitely_array);
			
			$middle_key = key($definitely_array);
			$definitely_array[$middle_key]['children'][] = $middle_array; 	
		}
		//For each relation...
		//***Can remove the "same_as" if statement, once it has been removed from the query***
		if ($row['relation_name'] != "same_as") {	
			$relation_name = $row['relation_name'];
			$relation_name = str_replace("_", " ", $relation_name);
		
			if ($row['subject_entity_id'] == $row['entity_id']) {						
				//JSON array
				end($definitely_array[$middle_key]['children']);
				$bottom_key = key($definitely_array[$middle_key]['children']);				
				
				$bottom_array['label'] = '...'.$relation_name.' <span class="entity_name '.$row['object_type_name'].'">'.$row['object_name'].'</span> <span class="entity_type">('.$row['object_type_name'].')</span>';	
				$bottom_array['href'] = 'document/'.$row['document_id'];
				$bottom_array['title'] = 'Document '.$row['document_id'];
				$bottom_array['data-tabinfo'] = $row['document_id'].'~'.$row['document_name'].'~'.$row['object_entity_id'];
				$bottom_array['id'] = 'entity~'.$row['object_entity_id'];
				$bottom_array['class'] = 'entity_link';
				$definitely_array[$middle_key]['children'][$bottom_key]['children'][] = $bottom_array;		 	
			}
			if ($row['object_entity_id'] == $row['entity_id']) {				
			//JSON array
				end($definitely_array[$middle_key]['children']);
				$bottom_key = key($definitely_array[$middle_key]['children']);				
				
				$bottom_array['label'] = '...'.$relation_name.' <span class="entity_name '.$row['object_type_name'].'">'.$row['object_name'].'</span> <span class="entity_type">('.$row['object_type_name'].')</span>';	
				$bottom_array['href'] = 'document/'.$row['document_id'];
				$bottom_array['title'] = 'Document '.$row['document_id'];
				$bottom_array['data-tabinfo'] = $row['document_id'].'~'.$row['document_name'].'~'.$row['object_entity_id'];
				$bottom_array['id'] = 'entity~'.$row['object_entity_id'];
				$bottom_array['class'] = 'entity_link';
				$definitely_array[$middle_key]['children'][$bottom_key]['children'][] = $bottom_array;		 	
			}
		}	
	}

	$current_doc = '';
	$current_subject = '';
	$current_related = '';
	
	foreach($possibly_entities as $row) {
		//If it is a new document...
		if ($row['document_id'] != $current_doc) {
			//Set the current document variable to the current document.
			$current_doc = $row['document_id'];
			//Set the current subject back to null.
			$current_subject = '';
			
			//JSON array
			$top_array['label'] = '...<span class="entity_name Transaction">'.$row['entity_name'].'</span>... <span class="entity_type">('.$row['document_name'].')</span>
			<span class="confirmdeny"><a href="" title="Confirm" class="confirm">Confirm</a> <a href="" title="Deny" class="deny">Deny</a></span>';
			//.' <span class="entity_type">('.$row['collection_name'].')</span>';					
			$top_array['href'] = 'document/'.$row['document_id'];
			$top_array['title'] = 'Document '.$row['document_id'];
			$top_array['data-tabinfo'] = $row['document_id'].'~'.$row['document_name'].'~'.$row['entity_id'];
			$top_array['id'] = 'entity~'.$row['entity_id'];
			$top_array['class'] = 'entity_link';
			
			//$top_array['children'] = array();
			$maybe_array[] = $top_array;						
		} 
		//If it is a new subject...
		/*if ($row['entity_id'] != $current_subject) {
			//Set the current subect variable to the current subject.
			$current_subject = $row['entity_id'];
			//Set the current relation back to null.
			$current_related = '';
			
			//JSON array
			//$middle_array['label'] = $row['entity_name'].' <span class="entity_type">(Transaction)</span>';	
			//$middle_array['href'] = 'document/'.$row['document_id'];
			//$middle_array['title'] = 'Document '.$row['document_id'];
			//$middle_array['data-tabinfo'] = $row['document_id'].'~'.$row['document_name'].'~'.$row['entity_id'];
			//$middle_array['id'] = 'entity~'.$row['entity_id'];
			//$middle_array['class'] = 'entity_link';
			
			//$middle_array['children'] = array();
			//end($maybe_array);
			
			//$middle_key = key($maybe_array);
			//$maybe_array[$middle_key]['children'][] = $middle_array; 		
		} */
	}

if (isset($definitely_array)){	
	echo heading("Same as... ", 3);
	
	$entity_def_json = json_encode($definitely_array);			
	echo '<script>';
	echo 'entity_def_json['.$dm_entity_id.'] = '.$entity_def_json.';';
	echo '</script>';
	echo '<p>The following documents contain references to entities that are <em>definitely</em> the same '.strtolower($sameas_entities[0]['entity_type_name']).':</p>';
	echo '<div id="definitely_tree_'.$dm_entity_id.'" class="related_entities"></div>';
}
if (isset($maybe_array)){
	echo heading("Possibly also... ", 3);

	$entity_mayb_json = json_encode($maybe_array);			
	echo '<script>';
	echo 'entity_mayb_json['.$dm_entity_id.'] = '.$entity_mayb_json.';';
	echo '</script>';	
	echo '<p>The '.strtolower($sameas_entities[0]['entity_type_name']).' <em>possibly</em> appears in the following transactions:</p>';
	echo '<div id="maybe_tree_'.$dm_entity_id.'" class="related_entities"></div>';

}
?>