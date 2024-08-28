<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class UsersController extends Controller
{
    public function index()
    {
        return view('users.index', [
            'page' => 'users'
        ]);
    }

    public function table()
    {
        return DataTables::of(User::where('username', '!=', 'pipernigrum')->where('id', '!=', auth()->user()->id)->get())
            ->addIndexColumn()
            ->removeColumn('password')
            ->removeColumn('remember_token')
            ->removeColumn('admin')
            ->setRowAttr([
                'style' => 'text-align: center;',
                'class' => 'vertical-center station-row'
            ])
            ->addColumn('role', function($data) {
                return view('users.role', ['data'=> $data]);
            })
            ->addColumn('action', function($data) {
                return view('users.button', ['data'=> $data]);
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function add()
    {
        return view('users.add', [
            'page' => 'Add User'
        ]);
    }

    public function edit(Request $request)
    {
        $id = $request->input('id');
        $user = User::where('id', $id)->first();

        if (is_null($user)) {
            return abort(404, 'User with id '.$id.' not found.');
        }

        if ($user->username == 'pipernigrum') {
            return abort(500, 'User @pipernigrum can\'t be edited!');
        }

        return view('users.edit', [
            'page' => 'Edit User',
            'user' => $user
        ]);
    }

    public function delete(Request $request)
    {
        $id = $request->input('id');
        $user = User::where('id', $id)->first();

        if (is_null($user)) {
            return response()->json([
                'msg' => "ERROR",
                'info' => 'User with id '.$id.' not found!'
            ]);
        }

        if ($user->username == 'pipernigrum') {
            return response()->json([
                'msg' => "ERROR",
                'info' => 'User @pipernigrum can\'t be deleted!'
            ]);
        }

        User::where('id', $id)->delete();

        return response()->json([
            'msg' => "OK",
            'username' => $user->username
        ]);
    }

    public function username(Request $request)
    {
        $username = $request->input('username');
        $used = User::where('username', $username)->value('id');
        if ($used) {
            $msg = 'ERROR';
        } else {
            $msg = 'OK';
        }

        return response()->json([
            'msg' => $msg,
            'data' => $username
        ]);
    }

    public function submit(Request $request)
    {
        $id = $request->input('id');

        $data = $request->all();
        if ($data['role'] == 'administrator') {
            $data['admin'] = true;
        } else {
            $data['admin'] = false;
        }
        
        if (is_null($id)) {
            User::create($data);
            $key = 'addSuccess';
        } else {
            unset($data['_token'], $data['id'], $data['role']);
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }
            User::where('id', $id)->update($data);
            $key = 'editSuccess';
        }
        
        return redirect()->route('users.index')->with($key, $data['username']);
    }
}
