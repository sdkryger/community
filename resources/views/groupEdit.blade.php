@extends('layouts.app')

@section('content')
<div class="container">
  <group-edit-component :group="{{$group}}"></group-edit-component>
</div>
@endsection
