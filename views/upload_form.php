<html>
<head>
<title>upload form</title>
</head>
<body>

<?php echo $error;?>

<?php echo form_open_multipart('upload/do_upload');?>

<input type="file" name="userfile" size="20" />

<br />
<br />

<?php
echo form_fieldset('Algorithm');
echo form_radio('a', 'ss') . "Secret Share Schema<br>";
echo form_radio('a', 'id',TRUE) . "Information Dipersal Algorithm<br>";
echo form_radio('a', 'zfec') . "Foward Error Correction Code<br>";
echo form_fieldset_close();
?>

<?php
echo form_fieldset('Redundant');
echo form_radio('k', '0') . "n<br>";
echo form_radio('k', '1',TRUE) . "n-1<br>";
echo form_radio('k', '2') . "n-2<br>";
echo form_fieldset_close();
?>

<?php
echo form_fieldset('Number of Dipersal');
echo form_radio('n', '4', TRUE) . "4 ";
echo form_radio('n', '5') . "5 ";
echo form_radio('n', '6') . "6 ";
echo form_radio('n', '7') . "7 ";
echo form_radio('n', '8') . "8 ";
echo form_radio('n', '9') . "9";
echo form_fieldset_close();
?>

<br />
<input type="submit" value="Start" />

</form>

</body>
</html>
