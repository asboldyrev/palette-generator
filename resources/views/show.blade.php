@extends('template')

@section('content')
	<h2 class="mb-2 mt-3">Исходное изображение</h2>
	<div class="row">
		<div class="col-sm-3">
			<figure class="figure">
				<img class="figure-img img-fluid result-image img-thumbnail rounded" src="{{ $image->paths->originalImage }}" alt="Исходное изображение">
				<figcaption class="figure-caption">Исходное изображение</figcaption>
			</figure>
		</div>
		<div class="col-sm-3">
			<figure class="figure">
				<img class="figure-img img-fluid result-image img-thumbnail rounded" src="{{ $image->paths->cleanedImage }}" alt="Очищенное изображение">
				<figcaption class="figure-caption">Очищенное изображение</figcaption>
			</figure>
		</div>
	</div>

	@foreach ($image->paths->paletteImage as $version => $image_url)
		<h2 class="mb-2 mt-3">Версия {{ $version }}</h2>
		<div class="row">
			<div class="col-sm-3">
				<figure class="figure">
					<img class="figure-img img-fluid result-image img-thumbnail rounded" src="{{ $image_url }}" alt="Палитра">
					<figcaption class="figure-caption">Палитра</figcaption>
				</figure>
			</div>
			<div class="col-sm-3">
				<div class="row">
					@foreach ($image->getPalette($version) as $color)
						<div class="col-6 py-4 text-center" style="background-color: #{{ $color }};">
							<span @if (!light_background($color)) style="color:white;" @endif>#{{ $color }}</span>
						</div>
					@endforeach
				</div>
			</div>
		</div>
	@endforeach
@endsection
