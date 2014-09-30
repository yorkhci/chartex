//Edit this to reflect the domain directory structure
var js_base_url = 'http://'+ window.location.host+'/chartex/';

//Array of document json data.
var document_json = {};
//Array of document graphs.
var document_graphs = {};

//Array of entity 'definitely' and 'maybe' json data for the trees.
var entity_def_json = {};
var entity_mayb_json = {};
//Array of entity trees.
var entity_trees = {};

//Array of entity json data for the graphs.
var entity_json = {};
//Array of entity graphs.
var entity_graphs = {};

$(document).ready(function() {
	
	//AJAX search functionality
	//When the search query form is submitted...
	$('#search_form').submit(function(){	
		//First of all, reveal the search results container (hidden for neatness) 	
		$('#search_results_container').show();
		//Then, serialise the entire search query form 
		var dataString = $('#search_form').serialize();
		//Post the data to 'results' and display the results via Ajax in the tabs container
		$(".ajaxloader").show();
		$.ajax({
    		type:'POST',
    		url: js_base_url+'results/documents',
    		data: dataString,
    		success: function(data){
				$("#ajaxloader").hide();
        		$('#documents').html(data);
				$("#document_pager").show();
				documentsTableEnhancements();
    		}
		});
		$.ajax({
    		type:'POST',
    		url: js_base_url+'results/entities',
    		data: dataString,
    		success: function(data){
        		$('#entities').html(data);
				$("#entity_pager").show();
				entitiesTableEnhancements();
    		}
		});
		return false;
	});	
	
	//Select All checkbox functionality
	//For the 'Select All' checkbox, find the nearby checkboxes either check or uncheck them, trigger the change above.
	$('.checkall').on('click', function () {
		if($(this).is(':checked')) {
			$(this).closest('fieldset').find(':checkbox').each(function(){
				$(this).prop("checked", true).trigger("change");
			});	
		} else {
			$(this).closest('fieldset').find(':checkbox').each(function(){
				$(this).prop("checked", false).trigger("change");
			});
		}
	});	
	
	//HIde/show LEGEND functionality
	//Make the legends clickable and the contents expandable
	//NOT currently used because it wasn't very obvious
	/*$("#advanced_fieldset > legend").click(function () {
        var $this = $(this);
        var parent = $this.parent();
        var contents = parent.contents().not(this);
        if (contents.length > 0) {
            $this.data("contents", contents.detach());
			$this.text("Entities included in search:");
        } else {
            $this.data("contents").appendTo(parent);
			$this.text("Entities included in search:");
        }
    });
	$("#advanced_fieldset > legend").click();*/
	
	//HELP functionality
	//Displays a tooltip with help information.
	//NOT currently used because it was very easy to trigger it with mouse over
	
	/*$(".help_tooltip_1").each(function(){		
		$(this).tooltip({
			show: {
    			effect: "slideDown"
  			},
			hide: {
    			effect: "slideUp"
  			},
			/*content:function(callback) { //callback
        		var url = js_base_url+$(this).attr('href');
				$.get(url,{}, function(data) {
            		callback(data); //call the callback function to return the value
        		});
    		}
			content: 
			"<h4>Help with this section:</h4>"+
			"<p>Type in a word or phrase you would like to search for. For example, you might search for a place name, such as <em>London</em>, or the name of a person, such as <em>John Marschall</em>.</p>"+
			"<p>Select one or more collections to include them in your search. These are collections of digital medieval charter documents that have been processed by the ChartEx Project. All of the collections have been included by default.</p>"+
			"<p>Select one or more entity types to include them in your search. These refer to the different types of entities (e.g. a person or a site) that have been identified in the charters. All of the entity types have been included by default.</p>"+
			"<p>Press the \"Search\" button. The results of your search will appear in a panel below the search options. This panel is divided into two sections: \"Document Results\" and \"Entity Results\". Click on  each tab to alternate between the two types of results. Your search may return many results. If so, you can use the forward and backward controls at the bottom of each tab to scroll through each page of results (10 per page).</p>"+
			"<p>The Document Results tab includes a table of documents from the selected collections. Documents are included in the results wherever there is a match in the document text to your search query. From this table, you can open each document by clicking on the document name.</p>"+
			"<p>The Entity Results tab includes a list of entities from the selected collections and selected entity types. Entities are included in the results wherever there is a match in the entity name to your search query. From this table, you can open each entity by clicking on the entity extract, or open the document it appears in by clicking on the document name.</p>"
		})
			.off( "click" )
  			.on( "click", function(){
      			$( this ).tooltip( "open" );
      			return false;
    		});
	});
	
	$(".help_tooltip_2").each(function(){		
		$(this).tooltip({
			show: {
    			effect: "slideDown"
  			},
			hide: {
    			effect: "slideUp"
  			},
			/*content:function(callback) { //callback
        		var url = js_base_url+$(this).attr('href');
				$.get(url,{}, function(data) {
            		callback(data); //call the callback function to return the value
        		});
    		}
			content: 
			"<h4>Help with this section:</h4>"+
			"<p>When a document is opened, it appears in the Documents panel. Each document opens in a separate tab within the panel allowing you to alternate between each one."+ 
			"<p>Each tab includes the name of the document and the collection it appears in, the document text, a control panel to show the markup of the document, and a visualisation of any transactions within the document.</p>"+
			"<p>The entities that appear in the document have been marked up in the document text and can be highlighted. Select one or more entity types in the \"Show markup\" control panel to highlight the relevant entities in the document text. All of the entity types have been highlighted by default. You can click on each highlighted entity to open it in the entities panel."+
			"<p>Below the document text is a visualisation of the transactions that are described in the document. At the centre of the visualisation is the current document. This is connected to one or more nodes representing the transactions in the document (e.g. a grant or a feoffment). Each transaction node is connected to a further set of nodes representing different relationship types (e.g. \"is recipient in\" or \"is parcel in\") and each of these is connected to one or more entities that are involved in the transaction. You can click on each node to bring it to the centre of the visualisation and further explore the details of the transaction.</p>"
		})
			.off( "click" )
  			.on( "click", function(){
      			$( this ).tooltip( "open" );
      			return false;
    		});
	});
	
	$(".help_tooltip_3").each(function(){		
		$(this).tooltip({
			show: {
    			effect: "slideDown"
  			},
			hide: {
    			effect: "slideUp"
  			},
			/*content:function(callback) { //callback
        		var url = js_base_url+$(this).attr('href');
				$.get(url,{}, function(data) {
            		callback(data); //call the callback function to return the value
        		});
    		}
			content: 
			"<h4>Help with this section:</h4>"+
			"<p>When an entity is opened, it appears in the Entities panel. Each entity opens in a separate tab within the panel allowing you to alternate between each one.</p>"+ 
			"<p>Each tab includes a list of entities that definitely refer to the same entity (\"Same as...\"), a list of entities that possibly refer to the same person (\"Possibly also...\"), and a visualisation of both of these lists.</p>"+ 
			"<p>The \"Same as...\" list initially displays a list of documents. By clicking on the arrow next to a document name, it will open to reveal a list of entities that have been confirmed by historians as definitely being the same. Some entities will have an arrow next to them. By clicking on the arrow next to an entity, it will reveal a further list of entities that are related to that entity. You can click on a document or entity name to open the relevant document in the Documents panel.</p>"+
			"<p>The \"Possibly also...\" list initially displays a list of documents. By clicking on the arrow next to a document name, it will open to reveal a list of entities that have been predicted as being the same. As the similarity has not been confirmed by a historian, a confidence percentage is included below each entity. Some entities will have an arrow next to them. By clicking on the arrow next to an entity, it will reveal a further list of entities that are related to that entity. You can click on a document or entity name to open the relevant document in the Documents panel.</p>"+
			"<p>To confirm or refute that a entity is definitely the same, you can click on the tick and cross icons next to that entity. The tick icon will confirm that the entity is the same. This will be indicated by the entity name becoming emboldened. The cross icon will refute that the entity is the same. This will be indicated with a strikethrough the entity name.</p>"+
			"<p>Below the entity details is a visualisation of the transactions that are described in the document. At the centre of the visualisation is the current entity. This is connected to two nodes representing the \"Same As...\" and \"Possibly Also...\" lists. Each of these nodes is connected to a further set of nodes representing the documents in which references to the entity appear in, and each of these is connected to one or more nodes representing the actual references to the entity. You can click on each node to bring it to the centre of the visualisation and further explore the connections between entities.</p>"
		})
			.off( "click" )
  			.on( "click", function(){
      			$( this ).tooltip( "open" );
      			return false;
    		});
	});*/
});         

//Documents results table enhancements (striping, sorting, pagination, tab callers etc.)
function documentsTableEnhancements() {
    //Add table sorting functionality to the document search table
	$("#document_search_results").tablesorter({ 
        widgets: ['zebra'],
        headers: 2 
    })
	//Add pagination functionality to the document search table
	.tablesorterPager({
		positionFixed: false,
		output: 'showing: {startRow} to {endRow} ({totalRows})',
		container: $("#document_pager"),
		size: 10
	})
	//Set up the document links to open the document in the Document Viewer
	.on("click", ".document_link", function(e) { 
		//Splitting up the ID of each link and passing the document ID, document name, and a null value to trigger the title highlighting.
		var document_details = $(this).attr('id').split("~");
		var document_id = document_details[0];
		var document_name = document_details[1];		
		addDocumentTab(document_id, document_name, null);
		return false;	
	});
	//Current unused tooltip functionality
	/*$(".document_link").each(function(){
		var document_details = $(this).attr('id').split("~");
		var document_id = document_details[0];	
		$(this).tooltip({
			show: {
    			effect: "slideDown",
    			delay: 1000
  			},
			hide: {
    			effect: "slideUp",
    			delay: 5000
  			},
			content:function(callback) { //callback
        		$.get(js_base_url+'tooltip/'+document_id,{}, function(data) {
            		callback(data); //call the callback function to return the value
        		});
    		}
		});
	});*/
}

//Entities results table enhancements (striping, sorting, pagination, tab callers etc.)
function entitiesTableEnhancements() {	
	//Add table sorting functionality to the entity search table
	$("#entity_search_results").tablesorter({ 
        widgets: ['zebra'],
		headers: 3
    })
	//Add pagination functionality to the entity search table
	.tablesorterPager({
		positionFixed: false,
		output: 'showing: {startRow} to {endRow} ({totalRows})',
		container: $("#entity_pager"),
		size: 10
	})
	//Set up the document links to open the document in the Document Viewer
	.on("click", ".document_link", function(e) { 
		//Splitting up the ID of each link and passing the document ID, document name, and a null value to trigger the title highlighting.
		var document_details = $(this).attr('id').split("~");
		var document_id = document_details[0];
		var document_name = document_details[1];		
		addDocumentTab(document_id, document_name, null);
		return false;	
	})
	//Set up the entity links to open the entity in the Entity Viewer
	.on("click", ".entity_link", function(e) { 
		//At the moment it is passing the dm_entity_id and the entity type name but only the former is being used. Left in in case it is needed.
		var tab_name = $(this).attr('title');
		var entity_details = $(this).attr('id').split("~");
		addEntityTab(tab_name, entity_details[0], entity_details[1], entity_details[2]);
		return false;	
	});
	//Current unused tooltip functionality
	/*$(".document_link").each(function(){
		var document_details = $(this).attr('id').split("~");
		var document_id = document_details[0];	
		$(this).tooltip({
			content:function(callback) { //callback
        		$.get(js_base_url+'tooltip/'+document_id,{}, function(data) {
            		callback(data); //call the callback function to return the value
        		});
    		}
		});
	});	*/
}

//Highlights data in the Entity Viewer
function EntityViewerHighlighter(dm_entity_id, entity_id) {	
	//If a dm_entity_id (used for the title) has been passed...
	if (dm_entity_id !== null){
		//Highlight the title of the current panel in the Entity Viewer
		//(Could make this a bit more accurate by actually finding the specific panel)
		var selectedPanel = $("#tabs3 div.ui-tabs-panel[aria-hidden='false']");
		selectedPanel.find('h2').effect( "highlight", {color:"#efe8be"}, 1500 )
	}	
	//If an entity_id has been passed...
	if (entity_id !== null){	
		//Get the entity, get the tree, and send these to the jQTree to highlight ("select") the tree node
		var $entity = "entity~"+entity_id;
		var $tree = $('#definitely_tree_'+dm_entity_id);
		var node = $tree.tree('getNodeById', $entity);	
		$tree.tree('selectNode', node);
	}
}

//Highlights data in the Document Viewer
function DocumentViewerHighlighter(document_id, entity_id) {	
	//If a document_id has been passed...
	if (document_id !== null){
		//Highlight the title of the current panel in the Document Viewer
		//(Could make this a bit more accurate by actually finding the specific panel)
		var selectedPanel = $("#tabs2 div.ui-tabs-panel[aria-hidden='false']");
		selectedPanel.find('h2').effect( "highlight", {color:"#efe8be"}, 1500 )
	}	
	//If an entity_id has been passed...
	if (entity_id !== null){	
		//Find it in the current panel and highlight it.
		var expression = "#tabs2 [id$='~"+entity_id+"']" ;
		$(expression).effect( "highlight", {color:"#efe8be"}, 1500 );
	}
}