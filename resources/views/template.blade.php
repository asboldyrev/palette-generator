<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>Генерация палитры по картинке</title>

	@vite(['resources/scss/style.scss', 'resources/js/app.js'])
</head>

<body>
	<nav class="navbar navbar-expand-lg bg-body-tertiary">
		<div class="container-fluid">
			<a class="navbar-brand" href="#">Ya colors</a>
			<button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" type="button" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="navbar-collapse collapse" id="navbarSupportedContent">
				<ul class="navbar-nav mb-lg-0 mb-2 me-auto">
					<li class="nav-item">
						<a class="nav-link {{ request()->route()->getName() == 'images.create' ? 'active' : '' }}" aria-current="page" href="{{ route('images.create') }}">Создать палитру</a>
					</li>
					<li class="nav-item">
						<a class="nav-link {{ !in_array(request()->route()->getName(), ['versions', 'images.create']) ? 'active' : '' }}" href="{{ route('images.list') }}">Каталог</a>
					</li>
					<li class="nav-item">
						<a class="nav-link {{ request()->route()->getName() == 'versions' ? 'active' : '' }}" href="{{ route('versions') }}">Описание версий</a>
					</li>
				</ul>
			</div>
		</div>
	</nav>

	<div class="container-fluid">
		@yield('content')
	</div>


</body>

</html>
