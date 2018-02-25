<!--<div id="rn_<?= $this->instanceID ?>" class="<?= $this->classList ?>">
<div class="container1">-->
<div class="row <?= $this->classList ?>" id=" firstrowcontent_<?= $this->data["attrs"]["prodcat"] ?> rn_<?= $this->instanceID ?>" style="justify-content:center;">
	<div class="col-md-10 offset-md-2" style="display:<?= $this->data["js"]["formdisplay"] ?> ">
	  <h2>Horizontal form</h2>
	  <form class="form-horizontal" action="">
	    <div class="form-group">
	      <label class="control-label col-sm-2" for="email">Email:</label>
	      <div class="col-sm-10">
	        <input type="email" class="form-control" id="email" placeholder="Enter email" name="email">
	      </div>
	    </div>
	    <div class="form-group">
	      <label class="control-label col-sm-2" for="pwd">Password:</label>
	      <div class="col-sm-10">          
	        <input type="password" class="form-control" id="pwd" placeholder="Enter password" name="pwd">
	      </div>
	    </div>
	    <div class="form-group">        
	      <div class="col-sm-offset-2 col-sm-10">
	        <div class="checkbox">
	          <label><input type="checkbox" name="remember"> Remember me</label>
	        </div>
	      </div>
	    </div>
	    <div class="form-group">        
	      <div class="col-sm-offset-2 col-sm-10">
	        <button type="submit" class="btn btn-primary">Submit</button>
	      </div>
	    </div>
	  </form>	
	</div>
	<? if($this->data["attrs"]["prodcat"] == "product") {?>
		<? $display = "none"; ?>
		<? for($i = 0; $i < count($this->data["js"]["products"]["row1"]); $i++) { ?>
			<? $item = $this->data["js"]["products"]["row1"][$i]; ?>
			<div class="box" onclick="document.location='/app/responsive/home/level/<?= $item["level"] ?>/prodid/<?= $item["ID"]?>'">
				<img src="/euf/assets/themes/responsivetmp/img/<?= str_replace("*","",str_replace(" ","_",$item["Name"])).".png";?>" class="img-responisve">
				<span>
					<?= $item["Name"] ?>
				</span>
			</div>
		<? } ?>
	<? } ?>
	<table cellspacing="10px">
		<? if($this->data["attrs"]["prodcat"] == "category") {?>
			<? $k = count($this->data["js"]["categories"]["row1"])/3; ?>
			<? for($i = 0; $i < count($this->data["js"]["categories"]["row1"]); $i++) { ?>
				<tr style="z-index:<?= $k ?>">
					<? $j = $i ?>
					<? while($j < $i + 3) { ?>
						<? $item = $this->data["js"]["categories"]["row1"][$j];?>
						<? if($item != NULL) { ?>
							<td height="100" width="200">
								<button type="button" class="btn btn-primary-outline" style="white-space:normal !important;width:100%;height:100%"  onclick="document.location='/app/responsive/home/level/<?= $item["level"] ?>/catid/<?= $item["ID"]?>'"><?= $item["Name"] ?></button>
							</td>
						<? } ?>
						<? $j++ ?>
					<? } ?>
					<? $i = $j - 1; ?>
				</tr>
				<? $k--; ?>
			<? } ?>
		<? } ?>	
	</table>	
	
</div>

<button id="home" style ='display:<?= $this->data["js"]["display"] ?>' class="btn btn-default" onclick="document.location='/app/responsive/home'"><i class="fa fa-home fa-4"></i></button>
