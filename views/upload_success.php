<html>
<head>
<title>uploda result</title>
<style type="text/css">
table.ss{
    border-top:1px solid #663300;
    border-left:1px solid #663300;
    border-collapse:collapse;
    border-spacing:0;
    background-color:#ffffff;
    empty-cells:show;
}
.ss th{
    border-right:1px solid #663300;
    border-bottom:1px solid #663300;
    color:#330000;
    background-color:#996633;
    background-position:left top;
    padding:0.3em 1em;
    text-align:center;
}
.ss td{
    border-right:1px solid #663300;
    border-bottom:1px solid #663300;
    padding:0.3em 1em;
}
</style>
</head>
<body>

<h3>ファイルのアップロードに成功しました!</h3>

<table class="ss">
<thead>
	<tr>
		<th>parameter</th><th>value</th>
	</tr>
</thead>
<tbody>
	<tr><td>Original</td><td><?php echo $upload_data['file_name'] ?></td></tr>
	<tr><td>分割数</td><td><?php echo $upload_data['num'] ?></td></tr>
	<tr><td>冗長数</td><td><?php echo $upload_data['rdd'] ?></td></tr>
	<tr><td>分散アルゴリズム</td><td><?php echo $upload_data['alg'] ?></td></tr>
	<tr><td>Share Size</td><td><?php echo $upload_data['share_size'] ?> K byte</td></tr>
	<tr><td>Share Total Size</td><td><?php echo $upload_data['share_size_total'] ?> K byte ( <?php echo round($upload_data['share_size_total'] / $upload_data[        'file_size'] * 100, 1) . " %"; ?> UP)</td></tr>
	<tr><td>Throughput(Encode)</td><td><?php echo $upload_data['throughput_encode'] ?></td></tr>
	<tr><td>Throughput(Decode)</td><td><?php echo $upload_data['throughput_decode'] ?></td></tr>
	<tr><td>Encode Time</td><td><?php echo $upload_data['encode_time'] ?></td></tr>
	<tr><td>Decode Time</td><td><?php echo $upload_data['decode_time'] ?></td></tr>
	<tr><td>Elapsed Time</td><td>{elapsed_time} sec</td></tr>
	<tr><td>Memory Usage</td><td>{memory_usage}</td></tr>
</tbody>
</table>

<p><?php echo anchor('upload', 'Upload Another File!'); ?></p>

</body>
</html>
