@extends('template')

@section('content')
	@if (request('show-images'))
		<a href="{{ route('images.list') }}">Без картинок</a>
	@else
		<a href="{{ route('images.list', ['show-images' => 1]) }}">С картинками</a>
	@endif

	<div class="row g-5 mb-5 mt-4">
		@foreach ($images as $image)
			<div class="col-sm-2">
				<a class="card" style="overflow: hidden;" href="{{ route('images.show', ['id' => $image->fileInfo->id]) }}">
					@if (request('show-images'))
						<img class="card-img-top result-image" src="{{ $image->paths->originalImage }}" alt="...">
					@endif

					@foreach ($image->palette as $palette)
						<table class="table-sm table-borderless mb-0 table">
							<tr @class(['border-top' => !$loop->first])>
								@foreach ($palette as $color)
									<td class="py-4" style="background-color: #{{ $color }};"></td>
								@endforeach
							</tr>
						</table>
					@endforeach
				</a>
			</div>
		@endforeach
	</div>
@endsection
