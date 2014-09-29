<?php
class Chartex extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('chartex_model');
	}
	
	//The default index function, which brings in the page header and footer and sets up the search fields
	public function index()
	{
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->helper('HTML');
		
		$data['collections'] = $this->chartex_model->get_collections();
		$data['entities'] = $this->chartex_model->get_entities();
		
		$this->load->view('chartex/header', $data);
		$this->load->view('chartex/search', $data);
		$this->load->view('chartex/document_viewer');
		$this->load->view('chartex/entity_viewer');
		$this->load->view('chartex/footer');
	}
	
	//The results function, which is used twice - for documents and entities. Outputs the search results table.
	public function results($search_type=0)
	{			
		$this->load->library('table');
		$this->load->helper('text');
		
		$search_form_query = null;
		if (isset($_POST['search_form_query'])) {
			$search_form_query = $_POST['search_form_query'];
		}		
		$chk_collections = null;
		if (isset($_POST['chk_collections'])) {
			$chk_collections = $_POST['chk_collections'];
		}
		
		$chk_entities = null;
		if (isset($_POST['chk_entities'])) {
			$chk_entities = $_POST['chk_entities'];
		}
		$data['search_form_query'] = $search_form_query;
		$data['chk_collections'] = $chk_collections;
		$data['search_type'] = ( $search_type === 'documents' ) ? 'documents' : 'entities';
			
		if ($search_type === 'documents'){
			$data['results'] = $this->chartex_model->get_results_documents($search_form_query, $chk_collections, $chk_entities);			
			$this->load->view('chartex/results_documents', $data);
		}else if ($search_type === 'entities'){
			$data['results'] = $this->chartex_model->get_results_entities($search_form_query, $chk_collections, $chk_entities);
			$this->load->view('chartex/results_entities', $data);
		}
	}
	
	//Not called via a URI, this creates a placeholder for the Document Viewer.
	public function document_viewer()
	{
		$this->load->view('chartex/document_viewer');
	}
	//Not called via a URI, this creates a placeholder for the Entity Viewer.
	public function entity_viewer()
	{
		$this->load->view('chartex/entity_viewer');
	}
	
	//Gets details about a document and the entities marked up within that document and populates a visualisation. 
	public function document($document_id)
	{					
		//A little CodeIgniter assistance.
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->helper('HTML');
		
		
		
		
		//Gets the entities to be highlighted in the document text.
		$data['entities'] = $this->chartex_model->get_document_entities($document_id);
		//Gets the different entity types to populate the checkboxes.				
		$data['entity_types'] = $this->chartex_model->get_entities();
		//Gets the data for the document visualisation.
		$data['document_vis'] = $this->chartex_model->get_document_vis($document_id);
		//Passes the document id (to be used by the visualisation.
		$data['document_id'] = $document_id;
			
		if(!empty($data['entities'])){
 			//Displays the document text and highlighting controls.	
			$this->load->view('chartex/document', $data);		
			//Displays the document visualisation.	
			$this->load->view('chartex/document_vis', $data);
		} else {
   			echo '<h2>Error: Could not find document</h2>'.
					'<p>The details of this document are currently unavailable. Please close this tab and open another document.</p>';
		}		
	}
	
	//Gets details about an entity, the entities it is definitely the same as, and the entities it is possibility the same as.
	public function entity($entity_type_name, $dm_entity_id, $entity_id)
	{					
		//A little CodeIgniter assistance.
		$this->load->helper('HTML');
		
		//Passes various details about the entity from the URI.
		//You may have to adjust these.
		//For localhost, these were 3, 4, and 5.
		//When uploaded to webspace, they 2, 3 and 4.	
		$data['entity_type_name'] = $this->uri->segment(2);
		$data['dm_entity_id'] = $this->uri->segment(3);
		$data['entity_id'] = $this->uri->segment(4);
		
		//Different options for different entity types.
		if ($data['entity_type_name'] == "Person"){		
			//Gets the entities that are definitely the same as this person entity.
			$data['sameas_entities'] = $this->chartex_model->get_sameas_entity($entity_id);
			//Gets the entities that are possibly the same as this person entity.
			$data['possibly_entities'] = $this->chartex_model->get_possibly_entity($dm_entity_id);
			
			//Uncomment the next line for HTML nested list.
			//$this->load->view('chartex/entity', $data);
			
			//Uncomment the next line for JSTree visualisation.
			$this->load->view('chartex/entity_tree', $data);
			
			//Uncomment the next line for JIT Visualisation.
			$this->load->view('chartex/entity_vis', $data);			
		}
		else if ($data['entity_type_name'] == "Site"){	
			//Gets the entities that are definitely the same as this site entity.
			$data['sameas_entities'] = $this->chartex_model->get_sameas_entity($entity_id);
			//Gets the entities that are possibly the same as this site entity.
			$data['possibly_entities'] = $this->chartex_model->get_possibly_entity_site($entity_id);
			
			//Uncomment the next line for HTML nested list.
			//$this->load->view('chartex/entity_site', $data);	
			
			//Uncomment the next line for JSTree visualisation.
			$this->load->view('chartex/entity_tree_site', $data);	
			
			//Uncomment the next line for JIT Visualisation.
			$this->load->view('chartex/entity_vis_site', $data);			
		}
		else {	
			//Gets the entities that are definitely the same as this entity.
			$data['sameas_entities'] = $this->chartex_model->get_sameas_entity($entity_id);
			//Gets the entities that are possibly the same as this entity.
			$data['possibly_entities'] = $this->chartex_model->get_possibly_entity($dm_entity_id);
			
			//Uncomment the next line for HTML nested list (not yet implemented!)
			//$this->load->view('chartex/entity_other', $data);
			
			//Uncomment the next line for JSTree
			$this->load->view('chartex/entity_tree_other', $data);	
			
			//Uncomment the next line for JIT Visualisation (not yet implemented!)
			//$this->load->view('chartex/entity_vis_other', $data);
		}
	}
	
	//Creates a tooltip, drawing on the get_document function. Jquery implementation of tooltips is currently a bit buggy.
	public function tooltip($document_id)
	{					
		$data['document'] = $this->chartex_model->get_document($document_id);
		$this->load->view('chartex/tooltip', $data);		
	}
	//Creates a tooltip, drawing on the get_document function. Jquery implementation of tooltips is currently a bit buggy.
	public function help($help_id)
	{					
		$this->load->view('chartex/help');		
	}
}	