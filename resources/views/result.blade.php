@extends('index')

@section('result')
	<div class="image-section">
		<div class="image-block">
			<img class="image" src="step1.jpg" alt="Исходное изображение">
			<p class="description">Исходное изображение</p>
		</div>
		<div class="image-block">
			<img class="image" src="step2.jpg" alt="Очищенное изображение">
			<p class="description">Очищенное изображение</p>
		</div>
		<div class="image-block">
			<img class="image" src="step3.jpg" alt="Палитра">
			<p class="description">Палитра</p>
		</div>
	</div>

	<div class="color-palette">
		<div class="color-block">
			<div class="color" style="background-color: #ff0000;">
				<p class="color__code">#FF0000</p>
			</div>
		</div>
		<div class="color-block">
			<div class="color" style="background-color: #00ff00;">
				<p class="color__code">#00FF00</p>
			</div>
		</div>
		<div class="color-block">
			<div class="color" style="background-color: #0000ff;">
				<p class="color__code">#0000FF</p>
			</div>
		</div>
		<div class="color-block">
			<div class="color" style="background-color: #ffff00;">
				<p class="color__code">#FFFF00</p>
			</div>
		</div>
	</div>
@endsection
