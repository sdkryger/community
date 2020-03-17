@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Request to join a group</div>

                <div class="card-body">
                  <form method="POST" action="/groups/joinRequest">
                    @csrf

                    <div class="form-group row">
                      <label for="name" class="col-md-4 col-form-label text-md-right">Group number</label>
                        <div class="col-md-6">
                          <input id="id" type="text" class="form-control" name="id" required autocomplete="id" autofocus>
                        </div>
                    </div>

                    <div class="form-group row mb-0">
                      <div class="col-md-8 offset-md-4">
                        <button type="submit" class="btn btn-primary">
                          Request to Join
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
