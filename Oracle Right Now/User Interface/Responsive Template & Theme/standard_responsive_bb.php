<!DOCTYPE html>
<html>
<head>
<style>
	.footer .category1 a {
			    text-decoration: none;
			    color: black;
			    
			    display: inline-block;
			    padding: 5px 20px;
			    margin: 1px;
			    border-radius:4px;
			    margin-top: 6px;
				-webkit-transition: all 1s ease;
			    //background-color: black;
			    //border: solid 1px #fff;
			}
	.footer .category1 a:hover {
		text-decoration: underline;
		font-size: 20px;
	}
	

</style>
<title>Home Page</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0" />
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.1/css/tether.min.css"/>
<rn:theme path="/euf/assets/themes/responsivetmp/css" css="main.css, font-awesome.min.css,  flag-icon.min.css" />
</head>
<body data-spy="scroll" data-target="#myScrollspy">
<div class="container">
  <div class="logo d-inline"> <img class="img-responsive" src="/euf/assets/themes/responsivetmp/img/beachbody_logo_site.png"> </div>
  <div class="d-inline" style="height:50px; width:50px; position: absolute; top: 10px;right:70px;">
  	<select class="d-inline selectpicker" data-width="fit" >
		  <option data-content='<span class="flag-icon flag-icon-us"></span> English'>English</option>
		  <option  data-content='<span class="flag-icon flag-icon-gb"></span> UK'>UK</option>
		  <option  data-content='<span class="flag-icon flag-icon-ca"></span> Canada'>Canada</option>
	  </select>
  </div>
</div>
<br/>
<div class="first-row">
  <div id="fixrow">
    <div class="container">
      <div class="stick" id="myScrollspy"> <a id="#">Get Help and Learn</a>|<a id="contactus">Contact Us</a> </div>
    </div>
  </div>
  <div class="container">
  <div class="banner_content">
    <div class="banner_text">
      <h1>Welcome to BeachBody Support</h1>
      <h5>We're here to help</h5>
    </div>
      <div id="form" class="search_wrap">
        <form class="form-horizontal" onsubmit="return false;">
          <rn:container report_id="176">
            <div class="form-group form-group-md">
              <rn:widget path="search/KeywordText" label_text="" initial_focus="true"/>
              <span class="input-group-btn">
              <rn:widget path="search/SearchButton" report_page_url="/app/responsive/answerList"/>
              </span> </div>
          </rn:container>
        </form>
      </div>
      </div>
  </div>
</div>
	<rn:page_content />
	<br/>
	<div class="row">
		<div style="display:flex;justify-content:center">
			<a href="#" class="btn btn-lg btn-primary">Contact Us</a>
		</div>
	</div>
	<br/>

<footer class="footer" style="background-image:url('https://img1.beachbodyimages.com/beachbody/image/upload/v1512693433/ola/misc/3WY_HP_Banner_1350.jpg')" >
	<div class="container-fluid" >
		 <div class="row" style="margin-left:50px;"	>
				<div class="col-sm-12">
						<!--<div class="row">
							<div style="display:flex;justify-content:center">
								<a href="#" class="btn btn-primary">Contact Us</a>
							</div>
						</div>
						<br/>-->
						<div class="row">
							<div class="category1">
								<a href="#">Beachbody on Demand</a>
								<a href="#">Beachbody Challenge</a>
								<a href="#">Coach Office</a>
								<a href="#">Be a coach</a>
								<a href="#">Beachbody LIVE!</a>
								<a href="#">My Challenge Tracker</a>
								<a href="#">Community</a>
								<a href="#">Shop</a>
							</div>
						</div>
						
				</div>
		   </div>
		        
	</div>	
</footer>

</body>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.2/css/bootstrap-select.min.css"/>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.2/js/bootstrap-select.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js"></script>
<script src="/euf/assets/themes/responsivetmp/js/main.js"></script>
<script>
	$(function(){
	    $('.selectpicker').selectpicker();
		/*$("td").hover(function(e){
			$(this).stop().animate({"width":"300px","height":"200px"}, "slow");
			 //$(this).css({"width":"300px","height":"200px"});
		},function(e){
			$(this).stop().animate({"width":"200px","height":"100px"}, "slow");
			//$(this).css({"width":"200px","height":"100px"});
		});*/
	});
</script>


</html>