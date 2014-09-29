<?php
class Chartex_model extends CI_Model {

	public function __construct()
	{
		$this->load->database();
	}
	//Gets the list of collections to populate the search form.
	public function get_collections()
	{		
		$query = $this->db->get('collections');
		return $query->result_array();
	}
	//Gets the list of entity types to populate the search form.
	public function get_entities()
	{	
		$query = $this->db->get('entity_types');
		return $query->result_array();
	}
	//Gets the data for the results table for documents.
	public function get_results_documents($search_form_query, $chk_collections, $chk_entities)
	{			
		$this->db->select('documents.document_id, documents.document_name, documents.document_text, collections.collection_name');
		$this->db->from('documents');
		$this->db->join('collections', 'documents.collection_id = collections.collection_id');
		$this->db->like('documents.document_text', $search_form_query);
		$this->db->where_in('collections.collection_name', $chk_collections);
		
		$query = $this->db->get();
			
		return $query->result_array();
	}
	//Gets the data for the results table for entities.
	public function get_results_entities($search_form_query, $chk_collections, $chk_entities)
	{				
		//$this->db->select('SUBSTRING( documents.document_text FROM entities.text_start -10 FOR entities.text_length +20 ),
		//$this->db->select('SUBSTRING( documents.document_text FROM entities.text_start +1 FOR entities.text_length ) as snippet,
		$this->db->select('
			documents.document_text,
			documents.document_char_count,
			entities.text_start,
			entities.text_end,
			entities.text_length,
			entities.entity_id,
			entities.entity_name,
			entities.entity_type_id,
			entity_types.entity_type_name,
			documents.document_id,
			documents.document_name,
			collections.collection_name,
			dm_entities.dm_entity_id');
		$this->db->from('documents');
		$this->db->join('entities', 'entities.document_id = documents.document_id');
		$this->db->join('collections', 'collections.collection_id = documents.collection_id');
		$this->db->join('entity_types', 'entity_types.entity_type_id = entities.entity_type_id');		
		$this->db->join('dm_entities', 'dm_entities.entity_id = entities.entity_id');
		$this->db->like('entities.entity_name', $search_form_query);		
		$this->db->where_in('collections.collection_name', $chk_collections);
		$this->db->where_in('entity_types.entity_type_name', $chk_entities);
		
		$query = $this->db->get();
		return $query->result_array();
	}	
	//Gets the data for a document.
	public function get_document($document_id)
	{
		$this->db->select('documents.document_id, documents.document_name, documents.document_text, collections.collection_name');
		$this->db->from('documents');
		$this->db->join('collections', 'documents.collection_id = collections.collection_id');
		$this->db->where(array('documents.document_id' => $document_id));
		$query = $this->db->get();
		
		return $query->row_array();
	}
	//Gets the data on the entities within a document (for highlighting).
	public function get_document_entities($document_id)
	{
		$this->db->select('documents.document_id,
		documents.document_name,
		documents.document_text,
		collections.collection_id,
		collections.collection_name,
		entities.entity_id,
		entities.text_fragment,
		entities.text_start,
		entities.text_end,
		entities.text_length,
		entity_types.entity_type_name,
		dm_entities.dm_entity_id');
		$this->db->from('documents');
		$this->db->join('collections', 'documents.collection_id = collections.collection_id');
		$this->db->join('entities', 'documents.document_id = entities.document_id');
		$this->db->join('entity_types', 'entities.entity_type_id = entity_types.entity_type_id');
		$this->db->join('dm_entities', 'dm_entities.entity_id = entities.entity_id');
		$this->db->where(array('documents.document_id' => $document_id));
		$this->db->order_by("entities.text_end", "desc"); 
		$query = $this->db->get();
		
		return $query->result_array();
	}
	
	public function get_document_vis_alt($entity_id)
	{		
		$query = $this->db->query('
		SELECT relations11.*, entity_types.entity_type_name AS object_type_name FROM entity_types
		JOIN
			(SELECT relations10.*, entities.entity_name AS object_name, entities.text_start AS object_text_start, entities.text_end AS object_text_end, entities.entity_type_id AS object_type_id FROM entities
			JOIN
				(SELECT relations9.*, relations.relation_name AS relation_name FROM relations
				JOIN
					(SELECT relations8.*, entity_types.entity_type_name AS subject_type_name from entity_types
					JOIN
						(SELECT relations7.*, entities.entity_name as subject_name, entities.text_start AS subject_text_start, entities.text_end AS subject_text_end, entities.entity_type_id AS subject_type_id FROM entities
						JOIN
							(SELECT relations6.* FROM entity_relations
							RIGHT JOIN
								(SELECT relations5.*, entity_relations.subject_entity_id, entity_relations.relation_id, entity_relations.object_entity_id FROM entity_relations
								RIGHT JOIN
									(SELECT relations4.*, entity_types.entity_type_name FROM entity_types
									JOIN
										(SELECT collections.collection_name, relations3.* FROM collections
										JOIN	
											(SELECT documents.collection_id, documents.document_name, relations2.* FROM documents 
											JOIN
												(SELECT entities.document_id, relations1.*, entities.entity_name, entities.text_start, entities.text_end, entities.entity_type_id FROM entities
												JOIN
													(SELECT dm_entities.entity_id from dm_entities 
													JOIN
														(SELECT dm_entities.dm_entity_id FROM dm_entities
														WHERE dm_entities.entity_id = '.$entity_id.') relations0
													ON dm_entities.dm_entity_id = relations0.dm_entity_id) AS relations1
												ON entities.entity_id = relations1.entity_id) AS relations2
											ON documents.document_id  = relations2.document_id) AS relations3
										ON collections.collection_id = relations3.collection_id) AS relations4
									ON entity_types.entity_type_id = relations4.entity_type_id) AS relations5
								ON entity_relations.subject_entity_id = relations5.entity_id) AS relations6
							ON entity_relations.object_entity_id = relations6.entity_id) AS relations7
						ON entities.entity_id = relations7.subject_entity_id) AS relations8
					ON entity_types.entity_type_id = relations8.subject_type_id) AS relations9
				ON relations.relation_id = relations9.relation_id) AS relations10
			ON entities.entity_id = relations10.object_entity_id) AS relations11
		ON entity_types.entity_type_id = relations11.object_type_id;');
		return $query->result_array();
	}
	
	public function get_document_vis($document_id)
	{		
		$query = $this->db->query('
		SELECT collections.collection_name, relations10.* FROM collections
RIGHT JOIN
	(SELECT entity_types.entity_type_name, relations9.* FROM entity_types
	RIGHT JOIN
		(SELECT entities.entity_type_id, relations8.* FROM entities
		RIGHT JOIN
			(SELECT dm_entities.dm_entity_id, relations7.* FROM dm_entities
			RIGHT JOIN
				(SELECT documents.collection_id, documents.document_name, relations6.* FROM documents
				RIGHT JOIN
					(SELECT relations5.*, entity_types.entity_type_name AS object_type_name FROM entity_types
					RIGHT JOIN
						(SELECT relations4.document_id, relations4.entity_id, relations4.subject_entity_id, relations4.subject_name, relations4.subject_type_id, entity_types.entity_type_name AS subject_type_name, relations4.relation_id, relations4.relation_name, relations4.object_entity_id, relations4.object_name, relations4.object_type_id FROM entity_types
						RIGHT JOIN
							(SELECT entities.document_id, relations3.*, entities.entity_name AS object_name, entities.entity_type_id AS object_type_id FROM entities
							RIGHT JOIN
								(SELECT relations2.entity_id, relations2.relation_id, relations2.subject_entity_id, entities.entity_name AS subject_name, entities.entity_type_id AS subject_type_id, relations2.relation_name, relations2.object_entity_id FROM entities
								RIGHT JOIN
									(SELECT relations1.entity_id, relations1.relation_id, relations1.subject_entity_id, relations.relation_name, relations1.object_entity_id FROM relations
									RIGHT JOIN
										(SELECT query2.*, entity_relations.* FROM entity_relations
										RIGHT JOIN
											(SELECT documents.document_name, query1.* FROM documents
											RIGHT JOIN
												(SELECT entities.* FROM entities WHERE entities.document_id = '.$document_id.') AS query1
											ON documents.document_id = query1.document_id) AS query2
										ON entity_relations.subject_entity_id = query2.entity_id OR entity_relations.object_entity_id = query2.entity_id) AS relations1
									ON relations.relation_id = relations1.relation_id) AS relations2
								ON entities.entity_id = relations2.subject_entity_id) AS relations3
							ON entities.entity_id = relations3.object_entity_id) AS relations4
						ON entity_types.entity_type_id = relations4.subject_type_id) AS relations5
					ON entity_types.entity_type_id = relations5.object_type_id) AS relations6
				ON documents.document_id = relations6.document_id) AS relations7
			ON dm_entities.entity_id = relations7.entity_id) AS relations8
		ON entities.entity_id = relations8.entity_id) AS relations9
	ON entity_types.entity_type_id = relations9.entity_type_id) AS relations10
ON collections.collection_id = relations10.collection_id
ORDER BY document_id, entity_type_id, entity_id, relation_id, subject_type_id, object_type_id;');
		return $query->result_array();
	}
	
	
	
	//Gets all entities belonging DM_ENTITY_ID and returns all of the their relations with appropriate information.
	/*public function get_entity($dm_entity_id)
	{		
		$query = $this->db->query('SELECT relations_temp4.*, dm_entities.dm_entity_id AS related_dm_entity_id FROM dm_entities
		RIGHT JOIN
		(SELECT relations_temp3.*,  entity_types.entity_type_name AS related_entity_type_name FROM entity_types
		RIGHT JOIN
		(SELECT relations_temp2.*, entities.text_fragment AS related_text_fragment, entities.entity_type_id AS related_entity_type_id FROM entities
		RIGHT JOIN
		(SELECT relations_temp1.*, relations.relation_name FROM relations
		RIGHT JOIN
		(SELECT entity_set.*, entity_relations.object_entity_id AS related_entity_id, entity_relations.relation_id AS relation_id FROM entity_relations
		RIGHT JOIN
		(SELECT documents.document_id, documents.document_name, entities.entity_id AS subject_entity_id, entities.text_fragment AS subject_text_fragment, entity_types.entity_type_name AS subject_type_name FROM documents 
		JOIN entities ON documents.document_id = entities.document_id
		JOIN entity_types ON entities.entity_type_id = entity_types.entity_type_id
		WHERE entities.entity_id IN 
		(SELECT dm_entities.entity_id AS subject_entity_id FROM dm_entities WHERE dm_entities.dm_entity_id IN 
		(SELECT dm_person_candidates.object_dm_entity_id AS connected from dm_person_candidates WHERE dm_person_candidates.subject_dm_entity_id = '.$dm_entity_id.'
		UNION DISTINCT
		SELECT dm_person_candidates.subject_dm_entity_id AS connected from dm_person_candidates WHERE dm_person_candidates.object_dm_entity_id = '.$dm_entity_id.'
		UNION DISTINCT
		SELECT ('.$dm_entity_id.')))) AS entity_set
		ON entity_relations.subject_entity_id = entity_set.subject_entity_id AND entity_relations.relation_id != 26) AS relations_temp1
		ON relations_temp1.relation_id = relations.relation_id) AS relations_temp2
		ON relations_temp2.related_entity_id = entities.entity_id) AS relations_temp3
		ON relations_temp3.related_entity_type_id = entity_types.entity_type_id) AS relations_temp4
		ON relations_temp4.related_entity_id = dm_entities.entity_id');
		return $query->result_array();
	}*/
	
	public function get_sameas_entity($entity_id)
	{		
		$query = $this->db->query('
		SELECT relations11.*, entity_types.entity_type_name AS object_type_name FROM entity_types
RIGHT JOIN
	(SELECT relations10.*, entities.entity_name AS object_name, entities.text_start AS object_text_start, entities.text_end AS object_text_end, entities.entity_type_id AS object_type_id FROM entities
	RIGHT JOIN
		(SELECT relations9.*, relations.relation_name AS relation_name FROM relations
		RIGHT JOIN
			(SELECT relations8.*, entity_types.entity_type_name AS subject_type_name from entity_types
			RIGHT JOIN
				(SELECT relations6.*, entities.entity_name as subject_name, entities.text_start AS subject_text_start, entities.text_end AS subject_text_end, entities.entity_type_id AS subject_type_id FROM entities
				RIGHT JOIN
						(SELECT relations5.*, entity_relations.subject_entity_id, entity_relations.relation_id, entity_relations.object_entity_id FROM entity_relations
						RIGHT JOIN
							(SELECT relations4.*, entity_types.entity_type_name FROM entity_types
							RIGHT JOIN
								(SELECT collections.collection_name, relations3.* FROM collections
								RIGHT JOIN	
									(SELECT documents.collection_id, documents.document_name, relations2.* FROM documents 
									RIGHT JOIN
										(SELECT entities.document_id, relations1.*, entities.entity_name, entities.text_start, entities.text_end, entities.entity_type_id FROM entities
										RIGHT JOIN
											(SELECT dm_entities.entity_id from dm_entities 
											RIGHT JOIN
												(SELECT dm_entities.dm_entity_id FROM dm_entities
												WHERE dm_entities.entity_id = '.$entity_id.') relations0
											ON dm_entities.dm_entity_id = relations0.dm_entity_id) AS relations1
										ON entities.entity_id = relations1.entity_id) AS relations2
									ON documents.document_id  = relations2.document_id) AS relations3
								ON collections.collection_id = relations3.collection_id) AS relations4
							ON entity_types.entity_type_id = relations4.entity_type_id) AS relations5
						ON entity_relations.subject_entity_id = relations5.entity_id) AS relations6
				ON entities.entity_id = relations6.subject_entity_id) AS relations8
			ON entity_types.entity_type_id = relations8.subject_type_id) AS relations9
		ON relations.relation_id = relations9.relation_id) AS relations10
	ON entities.entity_id = relations10.object_entity_id) AS relations11
ON entity_types.entity_type_id = relations11.object_type_id;');
		return $query->result_array();
	}

	public function get_possibly_entity($dm_entity_id)
	{		
		$query = $this->db->query('SELECT relations13.* FROM (SELECT collections.collection_name, relations12.* FROM collections
		JOIN
			(SELECT relations11.*, entity_types.entity_type_name AS object_type_name FROM entity_types
			JOIN
				(SELECT relations10.*, entities.entity_name AS object_name, entities.text_start AS object_text_start, entities.text_end AS object_text_end, entities.entity_type_id AS object_type_id FROM entities
				JOIN
					(SELECT relations9.*, relations.relation_name AS relation_name FROM relations
					JOIN
						(SELECT relations8.*, entity_types.entity_type_name AS subject_type_name from entity_types
						JOIN
							(SELECT relations7.*, entities.entity_name as subject_name, entities.text_start AS subject_text_start, entities.text_end AS subject_text_end, entities.entity_type_id AS subject_type_id FROM entities
							JOIN
								(SELECT relations6.* FROM entity_relations
								RIGHT JOIN
									(SELECT relations5.*, entity_relations.subject_entity_id, entity_relations.relation_id, entity_relations.object_entity_id FROM entity_relations
									RIGHT JOIN
										(SELECT documents.collection_id, documents.document_name, relations4.* FROM documents
										JOIN
											(SELECT relations3.document_id, relations3.related_dm_entity_id, relations3.entity_id, relations3.entity_name, relations3.entity_type_id, entity_types.entity_type_name, relations3.confidence FROM entity_types
											JOIN
												(SELECT entities.document_id, relations2.related_dm_entity_id, relations2.entity_id, entities.entity_name, entities.entity_type_id, relations2.confidence FROM entities
												JOIN
													(SELECT relations1.related_dm_entity_id, dm_entities.entity_id, relations1.confidence FROM dm_entities
													JOIN
														(SELECT dm_person_candidates.object_dm_entity_id AS related_dm_entity_id, dm_person_candidates.confidence FROM dm_person_candidates WHERE dm_person_candidates.subject_dm_entity_id = '.$dm_entity_id.'
														UNION DISTINCT
														SELECT dm_person_candidates.subject_dm_entity_id AS related_dm_entity_id, dm_person_candidates.confidence FROM dm_person_candidates WHERE dm_person_candidates.object_dm_entity_id = '.$dm_entity_id.'
														) AS relations1
													ON dm_entities.dm_entity_id = relations1.related_dm_entity_id ORDER BY entity_id) AS relations2
												ON entities.entity_id = relations2.entity_id ORDER BY entities.document_id) AS relations3
										ON entity_types.entity_type_id = relations3.entity_type_id) as relations4
									ON documents.document_id = relations4.document_id ORDER BY documents.collection_id) AS relations5
								ON entity_relations.subject_entity_id = relations5.entity_id) AS relations6
							ON entity_relations.object_entity_id = relations6.entity_id) AS relations7
						ON entities.entity_id = relations7.subject_entity_id) AS relations8
					ON entity_types.entity_type_id = relations8.subject_type_id) AS relations9
				ON relations.relation_id = relations9.relation_id) AS relations10
				ON entities.entity_id = relations10.object_entity_id) AS relations11
			ON entity_types.entity_type_id = relations11.object_type_id) AS relations12
		ON collections.collection_id = relations12.collection_id) AS relations13 WHERE relations13.confidence > 0.1');
		return $query->result_array();
	}
	
	public function get_possibly_entity_site($entity_id)
	{		
		$query = $this->db->query('
		SELECT documents.document_name, inferred_site_transactions.* FROM documents
		RIGHT JOIN
		(SELECT entities.document_id, entities.entity_id, entities.entity_type_id, entities.entity_name FROM entities
		JOIN
		(SELECT dm_entities.entity_id FROM dm_entities
		JOIN
		(SELECT DISTINCT(dm_entities.dm_entity_id) FROM dm_entities
		JOIN (SELECT dm_site_candidates.subject_dm_entity_id, dm_site_candidates.object_dm_entity_id 
		FROM dm_site_candidates 
		JOIN
		(SELECT dm_entities.dm_entity_id, related_transactions.entity_id FROM dm_entities
		JOIN
		(SELECT entities.entity_id FROM entities 
		JOIN 
		(SELECT DISTINCT(entity_relations.subject_entity_id) AS related_entity_id FROM entity_relations 
		JOIN entities ON entity_relations.object_entity_id = entities.entity_id
		WHERE entity_id = '.$entity_id.'
		UNION DISTINCT
		SELECT DISTINCT(entity_relations.object_entity_id) AS related_entity_id FROM entity_relations 
		JOIN entities ON entity_relations.subject_entity_id = entities.entity_id
		WHERE entity_id = '.$entity_id.') AS all_relations
		ON entities.entity_id = all_relations.related_entity_id
		WHERE entities.entity_type_id = 12) as related_transactions
		ON dm_entities.entity_id = related_transactions.entity_id) as dm_transactions
		ON dm_site_candidates.subject_dm_entity_id = dm_transactions.dm_entity_id OR dm_site_candidates.object_dm_entity_id = dm_transactions.dm_entity_id) AS dm_trans_relations
		ON dm_entities.dm_entity_id = dm_trans_relations.subject_dm_entity_id OR dm_entities.dm_entity_id = dm_trans_relations.object_dm_entity_id) AS related_dm_entity_ids
		ON dm_entities.dm_entity_id = related_dm_entity_ids.dm_entity_id) related_entity_ids
		ON entities.entity_id = related_entity_ids.entity_id
		WHERE entities.entity_type_id = 12) AS inferred_site_transactions
		ON documents.document_id = inferred_site_transactions.document_id');
		return $query->result_array();
	}
	
	public function get_local_relationships_site($entity_id)
	{		
		$query = $this->db->query('
		SELECT collections.collection_name, relations10.* FROM collections
JOIN
	(SELECT entity_types.entity_type_name, relations9.* FROM entity_types
	JOIN
		(SELECT entities.entity_type_id, relations8.* FROM entities
		JOIN
			(SELECT dm_entities.dm_entity_id, relations7.* FROM dm_entities
			JOIN
				(SELECT documents.collection_id, documents.document_name, relations6.* FROM documents
				JOIN
					(SELECT relations5.*, entity_types.entity_type_name AS object_type_name FROM entity_types
					JOIN
						(SELECT relations4.document_id, relations4.entity_id, relations4.subject_entity_id, relations4.subject_name, relations4.subject_type_id, entity_types.entity_type_name AS subject_type_name, relations4.relation_id, relations4.relation_name, relations4.object_entity_id, relations4.object_name, relations4.object_type_id FROM entity_types
						JOIN
							(SELECT entities.document_id, relations3.*, entities.entity_name AS object_name, entities.entity_type_id AS object_type_id FROM entities
							JOIN
								(SELECT relations2.entity_id, relations2.relation_id, relations2.subject_entity_id, entities.entity_name AS subject_name, entities.entity_type_id AS subject_type_id, relations2.relation_name, relations2.object_entity_id FROM entities
								JOIN
									(SELECT relations1.entity_id, relations1.relation_id, relations1.subject_entity_id, relations.relation_name, relations1.object_entity_id FROM relations
									JOIN
										(SELECT 123 AS entity_id, entity_relations.* FROM entity_relations WHERE entity_relations.subject_entity_id IN (123)
										UNION
										SELECT 123 AS entity_id, entity_relations.* FROM entity_relations WHERE entity_relations.object_entity_id IN (123)) AS relations1
									ON relations.relation_id = relations1.relation_id) AS relations2
								ON entities.entity_id = relations2.subject_entity_id) AS relations3
							ON entities.entity_id = relations3.object_entity_id) AS relations4
						ON entity_types.entity_type_id = relations4.subject_type_id) AS relations5
					ON entity_types.entity_type_id = relations5.object_type_id) AS relations6
				ON documents.document_id = relations6.document_id) AS relations7
			ON dm_entities.entity_id = relations7.entity_id) AS relations8
		ON entities.entity_id = relations8.entity_id) AS relations9
	ON entity_types.entity_type_id = relations9.entity_type_id) AS relations10
ON collections.collection_id = relations10.collection_id
ORDER BY document_id, subject_type_id, relation_id, object_type_id');
		return $query->result_array();
	}
}