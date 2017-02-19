<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Hash;

class UserController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->search;
        $users = User::where('name', 'LIKE', "%$search%")->orderBy('id', 'DESC')->paginate(5);
        $response = [
           'pagination' => [
               'total' => $users->total(),
               'per_page' => $users->perPage(),
               'current_page' => $users->currentPage(),
               'last_page' => $users->lastPage(),
               'from' => $users->firstItem(),
               'to' => $users->lastItem()
           ],
           'data' => $users
        ];

        return response()->json($users);
    }

    
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:users',
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);

        $users = User::create([
           'name' => $request->name,
           'email' => $request->email,
           'password' => Hash::make($request->password)
        ]);

        return response()->json($users);
    }

    public function edit($id)
    {
      $users = User::find($id);

      return response()->json($users);
    }

    
    public function update(Request $request, $id)
    {
        $u = User::findOrFail($id);

        if(!empty($request->password)) {
           $this->validate($request, [
             'password' => 'min:8'
          ]);

           $users = $u->update([
               'password' => Hash::make($request->password)
            ]);
        }
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
        ]);

        $users = $u->update([
           'name' => $request->name,
           'email' => $request->email,
        ]);

        return response()->json($users);
    }

    
    public function destroy($id)
    {
        $u = User::findOrFail($id);
        $u->delete($id);

        return response()->json(['Success']);
    }
}
