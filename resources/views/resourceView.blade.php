@extends('layouts.app')

@section('content')
<div class="container">
  <resource-view-component :resource="{{$resource}}" csrf="{{csrf_token()}}"></resource-view-component>
</div>
@endsection