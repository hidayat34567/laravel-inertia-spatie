<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
class UserController extends Controller implements HasMiddleware
{

    public static function middleware()
     // Mendefinisikan middleware untuk setiap metode pada controller ini
    {
        return [
            new Middleware('permission:users index', only : ['index']),
                   // Menentukan bahwa hanya user dengan izin 'users index' yang dapat mengakses method 'index'.
            new Middleware('permission:users create', only : ['create', 'store']),
            // Menentukan bahwa hanya user dengan izin 'users create' yang dapat mengakses method 'create' dan 'store'.
            new Middleware('permission:users edit', only : ['edit', 'update   ']),
             // Menentukan bahwa hanya user dengan izin 'users edit' yang dapat mengakses method 'edit' dan 'update'.
            new Middleware('permission:users delete', only : ['destroy']),
            // Menentukan bahwa hanya user dengan izin 'users delete' yang dapat mengakses method 'destroy'.

        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
            // Mengambil data user beserta role yang terkait
    {
        // get all users
        $users = User::with('roles')
         // Memuat relasi roles pada user
            ->when(request('search'), fn($query) => $query->where('name', 'like', '%'.request('search').'%'))
             // Pencarian berdasarkan nama user
            ->latest()
            // Mengurutkan berdasarkan data terbaru
            ->paginate(6);
            // Menggunakan pagination dengan 6 data per halaman


        // render view
        return inertia('Users/Index', ['users' => $users,'filters' => $request->only(['search'])]);
         // Mengembalikan tampilan dengan data user dan filter pencarian
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    /**
     * Menampilkan form untuk membuat user baru.
     */
    {
         // get roles
         $roles = Role::latest()->get();
          // Mengambil roles yang ada di database
         // render view
         return inertia('Users/Create', ['roles' => $roles]);
          // Mengembalikan tampilan untuk membuat user baru dengan data roles
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         // validate request
         $request->validate([
              // Validasi input request untuk memastikan data yang dimasukkan benar
            'name' => 'required|min:3|max:255',
            // Nama user harus ada dan memiliki panjang minimal 3 karakter
            'email' => 'required|email|unique:users',
            // Email harus unik dan valid
            'password' => 'required|confirmed|min:4',
             // Password harus ada, dikonfirmasi dan minimal 4 karakter
            'selectedRoles' => 'required|array|min:1',
            // Memastikan ada role yang dipilih

        ]);

        // create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // attach roles
        $user->assignRole($request->selectedRoles);

        // render view
        return to_route('users.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        // get roles
        $roles = Role::where('name', '!=', 'super-admin')->get();

        // load roles
        $user->load('roles');

        // render view
        return inertia('Users/Edit', ['user' => $user, 'roles' => $roles]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // validate request
        $request->validate([
            'name' => 'required|min:3|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'selectedRoles' => 'required|array|min:1',
        ]);

        // update user data
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // attach roles
        $user->syncRoles($request->selectedRoles);

        // render view
        return to_route('users.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // delete user data
        $user->delete();

        // render view
        return back();
    }
}