@extends('template')

@section('content')
	<form action="{{ route('images.store') }}" method="post" enctype="multipart/form-data">
		<input type="hidden" name="_token" value="{{ csrf_token() }}" />
		{{-- <div class="mb-3">
			<label for="versionSelect">Алгоритм</label>
			<select class="form-select" name="version">
				<option value="1" @if (($image ?? false) && $image->version == 'v1') selected @endif>V1 (Оригинальный от Яндекса)</option>
				<option value="2" @if (($image ?? false) && $image->version == 'v2') selected @endif>V2</option>
			</select>
		</div> --}}

		<div class="mb-3">
			<label class="form-label" for="fileInput">Файл для обработки</label>
			<input class="form-control" id="fileInput" type="file" name="file" required>
		</div>

		<button class="btn btn-primary">Загрузить</button>
	</form>
@endsection
