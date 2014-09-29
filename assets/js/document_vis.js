//////////////////////////////////////
//DOCUMENT VISUALISATION FACTORY
//////////////////////////////////////

function docGraph(vis_container, log_container, document_id) {
	
	var labelType, useGradients, nativeTextSupport, animate;

	(function() {
	  var ua = navigator.userAgent,
		  iStuff = ua.match(/iPhone/i) || ua.match(/iPad/i),
		  typeOfCanvas = typeof HTMLCanvasElement,
		  nativeCanvasSupport = (typeOfCanvas == 'object' || typeOfCanvas == 'function'),
		  textSupport = nativeCanvasSupport 
			&& (typeof document.createElement('canvas').getContext('2d').fillText == 'function');
	  //I'm setting this based on the fact that ExCanvas provides text support for IE
	  //and that as of today iPhone/iPad current text support is lame
	  labelType = (!nativeCanvasSupport || (textSupport && !iStuff))? 'Native' : 'HTML';
	  nativeTextSupport = labelType == 'Native';
	  useGradients = nativeCanvasSupport;
	  animate = !(iStuff || !nativeCanvasSupport);
	})();

	var Log = {
	  elem: false,
	  write: function(){
		if (!this.elem) 
		  this.elem = log_container;
		this.elem.innerHTML = "Done";
		this.elem.style.left = (500 - this.elem.offsetWidth / 2) + 'px';
	  }
	};	
	
	var rgraph = new $jit.RGraph({ 
		/*Events: {  
    		enable: true,  
    		onClick: function(node, eventInfo, e) {  
      			// The clicked node is 'event.node'
				//var node = event.node;	
				var node_id = node.id;
				var document_details = node_id.split("~");
				var tab_name = document_details[1]+' '+document_details[2];
				
				var entity_type_name = document_details[1];
				var dm_entity_id = document_details[2];
				var entity_id = document_details[3];
				//If it is the document link (which won't have an entity to highlight)
				if(typeof entity_id === 'undefined'){
					entity_id = null;
				};
				alert(document_details);
				if (entity_type_name!="Document"&& entity_type_name!="Relationship"){
					addEntityTab(tab_name, entity_type_name, dm_entity_id, entity_id);
				//addDocumentTab(document_id, document_name, entity_id);  
				}
			}
		}, */
  		
		duration:500,
				//Where to append the visualization
		injectInto: vis_container.id,  
		//Optional: create a background canvas that plots
		//concentric circles.
		background: {
		  CanvasStyles: {
			//Colour of the background circles
			strokeStyle: '#C5BE94'
		  }
		},
		//Add navigation capabilities:
		//zooming by scrolling and panning.
		Navigation: {
		  enable: true,
		  panning: true,
		  zooming: 10
		},
		//Set Node and Edge styles.
		Node: {
			//Colour of the actual nodes
			color: '#660000',
			dim: 5,
		},
		
		Edge: {
		  //Color of lines between nodes
		  color: '#660000',
		  lineWidth:1.5
		},
	
		onBeforeCompute: function(node){
			Log.write("Centering " + node.name + "...");
		},
		
		//Add the name of the node in the correponding label
		//and a click handler to move the graph.
		//This method is called once, on label creation.
		onCreateLabel: function(domElement, node){
			domElement.innerHTML = node.name;
			domElement.onclick = function(){
				
				rgraph.onClick(node.id, {
					onComplete: function() {
						Log.write("Done");
					}
				});
			};
		},
		//Change some label dom properties.
		//This method is called each time a label is plotted.
		onPlaceLabel: function(domElement, node){
			var style = domElement.style;
			style.display = '';
			style.cursor = 'pointer';
	
			if (node._depth == 0) {
				style.fontSize = "0.8em";
				//Text colour of ring 1 nodes
				//style.color = "#ffffff";
				//style.backgroundColor = "#36352B";
				//style.padding = "1px 3px";			
			
			} else if (node._depth == 1) {
				style.fontSize = "0.8em";
				//Text colour of ring 1 nodes
				//style.color = "#FFFFFF";
				//style.backgroundColor = "#6E6B57";
				style.padding = "1px 3px";
			
			} else if(node._depth == 2){
				style.fontSize = "0.7em";
				//Text colour of ring 2 nodes
				//style.color = "#000000";
				//style.backgroundColor = "#CAC399";
			
			} else if(node._depth == 3){
				style.fontSize = "0.6em";
				//Text colour of ring 2 nodes
				//style.color = "#000000";
				//style.backgroundColor = "#CAC399";
			
			} else {
				style.display = 'none';
			}
	
			var left = parseInt(style.left);
			var w = domElement.offsetWidth;
			style.left = (left - w / 2) + 'px';
		},		
		onComplete: function(){  
			Log.write("Done");  
		} 	
	});
	//load JSON data
	rgraph.loadJSON(document_json[document_id]);
	//trigger small animation
	rgraph.graph.eachNode(function(n) {
	  var pos = n.getPos();
	  pos.setc(-200, -200);
	});
	rgraph.compute('end');
	rgraph.fx.animate({
	  modes:['polar'],
	  duration: 1000
	});
	
	return rgraph;
}
