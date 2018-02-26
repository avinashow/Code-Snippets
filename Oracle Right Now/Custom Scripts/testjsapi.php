<html>
	<head>	
		<script>
			function onsave() {
				document.getElementById("name").value = "dichik";
				document.getElementById("form").submit();
				window.external.savecomplete(false, "notsaved");
			}
		</script>
	</head>
	<body>
		<form id="form" action="">
			<input type="text" id="name" name="name" value="<?php echo $_GET["incident"] ?>"/>
		</form>
	</body>
</html>
<?php
	if (isset($_GET["incident"])) {
		echo $_GET["incident"];
	}
	if(isset($_REQUEST["name"])) {
		echo $_REQUEST["name"];
	}
?>