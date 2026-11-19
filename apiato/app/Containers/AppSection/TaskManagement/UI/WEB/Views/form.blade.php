@extends('appSection@taskManagement::layout')
@section('title', $taskId ? 'Tapşırığı redaktə et' : 'Yeni tapşırıq')
@section('content')
<section id="task-form-page" data-task-id="{{ $taskId }}">
    <a class="back-link" href="{{ route('tasks.index') }}">← Tapşırıqlarıma qayıt</a>
    <div class="page-heading"><h1>{{ $taskId ? 'Tapşırığı redaktə et' : 'Yeni tapşırıq' }}</h1></div>
    <div class="panel form-panel">
        <p id="feedback" class="notice" role="status" aria-live="polite" hidden></p>
        <p id="form-loading" class="muted" @if(!$taskId) hidden @endif>Tapşırıq yüklənir…</p>
        <form id="task-form" novalidate>
            <fieldset id="task-fields" @disabled((bool) $taskId)>
                <div class="field"><label for="title">Başlıq <span class="muted">(məcburi)</span></label><input id="title" name="title" type="text" required maxlength="150" aria-describedby="title-error" placeholder="Nə etmək istəyirsiniz?"><p class="field-error" id="title-error" data-error="title"></p></div>
                <div class="field"><label for="description">Təsvir</label><textarea id="description" name="description" rows="6" aria-describedby="description-error" placeholder="Əlavə qeydlər və təfərrüatlar…"></textarea><p class="field-error" id="description-error" data-error="description"></p></div>
                <div class="field"><label class="checkbox-label"><input id="completed" name="is_completed" type="checkbox" aria-describedby="completed-error"> Tamamlanmış kimi işarələ</label><p class="field-error" id="completed-error" data-error="is_completed"></p></div>
                <div class="form-actions"><button class="button primary" type="submit" id="save-task">{{ $taskId ? 'Dəyişiklikləri saxla' : 'Tapşırıq yarat' }}</button><a class="button ghost" href="{{ route('tasks.index') }}">Siyahıya qayıt</a></div>
            </fieldset>
        </form>
    </div>
</section>
@endsection
