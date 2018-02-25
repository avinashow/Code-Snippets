<div id="rn_<?= $this->instanceID ?>" class="<?= $this->classList ?>">
<? $uri = $_SERVER['REQUEST_URI'];?>
    <div id="rn_<?=$this->instanceID;?>_Content" class="yui3-skin-sam">
		<rn:block id="topContent"/>
        <table id="rn_<?=$this->instanceID;?>_Grid" class="table table-hover table-striped table-bordered" role="grid">
			<thead class="thead-inverse">
				 <rn:block id="topHeader"/>
                <tr>
                <? $headerNumber = 0; ?>
                <? foreach($this->data['tabledata']['headers'] as $header):?>
                		 <rn:block id="headerData">
						<th scope="row" class="yui3-datatable-header rn_GridColumn_<?=$header['col_id'];?>"><?=$header['heading'];?></th>
						 </rn:block>
				 <? $headerNumber++; ?>
                <? endforeach;?>
				</tr>
				 <rn:block id="bottomHeader"/>
			</thead>
			 <? if(count($this->data['tabledata']['data']) > 0): ?>
            <tbody class="yui3-datatable-data">
            <rn:block id="topBody"/>
            <? for($i = 1; $i < count($this->data['tabledata']['data'])+1; $i++): ?>
                <rn:block id="preBodyRow"/>
                <tr role="row" class="<?=($i % 2 === 0) ? 'yui3-datatable-even' : 'yui3-datatable-odd'?>">
                <? if($this->data['tabledata']['row_num']):?>
                <td role="gridcell" class="yui3-datatable-cell"><?= $this->data['tabledata']['start_num'] + $i;?></td>
                <? endif;?>
                <? for($j = 0; $j < count($this->data['js']['headers']); $j++):?>
                        <td role="gridcell" class="yui3-datatable-cell">
                        <rn:block id="columnData">
                        <? if($this->data['tabledata']['headers'][$j]['heading'] === "Serial Number"):?>
                        	 <? if(strpos($uri,'/app/other_dealer_assets') !== false):?>
                        	 <a href= "/app/other_assets_details/a_id/<? echo $this->data['tabledata']['assetid'][$i - 1] ?>"><?= $this->data['tabledata']['data'][$i][$this->data["js"]["headers"][$j]["heading"]] ?></a> 
                        	<? else: ?>
                        	<a href= "/app/asset_details/a_id/<? echo $this->data['tabledata']['assetid'][$i - 1] ?>"><?= $this->data['tabledata']['data'][$i][$this->data["js"]["headers"][$j]["heading"]] ?></a> 
                        	<? endif;?>
                        	<? else: ?>
                        <? if($this->data['tabledata']['headers'][$j]['heading'] === "Transfer date"):?>
								<? $transferDateStr = str_replace("'", "", $this->data['tabledata']['data'][$i][$this->data["js"]["headers"][$j]["heading"]]);
									$transferDate = strtotime($transferDateStr);
								    $transferDateFormat = date('m/d/Y',$transferDate);
									echo $transferDateFormat;
								?>
							<? else:?>
                          <?= $this->data['tabledata']['data'][$i][$this->data["js"]["headers"][$j]["heading"]] ?>  
                        <? endif;?>
                        <? endif;?>
                        </rn:block>
                    </td>
                <? endfor;?>
                </tr>
                <rn:block id="postBodyRow"/>
            <? endfor;?>
            <rn:block id="bottomBody"/>
            </tbody>
        <? endif;?>

		</table>
	</div>
	<div><?= $links ?></div>
</div>