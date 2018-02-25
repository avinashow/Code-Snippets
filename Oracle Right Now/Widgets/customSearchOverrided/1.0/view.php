<? /* Overriding FormSubmit's view */ ?>
<div id="rn_<?= $this->instanceID ?>" class="<?= $this->classList ?>">	
 	<rn:block id="preSubmit"/>
    	<input type="submit" id="rn_<?= $this->instanceID ?>_Button" class="btn btn-primary" value="<?= $this->data['attrs']['label_button'] ?>"/>
    <rn:block id="postSubmit"/>
</div>