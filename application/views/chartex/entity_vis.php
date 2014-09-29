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
	echo heading('Person Visualisation', 3);
	echo '<p>Below is a visualisation of the entities related to '.$title.':</p>';
	
	//The following is in case we need a graph network
	/*$root_array_item = '';
	$current_outer_item = '';
	$current_inner_item = '';
	
	$root_array['id'] = "node0";
	$root_array['name'] = "";
	$root_array['data']['$type'] = "none";
	
	foreach($possibly_entities as $row) {
		if ($root_array_item != $row['entity_id']) {
			$root_array['adjacencies'][] = array('nodeTo' => $row['entity_id'],'data' => array('$type' => 'none'));
			$root_array_item = $row['entity_id'];
		}
	}
	$node_top_array[] = $root_array;
	
	foreach($possibly_entities as $row) {	
		if ($row['entity_id'] != $current_outer_item) {
			$node_array['id'] = $row['entity_id'];
			$node_array['name'] = $row['entity_name'];					
			$node_array['adjacencies'] = array();
			
			$current_inner_item = $row['entity_id'];
			$current_outer_item = $row['entity_id'];
			foreach($possibly_entities as $inner_row) {
				if ($inner_row['entity_id'] != $current_inner_item && $inner_row['entity_id'] != $current_outer_item) {
					$inner_node_array['nodeTo'] = $inner_row['entity_id'];	
					$node_array['adjacencies'][] = $inner_node_array;
					$current_inner_item = $inner_row['entity_id'];
				}
			}
			$node_top_array[] = $node_array;
		}			
	}*/
	//$node_top_array = json_encode($node_top_array);
	
	$current_doc = '';
	$current_subj = '';
	$current_rel = '';
	
	$entity_tree_root['id'] = "dm_".$dm_entity_id;
	$entity_tree_root['name'] = $title;					
	$entity_tree_root['children'][] = array('id' => "dm_".$dm_entity_id."_0",'name' => "Possibly Also...",'children' => array());
	$entity_tree_root['children'][] = array('id' => "dm_".$dm_entity_id."_1",'name' => "Same As...",'children' => array());
		
	
	foreach($possibly_entities as $row) {
		//If it is a new document...
		if ($row['document_id'] != $current_doc) {
			//Create a new document branch, hang it off the root, and reset the subject branch variable
			$current_doc = $row['document_id'];						
			$doc_array['id'] = 'dm_'.$dm_entity_id.'_pa_doc_'.$row['document_id'];
			$doc_array['name'] = $row['document_name'];					
			$doc_array['children'] = array();
			$entity_tree_root['children'][0]['children'][] = $doc_array;
			$current_subj = '';	
		}	
		//If it is a new subject...
		if ($row['entity_id'] != $current_subj) {			
			//Create a new subject branch, hang it off the document branch, and reset the relation branch variable
			$confidence = round($row['confidence'], 2) * 100; 
			
			$current_subj = $row['entity_id'];	
			$rel_array['id'] = 'dm_'.$dm_entity_id.'_pa_doc_'.$row['document_id'].'_subj_'.$row['entity_id'];
			$rel_array['name'] = $row['entity_name'].'<br /><span class="confidence">(Confidence = '.$confidence.'%)</span>';				
			$rel_array['children'] = array();				
			end($entity_tree_root['children'][0]['children']);	
			$subj_key = key($entity_tree_root['children'][0]['children']);
			$entity_tree_root['children'][0]['children'][$subj_key]['children'][] = $rel_array; 	
			$current_rel = '';	
		}
		//If it is a new relation...
		/*if ($row['subject_entity_id'] != $current_rel) {
			//Create a new relation branch, and hang it off the subject branch
			$relation_name = $row['relation_name'];
			$relation_name = str_replace("_", " ", $relation_name);
			
			if ($row['subject_entity_id'] == $row['entity_id']) {
				$rel_array['id'] = 'dm_'.$dm_entity_id.'_pa_doc_'.$row['document_id'].'_subj_'.$row['entity_id'].'_rel_'.$row['object_entity_id'];
				$rel_array['name'] = '...'.$relation_name.' '.$row['object_name'].' ('.$row['object_type_name'].')';	
				$rel_array['children'] = array();
				end($entity_tree_root['children'][0]['children'][$subj_key]['children']);
				$rel_key = key($entity_tree_root['children'][0]['children'][$subj_key]['children']);				
				$entity_tree_root['children'][0]['children'][$subj_key]['children'][$rel_key]['children'][] = $rel_array;	
			}
			if ($row['object_entity_id'] == $row['entity_id']) {				
			//JSON array
				$rel_array['id'] = 'dm_'.$dm_entity_id.'_pa_doc_'.$row['document_id'].'_subj_'.$row['entity_id'].'_rel_'.$row['subject_entity_id'];
				$rel_array['name'] = '...'.$relation_name.' '.$row['subject_name'].' ('.$row['subject_type_name'].')';	
				$rel_array['children'] = array();
				end($entity_tree_root['children'][0]['children'][$subj_key]['children']);
				$rel_key = key($entity_tree_root['children'][0]['children'][$subj_key]['children']);				
				$entity_tree_root['children'][0]['children'][$subj_key]['children'][$rel_key]['children'][] = $rel_array;		 	
			}				
		}*/
	}
	
	foreach($sameas_entities as $row) {
		//If it is a new document...
		if ($row['document_id'] != $current_doc) {
			//Create a new document branch, hang it off the root, and reset the subject branch variable
			$current_doc = $row['document_id'];						
			$doc_array['id'] = 'dm_'.$dm_entity_id.'_sa_doc_'.$row['document_id'];
			$doc_array['name'] = $row['document_name'];					
			$doc_array['children'] = array();
			$entity_tree_root['children'][1]['children'][] = $doc_array;
			$current_subj = '';	
		}	
		//If it is a new subject...
		if ($row['entity_id'] != $current_subj) {			
			//Create a new subject branch, hang it off the document branch, and reset the relation branch variable
			$current_subj = $row['entity_id'];	
			$rel_array['id'] = 'dm_'.$dm_entity_id.'_sa_doc_'.$row['document_id'].'_subj_'.$row['entity_id'];
			$rel_array['name'] = $row['entity_name'];				
			$rel_array['children'] = array();				
			end($entity_tree_root['children'][1]['children']);	
			$subj_key = key($entity_tree_root['children'][1]['children']);
			$entity_tree_root['children'][1]['children'][$subj_key]['children'][] = $rel_array; 	
			$current_rel = '';	
		}
		//If it is a new relation...
		/*if ($row['subject_entity_id'] != $current_rel) {
			//Create a new relation branch, and hang it off the subject branch
			$relation_name = $row['relation_name'];
			$relation_name = str_replace("_", " ", $relation_name);
			
			if ($row['subject_entity_id'] == $row['entity_id']) {
				$rel_array['id'] = 'dm_'.$dm_entity_id.'_sa_doc_'.$row['document_id'].'_subj_'.$row['entity_id'].'_rel_'.$row['object_entity_id'];
				$rel_array['name'] = '...'.$relation_name.' '.$row['object_name'].' ('.$row['object_type_name'].')';	
				$rel_array['children'] = array();
				end($entity_tree_root['children'][1]['children'][$subj_key]['children']);
				$rel_key = key($entity_tree_root['children'][1]['children'][$subj_key]['children']);				
				$entity_tree_root['children'][1]['children'][$subj_key]['children'][$rel_key]['children'][] = $rel_array;	
			}
			if ($row['object_entity_id'] == $row['entity_id']) {				
			//JSON array
				$rel_array['id'] = 'dm_'.$dm_entity_id.'_sa_doc_'.$row['document_id'].'_subj_'.$row['entity_id'].'_rel_'.$row['subject_entity_id'];
				$rel_array['name'] = '...'.$relation_name.' '.$row['subject_name'].' ('.$row['subject_type_name'].')';	
				$rel_array['children'] = array();
				end($entity_tree_root['children'][1]['children'][$subj_key]['children']);
				$rel_key = key($entity_tree_root['children'][1]['children'][$subj_key]['children']);				
				$entity_tree_root['children'][1]['children'][$subj_key]['children'][$rel_key]['children'][] = $rel_array;		 	
			}				
		}*/
	}
	
	$entity_json = json_encode($entity_tree_root);
	
	echo '<div id="entity_log_'.$dm_entity_id.'" class="vis_log"></div>';
	echo '<div id="entity_vis_'.$dm_entity_id.'" class="vis_container"></div>';
	
	echo '<script>';
	echo 'entity_json['.$dm_entity_id.'] = '.$entity_json.';';
	echo '</script>';
	
?>
