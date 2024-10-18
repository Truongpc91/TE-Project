@if ($m2signature == $partnerSignature)
    @if ($errorCode == '0')
        <div class="alert alert-success"><strong>Tình trạng thanh toán: </strong>Success</div>
    @else
        <div class="alert alert-danger"><strong>Tình trạng thanh toán: </strong>{{ $message }}/{{ $localMessage }}
        </div>
    @endif
@else
    <div class="alert alert-danger">Giao dịch này có thể bị hack, vui lòng kiểm tra chữ ký của bạn và chữ ký đã trả lại
    </div>
@endif
