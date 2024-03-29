@extends('layouts.app')

@section('title', 'Surat Keluar')

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
                                <h2 class="mb-0">Surat Keluar</h2>
                                <p class="mb-0 text-sm">Kelola Surat Keluar</p>
                            </div>
                            <div class="mb-3">
                                <button type="button" data-toggle="tooltip" title="Hapus data terpilih" class="btn btn-danger" id="delete" name="delete" >
                                    <i class="fas fa-trash"></i>
                                </button>
                                @if (count($surat_keluar) > 0)
                                    <button type="button" data-toggle="tooltip" title="Print" class="btn btn-primary" id="btn-print" name="btn-print" >
                                        <i class="fas fa-print"></i>
                                    </button>
                                @endif
                                <a href="{{ route('surat-keluar.create') }}?page={{ request('page') }}" class="btn btn-success" title="Tambah" data-toggle="tooltip"><i class="fas fa-plus"></i></a>
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
<form class="navbar-search navbar-search-dark form-inline mr-3 d-none d-md-flex ml-lg-auto" action="{{ URL::current() }}" method="GET">
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
<form class="mt-4 mb-3 d-md-none" action="{{ URL::current() }}" method="GET">
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
<div class="card shadow">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-sm table-striped table-bordered">
                <thead>
                    <th class="text-center">
                        <input type="checkbox" name="check_all" id="check_all">
                    </th>
                    <th class="text-center" width="20px">No. Urut</th>
                    <th class="text-center" width="100px">Opsi</th>
                    <th class="text-center">Tanggal Surat</th>
                    <th class="text-center">Ditujukan Kepada</th>
                    <th class="text-center">Isi Singkat</th>
                </thead>
                <tbody>
                    @forelse ($surat_keluar as $item)
                        <tr>
                            <td style="vertical-align: middle">
                                <input type="checkbox" class="surat-keluar-checkbox" id="delete{{ $item->id }}" name="delete[]" value="{{ $item->id }}">
                            </td>
                            <td style="vertical-align: middle" class="text-center">{{ $item->nomor_urut }}</td>
                            <td>
                                <a href="{{ route('surat-keluar.edit', $item) }}?page={{ request('page') }}" class="btn btn-sm btn-success" data-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></a>
                                @if ($item->berkas)
                                    <a target="_blank" href="{{ url(Storage::url($item->berkas)) }}" class="btn btn-sm btn-primary" data-toggle="tooltip" title="Download"><i class="fas fa-download"></i></a>
                                @endif
                                <a class="btn btn-sm btn-danger hapus-data" data-nama="{{ $item->nama }}" data-action="{{ route("surat-keluar.destroy", $item) }}" data-toggle="tooltip" title="Hapus" href="#modal-hapus"><i class="fas fa-trash"></i></a>
                            </td>
                            <td style="vertical-align: middle">{{ tgl(date('Y-m-d', strtotime($item->tanggal_surat))) }}</td>
                            <td style="vertical-align: middle">{{ $item->tujuan }}</td>
                            <td style="vertical-align: middle">{{ $item->isi_singkat_atau_perihal }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="15" align="center">Data tidak tersedia</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $surat_keluar->links('layouts.components.pagination') }}
        </div>
    </div>
</div>

@include('layouts.components.hapus', ['nama_hapus' => 'surat keluar'])

<div class="modal fade" id="print" tabindex="-1" role="dialog" aria-labelledby="print" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="modal-title-delete">Print</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <div class="modal-body pt-0">
                <form action="{{ route("surat-keluar.print") }}" method="POST" >
                    @csrf
                    <div class="form-group">
                        <label class="form-control-label" for="tahun">Tahun</label> <img style="display: none" id="loading" height="20px" src="{{ asset('storage/loading.gif') }}" alt="Loading">
                        <select class="form-control @error('tahun') is-invalid @enderror" name="tahun" id="tahun">
                            <option selected value="">Semua Tahun</option>
                            @for ($i = date('Y'); $i >= 1900; $i--)
                                <option value="{{ $i }}" {{ old('tahun') == $i ? 'selected="true"' : ''  }}>{{ $i }}</option>
                            @endfor
                        </select>
                        @error('tahun')<span class="invalid-feedback font-weight-bold">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-control-label" for="diketahui">Diketahui</label> <img style="display: none" id="loading" height="20px" src="{{ asset('storage/loading.gif') }}" alt="Loading">
                        <select required class="form-control @error('diketahui') is-invalid @enderror" name="diketahui" id="diketahui">
                            <option selected value="">Pilih Aparat Pemerintahan Desa</option>
                            @foreach ($pemerintahan_desa as $item)
                                <option value="{{ $item->id }}" {{ old('diketahui') == $item->id ? 'selected="true"' : ''  }}>{{ $item->nama }} ({{ $item->jabatan }})</option>
                            @endforeach
                        </select>
                        @error('diketahui')<span class="invalid-feedback font-weight-bold">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-control-label" for="ditandatangani">Ditandatangani</label> <img style="display: none" id="loading" height="20px" src="{{ asset('storage/loading.gif') }}" alt="Loading">
                        <select required class="form-control @error('ditandatangani') is-invalid @enderror" name="ditandatangani" id="ditandatangani">
                            <option selected value="">Pilih Aparat Pemerintahan Desa</option>
                            @foreach ($pemerintahan_desa as $item)
                                <option value="{{ $item->id }}" {{ old('ditandatangani') == $item->id ? 'selected="true"' : ''  }}>{{ $item->nama }} ({{ $item->jabatan }})</option>
                            @endforeach
                        </select>
                        @error('ditandatangani')<span class="invalid-feedback font-weight-bold">{{ $message }}</span>@enderror
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">Print</button>
                        <button type="button" class="btn btn-white" data-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $("#btn-print").click(function (e) {
            e.preventDefault();
            $("#print").modal('show');
        });

        $(document).on('click', '#delete', function(){
            let id = [];
            if (confirm("Apakah anda yakin ingin menghapus data ini?")) {
                $(".surat-keluar-checkbox:checked").each(function () {
                    id.push($(this).val());
                });
                if (id.length > 0) {
                    $.ajax({
                        url     : "{{ route('surat-keluar.destroys') }}",
                        method  : 'delete',
                        data    : {
                            _token  : "{{ csrf_token() }}",
                            id      : id,
                        },
                        success : function(data){
                            alertSuccess(data.message);
                            location.reload();
                        }
                    });
                } else {
                    alertFail('Harap pilih salah satu surat keluar');
                }
            }
        });

        $("#check_all").click(function(){
            if (this.checked) {
                $(".surat-keluar-checkbox").prop('checked',true);
            } else {
                $(".surat-keluar-checkbox").prop('checked',false);
            }
        });
    });
</script>
@endpush
