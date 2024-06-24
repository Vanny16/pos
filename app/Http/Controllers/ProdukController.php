<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use App\Models\Produk;
use PDF;

class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $kategori = Kategori::all()->pluck('nama_kategori', 'id_kategori');

        return view('produk.index', compact('kategori'));
    }

    public function save(Request $request)
    {
    // Validate the request data
    $validatedData = $request->validate([
        'nama_produk' => 'required|string|max:255',
        'id_kategori' => 'required|integer',
        'merk' => 'required|string|max:255',
        'harga_jual' => 'required|numeric',
        'diskon' => 'required|numeric',
        'stok' => 'required|integer',
    ]);

    // Set 'harga_beli' to 0 if not present in the request
    $validatedData['harga_beli'] = $request->filled('harga_beli') ? $request->input('harga_beli') : 0;

    // Retrieve the latest product to generate the next product code
    $latestProduct = Produk::latest()->first();
    $nextId = $latestProduct ? $latestProduct->id_produk + 1 : 1;
    $kode_produk = 'P' . str_pad($nextId, 6, '0', STR_PAD_LEFT);

    // Add the generated product code to the validated data
    $validatedData['kode_produk'] = $kode_produk;

    // Create and save the new product record
    $produk = Produk::create($validatedData);
        return redirect()->back()->with('message', 'Role saved successfully');
    }

    public function data()
    {
        $produk = Produk::leftJoin('kategori', 'kategori.id_kategori', 'produk.id_kategori')
            ->select('produk.*', 'nama_kategori')
            // ->orderBy('kode_produk', 'asc')
            ->get();

            $grandTotalSellingPrice = $produk->sum(function($product) {
        return $product->stok * $product->harga_jual;
        });

        $grandTotalPurchasePrice = $produk->sum(function($product) {
        return $product->stok * $product->harga_beli;
        });

        return datatables()
            ->of($produk)
            ->addIndexColumn()
            ->addColumn('select_all', function ($produk) {
                return '
                    <input type="checkbox" name="id_produk[]" value="'. $produk->id_produk .'">
                ';
            })
            ->addColumn('kode_produk', function ($produk) {
                return '<span class="label label-success">'. $produk->kode_produk .'</span>';
            })
            ->addColumn('harga_beli', function ($produk) {
                return format_uang($produk->harga_beli);
            })
            ->addColumn('purchase_total_value', function ($produk) {
                return format_uang($produk->stok * $produk->harga_beli);
            })
            ->addColumn('harga_jual', function ($produk) {
                return format_uang($produk->harga_jual);
            })
            ->addColumn('selling_total_value', function ($produk) {
                return format_uang($produk->stok * $produk->harga_jual);
            })
            ->addColumn('stok', function ($produk) {
                return format_uang($produk->stok);
            })
            ->addColumn('aksi', function ($produk) {
                return '
                <div class="btn-group">
                    <button type="button" onclick="editForm(`'. route('produk.update', $produk->id_produk) .'`)" class="btn btn-xs btn-primary btn-flat"><i class="fa fa-pencil"></i></button>
                    <button type="button" onclick="deleteData(`'. route('produk.destroy', $produk->id_produk) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>
                ';
            })
            ->with('grandTotalSellingPrice', format_uang($grandTotalSellingPrice))
            ->with('grandTotalPurchasePrice', format_uang($grandTotalPurchasePrice))
            ->rawColumns(['aksi', 'kode_produk', 'select_all'])
            ->make(true);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $produk = Produk::latest()->first() ?? new Produk();
        $request['kode_produk'] = 'P'. tambah_nol_didepan((int)$produk->id_produk +1, 6);

        $produk = Produk::create($request->all());

        return response()->json('Data saved successfully', 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $produk = Produk::find($id);

        return response()->json($produk);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $produk = Produk::find($id);
        $produk->update($request->all());

        return response()->json('Data saved successfully', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $produk = Produk::find($id);
        $produk->delete();

        return response(null, 204);
    }

    public function deleteSelected(Request $request)
    {
        foreach ($request->id_produk as $id) {
            $produk = Produk::find($id);
            $produk->delete();
        }

        return response(null, 204);
    }
    // visit "codeastro" for more projects!
    public function cetakBarcode(Request $request)
    {
        $dataproduk = array();
        foreach ($request->id_produk as $id) {
            $produk = Produk::find($id);
            $dataproduk[] = $produk;
        }

        $no  = 1;
        $pdf = PDF::loadView('produk.barcode', compact('dataproduk', 'no'));
        $pdf->setPaper('a4', 'potrait');
        return $pdf->stream('product.pdf');
    }
}
