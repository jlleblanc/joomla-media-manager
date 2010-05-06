<?php
defined( '_JEXEC' ) or die;

echo JHtml::_('behavior.keepalive');

?>

<div id="mediaman">
	<div id="MMContainer">
		<div id="MMAreaMain" class="column">
			<fieldset id="MMToolbarPane">
				Toolbar
			</fieldset>
			<fieldset id="MMFiles">
				<legend>Files</legend>
			</fieldset>
			<fieldset id="MMUploadPane">
				<legend>Upload</legend>
			</fieldset>
		</div>
		
		<div id="MMAreaFolders" class="column">
			<fieldset id="MMFoldersPane" class="scrollable">
				<legend>Folders</legend>
				<div id="media-tree_tree">
				</div>
				<ul id="media-tree">
					<li><a href="#">node</a></li>
					<li><a href="#">test</a>
						<ul>
							<li><a href="#">test1</a></li>
							<li><a href="#">test2</a></li>
						</ul>
					</li>
				</ul>
			</fieldset>
			<fieldset id="MMFolderActionsPane" class="scrollable">
				<legend>Actions</legend>
			</fieldset>
		</div>
	
		<div id="MMAreaDetails" class="column">
			<fieldset id="MMDetailsPane">
				<legend>Details</legend>
			</fieldset>
		</div>
	</div>
	
	<div id="MMAreaStatus">
		<fieldset id="MMStatusPane">
			Status
		</fieldset>
	</div>
</div>

<script type="text/javascript">
var tree = new MooTreeControl({
	div: 'media-tree_tree',
	mode: 'folders',
	grid: true,
	theme: '../media/system/images/mootree.gif',
	onClick:
		function(node){
			alert('Click');
		}.bind(this)
	},
	{
		text: 'Media',
		open: true,
		data: { url: '#' }
	});
tree.adopt('media-tree');

</script>