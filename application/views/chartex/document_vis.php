<?php
////////////////////////////////////////////////////////
//DOCUMENT VISUALISATION
//RECEIVES THE DATA FOR THE DOCUMENT VISUALISATION
//ENCODES IT IN JSON
//PREPARES THE STRUCTURE FOR THE DOCUMENT VISUALISATION
////////////////////////////////////////////////////////
	
	echo heading('Transactions Visualisation', 3);
	echo '<p>Below is a visualisation of the transactions that are described in this document:</p>';
	$current_trans = '';
	$current_rel = '';
	$current_subject = '';
	$current_related = '';
	
	foreach($document_vis as $row) {
		//If it is a transaction entity...
		if ($row['entity_type_id'] == 12) {		
			$tree_root['id'] = "doc_".$row['document_id'];
			$tree_root['name'] = "Document";					
			$tree_root['children'] = array();
			break;
		}
	}
	foreach($document_vis as $row) {
		//If it is a transaction...
		if ($row['entity_type_id'] == 12) {	
			//If it is a new transaction...
			if ($row['entity_id'] != $current_trans) {
				//Create a new transaction branch, hang it off the document root, and reset the relation branch variable
				$current_trans = $row['entity_id'];							
				$trans_array['id'] = "doc_".$row['document_id']."_trans_".$row['entity_id'];
				$trans_array['name'] = $row['object_name'];					
				$trans_array['children'] = array();
				$tree_root['children'][] = $trans_array;
				$current_rel = '';	
			}	
			//If it is a new relationship type...
			if ($row['relation_id'] != $current_rel) {			
				//Create a new relation branch, hang it off the transaction branch, and reset the subject branch variable
				$current_rel = $row['relation_id'];		
				$rel_array['id'] = "doc_".$row['document_id']."_trans_".$row['entity_id']."_rel_".$row['relation_id'];
				$rel_array['name'] = $row['relation_name'];					
				$rel_array['children'] = array();				
				end($tree_root['children']);	
				$relation_key = key($tree_root['children']);
				$tree_root['children'][$relation_key]['children'][] = $rel_array; 	
				$current_subject = '';		
			}
			//If it is a new subject...
			if ($row['subject_entity_id'] != $current_subject) {
				//Create a new subject branch, hang it off the relation branch, and reset the related branch variable
				$current_subject = $row['subject_entity_id'];
				$subj_array['id'] = "doc_".$row['document_id']."_trans_".$row['entity_id']."_rel_".$row['relation_id'].'_subj_'.$row['subject_entity_id'];
				$subj_array['name'] = $row['subject_name'];	
				$subj_array['children'] = array();
				end($tree_root['children'][$relation_key]['children']);	
				$subj_key = key($tree_root['children'][$relation_key]['children']);
				$tree_root['children'][$relation_key]['children'][$subj_key]['children'][] = $subj_array; 		
				$current_related = '';			
			}
		}
	}
	
	$document_json = json_encode($tree_root);
	
	echo '<div id="document_log_'.$row['document_id'].'" class="vis_log"></div>';
	echo '<div id="document_vis_'.$row['document_id'].'" class="vis_container"></div>';
	echo '<script>';
	echo 'document_json['.$row['document_id'].'] = '.$document_json.';';
	echo '</script>';
?>