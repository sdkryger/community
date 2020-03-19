<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Resource;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $user1 = new User;
      $user1->name = 'Bob';
      $user1->email = 'bob@bob.com';
      $user1->password = Hash::make('password');
      $user1->save();

      $user2 = new User;
      $user2->name = 'Doug';
      $user2->email = 'doug@doug.com';
      $user2->password = Hash::make('password');
      $user2->save();

      $resource = new Resource;
      $resource->title = 'Canoe';
      $user1->resources()->save($resource);
    }
}
