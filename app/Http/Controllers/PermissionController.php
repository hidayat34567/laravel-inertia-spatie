<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller implements HasMiddleware
{
    public static function middleware()
     // Menentukan middleware untuk setiap method tertentu pada controller ini.

    {
        return [
            new Middleware('permission:permissions index', only: ['index']),
             // Hanya method index yang membutuhkan izin 'permissions index'.
            new Middleware('permission:permissions create', only: ['create', 'store']),
             // Hanya method create dan store yang membutuhkan izin 'permissions create'.
            new Middleware('permission:permissions edit', only: ['edit', 'update']),
            // Hanya method edit dan update yang membutuhkan izin 'permissions edit'.
            new Middleware('permission:permissions delete', only: ['destroy']),
            // Hanya method destroy yang membutuhkan izin 'permissions delete'.

        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
       /**
     * Menampilkan daftar sumber daya (permissions).
     */
    {
        //  get permissions
        $permissions = Permission::select('id', 'name')
          // Mengambil data permission dengan memilih hanya id dan nama.
            ->when($request->search,fn($search) => $search->where('name', 'like', '%'.$request->search.'%'))
             // Menambahkan pencarian berdasarkan nama jika ada input pencarian.
            ->latest() 
            // Mengurutkan data berdasarkan tanggal terbaru.
            ->paginate(6)->withQueryString();
            // Menggunakan pagination dengan 6 data per halaman dan mempertahankan query string dalam URL.

        // render view
        return inertia('Permissions/Index', ['permissions' => $permissions,'filters' => $request->only(['search'])]);
           // Mengembalikan tampilan dengan data permissions dan filters (pencarian).
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
      /**
     * Menampilkan form untuk membuat permission baru.
     */

    {
        // render view
        return inertia('Permissions/Create');
         // Mengembalikan tampilan untuk membuat permission baru.
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
      /**
     * Menyimpan permission baru ke dalam database.
     */
    {
        // validate request
        $request->validate(['name' => 'required|min:3|max:255|unique:permissions']);
         // Validasi input request untuk memastikan 'name' ada, panjangnya minimal 3 karakter dan maksimal 255 karakter, serta unik di tabel permissions.

        // create new permission data
        Permission::create(['name' => $request->name]);
         // Membuat permission baru berdasarkan input 'name' yang ada di request.

        // render view
        return to_route('permissions.index');
 // Mengarahkan pengguna kembali ke halaman index permissions setelah berhasil menambahkan data baru.
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission)
    {
        // render view
        return inertia('Permissions/Edit', ['permission' => $permission]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
     /**
     * Menampilkan form untuk mengedit permission yang sudah ada.
     */
    {
        // validate request
        $request->validate(['name' => 'required|min:3|max:255|unique:permissions,name,'.$permission->id]);
         // Mengembalikan tampilan untuk mengedit permission yang telah dipilih.

        // update permission data
        $permission->update(['name' => $request->name]);
         // Memperbarui data permission yang telah dipilih berdasarkan input 'name'.

        // render view
        return to_route('permissions.index');
         // Mengarahkan pengguna kembali ke halaman index permissions setelah berhasil memperbarui data.
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
     /**
     * Menghapus permission yang telah dipilih.
     */
    {
        // delete permissions data
        $permission->delete();
         // Menghapus permission dari database.

        // render view
        return back();
         // Kembali ke halaman sebelumnya setelah berhasil menghapus data.
    }
}