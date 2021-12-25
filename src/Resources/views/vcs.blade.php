@extends('Dawnstar::layouts.app')

@section('content')
    @include('Dawnstar::includes.page_header',['headerTitle' => __('Developer::general.box.backup')])
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <td>Repository</td>
                            <td>
                                <a href="{!! $remoteUrl !!}" class="float-end" target="_blank">{!! $remoteUrl !!}</a>
                            </td>
                        </tr>
                        <tr>
                            <td>Deploy Branch</td>
                            <td>
                                <form action="{{ route('dawnstar.developer.vcs.checkout') }}" id="checkoutForm" method="post" class="d-flex justify-content-between">
                                    @csrf
                                    <select name="branch" class="form-select w-50">
                                        @foreach($branches as $branch)
                                            <option value="{!! $branch !!}" {!! $branch == $currentBranch ? 'selected' :'' !!}>{!! $branch !!}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-primary float-end" form="checkoutForm">
                                        Checkout
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <tr>
                            <td>Last Commit Id</td>
                            <td>
                                <div class="d-flex justify-content-between">
                                    <a href="{{ $remoteUrl . '/commits/' . $lastCommit->getId() }}" target="_blank">{!! substr($lastCommit->getId(), 0, 8) !!}</a>
                                    <span>{!! \Carbon\Carbon::parse($lastCommit->getCommitterDate())->diffForHumans() !!}</span>
                                </div>
                            </td>
                        </tr>
                    </table>

                    <table class="table">
                        <tr>
                            <th>Author</th>
                            <th>Commit</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th></th>
                        </tr>
                        @foreach($commits as $commit)
                            <tr>
                                <td><span></span> {{ $commit['author']['name'] }}</td>
                                <td><a href="{{ $remoteUrl . '/commits/' . $commit['id'] }}" target="_blank">{{ substr($commit['id'],0,8) }}</a></td>
                                <td>{!! \Str::limit($commit['message'], 50) !!}</td>
                                <td>
                                    <span>{!! $commit['date']->diffForHumans() !!}</span>
                                </td>
                                <td>
                                    <form action="{{ route('dawnstar.developer.vcs.merge') }}" id="checkoutForm" method="post" class="d-flex justify-content-between">
                                        @csrf
                                        <input type="hidden" name="commit" value="{{ $commit['id'] }}">
                                        <button class="btn btn-sm btn-primary">Pull and Merge</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
