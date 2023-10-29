@extends('index')

@section('result')
	<div>
		<div class="image-section">
			<div class="image-block">
				<img class="image" src="{{ $images['original'] }}" alt="Исходное изображение">
				<p class="description">Исходное изображение</p>
			</div>
			<div class="image-block">
				<img class="image" src="{{ $images['cleaned'] }}" alt="Очищенное изображение">
				<p class="description">Очищенное изображение</p>
			</div>
			<div class="image-block">
				<img class="image" src="{{ $images['resize'] }}" alt="Палитра">
				<p class="description">Палитра</p>
			</div>
		</div>

		<div class="color-palette">
			@foreach ($palette as $color)
				<div class="color-block">
					<div class="color" style="background-color: #{{ $color }};">
						<p class="color__code">#{{ $color }}</p>
					</div>
				</div>
			@endforeach
		</div>
	@endsection
