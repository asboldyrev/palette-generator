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
					<option value="1" @if (($image ?? false) && $image->version == 'v1') selected @endif>V1 (Оригинальный от Яндекса)</option>
					<option value="2" @if (($image ?? false) && $image->version == 'v2') selected @endif>V2</option>
				</select>
				<label class="label" for="fileInput">Файл для обработки</label>
				@if ($image ?? false)
					<input class="file-input" id="fileInput" type="file" name="file">
					<input type="hidden" name="image" value="{{ $image->id }}">
				@else
					<input class="file-input" id="fileInput" type="file" name="file" required>
				@endif
				<button class="button">Загрузить</button>
			</form>

			@yield('result')

			<div class="models">
				@foreach ($models as $image)
					<a class="models__item" href="{{ route('result', ['id' => $image->id]) }}">{{ $image->basename }}</a>
				@endforeach
			</div>
		</div>
	</div>
</body>

</html>
