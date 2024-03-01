<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>Генерация палитры по картинке</title>

	@vite(['resources/scss/style.scss', 'resources/js/app.js'])
</head>

<body>

	<div class="container-fluid">

		<h1>Генератор палитры по картинке</h1>

		<div class="row gx-5">
			<div class="col-4">
				<form action="{{ route('store') }}" method="post" enctype="multipart/form-data">
					<input type="hidden" name="_token" value="{{ csrf_token() }}" />
					<div class="mb-3">
						<label for="versionSelect">Алгоритм</label>
						<select class="form-select" name="version">
							<option value="1" @if (($image ?? false) && $image->version == 'v1') selected @endif>V1 (Оригинальный от Яндекса)</option>
							<option value="2" @if (($image ?? false) && $image->version == 'v2') selected @endif>V2</option>
						</select>
					</div>

					<div class="mb-3">
						<label class="form-label" for="fileInput">Файл для обработки</label>
						@if ($image ?? false)
							<input class="form-control" id="fileInput" type="file" name="file">
							<input type="hidden" name="image" value="{{ $image->id }}">
						@else
							<input class="form-control" id="fileInput" type="file" name="file" required>
						@endif
					</div>

					<button class="btn btn-primary">Загрузить</button>
				</form>
			</div>
			<div class="col-4">
				@yield('result')

			</div>
			<div class="col-4">
				<div class="list-group">
					@foreach ($models as $_image)
						@if (isset($image) && $_image->id == $image->id)
							<span class="list-group-item list-group-item-action active" href="{{ route('result', ['id' => $_image->id]) }}" aria-current="true">
								{{ $_image->basename }}
							</span>
						@else
							<a class="list-group-item list-group-item-action" href="{{ route('result', ['id' => $_image->id]) }}" aria-current="true">
								{{ $_image->basename }}
							</a>
						@endif
					@endforeach

				</div>
				<div>

				</div>
			</div>
		</div>
	</div>

</body>

</html>
