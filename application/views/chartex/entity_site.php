<?php
////////////////////////////////////////////////////////
//ENTITY_SITE
//RECEIVES DATA ABOUT A SITE ENTITY
//GENERATES HTML NESTED LISTS
////////////////////////////////////////////////////////

	if(isset($sameas_entities[0])){
		$title = $sameas_entities[0]['entity_type_name'].' '.$dm_entity_id;
	} else{
		$title = $possibly_entities[0]['entity_type_name'].' '.$dm_entity_id;
	}
	echo heading($title, 2);
	
	echo heading("Same as... ", 3);
	echo '<p>The following sites are definitely the same:</p>';
	
	$current_doc = '';
	$current_subject = '';
	$current_related = '';
		
	echo '<ul class="related_entities">';
	foreach($sameas_entities as $row) {
		//If it is a new document...
		if ($row['document_id'] != $current_doc) {
			//If it isn't the very first document...
			if ($current_doc != '') {
				//Close off all of the other document/subject/relation branches.
				echo '</ul></li></ul></li>';
			}
			//Echo the document, including the start of the sub-list.
			echo '<li><h3><a href="document/'.$row['document_id'].'" title="Document '.$row['document_id'].'" id="'.$row['document_id'].'~'.$row['document_name'].'" class="document_link">'.$row['document_name'].'</a> <span>('.$row['collection_name'].')</span></h3><ul>';
			//Set the current document variable to the current document.
			$current_doc = $row['document_id'];
			//Set the current subject back to null.
			$current_subject = '';
		} 
		//If it is a new subject...
		if ($row['entity_id'] != $current_subject) {
			//If it isn't the very first subject (within a document group)
			if ($current_subject != '') {
				//Close off all of the other subject/relation branches.
				echo '</ul></li>';
			}
			//Echo the subject, including the start of the sub-sub-list.
			echo '<li><a href="document/'.$row['document_id'].'" id="'.$row['document_id'].'~'.$row['document_name'].'~'.$row['entity_id'].'"><em>"'.$row['entity_name'].'"</em></a> <span>('.$row['entity_type_name'].')</span><ul>';
			//Set the current subect variable to the current subject.
			$current_subject = $row['entity_id'];
			//Set the current relation back to null.
			$current_related = '';
		} 
		//For each relation...
		//***Can remove the "same_as" if statement, once it has been removed from the query***
		if ($row['relation_name'] != "same_as") {	
			$relation_name = $row['relation_name'];
			$relation_name = str_replace("_", " ", $relation_name);
		
			if ($row['subject_entity_id'] == $row['entity_id']) {		
				echo '<li>...'.
				$relation_name.
				' <a href="document/'.$row['document_id'].'" id="'.$row['document_id'].'~'.$row['document_name'].'~'.$row['object_entity_id'].'"><em>'.$row['object_name'].'</em></a> <span>('.$row['object_type_name'].')</span></li>';
			}
			if ($row['object_entity_id'] == $row['entity_id']) {		
				echo '<li><a href="document/'.$row['document_id'].'" id="'.$row['document_id'].'~'.$row['document_name'].'~'.$row['object_entity_id'].'"><em>'.$row['object_name'].'</em></a> <span>('.$row['object_type_name'].') </span>'
			.$relation_name.
			' <a href="document/'.$row['document_id'].'" id="'.$row['document_id'].'~'.$row['document_name'].'~'.$row['object_entity_id'].'"><em>'.$row['object_name'].'</em></a> <span>('.$row['object_type_name'].')</span></li>';
			}
		}
				
		/*if ($row['related_entity_id'] != $current_related) {			
			//First, some special conditions...
			if ($row['relation_name'] == NULL) {
				$relation_name = "is related to";
			} else {			
				$relation_name = $row['relation_name'];
				$relation_name = str_replace("_", " ", $relation_name);
			}
			if ($row['related_entity_id'] == NULL) {
				$related_entity_id = $row['related_entity_id'];
			} else {			
				$related_entity_id = $row['related_entity_id'];
			}
			if ($row['related_text_fragment'] == NULL) {
				$related_text_fragment = "unknown transaction";
			} else {			
				$related_text_fragment = $row['related_text_fragment'];
			}
			if ($row['related_entity_type_name'] == NULL) {
				$related_entity_type_name = "";
			} else {			
				$related_entity_type_name = '('.$row['related_entity_type_name'].')';
			}	
		}*/
	}
	echo '</ul></li></ul></li>';
	echo '</ul>';
	
	echo heading("Possibly also... ", 3);
	echo '<p>This site possibly appears in the following transactions:</p>';
	$current_doc = '';
	$current_subject = '';
	$current_related = '';
	
	
	echo '<ul class="related_entities">';
	foreach($possibly_entities as $row) {
		//If it is a new document...
		if ($row['document_id'] != $current_doc) {
			//If it isn't the very first document...
			if ($current_doc != '') {
				//Close off all of the other document/subject/relation branches.
				echo '</ul></li></ul></li>';
			}
			//Echo the document, including the start of the sub-list.
			echo '<li><h3><a href="document/'.$row['document_id'].'" title="Document '.$row['document_id'].'" id="'.$row['document_id'].'~'.$row['document_name'].'" class="document_link">'.$row['document_name'].'</a></h3><ul>';
			//Set the current document variable to the current document.
			$current_doc = $row['document_id'];
			//Set the current subject back to null.
			$current_subject = '';
		} 
		//If it is a new subject...
		if ($row['entity_id'] != $current_subject) {
			//If it isn't the very first subject (within a document group)
			if ($current_subject != '') {
				//Close off all of the other subject/relation branches.
				echo '</ul></li>';
			}
			//Echo the subject, including the start of the sub-sub-list.
			echo '<li><a href="document/'.$row['document_id'].'" id="'.$row['document_id'].'~'.$row['document_name'].'~'.$row['entity_id'].'"><em>"'.$row['entity_name'].'"</em></a> <span>(Transaction)</span><ul>';
			//Set the current subect variable to the current subject.
			$current_subject = $row['entity_id'];
			//Set the current relation back to null.
			$current_related = '';
		} 
		//For each relation...
		//***Can remove the "same_as" if statement, once it has been removed from the query***
		/*if ($row['relation_name'] != "same_as") {	
			$relation_name = $row['relation_name'];
			$relation_name = str_replace("_", " ", $relation_name);
		
			if ($row['subject_entity_id'] == $row['entity_id']) {		
				echo '<li>...'.
				$relation_name.
				' <a href="document/'.$row['document_id'].'" id="'.$row['document_id'].'~'.$row['document_name'].'~'.$row['object_entity_id'].'"><em>'.$row['object_name'].'</em></a> <span>('.$row['object_type_name'].')</span></li>';
			}
			if ($row['object_entity_id'] == $row['entity_id']) {		
				echo '<li><a href="document/'.$row['document_id'].'" id="'.$row['document_id'].'~'.$row['document_name'].'~'.$row['object_entity_id'].'"><em>'.$row['object_name'].'</em></a> <span>('.$row['object_type_name'].') </span>'
			.$relation_name.
			' <a href="document/'.$row['document_id'].'" id="'.$row['document_id'].'~'.$row['document_name'].'~'.$row['object_entity_id'].'"><em>'.$row['object_name'].'</em></a> <span>('.$row['object_type_name'].')</span></li>';
			}
		}*/
	}
	echo '</ul>';

?>
<!--Script to call the entity features (top opener etc.)-->
<script src="<?=asset_url()?>js/entity.js" type="text/javascript"></script>