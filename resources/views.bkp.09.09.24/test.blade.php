@extends('layout.app')
@section('content')
	@foreach($image_details as $image)
		{{ $image['IMAGE_CODE'] }}
		<img src="data:image/{{ $image['IMAGE_TYPE'] }};base64,{{ $image['IMAGE_DATA'] }}"><br><br>
	@endforeach

@endsection
