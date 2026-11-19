@extends('appSection@taskManagement::layout')
@section('title', 'Tapşırıqlarım')
@section('content')
<section id="task-list-page">
    <div class="page-heading">
        <div><h1>Tapşırıqlarım</h1></div>
        <a class="button primary" href="{{ route('tasks.create') }}"><span aria-hidden="true">＋</span> Yeni tapşırıq</a>
    </div>
    <div class="panel">
        <form id="filters" class="toolbar">
            <div><label for="is_completed">Vəziyyət</label><select id="is_completed" name="is_completed"><option value="">Bütün tapşırıqlar</option value="0">Tamamlanmamış</option><option value="1">Tamamlanmış</option></select></div>
            <button class="button secondary" type="submit">Tətbiq et</button>
        </form>
        <p id="feedback" class="notice" role="status" aria-live="polite" hidden></p>
        <div id="tasks" class="task-list" aria-busy="true"><p class="empty">Tapşırıqlar yüklənir…</p></div>
    </div>
</section>
@endsection
