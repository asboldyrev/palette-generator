<?php
	require_once '../Libs/Colorist.php';
	ini_set('display_errors', 1);
	ini_set('memory_limit', '1024M');
	error_reporting(E_ALL & ~E_NOTICE);

	$uploaddir = '/home/boldyreva/data/sites/ya-colors/tmp/';
	$uploadfile = $uploaddir . basename($_FILES['image']['name']);

	if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile)) {
		$image = \Libs\Colorist::create($uploadfile);
	}
?>
<html>

<head>
	<title></title>
	<style>
		.color {
			width: 100px;
			height: 50px;
			text-align: center;
		}

		.color span {
			display: block;
			padding-top: 16px;
		}

		img {
			max-width: 200px;
		}
	</style>
</head>

<body>

<form action="/" method="post" enctype="multipart/form-data">
	<input name="image" type="file">
	<button type="submit">Рассчитать</button>
</form>

<img src="<?= $image->getFileUrl('-original') ?? NULL; ?>" alt="">
<img src="<?= $image->getFileUrl('-cleaned') ?? NULL; ?>" alt="">
<img src="<?= $image->getFileUrl('-resize') ?? NULL; ?>" alt="">

<?php foreach ($image->getHSLColors() as $color) { ?>
	<div class="color" style="
		background-color: <?= $image->getRGBAsString($color) ?>;
		color: <?= $image->getRGBAsString($color, true) ?>;"
	>
		<span><?= $image->getHEX($color); ?></span>
	</div>
<?php } ?>

</body>

</html>