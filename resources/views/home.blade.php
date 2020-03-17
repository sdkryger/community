@extends('layouts.app')

@section('content')
<div class="container">
  <home-component :groups='{!! json_encode($groups) !!}'></home-component>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in!
                    <br>
                    Groups:
                    @foreach(Auth::user()->groups() as $group)
                      <a href="/groups/{{$group->id}}">{{$group->name}}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
