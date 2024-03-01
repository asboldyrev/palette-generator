@extends('template')

@section('content')
	<div class="row">
		@foreach ($images as $image)
			<div class="col-sm-2 mb-4">
				<div class="card">
					<img class="card-img-top result-image" src="{{ $image->paths->originalImage }}" alt="...">

					<table class="table-sm table-borderless border-top table">
						<tr>
							@foreach ($image->getPalette('v1') as $color)
								<td class="py-3" style="background-color: #{{ $color }};"></td>
							@endforeach
						</tr>
					</table>

					<div class="card-body">
						<a class="btn btn-primary" href="{{ route('images.show', ['id' => $image->fileInfo->id]) }}">Посмотреть</a>
					</div>
				</div>
			</div>
		@endforeach
	</div>
@endsection
