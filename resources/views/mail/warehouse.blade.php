@foreach ($orderItem as $i)
    @if ($i->damaged > 0)
        <br/>{{$i->itemtype}} {{$i->typename}} арт {{$i->article}} - испорчено {{$i->damaged}} шт
    @endif
    @if ($i->quantity < $i->issuedQuantity)
        <br/>{{$i->itemtype}} {{$i->typename}} арт {{$i->article}} - утеряно {{$i->quantity - $i->issuedQuantity}} шт
    @endif
@endforeach