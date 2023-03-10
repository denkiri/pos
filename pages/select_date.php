
<html>
<head>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.js"></script>
	<script>
		function suggest(inputString){
			if(inputString.length == 0) {
				$('#suggestions').fadeOut();
			} else {
				$('#country').addClass('load');
				$.post("autosuggestdate.php", {queryString: ""+inputString+""}, function(data){
					if(data.length >0) {
						$('#suggestions').fadeIn();
						$('#suggestionsList').html(data);
						$('#country').removeClass('load');
					}
				});
			}
		}

		function fill(thisValue) {
			$('#country').val(thisValue);
			setTimeout("$('#suggestions').fadeOut();", 600);
		}

	</script>

	<style>
		#result {
			height:20px;
			font-size:16px;
			font-family:Arial, Helvetica, sans-serif;
			color:#333;
			padding:5px;
			margin-bottom:10px;
			background-color:#FFFF99;
		}
		#country{
			border: 1px solid #999;
			background: #EEEEEE;
			padding: 5px 10px;
			box-shadow:0 1px 2px #ddd;
			-moz-box-shadow:0 1px 2px #ddd;
			-webkit-box-shadow:0 1px 2px #ddd;
		}
		.suggestionsBox {
			position: absolute;
			left: 10px;
			margin: 0;
			width: 268px;
			top: 40px;
			padding:0px;
			background-color: #000;
			color: #fff;
		}
		.suggestionList {
			margin: 0px;
			padding: 0px;
		}
		.suggestionList ul li {
			list-style:none;
			margin: 0px;
			padding: 6px;
			border-bottom:1px dotted #666;
			cursor: pointer;
		}
		.suggestionList ul li:hover {
			background-color: #FC3;
			color:#000;
		}
		.load{
			background-image:url(loader.gif);
			background-position:right;
			background-repeat:no-repeat;
		}

		#suggest {
			position:relative;
		}
		.combopopup{
			padding:3px;
			width:268px;
			border:1px #CCC solid;
		}

	</style>	



	<!-- Bootstrap Core CSS -->
	<link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

	<!-- MetisMenu CSS -->
	<link href="../vendor/metisMenu/metisMenu.min.css" rel="stylesheet">

	<!-- Custom CSS -->
	<link href="../dist/css/sb-admin-2.css" rel="stylesheet">

	<!-- Custom Fonts -->
	<link href="../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

</head>
<body onLoad="document.getElementById('country').focus();">
	<form action="daySalesReport.php" method="get" class = "form-group">
		<div id="ac">
			<input type="text"required value="" class = "form-control" name="selectedDate" id="country" onkeyup="suggest(this.value);" onblur="fill();" class="" autocomplete="off" placeholder="Enter Date"  /><br />

			<div class="suggestionsBox" id="suggestions" style="display: none;">
				<div class="suggestionList" id="suggestionsList"> &nbsp; </div>
			</div>
			<input class="btn btn-primary btn-block" type="submit"  value="Search"/>
		</div>
	</form>
	
</body>
</html>
