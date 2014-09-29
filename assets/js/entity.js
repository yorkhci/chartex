//For all of the links in the related entities list, when clicked
$(".related_entities a.document_link, .related_entities a.entity_link").off('click').on('click',function() {	
	//Break up the id attribute to get the document_id, document name, and entity_id
	var document_details = $(this).attr('id').split("~");
	var document_id = document_details[0];
	var document_name = document_details[1];
	var entity_id = document_details[2];
	//If it is the document link (which won't have an entity to highlight)
	if(typeof entity_id === 'undefined'){
   		entity_id = null;
 	};
	addDocumentTab(document_id, document_name, entity_id);
	return false;	
});


