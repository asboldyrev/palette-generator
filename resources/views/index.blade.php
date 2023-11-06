<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Генерация палитры по картинке</title>
	<link rel="stylesheet" href="/css/styles.css">
</head>

<body>
	<div class="container">
		<h1>Генератор палитры по картинке</h1>

		<div class="content">
			<form class="form" action="{{ route('store') }}" method="post" enctype="multipart/form-data">
				<label class="label" for="versionSelect">Алгоритм</label>
				<select class="select" id="versionSelect" name="version">
					<option value="1">V1 (Оригинальный от Яндекса)</option>
					<option value="2">V2</option>
				</select>
				<label class="label" for="fileInput">Файл для обработки</label>
				<input class="file-input" id="fileInput" type="file" name="file" required>
				<button class="button">Загрузить</button>
			</form>

			@yield('result')
		</div>
	</div>
</body>

</html>
