@extends('layouts.app')

@section('title', 'Landfill PDF Import')

@section('content')

<div class="container-fluid px-4 py-4">

    <div class="card">

        <div class="card-header">
            <strong>Waste to Landfill PDF Import</strong>
        </div>

        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form
                action="{{ route('landfill.upload') }}"
                method="POST"
                enctype="multipart/form-data">

                @csrf

                <div class="mb-3">

    <label class="form-label">
        Upload Landfill File
    </label>

    <input
        type="file"
        name="landfill_file"
        class="form-control"
        accept=".pdf,.xlsx,.xls"
        required>

    <div class="form-text">
        Supported formats: PDF, XLSX, XLS.
        For scanned PDFs, convert the PDF to Excel first.
    </div>

</div>

                <button
                    type="submit"
                    class="btn btn-success">

                    Upload PDF

                </button>

            </form>

        </div>

    </div>

</div>

@endsection