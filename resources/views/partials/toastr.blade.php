@if(Session::has('dcat-admin-toastr'))
    @php
        $toastr  = Session::get('dcat-admin-toastr');
        $toastr  = $toastr instanceof \Illuminate\Contracts\Support\MessageProvider
            ? $toastr->getMessageBag()->getMessages()
            : (array) $toastr;
        $type    = \Illuminate\Support\Arr::get($toastr, 'type.0', 'success');
        $message = \Illuminate\Support\Arr::get($toastr, 'message.0', '');
        $options = admin_javascript_json(\Illuminate\Support\Arr::get($toastr, 'options', []));
    @endphp
    <script>$(function () { toastr.{{$type}}('{!!  $message  !!}', null, {!! $options !!}); })</script>
@endif
