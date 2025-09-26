{{-- resources/views/admin/crmKanban/index.blade.php --}}
@extends('layouts.admin')
@section('content')
<div class="content">
  <div class="panel panel-default">
    <div class="panel-heading">{{ $category->name }} — Kanban</div>
    <div class="panel-body">
      <div class="row" id="kanban" data-category="{{ $category->id }}">
        @foreach($category->stages as $stage)
          <div class="col-md-3">
            <div class="box box-solid">
              <div class="box-header with-border"><strong>{{ $stage->name }}</strong></div>
              <div class="box-body">
                <div class="kanban-col" data-stage="{{ $stage->id }}">
                  @foreach($stage->cards as $card)
                    <div class="kanban-card" data-card="{{ $card->id }}" data-pos="{{ $card->position }}">
                      <div class="title">{{ $card->title }}</div>
                      <small>#{{ $card->id }} • {{ $card->priority }} • {{ $card->value_amount }} {{ $card->value_currency }}</small>
                    </div>
                  @endforeach
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
@parent
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.querySelectorAll('.kanban-col').forEach(function(col){
  new Sortable(col, {
    group: 'crm-kanban',
    animation: 150,
    onEnd: function (evt) {
      const el = evt.item;
      const cardId = el.dataset.card;
      const stageId = el.parentElement.dataset.stage;
      const prev = el.previousElementSibling ? el.previousElementSibling.dataset.card : null;
      const next = el.nextElementSibling ? el.nextElementSibling.dataset.card : null;

      const moveUrl = '{{ route('admin.crm-cards.move', ['crm_card' => '___ID___']) }}'.replace('___ID___', cardId);

      fetch(moveUrl, {
        method: 'PATCH',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ stage_id: stageId, prev_id: prev, next_id: next })
      });
    }
  });
});
</script>
@endsection
