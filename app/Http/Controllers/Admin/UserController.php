<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuTombol;
use App\Models\Usaha;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lokasi = Usaha::where('id', '!=', '5')->with([
            'd',
            'd.kec'
        ])->orderBy('kd_desa')->get();

        $title = 'Daftar User';
        return view('admin.user.index')->with(compact('title', 'lokasi'));
    }

    public function userLokasi($lokasi)
    {
        if (request()->ajax()) {
            $usaha = Usaha::where('id', $lokasi)->first();
            $users = User::where('lokasi', $usaha->id)->with([
                'l', 'j'
            ])->get();

            return DataTables::of($users)
                ->editColumn('namadepan', function ($row) {
                    return $row->namadepan . ' ' . $row->namabelakang;
                })
                ->editColumn('j.nama_jabatan', function ($row) {
                    if (!$row->l) {
                        return '';
                    }

                    return $row->l->nama_level;
                })
                ->editColumn('j.nama_jabatan', function ($row) {
                    if (!$row->j) {
                        return '';
                    }

                    return $row->j->nama_jabatan;
                })
                ->make(true);
        }

        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $menu = Menu::where('parent_id', '0')->with('child')->get();

        $title = 'User ' . $user->namadepan . ' ' . $user->namabelakang;
        return view('admin.user.detail')->with(compact('title', 'user', 'menu'));
    }

    public function aksesTombol(Request $request, User $user)
    {
        $data = $request->only(['menu']);
        $menu_id = array_keys($data['menu']);

        $menu = Menu::whereNotIn('id', $menu_id)->pluck('id')->toArray();
        $MenuTombol = Menu::whereIn('id', $menu_id)->with('tombol')->get();

        return response()->json([
            'success' => true,
            'view' => view('admin.user.partial.akses_tombol')->with(compact('menu', 'MenuTombol', 'user'))->render()
        ]);
    }

    public function hakAkses(Request $request, User $user)
    {
        $data = $request->only(['akses_menu', 'tombol']);
        $tombol_id = array_keys($data['tombol']);

        $tombol = MenuTombol::whereNotIn('id', $tombol_id)->pluck('id')->toArray();
        $data['akses_tombol'] = implode('#', $tombol);

        unset($data['tombol']);

        User::where('id', $user->id)->update($data);

        return response()->json([
            'success' => true,
            'msg' => 'Hak Akses User ' . $user->namadepan . ' ' . $user->namabelakang . ' berhasil diperbarui.'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
