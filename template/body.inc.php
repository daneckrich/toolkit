<!DOCTYPE html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta http-equiv="X-UA-Compatible" content="IE=9" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">

		<title><?php echo SITENAME ?></title>

		<?php $this->cssOut(); ?>
		<?php $this->jsIEOut(); ?>
			
	</head>
	<body> 
		<div id="wrapper">
		
			<?php include_once "nav.inc.php"; ?>
		
			<div id="page-wrapper" style="height:100%">	
				<div id="page-content"></div>					
			</div>
			
			<?php include_once "footer.inc.php"; ?>	
			
		</div>

		<div class="modal fade in" id="ajax_loading" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="text-center" style="background:transparent;">
					<i class="fa fa-spinner fa-spin fa-5x fa-fw" style="margin-top:20%;"></i>
					<br><br>
					<span>Loading...</span>
				</div>
				<!-- /.modal-content -->
			</div>
			<!-- /.modal-dialog -->
		</div>
		
		<?php $this->jsOut(); ?>

		<script>
			$(function() {
				$('#main_content').prependTo('#page-content').show();
			});
		</script>

	</body>
</html>