(() => {
    'use strict';
    const config = window.taskManager;
    if (!config) return;

    const feedback = document.getElementById('feedback');
    function message(text, error = false) {
        feedback.textContent = text;
        feedback.className = `notice ${error ? 'error' : 'success'}`;
        feedback.hidden = !text;
    }

    async function api(path = '', options = {}) {
        const response = await fetch(config.apiUrl + path, {
            ...options,
            credentials: 'omit',
            headers: {
                Accept: 'application/json',
                Authorization: `Bearer ${config.accessToken}`,
                ...(options.body ? { 'Content-Type': 'application/json' } : {}),
            },
        });
        const body = response.status === 204 ? null : await response.json();
        if (!response.ok) {
            const error = new Error(response.status === 401
                ? 'API tokeninin vaxtı bitib. Çıxış edib yenidən daxil olun.'
                : response.status === 404 ? 'Tapşırıq tapılmadı.'
                : body?.message || 'Sorğu yerinə yetirilmədi.');
            error.status = response.status;
            error.errors = body?.errors || {};
            throw error;
        }
        return body;
    }

    function showError(error) {
        const details = Object.values(error.errors || {}).flat();
        message(details.length ? details.join('\n') : error.message, true);
    }

    function element(tag, className, text) {
        const node = document.createElement(tag);
        node.className = className;
        if (text !== undefined) node.textContent = text;
        return node;
    }

    const listPage = document.getElementById('task-list-page');
    if (listPage) {
        const list = document.getElementById('tasks');
        const filters = document.getElementById('filters');
        let latestRequest = 0;

        async function load() {
            const requestId = ++latestRequest;
            list.setAttribute('aria-busy', 'true');
            list.replaceChildren(element('p', 'empty', 'Tapşırıqlar yüklənir…'));
            try {
                const status = filters.elements.is_completed.value;
                const result = await api(status === '' ? '' : `?is_completed=${status}`);
                if (requestId !== latestRequest) return;
                list.replaceChildren();
                if (!result.data.length) {
                    list.append(element('p', 'empty', 'Bu siyahıda tapşırıq yoxdur.'));
                }
                result.data.forEach(renderTask);
            } catch (error) {
                if (requestId !== latestRequest) return;
                list.replaceChildren(element('p', 'empty', 'Siyahını yükləmək mümkün olmadı. Filtri yenidən tətbiq edin.'));
                showError(error);
            } finally {
                if (requestId === latestRequest) list.setAttribute('aria-busy', 'false');
            }
        }

        function renderTask(task) {
            const row = element('article', `task-card${task.is_completed ? ' completed' : ''}`);
            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.checked = task.is_completed;
            checkbox.setAttribute('aria-label', `${task.title}: tamamlanma vəziyyəti`);
            const content = element('div', 'task-body');
            content.append(element('span', 'task-title', task.title));
            if (task.description) content.append(element('p', 'task-description', task.description));
            const actions = element('div', 'task-actions');
            const edit = element('a', 'button ghost', 'Redaktə et');
            edit.href = `${config.tasksUrl}/${task.id}/edit`;
            const remove = element('button', 'button danger', 'Sil');
            remove.type = 'button';
            actions.append(edit, remove);
            row.append(checkbox, content, actions);
            list.append(row);

            checkbox.addEventListener('change', async () => {
                checkbox.disabled = true;
                remove.disabled = true;
                try {
                    await api(`/${task.id}`, { method: 'PATCH', body: JSON.stringify({ is_completed: checkbox.checked }) });
                    message('Tapşırığın vəziyyəti yeniləndi.');
                    await load();
                } catch (error) {
                    checkbox.checked = task.is_completed;
                    showError(error);
                } finally {
                    checkbox.disabled = false;
                    remove.disabled = false;
                }
            });

            remove.addEventListener('click', async () => {
                remove.disabled = true;
                checkbox.disabled = true;
                try {
                    await api(`/${task.id}`, { method: 'DELETE' });
                    message('Tapşırıq silindi.');
                    await load();
                } catch (error) {
                    showError(error);
                } finally {
                    remove.disabled = false;
                    checkbox.disabled = false;
                }
            });
        }

        filters.addEventListener('submit', (event) => { event.preventDefault(); message(''); load(); });
        filters.elements.is_completed.addEventListener('change', () => { message(''); load(); });
        load();
    }

    const formPage = document.getElementById('task-form-page');
    if (formPage) {
        const form = document.getElementById('task-form');
        const fields = document.getElementById('task-fields');
        const loading = document.getElementById('form-loading');
        const save = document.getElementById('save-task');
        let taskId = formPage.dataset.taskId;
        const defaultLabel = save.textContent;

        async function loadTask() {
            try {
                const { data } = await api(`/${taskId}`);
                form.elements.title.value = data.title;
                form.elements.description.value = data.description || '';
                form.elements.is_completed.checked = data.is_completed;
                fields.disabled = false;
            } catch (error) {
                showError(error);
            } finally {
                loading.hidden = true;
            }
        }
        if (taskId) loadTask();

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            form.querySelectorAll('[data-error]').forEach((node) => { node.textContent = ''; });
            form.querySelectorAll('[aria-invalid]').forEach((node) => { node.removeAttribute('aria-invalid'); });
            const data = {
                title: form.elements.title.value,
                description: form.elements.description.value || null,
                is_completed: form.elements.is_completed.checked,
            };
            fields.disabled = true;
            save.textContent = 'Saxlanılır…';
            message('');
            try {
                const editing = Boolean(taskId);
                const result = await api(editing ? `/${taskId}` : '', {
                    method: editing ? 'PATCH' : 'POST', body: JSON.stringify(data),
                });
                if (!editing) {
                    // Prevent a second submit from creating the same task again.
                    taskId = String(result.data.id);
                    window.history.replaceState(null, '', `${config.tasksUrl}/${taskId}/edit`);
                    document.querySelector('h1').textContent = 'Tapşırığı redaktə et';
                    document.title = 'Tapşırığı redaktə et · Task Manager';
                }
                message(editing ? 'Dəyişikliklər saxlanıldı.' : 'Tapşırıq yaradıldı.');
            } catch (error) {
                showError(error);
                for (const [key, errors] of Object.entries(error.errors || {})) {
                    const target = form.querySelector(`[data-error="${key}"]`);
                    if (target) target.textContent = [].concat(errors).join(' ');
                    if (form.elements[key]) form.elements[key].setAttribute('aria-invalid', 'true');
                }
                form.querySelector('[aria-invalid="true"]')?.focus();
            } finally {
                fields.disabled = false;
                save.textContent = taskId ? 'Dəyişiklikləri saxla' : defaultLabel;
            }
        });
    }
})();
