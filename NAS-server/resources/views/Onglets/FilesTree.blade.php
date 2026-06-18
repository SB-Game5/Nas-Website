<div> 
<?php
include("php_file_tree.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

	<head>
		<title>Example using PHP File Tree for the Web</title>
		<meta http-equiv="Content-Type" content="text/html;charset=windows-1251" />
		<meta xmlns="" name="KEYWORDS" content="">
		<link href="styles/default/default.css" rel="stylesheet" type="text/css" media="screen" />
		
		<!-- Makes the file tree(s) expand/collapsae dynamically -->
		<script src="php_file_tree.js" type="text/javascript"></script>
		
	</head>

	<body>
		
		
		<?php
		
		// This links the user to http://example.com/?file=filename.ext
		//echo php_file_tree($_SERVER['DOCUMENT_ROOT'], "http://example.com/?file=[link]/");

		// This links the user to http://example.com/?file=filename.ext and only shows image files
		//$allowed_extensions = array("gif", "jpg", "jpeg", "png");
		//echo php_file_tree($_SERVER['DOCUMENT_ROOT'], "http://example.com/?file=[link]/", $allowed_extensions);
		
		// This displays a JavaScript alert stating which file the user clicked on
		//echo php_file_tree(".", "javascript:alert('You clicked on [link]');");
		echo php_file_tree("files", "[link]");
		
		?>
		
	</body>


</div>