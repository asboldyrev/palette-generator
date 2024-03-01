@extends('index')

@section('result')
	<div class="row">
		<div class="col-6">
			<figure class="figure">
				<img class="figure-img img-fluid result-image img-thumbnail rounded" src="{{ $image->originalImage }}" alt="Исходное изображение">
				<figcaption class="figure-caption">Исходное изображение</figcaption>
			</figure>
		</div>
		<div class="col-6">
			<figure class="figure">
				<img class="figure-img img-fluid result-image img-thumbnail rounded" src="{{ $image->cleanedImage }}" alt="Очищенное изображение">
				<figcaption class="figure-caption">Очищенное изображение</figcaption>
			</figure>
		</div>
		<div class="col-6">
			<figure class="figure">
				<img class="figure-img img-fluid result-image img-thumbnail rounded" src="{{ $image->getPaletteImage() }}" alt="Палитра">
				<figcaption class="figure-caption">Палитра</figcaption>
			</figure>
		</div>
		<div class="col-6">
			<div class="row">
				@foreach ($image->getPalette() as $color)
					<div class="col-6 py-4 text-center" style="background-color: #{{ $color }};">
						<span @if (!light_background($color)) style="color:white;" @endif>#{{ $color }}</span>
					</div>
				@endforeach
			</div>
		</div>
	</div>
@endsection
