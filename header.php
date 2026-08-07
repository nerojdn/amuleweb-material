<!DOCTYPE html>
<html>
<head>
    <title>aMule control panel</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php
    if ($_SESSION["auto_refresh"] > 0) {
        echo "<script>
            setInterval(function() {
                if (document.querySelectorAll('input[type=\"checkbox\"]:checked').length > 0) return;
                location.reload();
            }, " . (1000 * $_SESSION["auto_refresh"]) . ");
        </script>";
    }
    ?>
	
	<script language="JavaScript" type="text/JavaScript">
		function formCommandSubmit(command)
		{
			<?php
				if ($_SESSION["guest_login"] != 0) {
						echo 'alert("You logged in as guest - commands are disabled");';
						echo "return;";
				}
			?>
			if ( command == "download" ) {
				var boxchecked = document.querySelectorAll('input[type="checkbox"]:checked');
				var selectedFiles = Object.values(boxchecked).filter(selected => selected.name != 'selectAllFiles').length;
				if (selectedFiles == 0)
					return;
				var res = confirm("Download selected " + (selectedFiles) + " files ?")
				if ( res == false ) {
					return;
				}
			}
			var frm=document.forms.mainform
			frm.command.value=command
			frm.submit()
		}

		function selectAll(check)
		{
			var checkboxes = document.querySelectorAll('input[type="checkbox"]');
			checkboxes.forEach(function(checkbox) {
				checkbox.checked = check.checked;
			});
		}
	</script>
	
    <link href="style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body>
	<div class="layout">