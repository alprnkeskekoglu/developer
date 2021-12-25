@extends('Dawnstar::layouts.app')

@section('content')
    @include('Dawnstar::includes.page_header',['headerTitle' => __('Developer::general.box.backup')])
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-block mb-3">
                        <form action="{{ route('dawnstar.developer.database.export') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger float-right backupBtn">@lang('Developer::general.database.backup')</button>
                        </form>
                    </div>
                    <table class="table table-striped table-centered mb-0">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>@lang('Developer::general.database.name')</th>
                            <th>@lang('Developer::general.database.size')</th>
                            <th>@lang('Developer::general.database.admin')</th>
                            <th>@lang('Developer::general.database.date')</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($databases as $database)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $database['name'] }}</td>
                                <td>{{ $database['size'] }}</td>
                                <td>{{ $database['user'] }}</td>
                                <td>{{ $database['date'] }}</td>
                                <td class="table-action d-flex">
                                    <form action="{{ route('dawnstar.developer.database.import') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="file" value="{{ $database['file'] }}">
                                        <button type="button" class="btn action-icon restoreBtn"><i class="mdi mdi-database-import"></i></button>
                                    </form>
                                    <form action="{{ route('dawnstar.developer.database.download') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="file" value="{{ $database['file'] }}">
                                        <button type="submit" class="btn action-icon"><i class="mdi mdi-database-arrow-down"></i></button>
                                    </form>
                                    <form action="{{ route('dawnstar.developer.database.delete') }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="file" value="{{ $database['file'] }}">
                                        <button type="submit" class="btn action-icon"><i class="mdi mdi-database-remove"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $('.restoreBtn').on('click', function () {
            if(confirm('{{ __('Developer::general.database.r_u_sure') }}')) {
                $('#loadingBox').removeClass('d-none');
                $(this).closest('form').submit();
            }
        });
    </script>
@endpush
