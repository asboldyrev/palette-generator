@extends('index')

@section('result')
	<div>
		<div class="image-section">
			<div class="image-block">
				<img class="image" src="{{ $image->originalImage }}" alt="Исходное изображение">
				<p class="description">Исходное изображение</p>
			</div>
			<div class="image-block">
				<img class="image" src="{{ $image->cleanedImage }}" alt="Очищенное изображение">
				<p class="description">Очищенное изображение</p>
			</div>
			<div class="image-block">
				<img class="image" src="{{ $image->getPaletteImage() }}" alt="Палитра">
				<p class="description">Палитра</p>
			</div>
		</div>

		<div class="color-palette">
			@foreach ($image->getPalette() as $color)
				<div class="color-block">
					<div class="color" style="background-color: #{{ $color }};">
						<p class="color__code" @if (!light_background($color)) style="color:white;" @endif>#{{ $color }}</p>
					</div>
				</div>
			@endforeach
		</div>
	</div>
@endsection
