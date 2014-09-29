//////////////////////////////////////
//VARIOUS DOCUMENT FUNCTIONS
//////////////////////////////////////

//Disable all of the links embedded in the document text and change the cursor to default
function disableDocLinks(currentPanel) { 
	currentPanel.find('.document_text a').click(function(event){
			event.preventDefault();	
			$(this).css("cursor", "default");
	});
	currentPanel.find('.document_text a').css('cursor', 'text');
	
	//DIsable the default tooltip (and optionally create ajax tooltips to replace them)
	currentPanel.find('.document_text a').tooltip({
		disabled: true,
		/*content: '... waiting on ajax ...',
		open: function(evt, ui) {
			var elem = $(this);
			$.ajax('/echo/html').always(function() {
				elem.tooltip('option', 'content', 'Ajax call complete');
			 });
		},*/
	});
}
//Set up document highlighting
function highlightDocument(currentPanel) {
	//Identify how many checkboxes are in the currently selected panel
	var checkboxesInPanel = currentPanel.find('input:checkbox[name="chk_entities[]"]')
	//For each of the checkboxes, when they change (either by a direct click or from the "Select All" checkbox)...
	//...find the anchors with the same name in the nearby "document_text" paragraph...
	//...then add/remove the highlight class, change the cursor accordingly, enable/disable the tooltip, and add/remove the click event.
	//Note: The route to the "document_text" paragraph seems a bit brittle and liable to change
	$(checkboxesInPanel).change(function() {
			var relatedAnchors = $(this).parent().parent().parent().siblings('.document_text').find('a.'+$(this).val());
			if($(this).is(':checked')) {
				relatedAnchors.addClass('highlight');
				relatedAnchors.css("cursor", "pointer");
				relatedAnchors.tooltip({ disabled: false });
				relatedAnchors.click(function(event){		
					//When clicked, get the tab name from the data-title attribute (the default title attribute is too unreliable)...
					var tab_name = $(this).data('title');
					//...get the entity details by splitting up the id attribute...
					var entity_details = $(this).attr('id').split("~");
					//...and send these to the addEntityTab function
					addEntityTab(tab_name, entity_details[0], entity_details[1], entity_details[2]);
					return false;	
				});			
			} else {
				relatedAnchors.removeClass('highlight');
				relatedAnchors.css("cursor", "default");
				relatedAnchors.tooltip({ disabled: true });		
				relatedAnchors.unbind().click(function(event){
					event.preventDefault();
				});		
			} 
	});
}

function setupSelectAll (currentPanel) {
	//Set up the "Select All" checkbox on the Document viewer to toggle the other checkboxes
	currentPanel.find('.checkall').on('click', function () {
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
	//Trigger the select all box, to active the highlighting when the document loads up
	currentPanel.find('.checkall').trigger("click");
}