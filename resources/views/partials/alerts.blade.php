@php
    $normalizeMessageBag = static function ($messages) {
        return $messages instanceof \Illuminate\Contracts\Support\MessageProvider
            ? $messages->getMessageBag()->getMessages()
            : (array) $messages;
    };
@endphp

@if($error = session()->get('error'))
    @php($error = $normalizeMessageBag($error))
    <div class="alert alert-danger alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4><i class="icon fa fa-ban"></i> &nbsp;{{ \Illuminate\Support\Arr::get($error, 'title.0') }}</h4>
        <p>{!!  \Illuminate\Support\Arr::get($error, 'message.0') !!}</p>
    </div>
@elseif ($errors = session()->get('errors'))
    @if ($errors->hasBag('error'))
      <div class="alert alert-danger alert-dismissable">

        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        @foreach($errors->getBag("error")->toArray() as $message)
            <p>{!!  \Illuminate\Support\Arr::get($message, 0) !!}</p>
        @endforeach
      </div>
    @endif
@endif

@if($success = session()->get('success'))
    @php($success = $normalizeMessageBag($success))
    <div class="alert alert-success alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4><i class="icon fa fa-check"></i> &nbsp;{{ \Illuminate\Support\Arr::get($success, 'title.0') }}</h4>
        <p>{!!  \Illuminate\Support\Arr::get($success, 'message.0') !!}</p>
    </div>
@endif

@if($info = session()->get('info'))
    @php($info = $normalizeMessageBag($info))
    <div class="alert alert-info alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4><i class="icon fa fa-info"></i> &nbsp;{{ \Illuminate\Support\Arr::get($info, 'title.0') }}</h4>
        <p>{!!  \Illuminate\Support\Arr::get($info, 'message.0') !!}</p>
    </div>
@endif

@if($warning = session()->get('warning'))
    @php($warning = $normalizeMessageBag($warning))
    <div class="alert alert-warning alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4><i class="icon fa fa-warning"></i> &nbsp;{{ \Illuminate\Support\Arr::get($warning, 'title.0') }}</h4>
        <p>{!!  \Illuminate\Support\Arr::get($warning, 'message.0') !!}</p>
    </div>
@endif
