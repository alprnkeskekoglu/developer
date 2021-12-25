@extends('Dawnstar::layouts.app')

@section('content')
    @include('Dawnstar::includes.page_header',['headerTitle' => __('Developer::general.title')])
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 text-center">
                            <button type="button" class="btn btn-lg btn-outline-primary" onclick="command(this, 'cache')">
                                @lang('Developer::general.box.cache_clear')
                            </button>
                            <button type="button" class="btn btn-lg btn-outline-primary" onclick="command(this, 'config')">
                                @lang('Developer::general.box.config_clear')
                            </button>
                            <button type="button" class="btn btn-lg btn-outline-primary" onclick="command(this, 'view')">
                                @lang('Developer::general.box.view_clear')
                            </button>
                        </div>
                        <div class="col-12 text-center mt-4">
                            <a href="{{ route('dawnstar.developer.env') }}" class="btn btn-lg btn-outline-danger">
                                @lang('Developer::general.box.env_edit')
                            </a>
                            <a href="{{ route('dawnstar.developer.database.index') }}" class="btn btn-lg btn-outline-danger">
                                @lang('Developer::general.box.backup')
                            </a>
                            <a href="{{ route('dawnstar.developer.vcs.index') }}" class="btn btn-lg btn-outline-danger">
                                @lang('Developer::general.box.vcs')
                            </a>
                            <form action="{{ route('dawnstar.developer.maintenance') }}" method="POST" class="d-inline-block">
                                @csrf
                                @if(env('DAWNSTAR_MAINTENANCE', false) == false)
                                    <button type="submit" class="btn btn-lg btn-outline-danger">
                                        @lang('Developer::general.box.maintenance_mode')
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-lg btn-outline-success">
                                        @lang('Developer::general.box.live_mode')
                                    </button>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function command(el, type) {
            $.ajax({
                url: '{{ route('dawnstar.developer.command') }}?type=' + type,
                success: function (response) {
                    var oldMessage = el.innerHTML;
                    el.innerHTML = response.message;
                    $(el).removeClass('btn-outline-primary').addClass('btn-success');
                    setTimeout(function () {
                        el.innerHTML = oldMessage
                        $(el).removeClass('btn-success').addClass('btn-outline-primary');
                    }, 1000);
                }
            })
        }
    </script>
@endpush

