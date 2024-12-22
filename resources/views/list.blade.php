@extends('template')

@section('content')
	<div class="row">
		@foreach ($images as $image)
			<div class="col-sm-2 mb-4">
				<a class="card" style="overflow: hidden;" href="{{ route('images.show', ['id' => $image->fileInfo->id]) }}">
					<img class="card-img-top result-image" src="{{ $image->paths->originalImage }}" alt="...">

					@foreach ($image->palette as $palette)
						<table class="table-sm table-borderless border-top mb-0 table">
							<tr>
								@foreach ($palette as $color)
									<td class="py-3" style="background-color: #{{ $color }};"></td>
								@endforeach
							</tr>
						</table>
					@endforeach
				</a>
			</div>
		@endforeach
	</div>
@endsection
