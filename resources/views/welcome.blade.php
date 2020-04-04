@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row">
    <div class="col">
      <div class="alert alert-secondary text-center">
        Cymydog - welsh for neighbor
      </div>
      <div class="card">
        <div class="card-header bg-secondary text-white">
          Frequently asked questions
        </div>
        <div class="card-body">
          <div class="list-group">
            <div class="list-group-item">
              <h4>What is it?</h4>
               Platform to help with being a good neighbor - at this point, specifically for sharing things (resources)
            </div>
            <div class="list-group-item">
              <h4>Who is it for?</h4>
              Anyone that has some things they could share with others around them
            </div>
            <div class="list-group-item">
              <h4>How does it work?</h4>
              Here's the basiscs:
              <ul>
                <li>Register to create an account</li>
                <li>Create a group you'd like to share with. Remember, it's pretty difficult to share with someone on the other side of the world.
                So, groups should be sets of people near you. Maybe a group for your neighbors. One for your co-workers. One for extended family. 
                One for your club, church...</li>
                <li>Join an existing group - If some else you know has already created a group, ask them for the group number. Then, request to join the group.</li>
                <li>List your things (we call them resources)</li>
                <li>Set which groups can access your resource. Some things may be viewable/accessible for all of your groups. Maybe some things are only available 
                to smaller/closer groups you're in.</li>
                <li>Request the use of resources from others in your group</li>
                <li>Review and approve requests for the use of your resources</li>
              </ul>
            </div>
          </div>
        </div>  
      </div>
    </div>
  </div>
</div>
@endsection