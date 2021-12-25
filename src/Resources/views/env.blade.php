@extends('Dawnstar::layouts.app')

@section('content')
    @include('Dawnstar::includes.page_header',['headerTitle' => __('Developer::general.box.env_edit')])
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('dawnstar.developer.env.update') }}" method="post">
                        @csrf
                        @method('PUT')
                        <textarea class="form-control" name="env" rows="20" style="resize: none;">{!! $env !!}</textarea>
                        <div class="mt-2 text-end">
                            <button type="submit" class="btn btn-primary">@lang('Developer::general.save')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
