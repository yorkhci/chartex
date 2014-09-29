//////////////////////////////////////
//ENTITY TREE
//////////////////////////////////////

//Sets up the collapsible jQuery trees for a particular entity
//The arrays are generated as JSON in the view 
//The autoEscape option is to allow HTML within the tree nodes

function entTree(dm_entity_id) {
	if(typeof(entity_def_json[dm_entity_id]) != "undefined" && entity_def_json[dm_entity_id] !== null) {
		$('#definitely_tree_'+dm_entity_id).tree({
			data: entity_def_json[dm_entity_id],
			autoEscape: false,
			autoOpen: false
		});
		
		//Binds a click event the nodes of the trees
		//This gets the value of the data-tabinfo attribute, splits it up, and sends it to the addDocument function
		$('#definitely_tree_'+dm_entity_id).bind(
			'tree.click',
			function(event) {
				// The clicked node is 'event.node'
				var node = event.node;	
				var document_details = node["data-tabinfo"].split("~");
				var document_id = document_details[0];
				var document_name = document_details[1];
				var entity_id = document_details[2];
				//If it is the document link (which won't have an entity to highlight)
				if(typeof entity_id === 'undefined'){
					entity_id = null;
				};
				//alert(document_details);
				addDocumentTab(document_id, document_name, entity_id);
			}
		);	
	}
	if(typeof(entity_mayb_json[dm_entity_id]) != "undefined" && entity_mayb_json[dm_entity_id] !== null) {
		$('#maybe_tree_'+dm_entity_id).tree({
			data: entity_mayb_json[dm_entity_id],
			autoEscape: false,
			autoOpen: false
		});
		
		$('#maybe_tree_'+dm_entity_id).bind(
			'tree.click',
			function(event) {
				// The clicked node is 'event.node'
				var node = event.node;	
				var document_details = node["data-tabinfo"].split("~");
				var document_id = document_details[0];
				var document_name = document_details[1];
				var entity_id = document_details[2];
				//If it is the document link (which won't have an entity to highlight)
				if(typeof entity_id === 'undefined'){
					entity_id = null;
				};
				//alert(document_details);
				addDocumentTab(document_id, document_name, entity_id);
			}
		);
	}
}

function confirmDeny(dm_entity_id) {
	$('#maybe_tree_'+dm_entity_id).find('.confirm').on('click', function (event) {		
		event.stopPropagation();
		event.preventDefault();
		$(this).toggleClass('confirm_on');
		$(this).siblings('.deny').removeClass('deny_on');
		$(this).parent().siblings('span.entity_name').removeClass('denied');
		$(this).parent().siblings('span.entity_name').toggleClass('confirmed');
		//var target = $(this).parent().siblings('span.entity_name')
		//target.css('text-decoration', 'none');	
		//target.css('font-weight', 'bold');	
	});
	$('#maybe_tree_'+dm_entity_id).find('.deny').on('click', function (event) {		
		event.stopPropagation();
		event.preventDefault();
		$(this).toggleClass('deny_on');
		$(this).siblings('.confirm').removeClass('confirm_on');
		$(this).parent().siblings('span.entity_name').removeClass('confirmed');
		$(this).parent().siblings('span.entity_name').toggleClass('denied');
		//var target = $(this).parent().siblings('span.entity_name')
		//target.css('text-decoration', 'line-through');	
		//target.css('font-weight', 'normal');		
	});
	return false;
}