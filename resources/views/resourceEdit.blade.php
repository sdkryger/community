@extends('layouts.app')

@section('content')
<div class="container">
  <resource-edit-component :resource="{{$resource}}"></resource-edit-component>
</div>
@endsection