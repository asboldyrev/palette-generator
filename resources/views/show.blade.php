@extends('template')

@section('content')
	<h2 class="mb-2 mt-3">Исходное изображение</h2>
	<div class="row">
		<div class="col-sm-3">
			<figure class="figure">
				<img class="figure-img img-fluid result-image img-thumbnail rounded" src="{{ $image->originalImage }}" alt="Исходное изображение">
				<figcaption class="figure-caption">Исходное изображение</figcaption>
			</figure>
		</div>
		<div class="col-sm-3">
			<figure class="figure">
				<img class="figure-img img-fluid result-image img-thumbnail rounded" src="{{ $image->cleanedImage }}" alt="Очищенное изображение">
				<figcaption class="figure-caption">Очищенное изображение</figcaption>
			</figure>
		</div>
	</div>

	<h2 class="mb-2 mt-3">Версия 1</h2>
	<div class="row">
		<div class="col-sm-3">
			<figure class="figure">
				<img class="figure-img img-fluid result-image img-thumbnail rounded" src="{{ $image->getPaletteImage('v1') }}" alt="Палитра">
				<figcaption class="figure-caption">Палитра</figcaption>
			</figure>
		</div>
		<div class="col-sm-3">
			<div class="row">
				@foreach ($image->getPalette() as $color)
					<div class="col-6 py-4 text-center" style="background-color: #{{ $color }};">
						<span @if (!light_background($color)) style="color:white;" @endif>#{{ $color }}</span>
					</div>
				@endforeach
			</div>
		</div>
	</div>

	@if (key_exists('v2', $image->images))
		<h2 class="mb-2 mt-3">Версия 2</h2>
		<div class="row">
			<div class="col-sm-3">
				<figure class="figure">
					<img class="figure-img img-fluid result-image img-thumbnail rounded" src="{{ $image->getPaletteImage('v2') }}" alt="Палитра">
					<figcaption class="figure-caption">Палитра</figcaption>
				</figure>
			</div>
			{{-- <div class="col-sm-3">
                <div class="row">
                    @foreach ($image->getPalette() as $color)
                        <div class="col-6 py-4 text-center" style="background-color: #{{ $color }};">
                            <span @if (!light_background($color)) style="color:white;" @endif>#{{ $color }}</span>
                        </div>
                    @endforeach
                </div>
            </div> --}}
		</div>
	@endif
@endsection
