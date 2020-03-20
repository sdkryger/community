@extends('layouts.app')

@section('content')
<div class="container">
  <resource-edit-component :resource="{{$resource}}" csrf="{{csrf_token()}}"></resource-edit-component>
</div>
@endsection