@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Create new group</div>

                <div class="card-body">
                  <form method="POST" action="/groups">
                    @csrf

                    <div class="form-group row">
                      <label for="name" class="col-md-4 col-form-label text-md-right">Name</label>
                        <div class="col-md-6">
                          <input id="name" type="text" class="form-control" name="name" required autocomplete="name" autofocus>
                        </div>
                    </div>

                    <div class="form-group row mb-0">
                      <div class="col-md-8 offset-md-4">
                        <button type="submit" class="btn btn-primary">
                          Create group
                        </button>                                
                      </div>
                    </div>

                  </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
