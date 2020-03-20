@extends('layouts.app')

@section('content')
<div class="container">
  <resource-create-component :csrf="{{csrf_token()}}"></resource-create-component>
</div>
@endsection