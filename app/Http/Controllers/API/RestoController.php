<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestoController extends Controller
{
    public function createRestoMenu(Request $request)
    {
        $resto                         = new Restaurant();

        $pesan = [
            'nama_resto.required'      => 'Nama restoran wajib diisi.',
            'alamat.required'          => 'Password wajib diisi.',
            'telp.required'            => 'Telepon wajib diisi.',
            'telp.numeric'             => 'Telepon harus berupa angka.',
            'telp.min'                 => 'Telepon tidak valid.',
            'telp.unique'              => 'Nomor telepon tertaut pada akun lain.',
            'jam_buka.required'        => 'Jam buka wajib diisi.',
            'jam_tutup.required'       => 'Jam tutup wajib diisi.',
            'rating.required'          => 'Rating wajib diisi.',
            'rating.numeric'           => 'Rating harus berupa angka.',
        ];

        $request->validate([
            'nama_resto'               => ['required', 'string', 'max:255'],
            'alamat'                   => ['required', 'string'],
            'telp'                     => ['required', 'min:10',  'numeric', 'unique:restaurants'],
            'jam_buka'                 => ['required', 'string'],
            'jam_tutup'                => ['required', 'string'],
            'rating'                   => ['required', 'numeric'],
        ], $pesan);

        $resto->nama_resto             = $request->nama_resto;
        $resto->alamat                 = $request->alamat;
        $resto->telp                   = $request->telp;
        $resto->jam_buka               = $request->jam_buka;
        $resto->jam_tutup              = $request->jam_tutup;
        $resto->rating                 = $request->rating;
        $resto->save();

        foreach ($request->list_menu as $key => $value) {
            $menu = array(
                'resto_id'             => $resto->id,
                'nama_menu'            => $value['nama_menu'],
                'harga'                => $value['harga'],
                'kategori'             => $value['kategori']
            );
            Menu::create($menu);
        }

        return response()->json([
            'status'                   => 'Success',
            'msg'                      => 'Berhasil menambahkan data',
        ], Response::HTTP_OK);
    }


    public function getRestoMenu($id)
    {
        $resto = Restaurant::with('menu')->where('id', $id)->first();

        if (!$resto) {
            return response()->json([
                'status'               => 'Failed',
                'msg'                  => 'Data tidak ditemukan',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'status'                   => 'Success',
            'msg'                      => 'Berhasil mendapatkan data',
            'list menu'                => $resto
        ], Response::HTTP_OK);
    }


    public function putRestoMenu(Request $request, $id)
    {
        $resto                         = Restaurant::find($id);

        if (!$resto) {
            return response()->json([
                'status'               => 'Failed',
                'msg'                  => 'Data tidak ditemukan',
            ], Response::HTTP_NOT_FOUND);
        }

        $pesan = [
            'nama_resto.required'      => 'Nama restoran wajib diisi.',
            'alamat.required'          => 'Password wajib diisi.',
            'telp.required'            => 'Telepon wajib diisi.',
            'telp.numeric'             => 'Telepon harus berupa angka.',
            'telp.min'                 => 'Telepon tidak valid.',
            'jam_buka.required'        => 'Jam buka wajib diisi.',
            'jam_tutup.required'       => 'Jam tutup wajib diisi.',
            'rating.required'          => 'Rating wajib diisi.',
            'rating.numeric'           => 'Rating harus berupa angka.',
        ];

        $request->validate([
            'nama_resto'               => ['required', 'string', 'max:255'],
            'alamat'                   => ['required', 'string'],
            'telp'                     => ['required', 'min:10', 'numeric'],
            'jam_buka'                 => ['required', 'string'],
            'jam_tutup'                => ['required', 'string'],
            'rating'                   => ['required', 'numeric'],
        ], $pesan);

        $resto->update([
            'nama_resto'               => $request->nama_resto,
            'alamat'                   => $request->alamat,
            'telp'                     => $request->telp,
            'jam_buka'                 => $request->jam_buka,
            'jam_tutup'                => $request->jam_tutup,
            'rating'                   => $request->rating,
        ]);

        Menu::where('resto_id', $id)->delete();

        foreach ($request->list_menu as $key => $value) {
            $menu = array(
                'resto_id'             => $id,
                'nama_menu'            => $value['nama_menu'],
                'harga'                => $value['harga'],
                'kategori'             => $value['kategori']
            );
            Menu::create($menu);
        }

        return response()->json([
            'status'                   => 'Success',
            'msg'                      => 'Berhasil mengubah data',
        ], Response::HTTP_OK);
    }


    public function createMenu(Request $request, $resto_id)
    {
        $resto                         = Restaurant::find($resto_id);

        if (!$resto) {
            return response()->json([
                'status'               => 'Failed',
                'msg'                  => 'Data Restoran tidak ada pada database kami',
            ], Response::HTTP_NOT_FOUND);
        }

        $pesan = [
            'nama_menu.required'       => 'Nama menu wajib diisi.',
            'harga.required'           => 'Harga wajib diisi.',
            'harga.numeric'            => 'Harga harus berupa angka.',
            'kategori.required'        => 'Kategori wajib diisi.',
        ];

        $request->validate([
            'nama_menu'                => ['required', 'string', 'max:255'],
            'harga'                    => ['required', 'numeric'],
            'kategori'                 => ['required'],
        ], $pesan);

        $menu = new Menu();
        $menu->resto_id                = $resto_id;
        $menu->nama_menu               = $request->nama_menu;
        $menu->harga                   = $request->harga;
        $menu->kategori                = $request->kategori;
        $menu->save();

        return response()->json([
            'status'                   => 'Success',
            'msg'                      => "Menu $request->nama_menu ditambahkan pada $resto->nama_resto",
        ], Response::HTTP_OK);
    }


    public function putMenu(Request $request, $resto_id, $menuId)
    {
        $resto                         = Restaurant::find($resto_id);

        if (!$resto) {
            return response()->json(
                [
                    'status'           => 'Failed',
                    'msg'              => 'Data Restoran tidak ada pada database kami',
                ],
                Response::HTTP_NOT_FOUND
            );
        }

        $pesan = [
            'nama_menu.required'       => 'Nama menu wajib diisi.',
            'harga.required'           => 'Harga wajib diisi.',
            'harga.numeric'            => 'Harga harus berupa angka.',
            'kategori.required'        => 'Kategori wajib diisi.',
        ];

        $request->validate([
            'nama_menu'                => ['required', 'string', 'max:255'],
            'harga'                    => ['required', 'numeric'],
            'kategori'                 => ['required'],
        ], $pesan);

        $menu = Menu::find($menuId);

        if (!$menu) {
            return response()->json(
                [
                    'status'           => 'Failed',
                    'msg'              => 'Menu tidak ada atau telah dihapus',
                ],
                Response::HTTP_NOT_FOUND
            );
        }

        $menu->update([
            'nama_menu'                => $request->nama_menu,
            'harga'                    => $request->harga,
            'kategori'                 => $request->kategori,
        ]);

        return response()->json([
            'status'                   => 'Success',
            'msg'                      => "Berhasil mengubah menu makanan pada $resto->nama_resto",
        ], Response::HTTP_OK);
    }


    public function deleteMenu($resto_id, $menuId)
    {
        $resto                         = Restaurant::find($resto_id);

        if (!$resto) {
            return response()->json(
                [
                    'status'           => 'Failed',
                    'msg'              => 'Data Restoran tidak ada pada database kami',
                ],
                Response::HTTP_NOT_FOUND
            );
        }

        $menu = Menu::find($menuId);

        if (!$menu) {
            return response()->json(
                [
                    'status'           => 'Failed',
                    'msg'              => 'Menu tidak ada atau telah dihapus',
                ],
                Response::HTTP_NOT_FOUND
            );
        }

        $menu->delete();

        return response()->json([
            'status'                   => 'Success',
            'msg'                      => "Menu $menu->nama_menu telah dihapus pada $resto->nama_resto",
        ], Response::HTTP_OK);
    }


    public function putResto(Request $request, $resto_id)
    {
        $resto                         = Restaurant::find($resto_id);

        if (!$resto) {
            return response()->json([
                'status'               => 'Failed',
                'msg'                  => 'Data Restoran tidak ada pada database kami',
            ], Response::HTTP_NOT_FOUND);
        }

        $pesan = [
            'nama_resto.required'      => 'Nama restoran wajib diisi.',
            'alamat.required'          => 'Password wajib diisi.',
            'telp.required'            => 'Telepon wajib diisi.',
            'telp.numeric'             => 'Telepon harus berupa angka.',
            'telp.min'                 => 'Telepon tidak valid.',
            'jam_buka.required'        => 'Jam buka wajib diisi.',
            'jam_tutup.required'       => 'Jam tutup wajib diisi.',
            'rating.required'          => 'Rating wajib diisi.',
            'rating.numeric'           => 'Rating harus berupa angka.',
        ];

        $request->validate([
            'nama_resto'               => ['required', 'string', 'max:255'],
            'alamat'                   => ['required', 'string'],
            'telp'                     => ['required', 'min:10', 'numeric'],
            'jam_buka'                 => ['required', 'string'],
            'jam_tutup'                => ['required', 'string'],
            'rating'                   => ['required', 'numeric'],
        ], $pesan);

        $resto->update([
            'nama_resto'               => $request->nama_resto,
            'alamat'                   => $request->alamat,
            'telp'                     => $request->telp,
            'jam_buka'                 => $request->jam_buka,
            'jam_tutup'                => $request->jam_tutup,
            'rating'                   => $request->rating,
        ]);

        return response()->json([
            'status'                   => 'Success',
            'msg'                      => "Nama resto telah berubah menjadi $request->nama_resto",
        ], Response::HTTP_OK);
    }


    public function getMenu()
    {
        $menu = Menu::all();

        return response()->json([
            'status'                   => 'Success',
            'msg'                      => 'Berhasil mendapatkan data',
            'menu'                     => $menu
        ], Response::HTTP_OK);
    }


    // public function restoMenu()
    // {

    // }
}
