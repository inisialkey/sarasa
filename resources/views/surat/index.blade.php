@extends('layouts.app')

@section('title', 'Surat')

@section('styles')
<link href="{{ asset('/css/style.css') }}" rel="stylesheet">
@endsection

@section('content-header')
<div class="header pb-8 pt-5 pt-lg-8 d-flex align-items-center" style="background-image: url({{ asset('/img/cover-bg.jpg') }}); background-size: cover; background-position: center top;">
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="card shadow h-100">
                    <div class="card-header border-0">
                        <div class="d-flex flex-column flex-md-row align-items-center justify-content-center justify-content-md-between text-center text-md-left">
                            <div class="mb-3">
                                <h2 class="mb-0">Surat</h2>
                                <p class="mb-0 text-sm">Kelola Surat</p>
                            </div>
                            <div class="mb-3">
                                <button type="button" data-toggle="tooltip" title="Pengaturan" class="btn btn-primary" id="btn-pengaturan" name="btn-pengaturan">
                                    <i class="fas fa-cog"></i>
                                </button>
                                <a href="{{ route('surat.create') }}" class="btn btn-success" title="Tambah"><i class="fas fa-plus"></i> Tambah Surat</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('form-search')
<form class="navbar-search navbar-search-dark form-inline mr-3 d-none d-md-flex ml-lg-auto">
    <div class="form-group mb-0">
        <div class="input-group input-group-alternative">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
            </div>
            <input class="form-control" placeholder="Cari ...." type="search" name="cari" value="{{ request('cari') }}">
        </div>
    </div>
</form>
@endsection

@section('form-search-mobile')
<form class="mt-4 mb-3 d-md-none">
    <div class="input-group input-group-rounded input-group-merge">
        <input type="search" name="cari" class="form-control form-control-rounded form-control-prepended" placeholder="cari" aria-label="cari" value="{{ request('cari') }}">
        <div class="input-group-prepend">
            <div class="input-group-text">
                <span class="fa fa-search"></span>
            </div>
        </div>
    </div>
</form>
@endsection

@section('content')
@include('layouts.components.alert')
<div id="card" class="row mt-4 justify-content-center">
    @forelse ($surat as $item)
        <div class="col-lg-4 col-md-6 surats">
            <div class="single-service bg-white rounded shadow">
                <a href="{{ route('surat.show', $item) }}">
                    <i class="fas fa-file-alt fa-5x mb-3"></i>
                    <h4>{{ $item->nama }}</h4>
                </a>
                <p>{{ $item->deskripsi }}</p>
                @if ($item->cetakSurat->count() > 0)
                    <p class="text-sm text-muted">Telah dicetak sebanyak {{ $item->cetakSurat->count() }}x</p>
                @endif
                @if ($item->tampilkan == 0)
                    <p class="font-weight-bold">(Belum ditampilkan)</p>
                @endif
                <a href="{{ route('buat-surat', ['id' => $item->id,'slug' => Str::slug($item->nama)]) }}" class="btn btn-sm btn-success" title="Cetak"><i class="fas fa-print"></i> Cetak</a>
                <a href="{{ route('surat.edit', $item) }}" class="btn btn-sm btn-primary" title="Edit"><i class="fas fa-edit"></i> Edit</a>
                <a class="btn btn-sm btn-danger hapus-data" data-nama="{{ $item->nama }}" data-action="{{ route('surat.destroy', $item) }}" data-toggle="modal" href="#modal-hapus" title="Hapus"><i class="fas fa-trash"></i> Hapus</a>
            </div>
        </div>
    @empty
        <div class="col">
            <div class="single-service bg-white rounded shadow">
                <h4>Data belum tersedia</h4>
            </div>
        </div>
    @endforelse
</div>

@include('layouts.components.hapus', ['nama_hapus' => 'surat'])

<div class="modal fade" id="pengaturan" tabindex="-1" role="dialog" aria-labelledby="pengaturan" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="modal-title-pengaturan">Pengaturan</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <div class="modal-body">
                <form action="{{ route("surat.pengaturan") }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="ditandatangani">Ditandatangani</label>
                        <select class="form-control" type="text" id="ditandatangani" name="ditandatangani">
                            <option value="">Pilih Aparat Pemerintahan Desa</option>
                            @foreach ($pemerintahan_desa as $item)
                                <option value="{{ $item->id }}" {{ $desa->pemerintahan_desa_id == $item->id ? 'selected' : '' }}>{{ $item->nama }} ({{ $item->jabatan }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mt-5 d-flex justify-content-between">
                        <button type="button" class="btn btn-white" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function(){
        $('#btn-pengaturan').click(function (e) {
            e.preventDefault();
            $("#pengaturan").modal('show');
        });

        $('[name="cari"]').on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#card .surats").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>
@endpush
