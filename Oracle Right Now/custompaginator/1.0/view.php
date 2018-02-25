<? /* Overriding Paginator's view */ ?>
<div id="rn_<?= $this->instanceID ?>" class="<?= $this->classList ?> row">
		<div class="col-md-2" id="pagerecords">Page Records: <span id="pagecount"></span></div>
		<div class="col-md-2" id="totalrecords">Total Records: <span id="recordcount"></span></div>
		<div class="col-md-6 col-md-offset-6">
			<ul class="pagination">
				<li id="next"><a>Next</a></li>
			</ul>
			<ul class="pagination" id="pages">
			</ul>
			
			<ul class="pagination">
				<li id="prev"><a>Previous</a></li>
			</ul>
		</div>		
</div>
	