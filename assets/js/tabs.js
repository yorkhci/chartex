//////////////////////////////////////
// TABS
//////////////////////////////////////

//Tab variables
var tabTitle = $( "#tab_title" ),
	tabContent = $( "#tab_content" ),
	tabTemplate = "<li><a href='#{href}' data-graphid='#{id}'>#{label}</a> <span class='ui-icon ui-icon-close' role='presentation'>Remove Tab</span></li>",
	documentTabCounter = 0,
	entityTabCounter = 0

//Global variables that are passed via the Ajax tabs for highlighting purposes
var DocumentViewerTitle = null; // The title to be highlighted in the Document Viewer
var DocumentViewerEntity = null; // The entity to be highlighted in the Document Viewer
var EntityViewerTitle = null; // The title to be highlighted in the Entity Viewer
var EntityViewerEntity = null; // The entity to be highlighted in the Entity Viewer

//////////////////////////////////////
// TREE / GRAPH LOADERS
//////////////////////////////////////

//Creates a new document graph, called from the tabs callback.
function createDocGraph(document_id) { 
	var vis_container = document.getElementById('document_vis_'+document_id); 
	var log_container = document.getElementById('document_log_'+document_id); 
	var newGraph = docGraph(vis_container, log_container, document_id);
	document_graphs[document_id] = newGraph;
}
//Creates a new entity tree, called from the tabs callback.
function createEntTree(dm_entity_id) { 
	var newTree = entTree(dm_entity_id);
	entity_trees[dm_entity_id] = newTree;
}
//Creates a new entity graph, called from the tabs callback.
function createEntGraph(dm_entity_id) { 
	var vis_container = document.getElementById('entity_vis_'+dm_entity_id);
	var log_container = document.getElementById('entity_log_'+dm_entity_id);
	var newGraph = entGraph(vis_container, log_container, dm_entity_id);
	entity_graphs[dm_entity_id] = newGraph;
}

//////////////////////////////////////
// TABS INITIALISATION
//////////////////////////////////////

$(document).ready(function() {
	//The Results Table Tabs
	tabs = $( "#tabs" ).tabs();
	//tabs.tabs();	
	//The Document Tabs
	tabs2 = $( "#tabs2" ).tabs({
		//This checks whether the tob has been loaded already and, if so, interrupts the Ajax call.
		beforeLoad: function( event, ui ) {
			if ( ui.tab.data( "loaded" ) ) {
				event.preventDefault();
				return;
			}
			ui.jqXHR.success(function() {
				ui.tab.data( "loaded", true );				
			});
		},
		//This waits until the Ajax content has loaded, grabs the document_id from the current tab, then calls the visualisation creation and highlighting functions. 
		load : function(event, ui){		
			var currentPanel = ui.panel
			var currentTab = ui.tab
			var document_id = currentTab.find("a").data('graphid');   
			disableDocLinks(currentPanel);
			highlightDocument(currentPanel);
			setupSelectAll(currentPanel);
			createDocGraph(document_id);
            DocumentViewerHighlighter(DocumentViewerTitle, DocumentViewerEntity);
        }
	});
	//Set up the Close All Documents button
	$("#remDocTabs").click(function ( ) {
		$('#tabs2 li').remove();
		$('#tabs2 div').remove();
		tabs2.tabs( "refresh" );
		documentTabCounter = 0;
		$("#remDocTabs").hide();
	});	
	$("#remDocTabs").hide();
	
	//The Entity Tabs
	tabs3 = $( "#tabs3" ).tabs({
		 //This checks whether the tob has been loaded already and, if so, interrupts the Ajax call.
		 beforeLoad: function( event, ui ) {
			if ( ui.tab.data( "loaded" ) ) {
				event.preventDefault();
				return;
			}
			ui.jqXHR.success(function() {
				ui.tab.data( "loaded", true );
			});
		},
		//This waits until the Ajax content has loaded before calling the highlighting function
		load : function(event, ui){
			var dm_entity_id = ui.tab.find("a").data('graphid');   
            createEntTree(dm_entity_id);
			createEntGraph(dm_entity_id);
			EntityViewerHighlighter(EntityViewerTitle, EntityViewerEntity);
			confirmDeny(dm_entity_id);
        }
	});
	//Set up the Close All Entities button
	$("#remEntTabs").click(function () {
		$('#tabs3 li').remove();
		$('#tabs3 div').remove();
		tabs3.tabs( "refresh" );
		entityTabCounter = 0;
		$("#remEntTabs").hide();
	});
	$("#remEntTabs").hide();
});         

//////////////////////////////////////
//DOCUMENT TABS
//////////////////////////////////////

// addDocumentTab function: adds new tab to the Document Viewer using the input from the form
function addDocumentTab(document_id, document_name, entity_id) {
	var nameToCheck = document_name;
	var numberOfTabs = 0;
	var targetTab = 0;
	var tabNameExists = false;
	
	//Loop through the open tabs to check whether the tab is already open (by comparing names) 
	$('#tabs2 ul li a').each(function(i) {
		numberOfTabs++;
		if (this.text == nameToCheck) {    	
			tabNameExists = true;
			targetTab = numberOfTabs;	
		}
	});	
	//If the tab is not already open, then open a new tab
	if (!tabNameExists){
		//This takes the title and entity to be highlighted and updates the global variables
		DocumentViewerTitle = document_id;
		DocumentViewerEntity = entity_id;
		
		var label = tabTitle.val() || document_name,
		id = "tabs-" + documentTabCounter,
		li = $( tabTemplate.replace( /#\{href\}/g, js_base_url+'document/'+document_id).replace( /#\{label\}/g, label ).replace( /#\{id\}/g, document_id ) );
		tabs2.find( ".ui-tabs-nav" ).append( li );
		tabs2.tabs( "refresh" );
		tabs2.tabs( "option", "active", documentTabCounter);
		documentTabCounter++;	
		
		// Set up functionality for the Close (X) link to remove the tab on click.
		tabs2.off("click", "span.ui-icon-close").on("click", "span.ui-icon-close", function() {
			var panelId = $( this ).closest( "li" ).remove().attr( "aria-controls" );
			$( "#" + panelId ).remove();
			tabs2.tabs( "refresh" );
			documentTabCounter--;
			if (documentTabCounter == 0) {
				$("#remDocTabs").hide();
			}
		});
		
		//Bind keyboard functionality for the Close (X) link to remove the tab when backspace is pressed.
		tabs2.bind( "keyup", function( event ) {
			if ( event.altKey && event.keyCode === $.ui.keyCode.BACKSPACE ) {
				var panelId = tabs2.find( ".ui-tabs-active" ).remove().attr( "aria-controls" );
				$( "#" + panelId ).remove();
				tabs2.tabs( "refresh" );
			}
		});
		
		//If this is the first tab to be opened, reveal the "Close All" button.		
		if (documentTabCounter > 0) {
			$("#remDocTabs").show();
		}
			
	}
	//If the tab is already open, then make it active
	else {
		tabs2.tabs( "option", "active", targetTab-1);
		DocumentViewerHighlighter(document_id, entity_id);			
	}		
};

//////////////////////////////////////
//ENTITY TABS
//////////////////////////////////////

// addEntityTab function: adds new tab to the Entity Viewer using the input from the form
function addEntityTab(tab_name, entity_type_name, dm_entity_id, entity_id) {
	var nameToCheck = tab_name;
	var numberOfTabs = 0;
	var targetTab = 0;
	var tabNameExists = false;
	
	//Loop through the open tabs to check whether the tab is already open (by comparing names) 
	$('#tabs3 ul li a').each(function(i) {
		numberOfTabs++;
		if (this.text == nameToCheck) {    	
			tabNameExists = true;
			targetTab = numberOfTabs;	
		}
	});	
	//If the tab is not already open, then open a new tab
	if (!tabNameExists){
		//This takes the title and entity to be highlighted and updates the global variables
		EntityViewerTitle = dm_entity_id;
		EntityViewerEntity = entity_id;
		var label = tabTitle.val() || tab_name,
		id = "tabs-" + entityTabCounter,
		li = $( tabTemplate.replace( /#\{href\}/g, js_base_url+'entity/'+entity_type_name+'/'+dm_entity_id+'/'+entity_id).replace( /#\{label\}/g, label ).replace( /#\{id\}/g, dm_entity_id )  );
		tabs3.find( ".ui-tabs-nav" ).append( li );
		tabs3.find( ".ui-tabs-nav a" ).click(function(e) {
			e.preventDefault(); 
		});
		tabs3.tabs( "refresh" );
		tabs3.tabs( "option", "active", entityTabCounter);
		entityTabCounter++;
		
		// Set up functionality for the Close (X) link to remove the tab on click.
		tabs3.off("click", "span.ui-icon-close").on("click", "span.ui-icon-close", function() {
			var panelId = $( this ).closest( "li" ).remove().attr( "aria-controls" );
			$( "#" + panelId ).remove();
			tabs3.tabs( "refresh" );
			entityTabCounter--;
			if (entityTabCounter == 0) {
				$("#remEntTabs").hide();
			}
		});
		//Bind keyboard functionality for the Close (X) link to remove the tab when backspace is pressed.
		tabs3.bind( "keyup", function( event ) {
			if ( event.altKey && event.keyCode === $.ui.keyCode.BACKSPACE ) {
				var panelId = tabs3.find( ".ui-tabs-active" ).remove().attr( "aria-controls" );
				$( "#" + panelId ).remove();
				tabs3.tabs( "refresh" );
			}
		});
		
		//If this is the first tab to be opened, reveal the "Close All" button.
		if (entityTabCounter > 0) {
			$("#remEntTabs").show();
		}
	}
	//If the tab is already open, then make it active
	else {
		tabs3.tabs( "option", "active", targetTab-1);
		EntityViewerHighlighter(dm_entity_id, entity_id);	
	}		
};