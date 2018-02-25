<div id="rn_<?= $this->instanceID ?>" style="display:<?= $this->data["js"]["visibility"] ?>">
	<label><?= $this->data["attrs"]["label_input"]?>:</label>
	<? if($this->data["js"]["is_menu"]) {?>
		<select name="<?= $this->data["attrs"]["name"]?>" value="" id="rn_<?= $this->instanceID ?>_option">
			<option value="">Choose Value</option>
			<? for($i = 0; $i < count($this->data["js"]["menuitems"]); $i++) { ?>
				<option value="<? echo $this->data["js"]["menuitems"][$i]->ID ?>"><? echo $this->data["js"]["menuitems"][$i]->LookupName ?></option>
			<? } ?>
		</select>
	<? } else { ?>
		<input type="text"  id="rn_<?= $this->instanceID?>_txt" name="<?= $this->data["attrs"]["name"]?>" value="">	
	<? } ?>
</div>
