@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="card">

        <div class="card-header">
            <strong>CENSEi Legacy Excel Import</strong>
        </div>

        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('legacy.upload') }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf

                <div class="mb-3">

                    <label class="form-label">
                        Upload Legacy Excel File
                    </label>

                    <input type="file"
                           name="excel_file"
                           class="form-control"
                           accept=".xlsx,.xls"
                           required>

                </div>

                <button type="submit"
                        class="btn btn-success">

                    Upload Workbook

                </button>

            </form>

        </div>

    </div>

</div>

@endsection