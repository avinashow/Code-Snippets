<? /* Overriding Grid's view */ ?>
<div id="rn_<?= $this->instanceID ?>" class="<?= $this->classList ?>">
   <rn:block id="top"/>	
			
	<div id="rn_<?=$this->instanceID;?>_Alert" role="alert" class="rn_ScreenReaderOnly"></div>
    <rn:block id="preLoadingIndicator"/>
    <div id="rn_<?=$this->instanceID;?>_Loading"></div>
    <rn:block id="postLoadingIndicator"/>
    <div id="rn_<?=$this->instanceID;?>_Content" class="yui3-skin-sam">
        <rn:block id="topContent"/>
        <table id="rn_<?=$this->instanceID;?>_Grid" class="yui3-datatable-table" role="grid">
        <caption><?=$this->data['attrs']['label_caption']?></caption>
        <? if($this->data['attrs']['headers']):?>
            <thead class="yui3-datatable-columns">
            <rn:block id="topHeader"/>
                <tr>
                <? if($this->data['tableData']['row_num']):?>
                    <th class="yui3-datatable-header yui3-datatable-sortable-column"><?=$this->data['attrs']['label_row_number']?></th>
                <? endif;?>
                <? foreach($this->data['tableData']['headers'] as $header):?>
                    <? if(!$header['visible'])
                           continue; ?>
                    <? if($header['width'] !== null):?>
                        <th class="yui3-datatable-header yui3-datatable-sortable-column" style='width:"<?=$header['width'];?>%"'><?=$header['heading'];?></th>
                    <? else:?>
                        <th class="yui3-datatable-header yui3-datatable-sortable-column"><?=$header['heading'];?></th>
                    <? endif;?>
                <? endforeach;?>
                </tr>
            <rn:block id="bottomHeader"/>
            </thead>
        <? endif;?>
      <? if( count($this->data['tableData']['data']) > 0): ?>
            <tbody class="yui3-datatable-data">
            <rn:block id="topBody"/>
            <? for($i = 0; $i < count($this->data['tableData']['data']); $i++): ?>
                <rn:block id="preBodyRow"/>
                <tr role="row" class="<?=($i%2 === 0)?'yui3-datatable-even':'yui3-datatable-odd'?>">
                <? if($this->data['tableData']['row_num']):?>
                    <td role="gridcell" class="yui3-datatable-cell"><?=$this->data['tableData']['start_num'] + $i;?></td>
                <? endif;?>
                <? for($j=0; $j<count($this->data['tableData']['headers']); $j++):?>
                    <? if(!$this->data['tableData']['headers'][$j]['visible'])
                           continue; ?>
					<td role="gridcell" class="yui3-datatable-cell">
				 <? $emprolestr = $this->data['tableData']['data'][$i][9] ?>	
				<? if($j == 5): ?>
				<? if(strpos($this->data['tableData']['data'][$i][9],$this->data['tableData']['data'][$i][$j]) !== false): ?>
					<div style="text-align:center">
						<input name="chkSales" type="checkbox" value= "<? echo $this->data['tableData']['data'][$i][$j]?>" checked/>	
					</div>
					<? else: ?>
					<div style="text-align:center">
						<input type="checkbox" value= "<? echo $this->data['tableData']['data'][$i][$j]?>"/>
					</div>
					<? endif; ?>
					<? elseif($j == 6): ?>
				<? if(strpos($this->data['tableData']['data'][$i][9],$this->data['tableData']['data'][$i][$j]) !== false): ?>
					<div style="text-align:center">
						<input name="chkTech" type="checkbox" value= "<? echo $this->data['tableData']['data'][$i][$j]?>" checked/>
					</div>
					<? else: ?>
					<div style="text-align:center">
						<input type="checkbox" value= "<? echo $this->data['tableData']['data'][$i][$j]?>"/>
					</div>
					<? endif; ?>
				
					<? else: ?>
					<?=($this->data['tableData']['data'][$i][$j]) ? $this->data['tableData']['data'][$i][$j] : '&nbsp;'?>
					<? endif; ?>
					</td>
                <? endfor;?>
                </tr>
                <rn:block id="postBodyRow"/>
            <? endfor;?>
            <rn:block id="bottomBody"/>
            </tbody>
        <? endif;?>
        </table>
        <rn:block id="bottomContent"/>
    </div>
    <rn:block id="bottom"/>

</div>