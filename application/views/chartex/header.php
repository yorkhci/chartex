<?php header("Content-Type: text/html; charset=utf-8"); ?>
<!DOCTYPE html> 
<head>
	<title>ChartEx Virtual Workbench v1.3</title>
    <meta charset="utf-8"> 
    <link href="<?=asset_url()?>css/chartex.css" rel="stylesheet" type="text/css" />
    <link href="<?=asset_url()?>css/smoothness/jquery-ui-1.10.3.custom.css" rel="stylesheet" type="text/css" />
    <link href="<?=asset_url()?>css/jqtree.css" rel="stylesheet" type="text/css" />
    
    <script type="text/javascript">var baseurl = "<?php print base_url(); ?>";</script>
    
	<script src="<?=asset_url()?>js/jquery-1.10.1.min.js" type="text/javascript"></script>
    <script src="<?=asset_url()?>js/jquery-ui-1.10.3.min.js" type="text/javascript"></script>
	<script src="<?=asset_url()?>js/tablesorter/jquery.tablesorter.js" type="text/javascript"></script>
    <script src="<?=asset_url()?>js/tablesorter/addons/pager/jquery.tablesorter.pager.js" type="text/javascript" ></script>
	<script src="<?=asset_url()?>js/tree.jquery.js" type="text/javascript"></script>
    <script src="<?=asset_url()?>js/jit.js" type="text/javascript"></script>
    <!--<script src="<?=asset_url()?>js/ui.tabs.paging.js" type="text/javascript"></script>-->
	
	<script src="<?=asset_url()?>js/global.js" type="text/javascript"></script>
	<script src="<?=asset_url()?>js/document.js" type="text/javascript"></script>
    <script src="<?=asset_url()?>js/document_vis.js" type="text/javascript"></script>
    <script src="<?=asset_url()?>js/entity_tree.js" type="text/javascript"></script>
	<script src="<?=asset_url()?>js/entity_vis.js" type="text/javascript"></script>
    <script src="<?=asset_url()?>js/tabs.js" type="text/javascript"></script>
</head>
<body>
	<div id="heading">
		<div id="inner_heading">
			<h1>ChartEx</h1>
    		<h2><a href="">Virtual Workbench <span>v1.3</span></a></h2>
		</div>
	</div>

	<div id="content">