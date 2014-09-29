//////////////////////////////////////
//DOCUMENT VISUALISATION
//////////////////////////////////////

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
      this.elem = document.getElementById('document_log_'+document_id);
    this.elem.innerHTML = "Done";
    this.elem.style.left = (500 - this.elem.offsetWidth / 2) + 'px';
  }
};

if (!window.doc_graph) {
	window.doc_rgraph = {};
}

//init RGraph
doc_rgraph[document_id] = new $jit.RGraph({
	//Where to append the visualization
	injectInto: 'document_vis_'+document_id,  
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
		color: '#660000'
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
			
			doc_rgraph[document_id].onClick(node.id, {
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
			style.color = "#000000";
			style.backgroundColor = "#F05322";
			style.padding = "1px 3px";			
		
		} else if (node._depth == 1) {
			style.fontSize = "0.8em";
			//Text colour of ring 1 nodes
			style.color = "#FFFFFF";
			style.backgroundColor = "#164557";
			style.padding = "1px 3px";
		
		} else if(node._depth == 2){
			style.fontSize = "0.7em";
			//Text colour of ring 2 nodes
			style.color = "#333333";
			style.backgroundColor = "#CAC399";
		
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
doc_rgraph[document_id].loadJSON(document_data[document_id]);
//trigger small animation
doc_rgraph[document_id].graph.eachNode(function(n) {
  var pos = n.getPos();
  pos.setc(-200, -200);
});
doc_rgraph[document_id].compute('end');
doc_rgraph[document_id].fx.animate({
  modes:['polar'],
  duration: 1000
});
//end